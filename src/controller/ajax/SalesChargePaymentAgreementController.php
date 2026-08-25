<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\SalesChargePaymentAgreement;

class SalesChargePaymentAgreementController extends Ajax
{
    public $model;

    function __construct()
    {
        parent::__construct();
        $this->model = new SalesChargePaymentAgreement();
    }

    public function getItemById()
    {
        $data = $this->model->getItemById($_POST['id'], $_POST['columns']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getWithFiltersAllItems()
    {
        $data = $this->model->getWithFiltersAllItems($_POST['filters'], $_POST['columns'], $_POST['options'])->data;
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function removeParticipantFromPaymentArrangement()
    {
        if (!empty($_POST)) {
            $response = (new SalesChargePaymentAgreement)->handleFormRemove($_POST['id']);

            if (!$this->error) {
                $this->error = $response->error;
                $this->message = $response->message;
            }

            echo json_encode(['error' => $this->error, 'message' => $this->message]);
        }
    }
}
