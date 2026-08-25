<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\Banks;
use PDOException;
use RR\libs\Pagination;
use RR\libs\Util;

class BanksController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'banks';
        $this->dir = 'banks';
        $this->model = new Banks();
        $this->table = 'banks';
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

        $banks = $this->model->getAndFilterAllBanks($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($banks->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($banks->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addBanks()
    {
        Secure::access_admin(true);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addBanks.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddBanks()
    {
        Secure::check_post_method($this->route . "/addBanks");

        $arrPost = array(
            'bank_code' => $_POST['bank_code'],
            'name' => $_POST['name'],
            'finance_bank' => $_POST['finance_bank'],
            'created_by' => $_SESSION['RR']->user->id
        );

        try {
            $bankId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            header('location:' . URL . $this->route . "/editBanks/$bankId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addBanks?added=false");
            exit;
        }
    }

    public function editBanks($bankId)
    {
        Secure::access_admin(true);

        $bank = (new Banks())->getBanksById($bankId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editBanks.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditBanks($bankId)
    {
        Secure::check_post_method($this->route . "/editBanks/$bankId");

        $arrPost = array(
            'bank_code' => $_POST['bank_code'],
            'name' => $_POST['name'],
            'finance_bank' => $_POST['finance_bank'],
            'status' => $_POST['status'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $bankId, false);

            header('location:' . URL . $this->route . "/editBanks/$bankId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editBanks/$bankId?edited=false");
            exit;
        }
    }

    public function disableBanks($bankId, $page)
    {
        $ModelGenerico =  new ModelGenerico();

        $ModelGenerico->disableItem($bankId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableBanks($bankId, $page)
    {
        $ModelGenerico =  new ModelGenerico();

        $ModelGenerico->enableItem($bankId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
