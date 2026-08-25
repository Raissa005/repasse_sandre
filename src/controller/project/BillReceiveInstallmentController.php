<?php

namespace RR\controller\project;

use RR\libs\Util;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Pagination;
use RR\libs\DeleteFile;
use RR\libs\FileUploader;
use RR\libs\RecursiveCostCenter;
use RR\model\User;
use RR\model\Banks;
use RR\model\Sales;
use RR\model\Branch;
use RR\model\Customer;
use RR\model\BillsToPay;
use RR\model\BillReceive;
use RR\model\GerenciaPost;
use RR\model\BankAccounts;
use RR\model\ModelGenerico;
use RR\model\FormOfPayment;
use RR\model\UserPosition;
use RR\model\BranchUserPosition;
use RR\model\BillsToPayInstallment;
use RR\model\BillReceiveInstallment;
use RR\model\SalesChargePaymentAgreement;
use RR\model\ArrangementPaymentChargesInvoiceReceiveInstallment;
use PDOException;
use RR\libs\ShowPage;
use RR\libs\TableDefault;
use RR\model\CheckControl;
use RR\model\CheckControlTimeline;
use RR\model\Contract;
use RR\model\CostCenter;
use RR\model\CustomerBalanceLog;
use RR\model\PaymentsOfSales;
use RR\model\StandardContract;
use RR\model\SummaryInvolved;
use RR\model\SummarySale;

use function RR\Controller\redirect;
use function RR\controller\view;

