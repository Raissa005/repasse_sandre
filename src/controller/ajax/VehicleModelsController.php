<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\VehicleModels;

class VehicleModelsController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new VehicleModels;

        parent::__construct();
    }

    public function getAllItemsByBrandId($brandId)
    {
        $response = $this->model->getWithFiltersAllItems([(object)['columns' => ['id_brand' => (object)['comparison' => 'EQUAL', 'value' => $brandId]]]])->data;

        echo json_encode(['error' => false, 'models' => $response]);
    }

    public function addItem()
    {
        $model = $this->model->getItemWithFilters(
            [
                (object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_POST['name']]]],
                (object)['columns' => ['id_brand' => (object)['comparison' => 'LIKE', 'value' => $_POST['brandId']]]]
            ]
        );

        if (!$model) {
            $arrPost = [
                'name' => $_POST['name'],
                'status' => $_POST['status'],
                'id_brand' => $_POST['brandId'],
                'created_by' => $_SESSION['RR']->user->id
            ];

            $response = $this->model->insert($arrPost);

            echo json_encode(['error' => false, 'message' => $response->message, 'model' => $response->item]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Já existe um modelo com este nome!']);
            exit;
        }
    }
}
