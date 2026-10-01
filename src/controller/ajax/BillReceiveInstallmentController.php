<?php

namespace RR\Controller\ajax;

use RR\core\Ajax;
use RR\model\BillsToPayInstallment;

class BillReceiveInstallmentController extends Ajax
{
    public function checkInstallmentPaidInBillToPay()
    {
        $bill_pay = (new BillsToPayInstallment())->getWithFiltersAllItems([(object)['columns' => [
            'id_bill_receive_installment' => (object)['comparison' => '=', 'value' => $_POST['bill_receive_installment_id']],
            'status_payment' => (object)['comparison' => '=', 'value' => 2]
        ]]])->data;
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => (object)['bill_pay' => $bill_pay]]);
        exit;
    }
}
