<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\libs\BoxAlert;
use RR\libs\Date;
use RR\libs\DeleteFile;
use RR\libs\FileUploader;
use RR\libs\RecursiveCostCenter;
use RR\libs\Secure;
use RR\model\BankAccounts;
use RR\model\Banks;
use RR\model\Customer;
use RR\model\FormOfPayment;
use RR\model\BillsToPayInstallment;
use PDOException;
use RR\libs\Pagination;
use RR\model\BillReceive;
use RR\model\BillReceiveInstallment;
use RR\model\BillsToPay;
use RR\model\CheckControl;
use RR\model\CheckControlTimeline;
use RR\model\Contract;
use RR\model\CostCenter;
use RR\model\LinkedCheckControl;
use RR\model\StandardContract;

use function RR\Controller\redirect;

class BillsToPayInstallmentController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'bills-to-pay-installment';
        $this->dir = 'bills-to-pay-installment';
        $this->model = new BillsToPayInstallment();
        $this->table = 'bills_to_pay_installments';
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
        $this->title = "Contas a Pagar";
    }

    public function index()
    {
        Secure::access_admin(true);

        $this->addScript(URL . "js/" . JSVERSION . "/bills-to-pay-installment/installments.js");

        $modelGenerico = new ModelGenerico();
        $billsToPayModel = new BillsToPayInstallment();

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['status_payment'])) {
            $_GET['status_payment'] = [1];
        }

        if (!isset($_GET['date']['start'])) {
            $_GET['date']['start'] = Date::year_month(date("Y-m-d")) . '-01';
        }

        if (!isset($_GET['date']['end'])) {
            $_GET['date']['end'] = date("Y-m-t");
        }

        $filtersBillPay = [
            (object)[
                'table' => 'bills_to_pay',
                'columns' => [
                    'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id],
                ]
            ],
            (object)[
                'columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']],
                ]
            ]
        ];

        if (!empty($_GET['id_customer'])) {
            array_push($filtersBillPay, (object)['table' => 'bills_to_pay', 'columns' => ['id_customer' => (object)['comparison' => '=', 'value' => $_GET['id_customer']]]]);
        }

        if (!empty($_GET['status_payment'])) {
            array_push($filtersBillPay, (object)['columns' => ['status_payment' => (object)['comparison' => '=', 'value' => $_GET['status_payment']]]]);
        }

        if (!empty($_GET['id_form_of_payment'])) {
            array_push($filtersBillPay, (object)['columns' => ['id_form_of_payment' => (object)['comparison' => 'EQUAL', 'value' => $_GET['id_form_of_payment']]]]);
        }

        if (isset($_GET['cod']) && !empty($_GET['cod'])) {
            array_push($filtersBillPay, (object)['columns' => ['id' => (object)['comparison' => '=', 'value' => $_GET['cod']]]]);
        }

        if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
            array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime("{$_GET['date']['start']} -1 day")), 'value2' => date('Y-m-d', strtotime($_GET['date']['end'] . "+1 day"))]]]);
        } else {
            if (!empty($_GET['date']['start'])) {
                array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime("{$_GET['date']['start']}"))]]]);
            }

            if (!empty($_GET['date']['end'])) {
                array_push($filtersBillPay, (object)['columns' => ['due_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
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

        $colunmsBillPay = [
            (object)['columns' => ['*']],
            (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]],
            (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
        ];
        $limit = 20;
        $optionsBillPay = [
            'orderBy' => " this->table.status_payment, this->table.due_date ASC",
            'page' => $_GET['page'] ?? 1,
            'limit' => $limit,
        ];

        $response = $this->model->getWithFiltersAllItems($filtersBillPay, $colunmsBillPay, $optionsBillPay);

        $rows = $limit;
        $page = Pagination::getPage();

        $pagination = (new Pagination())->pages($response->count, $rows);

        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function ($element) {
            $element->totalLaunchInstallments = (new BillsToPay)->getAmountOfInstallmentsOfRelease($element->id_bills_to_pay);
        }, $response->data);

        array_map(function ($item) {
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

            $item->customer_name = $item->customer_fancy_name_company ?? $item->customer_company_name ?? $item->customer_name;

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

        $paymentStatus = $modelGenerico->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $customers = (new Customer())->getAndFilterAllCustomer(0, ['status' => 1, 'id_customer_type' => 10, "id_branch" => $_SESSION['RR']->branch->current->id], 0);
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;

        array_map(function ($element) {
            $element->name = $element->fancy_name_company ?? $element->company_name ?? $element->name;
        }, $customers);

        $responseCards = $this->model->getItemsForCards();
        $cards = [
            'total' => 0,
            'paid' => 0,
            'open' => 0,
            'late' => 0,
        ];

        foreach ($responseCards->data as $item) {
            if ($item->status_payment != 3) {
                $cards['total'] += $item->status_payment == 2 ? $item->amount_paid : $item->value_of_installments;
                if ($item->status_payment == 2) {
                    $cards['paid'] += $item->amount_paid;
                } else if ($item->status_payment == 1) {
                    $cards['open'] += $item->value_of_installments;
                    if ($item->due_date < date("Y-m-d")) {
                        $cards['late'] += $item->value_of_installments;
                    }
                }
            }
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddPortion($entryId)
    {
        Secure::check_post_method($this->route . "/installments/$entryId?error=error");

        $lastPortion = (new BillsToPayInstallment())->getLastNumberPortionByBillsToPayId($entryId);

        $arrPost = [
            'number_portion' => ++$lastPortion->number_portion,
            'id_bills_to_pay' => $entryId,
            'id_form_of_payment' => $_POST['id_form_of_payment'],
            'due_date' => !empty(trim($_POST['due_date'])) ? $_POST['due_date'] : NULL,
            'value_of_installments' => Util::unmaskMoney($_POST['value_of_installments']),
            'description' => $_POST['description'],
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            (new GerenciaPost())->insert7181($arrPost, $this->table, false, false);

            header('location:' . URL . $this->route . "/installments/" . $entryId . "?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/installments/" . $entryId . "?added=false");
            exit;
        }
    }

    public function handleCancelInstallments()
    {
        Secure::check_post_method($this->route);

        $_POST['id_installments'] = explode(',', $_POST['id_installments']);

        $response = (new BillsToPay)->cancelAndUpdateInstallments($_POST['id_installments']);

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error != false ? 'success' : 'error',
            'title' => $response->message,
        ];

        redirect($this->route);
    }

    public function editItem($itemId)
    {
        Secure::access_admin(true);

        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/payment.js");

        $billsToPayModel = new BillsToPayInstallment();
        $modelGenerico = new ModelGenerico();

        $item = $billsToPayModel->getItemById8161($itemId);

        if ($item->id_form_of_payment_entry === 3) {
            $checks = (new LinkedCheckControl)->getWithFiltersAllItems(
                [
                    (object)['columns' => ['status' => ['value' => 1]]],
                    (object)['columns' => ['id_installment' => ['value' => $item->id]]],
                    (object)['columns' => ['id_bills_to_pay' => ['value' => $item->id_bills_to_pay]]]
                ],
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'banks', 'columns' => ['name']],
                    (object)['table' => 'check_control', 'columns' => ['number_check', 'owner_check', 'cpf_cnpj_check', 'value', 'due_date']]
                ]
            );
        }

        Secure::branch($item->id_branch, $this->route);
        $formOfPaymentModel = new FormOfPayment();

        $content_header = (object)[
            'title' => "#{$item->number_portion} - {$item->customer_name}",
            'subtitle' => 'Parcela',
            'buttons' => []
        ];

        if (!empty($item->id_bill_receive_installment)) {
            $billReceive = (new BillReceiveInstallment)->getItemById(
                $item->id_bill_receive_installment,
                [
                    (object)['columns' => ['id']],
                    (object)['table' => 'bill_receive', 'columns' => ['id_sale' => ['bill_receive_id_sale']]]
                ]
            );

            array_push(
                $content_header->buttons,
                (object)[
                    'text' => 'Ver Venda',
                    'bg' => 'info',
                    'size' => 'sm',
                    'attrs' => ["target" => '_blanck'],
                    'href' => URL . "sales/edit-item/" . $billReceive->bill_receive_id_sale
                ]
            );
        } else {
            array_push(
                $content_header->buttons,
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Lançamento',
                    'href' => URL . "bills-to-pay/entry/{$item->id_bills_to_pay}",
                ],
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Parcelas',
                    'href' => URL . "bills-to-pay/installments/{$item->id_bills_to_pay}",
                ]
            );
        }

        $standardContracts = (new StandardContract)->getWithFiltersAllItems([(object)['columns' => ['type_contract' => (object)['comparison' => 'EQUAL', 'value' => 4]]]])->data;

        $formOfPaymentsEntry = $formOfPaymentModel->getAndFilterAllItem(['status' => true], 0)->data;
        $formOfPayments = $formOfPaymentModel->getAndFilterAllItem(['status' => true, "form_payment_accounts_payable" => 1], 0)->data;
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $paymentStatus = $modelGenerico->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $accounts = (new BankAccounts())->getAndFilterAllBankAccounts(0, ['status' => 1], 0)->data;
        $banks = (new Banks())->getAndFilterAllBanks(0, ['status' => 1], 0)->data;

        /**Parcelas do cliente fornecedor */
        $installments = $billsToPayModel->getAndFilterAllItem(["status" => 1, "id_customer" => $item->id_customer]);

        $item->customer_name = $item->customer_fancy_name_company ?? $item->customer_name;
        $item->credit = round((new Customer)->calculateCustomerCredit($item->id_customer), 2);

        $nav_tabs = [
            (object)['text' => 'Parcela', 'route' => URL . $this->route . '/editItem/' . $itemId, 'class' => 'active'],
            (object)['text' => 'Anexos', 'route' => URL . $this->route . '/attachment/' . $itemId],
            // (object)['text' => 'Saldo cliente ' . Util::maskMoney($item->credit), 'class' => 'text-bold pull-right ', 'a_class' => ($item->credit >= 0 ? 'text-blue' : 'text-red')],
        ];

        $item->label_payment_transaction = "";
        $item->input_payment_transaction = "";

        if ($item->status_payment == 2) {
            /**Pago */
            if ($item->payment_transaction == 2) {
                /**Desconto */
                $item->label_payment_transaction = "Desconto";
                $item->input_payment_transaction = Util::maskMoney($item->value_of_installments - $item->amount_paid);
            } else if ($item->payment_transaction == 3) {
                /**Crédito */
                $item->label_payment_transaction = "Crédito";
                $item->input_payment_transaction = Util::maskMoney($item->amount_paid - $item->value_of_installments);
            } else if ($item->payment_transaction == 4) {
                /**Juros */
                $item->label_payment_transaction = "Juros";
                $item->input_payment_transaction = Util::maskMoney($item->amount_paid - $item->value_of_installments);
            }
        }

        $item->labels = $item->status_payment == 2 ? "" : "*";
        $item->inputs = $item->status_payment == 2 ? "disabled" : "required";

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId?error=error");

        $item = (new BillsToPayInstallment())->getItemById8161($itemId);

        $arrayPost = array(
            'value_of_installments' => isset($_POST['value_of_installments']) ? Util::unmaskMoney($_POST['value_of_installments']) : $item->value_of_installments,
            'due_date' => isset($_POST['due_date']) ? $_POST['due_date'] : $item->due_date,
            'description' => $_POST['description'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

        try {
            (new GerenciaPost())->update8191($arrayPost, $this->table, 'id', $itemId, false);

            header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=false");
            exit;
        }
    }

    public function handleSubmitPayment($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId?error=error");

        $portion = (new BillsToPayInstallment())->getItemById8161($itemId);

        $arrayPost = array(
            'status_payment' => 2,
            'pay_day' => $_POST['pay_day'],
            'id_form_of_payment' => $_POST['id_form_of_payment'],
            'amount_paid' => Util::unmaskMoney($_POST['amount_paid']),
            'payment_transaction' => $_POST['payment_transaction'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

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

                $arrayPost['own_check'] = $_POST['own_check'];
                if ($_POST['own_check'] == 1) {
                    $arrayPost['id_account'] = $_POST['id_account'];
                } else {
                    if (!empty($_POST['checkId'])) {
                        $arrayPost['id_check'] = $_POST['checkId'];
                    }

                    $arrayPost['id_bank'] = $_POST['id_bank'];
                    $arrayPost['owner_check'] = $_POST['owner_check'];
                    $arrayPost['cpfcnpj_check'] = Util::removeNonNumericCharacters($_POST['cpfcnpj_check']);
                    $arrayPost['agency'] = Util::removeNonNumericCharacters($_POST['agency']);
                    $arrayPost['number_account'] = Util::removeNonNumericCharacters($_POST['number_account']);
                }

                $arrayPost['number_check'] = $_POST['number_check'];

                break;
        }

        switch ($_POST['payment_transaction']) {
            case '1':
                /**Nova parcela */
                $arrayPost['value_of_installments'] = Util::unmaskMoney($_POST['amount_paid']);

                $arrayPostNewPortion = array(
                    'number_portion' => $portion->number_portion,
                    'id_bills_to_pay' => $portion->id_bills_to_pay,
                    'id_form_of_payment' => $portion->id_form_of_payment,
                    'due_date' => !empty(trim($portion->due_date)) ? $portion->due_date : NULL,
                    'value_of_installments' => ($portion->value_of_installments - Util::unmaskMoney($_POST['amount_paid'])),
                    'status_payment' => 1,
                    'description' => "Nova parcela",
                    'created_by' => $_SESSION['RR']->user->id,
                );

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

        try {
            $response = $this->model->update($arrayPost, 'id', $itemId);

            if (!$response->error && !empty($_POST['checks'])) {
                foreach ($_POST['checks'] as $postCheck) {
                    if (is_numeric($postCheck['id'])) {
                        $arrCheck = [
                            'status_check' => 3,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => $_SESSION['RR']->user->id
                        ];

                        $check = (new CheckControl)->update($arrCheck, 'id', $postCheck['id']);

                        if (!$check->error) {
                            unset($arrCheck);

                            $arrCheck = [
                                'status' => true,
                                'status_icon' => 12,
                                'id_check' => $postCheck['id'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'id_installment' => $portion->id,
                                'created_by' => $_SESSION['RR']->user->id,
                                'id_bills_to_pay' => $portion->id_bills_to_pay,
                                'comment' => "Cheque usado para pagamento da parcela <a href='" . URL . "bills-to-pay-installment/editItem/{$portion->id}' target='_blank'>Nº{$portion->id}</a> do Contas Pagar"
                            ];

                            (new CheckControlTimeline)->insert($arrCheck);
                        }
                    } else {
                        unset($arrPost);

                        $arrPost = [
                            'status' => true,
                            'status_check' => 1,
                            'value' => $postCheck['value'],
                            'forwarded_at' => date('Y-m-d'),
                            'agency' => $postCheck['agency'],
                            'id_bank' => $postCheck['id_bank'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'due_date' => $postCheck['due_date'],
                            'forwarded_by' => $portion->id_customer,
                            'created_by' => $_SESSION['RR']->user->id,
                            'owner_check' => $postCheck['owner_check'],
                            'number_check' => $postCheck['number_check'],
                            'id_bills_to_pay' => $portion->id_bills_to_pay,
                            'number_account' => $postCheck['number_account'],
                            'cpf_cnpj_check' => Util::removeNonNumericCharacters($postCheck['cpf_cnpj_check'])
                        ];

                        $check = (new CheckControl)->insert($arrPost);

                        if (!$check->error) {
                            unset($arrCheck);

                            $arrCheck = [
                                'status' => true,
                                'status_icon' => 1,
                                'id_check' => $check->lastId,
                                'created_at' => date('Y-m-d H:i:s'),
                                'id_installment' => $portion->id,
                                'created_by' => $_SESSION['RR']->user->id,
                                'id_bills_to_pay' => $portion->id_bills_to_pay,
                                'comment' => "Cheque recebido através da parcela <a href='" . URL . "bills-to-pay-installment/editItem/{$portion->id}' target='_blank'>Nº{$portion->id}</a> do Contas Pagar"
                            ];

                            (new CheckControlTimeline)->insert($arrCheck);
                        }
                    }

                    if (!$check->error) {
                        unset($arrPost);

                        $arrPost = [
                            'status' => true,
                            'id_installment' => $portion->id,
                            'id_bills_to_pay' => $portion->id_bills_to_pay,
                            'id_check' => $check->lastId ?? $postCheck['id']
                        ];

                        $linkedCheck = (new LinkedCheckControl)->getItemWithFilters([(object)['columns' => ['id_check' => ['value' => $postCheck['id']]]]]);

                        if (!empty($linkedCheck)) {
                            (new LinkedCheckControl)->update(['status' => 1], 'id', $linkedCheck->id);
                        } else {
                            (new LinkedCheckControl)->insert($arrPost);
                        }
                    }
                }
            }

            $_SESSION['RR']->toast = (object)[
                'icon' => ($response->error === true ? 'error' : 'success'),
                'title' => $response->message,
            ];

            if (isset($arrayPostNewPortion)) {
                $newPortionId = (new GerenciaPost())->insert7181($arrayPostNewPortion, $this->table, true, false);

                header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=true&newPortion=" . $newPortionId);
                exit;
            }

            header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=false");
            exit;
        }
    }

    public function handleSubmitReverseInstallment($itemId)
    {
        $item = (new BillsToPayInstallment)->getItemById($itemId);

        $arrPost = array(
            'pay_day' => '',
            'id_bank' => null,
            'amount_paid' => '',
            'id_account' => null,
            'status_payment' => 1,
            'number_check' => null,
            'payment_transaction' => 0,
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        );

        try {
            $response = $this->model->update($arrPost, 'id', $itemId);

            if (!$response->error && $item->id_form_of_payment == 3) {
                $checks = (new LinkedCheckControl)->getWithFiltersAllItems(
                    [
                        (object)['columns' => ['status' => ['value' => 1]]],
                        (object)['columns' => ['id_installment' => ['value' => $item->id]]],
                        (object)['columns' => ['id_bills_to_pay' => ['value' => $item->id_bills_to_pay]]]
                    ]
                );

                if ($checks->count > 0) {
                    foreach ($checks->data as $ch) {
                        $linkedCheck = (new LinkedCheckControl)->update(['status' => 0], 'id', $ch->id);

                        if (!$linkedCheck->error) {
                            $check = (new CheckControl)->getItemById($ch->id_check);

                            if (!empty($check) && $check->status_check == 3) {
                                unset($arrPost);

                                $arrPost = [
                                    'status_check' => 1,
                                    'id_bills_to_pay' => null,
                                ];

                                $checkUpdate = (new CheckControl)->update($arrPost, 'id', $ch->id_check);

                                if (!$checkUpdate->error) {
                                    $arrCheck = [
                                        'status' => true,
                                        'status_icon' => 10,
                                        'id_check' => $ch->id_check,
                                        'id_installment' => $item->id,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_by' => $_SESSION['RR']->user->id,
                                        'id_bills_to_pay' => $item->id_bills_to_pay,
                                        'comment' => "Status do cheque alterado referente ao estorno da parcela <a href='" . URL . "bills-to-pay-installment/editItem/{$item->id}' target='_blank'>Nº{$item->id}</a> do Contas Pagar."
                                    ];

                                    (new CheckControlTimeline)->insert($arrCheck);
                                }
                            }
                        }
                    }
                }
            }

            $_SESSION['RR']->toast = (object)[
                'icon' => ($response->error ? 'error' : 'success'),
                'title' => $response->error ? 'Não foi possível estornar a parcela.' : 'Parcela estornada com sucesso.',
            ];

            redirect($this->route . "/editItem/$itemId");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=false");
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
        $arrayPost = array(
            'status_payment' => 3,
            'pay_day' => '',
            'amount_paid' => '',
            'payment_transaction' => 0,
            'id_account' => null,
            'number_check' => null,
            'id_bank' => null,
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

        $response = $this->model->ActivateAndDesactivateInstallmentForm($itemId, $arrayPost);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . '/editItem/' . $itemId);
    }

    public function handleSubmitActivateInstallment($itemId)
    {
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

        $response = $this->model->ActivateAndDesactivateInstallmentForm($itemId, $arrayPost);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . '/editItem/' . $itemId);
    }

    public function attachment($itemId)
    {
        $billsToPayModel = new BillsToPayInstallment();

        $item = $billsToPayModel->getItemById8161($itemId);
        Secure::branch($item->id_branch, $this->route);

        $content_header = (object)[
            'title' => "#{$item->number_portion} - {$item->customer_name}",
            'subtitle' => 'Anexos',
            'buttons' => [
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Lançamento',
                    'href' => URL . "bills-to-pay/entry/{$item->id_bills_to_pay}",
                ],
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Parcelas',
                    'href' => URL . "bills-to-pay/installments/{$item->id_bills_to_pay}",
                ],
            ],
        ];

        $attachments = $billsToPayModel->getAttachmentsByItemId($itemId);
        $installments = $billsToPayModel->getAndFilterAllItem(["status" => 1, "id_customer" => $item->id_customer]);

        $item->credit = (new Customer)->calculateCustomerCredit($item->id_customer);

        $item->credit = round($item->credit, 2);

        $nav_tabs = [
            (object)['text' => 'Parcela', 'route' => URL . $this->route . '/editItem/' . $itemId],
            (object)['text' => 'Anexos', 'route' => URL . $this->route . '/attachment/' . $itemId, 'class' => 'active'],
            // (object)['text' => 'Saldo cliente ' . Util::maskMoney($item->credit), 'class' => 'text-bold pull-right ', 'a_class' => ($item->credit >= 0 ? 'text-blue' : 'text-red')],
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/attachment.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddAttachmentItem($itemId)
    {
        Secure::check_post_method($this->route . "/attachment/$itemId?error=error");

        if (empty($_FILES)) {
            redirect($this->route . "/attachment/$itemId?error=error");
        }

        try {
            $attachments = FileUploader::uploadFiles($_FILES['attachmentEntry'], array_fill(0, count($_FILES['attachmentEntry']) + 1, "attachments/billsToPay/$itemId"));

            foreach ($attachments as $attachment) {
                $arrayPost = array(
                    "id_bills_to_pay_installments" => $itemId,
                    "name" => $_POST['name'],
                    "filename" => $attachment['filename'],
                    "extension" => $attachment['extension'],
                    "created_by" => $_SESSION['RR']->user->id,
                );

                (new GerenciaPost())->insert7181($arrayPost, "bills_to_pay_installments_attachment", null, false);
            }

            header('location:' . URL . $this->route . '/attachment/' . $itemId . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/attachment/' . $itemId . "?edited=false");
            exit;
        }
    }

    public function handleSubmitDeleteAttachmentById($attachmentId)
    {
        $attachment = (new ModelGenerico())->getItemById8161($attachmentId, "bills_to_pay_installments_attachment");
        Secure::check_post_method($this->route . "/attachment/$attachment->id_bills_to_pay_installments?error=error");

        try {
            DeleteFile::deleteFile(
                [$attachmentId],
                "bills_to_pay_installments_attachment",
                ["attachments/billsToPay/$attachment->id_bills_to_pay_installments/$attachment->filename.$attachment->extension"]
            );

            header('location:' . URL . $this->route . '/attachment/' . $attachment->id_bills_to_pay_installments . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/attachment/' . $attachment->id_bills_to_pay_installments . "?edited=false");
            exit;
        }
    }
}
