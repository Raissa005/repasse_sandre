<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\VehicleCategories;

class VehicleCategoriesController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new VehicleCategories;

        parent::__construct();
    }

    public function addItem()
    {
        $category = $this->model->getItemWithFilters([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_POST['name']]]]]);

        if (!$category) {
            $arrPost = [
                'name' => $_POST['name'],
                'status' => $_POST['status'],
                'created_by' => $_SESSION['RR']->user->id
            ];

            $response = $this->model->insert($arrPost);

            echo json_encode(['error' => false, 'message' => $response->message, 'category' => $response->item]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Já existe uma categoria com este nome!']);
            exit;
        }
    }
}
