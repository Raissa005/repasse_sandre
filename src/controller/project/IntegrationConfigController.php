<?php

namespace RR\controller\project;

use PDOException;
use RR\libs\BoxAlert;
use RR\libs\Pagination;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\GerenciaPost;
use RR\model\ImmovableResource;
use RR\model\Integrations;
use RR\model\ModelGenerico;


class IntegrationConfigController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->dir = 'integration-config';
        $this->route = 'integration-config';
        $this->table = 'integrations';
        $this->model = new Integrations();

        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
    }

    public function index()
    {
        $contentHeader = (object)[
            'title' => "Integrações",
            'caption' => 'Confgurações',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                ]
            ]
        ];

        $rows = 20;
        $page = Pagination::getPage();

        $integrations = $this->model->getAndFilterAllItem($rows, $_GET, $page); 

        $pagination = (new Pagination())->pages($integrations->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($integrations->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->route . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->route . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleAddSubmitIntegration()
    {
        $response = $this->model->submitAddIntegration($_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error ? 'error' : 'success',
            'title' => $response->message
        ];

        header('location:' . URL . $this->route);
    }

    public function editItem($itemId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/config-integration/edit.js");

        $integration = $this->model->getItemById($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->route . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditIttem($itemId)
    {
        Secure::check_post_method($this->route . "/editPropertyCategory/$itemId");

        $arrPost = [
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'token' => $_POST['token'],
        ];

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $itemId, false);

            header('location:' . URL . $this->route . "/editPropertyCategory/$itemId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editPropertyCategory/$itemId?edited=false");
            exit;
        }
    }

    public function disableItem($itemId, $page)
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