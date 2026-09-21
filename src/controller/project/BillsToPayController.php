<?php

namespace RR\controller\project;

use PDOException;
use RR\model\ModelGenerico;
use RR\libs\BoxAlert;
use RR\libs\Date;
use RR\libs\Pagination;
use RR\libs\RecursiveCostCenter;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\Customer;
use RR\model\CustomerType;
use RR\model\FormOfPayment;
use RR\model\BillsToPay;
use RR\model\BillsToPayInstallment;
use RR\model\CostCenter;
use RR\model\GerenciaPost;

use RR\model\PurchaseRequests;
use function RR\Controller\redirect;

class BillsToPayController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'bills-to-pay';
        $this->model = new BillsToPay();
        $this->dir = 'bills-to-pay';
        $this->table = 'bills_to_pay';
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
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
        $response = $this->model->getAndFilterAllItem($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($response->count, $rows);

        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);

        array_map(function ($element) {
            $installments = (new BillsToPayInstallment())->getAllInstallmentsByBillsToPayId($element->id);

            $element->value = Util::maskMoney(array_reduce($installments, function ($accumulator, $item) {
                $accumulator += $item->value_of_installments;
                return $accumulator;
            }, 0));

            $element->bgtr = $element->status ? 'success' : 'danger';
            $element->label = $element->status ? 'Ativo' : 'Inativo';
        }, $response->data);

        $paymentStatus = $modelGenerico->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $customers = (new Customer())->getAndFilterAllCustomer(0, ['status' => 1, 'id_customer_type' => (new CustomerType())->getIdByName('Fornecedor'), "id_branch" => $_SESSION['RR']->branch->current->id], 0);

        array_map(function ($item) {
            $item->name = $item->customer_fancy_name ?? $item->customer_name;
        }, $response->data);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/global.js");

        $customers = (new Customer)->getWithFiltersAllItems(
            [
                (object)['columns' => ['status' => (object)["value" => 1]]],
                (object)["table" => "customer_branches", "columns" => ["id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]]],
                (object)["table" => "client_type_resource_types", "columns" => ["id_customer_type" => (object)['comparison' => 'IN', 'value' => [1, 2]]]]
            ],
            [],
            [
                "orderBy" => "coalesce(this->table.fancy_name_company, this->table.company_name), this->table.name asc",
                "groupBy" => "customer.id"
            ]
        )->data;

        array_map(function ($item) {
            $item->option_name = $item->fancy_name_company ?? ($item->company_name ?? $item->name);
        }, $customers);

        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $response = $this->model->handleFormAdd($_POST);

        redirect(!$response->error ? "{$this->route}/installments/$response->lastId" : "{$this->route}/addItem");
    }

    public function entry($itemId)
    {
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/global.js");

        $purchase = (new PurchaseRequests)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_commission_to_pay' => (object) [
                            'comparison' =>
                                'EQUAL',
                                'value' => $itemId
                        ]
                    ]
                ],
            ]
        )->data;

        $item = $this->model->getItemById($itemId,
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'customer',
                    'columns' => [
                        'name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']
                    ]
                ]
            ]
        );

        Secure::branch($item->id_branch, $this->route);
        $comissao = 0;

        if ($item->customer_fancy_name_company) {
            $item->customer_name = $item->customer_fancy_name_company;
        } else if ($item->customer_company_name) {
            $item->customer_name = $item->customer_company_name;
        }

        $amount = Util::maskMoney(array_reduce((new BillsToPayInstallment)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_bills_to_pay' => (object)[
                            'comparison' => "=",
                            "value" => $itemId
                        ],
                        'status_payment' => (object)[
                            'comparison' => "!=",
                            "value" => 3
                        ]
                    ]
                ]
            ]
        )->data, function ($accumulator, $item)
        {
            return $accumulator + ($item->status_payment == 2 && $item->payment_transaction != 3 ? $item->amount_paid : $item->value_of_installments);
        }, 0));

        $content_header = (object)['title' => "#{$itemId} - {$item->customer_fancy_name_company}", 'subtitle' => 'Lançamento'];
        $nav_tabs = [
            (object)['text' => 'Lançamento', 'route' => URL . "{$this->route}/entry/$itemId", 'class' => 'active'],
            (object)['text' => 'Parcelas', 'route' => URL . "{$this->route}/installments/$itemId"],
        ];

        $customers = (new Customer)->getWithFiltersAllItems(
            [
                (object)['columns' => ['status' => (object)["value" => 1]]],
                (object)["table" => "customer_branches", "columns" => ["id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]]],
                (object)["table" => "client_type_resource_types", "columns" => ["id_customer_type" => (object)['comparison' => 'IN', 'value' => [1, 2]]]]
            ],
            [],
            [
                "orderBy" => "coalesce(this->table.fancy_name_company, this->table.company_name), this->table.name asc",
                "groupBy" => "id"
            ]
        )->data;

        array_map(function ($element) {
            if ($element->fancy_name_company) {
                $element->label = $element->fancy_name_company;
            } elseif ($element->company_name) {
                $element->label = $element->$element->company_name;
            } else {
                $element->label = "Pessoa Física";
            }

            $element->name = $element->fancy_name_company ?? ($element->company_name ?? $element->name);
        }, $customers);

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/entry.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditEntry($itemId)
    {
        Secure::check_post_method($this->route . "/entry/$itemId");

        $purchase = (new PurchaseRequests)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_commission_to_pay' => (object) [
                            'comparison' =>
                                'EQUAL',
                                'value' => $itemId
                        ]
                    ]
                ],
            ]
        )->data;

        $arrayPost = [
            'id_customer' => $_POST['id_customer'],
            'competence' => ($_POST['competence'] . "-01"),
            'id_cost_center' => $_POST['id_cost_center'],
            'id_form_of_payment' => $_POST['id_form_of_payment'],
            'status' => $_POST['status'],
            'description' => $_POST['description'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $response = $this->model->update($arrayPost, 'id', $itemId);

            if (!$response->error) {
                $installments = (new BillsToPayInstallment)->getWithFiltersAllItems([
                    (object)['columns' => [
                        'id_bills_to_pay' => (object)['comparison' => 'EQUAL', 'value' => $itemId],
                        'status_payment' => (object)['comparison' => 'NOT_IN', 'value' => [2, 3]]
                    ]]
                ]);

                if (!empty($installments->data)) {

                    if(!empty($purchase)){
                        $arrPost = [
                            'id_form_of_payment' => $_POST['id_form_of_payment'],
                            'updated_at' => date("Y-m-d H:i:s"),
                            'updated_by' => $_SESSION['RR']->user->id,
                            'id_purchase_broker' => $_POST['id_customer']
                        ];
                    }else{
                        $arrPost = [
                            'id_form_of_payment' => $_POST['id_form_of_payment'],
                            'updated_at' => date("Y-m-d H:i:s"),
                            'updated_by' => $_SESSION['RR']->user->id
                        ];
                    }

                    foreach ($installments->data as $installment) {
                        (new BillsToPayInstallment)->update($arrPost, 'id', $installment->id);
                    }
                }
            }

            if ($_POST['status'] == 0) {
                $installments = (new BillsToPayInstallment())->getAllInstallmentsByBillsToPayId($itemId);
                foreach ($installments as $portion) {
                    (new GerenciaPost())->update8191(["status_payment" => 3, "updated_at" => date("Y-m-d H:i:s"), "updated_by" => $_SESSION['RR']->user->id], 'bills_to_pay_installments', "id", $portion->id, false);
                }
            }

            header('location:' . URL . $this->route . '/entry/' . $itemId . "?edited=true");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/entry/' . $itemId . "?edited=false");
            exit;
        }
    }

    public function installments($itemId)
    {
        $billsToPayModel = new BillsToPayInstallment();
        $this->addScript(URL . "js/" . JSVERSION . "/bills-to-pay/installments.js");

        $item = $billsToPayModel->getEntryById($itemId);
        Secure::branch($item->id_branch, $this->route);

        $content_header = (object)['title' => "#{$itemId} - {$item->customer_name}", 'subtitle' => 'Parcelas', 'buttons' => [
            (object)['text' => 'Cancelar parcelas', 'class' => 'btn btn-danger btn-cancel-installments hidden', 'size' => 'sm'],
            (object)['text' => 'Adicionar Parcela', 'class' => 'btn btn-primary', 'size' => 'sm', 'attrs' => ["data-toggle" => 'modal', "data-target" => '#add-installment-modal']],
        ]];
        $nav_tabs = [
            (object)['text' => 'Lançamento', 'route' => URL . "{$this->route}/entry/$itemId"],
            (object)['text' => 'Parcelas', 'route' => URL . "{$this->route}/installments/$itemId", 'class' => 'active'],
        ];

        $installments = $billsToPayModel->getAllInstallmentsByBillsToPayId($itemId);
        foreach ($installments as $portion) {
            $portion->text = "";
            switch ($portion->status_payment) {
                case '1':
                    if ($portion->due_date <  date("Y-m-d")) {
                        $portion->bgtr = 'danger';
                        $portion->label = 'Atrasado';
                        $portion->text = 'text-red';
                    } else {
                        if ($portion->due_date == date("Y-m-d")) {
                            $portion->text = 'text-yellow';
                        }
                        $portion->bgtr = 'warning';
                        $portion->label = 'Aguardando Pagamento';
                    }
                    break;
                case '2':
                    $portion->bgtr = 'success';
                    $portion->label = 'Pago';
                    break;
                case '3':
                    $portion->bgtr = 'default';
                    $portion->label = 'Cancelado';
                    break;
            }
        }

        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/installments.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleCancelInstallments($idBill)
    {
        $_POST['id_installments'] = explode(',', $_POST['id_installments']);

        $response = $this->model->cancelAndUpdateInstallments($_POST['id_installments']);

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error != false ? 'success' : 'error',
            'title' => $response->message,
        ];

        redirect($this->route . '/installments/' . $idBill);
    }

    public function handleSubmitAddPortion($entryId)
    {
        Secure::check_post_method($this->route . "/installments/$entryId");

        $lastPortion = (new BillsToPayInstallment())->getLastNumberPortionByBillsToPayId($entryId);

        $arrPost = [
            'number_portion' => ++$lastPortion->number_portion,
            'id_bills_to_pay' => $entryId,
            'id_form_of_payment' => $_POST['id_form_of_payment'],
            'due_date' => !empty(trim($_POST['due_date'])) ? $_POST['due_date'] : NULL,
            'value_of_installments' => Util::unmaskMoney($_POST['value_of_installments']),
            'status_payment' => !empty($_POST['status_payment']) ? $_POST['status_payment'] : 1,
            'description' => $_POST['description'],
            'created_by' => $_SESSION['RR']->user->id,
        ];

        if (!empty($_POST['status_payment'])) {
            $arrPost['pay_day'] = $_POST['due_date'];
            $arrPost['amount_paid'] = Util::unmaskMoney($_POST['value_of_installments']);
            $arrPost['updated_at'] = date("Y-m-d H:i:s");
            $arrPost['updated_by'] = $_SESSION['RR']->user->id;
        }

        try {
            (new GerenciaPost())->insert7181($arrPost, 'bills_to_pay_installments', false, false);

            header('location:' . URL . $this->route . "/installments/" . $entryId);
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/installments/" . $entryId);
            exit;
        }
    }
}
