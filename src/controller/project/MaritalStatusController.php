<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Toast;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\MaritalStatus;
use PDOException;

class MaritalStatusController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'marital-status';
        $this->dir = 'marital-status';
        $this->model = new MaritalStatus();
        $this->table = 'marital_status';
        parent::__construct($this->route);
    }

    public function index()
    {
        Secure::access_admin(true);

        $maritalStatusModel = new MaritalStatus();

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $pagination = (new ModelGenerico())->pagination();
        $nextPagination = $maritalStatusModel->getAndFilterAllMaritalStatus(20, $_GET, $pagination + 1);

        $maritalStatus = $maritalStatusModel->getAndFilterAllMaritalStatus(20, $_GET, $pagination);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addMaritalStatus()
    {
        Secure::access_admin(true);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addMaritalStatus.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddMaritalStatus()
    {
        Secure::check_post_method($this->route . "/addMaritalStatus");

        try {
            $branchId = (new GerenciaPost())->insert7181($_POST, $this->table, true);

            Toast::itemAdded();
            header('location:' . URL . $this->route . "/editMaritalStatus/$branchId");
            exit;
        } catch (PDOException $error) {
            Toast::itemAddError();
            header('location:' . URL . $this->route . "/addMaritalStatus");
            exit;
        }
    }

    public function editMaritalStatus($maritalStatusId)
    {
        Secure::access_admin(true);
        
        $maritalStatusModel = new MaritalStatus();
        $maritalStatusId = $maritalStatusId;

        $maritalStatus = $maritalStatusModel->getMaritalStatusById($maritalStatusId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editMaritalStatus.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditMaritalStatus($maritalStatusId)
    {
        Secure::check_post_method($this->route . "/editMaritalStatus/$maritalStatusId");

        try {
            (new GerenciaPost())->update8191($_POST, $this->table, 'id', $maritalStatusId);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route . "/editMaritalStatus/$maritalStatusId");
        exit;
    }

    public function disableMaritalStatus($maritalStatusId, $page)
    {
        try {
            $success = (new ModelGenerico())->disableItem($maritalStatusId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableMaritalStatus($maritalStatusId, $page)
    {
        try {
            $success = (new ModelGenerico())->enableItem($maritalStatusId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
