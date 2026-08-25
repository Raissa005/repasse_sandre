<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Pagination;
use RR\libs\Util;
use RR\model\BillReceiveInstallment;
use RR\model\BillsToPayInstallment;
use RR\model\Branch;
use RR\model\Customer;

class CustomerController extends Ajax
{
    public $model;

    function __construct()
    {
        parent::__construct();
        $this->model = new Customer();
    }

    public function calculateCustomerCredit()
    {
        if (!empty($_POST)) {
            $credit = 0;

            foreach ((new BillsToPayInstallment)->getWithFiltersAllItems([
                (object)['columns' => ['status_payment' => (object)['comparison' => 'EQUAL', 'value' => 2]]],
                (object)['table' => 'bills_to_pay', 'columns' => ['id_customer' => (object)['comparison' => 'EQUAL', 'value' => $_POST['customer_id']]]]
            ])->data as $bill) {
                if ($bill->payment_transaction == 3) {
                    /**Parcelas que geram crédito */
                    $credit += $bill->amount_paid - $bill->value_of_installments;
                }

                if ($bill->id_form_of_payment == 9) {
                    /**Todas as parcelas que foram pagas com o crédito */
                    $credit -= $bill->amount_paid;
                }
            }

            foreach ((new BillReceiveInstallment)->getWithFiltersAllItems([
                (object)['columns' => ['status_payment' => (object)['comparison' => 'EQUAL', 'value' => 2]]],
                (object)['table' => 'bill_receive', 'columns' => ['id_customer' => (object)['comparison' => 'EQUAL', 'value' => $_POST['customer_id']]]]
            ])->data as $bill) {
                if ($bill->payment_transaction == 3) {
                    /**Parcelas que geram crédito */
                    $credit -= $bill->amount_paid - $bill->value_installment;
                }

                if ($bill->id_form_of_payment == 9) {
                    /**Todas as parcelas que foram pagas com o crédito */
                    $credit += $bill->amount_paid;
                }
            }

            $data = (object)["credit" => $credit];
            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
            exit;
        }
    }

    public function customerBalance()
    {
        $data = $this->model->calculateCustomerCredit($_POST['customer_id']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }

    public function getCustomersSuppliersAndBuilders()
    {
        if (!empty($_POST)) {
            $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);

            $customer_filters = [
                (object)['columns' => ['status' => (object)['value' => 1]]],
                (object)['table' => 'customer_branches', 'columns' => ['id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id]]],
                (object)["table" => "client_type_resource_types", "columns" => ["id_customer_type" => (object)["value" => [9, 11]]]]
            ];

            if (isset($_POST['name']) && !empty($_POST['name'])) {
                $customer_filters[] = (object)['where' => " AND (
                    ucase(this->table.name) LIKE ucase('%" . $_POST['name'] . "%')
                    OR ucase(this->table.fancy_name_company) LIKE ucase('%" . $_POST['name'] . "%')
                    OR ucase(this->table.company_name) LIKE ucase('%" . $_POST['name'] . "%'))"];
            }

            if ($_SESSION['RR']->profile->access >= 30 && $branch->restrict_owner_data == 1) {
                $customer_filters[] = (object)['columns' => ['created_by' => (object)['value' => $_SESSION['RR']->user->id]]];
            }

            $response_customer = (new Customer)->getWithFiltersAllItems(
                $customer_filters,
                [
                    (object)['columns' => ['*']], (object)['table' => 'cities', 'columns' => ['name' => ['cities_name'], 'uf' => ['cities_uf']]]
                ],
                [
                    'limit' => $_POST['limit'],
                    'page' => $_POST['page'],
                    'order' => 'COALESCE(this->table.fancy_name_company, this->table.company_name, this->table.name) ASC',
                ]
            );

            $data = array_map(function ($customer) {
                if (!empty($customer->fancy_name_company)) {
                    $customer->name = $customer->fancy_name_company;
                    $customer->cpf_cnpj = $customer->cnpj;
                } else if (!empty($customer->company_name)) {
                    $customer->name = $customer->company_name;
                    $customer->cpf_cnpj = $customer->cnpj;
                } else {
                    $customer->name = $customer->name;
                    $customer->cpf_cnpj = $customer->person_registration;
                }
                return $customer;
            }, $response_customer->data);

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data, 'pagination' => (new Pagination)->pages($response_customer->count, $_POST['limit'], 10, $_POST['page'])]);
            exit;
        }
    }
}
