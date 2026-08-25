<?php

namespace RR\core;

use RR\model\User;

class Ajax
{
    public $error = false;
    public $message = 'Okay';

    public function __construct()
    {
        session_start();

        if (!isset($_SESSION['RR']->user->id)) {
            $this->error = true;
            $this->message = 'Invalid session!';
            Self::sendResponse();
        } else {
            $user = (new User())->getUserById($_SESSION['RR']->user->id);
            if (!$user || !$user->status) {
                $this->error = true;
                $this->message = 'User does not exist or is inactive!';
                Self::sendResponse();
            }
        }

        $responseObject = $this->associativeArrayToObjectConversion(isset($_POST['filters']) ? $_POST['filters'] : [], isset($_POST['columns']) ? $_POST['columns'] : []);
        $_POST['filters'] = $responseObject->filters;
        $_POST['columns'] = $responseObject->columns;
        $_POST['options'] = isset($_POST['options']) ? $_POST['options'] : [];
    }

    public function associativeArrayToObjectConversion($filters = [], $columns = [])
    {
        return (object)[
            'filters' => array_map(function ($filter) {
                $filter = (object)$filter;
                $filter->columns = array_map(function ($column) {
                    return (object)$column;
                }, $filter->columns);
                return $filter;
            }, $filters),
            'columns' => array_map(function ($column) {
                $column = (object)$column;
                return $column;
            }, $columns)
        ];
    }

    protected function sendResponse($data = []): void
    {
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => (array)$data]);
        exit;
    }
}
