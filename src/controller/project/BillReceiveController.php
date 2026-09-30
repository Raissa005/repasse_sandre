<?php

namespace RR\controller\project;

use PDOException;
use RR\libs\Util;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Toast;
use RR\libs\Pagination;
use RR\libs\RecursiveCostCenter;
use RR\model\ModelGenerico;
use RR\model\Customer;
use RR\model\FormOfPayment;
use RR\model\BillReceive;
use RR\model\BillReceiveInstallment;
use RR\model\BillsToPay;
use RR\model\BillsToPayInstallment;
use RR\model\CustomerBalanceLog;
use RR\model\PaymentsOfSales;

use function RR\Controller\redirect;
use function RR\Controller\view;

class BillReceiveController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $title;

    public function __construct()
    {
        $this->route = 'bill-receive';
        $this->model = new BillReceive();
        $this->dir = 'bill-receive';
        $this->table = 'bill_receive';
        parent::__construct($this->route);

        $this->title = "Lançamentos";
    }

    public function index()
    {
        $modelGenerico = new ModelGenerico();

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET["date"]['start'])) {
            $_GET['date']['start'] = Date::year_month(date("Y-m-d"));
        }

        if (!isset($_GET["date"]['end'])) {
            $_GET['date']['end'] = Date::year_month(date("Y-m-d"));
        }

        $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;

        $rows = 20;
        $page = Pagination::getPage();

        $response = $this->model->getAndFiltersAllItems($_GET, ['limit' => $rows, 'page' => $page]);

        $pagination = (new Pagination())->pages($response->count, $rows);
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);

        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object)['id' => $item->id, 'icon' => 'fas fa-pencil-alt', 'href' => URL . "{$this->route}/entry/{$item->id}", 'title' => 'Editar', 'size' => 'sm', 'color' => 'primary',]);

            $installments = (new BillReceiveInstallment)->getWithFiltersAllItems([
                (object)['columns' => ['id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $item->id], 'status_payment' => (object)['comparison' => 'NOT_EQUAL', 'value' => 3], 'status_payment' => (object)['comparison' => 'NOT_EQUAL', 'value' => 3]]]
            ]);

            $item->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                $accumulator += $item->value_installment;
                return $accumulator;
            }, 0));

            $item->status = (object)['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];
        }, $response->data);

        // $response->data = array_filter($response->data, function ($item) {
        //     $installments =  (new BillReceiveInstallment)->getWithFiltersAllItems([
        //         (object)['columns' => ['id_bill_receive' => (object)['value' => $item->id]]]
        //     ]);

        //     return $installments->count > 0;
        // });

        $paymentStatus = $modelGenerico->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $customers = (new Customer())->getAndFilterAllCustomer(0, ['status' => 1, "id_branch" => $_SESSION['RR']->branch->current->id], 0);
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;

        $contentHeader = (object)[
            'route' => URL . $this->route, 'title' => 'Lançamentos', 'caption' => 'Listagem', 'buttons' => [(object)['color' => 'info', 'text' => 'Adicionar', 'href' => URL . "$this->route/add"]],
        ];

        $table = (object) [
            'config' => (object) ['responsive' => true, 'condensed' => true, 'bordered' => true, 'striped' => true],
            'thead' => [
                (object)['style' => 'width: 60px', 'class' => 'text-center', 'text' => 'Cód.', 'column' => (object)['type' => 'text', 'link' => 'id']],
                (object)['text' => 'Cliente', 'column' => (object)['type' => 'text', 'link' => 'customer_name']],
                (object)['class' => 'text-center', 'text' => 'Centro Centro', 'column' => (object)['type' => 'text', 'link' => 'cost_center_name']],
                (object)['class' => 'text-center', 'text' => 'Forma Pagamento', 'column' => (object)['type' => 'text', 'link' => 'form_of_payment_name']],
                (object)['class' => 'text-center', 'text' => 'Valor', 'column' => (object)['type' => 'text', 'link' => 'value']],
                (object)['class' => 'text-center', 'text' => 'Status', 'column' => (object)['type' => 'label', 'link' => 'status']],
                (object)['style' => 'width: 180px', 'class' => 'text-center', 'text' => 'Ações', 'column' => (object)['type' => 'button', 'link' => 'action']],
            ],
            'data' => $response->data
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function add()
    {
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/global.js");

        $content_header = (object)['title' => 'Lançamento Receber', 'subtitle' => 'Cadastrar'];

        $customers = (new customer)->getWithFiltersAllItems(
            [
                (object)['columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => 1]
                ]],
                (object)["table" => "customer_branches", "columns" => [
                    "id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]
                ]],
            ],
            [(object)['columns' => ['id', 'name', 'fancy_name_company', 'company_name']]],
            ['orderBy' => "coalesce(customer.fancy_name_company, customer.company_name, customer.name) asc"]
        );

        array_map(function ($item) {
            $item->option_name = $item->fancy_name_company ?? ($item->company_name ?? $item->name);
        }, $customers->data);

        $form_payments = (new FormOfPayment)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => 1]]]], [], ['orderBy' => 'form_of_payment.name asc']);
        $cost_centers = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);

        require APP . 'view/_templates/header.php';
        view("{$this->dir}/add.php", ['route' => $this->route, 'content_header' => $content_header, 'customers' => $customers->data, 'form_payments' => $form_payments->data, 'cost_centers' => $cost_centers]);
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAdd()
    {
        Secure::check_post_method($this->route . "/add");

        $response = $this->model->handleFormAdd($_POST);

        redirect(!$response->error ? "{$this->route}/installment/$response->lastId" : "{$this->route}/add");
    }

    public function entry(int $itemId)
    {
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/global.js");

        $item = $this->model->getItemById($itemId, [
            (object)['columns' => ['id', 'id_branch', 'id_customer', 'id_form_of_payment', 'id_cost_center', 'status', 'description']],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ]);
        Secure::branch($item->id_branch, $this->route);

        $name = $item->customer_name;
        if (!empty($item->customer_fancy_name_company)) {
            $name = $item->customer_fancy_name_company;
        } else if (!empty($item->customer_company_name)) {
            $name = $item->customer_company_name;
        }
        unset($item->customer_fancy_name_company, $item->customer_company_name);
        $item->customer_name = $name;

        $item->amount = Util::maskMoney(array_reduce((new BillReceiveInstallment)->getWithFiltersAllItems(
            [(object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id], 'status_payment' => (object)['comparison' => '!=', 'value' => 3], 'status' => (object)['comparison' => '=', 'value' => 1]]]]
        )->data, function ($accumulator, $item) {
            $accumulator += ($item->status_payment == 2 && $item->payment_transaction != 3 ? $item->amount_paid : $item->value_installment);
            return $accumulator;
        }, 0));

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);
        $customers = (new Customer)->getWithFiltersAllItems(
            [
                (object)['columns' => [
                    'status' => (object)['value' => 1], 'id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id]
                ]],
                (object)["table" => "customer_branches", "columns" => [
                    "id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]
                ]],
            ],
            [(object)['columns' => ['id', 'name', 'fancy_name_company', 'company_name']]]
        );

        array_map(function ($item) {
            $item->option_name = $item->fancy_name_company ?? ($item->company_name ?? $item->name);
        }, $customers->data);

        $form_payments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;

        $content_header = (object)['title' => "#{$itemId} - {$item->customer_name}", 'subtitle' => 'Lançamento'];

        $nav_tabs = [
            (object)['text' => 'Lançamento', 'route' => URL . "{$this->route}/entry/$itemId", 'class' => 'active'],
            (object)['text' => 'Parcelas', 'route' => URL . "{$this->route}/installment/$itemId"],
        ];

        $nav_tabs_filters = [
            (object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id]]],
            (object)['table' => 'bills_to_pay_installments', 'columns' => ['status_payment' => (object)['comparison' => '!=', 'value' => 3]]]
        ];

        if ((new BillsToPay)->getWithFiltersAllItems($nav_tabs_filters)->count > 0) {
            $nav_tabs[] = (object)['text' => 'Contas a Pagar', 'route' => URL . $this->route . '/bill-pay/' . $itemId,];
        }

        require APP . 'view/_templates/header.php';
        view("{$this->dir}/entry.php", [
            'route' =>  $this->route, 'content_header' => $content_header, 'nav_tabs' => $nav_tabs, 'route_form' =>  URL . "{$this->route}/handleSubmitEditEntry/$itemId", 'itemId' => $itemId, 'item' => $item, 'customers' => $customers->data, 'costCenters' => $costCenters, 'form_payments' => $form_payments
        ]);
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditEntry(int $itemId)
    {
        Secure::check_post_method($this->route . "/entry/$itemId");

        $this->model->handleFormEdit($itemId, $_POST);

        redirect("{$this->route}/entry/$itemId");
    }

    public function inactivateAndCancelAllInstallments(int $itemId)
    {
        $this->model->inactivateAndCancelAllInstallments($itemId);

        redirect("{$this->route}/");
    }

    public function inactivateAndCancelAllInstallmentsOfTheRelease(int $itemId)
    {
        Secure::redirectFunction(!Secure::access_admin(), "{$this->route}/edit/$itemId");

        $item = $this->model->getItemById($itemId);

        $billReceiveInstallmentModel = new BillReceiveInstallment();
        $billsToPayInstallmentModel = new BillsToPayInstallment();

        $bill_receive_installments = $billReceiveInstallmentModel->getWithFiltersAllItems(
            [(object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id]]]]
        )->data;

        $bill_pay_installments = $billsToPayInstallmentModel->getWithFiltersAllItems(
            [(object)['table' => 'bills_to_pay', 'columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id]]]]
        )->data;

        switch ($_POST['payment_transaction']) {
            case '1':
                foreach ($bill_receive_installments as $bill_receive_installment) {
                    if ($bill_receive_installment->status_payment == 2) { #pago
                        (new CustomerBalanceLog)->update(['id_customer' => $itemId, 'amount_received'], 'id', $itemId);
                        $billReceiveInstallmentModel->update(['payment_transaction' => 3, 'value_installment' => 0], 'id', $bill_receive_installment->id); #gerando credito para o cliente comprador
                    } else {
                        $billReceiveInstallmentModel->update(['status_payment' => 3], 'id', $bill_receive_installment->id);
                    }
                }

                foreach ($bill_pay_installments as $bill_pay_installment) {
                    if ($bill_pay_installment->status_payment == 2) { #pago
                        (new CustomerBalanceLog)->update(['id_customer' => $itemId, 'amount_paid'], 'id', $itemId);
                        $billsToPayInstallmentModel->update(['payment_transaction' => 3, 'value_of_installments' => 0], 'id', $bill_pay_installment->id); #gerando debitos para os cliente fornecedores
                    } else {
                        $billsToPayInstallmentModel->update(['status_payment' => 3], 'id', $bill_pay_installment->id);
                    }
                }
                break;
            case '0':
                break;
            case '2':
                foreach ($bill_receive_installments as $bill_receive_installment) {
                    if ($bill_receive_installment->status_payment != 2) { #pago
                        $billReceiveInstallmentModel->update(['status_payment' => 3], 'id', $bill_receive_installment->id);
                    }
                }

                foreach ($bill_pay_installments as $bill_pay_installment) {
                    if ($bill_pay_installment->status_payment != 2) { #pago
                        $billsToPayInstallmentModel->update(['status_payment' => 3], 'id', $bill_pay_installment->id);
                    }
                }
                break;
        }

        $bills_pay = (new BillsToPay)->getWithFiltersAllItems(
            [(object)['columns' => ['id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $item->id_bill_receive]]]]
        )->data;

        foreach ($bills_pay as $bill_pay) {
            /**Deletando lançamento de contas a pagar vazio */
            if (empty($billsToPayInstallmentModel->getWithFiltersAllItems(
                [(object)['columns' => ['id_bills_to_pay' => (object)['comparison' => '=', 'value' => $bill_pay->id], 'status_payment' => (object)['comparison' => '!=', 'value' => 3]]]]
            )->data)) {
                (new BillsToPay)->update(['status' => 0], 'id', $bill_pay->id);
            }
        }

        Toast::successToast('Parcelas canceladas com sucesso');
        redirect("{$this->route}/entry/$itemId");
    }

    public function installment(int $itemId)
    {
        $item = $this->model->getItemById($itemId, [(object)['columns' => '*'], (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]]);
        Secure::branch($item->id_branch, $this->route);

        $name = $item->customer_name;
        if (!empty($item->customer_fancy_name_company)) {
            $name = $item->customer_fancy_name_company;
        } else if (!empty($item->customer_company_name)) {
            $name = $item->customer_company_name;
        }
        unset($item->customer_fancy_name_company, $item->customer_company_name);
        $item->customer_name = $name;

        $response = (new BillReceiveInstallment())->getWithFiltersAllItems(
            [
                (object)['columns' => ['status_payment' => (object)['comparison' => '!=', 'value' => 9]]],
                (object)['columns' => ['id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object)['columns' => ['id', 'due_date', 'status_payment', 'number_portion', 'amount_paid', 'value_installment']],
                (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                (object)['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]]
            ]
        );

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object)['id' => $item->id, 'icon' => 'fas fa-pencil-alt', 'href' => URL . "bill-receive-installment/edit/{$item->id}", 'target' => '_blank', 'title' => 'Editar', 'size' => 'sm', 'color' => 'primary']);

            switch ($item->status_payment) {
                case '1':
                    if ($item->due_date <  date("Y-m-d")) {
                        $color_status = 'danger';
                        $text_status = 'Atrasado';
                        $item->tr = 'text-red';
                    } else {
                        if ($item->due_date == date("Y-m-d")) {
                            $item->tr = 'text-yellow';
                        }
                        $color_status = 'warning';
                        $text_status = 'Aguardando Pagamento';
                    }
                    break;
                case '2':
                    $color_status = 'success';
                    $text_status = 'Pago';
                    break;
                case '3':
                    $color_status = 'default';
                    $text_status = 'Cancelado';
                    break;
            }
            $item->due_date = Date::date($item->due_date);
            $item->value = $item->status_payment == 2 ? Util::maskMoney($item->amount_paid) : Util::maskMoney($item->value_installment);

            $item->form_of_payment_name = $item->form_of_payment_name ?? "-";

            $item->status = (object)['value' => $text_status, 'color' => $color_status];
        }, $response->data);

        $content_header = (object)[
            'title' => "#{$itemId} - {$item->customer_name}", 'subtitle' => 'Parcelas', 'buttons' => [
                (object)['text' => 'Adicionar Parcela', 'class' => 'btn btn-primary', 'size' => 'sm', 'attrs' => ["data-toggle" => 'modal', "data-target" => '#add-installment-modal']]
            ]
        ];

        $nav_tabs = [
            (object)['text' => 'Lançamento', 'route' => URL . "{$this->route}/entry/$itemId"],
            (object)['text' => 'Parcelas', 'route' => URL . "{$this->route}/installment/$itemId", 'class' => 'active'],
        ];

        $nav_tabs_filters = [
            (object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id]]],
            (object)['table' => 'bills_to_pay_installments', 'columns' => ['status_payment' => (object)['comparison' => '!=', 'value' => 3]]]
        ];

        if ((new BillsToPay)->getWithFiltersAllItems($nav_tabs_filters)->count > 0) {
            $nav_tabs[] = (object)['text' => 'Contas a Pagar', 'route' => URL . $this->route . '/bill-pay/' . $itemId];
        }

        $table = (object) [
            'config' => (object) ['responsive' => true, 'condensed' => true, 'bordered' => true, 'striped' => true],
            'thead' => [
                (object)['style' => 'width: 60px', 'class' => 'text-center', 'text' => 'Nº', 'column' => (object)['type' => 'text', 'link' => 'number_portion']],
                (object)['class' => 'text-center', 'text' => 'Data Vencimento', 'column' => (object)['type' => 'text', 'link' => 'due_date']],
                (object)['class' => 'text-center', 'text' => 'Forma Pagamento', 'column' => (object)['type' => 'text', 'link' => 'form_of_payment_name']],
                (object)['class' => 'text-center', 'text' => 'Status', 'column' => (object)['type' => 'label', 'link' => 'status']],
                (object)['class' => 'text-center', 'text' => 'Valor', 'column' => (object)['type' => 'text', 'link' => 'value']],
                (object)['style' => 'width: 180px', 'class' => 'text-center', 'text' => 'Ações', 'column' => (object)['type' => 'button', 'link' => 'action']],
            ],
            'data' => $response->data
        ];

        $form_payments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;

        require APP . 'view/_templates/header.php';
        view("{$this->dir}/installment.php", ['route' => $this->route, 'itemId' => $itemId, 'content_header' => $content_header, 'nav_tabs' => $nav_tabs, 'table' => $table, 'form_payments' => $form_payments]);
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddPortion($entryId)
    {
        Secure::check_post_method($this->route . "/installments/$entryId");

        $response = (new BillReceiveInstallment)->submitAddPortion($entryId);
        if (!$response->error) (new PaymentsOfSales)->submitAddPortionFromBillReceive($entryId, $response->lastId);

        Toast::checkResponse($response->error, $response->message);

        redirect($this->route . "/installment/{$entryId}");
    }

    public function billPay(int $itemId)
    {
        $item = $this->model->getItemById($itemId, [
            (object)['columns' => '*'],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name']]]
        ]);
        Secure::branch($item->id_branch, $this->route);

        $content_header = (object)['title' => "#{$itemId} - {$item->customer_name}", 'subtitle' => 'Contas a Pagar'];
        $nav_tabs = [
            (object)['text' => 'Lançamento', 'route' => URL . "{$this->route}/entry/$itemId"],
            (object)['text' => 'Parcelas', 'route' => URL . "{$this->route}/installment/$itemId"],
        ];

        $nav_tabs_filters = [
            (object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id]]],
            (object)['table' => 'bills_to_pay_installments', 'columns' => ['status_payment' => (object)['comparison' => '!=', 'value' => 3]]]
        ];

        if ((new BillsToPay)->getWithFiltersAllItems($nav_tabs_filters)->count > 0) {
            $nav_tabs[] = (object)['text' => 'Contas a Pagar', 'route' => URL . $this->route . '/bill-pay/' . $itemId, 'class' => 'active'];
        }

        $bills_pay_filters = [
            (object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id]]],
            (object)['table' => 'bills_to_pay_installments', 'columns' => ['status_payment' => (object)['comparison' => '!=', 'value' => 3]]]
        ];

        $bills_pay_columns = [
            (object)['columns' => '*'],
            (object)['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ];

        $bills_pay_options = ['group' => 'this->table.id', 'order' => 'this->table.id asc'];

        $bills_pay = array_map(function ($bill_pay) {
            $bill_pay_installment_filters = [(object)['columns' => ['id_bills_to_pay' => (object)['comparison' => '=', 'value' => $bill_pay->id], 'status_payment' => (object)['comparison' => '!=', 'value' => 3], 'status' => (object)['comparison' => '=', 'value' => 1]]]];

            $bill_pay->value = Util::maskMoney(array_reduce(
                (new BillsToPayInstallment)->getWithFiltersAllItems($bill_pay_installment_filters)->data,
                function ($accumulator, $element) {
                    return $accumulator + ($element->status_payment == 2 ? $element->amount_paid : $element->value_of_installments);
                }
            ));

            $bill_pay->name = '';
            if (!empty($bill_pay->customer_fancy_name_company)) {
                $bill_pay->name = $bill_pay->customer_fancy_name_company;
            } else if (!empty($bill_pay->customer_company_name)) {
                $bill_pay->name = $bill_pay->customer_company_name;
            } else if (!empty($bill_pay->customer_name)) {
                $bill_pay->name = $bill_pay->customer_name;
            }

            $bill_pay->competence = Date::date($bill_pay->competence);

            $bill_pay->action = [
                (object)['id' => $bill_pay->id, 'icon' => "fas fa-file-invoice-dollar", 'href' => URL . "bills-to-pay/entry/{$bill_pay->id}", 'title' => 'Lançamento', 'size' => 'sm', 'color' => 'info', 'attr' => ['target' => '_blank']],
                (object)['id' => $bill_pay->id, 'icon' => "fas fa-money-check-alt", 'href' => URL . "bills-to-pay/installments/{$bill_pay->id}", 'title' => 'Parcelas', 'size' => 'sm', 'color' => 'info', 'attr' => ['target' => '_blank']],
            ];
            return $bill_pay;
        }, (new BillsToPay)->getWithFiltersAllItems($bills_pay_filters, $bills_pay_columns, $bills_pay_options)->data);

        $table = (object) [
            'config' => (object) ['responsive' => true, 'condensed' => true, 'bordered' => true, 'striped' => true],
            'thead' => [
                (object)['style' => 'width: 60px', 'class' => 'text-center', 'text' => 'Cód', 'column' => (object)['type' => 'text', 'link' => 'id']],
                (object)['class' => 'text-left', 'text' => 'Data Competência', 'column' => (object)['type' => 'text', 'link' => 'competence']],
                (object)['class' => 'text-left', 'text' => 'Fornecedor', 'column' => (object)['type' => 'text', 'link' => 'name']],
                (object)['class' => 'text-center', 'text' => 'Custo Centro', 'column' => (object)['type' => 'text', 'link' => 'cost_center_name']],
                (object)['class' => 'text-center', 'text' => 'Forma Pagamento', 'column' => (object)['type' => 'text', 'link' => 'form_of_payment_name']],
                (object)['class' => 'text-center', 'text' => 'Valor', 'column' => (object)['type' => 'text', 'link' => 'value']],
                (object)['style' => 'width: 180px', 'class' => 'text-center', 'text' => 'Ações', 'column' => (object)['type' => 'button', 'link' => 'action']],
            ],
            'data' => $bills_pay
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/bill-pay.php';
        require APP . 'view/_templates/footer.php';
    }
}
