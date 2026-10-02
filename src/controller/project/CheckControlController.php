<?php

namespace RR\controller\project;

use RR\libs\Date;
use RR\libs\Util;
use RR\model\User;
use RR\libs\Secure;
use PDOException;
use RR\libs\Toast;
use RR\model\Banks;
use RR\model\Customer;
use RR\libs\Pagination;
use RR\libs\RecursiveCostCenter;
use RR\model\StatusCheck;
use RR\model\BankAccounts;
use RR\model\BillReceiveInstallment;
use RR\model\CheckControl;
use RR\model\CheckControlTimeline;
use RR\model\FormOfPayment;

use function RR\Controller\redirect;

class CheckControlController extends FrontController
{
    public $dir;
    public $route;

    private $model;
    private $table;

    public function __construct()
    {
        $this->dir = 'check-control';
        $this->route = 'check-control';
        $this->table = 'check_control';
        $this->model = new CheckControl();

        parent::__construct($this->route);
    }

    public function index()
    {
        Secure::access_admin(true);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Cheques',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                ]
            ],
        ];

        $statusCheck = (new StatusCheck)->getWithFiltersAllItems();

        $rows = 20;
        $page = Pagination::getPage();

        if (!isset($_GET['statusCheck'])) {
            $_GET['statusCheck'] = 1;
        }

        $filters = [(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['statusCheck']]]]];

        if (!empty($_GET['name'])) {
            $busca = '%' . $_GET['name'] . '%';

            // Titular do cheque ou cliente que repassou (coluna "Repassado Por")
            array_push($filters, (object)[
                'where' => " AND (
                    ucase(this->table.owner_check) LIKE ucase(:busca_1)
                    OR this->table.forwarded_by IN (
                        SELECT customer.id FROM customer
                        WHERE ucase(customer.name) LIKE ucase(:busca_2)
                        OR ucase(customer.fancy_name_company) LIKE ucase(:busca_3)
                        OR ucase(customer.company_name) LIKE ucase(:busca_4)
                    )
                )",
                'parameters' => [
                    ':busca_1' => $busca,
                    ':busca_2' => $busca,
                    ':busca_3' => $busca,
                    ':busca_4' => $busca
                ]
            ]);
        }

        $response = $this->model->getWithFiltersAllItems(
            $filters,
            [],
            [
                'limit' => $rows,
                'page' => $page
            ]
        );

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => 'fas fa-pencil-alt',
                'href' => URL . "{$this->route}/editItem/{$item->id}",
                'title' => 'Editar',
                'size' => 'sm',
                'color' => 'primary',
            ], (object)[
                'id' => $item->id,
                'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                'title' => ($item->status ? 'Inativar' : 'Ativar'),
                'size' => 'sm',
                'color' => ($item->status ? 'danger' : 'success'),
                'class' => ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                'attr' => [
                    'sendTo' => $this->route . ($item->status ? '/disableItem/' : '/enableItem/')
                ]
            ]);

            switch ($item->status_check) {
                case '1':
                    $item->status_check = (object)[
                        'value' => 'Aberto',
                        'color' => 'warning'
                    ];

                    $item->due_date >= date("Y-m-d") ? $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'success'] : $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'warning'];
                    break;
                case '2':
                    $item->status_check = (object)[
                        'value' => 'Compensado',
                        'color' => 'success'
                    ];

                    $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'default'];
                    break;
                case '3':
                    $item->status_check = (object)[
                        'value' => 'Repassado',
                        'color' => 'default'
                    ];

                    $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'default'];
                    break;
                case '4':
                    $item->status_check = (object)[
                        'value' => 'S/ Fundo',
                        'color' => 'danger'
                    ];

                    $item->due_date = (object)['value' => Date::date($item->due_date), 'color' => 'default'];
                    break;
            };

            if (!empty($item->forwarded_by)) {
                $customer = (new Customer)->getItemById($item->forwarded_by);

                $item->forwarded_by = $customer->fancy_name_company ?? ($customer->company_name ?? $customer->name);
            }
        }, $response->data);

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true,
            ],
            'thead' => [
                (object)[
                    'style' => 'width: 80px',
                    'class' => 'text-center',
                    'text' => 'Nº Cheque',
                    'column' => (object)['type' => 'text', 'link' => 'number_check'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Titular Cheque',
                    'column' => (object)['type' => 'text', 'link' => 'owner_check'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Repassado Por',
                    'column' => (object)['type' => 'text', 'link' => 'forwarded_by'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Vencimento',
                    'column' => (object)['type' => 'label', 'link' => 'due_date'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Status',
                    'column' => (object)['type' => 'label', 'link' => 'status_check'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action'],
                ],
            ],
            'data' => $response->data
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        Secure::access_admin(true);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Cheques',
            'caption' => 'Adicionar',
            'buttons' => []
        ];

        $banks = (new Banks)->getWithFiltersAllItems([(object)['columns' => ['status' => ['value' => true]]]]);
        $customers = (new Customer)->getWithFiltersAllItems([(object)['columns' => ['status' => ['value' => true]]]]);

        array_map(function ($c) {
            $c->name = $c->fancy_name_company ?? ($c->company_name ?? $c->name);
        }, $customers->data);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::access_admin(true);

        Secure::check_post_method($this->route . "/addItem");

        $arrPost = [
            'status' => true,
            'status_check' => 1,
            'id_bank' => $_POST['bank'],
            'agency' => $_POST['agency'],
            'due_date' => $_POST['dueDate'],
            'forwarded_at' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'owner_check' => $_POST['ownerCheck'],
            'forwarded_by' => $_POST['forwardedBy'],
            'number_check' => $_POST['numberCheck'],
            'created_by' => $_SESSION['RR']->user->id,
            'number_account' => $_POST['numberAccount'],
            'value' => Util::unmaskMoney($_POST['value']),
            'cpf_cnpj_check' => Util::removeNonNumericCharacters($_POST['cpfCnpjCheck'])
        ];

        $response = (new CheckControl)->insert($arrPost);

        if (!$response->error) {
            unset($arrPost);
            $date = date('d/m/Y');
            $hour = date('H:i:s');
            $user = (new User)->getItemById($_SESSION['RR']->user->id)->name;
            $message = "Cheque adiconado por <strong>{$user}</strong> em <strong>{$date}</strong> as <strong>{$hour}h</strong>";

            $arrPost = [
                'status' => true,
                'status_icon' => 1,
                'comment' => $message,
                'id_check' => $response->lastId,
                'forwarded_at' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'forwarded_by' => $_POST['forwardedBy'],
                'created_by' => $_SESSION['RR']->user->id
            ];

            (new CheckControlTimeline)->insert($arrPost);
        }

        $_SESSION['RR']->toast = (object)[
            'icon' => !$response->error ? 'success' : 'error',
            'title' => $response->message
        ];

        redirect(!$response->error ? "{$this->route}/editItem/$response->lastId" : "{$this->route}/addItem");
    }

    public function editItem($itemId)
    {
        Secure::access_admin(true);

        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/checkControl.js");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Cheques',
            'caption' => 'Edite',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                ]
            ],
        ];

        $item = (new CheckControl)->getItemById($itemId);
        array_map(function ($i) {
            $i->value = Util::maskMoney($i->value);
        }, [$item]);

        $blockCheck = $item->status_check == 4 ? true : false;

        $customer = (new Customer)->getItemById($item->forwarded_by);
        array_map(function ($c) {
            $c->name = $c->fancy_name_company ?? ($c->company_name ?? $c->name);
        }, [$customer]);

        $banks = (new Banks)->getWithFiltersAllItems([(object)['columns' => ['status' => ['value' => true]]]]);
        $accounts = (new BankAccounts)->getWithFiltersAllItems(
            [
                (object)['columns' => ['status' => ['value' => true]]]
            ],
            [
                (object)['columns' => ['*']],
                (object)['table' => 'banks', 'columns' => ['name' => ['name']]]
            ]
        );

        $filtersStatus = [
            (object)['columns' => ['status' => ['value' => true]]]
        ];

        if ($item->status_check != 3) {
            array_push($filtersStatus, (object)['columns' => ['id' => (object)['comparison' => 'NOT_IN', 'value' => 3]]]);
        }
        $statusCheck = (new StatusCheck)->getWithFiltersAllItems($filtersStatus);

        $formOfPayments = (new FormOfPayment())->getWithFiltersAllItems([(object)['columns' => ['status' => ['value' => true]]]])->data;
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);

        $timeline = (new CheckControlTimeline)->getWithFiltersAllItems(
            [
                (object)['columns' => ['status' => ['value' => true]]],
                (object)['columns' => ['id_check' => ['value' => $item->id]]]
            ],
            [
                (object)['columns' => ['*']],
                (object)['table' => 'status_icon', 'columns' => ['name' => ['icon']]]
            ],
            [
                'orderBy' => 'this->table.id DESC, this->table.created_at DESC'
            ]
        );

        array_map(function ($tl) use ($item, &$existInstallment) {
            if ($tl->id_bill_receive && $tl->id_installment && !$existInstallment && (empty($item->id_bill_receive) && empty($item->id_bills_to_pay))) {
                $existInstallment = (new BillReceiveInstallment)->getItemWithFilters(
                    [
                        (object)['columns' => ['number_portion' => ['value' => $tl->id_installment]]],
                        (object)['columns' => ['id_bill_receive' => ['value' => $tl->id_bill_receive]]],
                        (object)['columns' => ['status_payment' => (object)['comparison' => 'NOT_IN', 'value' => 3]]]
                    ]
                ) ? $tl->id : false;
            }
            $tl->userName = (new User)->getItemById($tl->created_by)->name;

            $tl->comment = nl2br(Util::escapeSystemHtml($tl->comment));
        }, $timeline->data);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::access_admin(true);

        Secure::check_post_method($this->route . "/addItem");

        $item = (new CheckControl)->getItemById($itemId);

        if (!empty($_POST['account'])) {
            $account = (new BankAccounts)->getItemById(
                $_POST['account'],
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'banks', 'columns' => ['name' => ['name']]]
                ]
            );
        }

        if ($_POST['statusCheck'] == $item->status_check) {
            $arrPost = [
                'status' => true,
                'status_icon' => 3,
                'id_check' => $item->id,
                'comment' => $_POST['comment'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['RR']->user->id
            ];

            $response = (new CheckControlTimeline)->insert($arrPost);
        } else if ($_POST['statusCheck'] == 1) {
            $arrPost = [
                'status' => true,
                'status_icon' => 3,
                'id_check' => $item->id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['RR']->user->id,
                'comment' => !empty($_POST['comment']) ? $_POST['comment'] : "Status do cheque modificado para <strong>Aberto!</strong>"
            ];

            $response = (new CheckControlTimeline)->insert($arrPost);

            (new CheckControl)->update(['status_check' => $_POST['statusCheck']], 'id', $item->id);
        } else if ($_POST['statusCheck'] == 2 && !empty($_POST['account'])) {
            $arrPost = [
                'status' => true,
                'status_icon' => 10,
                'id_check' => $item->id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['RR']->user->id,
                'comment' => "Cheque compensado na conta.\n Banco: {$account->name}\n Agencia: {$account->agency}\n Conta: {$account->account_number}"
            ];

            $response = (new CheckControlTimeline)->insert($arrPost);

            unset($arrPost);
            $arrPost = [
                'id_account' => $_POST['account'],
                'status_check' => $_POST['statusCheck']
            ];

            (new CheckControl)->update($arrPost, 'id', $item->id);
        } else if ($_POST['statusCheck'] == 4) {
            $arrPost = [
                'status_icon' => 11,
                'id_check' => $item->id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $_SESSION['RR']->user->id,
                'comment' => !empty($_POST['comment']) ? $_POST['comment'] : "Status do cheque alterado para <strong>Cancelado!</strong>"
            ];

            $response = (new CheckControlTimeline)->insert($arrPost);

            (new CheckControl)->update(['status_check' => $_POST['statusCheck']], 'id', $item->id);
        }

        $_SESSION['RR']->toast = (object)[
            'icon' => !$response->error ? 'success' : 'error',
            'title' => $response->message
        ];

        redirect("{$this->route}/editItem/$item->id");
    }

    // N10: botões Ativar/Inativar da listagem (modal envia POST para check-control/disableItem|enableItem/{id})
    public function disableItem($itemId, $page = "1")
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route);

        try {
            $success = $this->model->disableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . (int) $page);
        exit;
    }

    public function enableItem($itemId, $page = "1")
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route);

        try {
            $success = $this->model->enableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . (int) $page . '&statusCheck=0');
        exit;
    }

    public function handleDeleteCheckTimeline($itemId)
    {
        // Mesma regra do botão de excluir comentário em edit.php
        Secure::access_superAdm(true);

        $checkTimeline = (new CheckControlTimeline)->getItemById($itemId);

        Secure::check_post_method($this->route . "/editItem/$checkTimeline->id_check");

        $response = (new CheckControlTimeline)->update(['status' => false], 'id', $checkTimeline->id);

        $_SESSION['RR']->toast = (object)[
            'icon' => !$response->error ? 'success' : 'error',
            'title' => $response->message
        ];

        redirect("{$this->route}/editItem/{$checkTimeline->id_check}");
    }
}
