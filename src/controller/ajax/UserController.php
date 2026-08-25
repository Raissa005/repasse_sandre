<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Util;
use RR\model\User;

class UserController extends Ajax
{
    public $model;

    function __construct()
    {
        parent::__construct();
        $this->model = new User();
    }

    public function getItemById()
    {
        $data = $this->model->getItemById($_POST['id'], $_POST['columns']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getSellerByBranch()
    {
        $data = $this->model->getWithFiltersAllItems([
            (object)[
                'columns' => [
                    'status' => (object)['value' => 1],
                    'id_profile' => (object)['value' => 4],
                ]
            ],
            (object)[
                'table' => 'user_branches',
                'columns' => [
                    'id_branch' => (object)['value' => $_POST['id_branch']],
                ]
            ], 
        ])->data;

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getWithFiltersAllItems()
    {
        $data = $this->model->getWithFiltersAllItems($_POST['filters'], $_POST['columns'], $_POST['options'])->data;
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function validateEmail()
    {
        if (!empty($_POST)) {
            $data = (new User)->getWithFiltersAllItems(
                [(object)['columns' => ['email' => (object)['comparison' => "EQUAL", 'value' => $_POST['email']]]]]
            )->data;

            if (!empty($data)) {
                $this->message = "Email em uso!";
            }

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        }
    }
}
