<?php

namespace RR\controller\project;

use RR\core\Model;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\Branch;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\StandardContract;
use RR\model\TypeContract;
use PDOException;
use RR\libs\Pagination;

use function RR\Controller\redirect;

class StandardContractController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'standard-contract';
        $this->dir = 'standard-contract';
        $this->model = new StandardContract();
        $this->table = 'standard_contract';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $branches = (new Branch())->getAllBranch();

        $standardContract = $this->model->getAndFilterAllStandardContract($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($standardContract->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($standardContract->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addStandardContract()
    {
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $typeContract = (new TypeContract())->getAllTypeContract();
        $branches = (new Branch())->getAllBranch();

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addStandardContract.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleFormAdd()
    {
        Secure::check_post_method($this->route . "/addStandardContract");

        $response = $this->model->submitFormAdd($_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . '/edit-item/' . $response->lastId);
    }

    public function editItem($standardContractId)
    {
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $standardContractModel = new StandardContract();

        $standardContract = $standardContractModel->getStandardContractById($standardContractId);
        $typeContract = (new TypeContract())->getAllTypeContract();
        
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editStandardContract.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($standardContractId)
    {
        Secure::check_post_method($this->route . "/editStandardContract/$standardContractId");

        $response = $this->model->submitEditForm($standardContractId, $_POST);

        $_SESSION['RR']->toast = (object) [
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . "/edit-item/$standardContractId");
    }

    public function disableStandardContract($standardContractId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem($standardContractId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableStandardContract($standardContractId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem($standardContractId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
