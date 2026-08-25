<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\VehicleColors;

class VehicleColorsController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new VehicleColors;

        parent::__construct();
    }

    public function addItem()
    {
        $color = $this->model->getItemWithFilters([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_POST['name']]]]]);

        if (!$color) {
            $arrPost = [
                'name' => $_POST['name'],
                'status' => $_POST['status'],
                'created_by' => $_SESSION['RR']->user->id
            ];

            $response = $this->model->insert($arrPost);

            echo json_encode(['error' => false, 'message' => $response->message, 'color' => $response->item]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Já existe uma cor com este nome!']);
            exit;
        }
    }
}
