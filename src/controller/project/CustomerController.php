<?php

namespace RR\controller\project;

use PDOException;
use RR\libs\Util;
use Dompdf\Dompdf;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\FileUploader;
use RR\libs\Pagination;
use RR\libs\Toast;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\Customer;
use RR\model\CustomerType;
use RR\model\MaritalStatus;
use RR\model\Professions;
use RR\model\Contract;
use RR\model\FormOfPayment;
use RR\model\User;
use RR\model\BillsToPay;
use RR\model\BillsToPayInstallment;
use RR\model\Branch;
use RR\model\Cities;
use RR\model\ClientTypeResourceTypes;
use RR\model\CustomerAttachment;
use RR\model\CustomerBranch;
use RR\model\CustomerBalanceLog;
use RR\model\Sales;
use RR\model\States;

use function RR\Controller\redirect;

class CustomerController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $siteConfig;
    public $title;

    public function __construct()
    {
        $this->route = 'customer';
        $this->dir = 'customer';
        $this->model = new Customer();
        $this->table = 'customer';
        parent::__construct($this->route);

        $this->title = "Clientes";
        $this->siteConfig = (new ModelGenerico())->getItemById8161(1, "configuracao");
        $this->title = 'Clientes';
    }

    private function menuEdit()
    {
        $customerType = (new CustomerType())->getAllCustomerType();

        if (isset($_GET['customer_type_in'])) {
            switch ($_GET['customer_type_in']) {
                case ['1']:
                    $this->page->id = 19;
                    break;
                case ['2']:
                    $this->page->id = 200;
                    break;
                case ['3']:
                    $this->page->id = 201;
                    break;
            }
        } else {
            $this->page->id = 64;
        }

        return $this->page->id;
    }

    private function navTabs($customerId)
    {
        $customer = $this->model->getItemById($customerId);
        $credit = $this->model->calculateCustomerCredit($customerId);
        $menuSpouse = (new ModelGenerico())->getItemByGenericField($customerId, "spouse_customer", "id_spouse");
        $typeCustomer = (new ModelGenerico())->getItemByGenericField($customerId, "client_type_resource_types", "id_customer");
        $typeSelected = array_map(function ($type) {
            return $type->id_customer_type;
        }, $typeCustomer);
        $menuBillToPay = in_array(2, $typeSelected);

        $navTabs = [
            (object)['text' => 'Dados Gerais', 'route' => URL . $this->route . '/edit-item/' . $customerId, 'class' => $_GET['pg1'] === 'edit-item' ? 'active' : '']
        ];

        if ($menuSpouse) {
            array_push($navTabs, (object)[
                'text' => 'Cônjuge',
                'route' => URL . $this->route . '/spouse/' . $customerId,
                'class' => $_GET['pg1'] === 'spouse' ? 'active' : ''
            ]);
        }
        if (Secure::creator($customer->created_by) || Secure::access_secretary()) {
            if ($menuBillToPay) {
                array_push($navTabs, (object)['text' => 'Contas a Pagar', 'route' => URL . $this->route . '/billToPay/' . $customerId, 'class' => $_GET['pg1'] === 'billToPay' ? 'active' : '']);
            }
            array_push($navTabs, (object)['text' => 'Anexos', 'route' => URL . $this->route . '/attachment/' . $customerId, 'class' => $_GET['pg1'] === 'attachment' ? 'active' : '']);
            /**Aba "Vendas" desativada a pedido do usuário (2026-10-01, M1) — lia a tabela `sales` do domínio imobiliário, sempre vazia. Código/rota mantidos, só tirada da navegação. */
            // array_push($navTabs, (object)['text' => Util::maskMoney($credit), 'class' => 'text-bold pull-right ', 'a_class' => ($credit >= 0 ? 'text-blue' : 'text-red')]);
        }

        return $navTabs;
    }

    public function index()
    {

        $this->addScript(URL . "js/" . JSVERSION . "/customer/filters.js");
        $this->addScript(URL . "js/" . JSVERSION . "/customer/exportCustomers.js");
        $customerModel = new Customer();

        $branch = (new Branch())->getItemById8161($_SESSION['RR']->branch->current->id);
        $customerType = (new CustomerType())->getAllCustomerType();

        $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;

        $this->page->id = Self::menuEdit();

        if (!Secure::access_secretary() && $branch->restrict_owner_data == 1) {
            $_GET['created_by'] = $_SESSION['RR']->user->id;
        }

        if (!Secure::seller_manager()) {
            $_GET['id_seller_manager'] = $_SESSION['RR']->user->id;
        }

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $users =  (new User())->getAndFilterAllUsers(0,
            [
                "id_branch_and_profile" =>
                    $_SESSION['RR']->branch->current->id,
                "status" =>
                    true,
                "order" =>
                    " up.access ASC, u.name ASC"
            ],
        0)->data;

        if (!isset($_GET['order_by'])) {
            $_GET['order_by'] = 3;
        }

        switch ($_GET['order_by']) {
            case '1':
                $_GET['order'] = "cust.id ASC";
                break;
            case '2':
                $_GET['order'] = "cust.id DESC";
                break;
            case '3':
                $_GET['order'] = "COALESCE(cust.fancy_name_company, cust.company_name, cust.name) ASC";
                break;
            case '4':
                $_GET['order'] = "COALESCE(cust.fancy_name_company, cust.company_name, cust.name) DESC";
                break;
            case '5':
                $_GET['order'] = "cust.created_at ASC";
                break;
            case '6':
                $_GET['order'] = "cust.created_at DESC";
                break;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $items = $this->model->getAndFilterAllCustomer($rows, $_GET, $page);

        $pagination = (new Pagination())->pages($this->model->getCountWithFiltersForItems($_GET), $rows);
        $showItems = (new Pagination())->listItemsOnPage($this->model->getCountWithFiltersForItems($_GET), $pagination, $rows);

        $states = (new States)->getWithFiltersAllItems()->data;
        $cities = (new Cities)->getWithFiltersAllItems()->data;

        array_map(function ($customer) {
            $customer->name = !empty($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name;
            $customer->phone = !empty($customer->cellphone) ? $customer->cellphone : (!empty($customer->phone) ? $customer->phone : 'Não informado');
            $customer->phone = Util::maskTelefone($customer->phone);
            $customer->city_name = ucwords(mb_strtolower($customer->city_name), " ");
            $customer->locate = $customer->city_name . ' - ' . $customer->uf_state;

            $customer->action = [];

            array_push($customer->action, (object)['class' => in_array($customer->id_person_type, [1, 2]) && Secure::access_dev() ? 'disabled' : '', 'id' => $customer->id, 'icon' => 'fas fa-pencil-alt', 'href' => URL . "{$this->route}/edit-item/{$customer->id}", 'title' => 'Editar', 'size' => 'sm', 'color' => 'primary',]);

            array_push($customer->action, (object)['id' => $customer->id, 'icon' => 'fa ' . ($customer->status ? 'fa-times' : 'fa-check'), 'size' => 'sm', 'color' => ($customer->status ? 'danger' : 'success'), 'class' => 'btn-' . ($customer->status ? 'disable' : 'enable') . '-item' . in_array($customer->id_person_type, [1, 2]) && Secure::access_dev() ? 'disabled' : '', 'attr' => (object)['sendTo' => $this->route . ($customer->status ? '/disableItem' : '/enableItem') . "/"]]);

            $customer->logo = (object)[
                'url' => URL . "img/customer/" . ($customer->logo ? "{$customer->id}/logo-{$customer->logo_cont}.{$customer->logo_ext}" : "default/default.png"),
                'style' => "width: 50px; max-width: 150px; max-height: 150px;",
                'class' => "img-responsive img-circle"
            ];
            $customer->status = (object)['value' => ($customer->status ? 'Ativo' : 'inativo'), 'color' => ($customer->status ? 'success' : 'danger')];
        }, $items);

        $table = (object) [
            'config' => (object) ['responsive' => true, 'condensed' => true, 'bordered' => true, 'striped' => true],
            'thead' => [
                (object)['tdStyle' => 'vertical-align: middle;', 'style' => 'width: 60px', 'class' => 'text-center', 'text' => 'Cód.', 'column' => (object)['type' => 'text', 'link' => 'id']],
                (object)['tdStyle' => 'vertical-align: middle;', 'text' => 'Nome', 'column' => (object)['type' => 'text', 'link' => 'name']],
                (object)['tdStyle' => 'vertical-align: middle;', 'class' => 'text-center', 'text' => 'Celular', 'column' => (object)['type' => 'text', 'link' => 'phone']],
                (object)['tdStyle' => 'vertical-align: middle;', 'class' => 'text-center', 'text' => 'Localidade', 'column' => (object)['type' => 'text', 'link' => 'locate']],
                (object)['tdStyle' => 'vertical-align: middle;', 'class' => 'text-center', 'text' => 'Status', 'column' => (object)['type' => 'label', 'link' => 'status']],
                (object)['tdStyle' => 'vertical-align: middle;', 'style' => 'width: 180px', 'class' => 'text-center', 'text' => 'Ações', 'column' => (object)['type' => 'button', 'link' => 'action']],
            ],
            'data' => $items
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->addScript(URL . "js/" . JSVERSION . "/cnpj.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/customer.js");

        $modelGenerico = new ModelGenerico();
        $branchModel = new Branch();
        $professionsModel = new Professions();
        $maritalStatusModel = new MaritalStatus();
        $customerTypeModel = new CustomerType();

        $branch = $branchModel->getItemById8161($_SESSION['RR']->branch->current->id);
        $city = $modelGenerico->getItemById8161($branch->id_city, "cities");
        $state = (new Contract)->getStatesByUF($city->uf);

        $persons = $modelGenerico->getAllItens("person_type");
        $countries = $modelGenerico->getAllItens("countries");
        $states = $modelGenerico->getAllItens("states");
        $cities = $branchModel->getCitiesByState($city->uf);
        $branches = $branchModel->getAllBranch();
        $professions = $professionsModel->getAllProfessions();
        $maritalStatus = $maritalStatusModel->getAllMaritalStatus();
        $customerTypes = $customerTypeModel->getAllCustomerType();

        $requiredField = $modelGenerico->getItemById8161(1, 'customer_required_field');

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }



    public function handleFormAdd()
    {
        Secure::check_post_method($this->route . "/addItem");

        $response = $this->model->submitFormAdd($_POST);

        Toast::checkResponse($response->error, $response->message);

        redirect($this->route . '/edit-item/' . $response->lastId);
    }

    public function editItem(int $customerId)
    {
        parent::addScript(URL . "js/" . JSVERSION . "/state.js");
        parent::addScript(URL . "js/" . JSVERSION . "/cnpj.js");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/customer.js");
        $modelGenerico = new ModelGenerico();
        $branchModel = new Branch();

        $customer = $this->model->getItemById($customerId);

        if (!$customer) {
            Toast::errorToast('Cliente não encontrado');
            redirect($this->route);
        }

        $credit = $this->model->calculateCustomerCredit($customerId);

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$customer->id}",
            'title' => isset($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name,
            'caption' => 'Dados Gerais',
            'buttons' => []
        ];

        $navTabs = Self::navTabs($customerId);

        /**Segurança do cadastro do cliente */
        if (!in_array($_SESSION['RR']->branch->current->id, array_column((new CustomerBranch)->getWithFiltersAllItems([(object)['columns' => ['id_customer' => (object)['value' => $customerId]]]])->data, 'id_branch'))) {
            redirect($this->route);
        }

        $this->page->id = $this->menuEdit($customerId);
        $customer->branches = (new CustomerBranch)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id_customer' => (object)['comparison' => '=', 'value' => $customerId]]]
            ],
            [
                (object)['table' => 'branch', 'columns' => ['id' => ['branch_id'], 'name' => ['branch_name']]]
            ]
        )->data;

        $customerTypes = (new CustomerType())->getAllCustomerType();
        $professions = (new Professions())->getAllProfessions();
        $maritalStatus = (new MaritalStatus())->getAllMaritalStatus();
        $branch = $branchModel->getItemById8161($customer->id_branch);
        $branches = $branchModel->getAllBranch();
        $cities = $branchModel->getCitiesByState($customer->uf_state);
        $states = $modelGenerico->getAllItens("states");
        $countries = $modelGenerico->getAllItens("countries");
        $persons = $modelGenerico->getAllItens("person_type");

        /**Trocar Usuário */
        if (Secure::access_secretary()) {
            $users = (new User())->getAndFilterAllUsers(0, ['status' => true, 'id_branch_and_profile' => $customer->id_branch, 'order' => "up.id ASC"], 0)->data;
        }

        /**Tipo Cliente */
        $typeCustomer = $modelGenerico->getItemByGenericField($customerId, "client_type_resource_types", "id_customer");
        $typeSelected = array_map(function ($type) {
            return $type->id_customer_type;
        }, $typeCustomer);

        /**Informações do cadastro */
        $userCreated = $modelGenerico->getItemById8161($customer->created_by, "users");
        $userUpdated = $modelGenerico->getItemById8161($customer->updated_by, "users");

        /**Cliente foi travado ou o usuário não tem permissão de edição */
        $permission = Secure::access_secretary() || (!$customer->blocked && Secure::creator($customer->created_by));
        /**Os dados de contato do cliente ficarão restritos, Configuração definida por filial */
        $restricted = !Secure::access_secretary() && !Secure::creator($customer->created_by) && $branch->restrict_owner_data;

        $attrInputs = $permission && !$restricted ? "" : "disabled";
        $attrInputsRequired = $permission && !$restricted ? "required" : "disabled";
        $textLabelRequired = $permission && !$restricted ? "*" : "";

        $requiredField = $modelGenerico->getItemById8161(1, 'customer_required_field');

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($customerId)
    {
        Secure::check_post_method($this->route . "edit-item/$customerId");

        $customer = (new Customer())->getItemById($customerId);
        Secure::redirectFunction(!Secure::access_secretary() && (!$customer->created_by || $customer->blocked == 1), $this->route . "/editItem/" . $customerId, "authorization=false");

        $response = $this->model->submitEditForm($customerId, $_POST);

        Toast::checkResponse($response->error, $response->message);

        redirect($this->route . "/edit-item/$customerId");
    }

    public function spouse($customerId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->addScript(URL . "js/" . JSVERSION .  "/" . $this->dir . "/spouse.js");

        $customerModel = new Customer();
        $professionsModel = new Professions();
        $modelGenerico = new ModelGenerico();
        $branchModel = new Branch();

        $customer = $customerModel->getCustomerById($customerId);
        /**Segurança do cadastro do cliente */
        if (!in_array($_SESSION['RR']->branch->current->id, array_column((new CustomerBranch)->getWithFiltersAllItems([(object)['columns' => ['id_customer' => (object)['value' => $customerId]]]])->data, 'id_branch'))) {
            redirect($this->route);
        }

        $branch = $branchModel->getItemById8161($customer->id_branch);
        $city = $modelGenerico->getItemById8161($branch->id_city, "cities");

        $spouse  = $customerModel->getSpouseByCustomerId($customerId);
        $professions = $professionsModel->getAllProfessions();
        $cities = $branchModel->getCitiesByState(!empty($spouse->uf_state) ? $spouse->uf_state : $city->uf);
        $states = $modelGenerico->getAllItens("states");
        $countries = $modelGenerico->getAllItens("countries");

        /**Tipo Cliente */
        $typeCustomer = $modelGenerico->getItemByGenericField($customerId, "client_type_resource_types", "id_customer");
        $typeSelected = array_map(function ($type) {
            return $type->id_customer_type;
        }, $typeCustomer);

        /**Informações do cadastro */
        $userCreated = $modelGenerico->getItemById8161($customer->created_by, "users");
        $userUpdated = $modelGenerico->getItemById8161($customer->updated_by, "users");

        /**Cliente foi travado ou o usuário não tem permissão de edição */
        $permission = Secure::access_secretary() || (!$customer->blocked && Secure::creator($customer->created_by));
        /**Os dados de contato do cliente ficarão restritos, Configuração definida por filial */
        $restricted = !Secure::access_secretary() && !Secure::creator($customer->created_by) && $branch->restrict_owner_data;

        $attrInputs = $permission && !$restricted ? "" : "disabled";
        $attrInputsRequired = $permission && !$restricted ? "required" : "disabled";
        $textLabelRequired = $permission && !$restricted ? "*" : "";

        $requiredField = $modelGenerico->getItemById8161(2, 'customer_required_field');

        /**Menu */
        $menuSpouse = 1;
        $menuBillToPay = in_array(2, $typeSelected);
        $credit = $this->model->calculateCustomerCredit($customerId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/spouse.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitSpouse($customerId)
    {
        Secure::check_post_method($this->route . "/spouse/$customerId");

        $customer = (new Customer)->getCustomerById($customerId);
        Secure::redirectFunction(!Secure::access_secretary() && (!$customer->created_by || $customer->blocked == 1), $this->route . "/spouse/" . $customerId, "authorization=false");

        $arrPost = array(
            'name' => $_POST['name'],
            'birth_date' => !empty(trim($_POST['birth_date'])) ? $_POST['birth_date'] : NULL,
            'id_profession' => $_POST['id_profession'],
            'nationality' => $_POST['nationality'],
            'rg' => Util::removeNonNumericCharacters($_POST['rg']),
            'person_registration' => Util::removeNonNumericCharacters($_POST['person_registration']),
            'cellphone' => Util::removeNonNumericCharacters($_POST['cellphone']),
            'phone' => Util::removeNonNumericCharacters($_POST['phone']),
            'created_by' => $_SESSION['RR']->user->id,
        );

        if (isset($_POST['checkAddress'])) {
            $arrPost['address_spouse'] = 1;
            $arrPost['cep'] = "";
            $arrPost['zip'] = "";
            $arrPost['id_country'] = "";
            $arrPost['uf_state'] = "";
            $arrPost['id_city'] = "";
            $arrPost['address'] = "";
            $arrPost['number_address'] = "";
            $arrPost['neighborhood'] = "";
            $arrPost['complement'] = "";
        } else {
            $arrPost['address_spouse'] = 0;
            $arrPost['cep'] = !empty($_POST['cep']) ? Util::removeNonNumericCharacters($_POST['cep']) : "";
            $arrPost['zip'] = !empty($_POST['zip']) ? $_POST['zip'] : "";
            $arrPost['id_country'] = !empty($_POST['id_country']) ? $_POST['id_country'] : "";
            $arrPost['uf_state'] = !empty($_POST['state']) ?  $_POST['state'] : "";
            $arrPost['id_city'] = !empty($_POST['id_city']) ? $_POST['id_city'] : "";
            $arrPost['address'] = $_POST['address'];
            $arrPost['number_address'] = $_POST['number_address'];
            $arrPost['neighborhood'] = $_POST['neighborhood'];
            $arrPost['complement'] = $_POST['complement'];
        }

        try {
            (new GerenciaPost())->update8191($arrPost, 'spouse_customer', 'id_spouse', $customerId, false, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route . "/spouse/$customerId");
        exit;
    }

    public function image($customerId)
    {
        $modelGenerico = new ModelGenerico();
        $this->page->id = Self::menuEdit();
        $customer = $this->model->getCustomerById($customerId);

        $menuSpouse = $modelGenerico->getItemByGenericField($customerId, "spouse_customer", "id_spouse");
        $typeCustomer = $modelGenerico->getItemByGenericField($customerId, "client_type_resource_types", "id_customer");
        $typeSelected = array_map(function ($type) {
            return $type->id_customer_type;
        }, $typeCustomer);
        $menuBillToPay = in_array(2, $typeSelected);
        $credit = $this->model->calculateCustomerCredit($customerId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/image.php';
        require APP . 'view/_templates/footer.php';
    }

    public function attachment($customerId)
    {
        $this->page->id = Self::menuEdit();
        $item = $this->model->getItemById($customerId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route, "authorization=false");
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by));
        $customer = $this->model->getCustomerById($customerId);

        $attachment_filters = [
            (object)[
                'columns' => [
                    'status' => (object)['value' => 1],
                    'id_customer' => (object)['value' => $customerId],
                ]
            ],
        ];

        if ($_SESSION['RR']->user->id != $customer->created_by) {
            array_push(
                $attachment_filters,
                (object)[
                    'table' => 'users_profiles',
                    "where" => "AND (this->table.access <= 10 OR customer_attachments.created_by = {$_SESSION['RR']->user->id})"
                ]
            );
        } else {
            array_push(
                $attachment_filters,
                (object)[
                    'columns' => [
                        'created_by' => (object)['value' => $_SESSION['RR']->user->id],
                    ]
                ]
            );
        }

        $attachment_columns = [
            (object)['columns' => ['id', 'id_customer', 'name', 'created_at', 'filename', 'extension']],
            (object)['table' => 'users', 'columns' => ['name' => ['users_name']]]
        ];

        $attachments = (new CustomerAttachment())->getWithFiltersAllItems($attachment_filters, $attachment_columns);

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
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$customer->id}",
            'title' => isset($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name,
            'caption' => 'Anexos',
            'buttons' => [
                (object)[
                    'icon' => "fas fa-paperclip",
                    'text' => 'Adicionar Anexos',
                    'color' => 'primary',
                    'class' => 'btn-generic-item',
                    'attr' => [
                        'sendTo' => "{$this->route}/handleSubmitAddAttachment/{$customer->id}",
                        'headerHtml' => "Adicionar Anexos",
                        'bodyHtml' => $form_add_attachment,
                        'footerHtml' => "Adicionar",
                        'btnFooter' => "btn-primary",
                    ]
                ]
            ]
        ];

        $navTabs = Self::navTabs($customerId);

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
                    'href' => URL . "attachments/customer/{$attachment->id_customer}/{$attachment->filename}.{$attachment->extension}",
                    'attr' => ['download' => "{$attachment->name}-{$attachment->filename}.{$attachment->extension}"]
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
        }, $attachments->data);

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => false,
                'bordered' => true,
                'striped' => true,
            ],
            'thead' => [
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
                    'class' => 'text-center',
                    'text' => 'Criado Por',
                    'column' => (object)['type' => 'text', 'link' => 'users_name'],
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
        require APP . 'view/' . $this->dir . '/attachments.php';
        require APP . 'view/_templates/footer.php';
    }

    public function balance($customerId)
    {
        $this->page->id = Self::menuEdit();
        $item = $this->model->getItemById($customerId);
        Secure::branch($item->id_branch, $this->route);
        Secure::redirectFunction(!Secure::access_secretary() && !Secure::creator($item->created_by), $this->route, "authorization=false");
        $permission = Secure::access_secretary() || (Secure::creator($item->created_by));
        $customer = $this->model->getCustomerById($customerId);

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/{$_GET['pg1']}/{$customer->id}",
            'title' => isset($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name,
            'caption' => 'Saldo',
            'buttons' => []
        ];

        $navTabs = Self::navTabs($customerId);

        $log = (new CustomerBalanceLog)->getWithFiltersAllItems(
            [(object)['columns' => ['id_customer' => (object)['value' => $customerId]]]],
            [
                (object)['columns' => ["*"]],
                (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
            ],
            ['orderBy' => 'customer_balance_log.created_at DESC']
        );

        $lastTime = 0;
        array_map(function ($log) use (&$lastTime) {
            $log->paid = $log->amount_paid > 0 ? true : false;
            $log->name = isset($log->customer_fancy_name_company) ? $log->customer_fancy_name_company : $log->customer_name;
            $log->bg = 'bg-gray';
            $log->icon = !$log->paid ? 'fa-hand-holding-usd' : 'fas fa-receipt';
            $log->iconBg = !$log->paid ? 'bg-green' : 'bg-red';
            $log->text_color = !$log->paid ? 'text-blue' : 'text-red';
            $log->hour = Date::hour($log->created_at);
            $log->day = Date::day($log->created_at);
            $log->month = (Date::month_abreviation($log->created_at));
            $log->year = Date::year($log->created_at);
            $log->created_at = Date::date($log->created_at);
            $log->lastTime = $lastTime;
            $log->id_sale = isset($log->id_sale) ? $log->id_sale : '';
            $lastTime = $log->created_at;

            $log->amount = $log->amount_paid ? $log->amount_paid : $log->amount_received;
            $log->amount = Util::maskMoney($log->amount);

            if ($log->text != "") {
                $log->text = $log->text;
            } else {
                switch ($log->type) {
                    case 1:
                        $log->type = 'Contas Receber';
                        $log->text = "Usuário recebeu {$log->amount} ao excluir todas as parcelas na <a href=\"" . URL . "sales/editItem/{$log->id_sale}" . "\">Venda de código {$log->id_sale}</a>";
                        break;

                    case 2:
                        $log->type = 'Contas Pagar';
                        $log->text = "Foi descontado {$log->amount} ao pagar as Contas Pagar no <a href=\"" . URL . "bills-to-pay/" . "\">Contas Pagar</a>";
                        break;

                    default:
                        $log->type = !$log->paid ? 'Recebeu' : 'Pagou';
                        $log->text = "Usuário {$log->type} {$log->amount}";
                        break;
                }
            }
        }, $log->data);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/balance.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddAttachment($customerId)
    {
        if ($sizeError = FileUploader::sizeLimitError($_FILES['file'] ?? null, FileUploader::MAX_SIZE_ATTACHMENT)) {
            Toast::warningToast($sizeError);
            redirect("{$this->route}/attachment/$customerId/");
        }

        if ($typeError = FileUploader::typeError($_FILES['file'] ?? null, FileUploader::ALLOWED_ATTACHMENT)) {
            Toast::warningToast($typeError);
            redirect("{$this->route}/attachment/$customerId/");
        }

        $attachments = FileUploader::uploadFiles(
            $_FILES['file'],
            array_fill(0, count($_FILES['file']) + 1, "attachments/customer/$customerId"),
            FileUploader::ALLOWED_ATTACHMENT
        );

        $hasError = false;
        foreach ($attachments as $attachment) {
            if ($attachment['error']) {
                $hasError = true;
                continue;
            }

            (new CustomerAttachment)->insert([
                "id_customer" => $customerId,
                "name" => $_POST["name"],
                "filename" => $attachment['filename'],
                "extension" => $attachment['extension'],
                "status" => 1,
                "created_by" => $_SESSION['RR']->user->id,
            ]);
        }

        if ($hasError) {
            Toast::errorToast('Não foi possível enviar um ou mais arquivos');
        }

        redirect("{$this->route}/attachment/$customerId/");
    }

    public function handleSubmitImage($customerId)
    {
        if ($sizeError = FileUploader::sizeLimitError($_FILES['logo'] ?? null, FileUploader::MAX_SIZE_IMAGE)) {
            Toast::warningToast($sizeError);
            redirect($this->route . "/image/$customerId");
        }

        if ($typeError = FileUploader::typeError($_FILES['logo'] ?? null, FileUploader::ALLOWED_IMAGE)) {
            Toast::warningToast($typeError);
            redirect($this->route . "/image/$customerId");
        }

        if (isset($_FILES)) {
            $gerenciaPost = new GerenciaPost();
            $item = $this->model->getCustomerById($customerId);

            try {

                if (!empty($_FILES['logo']['tmp_name'])) {

                    if (!file_exists("img/customer/$customerId/")) {
                        mkdir("img/customer/$customerId/", 0777, true);
                    }
                    $extension = FileUploader::allowedExtension($_FILES['logo']['name'], $_FILES['logo']['tmp_name'], FileUploader::ALLOWED_IMAGE);
                    $newCont = $item->logo_cont + 1;

                    $filename = $_FILES['logo']['tmp_name'];
                    $path = "img/customer/$customerId/";

                    if ($extension !== null) {
                        Util::resizeImageCrop($filename, 150, 150, $path . "logo-$newCont.$extension");

                        // Só aponta o banco para o logo novo depois que o arquivo existe
                        if (file_exists($path . "logo-$newCont.$extension")) {
                            @unlink("img/customer/$customerId/logo-$item->logo_cont.$item->logo_ext");
                            $this->model->update(["logo" => 1, "logo_cont" => $newCont, "logo_ext" => $extension], "id", $customerId);
                        }
                    }
                }

                Toast::itemEdited();
                header('location:' . URL . $this->route . "/image/$customerId");
                exit;
            } catch (PDOException $error) {
                Toast::itemEditError();
                header('location:' . URL . $this->route . "/image/$customerId");
                exit;
            }
        }
    }

    public function deleteLogo($customerId)
    {
        $item = $this->model->getCustomerById($customerId);
        $this->model->update(["logo" => 0], "id", $customerId);
        @unlink("img/customer/$customerId/logo-$item->logo_cont.$item->logo_ext");

        Toast::itemDeleted();
        header('location: ' . URL . $this->route . "/image/$customerId");
        exit;
    }

    public function billToPay($customerId)
    {
        $modelGenerico = new ModelGenerico();
        $customerModel = new Customer();
        $branchModel = new Branch();
        $billsToPayInstallment = new BillsToPayInstallment();

        $customer = $customerModel->getCustomerById($customerId);
        $branch = $branchModel->getItemById8161($customer->id_branch);

        /**Cliente foi travado ou o usuário não tem permissão de edição */
        $permission = Secure::access_secretary() || (!$customer->blocked && Secure::creator($customer->created_by));
        /**Os dados de contato do cliente ficarão restritos, Configuração definida por filial */
        $restricted = !Secure::access_secretary() && !Secure::creator($customer->created_by) && $branch->restrict_owner_data;

        $attrInputs = $permission && !$restricted ? "" : "disabled";
        $attrInputsRequired = $permission && !$restricted ? "required" : "disabled";
        $textLabelRequired = $permission && !$restricted ? "*" : "";

        /**Segurança do cadastro do cliente */
        if (!in_array($_SESSION['RR']->branch->current->id, array_column((new CustomerBranch)->getWithFiltersAllItems([(object)['columns' => ['id_customer' => (object)['value' => $customerId]]]])->data, 'id_branch'))) {
            redirect($this->route);
        }

        $paymentStatus = $modelGenerico->getItemByGenericFieldArray(['status' => 1], "payment_status");
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0);

        /**Filtros */
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['status_payment'])) {
            $_GET['status_payment'] = [1];
        }

        if (!isset($_GET['date']['start'])) {
            $_GET['date']['start'] = Date::year_month(date("Y-m-d"));
        }

        if (!isset($_GET['date']['end'])) {
            $_GET['date']['end'] = Date::year_month(date("Y-m-d"));
        }

        $_GET['id_customer'] = $customerId;
        $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
        $order = 'bills_to_pay_installments.status_payment, bills_to_pay_installments.due_date ASC ';

        $response = $billsToPayInstallment->getAndFilterAllItem($_GET, ['order' => $order]);

        array_map(function ($element) {
            $element->totalLaunchInstallments = (new BillsToPay)->getAmountOfInstallmentsOfRelease($element->id_bills_to_pay);
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
                        $item->label = 'Aguardando Pagamento';
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

        $filterForCards = array(
            'date' => [
                'start' => isset($_GET['date']['start']) ? $_GET['date']['start'] : "",
                'end' => isset($_GET['date']['end']) ? $_GET['date']['end'] : ""
            ],
            'id_branch' => $_SESSION['RR']->branch->current->id,
            'id_customer' => $customerId,
            'id_form_of_payment' => isset($_GET['id_form_of_payment']) ? $_GET['id_form_of_payment'] : "",
            'status' => isset($_GET['status']) ? $_GET['status'] : true,
        );

        $responseAll = $billsToPayInstallment->getAndFilterAllItem($filterForCards);

        $cards = array(
            'total' => 0,
            'paid' => 0,
            'open' => 0,
            'late' => 0,
        );

        foreach ($responseAll->data as $item) {
            if ($item->status_payment != 3 && $item->status_payment != 9) {
                $cards['total'] += $item->value_of_installments;
                if ($item->status_payment == 2) {
                    $cards['paid'] += $item->value_of_installments;
                } else if ($item->status_payment == 1) {
                    $cards['open'] += $item->value_of_installments;
                    if ($item->due_date < date("Y-m-d")) {
                        $cards['late'] += $item->value_of_installments;
                    }
                }
            }
        }

        /**Menu */
        $typeCustomer = $modelGenerico->getItemByGenericField($customerId, "client_type_resource_types", "id_customer");
        $typeSelected = array_map(function ($type) {
            return $type->id_customer_type;
        }, $typeCustomer);
        $menuSpouse = $modelGenerico->getItemByGenericField($customerId, "spouse_customer", "id_spouse");
        $menuBillToPay = 1;
        $credit = $this->model->calculateCustomerCredit($customerId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/billToPay.php';
        require APP . 'view/_templates/footer.php';
    }


    public function disableItem($customerId, $page)
    {
        $customer = (new Customer)->getCustomerById($customerId);
        Secure::redirectFunction(!Secure::access_secretary() && (!$customer->created_by || $customer->blocked == 1), $this->route, "authorization=false");

        try {
            $success = (new ModelGenerico())->disableItem($customerId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableItem($customerId, $page)
    {
        $customer = (new Customer)->getCustomerById($customerId);
        Secure::redirectFunction(!Secure::access_secretary() && (!$customer->created_by || $customer->blocked == 1), $this->route, "authorization=false");

        try {
            $success = (new ModelGenerico())->enableItem($customerId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function deleteAttachment($customerId)
    {
        $item = (new CustomerAttachment())->getItemById($customerId, [(object)['columns' => ['id_customer']]]);
        try {
            $response = (new CustomerAttachment())->update(['status' => '0'], 'id', $customerId);

            Toast::checkResponse($response->error, $response->message);

            redirect($this->route . '/attachment/' . $item->id_customer);
        } catch (PDOException $error) {
            Toast::genericError();
            redirect($this->route . '/attachment/' . $item->id_customer);
        }
    }

    /**
     * Adicionar filiais nos clientes
     * Após rodar em todos os cliente essa função deve ser removida
     */
    public function addBranches()
    {
        $branches = (new Branch)->getWithFiltersAllItems([
            (object)['columns' => ['status' => (object)['value' => 1]]]
        ])->data;
        foreach ((new Customer)->getWithFiltersAllItems()->data as $customer) {
            $types = (new ClientTypeResourceTypes)->getWithFiltersAllItems([
                (object)['columns' => ['id_customer' => (object)['value' => $customer->id]]]
            ])->data;
            if (in_array(10, array_column($types, 'id_customer_type')) || in_array(11, array_column($types, 'id_customer_type'))) {
                foreach ($branches as $branch) {
                    (new CustomerBranch)->insert(['id_customer' => $customer->id, 'id_branch' => $branch->id]);
                }
            } else {
                (new CustomerBranch)->insert(['id_customer' => $customer->id, 'id_branch' => $customer->id_branch]);
            }
        }

        redirect("{$this->route}");
    }

    /**
     * Adicionar saldo nos clientes
     */
    public function getBalanceFromCustomers($token = '')
    {
        if ($token === $this->token) {
            $customers = (new Customer)->getWithFiltersAllItems()->data;
            $balancesAdded = [];
            foreach ($customers as $customer) {
                $balance = $this->model->calculateCustomerCredit($customer->id);
                (new Customer)->update(['balance' => $balance ? $balance : '0'], 'id', $customer->id);
                $balancesAdded[] = (object)['id' => $customer->id, 'name' => $customer->name, 'balance' => $balance];
            }

            exit;
        }
    }

    public function exportCustomersAsCsv()
    {
        $arrayCsv = [];

        $customers = $this->model->getAndFilterAllCustomer(0, $_GET, 0);

        $arrayCsv = array(array("Nome", "RG", "Endereço", "Estado", "CNPJ"));
        $attachmentName = "clientes" . "-" . date("d-m-Y");

        foreach ($customers as $customer) {
            $arrayCsv[] = array($customer->name, $customer->rg, $customer->address, $customer->uf_state, $customer->cnpj);
        }

        $output = fopen("php://output", 'w');

        header("Content-Type:application/csv");
        header("Content-Disposition:attachment;filename={$attachmentName}.csv");


        foreach ($arrayCsv as $row) {
            fputcsv($output, $row, ',',);
        }

        fclose($output);
    }

    public function exportCustomersAsPDF()
    {
        $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
        $customers = $this->model->getAndFilterAllCustomer(0, $_GET, 0);

        array_map(function ($customer) {
            $customer->customer_name = $customer->company_name ?? $customer->name;
        }, $customers);

        $styleCustomer = 'font-family: Arial, Helvetica, sans-serif; border-collapse: collapse; width: 100%;';
        $styleCustomerTd = 'border: 1px solid #ddd; padding: 8px;';
        $styleCustomerTr = 'background-color: #f2f2f2;';
        $styleCustomerTh = 'padding-top: 12px; padding-bottom: 12px; text-align: left; background-color: #0b94a1; color: white;';

        $rows = '';
        foreach ($customers as $item) {
            $rows .= "<tr style=\"{$styleCustomerTr}\">
                        <td style=\"{$styleCustomerTd} vertical-align: middle; width: 50px;\">{$item->id}</td>
                        <td style=\"{$styleCustomerTd} vertical-align: middle;\">{$item->name}</td>
                        <td style=\"{$styleCustomerTd} vertical-align: middle;\">{$item->rg}</td>
                        <td style=\"{$styleCustomerTd} vertical-align: middle;\">{$item->address}</td>
                        <td style=\"{$styleCustomerTd} vertical-align: middle;\">{$item->uf_state}</td>
                        <td style=\"{$styleCustomerTd} vertical-align: middle;\">{$item->city_name}</td>
                        <td style=\"{$styleCustomerTd} vertical-align: middle;\">{$item->phone}</td>
                    </tr>";
        }

        $html = "<div id=\"content\">
                    <div>
                        <table style=\"{$styleCustomer}\">
                            <thead>
                                <tr style=\"{$styleCustomerTr}\">
                                    <th style=\"{$styleCustomerTd} {$styleCustomerTh}\" style=\"width: 40px;\">Cód</th>
                                    <th style=\"{$styleCustomerTd} {$styleCustomerTh}\">Nome</th>
                                    <th style=\"{$styleCustomerTd} {$styleCustomerTh}\">RG</th>
                                    <th style=\"{$styleCustomerTd} {$styleCustomerTh}\">Endereço</th>
                                    <th style=\"{$styleCustomerTd} {$styleCustomerTh}\">Estado</th>
                                    <th style=\"{$styleCustomerTd} {$styleCustomerTh}\">Cidade</th>
                                    <th style=\"{$styleCustomerTd} {$styleCustomerTh}\">Telefone</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$rows}
                            </tbody>
                        </table>
                    </div>
                </div>";

        echo $html;
        echo "<script>window.print();</script>";
        exit;
    }

    public function sales(int $customerId): void
    {
        $item = $this->model->getItemById($customerId);

        if (empty($item)) {
            Toast::errorToast("Este cliente não foi encontrado ou não existe!");
            redirect($this->route);
        }

        $nav_tabs = self::navTabs($customerId);
        $content_header = (object)[
            'title' => $item->name,
            'subtitle' => 'Vendas'
        ];

        $sales = (new Sales())->getSalesForCustomers($customerId);

        if (empty($sales->data)) {
            Toast::errorToast("Este cliente não possui nenhuma venda!");
            redirect($this->route);
        }

        array_map(function ($item) {
            $item->sale_date = Date::date($item->sale_date);
            $item->sale_value = Util::maskMoney($item->sale_value);
        }, $sales->data);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->route . '/sales.php';
        require APP . 'view/_templates/footer.php';
    }
}
