<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Pagination;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\Attendance;
use RR\model\BillReceive;
use RR\model\BillsToPay;
use RR\model\Branch;
use RR\model\Expenses;
use RR\model\Sales;
use RR\model\SalesChargePaymentAgreement;

class SalesController extends Ajax
{
    public $model;

    function __construct()
    {
        parent::__construct();
        $this->model = new Sales();
    }

    public function getPaymentArrangement()
    {
        $columns = [
            (object)[
                'columns' => ['id', 'sale_value', 'percentage_commission', 'percentage_commission_virtual_rate', 'percentage_commission_real_rate', 'percentage_commission_seller', 'origin_commission_seller']
            ]
        ];

        $sale = (new Sales)->getItemById($_POST['id'], $columns);

        $filtersPaymentAgreement = [
            (object)[
                'columns' => [
                    'id_sale' => (object)['comparison' => 'EQUAL', 'value' => $sale->id]
                ]
            ]
        ];

        $columnsPaymentAgreement = [
            (object)['columns' => ['id', 'percentage_commission', 'origin_commission']]
        ];

        $salePaymentAgreement = (new SalesChargePaymentAgreement)->getWithFiltersAllItems($filtersPaymentAgreement, $columnsPaymentAgreement)->data;

        $data = (object)[
            'value' => $sale->sale_value,
            'percentage' => $sale->percentage_commission,
            'taxes' => (object)[
                'real' => $sale->percentage_commission_real_rate,
                'virtual' => $sale->percentage_commission_virtual_rate,
            ],
            'seller' => (object)[
                'percentage' => $sale->percentage_commission_seller,
                'origin' => $sale->origin_commission_seller
            ],
            'positions' => $salePaymentAgreement
        ];
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

    public function checkPaidInstallments()
    {
        if (!empty($_POST)) {
            $bill_receive = (new BillReceive)->getWithFiltersAllItems(
                [
                    (object)[
                        'columns' => [
                            'id' => (object)['comparison' => 'EQUAL', 'value' => $_POST['bill_receive_id']]
                        ]
                    ],
                    (object)[
                        'table' => 'bill_receive_installment',
                        'columns' => [
                            'status_payment' => (object)['comparison' => 'EQUAL', 'value' => '2']
                        ]
                    ]
                ]
            )->data;

            $bill_pay = (new BillsToPay)->getWithFiltersAllItems(
                [
                    (object)[
                        'columns' => [
                            'id_bill_receive' => (object)['comparison' => '=', 'value' => $_POST['bill_receive_id']]
                        ]
                    ],
                    (object)[
                        'table' => 'bills_to_pay_installments',
                        'columns' => [
                            'status_payment' => (object)['comparison' => 'EQUAL', 'value' => '2']
                        ]
                    ]
                ]
            )->data;

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => (object)['bill_receive' => $bill_receive, 'bill_pay' => $bill_pay]]);
        }
    }

    public function handleExpenses()
    {
        /** @param string/Name */
        /** @param int/Amount */
        /** @param int/Sale */

        $response = (new Expenses)->addExpense($_POST['name'], $_POST['amount'], $_POST['id_sale']);
        echo json_encode(['error' => $this->error, 'data' => $response]);
        exit;
    }

    public function handleDelete()
    {
        /** @param string/Expense */

        $response = (new Expenses)->deleteExpense($_POST['id_expense']);
        echo json_encode(['error' => $this->error, 'data' => $response]);
        exit;
    }

    public function getCurrentBranch()
    {
        $data = (new Branch())->getItemById($_POST['id_branch']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }

    public function getAttendanceForSale()
    {
        $attendance_filters = [
            (object)[
                'columns' => [
                    'id_status' => (object)['value' => 10]
                ]
            ]
        ];

        if (!Secure::access_secretary()) {
            $attendance_filters[] = (object)['columns' => ['created_by' => (object)['value' => $_SESSION['RR']->user->id]]];
        }

        if (isset($_POST['name']) && !empty($_POST['name'])) {
            $attendance_filters[] = (object)['where' => " AND ucase(this->table.name) LIKE ucase('%" . $_POST['name'] . "%')"];
        }

        $attendances = (new Attendance)->getWithFiltersAllItems(
            $attendance_filters,
            [(object)['columns' => ['id', 'name']], (object)['table' => 'users', 'columns' => ['name']]],
            [
                'limit' => $_POST['limit'],
                'page' => $_POST['page'],
                'order' => 'this->table.name ASC',
            ]
        );

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $attendances->data, 'pagination' => (new Pagination)->pages($attendances->count, $_POST['limit'], 10, $_POST['page'])]);
        exit;
    }
}
