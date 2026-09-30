<?php

namespace RR\controller\project;

use RR\model\CostCenter;
use RR\model\ModelGenerico;
use RR\libs\Date;
use RR\libs\RecursiveCostCenter;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\Customer;
use RR\model\CustomerType;
use RR\model\FormOfPayment;
use RR\model\BillsToPayInstallment;
use RR\model\BillsToPay;
use RR\model\Branch;

class RecordBillsToPayInstallmentController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'record-bills-to-pay-installment';
        $this->dir = 'record-bills-to-pay-installment';
        $this->model = new BillsToPayInstallment();
        $this->table = 'bills_to_pay_installments';
        parent::__construct($this->route);

        $this->title = "Relatório de Contas à Pagar";
    }

    public function index()
    {
        Secure::access_admin(true);

        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/bills-to-pay-installment/record/report.js");

        $modelGenerico = new ModelGenerico();
        $billsToPayModel = new BillsToPayInstallment();

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

        $filtersBillPay = [
            (object)[
                'columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']],
                ]
            ]
        ];

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        } elseif (!empty($_GET['branches']) && !in_array("all", $_GET['branches'])) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => (object)['comparison' => 'IN', 'value' => $_GET['branches']]]]);
        }

        if (!empty($_GET['id_customer'])) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_customer' => (object)['comparison' => '=', 'value' => $_GET['id_customer']]]]);
        }

        if (!empty($_GET['status_payment'])) {
            array_push($filtersBillPay, (object)['columns' => ['status_payment' => (object)['comparison' => '=', 'value' => $_GET['status_payment']]]]);
        }

        if (!empty($_GET['id_form_of_payment'])) {
            array_push($filtersBillPay, (object)['columns' => ['id_form_of_payment' => (object)['comparison' => 'EQUAL', 'value' => $_GET['id_form_of_payment']]]]);
        }

        if (!empty($_GET['date_type'])) {

            switch ($_GET['date_type']) {
                case 1:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;

                case 2:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillPay, (object)['columns' => ['pay_day' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillPay, (object)['columns' => ['pay_day' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillPay, (object)['columns' => ['pay_day' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;
                case 3:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['competence' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['competence' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['competence' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
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

            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_cost_center' => (object)['comparison' => 'IN', 'value' => $costCentersIds]]]);
        }

        $columnsBillPay = [
            (object)['columns' => ['*']],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'branch', 'columns' => ['id' => ['branch_id'], 'name' => ['branch_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]],
            (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => ['bills_to_pay_id_branch'], 'id_cost_center' => ['bills_to_pay_id_cost_center']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'fancy_name_company' => ['customer_fancy_name_company'], 'company_name' => ['customer_company_name']]]
        ];

        $optionsBillPay = [
            'orderBy' => " this->table.status_payment, this->table.due_date, cost_center.id_father" . ($_SESSION['RR']->branch->current->id == 0 ? " ,branch.id " : " ") . "ASC"
        ];

        $response = $this->model->getWithFiltersAllItems($filtersBillPay, $columnsBillPay, $optionsBillPay);

        $amount = Util::maskMoney(array_reduce($response->data, function ($accumulator, $item) {
            return ($accumulator += ($item->amount_paid ? $item->amount_paid : $item->value_of_installments));
        }, 0));

        array_map(function ($billPayInstallment) {
            if ($billPayInstallment->customer_fancy_name_company) {
                $billPayInstallment->customer_name = $billPayInstallment->customer_fancy_name_company;
            } else if ($billPayInstallment->customer_company_name) {
                $billPayInstallment->customer_name = $billPayInstallment->customer_company_name;
            }

            $billPayInstallment->totalLaunchInstallments = (new BillsToPay)->getAmountOfInstallmentsOfRelease($billPayInstallment->id_bills_to_pay);
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
        $paymentStatus = $modelGenerico->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $customers = (new Customer())->getAndFilterAllCustomer(0, ['status' => 1, 'id_customer_type' => (new CustomerType())->getIdByName('Fornecedor'), "id_branch" => $_SESSION['RR']->branch->current->id], 0);
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $costCentersForIndexes = (new RecursiveCostCenter())->recursiveTree(0, ['id_type' => 1]);

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

            $value = $item->amount_paid ?? $item->value_of_installments;

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

        $filtersBillPay = [
            (object)[
                'columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']],
                ]
            ]
        ];

        if (!empty($_GET['status_payment'])) {
            array_push($filtersBillPay, (object)['columns' => ['status_payment' => (object)['comparison' => '=', 'value' => $_GET['status_payment']]]]);
        }

        if (!empty($_GET['id_form_of_payment'])) {
            array_push($filtersBillPay, (object)['columns' => ['id_form_of_payment' => (object)['comparison' => 'EQUAL', 'value' => $_GET['id_form_of_payment']]]]);
        }

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        } elseif (!empty($_GET['branches']) && !in_array("all", $_GET['branches'])) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => (object)['comparison' => 'IN', 'value' => $_GET['branches']]]]);
        }

        if (!empty($_GET['id_customer'])) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_customer' => (object)['comparison' => '=', 'value' => $_GET['id_customer']]]]);
        }

        if (!empty($_GET['date_type'])) {

            switch ($_GET['date_type']) {
                case 1:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;

                case 2:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillPay, (object)['columns' => ['pay_day' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillPay, (object)['columns' => ['pay_day' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillPay, (object)['columns' => ['pay_day' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                        }
                    }
                    break;
                case 3:
                    if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                        array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['competence' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                    } else {
                        if (!empty($_GET['date']['start'])) {
                            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['competence' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                        }

                        if (!empty($_GET['date']['end'])) {
                            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['competence' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
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

            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_cost_center' => (object)['comparison' => 'IN', 'value' => $costCentersIds]]]);
        }

        $columnsBillPay = [
            (object)['columns' => ['*']],
            (object)['table' => 'branch', 'columns' => ['name' => ['branch_name']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]],
            (object)['table' => 'bills_to_pay', 'columns' => ['id_branch' => ['bills_to_pay_id_branch'], 'id_cost_center' => ['bills_to_pay_id_cost_center']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ];

        $optionsBillPay = [
            'orderBy' => " this->table.status_payment, this->table.due_date, cost_center.id_father" . ($_SESSION['RR']->branch->current->id == 0 ? " ,branch.id " : " ") . "ASC"
        ];

        $response = $this->model->getWithFiltersAllItems($filtersBillPay, $columnsBillPay, $optionsBillPay);

        $amount = Util::maskMoney(array_reduce($response->data, function ($accumulator, $item) {
            return ($accumulator += ($item->amount_paid ? $item->amount_paid : $item->value_of_installments));
        }, 0));


        array_map(function ($billPayInstallment) {
            if ($billPayInstallment->customer_fancy_name_company) {
                $billPayInstallment->customer_name = $billPayInstallment->customer_fancy_name_company;
            } else if ($billPayInstallment->customer_company_name) {
                $billPayInstallment->customer_name = $billPayInstallment->customer_company_name;
            }

            $billPayInstallment->totalLaunchInstallments = (new BillsToPay)->getAmountOfInstallmentsOfRelease($billPayInstallment->id_bills_to_pay);
        }, $response->data);

        array_map(function ($element) {
            $element->customer_name = ucwords(mb_strtolower($element->customer_name), ' ');
            $element->totalLaunchInstallments = (new BillsToPay)->getAmountOfInstallmentsOfRelease($element->id_bills_to_pay);
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

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $costCentersForIndexes = (new RecursiveCostCenter())->recursiveTree(0, ['id_type' => 1]);

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

            $value = $item->amount_paid ?? $item->value_of_installments;
            $item->amount_paid = Util::maskMoney($item->amount_paid);
            $item->value_of_installments = Util::maskMoney($item->value_of_installments);
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
