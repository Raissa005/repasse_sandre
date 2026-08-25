<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\Professions;
use PDOException;
use RR\libs\Pagination;

class ProfessionsController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'professions';
        $this->dir = 'professions';
        $this->model = new Professions();
        $this->table = 'professions';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $professions = $this->model->getAndFilterAllProfessions($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($professions->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($professions->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addProfessions()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addProfessions.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddProfessions()
    {
        Secure::check_post_method($this->route . "/addProfessions");

        $arrPost = [
            'name' => $_POST['name'],
            'created_by' => $_SESSION['RR']->user->id
        ];

        try {
            $professionId = (new GerenciaPost())->insert7181($arrPost, $this->table, true);

            header('location:' . URL . $this->route . "/editProfessions/$professionId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addProfessions?added=false");
            exit;
        }
    }

    public function editProfessions($professionsId)
    {
        $professionsModel = new Professions();
        $this->addScript(URL . "js/" . JSVERSION . "/professions.js");

        $professions = $professionsModel->getProfessionsById($professionsId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editProfessions.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditProfessions($professionsId)
    {
        Secure::check_post_method("/editProfessions/$professionsId");

        $arrPost = array(
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $professionsId);

            header('location:' . URL . $this->route . "/editProfessions/$professionsId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editProfessions/$professionsId?edited=false");
            exit;
        }
    }

    public function disableProfessions($professionsId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem($professionsId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableProfessions($professionsId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem($professionsId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
