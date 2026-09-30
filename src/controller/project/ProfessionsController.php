<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Toast;
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

            Toast::itemAdded();
            header('location:' . URL . $this->route . "/editProfessions/$professionId");
            exit;
        } catch (PDOException $error) {
            Toast::itemAddError();
            header('location:' . URL . $this->route . "/addProfessions");
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

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route . "/editProfessions/$professionsId");
        exit;
    }

    public function disableProfessions($professionsId, $page)
    {
        try {
            $success = (new ModelGenerico())->disableItem($professionsId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableProfessions($professionsId, $page)
    {
        try {
            $success = (new ModelGenerico())->enableItem($professionsId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
