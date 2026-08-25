<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\Status;
use PDOException;
use RR\libs\Pagination;

class StatusController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'status';
        $this->dir = 'status';
        $this->model = new Status();
        $this->table = 'status';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $status = $this->model->getAndFilterAllStatus($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($status->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($status->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addStatus()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addStatus.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddStatus()
    {
        Secure::check_post_method($this->route . "/addStatus");

        $arrPost = array('name' => $_POST['name'], 'created_by' => $_SESSION['RR']->user->id);

        try {
            $statusId = (new GerenciaPost())->insert7181($arrPost, $this->table, true);

            header('location:' . URL . $this->route . "/editStatus/$statusId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addStatus?added=false");
            exit;
        }
    }

    public function editStatus($statusId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/status.js");
        $statusModel = new Status();

        $status = $statusModel->getStatusById($statusId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editStatus.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditStatus($statusId)
    {
        Secure::check_post_method($this->route . "/editStatus/$statusId");

        $arrPost = [
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        ];

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $statusId);

            header('location:' . URL . $this->route . "/editStatus/$statusId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editStatus/$statusId?edited=false");
            exit;
        }
    }

    public function disableStatus($statusId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem($statusId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableStatus($statusId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem($statusId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