class BillReceiveInstallmentController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'bill-receive-installment';
        $this->dir = 'bill-receive-installment';
        $this->model = new BillReceiveInstallment();
        $this->table = 'bill_receive_installment';
        parent::__construct($this->route);
    }

    public function index()
    {
        Secure::access_admin(true);

        $modelGenerico = new ModelGenerico();

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['status_payment'])) {
            $_GET['status_payment'] = 1;
        }

        if (!isset($_GET['date']['start'])) {
            $_GET['date']['start'] = Date::year_month(date("Y-m-d")) . '-01';
        }

        if (!isset($_GET['date']['end'])) {
            $_GET['date']['end'] = date("Y-m-t");
        }

        $filtersBillReceive = [
            (object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']]]]
        ];

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        } elseif (!empty($_GET['branches']) && !in_array("all", $_GET['branches'])) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_branch' => (object)['comparison' => 'IN', 'value' => $_GET['branches']]]]);
        }

        if (!empty($_GET['id'])) {
            array_push($filtersBillReceive, (object)['columns' => ['id' => ['comparison' => 'EQUAL', 'value' => $_GET['id']]]]);
        }

        if (!empty($_GET['id_customer'])) {
            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_customer' => ['comparison' => 'EQUAL', 'value' => $_GET['id_customer']]]]);
        }

        if (!empty($_GET['id_form_of_payment'])) {
            array_push($filtersBillReceive, (object)['columns' => ['id_form_of_payment' => ['comparison' => 'EQUAL', 'value' => $_GET['id_form_of_payment']]]]);
        }

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

        if (!empty($_GET['id_cost_center'])) {
            $costCentersIds = (new RecursiveCostCenter)->recursiveGetChildren($_GET['id_cost_center'], [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']],
                        'id_type' => (object)['comparison' => 'EQUAL', 'value' => 2]
                    ]
                ]
            ]);

            array_push($filtersBillReceive, (object)['table' => 'bill_receive', 'columns' => ['id_cost_center' => ['comparison' => 'IN', 'value' => $costCentersIds]]]);
        }

        if (!empty($_GET['status_payment'])) {
            array_push($filtersBillReceive, (object)['columns' => ['status_payment' => ['comparison' => 'EQUAL', 'value' => $_GET['status_payment']]]]);
        }

        $columnsBillReceive = [
            (object)['columns' => ['*']],
            (object)['table' => 'bill_receive', 'columns' => ['id_customer' => ['id_customer']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]]
        ];

        $rows = 20;
        $page = Pagination::getPage();

        $response = $this->model->getWithFiltersAllItems($filtersBillReceive, $columnsBillReceive, ['limit' => $rows, 'page' => $page]);

        array_map(function ($item) {
            $item->cost_center = $item->cost_center_name;
            $item->form_payment = $item->form_of_payment_name;

            $customer = (new Customer)->getItemById($item->id_customer, [(object)['columns' => ['id', 'name', 'fancy_name_company', 'company_name']]]);
            $item->owner = $customer->name;
            if (!empty($customer->fancy_name_company)) {
                $item->owner = $customer->fancy_name_company;
            } else if (!empty($customer->company_name)) {
                $item->owner = $customer->company_name;
            }

            $status_bg = '';
            $status_text = '';

            switch ($item->status_payment) {
                case '1':
                    if ($item->due_date < date("Y-m-d")) {
                        $status_bg = 'danger';
                        $status_text = 'Atrasado';
                        $item->color = 'text-red';
                    } else {
                        if ($item->due_date == date("Y-m-d")) {
                            $item->color = 'text-yellow';
                        }
                        $status_bg = 'warning';
                        $status_text = 'Aguard. Pagam.';
                    }
                    break;
                case '2':
                    $status_bg = 'success';
                    $status_text = 'Pago';
                    break;
                case '3':
                    $status_bg = 'default';
                    $status_text = 'Cancelado';
                    break;
            }

            $item->status = (object)['bg' => $status_bg, 'text' => $status_text];

            $item->due_date = Date::date($item->due_date);
            $item->fragment = $item->number_portion . '/' . (new BillReceive)->getAmountOfInstallmentsOfRelease($item->id_bill_receive);
            $item->value_installment = $item->status_payment == 2 ? Util::maskMoney($item->amount_paid) : Util::maskMoney($item->value_installment);

            /**Botões de ações */
            $item->action = [
                (object)[
                    'id' => $item->id,
                    'title' => 'Editar',
                    'href' => URL . "{$this->route}/edit/$item->id",
                    'icon' => 'fas fa-pencil-alt',
                    'size' => 'sm',
                    'bg' => 'primary',
                    'styles' => ['width' => '35px'],
                    'attrs' => [],
                ],
            ];

            if (!empty($item->cost_center_id)) {
                if (!empty($_GET['column_complete_cost_center'])) {
                    $costCenter = (new CostCenter)->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $item->cost_center_id]]]]);

                    if (!empty($costCenter->id_father)) {
                        $father = (new CostCenter)->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $costCenter->id_father]]]], [(object)['columns' => ['name']]]);

                        $item->cost_center_name = ucwords(mb_convert_case($father->name, MB_CASE_LOWER, 'UTF-8')) . " / " . ucwords(mb_convert_case($item->cost_center_name, MB_CASE_LOWER, 'UTF-8'));
                    }
                } else {
                    $item->cost_center_name = ucwords(mb_convert_case($item->cost_center_name, MB_CASE_LOWER, 'UTF-8'));
                }
            } else {
                $item->cost_center_name = ucwords(mb_convert_case($item->cost_center_name, MB_CASE_LOWER, 'UTF-8'));
            }
        }, $response->data);

        $payment_status = $modelGenerico->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $cost_centers = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);
        $form_payments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $customers = (new Customer())->getWithFiltersAllItems([
            (object)[
                'columns' => [
                    'status' => (object)['value' => 1],
                    'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]
                ]
            ],
            (object)[
                'table' => 'client_type_resource_types',
                'columns' => [
                    'id_customer_type' => (object)['comparison' => 'EQUAL', 'value' => [9, 11]],
                ]
            ],
        ], [
            (object)[
                'columns' => ['id', 'name', 'fancy_name_company', 'company_name']
            ]
        ]);

        array_map(function ($customer) {
            $name = $customer->name;
            if (!empty($customer->fancy_name_company)) {
                $name = $customer->fancy_name_company;
            } else if (!empty($customer->company_name)) {
                $name = $customer->company_name;
            }
            unset($customer->fancy_name_company, $customer->company_name);
            $customer->name = $name;
        }, $customers->data);

        $responseAll = $this->model->getAndFiltersAllItems(array_filter($_GET, function ($get) {
            return $get != 'status_payment';
        }, ARRAY_FILTER_USE_KEY));

        $content_header = (object)[
            'route' => URL . $this->route,
            'title' => 'Contas a Receber',
            'subtitle' => 'Listagem',
            'buttons' => [
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Adicionar Lançamento',
                    'href' => URL . "bill-receive/add",
                ]
            ],
        ];

        $info_boxs = [
            (object)[
                'bg' => 'blue',
                'icon' => 'fas fa-coins',
                'text' => 'Valor Total',
                'number' => Util::maskMoney(array_reduce($responseAll->data, function ($accumulator, $item) {
                    $accumulator += $item->status_payment != 3 && $item->status_payment != 9 ? $item->value_installment : 0;
                    return $accumulator;
                }, 0))
            ],
            (object)[
                'bg' => 'green',
                'icon' => 'fas fa-wallet',
                'text' => 'Valor Pago',
                'number' => Util::maskMoney(array_reduce($responseAll->data, function ($accumulator, $item) {
                    $accumulator += $item->status_payment == 2 ? $item->value_installment : 0;
                    return $accumulator;
                }, 0))
            ],
            (object)[
                'bg' => 'yellow',
                'icon' => 'fas fa-money-bill-wave',
                'text' => 'Valor a Pagar',
                'number' => Util::maskMoney(array_reduce($responseAll->data, function ($accumulator, $item) {
                    $accumulator += $item->status_payment == 1 ? $item->value_installment : 0;
                    return $accumulator;
                }, 0))
            ],
            (object)[
                'bg' => 'red',
                'icon' => 'fas fa-hand-holding-usd',
                'text' => 'Valor Atrasado',
                'number' => Util::maskMoney(array_reduce($responseAll->data, function ($accumulator, $item) {
                    $accumulator += $item->status_payment == 1 && $item->due_date < date("Y-m-d") ? $item->value_installment : 0;
                    return $accumulator;
                }, 0))
            ],
        ];

        $thead = [
            (object)[
                'text' => 'Cód.',
                'class' => 'align-middle text-center',
                'styles' => ['width' => '4,3rem'],
                'link' => 'id',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Nº parcela',
                'class' => 'align-middle text-center',
                'link' => 'fragment',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Vencimento',
                'class' => 'align-middle text-center',
                'link' => 'due_date',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Proprietário',
                'class' => 'align-middle text-center',
                'link' => 'owner',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Custo Centro',
                'class' => 'align-middle text-center',
                'link' => 'cost_center',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Forma Pagam.',
                'class' => 'align-middle text-center',
                'link' => 'form_payment',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Valor',
                'class' => 'align-middle text-center',
                'link' => 'value_installment',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Status',
                'class' => 'align-middle text-center',
                'link' => 'status',
                'field_type' => 'badge'
            ],
            (object)[
                'text' => 'Ações',
                'class' => 'align-middle text-center',
                'link' => 'action',
                'field_type' => 'action'
            ],
        ];

        $rows = 20;
        $page = Pagination::getPage();

        $listing_card = (object)[
            'table' => (new TableDefault($thead, $response->data))->model01(),
            'pagination' => (new Pagination)->pages($response->count, $rows),
            'show' => ShowPage::show($page, $rows, $response->count)
        ];

        $showItems = (new Pagination())->listItemsOnPage($response->count, $listing_card->pagination, $rows);

        require APP . 'view/_templates/header.php';
        view("{$this->dir}/index.php", [
            'route' => $this->route,
            'content_header' => $content_header,
            'customers' => $customers->data,
            'payment_status' => $payment_status,
            'form_payments' => $form_payments,
            'cost_centers' => $cost_centers,
            'info_boxs' => $info_boxs,
            'listing_card' => $listing_card
        ]);
        require APP . 'view/_templates/footer.php';
    }

    public function edit(int $itemId)
    {
        Secure::access_admin(true);

        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/payment.js");

        $item = $this->model->getItemById(
            $itemId,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'payment_status', 'columns' => ['id' => ['payment_status_id'], 'name' => ['payment_status_name']]],
                (object)['table' => 'bill_receive', 'columns' => ['id_customer' => ['bill_receive_id_customer'], 'id_sale' => ['bill_receive_id_sale']]]
            ]
        );

        if (!$item) redirect($this->route . "/index/");

        $bill_receive = (new BillReceive)->getItemById($item->id_bill_receive, [
            (object)['columns' => ['id', 'id_form_of_payment', 'id_branch']],
            (object)['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ]);

        Secure::branch($bill_receive->id_branch, $this->route);

        $name = $bill_receive->customer_name;
        if (!empty($bill_receive->customer_fancy_name_company)) {
            $name = $bill_receive->customer_fancy_name_company;
        } else if (!empty($bill_receive->customer_company_name)) {
            $name = $bill_receive->customer_company_name;
        }
        unset($bill_receive->customer_fancy_name_company, $bill_receive->customer_company_name);
        $bill_receive->customer_name = $name;

        if ($item->status_payment == 1 && $item->due_date < date("Y-m-d")) {
            $item->payment_status_name = "Atrasado";
        }

        $form_payments = (new FormOfPayment)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => 1],
                        'form_payment_accounts_payable' => (object)['comparison' => 'EQUAL', 'value' => 1]
                    ]
                ]
            ],
            [
                (object)['columns' => ['id', 'name']]
            ]
        );
        $accounts = (new BankAccounts())->getAndFilterAllBankAccounts(0, ['status' => 1], 0)->data;
        $banks = (new Banks())->getAndFilterAllBanks(0, ['status' => 1], 0)->data;

        $item->credit = (new Customer)->calculateCustomerCredit($item->bill_receive_id_customer);

        $item->credit = round($item->credit, 2);

        $item->label_payment_transaction = "";
        $item->input_payment_transaction = "";

        if ($item->status_payment == 2) {
            /**Pago */
            if ($item->payment_transaction == 2) {
                /**Desconto */
                $item->label_payment_transaction = "Desconto";
                $item->input_payment_transaction = Util::maskMoney($item->value_installment - $item->amount_paid);
            } else if ($item->payment_transaction == 3) {
                /**Crédito */
                $item->label_payment_transaction = "Crédito";
                $item->input_payment_transaction = Util::maskMoney($item->amount_paid - $item->value_installment);
            } else if ($item->payment_transaction == 4) {
                /**Juros */
                $item->label_payment_transaction = "Juros";
                $item->input_payment_transaction = Util::maskMoney($item->amount_paid - $item->value_installment);
            }
        }

        $standardContracts = (new StandardContract)->getWithFiltersAllItems([(object)['columns' => ['type_contract' => (object)['comparison' => 'EQUAL', 'value' => 4]]]])->data;

        $item->labels = $item->status_payment == 2 ? "" : "*";
        $item->inputs = $item->status_payment == 2 ? "disabled" : "required";

        $content_header = (object)[
            'title' => "#{$item->number_portion} - {$bill_receive->customer_name}",
            'subtitle' => 'Parcela',
            'buttons' => []
        ];

        if (!empty($item->bill_receive_id_sale)) {
            array_push(
                $content_header->buttons,
                (object)[
                    'text' => 'Ver Venda',
                    'bg' => 'info',
                    'size' => 'sm',
                    'attrs' => ["target" => '_blanck'],
                    'href' => URL . "sales/edit-item/" . $item->bill_receive_id_sale
                ]
            );
        } else {
            array_push(
                $content_header->buttons,
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Lançamento',
                    'href' => URL . "bill-receive/entry/{$item->id_bill_receive}",
                ],
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Parcelas',
                    'href' => URL . "bill-receive/installment/{$item->id_bill_receive}",
                ]
            );
        }

        $content_header_filters = [
            (object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id]]],
            (object)['table' => 'bills_to_pay_installments', 'columns' => ['status_payment' => (object)['comparison' => '!=', 'value' => 3]]]
        ];

        if ((new BillsToPay)->getWithFiltersAllItems($content_header_filters)->count > 0) {
            $content_header->buttons[] = (object)['bg' => 'info', 'size' => 'sm', 'text' => 'Contas Pagar', 'href' => URL . "bill-receive/bill-pay/{$item->id_bill_receive}"];
        }

        $nav_tabs = [
            (object)['text' => 'Parcela', 'route' => URL . $this->route . '/edit/' . $itemId, 'class' => 'active'],
            (object)['text' => 'Anexos', 'route' => URL . $this->route . '/attachment/' . $itemId],
            // (object)['text' => 'Saldo cliente ' . Util::maskMoney($item->credit), 'id' => 'customer_balance', 'class' => 'text-bold pull-right', 'a_class' => ($item->credit >= 0 ? 'text-blue' : 'text-red'), 'attr' => ['data-value' => $item->credit]],
        ];

        if ((new BillsToPayInstallment)->getWithFiltersAllItems([(object)['columns' => ['id_bill_receive_installment' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]])->count > 0) {
            $nav_tabs[] = (object)['text' => 'Parcelas contas pagar', 'route' => URL . $this->route . '/bill-pay-installment/' . $itemId];
        }

        require APP . 'view/_templates/header.php';
        view("{$this->dir}/edit.php", [
            'route' => $this->route,
            'content_header' => $content_header,
            'nav_tabs' => $nav_tabs,
            'route_form' => URL . "{$this->route}/handleSubmitEdit/$itemId",
            'item' => $item,
            'bill_receive' => $bill_receive,
            'form_payments' => $form_payments->data,
            'accounts' => $accounts,
            'banks' => $banks,
            'standardContracts' => $standardContracts,
        ]);
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEdit($itemId)
    {
        Secure::check_post_method($this->route . "/edit/$itemId");

        $response = $this->model->submitEditPortion($itemId);
        if (!$response->error) (new PaymentsOfSales)->submitEditPaymentOfSaleFromInstallment($itemId);

        $_SESSION['RR']->toast = (object)[
            'icon' => !$response->error ? 'success' : 'error',
            'title' => $response->message
        ];

        redirect($this->route . "/edit/$itemId");
    }

    public function handleSubmitPayment(int $itemId)
    {
        Secure::check_post_method($this->route . "edit/$itemId");
        $installment = $this->model->getItemById($itemId, [(object)['columns' => ['*']], (object)['table' => 'bill_receive', 'columns' => ['id_customer' => ['id_customer']]]]);

        $arrayPost = array(
            'status_payment' => 2,
            'pay_day' => $_POST['pay_day'],
            'id_form_of_payment' => $_POST['id_form_of_payment'],
            'amount_paid' => Util::unmaskMoney($_POST['amount_paid']),
            'payment_transaction' => $_POST['payment_transaction'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

        if (!empty($_POST['checkId'])) {
            $arrayPost['id_check'] = $_POST['checkId'];
        }

        switch ($_POST['id_form_of_payment']) {
            case '1':
                /**Boleto */
            case '2':
                /**Transferência Bancária */
            case '7':
                /**Cartão de Crédito */
            case '8':
                /**Cartão de Débito */
                $arrayPost['id_account'] = $_POST['id_account'];
                break;
            case '3':
                /**Cheque */
                $arrayPost['id_bank'] = $_POST['id_bank'] ?? '';
                $arrayPost['own_check'] = $_POST['own_check'] ?? '';
                $arrayPost['owner_check'] = $_POST['owner_check'] ?? '';
                $arrayPost['number_check'] = $_POST['number_check'] ?? '';
                $arrayPost['agency'] = Util::removeNonNumericCharacters($_POST['agency']);
                $arrayPost['number_account'] = Util::removeNonNumericCharacters($_POST['number_account']);
                $arrayPost['cpfcnpj_check'] = Util::removeNonNumericCharacters($_POST['cpfcnpj_check'] ?? '');

                if (!empty($_POST['own_check']) && $_POST['own_check'] == 1) {
                    $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id, [(object)['columns' => ['name', 'cnpj']]]);

                    $arrayPost['owner_check'] = $branch->name;
                    $arrayPost['id_account'] = $_POST['id_account'];
                    $arrayPost['cpfcnpj_check'] = Util::removeNonNumericCharacters($branch->cnpj ?? '');
                }
                break;
        }

        $sale = (new Sales)->getWithFiltersAllItems(
            [(object)['columns' => ['id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $installment->id_bill_receive]]]]
        )->data;

        $sale = !empty($sale) ? $sale[0] : false;

        try {
            switch ($_POST['payment_transaction']) {
                case '1':
                    /**Nova parcela */
                    $arrayPost['value_installment'] = Util::unmaskMoney($_POST['amount_paid']);

                    $arrayPostNewPosition = array(
                        'number_portion' => $installment->number_portion,
                        'id_bill_receive' => $installment->id_bill_receive,
                        'id_form_of_payment' => $installment->id_form_of_payment,
                        'due_date' => !empty(trim($installment->due_date)) ? $installment->due_date : NULL,
                        'value_installment' => ($installment->value_installment - Util::unmaskMoney($_POST['amount_paid'])),
                        'status_payment' => 1,
                        'description' => "Nova parcela",
                        'created_by' => $_SESSION['RR']->user->id,
                    );

                    $installment->value_installment = Util::unmaskMoney($_POST['amount_paid']);

                    if ($sale) {
                        $arrayPostNewPosition['percentage_commission_seller'] = $installment->percentage_commission_seller;
                        $arrayPostNewPosition['origin_commission_seller'] = $installment->origin_commission_seller;
                        $arrayPostNewPosition['id_customer_seller'] = $installment->id_customer_seller;
                    }

                    $responseNewPosition = $this->model->insert($arrayPostNewPosition);

                    if ($sale) {
                        foreach ((new SalesChargePaymentAgreement)->getWithFiltersAllItems(
                            [(object)['columns' => ['id_sale' => (object)['comparison' => 'EQUAL', 'value' => $sale->id]]]]
                        )->data as $position) {
                            (new ArrangementPaymentChargesInvoiceReceiveInstallment)->insert([
                                'id_bill_receive_installment' => $responseNewPosition->lastId,
                                'id_customer' => $position->id_customer,
                                'id_user_position' => $position->id_user_position,
                                'origin_commission' => $position->origin_commission,
                                'percentage_commission' => $position->percentage_commission,
                                'created_by' => $_SESSION['RR']->user->id,
                            ]);
                        }
                    }

                    break;
                case '2':
                    /**Desconto */
                    break;
                case '3':
                    /**Credito */
                    break;
                case '4':
                    /**Juros */
                    break;
            }

            $response = $this->model->update($arrayPost, 'id', $itemId);
            if (!$response->error) {
                if (!empty($_POST['checkId'])) {
                    $arrCheck = [
                        'status_check' => 3,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => $_SESSION['RR']->user->id,
                        'id_bill_receive' => $installment->id_bill_receive
                    ];

                    $check = (new CheckControl)->update($arrCheck, 'id', $_POST['checkId']);

                    if (!$check->error) {
                        unset($arrCheck);

                        $arrCheck = [
                            'status' => true,
                            'status_icon' => 10,
                            'id_check' => $_POST['checkId'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'id_installment' => $installment->id,
                            'created_by' => $_SESSION['RR']->user->id,
                            'id_bill_receive' => $installment->id_bill_receive,
                            'comment' => "Cheque usado para pagamento da parcela <a href='" . URL . "bill-receive-installment/edit/{$installment->id}' target='_blank'>Nº{$installment->id}</a> do Contas Receber"
                        ];

                        (new CheckControlTimeline)->insert($arrCheck);
                    }
                } else if ($_POST['id_form_of_payment'] == 3) {
                    $arrCheck = [
                        'status' => true,
                        'status_check' => 1,
                        'id_bank' => $_POST['id_bank'],
                        'agency' => $_POST['agency'],
                        'forwarded_at' => $_POST['pay_day'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'owner_check' => $_POST['owner_check'],
                        'due_date' => $_POST['due_date_check'],
                        'number_check' => $_POST['number_check'],
                        'created_by' => $_SESSION['RR']->user->id,
                        'forwarded_by' => $installment->id_customer,
                        'number_account' => $_POST['number_account'],
                        'id_bill_receive' => $installment->id_bill_receive,
                        'value' => Util::unmaskMoney($_POST['amount_paid']),
                        'cpf_cnpj_check' => Util::removeNonNumericCharacters($_POST['cpfcnpj_check'])
                    ];

                    $check = (new CheckControl)->insert($arrCheck);

                    if (!$check->error) {
                        $this->model->update(['id_check' => $check->lastId], 'id', $itemId);

                        unset($arrCheck);

                        $arrCheck = [
                            'status' => true,
                            'status_icon' => 1,
                            'id_check' => $check->lastId,
                            'created_at' => date('Y-m-d H:i:s'),
                            'id_installment' => $installment->id,
                            'created_by' => $_SESSION['RR']->user->id,
                            'id_bill_receive' => $installment->id_bill_receive,
                            'comment' => "Cheque recebido através da parcela <a href='" . URL . "bill-receive-installment/edit/{$installment->id}' target='_blank'>Nº{$installment->id}</a> do Contas Receber"
                        ];

                        (new CheckControlTimeline)->insert($arrCheck);
                    }
                }

                if ($_POST['id_form_of_payment'] == 3) {
                    $arrPost = [
                        'status' => 1,
                        'due_date' => $_POST['due_date'],
                        'agency' => $arrayPost['agency'],
                        'id_bank' => $arrayPost['id_bank'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'id_installment' => $installment->id,
                        'created_by' => $_SESSION['RR']->user->id,
                        'owner_check' => $arrayPost['owner_check'],
                        'forwarded_by' => $installment->id_customer,
                        'own_check' => $arrayPost['own_check'] ?? 0,
                        'number_check' => $arrayPost['number_check'],
                        'id_account' => $arrayPost['id_account'] ?? '',
                        'number_account' => $arrayPost['number_account'],
                        'id_bill_receive' => $installment->id_bill_receive
                    ];

                    (new CheckControl)->insert($arrPost);
                }

                (new PaymentsOfSales)->submitStatusOfPaymentFromAnInstallment($itemId, 1);
            }

            if ($_POST['id_form_of_payment'] == 9) {
                /** Crédito Fornecedor */
                (new CustomerBalanceLog)->insertLogPay($installment->id_customer, Util::unmaskMoney($_POST['amount_paid']), "Descontado do saldo do cliente para pagar a parcela #{$installment->number_portion} do lançamento #{$installment->id_bill_receive}");
            }

            if ($sale) {
                $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
                $summarySale = (new SummarySale)->getItemWithFilters([
                    (object)['columns' => [
                        'sale_id' => (object)['comparison' => 'EQUAL', 'value' => $sale->id],
                        'installment_number' => (object)['comparison' => 'EQUAL', 'value' => $installment->number_portion]
                    ]]
                ]);

                $summaryInvolved = (new SummaryInvolved)->getWithFiltersAllItems([
                    (object)['columns' => [
                        'sale_id' => (object)['comparison' => 'EQUAL', 'value' => $sale->id],
                        'installment_number' => (object)['comparison' => 'EQUAL', 'value' => $installment->number_portion]
                    ]]
                ])->data;

                /**Vendedor */
                if (!empty($summarySale)) {
                    $userSeller = (new User)->getItemById($sale->created_by);
                    $billPay = (new BillsToPay)->getWithFiltersAllItems(
                        [
                            (object)[
                                'columns' => [
                                    'id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $installment->id_bill_receive],
                                    'id_customer' => (object)['comparison' => 'EQUAL', 'value' => $userSeller->id_customer],
                                ]
                            ]
                        ]
                    )->data;

                    if (empty($billPay)) {
                        $user = (new User)->getItemById($sale->created_by);

                        $billPay = (new BillsToPay)->insert([
                            'id_customer' => $user->id_customer, #vendedor
                            'id_branch' => $_SESSION['RR']->branch->current->id,
                            'id_cost_center' => $branch->id_cost_center_commission_seller,
                            'id_form_of_payment' => $branch->id_form_payment_seller,
                            'competence' => date('Y-m-d', strtotime($sale->sale_date)),
                            'description' => "Conta a pagar gerada pelo arranjo de pagamento da comissão\n" .
                                "Venda: #{$sale->id}\n" .
                                "Vendedor: {$user->name}",
                            'created_by' => $_SESSION['RR']->user->id,
                            'id_bill_receive' => $installment->id_bill_receive,
                        ]);
                        $billPay->id = $billPay->lastId;
                    } else {
                        $billPay = $billPay[0];
                    }

                    $numberPortion = (new BillsToPayInstallment)->getLastNumberPortionByBillsToPayId($billPay->id);
                    $numberPortion = !empty($numberPortion) ? $numberPortion->number_portion : 0;
                    (new BillsToPayInstallment)->insert([
                        'id_bills_to_pay' => $billPay->id,
                        'id_bill_receive_installment' => $itemId,
                        'id_form_of_payment' => $branch->id_form_payment_seller,
                        'value_of_installments' => $summarySale->seller_amount,
                        'due_date' => $summarySale->seller_payment_date ?? date('Y-m-d', strtotime("+{$branch->days_after_seller} days")),
                        'number_portion' => $summarySale->installment_number,
                        'description' => ($numberPortion + 1) . "º Parcela gerada pelo arranjo de pagamento da comissão",
                        'created_by' => $_SESSION['RR']->user->id,
                    ]);
                }

                /**Cargos */
                foreach ($summaryInvolved as $position) {
                    $billPay = (new BillsToPay)->getWithFiltersAllItems(
                        [
                            (object)[
                                'columns' => [
                                    'id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $installment->id_bill_receive],
                                    'id_customer' => (object)['comparison' => 'EQUAL', 'value' => $position->customer_id],
                                ]
                            ]
                        ]
                    )->data;

                    $userPosition = (new UserPosition)->getItemById($position->user_position_id);

                    if (!empty($userPosition)) {
                        $branchUserPosition = (new BranchUserPosition)->getWithFiltersAllItems([
                            (object)[
                                "columns" => [
                                    "id_branch" => (object)["comparison" => "EQUAL", "value" => $_SESSION['RR']->branch->current->id],
                                    "id_user_position" => (object)["comparison" => "EQUAL", "value" => $userPosition->id],
                                ]
                            ]
                        ])->data;
                    }

                    if (empty($billPay)) {
                        $billPay = (new BillsToPay)->insert([
                            'id_customer' => $position->customer_id,
                            'id_branch' => $_SESSION['RR']->branch->current->id,
                            'id_cost_center' => !empty($branchUserPosition) ? $branchUserPosition[0]->id_cost_center : $position->id_cost_center,
                            'id_form_of_payment' => !empty($branchUserPosition) ? $branchUserPosition[0]->id_form_payment : $position->id_form_payment,
                            'competence' => date('Y-m-d', strtotime($sale->sale_date)),
                            'description' => "Conta a pagar gerada pelo arranjo de pagamento da comissão\n" .
                                "Venda: #{$sale->id}\n" .
                                "Cargo: " . (!empty($userPosition) ? $userPosition->name : 'Avulso'),
                            'id_bill_receive' => $installment->id_bill_receive,
                            'created_by' => $_SESSION['RR']->user->id,
                        ]);
                        $billPay->id = $billPay->lastId;
                    } else {
                        $billPay = $billPay[0];
                    }

                    $numberPortion = (new BillsToPayInstallment)->getLastNumberPortionByBillsToPayId($billPay->id);
                    $numberPortion = !empty($numberPortion) ? $numberPortion->number_portion : 0;

                    if ($position->amount > 0) {
                        (new BillsToPayInstallment)->insert([
                            'id_bills_to_pay' => $billPay->id,
                            'id_bill_receive_installment' => $itemId,
                            'id_form_of_payment' => isset($branchUserPosition) && !empty($branchUserPosition) ? $branchUserPosition[0]->id_form_payment : $position->id_form_payment,
                            'value_of_installments' => $position->amount,
                            'due_date' => $position->payment_date ?? date('Y-m-d', strtotime("+" . (isset($branchUserPosition) && !empty($branchUserPosition) ? $branchUserPosition[0]->days_after : $branch->days_after_single) . " days")),
                            'number_portion' => $position->installment_number,
                            'description' => ($numberPortion + 1) . "º Parcela gerada pelo arranjo de pagamento da comissão",
                            'created_by' => $_SESSION['RR']->user->id,
                        ]);
                    }
                }
            }

            if (isset($responseNewPosition)) {
                redirect("{$this->route}/edit/$itemId?newPortion={$responseNewPosition->lastId}");
            }

            header('location:' . URL . $this->route . '/edit/' . $itemId);
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/edit/' . $itemId);
            exit;
        }
    }

    public function handleSubmitReverseInstallment($itemId)
    {
        $installment = $this->model->getItemById($itemId, [(object)['columns' => ['*']], (object)['table' => 'bill_receive', 'columns' => ['id_customer' => ['id_customer']]]]);

        $arrayPost = array(
            'status_payment' => 1,
            'pay_day' => '',
            'amount_paid' => '',
            'payment_transaction' => 0,
            'id_account' => null,
            'number_check' => null,
            'id_bank' => null,
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

        try {
            $this->model->db->beginTransaction();

            (new CustomerBalanceLog)->insertLogReceive($installment->id_customer, $installment->amount_paid, "Valor pago ao estornar a parcela #{$installment->id} do lançamento de código #{$installment->id_bill_receive}");
            $response = $this->model->update($arrayPost, 'id', $itemId);

            $arrayPost['status_payment'] = 3;
            foreach ((new BillsToPayInstallment)->getWithFiltersAllItems([(object)['columns' => [
                'id_bill_receive_installment' => (object)['comparison' => '=', 'value' => $itemId],
                'status_payment' => (object)['comparison' => '!=', 'value' => 3]
            ]]])->data as $key => $value) {
                if ($value->status_payment != 2) {
                    (new BillsToPayInstallment)->update($arrayPost, 'id', $value->id);
                } else if (isset($_POST['payment-transaction']) && !empty($_POST['payment-transaction']) && $_POST['payment-transaction'] == 1) {
                    (new BillsToPayInstallment)->update(['payment_transaction' => 3, 'value_of_installments' => 0], 'id', $value->id);
                }
            }

            $_SESSION['RR']->toast = (object)[
                'icon' => $response->error == true ? 'error' : 'success',
                'title' => $response->message
            ];

            $this->model->db->commit();
            if (!$response->error) (new PaymentsOfSales())->submitStatusOfPaymentFromAnInstallment($itemId, 0);

            header('location:' . URL . "{$this->route}/edit/$itemId");
            exit;
        } catch (PDOException $error) {
            $this->model->db->rollBack();
            header('location:' . URL . "{$this->route}/edit/$itemId");
            exit;
        }
    }

    public function printReceipt($id)
    {
        $installment = (new Contract)->getInstalmentReceivePrintReceipt($id);
        $receipt = (new StandardContract)->getItemById($_POST['id_standard_contract']);
        $contractText = str_replace("breakPage", "<span class='break-page-print-after'></span>", $receipt->text);

        foreach ($installment as $key => &$value) {
            switch ($key) {
                case 'rg_cliente':
                    if (!empty($value)) $value = Util::maskRg($value);
                    break;
                case 'cpf_cliente':
                    if (!empty($value)) $value = Util::maskCpf($value);
                    break;
                case 'celular_cliente':
                    if (!empty($value)) $value = Util::maskTelefone($value);
                    break;
                case 'cep_filial':
                    if (!empty($value)) $value = Util::maskCep($value);
                    break;
                case 'cpf_cliente':
                    if (!empty($value)) $value = Util::maskCpf($value);
                    break;
                case 'cnpj_empresa':
                    if (!empty($value)) $value = Util::maskCnpj($value);
                    break;
                case 'cnpj_filial':
                    if (!empty($value)) $value = Util::maskCpfCnpj($value);
                    break;
                case 'valor_parcela':
                    if (!empty($value)) {
                        $value = Util::maskMoney($value);
                        $installment->valor_extenso = Util::converte($value);
                    }
                    break;
                case 'agencia_pagamento':
                    if (!empty($value)) $value = Util::maskAgency($value);
                    break;
                case 'conta_pagamento':
                    if (!empty($value)) $value = Util::maskAccount($value);
                    break;
                case 'data_pagamento':
                    if (!empty($value)) {
                        $installment->dia = Date::day($value);
                        $installment->mes_extenso = Date::month_full($value);
                        $installment->ano = Date::year($value);
                        $installment->data_extenso = "$installment->dia de $installment->mes_extenso de $installment->ano";
                        $value = Date::date($value);
                    }
                    break;
            }

            $contractText = str_replace("{%" . $key . "%}", $value ?? '', $contractText);
        }

        require APP . 'view/' . $this->dir . '/printReceipt.php';
    }

    public function handleSubmitCancelInstallment($itemId)
    {
        $response = $this->model->submitCancelInstallment($itemId);
        if (!$response->error) (new PaymentsOfSales())->submitStatusOfPaymentFromAnInstallment($itemId, 9);

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error == true ? 'error' : 'success',
            'title' => $response->message
        ];

        redirect("{$this->route}/edit/$itemId");
    }

    public function handleSubmitActivateInstallment($itemId)
    {
        $response = $this->model->submitActivateInstallment($itemId);
        if (!$response->error) (new PaymentsOfSales())->submitStatusOfPaymentFromAnInstallment($itemId, 0);

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error == true ? 'error' : 'success',
            'title' => $response->message
        ];

        redirect("{$this->route}/edit/$itemId");
    }

    public function attachment($itemId)
    {
        $item = $this->model->getItemById(
            $itemId,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'bill_receive', 'columns' => ['id_customer' => ['bill_receive_id_customer']]],
            ]
        );

        $bill_receive = (new BillReceive)->getItemById($item->id_bill_receive, [
            (object)['columns' => ['id', 'id_form_of_payment', 'id_branch']],
            (object)['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ]);

        Secure::branch($bill_receive->id_branch, $this->route);
        $attachments = $this->model->getAttachmentsByItemId($itemId);

        $content_header = (object)[
            'title' => "#{$item->number_portion} - {$bill_receive->customer_name}",
            'subtitle' => 'Anexos',
            'buttons' => [
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Lançamento',
                    'href' => URL . "bill-receive/entry/{$item->id_bill_receive}",
                ],
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Parcelas',
                    'href' => URL . "bill-receive/installment/{$item->id_bill_receive}",
                ],
            ],
        ];

        $content_header_filters = [(object)['columns' => [
            'id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id_bill_receive],
        ]]];

        if ((new BillsToPay)->getWithFiltersAllItems($content_header_filters)->count > 0) {
            $content_header->buttons[] = (object)[
                'bg' => 'info',
                'size' => 'sm',
                'text' => 'Contas Pagar',
                'href' => URL . "bill-receive/bill-pay/{$item->id_bill_receive}",
            ];
        }

        $installmentBillPay = (new BillsToPayInstallment)->getWithFiltersAllItems([
            (object)['columns' => ['status_payment' => (object)['comparison' => 'EQUAL', 'value' => 2]]],
            (object)['table' => 'bills_to_pay', 'columns' => ['id_customer' => (object)['comparison' => 'EQUAL', 'value' => $item->bill_receive_id_customer]]]
        ]);

        $item->credit = (new Customer)->calculateCustomerCredit($item->bill_receive_id_customer);

        $item->credit = round($item->credit, 2);

        $nav_tabs = [
            (object)['text' => 'Parcela', 'route' => URL . $this->route . '/edit/' . $itemId,],
            (object)['text' => 'Anexos', 'route' => URL . $this->route . '/attachment/' . $itemId, 'class' => 'active'],
            // (object)['text' => 'Saldo cliente ' . Util::maskMoney($item->credit), 'class' => 'text-bold pull-right ', 'a_class' => ($item->credit >= 0 ? 'text-blue' : 'text-red')],
        ];
        if ((new BillsToPayInstallment)->getWithFiltersAllItems([(object)['columns' => ['id_bill_receive_installment' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]])->count > 0) {
            $nav_tabs[] = (object)['text' => 'Parcelas contas pagar', 'route' => URL . $this->route . '/bill-pay-installment/' . $itemId];
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/attachment.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddAttachmentItem($itemId)
    {
        Secure::check_post_method($this->route . "/attachment/$itemId");

        try {
            $attachments = FileUploader::uploadFiles($_FILES['attachmentEntry'], array_fill(0, count($_FILES['attachmentEntry']) + 1, "attachments/bill-receive/$itemId"));

            foreach ($attachments as $attachment) {
                $arrayPost = array(
                    "id_bill_receive_installment" => $itemId,
                    "name" => $_POST['name'],
                    "filename" => $attachment['filename'],
                    "extension" => $attachment['extension'],
                    "created_by" => $_SESSION['RR']->user->id,
                );

                (new GerenciaPost())->insert7181($arrayPost, "bill_receive_installment_attachment", null, false);
            }

            header('location:' . URL . "{$this->route}/attachment/$itemId");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . "{$this->route}/attachment/$itemId");
            exit;
        }
    }

    public function handleSubmitDeleteAttachmentById($attachmentId)
    {
        $attachment = (new ModelGenerico())->getItemById8161($attachmentId, "bill_receive_installment_attachment");
        Secure::check_post_method($this->route . "/attachment/$attachment->id_bill_receive_installment");

        if (!empty($_FILES)) {
            redirect($this->route . "/attachment/$attachment->id_bill_receive_installment");
        }

        try {
            DeleteFile::deleteFile(
                [$attachmentId],
                "bill_receive_installment_attachment",
                ["attachments/bill-receive/$attachment->id_bill_receive_installment/$attachment->filename.$attachment->extension"]
            );

            header('location:' . URL . $this->route . '/attachment/' . $attachment->id_bill_receive_installment . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/attachment/' . $attachment->id_bill_receive_installment . "?edited=false");
            exit;
        }
    }

    public function billPayInstallment($itemId)
    {
        $item = $this->model->getItemById(
            $itemId,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'bill_receive', 'columns' => ['id_customer' => ['bill_receive_id_customer']]]
            ]
        );

        $bill_receive = (new BillReceive)->getItemById($item->id_bill_receive, [
            (object)['columns' => ['id', 'id_form_of_payment', 'id_branch', 'id_sale']],
            (object)['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ]);

        Secure::branch($bill_receive->id_branch, $this->route);

        $content_header = (object)[
            'title' => "#{$item->number_portion} - {$bill_receive->customer_name}",
            'subtitle' => 'Parcelas contas pagar',
            'buttons' => []
        ];

        if (!empty($bill_receive->id_sale)) {
            array_push(
                $content_header->buttons,
                (object)[
                    'text' => 'Ver Venda',
                    'bg' => 'info',
                    'size' => 'sm',
                    'attrs' => ["target" => '_blanck'],
                    'href' => URL . "sales/edit-item/" . $bill_receive->id_sale
                ]
            );
        } else {
            array_push(
                $content_header->buttons,
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Lançamento',
                    'href' => URL . "bill-receive/entry/{$item->id_bill_receive}",
                ],
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Parcelas',
                    'href' => URL . "bill-receive/installment/{$item->id_bill_receive}",
                ]
            );

            $content_header_filters = [(object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id_bill_receive]]]];

            if ((new BillsToPay)->getWithFiltersAllItems($content_header_filters)->count > 0) {
                $content_header->buttons[] = (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Contas Pagar',
                    'href' => URL . "bill-receive/bill-pay/{$item->id_bill_receive}",
                ];
            }
        }

        $item->credit = (new Customer)->calculateCustomerCredit($item->bill_receive_id_customer);

        $item->credit = round($item->credit, 2);

        $nav_tabs = [
            (object)['text' => 'Parcela', 'route' => URL . $this->route . '/edit/' . $itemId,],
            (object)['text' => 'Anexos', 'route' => URL . $this->route . '/attachment/' . $itemId],
            // (object)['text' => 'Saldo cliente ' . Util::maskMoney($item->credit), 'class' => 'text-bold pull-right ', 'a_class' => ($item->credit >= 0 ? 'text-blue' : 'text-red')],
        ];
        if ((new BillsToPayInstallment)->getWithFiltersAllItems([(object)['columns' => [
            'id_bill_receive_installment' => (object)['comparison' => 'EQUAL', 'value' => $itemId],
        ]]])->count > 0) {
            $nav_tabs[] = (object)['text' => 'Parcelas contas pagar', 'route' => URL . $this->route . '/bill-pay-installment/' . $itemId, 'class' => 'active'];
        }

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true,
            ],
            'thead' => [
                (object)[
                    'style' => 'width: 60px',
                    'class' => 'text-center',
                    'text' => 'Cód',
                    'column' => (object)['type' => 'text', 'link' => 'id'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data Vencimento',
                    'column' => (object)['type' => 'text', 'link' => 'fragment'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data Vencimento',
                    'column' => (object)['type' => 'text', 'link' => 'due_date'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Fornecedor',
                    'column' => (object)['type' => 'text', 'link' => 'name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Custo Centro',
                    'column' => (object)['type' => 'text', 'link' => 'cost_center_name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Forma Pagamento',
                    'column' => (object)['type' => 'text', 'link' => 'form_of_payment_name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Valor',
                    'column' => (object)['type' => 'text', 'link' => 'value_installment'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Status pagamento',
                    'column' => (object)['type' => 'label', 'link' => 'status'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action'],
                ],
            ],
            'data' => array_map(function ($installment) {
                $status_bg = '';
                $status_text = '';

                switch ($installment->status_payment) {
                    case '1':
                        if ($installment->due_date < date("Y-m-d")) {
                            $status_bg = 'danger';
                            $status_text = 'Atrasado';
                            $installment->color = 'text-red';
                        } else {
                            if ($installment->due_date == date("Y-m-d")) {
                                $installment->color = 'text-yellow';
                            }
                            $status_bg = 'warning';
                            $status_text = 'Aguard. Pagam.';
                        }
                        break;
                    case '2':
                        $status_bg = 'success';
                        $status_text = 'Pago';
                        break;
                    case '3':
                        $status_bg = 'default';
                        $status_text = 'Cancelado';
                        break;
                }

                $installment->name = '';
                if (!empty($installment->customer_fancy_name_company)) {
                    $installment->name = $installment->customer_fancy_name_company;
                } else if (!empty($installment->customer_company_name)) {
                    $installment->name = $installment->customer_company_name;
                } else if (!empty($installment->customer_name)) {
                    $installment->name = $installment->customer_name;
                }

                $installment->status = (object)['color' => $status_bg, 'value' => $status_text];

                $installment->due_date = Date::date($installment->due_date);
                $installment->fragment = $installment->number_portion . '/' . (new BillsToPayInstallment)->getWithFiltersAllItems([(object)['columns' => [
                    'id_bills_to_pay' => (object)['comparison' => '=', 'value' => $installment->id_bills_to_pay],
                    'status_payment' => (object)['comparison' => '!=', 'value' => 3]
                ]]])->count;
                $installment->value_installment = $installment->status_payment == 2 ? Util::maskMoney($installment->amount_paid) : Util::maskMoney($installment->value_of_installments);

                $installment->action = [
                    (object)[
                        'id' => $installment->id,
                        'title' => 'Editar',
                        'href' => URL . "bills-to-pay-installment/edit-item/$installment->id",
                        'icon' => 'fas fa-pencil-alt',
                        'size' => 'sm',
                        'color' => 'primary',
                        'attr' => ['target' => '_blank']
                    ],
                ];

                return $installment;
            }, (new BillsToPayInstallment)->getWithFiltersAllItems(
                [
                    (object)['columns' => ['id_bill_receive_installment' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]],
                ],
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                    (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                    (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                ],
                ['group' => 'this->table.id', 'order' => 'this->table.status_payment, this->table.due_date ASC']
            )->data)
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/bill-pay-installment.php';
        require APP . 'view/_templates/footer.php';
    }
}
