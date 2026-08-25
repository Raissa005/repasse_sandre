<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\VehicleDoors;

class VehicleDoorsController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new VehicleDoors;

        parent::__construct();
    }

    public function addItem()
    {
        $door = $this->model->getItemWithFilters([(object)['columns' => ['doors' => (object)['comparison' => 'EQUAL', 'value' => $_POST['door']]]]]);

        if (!$door) {
            $arrPost = [
                'doors' => $_POST['door'],
                'status' => $_POST['status'],
                'created_by' => $_SESSION['RR']->user->id
            ];
    
            $response = $this->model->insert($arrPost);

            echo json_encode(['error' => false, 'message' => $response->message, 'door' => $response->item]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Já existe a quantidade de portas!']);
            exit;
        }
    }
}
