<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\BoxAlert;
use RR\libs\Secure;
use RR\model\PropertyClassification;
use PDOException;
use RR\libs\Pagination;

class PropertyClassificationController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'property-classification';
        $this->dir = 'property-classification';
        $this->model = new PropertyClassification();
        $this->table = 'property_classification';
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
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
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem?error=error");

        $arrPost = [
            'name' => $_POST['name'],
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $itemId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            header('location:' . URL . $this->route . "/editItem/" . $itemId . "?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/addItem?added=false');
            exit;
        }
    }

    public function editItem($itemId)
    {
        $classificationModel = new PropertyClassification();

        $item = $classificationModel->getItemById8161($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId?error=error");

        $arrayPost = array(
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'updated_by' => $_SESSION['RR']->user->id,
        );

        try {
            (new GerenciaPost())->update8191($arrayPost, $this->table, 'id', $itemId, false);

            header('location:' . URL . $this->route . "/editItem/$itemId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId?edited=false");
            exit;
        }
    }

    public function disableItem($itemId, $page = "1")
    {
        $this->model->disableItem($itemId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $this->model->enableItem($itemId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
