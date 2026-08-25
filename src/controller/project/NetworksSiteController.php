<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Secure;
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

            header('location:' . URL . $this->route . "/editNetwork/$networksId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addNetwork?added=false");
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

            header('location:' . URL . $this->route . "/editNetwork/$networkId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editNetwork/$networkId?edited=false");
            exit;
        }
    }

    public function disableNetwork($networkId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem2($networkId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableNetwork($networkId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem2($networkId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
