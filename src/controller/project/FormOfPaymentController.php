<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Toast;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\FormOfPayment;
use PDOException;
use RR\libs\Pagination;

class FormOfPaymentController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $title;

    public function __construct()
    {
        $this->route = 'form-of-payment';
        $this->dir = 'form-of-payment';
        $this->model = new FormOfPayment();
        $this->table = 'form_of_payment';
        parent::__construct($this->route);

        $this->title = "Formas de Pagamento";
    }

    public function index()
    {
        Secure::access_admin(true);

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $items = $this->model->getAndFilterAllItem($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($items->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($items->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        Secure::access_admin(true);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = array(
            'name' => $_POST['name'],
            'form_payment_sale' => $_POST['form_payment_sale'],
            'form_payment_accounts_payable' => $_POST['form_payment_accounts_payable'],
            'created_by' => $_SESSION['RR']->user->id
        );

        try {
            $itemId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            Toast::itemAdded();
            header('location:' . URL . $this->route . "/editItem/$itemId");
            exit;
        } catch (PDOException $error) {
            Toast::itemAddError();
            header('location:' . URL . $this->route . "/addItem");
            exit;
        }
    }

    public function editItem($itemId)
    {
        Secure::access_admin(true);

        $item = (new FormOfPayment())->getItemById8161($itemId);
        $permissionLabel = $item->restricted && !Secure::access_dev() ? "" : "*";
        $permissionInput = $item->restricted && !Secure::access_dev() ? "disabled" : "required";

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $item = (new FormOfPayment())->getItemById8161($itemId);

        if ($item->restricted) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        $arrPost = array(
            'name' => $_POST['name'],
            'form_payment_sale' => $_POST['form_payment_sale'],
            'form_payment_accounts_payable' => $_POST['form_payment_accounts_payable'],
            'status' => $_POST['status'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $itemId, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route . "/editItem/$itemId");
        exit;
    }

    public function disableItem($itemId, $page)
    {
        $item = (new FormOfPayment())->getItemById8161($itemId);

        if ($item->restricted) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        try {
            $success = (new ModelGenerico())->disableItem($itemId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $item = (new FormOfPayment())->getItemById8161($itemId);

        if ($item->restricted) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        try {
            $success = (new ModelGenerico())->enableItem($itemId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
