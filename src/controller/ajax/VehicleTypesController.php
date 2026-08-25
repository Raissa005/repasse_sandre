<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\VehicleTypes;

class VehicleTypesController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new VehicleTypes;

        parent::__construct();
    }

    public function addItem()
    {
        $type = $this->model->getItemWithFilters([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_POST['name']]]]]);

        if (!$type) {
            $arrPost = [
                'name' => $_POST['name'],
                'status' => $_POST['status'],
                'created_by' => $_SESSION['RR']->user->id
            ];

            $response = $this->model->insert($arrPost);

            echo json_encode(['error' => false, 'message' => $response->message, 'type' => $response->item]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Já existe um tipo com este nome!']);
            exit;
        }
    }
}
