<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\libs\BoxAlert;
use RR\libs\Secure;
use RR\model\PresentationCard;
use PDOException;

use function RR\Controller\redirect;

class PresentationCardController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $itemName;
    public $alert;

    public function __construct()
    {
        $this->route = 'presentation-card';
        $this->dir = 'presentation-card';
        $this->model = new PresentationCard();
        $this->table = 'presentation_card';
        parent::__construct($this->route);

        $this->itemName = "Cartão Apresentação";
        $this->addScript(URL . "js/" . JSVERSION . "/lead.js");
        $this->alert = (new BoxAlert());
    }

    public function index()
    {
        Secure::access_admin(true);
        header('location:' . URL . $this->route . '/editItem/1');
        exit;
        $modelGenerico = new ModelGenerico();
        $presentationCardModel = new PresentationCard();

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $pagination = $modelGenerico->pagination();
        $nextPagination = $presentationCardModel->getAndFilterAllItem(20, $_GET, $pagination + 1);
        $items = $presentationCardModel->getAndFilterAllItem(20, $_GET, $pagination);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        Secure::access_admin(true);
        $modelGenerico = new ModelGenerico();

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route . "/addItem?error=error");

        $arrPost = array(
            'name' => $_POST['name'],
            'status' => $_POST['status'],
            'created_by' => $_SESSION['RR']->user->id,
        );

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
        $modelGenerico = new ModelGenerico();
        $presentationCardModel = new PresentationCard();

        $item = $presentationCardModel->getItemById8161($itemId);

        require APP . 'view/_templates/header.php';
        // require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $response = $this->model->submitEditForm($itemId);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . "/editItem/$itemId");
    }

    public function disableItem($itemId, $page = "1")
    {
        Secure::access_admin(true);

        $modelGenerico =  new ModelGenerico();
        $modelGenerico->disableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        Secure::access_admin(true);

        $modelGenerico =  new ModelGenerico();
        $modelGenerico->enableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
