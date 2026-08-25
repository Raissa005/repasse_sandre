<?php

namespace RR\controller\project;

use RR\libs\Util;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\BoxAlert;
use RR\libs\Pagination;
use RR\libs\CommissionArrangement;
use RR\model\User;
use RR\model\Sales;
use RR\model\Status;
use RR\model\Branch;
use RR\model\Contract;
use RR\model\Customer;
use RR\model\Property;
use RR\model\GerenciaPost;
use RR\model\FormOfPayment;
use RR\model\ModelGenerico;
use RR\model\StandardContract;
use RR\model\BranchUserPosition;
use RR\model\BillReceiveInstallment;
use RR\model\SalesChargePaymentAgreement;
use PDOException;
use RR\libs\FileUploader;
use RR\libs\RecursiveCostCenter;
use RR\model\Attendance;
use RR\model\BillsToPay;
use RR\model\BillsToPayInstallment;
use RR\model\Currencies;
use RR\model\CustomerBalanceLog;
use RR\model\Expenses;
use RR\model\PaymentsOfSales;
use RR\model\SalesAttachment;
use RR\model\SummaryInvolved;
use RR\model\SummarySale;
use RR\model\UserPosition;

use function RR\controller\redirect;

class SalesController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'sales';
        $this->dir = 'sales';
        $this->model = new Sales();
        $this->table = 'sales';
        parent::__construct($this->route);

        $this->alert = new BoxAlert();
        $this->title = 'Vendas';
    }

    private function navTabs($itemId)
    {
        $item = $this->model->getItemById($itemId);

        $navTabs = [
            (object)['text' => 'Gerais', 'route' => URL . $this->route . '/edit-item/' . $itemId, 'class' => ($_GET['pg1'] == 'edit-item' ? 'active' : '')],
            (object)['text' => 'Anexos', 'route' => URL . $this->route . '/attachment/' . $itemId, 'class' => ($_GET['pg1'] == 'attachment' ? 'active' : '')],
        ];

        if ($this->branch->type == 1 && Secure::access_admin()) {
            array_push($navTabs, (object)['text' => 'Arranjo Pagamento', 'route' => URL . $this->route . '/payment-agreement/' . $itemId, 'class' => ($_GET['pg1'] == 'payment-agreement' ? 'active' : '')]);

            if (!empty($item->origin_commission_seller)) {
                array_push($navTabs, (object)['text' => 'Resumo Venda', 'route' => URL . $this->route . '/summary-payment-agreement/' . $itemId, 'class' => ($_GET['pg1'] == 'summary-payment-agreement' ? 'active' : '')]);
            }

            if ($item->number_installments >= 1) {
                /**As parcelas da comissão são geradas após o arranjo de pagamento for definido */
                if ((new BillReceiveInstallment())->getWithFiltersAllItems([(object)['columns' => ['id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $item->id_bill_receive]]], (object)['columns' => ['status_payment' => (object)['comparison' => '!=', 'value' => 9]]]])->count > 0) {
                    array_push($navTabs, (object)['text' => 'Pagamento de Comissão', 'route' => URL . $this->route . '/commission/' . $itemId, 'class' => ($_GET['pg1'] == 'commission' ? 'active' : '')]);
                }
            }
        }

        if ($this->branch->type == 2) {
            array_push($navTabs, (object)['text' => 'Pagamentos', 'route' => URL . $this->route . '/payment/' . $itemId, 'class' => ($_GET['pg1'] == 'payment' ? 'active' : '')]);
            array_push($navTabs, (object)['text' => 'Despesas', 'route' => URL . $this->route . '/expenses/' . $itemId, 'class' => ($_GET['pg1'] == 'expenses' ? 'active' : '')]);
        }

        return $navTabs;
    }

    public function index()
    {
        $statusSales = (new ModelGenerico)->getAllItens("status");

        $filtersUsers = array(
            "id_branch_and_profile" => $_SESSION['RR']->branch->current->id,
            "order" => " up.access ASC, u.name ASC",
        );

        $users = (new User)->getAndFilterAllUsers(0, $filtersUsers, 0)->data;

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;

        if (!Secure::access_secretary()) {
            $_GET['created_by'] = $_SESSION['RR']->user->id;
        }

        if (!Secure::seller_manager()) {
            $_GET['sales_manager'] = $_SESSION['RR']->user->id;
            $_GET['id_seller_manager'] = $_SESSION['RR']->user->id;
        }

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Vendas',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "{$this->route}/add-item",
                ]
            ],
        ];

        $rows = 20;
        $page = Pagination::getPage();

        $response = $this->model->getAndFilterAllItem($_GET, ['limit' => $rows, 'page' => $page, 'orderBy' => 'sales.id DESC']);
        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function ($item) {
            $item->customer_name = $item->customer_name_identifier;
            $item->property_name = "{$item->product_identifier} <div>{$item->product_name}</div>";

            $item->permission = $item->blocked == 1 && !Secure::access_secretary() ? false : true;

            $item->action = [];
            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => 'fas fa-pencil-alt',
                'href' => $item->permission ? URL . "{$this->route}/edit-item/{$item->id}" : "#",
                'title' => 'Editar',
                'size' => 'sm',
                'color' => 'primary',
                'attr' => (!$item->permission ? ['disabled' => true] : [])
            ]);

            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                'title' => ($item->status ? 'Inativar' : 'Ativar'),
                'size' => 'sm',
                'color' => ($item->status ? 'danger' : 'success'),
                'class' => ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                'attr' => ($item->permission ? ['sendTo' => $this->route . ($item->status ? '/disableItem/' : '/enableItem/')] : ['disabled' => true])
            ]);

            $item->status = (object)['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];

            $item->sale_status = Util::titleCase($item->status_name);
            $item->price = Util::maskMoney($item->sale_value);
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
                    'style' => 'width: 60px',
                    'class' => 'text-center',
                    'text' => 'Cód.',
                    'column' => (object)['type' => 'text', 'link' => 'id'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Cliente',
                    'column' => (object)['type' => 'text', 'link' => 'customer_name'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Imóvel',
                    'column' => (object)['type' => 'text', 'link' => 'property_name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Status Venda',
                    'column' => (object)['type' => 'text', 'link' => 'sale_status'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Valor',
                    'column' => (object)['type' => 'text', 'link' => 'price'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Status',
                    'column' => (object)['type' => 'label', 'link' => 'status'],
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
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/{$this->dir}/" . ($this->branch->type == '1' ? 'real-estate' : 'construction-company') . "/sales.js");
        Secure::redirectFunction(!Secure::protect_managers());

        $currencies = (new Currencies)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]]);
        $standardContractModel = new StandardContract();
        $customers_filter = [
            (object)['columns' => ['status' => (object)['value' => 1]]],
            (object)["table" => "customer_branches", "columns" => ["id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]]],
            (object)["table" => "client_type_resource_types", "columns" => ["id_customer_type" => (object)["value" => 8]]]
        ];
        if (!Secure::access_admin()) {
            $customers_filter[] = (object)['columns' => ['created_by' => (object)['value' => $_SESSION['RR']->user->id]]];
        }
        $customers = (new Customer)->getWithFiltersAllItems($customers_filter)->data;

        if (!isset($_GET['property_branch'])) {
            $_GET['property_branch'] = array_map(function ($element) {
                return $element->id;
            }, (new Branch)->getBranchesByProperties());
        }

        $properties = (new Property)->getWithFiltersAllItems(
            [
                (object)['columns' => ['status' => (object)['value' => 1]]],
                (object)['table' => 'property_branch', 'columns' => ['id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id]]],
                (object)['where' => "AND this->table.id NOT IN (SELECT s.id_product FROM sales s WHERE s.status = 1 AND products.id = s.id_product)"]
            ],
            [],
            ['group' => 'this->table.id', 'order' => 'coalesce(this->table.cod, this->table.id) ASC']
        );

        $status = (new Status())->getAllStatus();
        $users = (new User())->getAndFilterAllUsers(0, ['status' => 1, 'id_branch_and_profile' => $_SESSION['RR']->branch->current->id], 0)->data;
        $today = date("Y-m-d");

        $contracts = $standardContractModel->getAndFilterAllStandardContract(0, ['status' => 1, 'id_type_contract' => 1], 0)->data;
        $proposals = $standardContractModel->getAndFilterAllStandardContract(0, ['status' => 1, 'id_type_contract' => 2], 0)->data;

        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id, [(object)['columns' => ['percentage_commission_sale']]]);
        $form_payment = (new FormOfPayment())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => 1]]]])->data;

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/" . ($this->branch->type == '1' ? 'real-estate' : 'construction-company') . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $response = $this->model->submitAddForm($_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . ($response->error != true ? '/edit-item/' . $response->lastId : ''));
    }

    public function editItem($itemId)
    {
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/" . ($this->branch->type == '1' ? 'real-estate' : 'construction-company') . "/sales.js");

        $item = $this->model->getItemById($itemId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route . "?authorization=false");
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by) && !$item->blocked) || Secure::seller_manager();
        $currencies = (new Currencies)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]]);
        $attendance = (new Attendance)->getItemById($item->id_attendance);

        $currencyValue = $item->currency_id == 1 ? "hidden" : "";
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";
        $attrStatus = $item->id_sale_status > 1 && !Secure::access_admin() || in_array($item->id_sale_status, [3, 9]) ? "disabled" : "";

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => 'Venda',
            'caption' => 'Editar'
        ];

        $navTabs = Self::navTabs($itemId);

        $userCreatedBy = (new User)->getUserById($item->created_by);
        $userUpdatedBy = (new User)->getUserById($item->updated_by);

        $customers_filter = [
            (object)['columns' => ['status' => (object)['value' => 1]]],
            (object)["table" => "customer_branches", "columns" => ["id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]]],
            (object)["table" => "client_type_resource_types", "columns" => ["id_customer_type" => (object)["value" => 8]]]
        ];

        if (!Secure::seller_manager()) {
            $customers_filter[] = (object)['columns' => ['id' => (object)['value' => $item->id_customer]]];
        }

        if (!Secure::access_admin() && Secure::seller_manager()) {
            $customers_filter[] = (object)['columns' => ['created_by' => (object)['value' => $_SESSION['RR']->user->id]]];
        }

        $customers = (new Customer)->getWithFiltersAllItems($customers_filter)->data;

        $properties_filters = [
            (object)['columns' => ['status' => (object)['comparison' => '=', 'value' => 1]]],
            (object)['table' => 'property_branch', 'columns' => ['id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id]]],
            (object)['table' => 'sales', 'where' => "AND ( NOT (this->table.id_product is not null AND this->table.status = 1) OR this->table.id_product = {$item->id_product})"]
        ];

        $properties_options = ['group' => 'this->table.id', 'order' => 'coalesce(this->table.cod, this->table.id) ASC'];
        $properties = (new Property)->getWithFiltersAllItems($properties_filters, [], $properties_options)->data;
        $property = $properties[array_search($item->id_product, array_column($properties, 'id'))];

        $liquid_value = ($item->commission_value / 0.05);
        $seller_managers = (new User)->getWithFiltersAllItems([
            (object)[
                'columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => true],
                    'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]
                ]
            ],
            (object)[
                'table' => 'users_profiles',
                'columns' => [
                    'access' => (object)['comparison' => 'LESSER_EQUAL', 'value' => 20]
                ]
            ]
        ])->data;
        $status = (new Status)->getWithFiltersAllItems([(object)['status' => (object)['comparison' => '=', 'value' => true]]])->data;
        $users = (new User)->getWithFiltersAllItems()->data;
        $contracts = (new StandardContract)->getAndFilterAllStandardContract(0, ['status' => true, 'id_type_contract' => 1], 0)->data;
        $proposals = (new StandardContract)->getAndFilterAllStandardContract(0, ['status' => true, 'id_type_contract' => 2], 0)->data;
        $form_payment = (new FormOfPayment())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => 1]]]])->data;

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/menu.php";
        require APP . "view/{$this->dir}/" . ($this->branch->type == '1' ? 'real-estate' : 'construction-company') . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/edit-item/$itemId");

        $response = $this->model->submitFormEdit($itemId, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . "/edit-item/$itemId");
    }

    public function handleSubmitStatus($itemId)
    {
        Secure::check_post_method($this->route . "/edit-item/$itemId");

        $response = $this->model->submitFormStatus($itemId, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . "/edit-item/$itemId");
    }

    public function attachment($itemId)
    {
        $item = $this->model->getItemById($itemId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route . "?authorization=false");
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by) && !$item->blocked);

        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        $form_add_attachment = "
                            <div class=\"row\">
                                 <div class=\"col-md-6\">
                                    <div class=\"form-group\">
                                        <label for=\"name\">Nome <span class=\"text-danger\">*</span></label>
                                        <input autocomplete=\"off\" type=\"text\" class=\"form-control\" name=\"name\" id=\"name\" required>
                                    </div>
                                </div>
                                <div class=\"col-md-6\">
                                    <div class=\"form-group\">
                                        <label style=\"margin-top: 25px;\" class=\"btn btn-block btn-warning\" for=\"file\">SELECIONE O ANEXO</label>
                                        <input style=\"display: noNe;\" type=\"file\" name=\"file[]\" id=\"file\" required>
                                    </div>
                                </div>
                            </div>
                                ";

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => 'Venda',
            'caption' => 'Anexos',
            'buttons' => [
                (object)[
                    'icon' => "fas fa-paperclip",
                    'text' => 'Adicionar Anexos',
                    'color' => 'primary',
                    'class' => 'btn-generic-item',
                    'attr' => [
                        'sendTo' => "{$this->route}/handleAddAttachment/{$itemId}",
                        'headerHtml' => "Adicionar Anexos",
                        'bodyHtml' => $form_add_attachment,
                        'footerHtml' => "Adicionar",
                        'btnFooter' => "btn-primary",
                    ]
                ]
            ]
        ];


        $navTabs = Self::navTabs($itemId);

        $sales_attachment_filters = [(object)['columns' => ['id_sale' => (object)['comparison' => '=', 'value' => $itemId]]]];
        $sales_attachment_columns = [
            (object)['columns' => ['*']],
        ];
        $data = array_map(function ($attachment) {
            $attachment->action = [];
            $attachment->created_at = Date::date_hour($attachment->created_at);
            array_push(
                $attachment->action,
                (object)[
                    'id' => $attachment->id,
                    'icon' => 'fas fa-paperclip',
                    'title' => 'Download',
                    'size' => 'sm',
                    'color' => 'warning',
                    'href' => URL . "attachments/sales/{$attachment->id_sale}/{$attachment->filename}.{$attachment->extension}",
                    'attr' => ['download' => "{$attachment->name}-{$attachment->filename}"]
                ],
                (object)[
                    'id' => $attachment->id,
                    'icon' => 'fas fa-times',
                    'title' => 'Remover',
                    'size' => 'sm',
                    'color' => 'danger',
                    'class' => 'btn-generic-item',
                    'attr' => [
                        'sendTo' => "{$this->route}/deleteAttachment/",
                        'bodyHtml' => "Deseja realmente EXCLUIR esse anexo?",
                        'headerHtml' => "Excluir Anexo",
                        'footerHtml' => "Excluir",
                        'btnFooter' => "btn-danger",
                    ]
                ]
            );
            return $attachment;
        }, (new SalesAttachment)->getWithFiltersAllItems($sales_attachment_filters, $sales_attachment_columns)->data);

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => false,
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
                    'text' => 'Nome',
                    'column' => (object)['type' => 'text', 'link' => 'name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data de Cadastro',
                    'column' => (object)['type' => 'text', 'link' => 'created_at'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action'],
                ],
            ],
            'data' => $data
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/attachment.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleAddAttachment(int $itemId)
    {
        $attachments = FileUploader::uploadFiles(
            $_FILES['file'],
            array_fill(0, count($_FILES['file']) + 1, "attachments/sales/$itemId")
        );

        foreach ($attachments as $attachment) {
            (new SalesAttachment)->insert([
                "id_sale" => $itemId,
                "name" => $_POST["name"],
                "filename" => $attachment['filename'],
                "extension" => $attachment['extension'],
                "created_by" => $_SESSION['RR']->user->id,
            ]);
        }

        redirect("{$this->route}/attachment/$itemId");
    }

    public function deleteAttachment(int $file_id)
    {
        $file = (new SalesAttachment)->getItemById($file_id);

        $item = $this->model->getItemById($file->id_sale);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route);
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by) && !$item->blocked);

        unlink("attachments/sales/{$file->id_sale}/{$file->filename}.{$file->extension}");
        (new SalesAttachment)->delete($file_id);
        redirect("{$this->route}/attachment/{$file->id_sale}");
    }

    public function contract($itemId)
    {
        parent::addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        parent::addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $contractModel = new Contract();

        $item = $this->model->getItemById($itemId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route . "?authorization=false");
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by) && !$item->blocked);

        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => 'Venda',
            'caption' => 'Contrato',
        ];

        $navTabs = Self::navTabs($itemId);

        $contract = $contractModel->getContractByIdSale($itemId, 1);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/contract.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditContract($itemId)
    {
        Secure::check_post_method($this->route . "/contract/$itemId");

        $item = $this->model->getItemById8161($itemId);
        Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), $this->route . "/contract/" . $itemId, "authorization=false");

        try {
            (new GerenciaPost())->update8191(['contract_text' => $_POST['text']], 'contracts', 'id_sale', $itemId, false);
            header('location:' . URL . $this->route . "/contract/$itemId?edited=true");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/contract/$itemId?edited=false");
            exit;
        }
    }

    public function printContract($itemId, $type_standard)
    {
        $contractModel = new Contract();
        $contractsSale = $contractModel->getAllContractByIdSale($itemId);
        $paymentsOfSales = (new PaymentsOfSales)->getAllPortionsBySale($itemId);

        foreach ($contractsSale as $contract) {
            if ($contract->id_type_standard == $type_standard) {
                $contractVariables = $contractModel->getContractVariablesBySaleId($itemId);
                $contractText = Self::replaceVariables($contract, $contractVariables, $paymentsOfSales);
            }
        }
        require APP . 'view/' . $this->dir . '/printContract.php';
    }

    public function replaceVariables($contract, $contractVariables, $paymentsOfSales)
    {
        $contractText = str_replace("breakPage", "<span class='break-page-print-after'></span>", $contract->contract_text);

        foreach ($contractVariables as $key => $value) {
            if ($key == 'valor_venda') {
                $valorVenda = Util::maskMoney($value);
                $contractVariables->valor_venda = $valorVenda;
                $contractVariables->valorExtenso_venda = Util::converte($valorVenda);
            }

            if ($key == 'rg_cliente') {
                $rgCliente = Util::maskRg($value);
                $contractVariables->rg_cliente = $rgCliente;
            }

            if ($key == 'cep_cliente') {
                $cepCliente = Util::maskCep($value);
                $contractVariables->cep_cliente = $cepCliente;
            }

            if ($key == 'celular_cliente') {
                $celularCliente = Util::maskTelefone($value);
                $contractVariables->celular_cliente = $celularCliente;
            }

            if ($key == 'cep_filial') {
                $cepFilial = Util::maskCep($value);
                $contractVariables->cep_filial = $cepFilial;
            }

            if ($key == 'cpf_cliente') {
                $cpfCliente = Util::maskCpf($value);
                $contractVariables->cpf_cliente = $cpfCliente;
            }

            if ($key == 'cpfGerente_venda') {
                $cpfGerente = Util::maskCpf($value);
                $contractVariables->cpfGerente_venda = $cpfGerente;
            }

            if ($key == 'cnpj_filial') {
                $cpfFilial = Util::maskCpfCnpj($value);
                $contractVariables->cnpj_filial = $cpfFilial;
            }

            if ($key == 'data_venda') {
                $saleDay = date("d", strtotime($value));
                $saleMonth = date("m", strtotime($value));
                $saleYear = date("Y", strtotime($value));

                $contractVariables->dia_venda = $saleDay;
                $contractVariables->mes_venda = Date::month_full($saleMonth);
                $contractVariables->ano_venda = $saleYear;
            }

            if ($key == 'parcelas_venda' and $value >= 1) {
                $value = "";
                foreach ($paymentsOfSales as $payment) {
                    $value = $value . "<b>" . $payment->portion_number . "ª Parcela:</b>" .  "<br>Data de vencimento: " . date("d/m/Y", strtotime($payment->pay_day)) . ". Valor da parcela: " . Util::maskMoney($payment->value) . ". <br>" . "A ser pago por meio de " . $payment->form_of_payment_name . ". <hr>";
                }
            }

            $contractText = str_replace("{%" . $key . "%}", $value, $contractText);
        }
        return $contractText;
    }

    public function resetContract($itemId)
    {
        $modelGenerico = new ModelGenerico();
        $contractModel = new Contract();

        $item = $this->model->getItemById($itemId);

        Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), $this->route . "/contract/" . $itemId, "authorization=false");

        $contract_contract = $contractModel->getContractByIdSale($itemId, 1);
        $textStandardContract = $modelGenerico->getItemById8161($item->id_standard_contract, 'standard_contract');

        try {
            (new GerenciaPost())->update8191(['id_standard_contract' => $item->id_standard_contract, 'contract_text' => $textStandardContract->text], 'contracts', 'id', $contract_contract->id, false);

            header('location:' . URL . $this->route . "/contract/$itemId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/contract/$itemId?edited=false");
            exit;
        }
    }

    public function payment($itemId)
    {
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/" . "payment.js");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/" . "paymentPropertyModal.js");

        $formPaymentModel = new FormOfPayment();

        $item = $this->model->getItemById($itemId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route . "?authorization=false");
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by) && !$item->blocked);

        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => 'Venda',
            'caption' => 'Pagamento',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'href' => URL . "bill-receive/entry/{$item->id_bill_receive}",
                    'text' => 'Ver Lançamento',
                    'permission' => $permission,
                    'attr' => [
                        'target' => '_blank'
                    ]
                ],
            ]
        ];

        $navTabs = Self::navTabs($itemId);

        $paymentsOfSales = (new PaymentsOfSales)->getAllPortionsBySale($itemId);

        $formOfPayment = $formPaymentModel->getAndFilterAllItem(['status' => 1, "form_payment_sale" => 1], 0)->data;

        $totalValuePortions = $this->model->getTotalValuePortionsBySale($itemId);

        $status_portion = [
            0 => (object)['color' => 'label-danger', 'text' => 'Não Pago'],
            1 => (object)['color' => 'label-success', 'text' => 'Pago'],
            9 => (object)['color' => 'label-default', 'text' => 'Cancelado']
        ];

        $type_of_payment = [
            1 => 'Moeda Corrente',
            2 => 'Imóvel',
            3 => 'Veículo',
        ];

        $amountPortions = $this->model->getAmountPortionsBySale($itemId);
        $amount = ++$amountPortions->amount;

        $remainingAmount = $item->sale_value - $totalValuePortions->totalValue;
        $remainingAmount = Util::maskMoney($remainingAmount);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/payment.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleAddPayment($saleId)
    {
        Secure::check_post_method($this->route);
        $item = $this->model->getItemById($saleId);

        if (empty($item->id_bill_receive)) $this->model->insertBillReceiveForPayments($item);

        if (!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1)) {
            $_SESSION['RR']->toast = (object)['icon' => 'error', 'title' => 'Você não tem permissão para realizar essa ação'];
            Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), "sales/payment/" . $saleId);
        }

        $response = (new PaymentsOfSales)->addPayment($saleId, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/payment/{$saleId}");
    }

    public function handleEditPayment($saleId, $portionId)
    {
        Secure::check_post_method($this->route);
        $item = $this->model->getItemById($saleId);

        if (!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1)) {
            $_SESSION['RR']->toast = (object)['icon' => 'error', 'title' => 'Você não tem permissão para realizar essa ação'];
            Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), "sales/payment/" . $saleId);
        }

        $response = (new PaymentsOfSales)->editPayment($saleId);
        if (!$response->error) (new PaymentsOfSales)->editBillReceiveInstallmentFromPortion($portionId, $saleId);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/payment/{$saleId}");
    }

    public function handleInactivePaymentById($paymentId)
    {
        Secure::check_post_method($this->route);
        $portion = $this->model->getPortionById($paymentId);
        $item = $this->model->getItemById($portion->id_sale);

        if (!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1)) {
            $_SESSION['RR']->toast = (object)['icon' => 'error', 'title' => 'Você não tem permissão para realizar essa ação'];
            Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), "sales/payment/" . $portion->id_sale);
        }

        $response = (new PaymentsOfSales)->inactiveStatus($paymentId);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/payment/{$portion->id_sale}");
    }

    public function paymentAgreement(int $itemId)
    {
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/payment-agreement.js");
        $item = $this->model->getItemById($itemId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_admin());
        $permission = Secure::access_admin();

        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => 'Venda',
            'caption' => 'Arranjo de Pagamento',
        ];

        $navTabs = Self::navTabs($itemId);

        $branch_payment_arrangement_positions = (new BranchUserPosition)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'days_after' => (object)['comparison' => 'NOT_EQUAL', 'value' => null],
                        'id_cost_center' => (object)['comparison' => 'NOT_EQUAL', 'value' => null],
                        'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]
                    ]
                ]
            ],
            [
                (object)[
                    'columns' => ['id', 'percentage_commission', 'origin_commission', 'days_after']
                ],
                (object)[
                    'table' => 'users',
                    'columns' => ['id', 'name', 'id_customer', 'phone', 'email']
                ],
                (object)[
                    'table' => 'user_position',
                    'columns' => ['id', 'name']
                ]
            ],
        )->data;

        if (empty($item->origin_commission_seller)) {
            $branch_payment_arrangement = (new Branch)->getItemById($_SESSION['RR']->branch->current->id, [
                (object)[
                    'columns' => [
                        'id_cost_center_receive_commission',
                        'id_form_payment_seller',
                        'percentage_commission_sale',
                        'percentage_commission_virtual_rate',
                        'percentage_commission_real_rate',
                        'percentage_commission_seller',
                        'origin_commission_seller',
                    ]
                ]
            ]);

            $this->model->update([
                'percentage_commission_virtual_rate' => $branch_payment_arrangement->percentage_commission_virtual_rate,
                'percentage_commission_real_rate' => $branch_payment_arrangement->percentage_commission_real_rate,
                'percentage_commission_seller' => $branch_payment_arrangement->percentage_commission_seller,
                'origin_commission_seller' => $branch_payment_arrangement->origin_commission_seller,
            ], 'id', $item->id);

            $item->percentage_commission_virtual_rate = $branch_payment_arrangement->percentage_commission_virtual_rate;
            $item->percentage_commission_real_rate = $branch_payment_arrangement->percentage_commission_real_rate;
            $item->percentage_commission_seller = $branch_payment_arrangement->percentage_commission_seller;
            $item->origin_commission_seller = $branch_payment_arrangement->origin_commission_seller;

            foreach ($branch_payment_arrangement_positions as $position) {
                (new SalesChargePaymentAgreement)->insert([
                    'id_sale' => $item->id,
                    'currency_id' => $item->currency_id ?? 1,
                    'id_user_position' => $position->user_position_id,
                    'id_customer' => $position->users_id_customer,
                    'percentage_commission' => $position->percentage_commission,
                    'origin_commission' => $position->origin_commission,
                    'currency_value' => $item->currency_id != 1 ? $item->currency_value : "",
                    'payment_date' => Date::add_working_days($item->due_date, $position->days_after) ?? Date::add_working_days($item->sale_date, $position->days_after),
                ]);
            }
        }

        $user = (new User)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $item->created_by]]]
            ],
            [
                (object)['columns' => ['id', 'name', 'id_customer']],
                (object)['table' => 'customer', 'columns' => ['id', 'name']],
                (object)['table' => 'users_profiles', 'columns' => ['name']]
            ]
        )->data;

        $array_origin_percentage = [
            (object)['id' => 1, 'name' => 'Comissão Bruta'],
            (object)['id' => 2, 'name' => 'Comissão Líquida Real'],
            (object)['id' => 3, 'name' => 'Comissão Líquida Virtual'],
        ];

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $formOfPayment = (new FormOfPayment())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => 1]]]])->data;

        $positions = (new SalesChargePaymentAgreement)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id_sale' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object)['columns' => ['id', 'percentage_commission', 'origin_commission']],
                (object)['table' => 'customer', 'columns' => ['id', 'name', 'fancy_name_company', 'company_name']],
                (object)['table' => 'user_position', 'columns' => ['id', 'name']]
            ]
        )->data;

        array_map(function ($position) {
            $position->name = '';
            if (!empty($position->customer_fancy_name_company)) {
                $position->name = $position->customer_fancy_name_company;
            } else if (!empty($position->customer_company_name)) {
                $position->name = $position->customer_company_name;
            } else if (!empty($position->customer_name)) {
                $position->name = $position->customer_name;
            }

            $position->position_name = '';
            if (!empty($position->user_position_name)) {
                $position->position_name .= $position->user_position_name;
            }
        }, $positions);

        $usersPositions = (new User)->getWithFiltersAllItems(
            [],
            [
                (object)['columns' => ['id', 'name', 'id_customer']],
                (object)['table' => 'users_profiles', 'columns' => ['name']]
            ]
        )->data;

        $calculateCommission = (new CommissionArrangement($item->sale_value, $item->percentage_commission, (object)['real' => $item->percentage_commission_real_rate, 'virtual' => $item->percentage_commission_virtual_rate], $itemId));

        $arrangement = (object)[
            'commission' => $calculateCommission->commission(),
            'taxes' => $calculateCommission->taxes(),
            'branch' => $calculateCommission->branch((object)['type' => $item->origin_commission_seller, 'percentage' => $item->percentage_commission_seller], $positions),
            'seller' => $calculateCommission->seller($item->origin_commission_seller, $item->percentage_commission_seller),
            'positions' => $calculateCommission->positions($positions),
        ];

        $freezePaymentAgreement = (new BillReceiveInstallment)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $item->id_bill_receive],
                        'status_payment' => (object)['comparison' => 'NOT_EQUAL', 'value' => 3],
                        'status_payment' => (object)['comparison' => 'NOT_EQUAL', 'value' => 9]
                    ]
                ]
            ]
        )->count > 0 ? true : false;

        $generateInstallments = count(array_filter($positions, function ($p) {
            return $p->customer_id == null;
        })) == 0 && !$freezePaymentAgreement ? true : false;

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/payment-agreement.php";
        require APP . 'view/_templates/footer.php';
    }

    public function summaryPaymentAgreement(int $itemId)
    {
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/payment-agreement.js");

        $item = $this->model->getItemById($itemId);
        $monthFirstInstallment = $item->due_date ?? $item->sale_date;
        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
        $summarySale = (new SummarySale)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]]);
        $currencies = (new Currencies)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]]);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_admin());
        $permission = Secure::access_admin();

        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => 'Venda',
            'caption' => 'Resumo Venda',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'icon' => 'fas fa-print',
                    'text' => 'Imprimir Resumo Venda',
                    'href' => URL . $this->route . '/printSummaryPaymentAgreement/' . $itemId,
                    'attr' => [
                        'target' => '_blank',
                    ]
                ]
            ],
        ];

        $navTabs = Self::navTabs($itemId);

        if ($summarySale->count > 0) {
            $summarySale->totalPaid = 0;
            $summarySale->totalPaidSeller = 0;

            array_map(function ($el) use ($summarySale) {
                $summarySale->totalPaid = $summarySale->totalPaid + $el->amount;
                $summarySale->totalPaidSeller = $summarySale->totalPaidSeller + $el->seller_amount;
            }, $summarySale->data);

            $generateInstallments = true;
        } else {
            $targetDay =  date('d', strtotime($monthFirstInstallment));
            $summarySale->totalPaid = 0;

            for ($i = 0; $i < $item->number_installments; $i++) {
                $summarySale->data[$i] = (object)[
                    'currency_id' => $item->currency_id,
                    'sale_id' => $item->id,
                    'installment_number' => $i + 1,
                    'received_date' => $monthFirstInstallment,
                    'currency_value' => $item->currency_id != 1 ? $item->currency_value : null,
                    'amount' => $item->commission_value / $item->number_installments,
                    'seller_currency_id' => $item->currency_id,
                    'seller_currency_value' => $item->currency_id != 1 ? $item->currency_value : null,
                    'seller_payment_date' => Date::add_working_days($monthFirstInstallment, $branch->days_after_seller)
                ];

                $summarySale->count = $i + 1;
                $summarySale->totalPaid = $item->commission_value;
                $monthFirstInstallment = Date::safeGenNextDueDate($monthFirstInstallment, $targetDay);

                $generateInstallments = false;
            }
        }

        $user = (new User)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $item->created_by]]]
            ],
            [
                (object)['columns' => ['id', 'name', 'id_customer']],
                (object)['table' => 'customer', 'columns' => ['id', 'name']],
                (object)['table' => 'users_profiles', 'columns' => ['name']]
            ]
        )->data;

        $array_origin_percentage = [
            (object)['id' => 1, 'name' => 'Comissão Bruta'],
            (object)['id' => 2, 'name' => 'Comissão Líquida Real'],
            (object)['id' => 3, 'name' => 'Comissão Líquida Virtual'],
        ];

        $positions = (new SalesChargePaymentAgreement)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id_sale' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object)['columns' => ['id', 'id_cost_center', 'id_form_payment', 'percentage_commission', 'origin_commission', 'payment_date', 'currency_id', 'currency_value']],
                (object)['table' => 'customer', 'columns' => ['id', 'name', 'fancy_name_company', 'company_name']],
                (object)['table' => 'user_position', 'columns' => ['id', 'name']]
            ]
        )->data;

        array_map(function ($position) {
            $position->name = '';
            if (!empty($position->customer_fancy_name_company)) {
                $position->name = $position->customer_fancy_name_company;
            } else if (!empty($position->customer_company_name)) {
                $position->name = $position->customer_company_name;
            } else if (!empty($position->customer_name)) {
                $position->name = $position->customer_name;
            }

            $position->position_name = '';
            if (!empty($position->user_position_name)) {
                $position->position_name .= $position->user_position_name;
            }
        }, $positions);

        $customers = (new Customer())->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => 1]]
                ],
                (object)[
                    'table' => 'client_type_resource_types',
                    'columns' => ['id_customer_type' => (object)['comparison' => 'EQUAL', 'value' => 10]]
                ]
            ]
        )->data;

        array_map(function ($customer) {
            if (!empty($customer->fancy_name_company)) {
                $customer->name = $customer->fancy_name_company;
            } else if (!empty($customer->company_name)) {
                $customer->name = $customer->company_name;
            }
        }, $customers);

        $calculateCommission = (new CommissionArrangement($item->sale_value, $item->percentage_commission, (object)['real' => $item->percentage_commission_real_rate, 'virtual' => $item->percentage_commission_virtual_rate], $itemId, $item->number_installments));

        $arrangement = (object)[
            'commission' => $calculateCommission->commission(),
            'taxes' => $calculateCommission->taxes(),
            'branch' => $calculateCommission->branch((object)['type' => $item->origin_commission_seller, 'percentage' => $item->percentage_commission_seller], $positions),
            'seller' => $calculateCommission->seller($item->origin_commission_seller, $item->percentage_commission_seller),
            'positions' => $calculateCommission->positions($positions),
        ];

        if (empty($summarySale->totalPaidSeller)) {
            $summarySale->totalPaidSeller = Util::unmaskMoney($arrangement->seller->amount) * $item->number_installments;
        }

        $summaryInvolved = (new SummaryInvolved)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]]);

        if ($summaryInvolved->count > 0) {
            array_map(function ($el) {
                if ($el->customer_id && $el->user_position_id || $el->customer_id && $el->user_position_id == 0) {
                    $customer = [(new Customer)->getItemById($el->customer_id)];

                    array_map(function ($customer) use ($el) {
                        if (!empty($customer->fancy_name_company)) {
                            $el->name = $customer->fancy_name_company;
                        } else if (!empty($customer->company_name)) {
                            $el->name = $customer->company_name;
                        } else if (!empty($customer->name)) {
                            $el->name = $customer->name;
                        }
                    }, $customer);

                    $el->amount = Util::maskMoney($el->amount);
                    $el->position_name = !empty((new UserPosition)->getItemById($el->user_position_id)->name) ? (new UserPosition)->getItemById($el->user_position_id)->name : "";
                }
            }, $summaryInvolved->data);
        } else {
            $summaryInvolved->count = 0;
            $monthFirstInstallment = $item->due_date ?? $item->sale_date;

            for ($i = 0; $i < $item->number_installments; $i++) {
                $targetDay =  date('d', strtotime($monthFirstInstallment));
                foreach ($arrangement->positions as $position) {
                    if ($position->customer_id && $position->user_position_id || $position->customer_id && $position->user_position_id == 0) {
                        $summaryInvolved->data[] = (object)[
                            'sale_id' => $item->id,
                            'installment_number' => $i + 1,
                            'id' => $summaryInvolved->count + 1,
                            'customer_id' => $position->customer_id,
                            'currency_id' => $position->currency_id,
                            'payment_date' => Date::add_working_days($monthFirstInstallment, $branch->days_after_single),
                            'position_name' => $position->user_position_name,
                            'user_position_id' => $position->user_position_id,
                            'id_cost_center' => $position->id_cost_center ?? "",
                            'id_form_payment' => $position->id_form_payment ?? "",
                            'currency_value' => $position->currency_value ?? null,
                            'amount' => Util::maskMoney(Util::unmaskMoney($position->amount) / $item->number_installments),
                            'name' => $position->customer_fancy_name_company ?? ($position->customer_company_name ?? $position->customer_name)
                        ];
                    }

                    $summaryInvolved->count = $summaryInvolved->count + 1;
                }

                $monthFirstInstallment = Date::safeGenNextDueDate($monthFirstInstallment, $targetDay);
            }
        }

        $summaryInvolvedTotal = (new SummaryInvolved)->getTotalInvolved($itemId);

        if (!empty($summaryInvolvedTotal)) {
            $totalValueInvolved = 0;

            array_map(function ($el) use (&$totalValueInvolved) {
                if ($el->customer_id && $el->user_position_id || $el->customer_id && $el->user_position_id == 0) {
                    $customer = [(new Customer)->getItemById($el->customer_id)];

                    array_map(function ($customer) use ($el) {
                        if (!empty($customer->fancy_name_company)) {
                            $el->name = $customer->fancy_name_company;
                        } else if (!empty($customer->company_name)) {
                            $el->name = $customer->company_name;
                        } else if (!empty($customer->name)) {
                            $el->name = $customer->name;
                        }
                    }, $customer);

                    $totalValueInvolved += $el->total;
                    $el->total = Util::maskMoney($el->total);
                    $el->position_name = !empty((new UserPosition)->getItemById($el->user_position_id)->name) ? (new UserPosition)->getItemById($el->user_position_id)->name : "";
                }
            }, $summaryInvolvedTotal);
        } else {
            $totalValueInvolved = 0;

            foreach ($arrangement->positions as $position) {
                $summaryInvolvedTotal[] = (object)[
                    'customer_id' => $position->customer_id,
                    'user_position_id' => $position->user_position_id,
                    'total' => $position->amount,
                    'name' => $position->customer_fancy_name_company ?? ($position->customer_company_name ?? $position->customer_name),
                    'position_name' => $position->position_name,
                ];

                $totalValueInvolved += Util::unmaskMoney($position->amount);
            }
        }

        $freezePaymentAgreement = (new BillReceiveInstallment)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $item->id_bill_receive],
                        'status_payment' => (object)['comparison' => 'NOT_EQUAL', 'value' => 3],
                        'status_payment' => (object)['comparison' => 'NOT_EQUAL', 'value' => 9]
                    ]
                ]
            ]
        )->count > 0 ? true : false;

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/summary-payment-agreement.php";
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitSummaryPaymentAgreement(int $itemId)
    {
        Secure::access_admin() ? true : parent::noticeAuthorization();

        $response = (new SummarySale)->handleFormSummaryPaymentAgreement($itemId, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/summary-payment-agreement/$itemId");
    }

    public function printPaymentAgreement(int $itemId)
    {
        $item = $this->model->getItemById($itemId);
        $user_name = (new User)->getItemById($item->created_by, [(object)['columns' => ['name']]])->name;
        $positions = (new SalesChargePaymentAgreement)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id_sale' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object)['columns' => ['id', 'percentage_commission', 'origin_commission']],
                (object)['table' => 'customer', 'columns' => ['id', 'name', 'fancy_name_company', 'company_name']],
                (object)['table' => 'user_position', 'columns' => ['id', 'name']]
            ]
        )->data;
        array_map(function ($position) {
            $position->name = '';
            if (!empty($position->customer_fancy_name_company)) {
                $position->name = $position->customer_fancy_name_company;
            } else if (!empty($position->customer_company_name)) {
                $position->name = $position->customer_company_name;
            } else if (!empty($position->customer_name)) {
                $position->name = $position->customer_name;
            }

            $position->position_name = '';
            if (!empty($position->user_position_name)) {
                $position->position_name .= $position->user_position_name;
            }
        }, $positions);
        $calculateCommission = (new CommissionArrangement($item->sale_value, $item->percentage_commission, (object)['real' => $item->percentage_commission_real_rate, 'virtual' => $item->percentage_commission_virtual_rate], $itemId));
        $arrangement = (object)[
            'commission' => $calculateCommission->commission(),
            'taxes' => $calculateCommission->taxes(),
            'branch' => $calculateCommission->branch((object)['type' => $item->origin_commission_seller, 'percentage' => $item->percentage_commission_seller], $positions),
            'seller' => $calculateCommission->seller($item->origin_commission_seller, $item->percentage_commission_seller),
            'positions' => $calculateCommission->positions($positions),
        ];

        $array_origin_percentage = [
            1 => 'Comissão Bruta',
            'Comissão Líquida Real',
            'Comissão Líquida Virtual',
        ];
        $item->sale_value = Util::maskMoney($item->sale_value);

        require APP . "view/{$this->dir}/print-payment-agreement.php";
    }

    public function printSummaryPaymentAgreement(int $itemId)
    {
        $item = $this->model->getItemById($itemId);
        $user = (new User)->getItemWithFilters(
            [
                (object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $item->created_by]]]
            ],
            [
                (object)['columns' => ['id', 'name', 'id_customer']],
                (object)['table' => 'customer', 'columns' => ['id', 'name']],
                (object)['table' => 'users_profiles', 'columns' => ['name']]
            ]
        );

        $monthFirstInstallment = $item->due_date ?? $item->sale_date;
        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
        $summarySale = (new SummarySale)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]]);

        if ($summarySale->count > 0) {
            $summarySale->totalPaid = 0;
            $summarySale->totalPaidSeller = 0;

            array_map(function ($el) use ($summarySale) {
                if ($el->seller_currency_id) {
                    $el->seller_currency_name = (new Currencies)->getItemById($el->seller_currency_id)->currency_symbol;
                }

                $summarySale->totalPaid = $summarySale->totalPaid + $el->amount;
                $summarySale->totalPaidSeller = Util::maskMoney(Util::unmaskMoney($summarySale->totalPaidSeller) + $el->seller_amount);
            }, $summarySale->data);

            $generateInstallments = true;
        } else {
            $targetDay =  date('d', strtotime($monthFirstInstallment));
            $summarySale->totalPaid = 0;

            for ($i = 0; $i < $item->number_installments; $i++) {
                $summarySale->data[$i] = (object)[
                    'currency_id' => $item->currency_id,
                    'sale_id' => $item->id,
                    'installment_number' => $i + 1,
                    'received_date' => $monthFirstInstallment,
                    'currency_value' => $item->currency_id != 1 ? $item->currency_value : null,
                    'amount' => $item->commission_value / $item->number_installments,
                    'seller_currency_id' => $item->currency_id,
                    'seller_currency_value' => $item->currency_id != 1 ? $item->currency_value : null,
                    'seller_currency_name' => (new Currencies)->getItemById($item->currency_id)->currency_symbol,
                    'seller_payment_date' => Date::add_working_days($monthFirstInstallment, $branch->days_after_seller)
                ];

                $summarySale->count = $i + 1;
                $summarySale->totalPaid = $item->commission_value;
                $monthFirstInstallment = Date::safeGenNextDueDate($monthFirstInstallment, $targetDay);

                $generateInstallments = false;
            }
        }

        $positions = (new SalesChargePaymentAgreement)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id_sale' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object)['columns' => ['id', 'percentage_commission', 'origin_commission', 'payment_date', 'currency_id', 'currency_value']],
                (object)['table' => 'customer', 'columns' => ['id', 'name', 'fancy_name_company', 'company_name']],
                (object)['table' => 'user_position', 'columns' => ['id', 'name']]
            ]
        )->data;

        array_map(function ($position) {
            $position->name = '';
            if (!empty($position->customer_fancy_name_company)) {
                $position->name = $position->customer_fancy_name_company;
            } else if (!empty($position->customer_company_name)) {
                $position->name = $position->customer_company_name;
            } else if (!empty($position->customer_name)) {
                $position->name = $position->customer_name;
            }

            $position->position_name = '';
            if (!empty($position->user_position_name)) {
                $position->position_name .= $position->user_position_name;
            }
        }, $positions);

        $calculateCommission = (new CommissionArrangement($item->sale_value, $item->percentage_commission, (object)['real' => $item->percentage_commission_real_rate, 'virtual' => $item->percentage_commission_virtual_rate], $itemId, $item->number_installments));

        $item->sale_value = Util::maskMoney($item->sale_value);

        $arrangement = (object)[
            'commission' => $calculateCommission->commission(),
            'taxes' => $calculateCommission->taxes(),
            'branch' => $calculateCommission->branch((object)['type' => $item->origin_commission_seller, 'percentage' => $item->percentage_commission_seller], $positions),
            'seller' => $calculateCommission->seller($item->origin_commission_seller, $item->percentage_commission_seller),
            'positions' => $calculateCommission->positions($positions),
        ];

        if (empty($summarySale->totalPaidSeller)) {
            $summarySale->totalPaidSeller = Util::maskMoney(Util::unmaskMoney($arrangement->seller->amount) * $item->number_installments);
        }

        array_map(function ($el) use ($arrangement) {
            $el->amount = Util::maskMoney($el->amount);
            $el->seller_amount = !empty($el->seller_amount) ? Util::maskMoney($el->seller_amount) : $arrangement->seller->amount;
            $el->received_date = date("d/m/Y", strtotime($el->received_date));
            $el->seller_payment_date = date("d/m/Y", strtotime($el->seller_payment_date));
            $el->currency_name = (new Currencies)->getItemById($el->currency_id)->currency_symbol;
        }, $summarySale->data);

        $summaryInvolved = (new SummaryInvolved)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]]);

        if ($summaryInvolved->count > 0) {
            array_map(function ($el) {
                if ($el->customer_id && $el->user_position_id || $el->customer_id && $el->user_position_id == 0) {
                    $customer = [(new Customer)->getItemById($el->customer_id)];

                    array_map(function ($customer) use ($el) {
                        if (!empty($customer->fancy_name_company)) {
                            $el->name = $customer->fancy_name_company;
                        } else if (!empty($customer->company_name)) {
                            $el->name = $customer->company_name;
                        } else if (!empty($customer->name)) {
                            $el->name = $customer->name;
                        }
                    }, $customer);

                    $el->amount = Util::maskMoney($el->amount);
                    $el->payment_date = date("d/m/Y", strtotime($el->payment_date));
                    $el->currency_name = (new Currencies)->getItemById($el->currency_id)->currency_symbol;
                    $el->position_name = !empty((new UserPosition)->getItemById($el->user_position_id)->name) ? (new UserPosition)->getItemById($el->user_position_id)->name : "";
                }
            }, $summaryInvolved->data);
        } else {
            for ($i = 0; $i < $item->number_installments; $i++) {
                foreach ($arrangement->positions as $position) {
                    if ($position->customer_id && $position->user_position_id) {
                        $summaryInvolved->data[] = (object)[
                            'id' => $i + 1,
                            'sale_id' => $item->id,
                            'installment_number' => $i + 1,
                            'customer_id' => $position->customer_id,
                            'currency_id' => $position->currency_id,
                            'payment_date' => $position->payment_date,
                            'position_name' => $position->user_position_name,
                            'user_position_id' => $position->user_position_id,
                            'currency_value' => $position->currency_value ?? null,
                            'currency_name' => (new Currencies)->getItemById($position->currency_id)->currency_symbol,
                            'amount' => Util::maskMoney(Util::unmaskMoney($position->amount) / $item->number_installments),
                            'name' => $position->customer_fancy_name_company ?? ($position->customer_company_name ?? $position->customer_name)
                        ];
                    }

                    $summaryInvolved->count = $summaryInvolved->count + 1;
                }
            }
        }

        $summaryInvolvedTotal = (new SummaryInvolved)->getTotalInvolved($itemId);

        if (!empty($summaryInvolvedTotal)) {
            $totalValueInvolved = 0;

            array_map(function ($el) use (&$totalValueInvolved) {
                if ($el->customer_id && $el->user_position_id || $el->customer_id && $el->user_position_id == 0) {
                    $customer = [(new Customer)->getItemById($el->customer_id)];

                    array_map(function ($customer) use ($el) {
                        if (!empty($customer->fancy_name_company)) {
                            $el->name = $customer->fancy_name_company;
                        } else if (!empty($customer->company_name)) {
                            $el->name = $customer->company_name;
                        } else if (!empty($customer->name)) {
                            $el->name = $customer->name;
                        }
                    }, $customer);

                    $totalValueInvolved += $el->total;
                    $el->total = Util::maskMoney($el->total);
                    $el->position_name = !empty((new UserPosition)->getItemById($el->user_position_id)->name) ? (new UserPosition)->getItemById($el->user_position_id)->name : "";
                }
            }, $summaryInvolvedTotal);
        } else {
            foreach ($arrangement->positions as $position) {
                $summaryInvolvedTotal[] = (object)[
                    'customer_id' => $position->customer_id,
                    'user_position_id' => $position->user_position_id,
                    'total' => $position->amount,
                    'name' => $position->customer_fancy_name_company ?? ($position->customer_company_name ?? $position->customer_name),
                    'position_name' => $position->position_name,
                ];
            }
        }

        require APP . "view/{$this->dir}/print-summary-payment-agreement.php";
    }

    public function handleSubmitPaymentAgreement(int $itemId)
    {
        Secure::check_post_method($this->route . "/payment-agreement/$itemId");
        Secure::access_admin() ? true : parent::noticeAuthorization();

        $response = $this->model->handleFormPaymentAgreement($itemId, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/payment-agreement/$itemId");
    }

    public function generateInstallmentsBillsReceive(int $itemId)
    {
        Secure::access_admin() ? true : parent::noticeAuthorization();

        $response = $this->model->handleFormGenerateInstallments($itemId);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/commission/$itemId");
    }

    public function commission($itemId)
    {
        parent::addScript(URL . 'js/' . JSVERSION . "/{$this->dir}/commission.js");

        $item = $this->model->getItemById($itemId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route . "?authorization=false");
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by) && !$item->blocked);

        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$itemId}",
            'title' => 'Venda',
            'caption' => 'Comissão',
            'buttons' => [
                (object)[
                    'icon' => 'fas fa-times',
                    'text' => 'Excluir <strong>TODAS</strong> as parcelas',
                    'color' => 'danger',
                    'class' => 'btn-cancel-installments',
                    'attr' => ['sendTo' => URL . "{$this->route}/cancelAllSalesCommissionInstallments/$itemId", 'bill-receive-id' => $item->id_bill_receive]
                ],
                (object)[
                    'text' => 'Visualizar Lançamento',
                    'color' => 'info',
                    'href' => URL . "bill-receive/entry/{$item->id_bill_receive}",
                    'attr' => ['target' => '_blank']
                ]
            ]
        ];

        $navTabs = Self::navTabs($itemId);

        $array_origin_percentage = [
            (object)['id' => 1, 'name' => 'Bruta'],
            (object)['id' => 2, 'name' => 'Líquida Real'],
            (object)['id' => 3, 'name' => 'Líquida Virtual'],
        ];

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $formOfPayment = (new FormOfPayment())->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => 1]
                    ]
                ]
            ]
        )->data;

        $paymentMethods = (new FormOfPayment)->getAndFilterAllItem(['status' => 1, 'form_payment_accounts_payable' => 1], 0);
        $branch = (new Branch)->getItemById8161($_SESSION['RR']->branch->current->id);
        $baseCommissionAmount = Util::maskMoney($item->sale_value * ($branch->percentage_commission_sale / 100));

        $installments = (new BillReceiveInstallment)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $item->id_bill_receive],
                        'status_payment' => (object)['comparison' => 'NOT_IN', 'value' => [3, 9]],
                    ]
                ]
            ],
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'form_of_payment',
                    'columns' => ['name']
                ]
            ]
        )->data;

        $boxInfoValue = (object) [
            'total' => 0,
            'paid' => 0,
            'open' => 0,
            'late' => 0,
        ];

        array_map(function ($installment) use ($itemId, $boxInfoValue) {

            if ($installment->status_payment != 3 && $installment->status_payment != 9) {
                $boxInfoValue->total += $installment->value_installment;
            }

            $installment->action = [];

            switch ($installment->status_payment) {
                case '1':
                    if ($installment->due_date < date('Y-m-d')) {
                        $installment->tr = 'text-red';
                        $installment->status = (object)['value' => 'Atrasado', 'color' => 'danger'];
                        $boxInfoValue->late += $installment->value_installment;
                    } else {
                        if ($installment->due_date == date('Y-m-d')) {
                            $installment->tr = 'text-yellow';
                        }
                        $installment->status = (object)['value' => 'Aguard. Pagam.', 'color' => 'warning'];
                        $boxInfoValue->open += $installment->value_installment;
                    }

                    array_push(
                        $installment->action,
                        (object)[
                            'id' => $installment->id,
                            'icon' => 'fas fa-pencil-alt',
                            'title' => 'Editar',
                            'href' => URL . 'bill-receive-installment/edit/' . $installment->id,
                            'size' => 'sm',
                            'color' => 'primary',
                            'class' => 'btn-edit-installment',
                            'attr' => ['target' => '_blank']
                        ]
                    );

                    break;
                case '2':
                    $installment->status = (object)['value' => 'Pago', 'color' => 'success'];
                    $boxInfoValue->paid += $installment->value_installment;
                    array_push(
                        $installment->action,
                        (object)[
                            'id' => $installment->id,
                            'icon' => 'fas fa-pencil-alt',
                            'title' => 'Editar',
                            'size' => 'sm',
                            'href' => URL . 'bill-receive-installment/edit/' . $installment->id,
                            'color' => 'primary',
                            'class' => '',
                            'attr' => ['target' => '_blank']
                        ]
                    );

                    break;
                case '3':
                    $installment->status = (object)['value' => 'Cancelado', 'color' => 'default'];
                    array_push(
                        $installment->action,
                        (object)[
                            'id' => $installment->id,
                            'icon' => 'fas fa-pencil-alt',
                            'title' => 'Editar',
                            'size' => 'sm',
                            'href' => URL . 'bill-receive-installment/edit/' . $installment->id,
                            'color' => 'primary',
                            'class' => 'disabled',
                            'attr' => ['target' => '_blank']
                        ]
                    );
                    break;
            }

            $installment->due_date = Date::date($installment->due_date);
            $installment->value_installment = $installment->status_payment == 2 ? Util::maskMoney($installment->amount_paid) : Util::maskMoney($installment->value_installment);
        }, $installments);

        $boxInfo = [
            (object)['bg' => 'blue', 'icon' => 'fas fa-coins', 'text' => 'Valor Total', 'value' => Util::maskMoney($boxInfoValue->total)],
            (object)['bg' => 'green', 'icon' => 'fas fa-wallet', 'text' => 'Valor Pago', 'value' => Util::maskMoney($boxInfoValue->paid)],
            (object)['bg' => 'yellow', 'icon' => 'fas fa-money-bill-wave', 'text' => 'Valor a Pagar', 'value' => Util::maskMoney($boxInfoValue->open)],
            (object)['bg' => 'red', 'icon' => 'fas fa-hand-holding-usd', 'text' => 'Valor Atrasado', 'value' => Util::maskMoney($boxInfoValue->late)],
        ];

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
                    'column' => (object)['type' => 'text', 'link' => 'due_date'],
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
                    'text' => 'Status',
                    'column' => (object)['type' => 'label', 'link' => 'status'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action'],
                ],
            ],
            'data' => $installments
        ];

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/commission.php";
        require APP . 'view/_templates/footer.php';
    }

    public function cancelAllSalesCommissionInstallments($itemId)
    {
        Secure::redirectFunction(!Secure::access_admin(), "{$this->route}/edit-item/$itemId");

        $item = $this->model->getItemById($itemId);
        $bill_receive_installments = (new BillReceiveInstallment)->getWithFiltersAllItems(
            [(object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id_bill_receive]]]]
        )->data;
        $bill_pay_installments = (new BillsToPayInstallment)->getWithFiltersAllItems(
            [(object)['table' => 'bills_to_pay', 'columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $item->id_bill_receive]]]]
        )->data;

        switch ($_POST['payment_transaction']) {
            case '1':
                foreach ($bill_receive_installments as $bill_receive_installment) {
                    if ($bill_receive_installment->status_payment == 2) { #pago
                        (new CustomerBalanceLog)->insertLogReceive($item->id_customer, $bill_receive_installment->amount_paid, "Valor gerado ao cancelar todas as parcelas na Venda de código {$item->id}", $item->id);
                        (new BillReceiveInstallment)->update(['payment_transaction' => 3, 'value_installment' => 0, 'status_payment' => 9], 'id', $bill_receive_installment->id); #gerando credito para o cliente comprador
                    } else {
                        (new BillReceiveInstallment)->delete($bill_receive_installment->id);
                    }
                }

                foreach ($bill_pay_installments as $bill_pay_installment) {
                    if ($bill_pay_installment->status_payment == 2) { #pago
                        (new CustomerBalanceLog)->insertLogPay($item->id_customer, $bill_pay_installment->amount_paid, "Valor descontado ao cancelar todas as parcelas na Venda {$item->id}", $item->id);
                        (new BillsToPayInstallment)->update(['payment_transaction' => 3, 'value_of_installments' => 0, 'status_payment' => 9], 'id', $bill_pay_installment->id); #gerando debitos para os cliente fornecedores
                    } else {
                        (new BillsToPayInstallment)->delete($bill_pay_installment->id);
                    }
                }
                break;
            case '2':
                foreach ($bill_receive_installments as $bill_receive_installment) {
                    if ($bill_receive_installment->status_payment != 2) { #pago
                        (new BillReceiveInstallment)->delete($bill_receive_installment->id);
                    }
                }

                foreach ($bill_pay_installments as $bill_pay_installment) {
                    if ($bill_pay_installment->status_payment != 2) { #pago
                        (new BillsToPayInstallment)->delete($bill_pay_installment->id);
                    }
                }
                break;
            default:
                foreach ($bill_receive_installments as $bill_receive_installment) {
                    (new BillReceiveInstallment)->delete($bill_receive_installment->id);
                }
                foreach ($bill_pay_installments as $bill_pay_installment) {
                    (new BillsToPayInstallment)->delete($bill_pay_installment->id);
                }
                break;
        }

        $bills_pay = (new BillsToPay)->getWithFiltersAllItems(
            [(object)['columns' => ['id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $item->id_bill_receive]]]]
        )->data;

        foreach ($bills_pay as $bill_pay) {
            /**Deletando lançamento de contas a pagar vazio */
            if (empty((new BillsToPayInstallment)->getWithFiltersAllItems(
                [(object)['columns' => ['id_bills_to_pay' => (object)['comparison' => 'EQUAL', 'value' => $bill_pay->id]]]]
            )->data)) {
                (new BillsToPay)->delete($bill_pay->id);
            }
        }

        redirect("{$this->route}/summary-payment-agreement/$itemId");
    }

    public function handleSubmitArrangement(int $itemId, int $bill_receive_installment_id)
    {
        Secure::check_post_method($this->route . "/commission/$itemId");
        Secure::redirectFunction(!Secure::access_admin(), "{$this->route}/edit-item/$itemId");

        $response = $this->model->handleFormPaymentArrangement($bill_receive_installment_id, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/commission/$itemId");
    }

    public function disableItem($itemId, $page)
    {
        $item = (new Sales())->getItemById($itemId);

        Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), $this->route, "authorization=false");

        $billReceiveInstalment = (new BillReceiveInstallment)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'status_payment' => (object)['comparison' => 'IN', 'value' => [1, 2]]
                    ]
                ],
                (object)[
                    'table' => 'bill_receive',
                    'columns' => [
                        'id' => (object)['comparison' => 'EQUAL', 'value' => $item->id_bill_receive]
                    ]
                ]
            ]
        )->data;
        if ($billReceiveInstalment) {
            foreach ($billReceiveInstalment as $item) {
                (new BillReceiveInstallment)->submitCancelInstallment($item->id);
            }
        }

        $this->model->disableItem($itemId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $item = (new Sales())->getItemById($itemId);

        Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), $this->route, "authorization=false");

        $this->model->enableItem($itemId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function expenses(int $itemId): void
    {
        Secure::redirectFunction(!($this->branch->type == 2));
        $this->addScript(URL . "js/" . JSVERSION . "/{$this->dir}/" . "/expenses.js");
        $item = (new Sales())->getItemById($itemId);
        $expenses = (new Expenses)->getWithFiltersAllItems([(object)['columns' => ['id_sale' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]])->data;

        $nav_tabs = Self::navTabs($itemId);
        $content_header = (object)[
            'title' => "Vendas",
            'subtitle' => "Despesas",
            'buttons' => []
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->route . '/expenses.php';
        require APP . 'view/_templates/footer.php';
    }
}
