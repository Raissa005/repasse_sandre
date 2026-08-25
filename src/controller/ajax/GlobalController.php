<?php

namespace RR\Controller\ajax;

use RR\core\Ajax;
use RR\model\ModelGenerico;

class GlobalController extends Ajax
{
    public function __construct()
    {
        session_start();
    }

    public function getGenericoById()
    {
        $data = (new ModelGenerico())->getItemById8161($_POST['id'], $_POST['table']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }

    public function getItemByGenericField()
    {
        $data = (new ModelGenerico())->getItemByGenericField($_POST['value'], $_POST['table'], $_POST['field']);

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }

    public function getItemByGenericFieldArray()
    {
        $data = (new ModelGenerico())->getItemByGenericFieldArray($_POST['array'], $_POST['table']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }

    public function toast()
    {
        $data = (object)['toast' => isset($_SESSION['RR']->toast) ? $_SESSION['RR']->toast : ""];
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        unset($_SESSION['RR']->toast);
        exit;
    }
}
