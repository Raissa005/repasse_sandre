<?php

namespace RR\controller\project;

use RR\libs\BoxAlert;
use RR\libs\Pagination;
use RR\libs\Secure;
use RR\model\ModelGenerico;
use RR\model\CustomerType;

use function RR\Controller\redirect;

class CustomerTypeController extends FrontController
{
    public $dir;
    public $alert;
    public $route;
    private $model;
    private $table;

    public function __construct()
    {
        $this->alert = (new BoxAlert);
        $this->route = 'customer-type';
        $this->dir = 'customer-type';
        $this->model = new CustomerType();
        $this->table = 'customer_type';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $customerTypes = $this->model->getAndFilterAllCustomerType($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($customerTypes->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($customerTypes->count, $pagination, $rows);

        $names = $this->model->getWithFiltersAllItems([], [(object)['columns' => ['name']]], ['groupBy' => 'customer_type.name', 'orderBy' => 'customer_type.id ASC'])->data;
        foreach ($names as $name_key => $name) {
            $names[$name_key] = $name->name;
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function add()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAdd()
    {
        Secure::check_post_method($this->route);

        $response = $this->model->addSubmitForm($_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error ? 'error' : 'success'),
            'title' => $response->message
        ];

        redirect($this->route . ($response->error ? '/add' : '/edit/' . $response->lastId));
    }

    public function edit($customerTypeId)
    {
        $customerType = $this->model->getWithFiltersAllItems(
            [(object)['columns' => ['id' => ['comparision' => 'EQUAL', 'value' => $customerTypeId]]]],
            [(object)['columns' => ['name', 'status', 'disableable']]]
        )->data[0];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEdit($customerTypeId)
    {
        Secure::check_post_method($this->route);

        $customerType = $this->model->getWithFiltersAllItems(
            [(object)['columns' => ['id' => ['comparision' => 'EQUAL', 'value' => $customerTypeId]]]],
            [(object)['columns' => ['disableable']]]
        )->data[0];

        if ($customerType->disableable == 0 && isset($_POST['status']) && $_POST['status'] == 0) {
            $_SESSION['RR']->toast = (object)[
                'icon' => 'error',
                'title' => 'Este item não pode ser desativado'
            ];

            redirect($this->route . "/edit/$customerTypeId");
        }

        $response = $this->model->editSubmitForm($customerTypeId, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error ? 'error' : 'success',
            'title' => $response->message
        ];

        redirect($this->route . "/edit/$customerTypeId");
    }

    public function disable($customerTypeId)
    {
        $customerType = $this->model->getWithFiltersAllItems(
            [(object)['columns' => ['id' => ['comparision' => 'EQUAL', 'value' => $customerTypeId]]]],
            [(object)['columns' => ['disableable']]]
        )->data[0];

        if ($customerType->disableable == 0) {
            $_SESSION['RR']->toast = (object)[
                "icon" => "error",
                "title" => "Este item não pode ser desativado."
            ];
        }

        if ($customerType->disableable == 1) {
            $this->model->disableItem($customerTypeId);

            $_SESSION['RR']->toast = (object)[
                "icon" => "success",
                "title" => "Item desativado com sucesso."
            ];
        }

        redirect($this->route);
    }

    public function enable($customerTypeId)
    {
        $this->model->enableItem($customerTypeId);

        $_SESSION['RR']->toast = (object)[
            "icon" => "success",
            "title" => "Item ativado com sucesso."
        ];

        redirect($this->route . "/?status=0");
    }
}
