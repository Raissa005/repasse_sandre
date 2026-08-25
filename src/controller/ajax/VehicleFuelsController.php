<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\VehicleFuels;

class VehicleFuelsController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new VehicleFuels;

        parent::__construct();
    }

    public function addItem()
    {
        $fuel = $this->model->getItemWithFilters([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_POST['name']]]]]);

        if (!$fuel) {
            $arrPost = [
                'name' => $_POST['name'],
                'status' => $_POST['status'],
                'created_by' => $_SESSION['RR']->user->id
            ];

            $response = $this->model->insert($arrPost);

            echo json_encode(['error' => false, 'message' => $response->message, 'fuel' => $response->item]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Já existe um combustível com este nome!']);
            exit;
        }
    }
}
