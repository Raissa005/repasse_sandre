<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Secure;
use RR\libs\Toast;
use RR\model\NetworksSite;
use PDOException;
use RR\libs\Pagination;

class NetworksSiteController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'networks-site';
        $this->dir = 'networks-site';
        $this->model = new NetworksSite();
        $this->table = 'rede_social';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['ativo'])) {
            $_GET['ativo'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $networks = $this->model->getAndFilterAllNetworks($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($networks->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($networks->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addNetwork()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addNetwork.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddNetwork()
    {
        Secure::check_post_method($this->route . "/addNetwork");

        $arrPost = array(
            'nome' => $_POST['nome'],
            'ordem' => $_POST['ordem'],
            'icone' => $_POST['icone'],
            'link' => $_POST['link'],
            'cor' => $_POST['cor'],
            'cor_hover' => $_POST['cor_hover'],
            'cor_rodape' => $_POST['cor_rodape'],
            'cor_rodape_hover' => $_POST['cor_rodape_hover'],
            'ativo' => $_POST['ativo'],
        );

        try {
            $networksId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            Toast::itemAdded();
            header('location:' . URL . $this->route . "/editNetwork/$networksId");
            exit;
        } catch (PDOException $error) {
            Toast::itemAddError();
            header('location:' . URL . $this->route . "/addNetwork");
            exit;
        }
    }

    public function editNetwork($networkId)
    {
        $modelGenerico = new ModelGenerico();
        $networksModel = new NetworksSite();

        $network = $networksModel->getNetworkById($networkId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editNetwork.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditNetwork($networkId)
    {
        Secure::check_post_method($this->route . "/editNetwork/$networkId");

        $arrPost = array(
            'nome' => $_POST['nome'],
            'ordem' => $_POST['ordem'],
            'icone' => $_POST['icone'],
            'link' => $_POST['link'],
            'cor' => $_POST['cor'],
            'cor_hover' => $_POST['cor_hover'],
            'cor_rodape' => $_POST['cor_rodape'],
            'cor_rodape_hover' => $_POST['cor_rodape_hover'],
            'ativo' => $_POST['ativo'],
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $networkId, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route . "/editNetwork/$networkId");
        exit;
    }

    public function disableNetwork($networkId, $page)
    {
        try {
            $success = (new ModelGenerico())->disableItem2($networkId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableNetwork($networkId, $page)
    {
        try {
            $success = (new ModelGenerico())->enableItem2($networkId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
