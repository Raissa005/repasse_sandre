<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Date;
use RR\libs\Util;
use RR\model\BillsToPayInstallment;

class RecordBillsToPayInstallmentsController extends Ajax
{
    public $model;

    function __construct()
    {
        parent::__construct();
        $this->model = new BillsToPayInstallment();
    }

    public function getValuesFromCostCenterForRecord()
    {
        /**
         * @param string/get
         * @param int/id_cost_center
         * @return int/total_value
         * @return array/cc_values
         * @return array/months_values
         */

        $get = json_decode($_POST['get']);

        if (!isset($get->status)) {
            $get->status = true;
        }

        if (!isset($get->status_payment)) {
            $get->status_payment = [1, 2];
        }

        if (in_array("all", $get->status_payment)) {
            $get->status_payment = ["all", 1, 2, 3];
        }

        if (!isset($get->date->start)) {
            $get->date->start = Date::year_month(date("Y-m-d")) . '-01';
        }

        if (!isset($get->date->end)) {
            $get->date->end = date("Y-m-t");
        }

        $filtersBillPay = [
            (object)[
                'columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => $get->status],
                ]
            ],
            (object)[
                'table' => 'bills_to_pay',
                'columns' => [
                    'id_cost_center' => (object)['value' => $_POST['id_cost_center']]
                ]
            ]
        ];

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        } elseif (!empty($get->branches) && !in_array("all", $get->branches)) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => (object)['comparison' => 'IN', 'value' => $get->branches]]]);
        }

        if (!empty($get->id_customer)) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_customer' => (object)['comparison' => '=', 'value' => $get->id_customer]]]);
        }

        if (!empty($get->status_payment)) {
            array_push($filtersBillPay, (object)['columns' => ['status_payment' => (object)['comparison' => '=', 'value' => $get->status_payment]]]);
        }

        if (!empty($get->date->start) && !empty($get->date->end)) {
            array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime("{$get->date->start}")), 'value2' => date('Y-m-d', strtotime($get->date->end))]]]);
        } else {
            if (!empty($get->date->start)) {
                array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime("{$get->date->start}"))]]]);
            }

            if (!empty($get->date->end)) {
                array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($get->date->end))]]]);
            }
        }

        $columnsBillPay = [
            (object)['columns' => ['*']],
            (object)['table' => 'branch', 'columns' => ['id' => ['branch_id'], 'name' => ['branch_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]]
        ];

        $response = $this->model->getWithFiltersAllItems($filtersBillPay, $columnsBillPay);

        $months_values = $this->model->getValuesFromMonths($response->data);

        $cc_values = [];
        $total_values = 0;
        array_walk($response->data, function ($value) use (&$cc_values, &$total_values) {
            $amount = $value->amount_paid ?? $value->value_of_installments;
            $total_values += $amount;

            $cc_values[$value->branch_id] = (object)[
                'branch_name' => $value->branch_name,
                'branch_id' => $value->branch_id,
                'amount' => ($cc_values[$value->branch_id]->amount ?? 0) + $amount,
            ];
        });

        parent::sendResponse([
            'cc_values' => $cc_values,
            'total_values' => $total_values,
            'months_values' => $months_values,
        ]);
    }
}
