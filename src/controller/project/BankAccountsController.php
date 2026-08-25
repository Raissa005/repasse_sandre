<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\BankAccounts;
use RR\model\Banks;
use PDOException;
use RR\libs\Pagination;

class BankAccountsController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'bank-accounts';
        $this->dir = 'bank-accounts';
        $this->model = new BankAccounts();
        $this->table = 'bank_accounts';
        parent::__construct($this->route);
    }

    public function index()
    {
        Secure::access_admin(true);

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $bankAccounts = $this->model->getAndFilterAllBankAccounts($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($bankAccounts->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($bankAccounts->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addBankAccounts()
    {
        Secure::access_admin(true);

        $bankAccount = new BankAccounts();
        $banks = (new Banks())->getAllBanks();

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addBankAccounts.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddBankAccounts()
    {
        Secure::check_post_method($this->route . "/addBankAccounts");

        $arrPost = [
            'id_bank' => $_POST['id_bank'],
            'agency' => preg_replace('/\D/', "", $_POST['agency']),
            'account_number' => preg_replace('/\D/', "", $_POST['account_number']),
            'created_by' => $_SESSION['RR']->user->id
        ];

        try {
            $bankAccountId = (new GerenciaPost())->insert7181($arrPost, $this->table, true);

            header('location:' . URL . $this->route . "/editBankAccounts/$bankAccountId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/addBankAccounts?added=false');
            exit;
        }
    }

    public function editBankAccounts($bankAccountId)
    {
        Secure::access_admin(true);

        $bankAccountModel = new BankAccounts();

        $banks = (new Banks())->getAllBanks();
        $bankAccount = $bankAccountModel->getBankAccountsById($bankAccountId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editBankAccounts.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditBankAccounts($bankAccountId)
    {
        Secure::check_post_method($this->route . "/editBankAccounts/$bankAccountId");

        $arrPost = array(
            'id_bank' => $_POST['id_bank'],
            'agency' => preg_replace('/\D/', "", $_POST['agency']),
            'account_number' => preg_replace('/\D/', "", $_POST['account_number']),
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $bankAccountId);

            header('location:' . URL . $this->route . "/editBankAccounts/$bankAccountId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editBankAccounts/$bankAccountId?edited=false");
            exit;
        }
    }

    public function disableBankAccounts($bankAccountId, $page)
    {
        $this->model->disableItem($bankAccountId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableBankAccounts($bankAccountId, $page)
    {
        $this->model->enableItem($bankAccountId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
