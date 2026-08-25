<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Date;
use RR\libs\Util;
use RR\model\Banks;
use RR\libs\Pagination;
use RR\model\BillReceive;
use RR\model\BankAccounts;
use RR\model\CheckControl;
use RR\model\CheckControlTimeline;
use RR\model\BillsToPayInstallment;
use RR\model\BillReceiveInstallment;

class CheckControlController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new CheckControl;

        parent::__construct();
    }

    public function getAccountsById()
    {
        $item = $this->model->getItemById($_POST['checkId']);
        $accounts = (new BankAccounts)->getWithFiltersAllItems([(object)['columns' => ['status' => ['value' => true]]]]);

        $message = '';
        $error = false;
        if ($accounts->count <= 0) {
            $error = true;
            $message = 'Você precisa ter uma "Conta Bancária" cadastrada para continuar!';
        }

        echo json_encode(['error' => $error, 'message' => $message, 'check' => $item]);
    }

    public function getBillReceiveById()
    {
        $item = $this->model->getItemById($_POST['checkId']);

        if (!empty($item->id_bill_receive)) {
            $lastPortion = (new BillReceiveInstallment)->getLastNumberPortionByBillsReceiveId($item->id_bill_receive);

            $arrPost = [
                'value_installment' => $item->value,
                'id_form_of_payment' => 1,
                'id_bill_receive' => $item->id_bill_receive,
                'number_portion' => ++$lastPortion->number_portion,
                'status_payment' => !empty($_POST['status_payment']) ? $_POST['status_payment'] : 1,
                'description' => $_POST['description'],
                'created_by' => $_SESSION['RR']->user->id,
            ];
        }
    }

    public function addNewInstallment()
    {
        $item = (new CheckControl)->getItemById($_POST['checkId']);

        (new CheckControl)->update(['status_check' => 4], 'id', $item->id);

        $arrPost = [
            'status' => true,
            'status_icon' => 11,
            'id_check' => $item->id,
            'comment' => "Cheque devolvido!",
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['RR']->user->id
        ];

        (new CheckControlTimeline)->insert($arrPost);

        if (!empty($_POST['checkBillsToPayId'])) {
            unset($arrPost);

            $lastPortion = (new BillsToPayInstallment)->getLastNumberPortionByBillsToPayId($_POST['checkBillsToPayId']);
            $newInstallment = ++$lastPortion->number_portion;

            $arrPost = [
                'due_date' => $_POST['dueDate'],
                'number_portion' => $newInstallment,
                'created_by' => $_SESSION['RR']->user->id,
                'id_form_of_payment' => $_POST['formPaymentId'],
                'id_bills_to_pay' => $_POST['checkBillsToPayId'],
                'value_of_installments' => Util::unmaskMoney($_POST['price']),
                'description' => "Parcela Nº{$newInstallment}/{$newInstallment} gerada através do cheque <a href='" . URL . "check-control/editItem/$item->number_check' target='_blank'>Nº{$item->number_check}</a>, cancelado!"
            ];

            $response = (new BillsToPayInstallment)->insert($arrPost);

            if (!$response->error) {
                unset($arrPost);

                $arrPost = [
                    'status' => true,
                    'status_icon' => 12,
                    'id_check' => $item->id,
                    'id_installment' => $newInstallment,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $_SESSION['RR']->user->id,
                    'id_bills_to_pay' => $_POST['checkBillsToPayId'],
                    'comment' => "Nova parcela pagar <a href='" . URL . "bills-to-pay-installment/editItem/{$newInstallment}' target='_blank'>Nº{$newInstallment}/{$newInstallment}</a> gerada!"
                ];

                $checkTimeLine = (new CheckControlTimeline)->insert($arrPost);
            }
        }

        if (!empty($_POST['checkBillReceiveId'])) {
            unset($arrPost);

            $lastPortion = (new BillReceiveInstallment)->getLastNumberPortionByBillsReceiveId($_POST['checkBillReceiveId']);
            $newInstallment = ++$lastPortion->number_portion;

            $arrPost = [
                'status_payment' => 1,
                'due_date' => $_POST['dueDate'],
                'number_portion' => $newInstallment,
                'created_by' => $_SESSION['RR']->user->id,
                'id_form_of_payment' => $_POST['formPaymentId'],
                'id_bill_receive' => $_POST['checkBillReceiveId'],
                'value_installment' => Util::unmaskMoney($_POST['price']),
                'description' => "Parcela Nº{$newInstallment}/{$newInstallment} gerada através do cheque <a href='" . URL . "check-control/editItem/$item->number_check' target='_blank'>Nº{$item->number_check}</a>, cancelado!"
            ];

            $response = (new BillReceiveInstallment)->insert($arrPost);

            if (!$response->error) {
                unset($arrPost);

                $arrPost = [
                    'status' => true,
                    'status_icon' => 10,
                    'id_check' => $item->id,
                    'id_installment' => $newInstallment,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $_SESSION['RR']->user->id,
                    'id_bill_receive' => $_POST['checkBillReceiveId'],
                    'comment' => "Nova parcela receber <a href='" . URL . "bill-receive-installment/edit/{$newInstallment}' target='_blank'>Nº{$newInstallment}/{$newInstallment}</a> gerada!"
                ];

                (new CheckControlTimeline)->insert($arrPost);
            }
        } else {
            unset($arrPost);

            $arrPost = [
                'id_cost_center' => $_POST['costCenter'],
                'created_by' => $_SESSION['RR']->user->id,
                'id_customer' => $_POST['checkForwardedBy'],
                'id_form_of_payment' => $_POST['formPaymentId'],
                'id_branch' => $_SESSION['RR']->branch->current->id,
                'description' => "Lançamento gerado através do cheque <a href='" . URL . "check-control/editItem/$item->number_check' target='_blank'>Nº{$item->number_check}</a>, cancelado!"
            ];

            $response = (new BillReceive)->insert($arrPost);

            if (!$response->error) {
                unset($arrPost);

                $arrPost = [
                    'number_portion' => 1,
                    'status_payment' => 1,
                    'due_date' => $_POST['dueDate'],
                    'id_bill_receive' => $response->lastId,
                    'created_by' => $_SESSION['RR']->user->id,
                    'id_form_of_payment' => $_POST['formPaymentId'],
                    'value_installment' => Util::unmaskMoney($_POST['price']),
                    'description' => "Parcela gerada através do cheque <a href='" . URL . "check-control/editItem/$item->number_check' target='_blank'>Nº{$item->number_check}</a>, cancelado!"
                ];

                $intallment = (new BillReceiveInstallment())->insert($arrPost);

                if (!$intallment->error) {
                    unset($arrPost);

                    $arrPost = [
                        'status' => true,
                        'status_icon' => 10,
                        'id_installment' => 1,
                        'id_check' => $item->id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'id_bill_receive' => $response->lastId,
                        'created_by' => $_SESSION['RR']->user->id,
                        'comment' => "Nova parcela receber <a href='" . URL . "bill-receive-installment/edit/1' target='_blank'>Nº1/1</a> gerada!"
                    ];

                    (new CheckControlTimeline)->insert($arrPost);
                }
            }
        }

        echo json_encode(['error' => $response->error, 'message' => $response->message]);
    }

    public function getCustomersChecks()
    {
        $checkFilters = [
            (object)['columns' => ['status' => ['value' => true]]],
            (object)['columns' => ['status_check' => ['value' => true]]],
            (object)['columns' => ['forwarded_by' => ['value' => $_POST['customerId']]]],
            (object)['where' => " AND this->table.id_bill_receive IS NULL AND this->table.id_bills_to_pay IS NULL"]
        ];

        if (!empty($_POST['name'])) {
            $checkFilters[] = (object)['where' => " AND (
                ucase(this->table.owner_check) LIKE ucase('%" . $_POST['name'] . "%')
                OR ucase(customer.name) LIKE ucase('%" . $_POST['name'] . "%')
                OR ucase(customer.fancy_name_company) LIKE ucase('%" . $_POST['name'] . "%')
                OR ucase(customer.company_name) LIKE ucase('%" . $_POST['name'] . "%'))"];
        }

        $response = (new CheckControl)->getWithFiltersAllItems(
            $checkFilters,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'banks', 'columns' => ['name' => ['bank_name']]],
                (object)['table' => 'customer', 'columns' => ['name' => ['name'], 'company_name' => ['company_name'], 'fancy_name_company' => ['fancy_name_company']]]
            ],
            [
                'limit' => $_POST['limit'],
                'page' => $_POST['page'],
                'order' => 'COALESCE(customer.fancy_name_company, customer.company_name, customer.name) ASC',
            ]
        );

        $data = array_map(function ($item) {
            if (!empty($item->fancy_name_company)) {
                $item->name = $item->fancy_name_company;
                $item->cpf_cnpj = $item->cnpj ?? ' - ';
            } else if (!empty($item->company_name)) {
                $item->name = $item->company_name;
                $item->cpf_cnpj = $item->cnpj ?? ' - ';
            } else {
                $item->name = $item->name;
                $item->cpf_cnpj = $item->person_registration ?? ' - ';
            }

            switch ($item->status_check) {
                case '1':
                    $item->status_check = (object)[
                        'value' => 'Aberto',
                        'color' => 'warning'
                    ];

                    $item->due_date <= date("Y-m-d") ? $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'success'] : $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'warning'];
                    break;
                case '2':
                    $item->status_check = (object)[
                        'value' => 'Compensado',
                        'color' => 'success'
                    ];

                    $item->due_date <= date("Y-m-d") ? $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'success'] : $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'warning'];
                    break;
                case '3':
                    $item->status_check = (object)[
                        'value' => 'Cancelado',
                        'color' => 'default'
                    ];

                    $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'default'];
                    break;
                case '4':
                    $item->status_check = (object)[
                        'value' => 'S/ Fundo',
                        'color' => 'danger'
                    ];

                    $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'danger'];
                    break;
            };

            return $item;
        }, $response->data);

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data, 'pagination' => (new Pagination)->pages($response->count, $_POST['limit'], 10, $_POST['page'])]);
        exit;
    }

    public function getAllChecks()
    {
        $checkFilters = [
            (object)['columns' => ['status' => ['value' => true]]],
            (object)['columns' => ['status_check' => ['value' => true]]],
            (object)['where' => " AND this->table.id NOT IN (SELECT lcc.id_check FROM linked_check_control lcc WHERE lcc.status = true AND lcc.id_check = this->table.id)"]
        ];

        if (!empty($_POST['deleteItems'])) {
            array_push($checkFilters, (object)['columns' => ['id' => (object)['comparison' => 'NOT_IN', 'value' => $_POST['deleteItems']]]]);
        }

        if (!empty($_POST['name'])) {
            $checkFilters[] = (object)['where' => " AND (
                ucase(this->table.owner_check) LIKE ucase('%" . $_POST['name'] . "%')
                OR ucase(customer.name) LIKE ucase('%" . $_POST['name'] . "%')
                OR ucase(customer.fancy_name_company) LIKE ucase('%" . $_POST['name'] . "%')
                OR ucase(customer.company_name) LIKE ucase('%" . $_POST['name'] . "%'))"];
        }

        $response = (new CheckControl)->getWithFiltersAllItems(
            $checkFilters,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'banks', 'columns' => ['name' => ['bank_name']]],
                (object)['table' => 'customer', 'columns' => ['name' => ['name'], 'company_name' => ['company_name'], 'fancy_name_company' => ['fancy_name_company']]]
            ],
            [
                'limit' => $_POST['limit'],
                'page' => $_POST['page'],
                'order' => 'COALESCE(customer.fancy_name_company, customer.company_name, customer.name) ASC',
            ]
        );

        $data = array_map(function ($item) {
            if (!empty($item->fancy_name_company)) {
                $item->name = $item->fancy_name_company;
                $item->cpf_cnpj = $item->cnpj ?? ' - ';
            } else if (!empty($item->company_name)) {
                $item->name = $item->company_name;
                $item->cpf_cnpj = $item->cnpj ?? ' - ';
            } else {
                $item->name = $item->name;
                $item->cpf_cnpj = $item->person_registration ?? ' - ';
            }

            switch ($item->status_check) {
                case '1':
                    $item->status_check = (object)[
                        'value' => 'Aberto',
                        'color' => 'warning'
                    ];

                    $item->due_date <= date("Y-m-d") ? $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'success'] : $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'warning'];
                    break;
                case '2':
                    $item->status_check = (object)[
                        'value' => 'Compensado',
                        'color' => 'success'
                    ];

                    $item->due_date <= date("Y-m-d") ? $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'success'] : $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'warning'];
                    break;
                case '3':
                    $item->status_check = (object)[
                        'value' => 'Cancelado',
                        'color' => 'default'
                    ];

                    $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'default'];
                    break;
                case '4':
                    $item->status_check = (object)[
                        'value' => 'S/ Fundo',
                        'color' => 'danger'
                    ];

                    $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'danger'];
                    break;
            };

            return $item;
        }, $response->data);

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data, 'pagination' => (new Pagination)->pages($response->count, $_POST['limit'], 10, $_POST['page'])]);
        exit;
    }

    public function getCheckById()
    {
        if (!empty($_POST['checkId'])) {
            $response = (new CheckControl)->getItemById($_POST['checkId']);

            if (!empty($response)) {
                $error = false;
                $message = '';
            } else {
                $error = true;
                $message = 'Item não encontrado!';
            }
        } else {
            $error = true;
            $message = 'Número do cheque não informado!';
        }

        echo json_encode(['error' => $error, 'message' => $message, 'check' => $response]);
    }

    public function getBankById()
    {
        if (!empty($_POST['bankId'])) {
            $response = (new Banks)->getItemById($_POST['bankId']);

            if (!empty($response)) {
                $error = false;
                $message = '';
            } else {
                $error = true;
                $message = 'Item não encontrado!';
            }
        } else {
            $error = true;
            $message = 'Banco não informado!';
        }

        echo json_encode(['error' => $error, 'message' => $message, 'bank' => $response]);
    }
}
