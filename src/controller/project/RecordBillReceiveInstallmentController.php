<?php

namespace RR\controller\project;

use RR\libs\BoxAlert;
use RR\libs\Date;
use RR\libs\RecursiveCostCenter;
use RR\libs\Util;
use RR\model\BillReceive;
use RR\model\BillReceiveInstallment;
use RR\model\Branch;
use RR\model\CostCenter;
use RR\model\Customer;
use RR\model\FormOfPayment;
use RR\model\ModelGenerico;

class RecordBillReceiveInstallmentController extends FrontController
{
    public $route;
    public $dir;
    private $model;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'record-bill-receive-installment';
        $this->dir = 'record-bill-receive-installment';
        $this->model = new BillReceiveInstallment();
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
        $this->title = "Relatório de Contas à Receber";
    }

    public function index()
    {
        Secure::access_admin(true);

        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");

        $filters = isset(explode('?', $_SERVER["REQUEST_URI"])[1]) ? explode('?', $_SERVER["REQUEST_URI"])[1] : '';

        if (!isset($_GET['b'])) {
            if (!isset($_GET['column_id'])) {
                $_GET['column_id'] = 'on';
            }

            if (!isset($_GET['column_number_portion'])) {
                $_GET['column_number_portion'] = 'on';
            }

            if (!isset($_GET['column_form_payment'])) {
                $_GET['column_form_payment'] = 'on';
            }

            if (!isset($_GET['column_cost_center'])) {
                $_GET['column_cost_center'] = 'on';
            }

            if (!isset($_GET['column_status_payment'])) {
                $_GET['column_status_payment'] = 'on';
            }
        } else {

            if (!isset($_GET['column_id'])) {
                $_GET['column_id'] = 'off';
            }

            if (!isset($_GET['column_number_portion'])) {
                $_GET['column_number_portion'] = 'off';
            }

            if (!isset($_GET['column_form_payment'])) {
                $_GET['column_form_payment'] = 'off';
            }

            if (!isset($_GET['column_cost_center'])) {
                $_GET['column_cost_center'] = 'off';
            }

            if (!isset($_GET['column_status_payment'])) {
                $_GET['column_status_payment'] = 'off';
            }
        }

        $countFilters = 0;
        array_walk($_GET, function ($value) use (&$countFilters) {
            if ($value == 'on') $countFilters++;
        });

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['status_payment'])) {
            $_GET['status_payment'] = [1, 2];
        }

        if (in_array("all", $_GET['status_payment'])) {
            $_GET['status_payment'] = ["all", 1, 2, 3];
        }

        if (!isset($_GET['date_type'])) {
            $_GET['date_type'] = 1;
        }

        if (!isset($_GET['date']['start'])) {
            $_GET['date']['start'] = Date::year_month(date("Y-m-d"));
        }

        if (!isset($_GET['date']['end'])) {
            $_GET['date']['end'] = date("Y-m-t");
        }

        $filtersBillReceive = [(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']]]]];

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        } elseif (!empty($_GET['branches']) && !in_array("all", $_GET['branches'])) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_branch' => (object)['comparison' => 'IN', 'value' => $_GET['branches']]]]);
        }

        if (!empty($_GET['id_customer'])) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_customer' => (object)['comparison' => '=', 'value' => $_GET['id_customer']]]]);
        }

        if (!empty($_GET['status_payment'])) {
            array_push($filtersBillReceive, (object)['columns' => ['status_payment' => (object)['comparison' => '=', 'value' => $_GET['status_payment']]]]);
        }

        if (!empty($_GET['id_form_of_payment'])) {
            array_push($filtersBillPay, (object)['columns' => ['id_form_of_payment' => (object)['comparison' => 'EQUAL', 'value' => $_GET['id_form_of_payment']]]]);
        }

        if (!empty($_GET['date_type'])) {

            switch ($_GET['date_type']) {
                case 1:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillReceive, (object)['columns' => ['due_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['due_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['due_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;

                case 2:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillReceive, (object)['columns' => ['pay_day' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['pay_day' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['pay_day' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;
            }
        }

        if (isset($_GET['id_cost_center']) && !empty($_GET['id_cost_center'])) {
            $costCentersIds = (new RecursiveCostCenter)->recursiveGetChildren($_GET['id_cost_center'], [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']],
                        'id_type' => (object)['comparison' => 'EQUAL', 'value' => 1]
                    ]
                ]
            ]);

            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_cost_center' => (object)['comparison' => 'IN', 'value' => $costCentersIds]]]);
        }

        $columnsBillReceive = [
            (object)['columns' => ['*']],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'branch', 'columns' => ['id' => ['branch_id'], 'name' => ['branch_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]],
            (object)['table' => 'bill_receive', 'columns' => ['id_branch' => ['bill_receive_id_branch'], 'id_cost_center' => ['bill_receive_id_cost_center']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ];

        $optionsBillReceive = [
            'orderBy' => " this->table.status_payment, this->table.due_date, cost_center.id_father" . ($_SESSION['RR']->branch->current->id == 0 ? " ,branch.id " : " ") . "ASC"
        ];

        $response = $this->model->getWithFiltersAllItems($filtersBillReceive, $columnsBillReceive, $optionsBillReceive);

        $amount = Util::maskMoney(array_reduce($response->data, function ($accumulator, $item) {
            return ($accumulator += ($item->amount_paid ? $item->amount_paid : $item->value_installment));
        }, 0));

        array_map(function ($billReceiveInstallment) {
            if ($billReceiveInstallment->customer_fancy_name_company) {
                $billReceiveInstallment->customer_name = $billReceiveInstallment->customer_fancy_name_company;
            } else if ($billReceiveInstallment->customer_company_name) {
                $billReceiveInstallment->customer_name = $billReceiveInstallment->customer_company_name;
            }

            $billReceiveInstallment->totalLaunchInstallments = (new BillReceive)->getAmountOfInstallmentsOfRelease($billReceiveInstallment->id_bill_receive);
        }, $response->data);

        foreach ($response->data as $item) {
            $item->text = "";
            switch ($item->status_payment) {
                case '1':
                    if ($item->due_date < date("Y-m-d")) {
                        $item->bgtr = 'danger';
                        $item->label = 'Atrasado';
                        $item->text = 'text-red';
                    } else {
                        if ($item->due_date == date("Y-m-d")) {
                            $item->text = 'text-yellow';
                        }
                        $item->bgtr = 'warning';
                        $item->label = 'Aguard. Pagam.';
                    }
                    break;
                case '2':
                    $item->bgtr = 'success';
                    $item->label = 'Pago';
                    break;
                case '3':
                    $item->bgtr = 'default';
                    $item->label = 'Cancelado';
                    break;
            }
        }

        $branches = (new Branch)->getWithFiltersAllItems([(object)['columns' => ['status' => ['value' => 1]]]], [], ['orderBy' => 'this->table.name ASC']);
        $paymentStatus = (new ModelGenerico)->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);
        $customers = (new Customer())->getAndFilterAllCustomer(0, ['status' => 1, 'id_customer_type' => 10, "id_branch" => $_SESSION['RR']->branch->current->id], 0);
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $costCentersForIndexes = (new RecursiveCostCenter())->recursiveTree(0, ['id_type' => 2]);

        array_map(function ($element) {
            $element->name = $element->fancy_name_company ?? $element->company_name ?? $element->name;
        }, $customers);

        $report = (object)['branches' => [], 'cost_centers' => []];
        array_map(function ($item) use (&$report, $costCentersForIndexes) {
            $item->cc_index_name = (new RecursiveCostCenter())->findIndex($costCentersForIndexes, '', $item->cost_center_id);

            if (!empty($item->cost_center_id)) {
                if (!empty($_GET['column_complete_cost_center'])) {
                    $costCenter = (new CostCenter)->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $item->cost_center_id]]]]);

                    if (!empty($costCenter->id_father)) {
                        $father = (new RecursiveCostCenter())->findIndex($costCentersForIndexes, '', $costCenter->id_father);

                        $item->cc_index_name = ucwords(mb_convert_case($father, MB_CASE_LOWER, 'UTF-8')) . " / " . ucwords(mb_convert_case($item->cc_index_name, MB_CASE_LOWER, 'UTF-8'));
                    }
                } else {
                    $item->cc_index_name = ucwords(mb_convert_case($item->cc_index_name, MB_CASE_LOWER, 'UTF-8'));
                }
            } else {
                $item->cc_index_name = ucwords(mb_convert_case($item->cc_index_name, MB_CASE_LOWER, 'UTF-8'));
            }

            $value = $item->amount_paid ?? $item->value_installment;

            $report->branches[$item->branch_id] = (object)[
                'name' => $item->branch_name,
                'id' => $item->branch_id,
                'value' => ($report->branches[$item->branch_id]->value ?? 0) + $value
            ];

            $report->cost_centers[$item->cost_center_id] = (object)[
                'name' => $item->cc_index_name,
                'id' => $item->cost_center_id,
                'value' => ($report->cost_centers[$item->cost_center_id]->value ?? 0) + $value
            ];
        }, $response->data);
        sort($report->branches);
        sort($report->cost_centers);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function print()
    {
        Secure::access_admin(true);

        if (!isset($_GET['b'])) {
            if (!isset($_GET['column_id'])) {
                $_GET['column_id'] = 'on';
            }

            if (!isset($_GET['column_number_portion'])) {
                $_GET['column_number_portion'] = 'on';
            }

            if (!isset($_GET['column_form_payment'])) {
                $_GET['column_form_payment'] = 'on';
            }

            if (!isset($_GET['column_cost_center'])) {
                $_GET['column_cost_center'] = 'on';
            }

            if (!isset($_GET['column_status_payment'])) {
                $_GET['column_status_payment'] = 'on';
            }
        } else {

            if (!isset($_GET['column_id'])) {
                $_GET['column_id'] = 'off';
            }

            if (!isset($_GET['column_number_portion'])) {
                $_GET['column_number_portion'] = 'off';
            }

            if (!isset($_GET['column_form_payment'])) {
                $_GET['column_form_payment'] = 'off';
            }

            if (!isset($_GET['column_cost_center'])) {
                $_GET['column_cost_center'] = 'off';
            }

            if (!isset($_GET['column_status_payment'])) {
                $_GET['column_status_payment'] = 'off';
            }
        }

        $countFilters = 0;
        array_walk($_GET, function ($value) use (&$countFilters) {
            if ($value == 'on') $countFilters++;
        });

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['status_payment'])) {
            $_GET['status_payment'] = [1, 2];
        }

        if (in_array("all", $_GET['status_payment'])) {
            $_GET['status_payment'] = ["all", 1, 2, 3];
        }

        if (!isset($_GET['date_type'])) {
            $_GET['date_type'] = 1;
        }

        if (!isset($_GET['date']['start'])) {
            $_GET['date']['start'] = Date::year_month(date("Y-m-d"));
        }

        if (!isset($_GET['date']['end'])) {
            $_GET['date']['end'] = date("Y-m-t");
        }

        $filtersBillReceive = [
            (object)[
                'columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']],
                ]
            ]
        ];

        if (!empty($_GET['status_payment'])) {
            array_push($filtersBillReceive, (object)['columns' => ['status_payment' => (object)['comparison' => '=', 'value' => $_GET['status_payment']]]]);
        }

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        } elseif (!empty($_GET['branches']) && !in_array("all", $_GET['branches'])) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_branch' => (object)['comparison' => 'IN', 'value' => $_GET['branches']]]]);
        }

        if (!empty($_GET['id_customer'])) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_customer' => (object)['comparison' => '=', 'value' => $_GET['id_customer']]]]);
        }

        if (!empty($_GET['date_type'])) {

            switch ($_GET['date_type']) {
                case 1:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillReceive, (object)['columns' => ['due_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['due_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime("{$_GET['date']['start']}"))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['due_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;

                case 2:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillReceive, (object)['columns' => ['pay_day' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['pay_day' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillReceive, (object)['columns' => ['pay_day' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;
            }
        }

        if (isset($_GET['id_cost_center']) && !empty($_GET['id_cost_center'])) {
            $nameFather = (new CostCenter)->getItemById($_GET['id_cost_center']);

            $costCentersIds = (new RecursiveCostCenter)->recursiveGetChildren($_GET['id_cost_center'], [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']],
                        'id_type' => (object)['comparison' => 'EQUAL', 'value' => 1]
                    ]
                ]
            ]);

            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_cost_center' => (object)['comparison' => 'IN', 'value' => $costCentersIds]]]);
        }

        $columnsBillReceive = [
            (object)['columns' => ['*']],
            (object)['table' => 'branch', 'columns' => ['name' => ['branch_name']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]],
            (object)['table' => 'bill_receive', 'columns' => ['id_branch' => ['bill_receive_id_branch'], 'id_cost_center' => ['bill_receive_id_cost_center']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ];

        $optionsBillReceive = [
            'orderBy' => " this->table.status_payment, this->table.due_date, cost_center.id_father" . ($_SESSION['RR']->branch->current->id == 0 ? " ,branch.id " : " ") . "ASC"
        ];

        $response = $this->model->getWithFiltersAllItems($filtersBillReceive, $columnsBillReceive, $optionsBillReceive);

        $amount = Util::maskMoney(array_reduce($response->data, function ($accumulator, $item) {
            return ($accumulator += ($item->amount_paid ? $item->amount_paid : $item->value_installment));
        }, 0));


        array_map(function ($billReceiveInstallment) {
            if ($billReceiveInstallment->customer_fancy_name_company) {
                $billReceiveInstallment->customer_name = $billReceiveInstallment->customer_fancy_name_company;
            } else if ($billReceiveInstallment->customer_company_name) {
                $billReceiveInstallment->customer_name = $billReceiveInstallment->customer_company_name;
            }

            $billReceiveInstallment->totalLaunchInstallments = (new BillReceive)->getAmountOfInstallmentsOfRelease($billReceiveInstallment->id_bill_receive);
        }, $response->data);

        array_map(function ($element) {
            $element->customer_name = ucwords(mb_strtolower($element->customer_name), ' ');
            $element->totalLaunchInstallments = (new BillReceive)->getAmountOfInstallmentsOfRelease($element->id_bill_receive);
        }, $response->data);

        foreach ($response->data as $item) {
            $item->text = "";
            switch ($item->status_payment) {
                case '1':
                    if (($item->due_date) < date("Y-m-d")) {
                        $item->label = 'Atrasado';
                    } else {
                        $item->label = 'Aguard. Pagam.';
                    }
                    break;
                case '2':
                    $item->label = 'Pago';
                    break;
                case '3':
                    $item->label = 'Cancelado';
                    break;
            }

            $item->due_date = Date::date($item->due_date);

            if (!empty($item->pay_day)) {
                $item->pay_day = Date::date($item->pay_day);
            } else {
                $item->pay_day = "-";
            }
        }

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);
        $costCentersForIndexes = (new RecursiveCostCenter())->recursiveTree(0, ['id_type' => 2]);

        $report = (object)['branches' => [], 'cost_centers' => []];
        array_map(function ($item) use (&$report, $costCentersForIndexes) {
            $item->cc_index_name = (new RecursiveCostCenter())->findIndex($costCentersForIndexes, '', $item->cost_center_id);

            if (!empty($item->cost_center_id)) {
                if (!empty($_GET['column_complete_cost_center'])) {
                    $costCenter = (new CostCenter)->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $item->cost_center_id]]]]);

                    if (!empty($costCenter->id_father)) {
                        $father = (new RecursiveCostCenter())->findIndex($costCentersForIndexes, '', $costCenter->id_father);

                        $item->cc_index_name = ucwords(mb_convert_case($father, MB_CASE_LOWER, 'UTF-8')) . " / " . ucwords(mb_convert_case($item->cc_index_name, MB_CASE_LOWER, 'UTF-8'));
                    }
                } else {
                    $item->cc_index_name = ucwords(mb_convert_case($item->cc_index_name, MB_CASE_LOWER, 'UTF-8'));
                }
            } else {
                $item->cc_index_name = ucwords(mb_convert_case($item->cc_index_name, MB_CASE_LOWER, 'UTF-8'));
            }

            $value = $item->amount_paid ?? $item->value_installment;
            $item->amount_paid = Util::maskMoney($item->amount_paid);
            $item->value_of_installments = Util::maskMoney($item->value_installment);
            $report->branches[$item->branch_name] = ($report->branches[$item->branch_name] ?? 0) + $value;
            $report->cost_centers[$item->cc_index_name] = ($report->cost_centers[$item->cc_index_name] ?? 0) + $value;
        }, $response->data);
        ksort($report->branches);
        ksort($report->cost_centers);

        $filters = (object)[
            'due_date_start' => date("d/m/Y", strtotime($_GET['date']['start'])),
            'due_date_end' => date("d/m/Y", strtotime($_GET['date']['end'])),
        ];

        if ($_SESSION['RR']->branch->current->id != 0) {
            $filters->branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id)->name;
        }

        $totalAmountVerify = 0;

        require APP . 'view/' . $this->dir . '/print.php';
    }
}
