<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\VehicleBrands;

class VehicleBrandsController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new VehicleBrands;

        parent::__construct();
    }

    public function addItem()
    {
        $brand = $this->model->getItemWithFilters([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_POST['name']]]]]);

        if (!$brand) {
            $arrPost = [
                'name' => $_POST['name'],
                'status' => $_POST['status'],
                'created_by' => $_SESSION['RR']->user->id
            ];

            $response = $this->model->insert($arrPost);

            echo json_encode(['error' => false, 'message' => $response->message, 'brand' => $response->item]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Já existe uma marca com este nome!']);
            exit;
        }
    }
}
