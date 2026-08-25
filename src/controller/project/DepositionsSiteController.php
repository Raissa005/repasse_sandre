<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\model\DepositionsSite;
use PDOException;

class DepositionsSiteController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'depositions-site';
        $this->dir = 'depositions-site';
        $this->model = new DepositionsSite();
        $this->table = 'depoimento';
        parent::__construct($this->route);
    }

    public function index()
    {
        $modelGenerico = new ModelGenerico();
        $depositionsModel = new DepositionsSite();

        if (!isset($_GET['ativo'])) {
            $_GET['ativo'] = true;
        }

        $pagination = $modelGenerico->pagination();
        $nextPagination = $depositionsModel->getAndFilterAllDepositions(20, $_GET, $pagination + 1);
        $depositions = $depositionsModel->getAndFilterAllDepositions(20, $_GET, $pagination);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addDeposition()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addDeposition.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddDeposition()
    {
        Secure::check_post_method($this->route . "/addDeposition");

        $arrPost = array(
            'nome' => $_POST['nome'],
            'descricao' => $_POST['descricao'],
            'ativo' => $_POST['ativo'],
            'slugify' => Util::slugify($_POST['nome']),
        );

        try {
            $depositionId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);
            $deposition = (new DepositionsSite())->getDepositionById($depositionId);

            if (!empty($_FILES['image']['tmp_name'])) {
                if (!file_exists("img/site_imgs/depositions/$depositionId/")) {
                    mkdir("img/site_imgs/depositions/$depositionId/", 0777, true);
                }

                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                $arrImage = array("cont" => ++$deposition->cont, "capa" => true);
                (new GerenciaPost())->update8191($arrImage, $this->table, "id", $depositionId, false);

                $extension = str_replace(".", "", substr($_FILES['image']['name'], -4));
                $filename = $_FILES['image']['tmp_name'];
                $path = "img/site_imgs/depositions/$depositionId/";

                $image = resize1($path, $depositionId, 200, 200, 1, "-" . $deposition->cont . "thumb", $filename, $extension);
            }

            header('location:' . URL . $this->route . "/editDeposition/$depositionId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addDeposition?added=false");
            exit;
        }
    }

    public function editDeposition($depositionId)
    {
        $depositionsModel = new DepositionsSite();
        $deposition = $depositionsModel->getDepositionById($depositionId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editDeposition.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditDeposition($depositionId)
    {
        Secure::check_post_method($this->route . "/editDeposition/$depositionId");

        $gerenciaPost = new GerenciaPost();
        $depositionsModel = new DepositionsSite();

        $deposition = $depositionsModel->getDepositionById($depositionId);
        $arrPost = array('nome' => $_POST['nome'], 'descricao' => $_POST['descricao'], 'ativo' => $_POST['ativo']);

        try {
            $gerenciaPost->update8191($arrPost, $this->table, 'id', $depositionId, false);

            if (!empty($_FILES['image']['tmp_name'])) {
                @unlink("img/site_imgs/depositions/$depositionId/$depositionId-$deposition->cont" . "thumb" . ".jpg");
                if (!file_exists("img/site_imgs/depositions/$depositionId/")) {
                    mkdir("img/site_imgs/depositions/$depositionId/", 0777, true);
                }

                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                $arrImage = array("cont" => ++$deposition->cont, "capa" => true);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $depositionId, false);

                $extension = str_replace(".", "", substr($_FILES['image']['name'], -4));
                $filename = $_FILES['image']['tmp_name'];
                $path = "img/site_imgs/depositions/$depositionId/";

                $image = resize1($path, $depositionId, 200, 200, 1, "-" . $deposition->cont . "thumb", $filename, $extension);
            }

            header('location:' . URL . $this->route . "/editDeposition/$depositionId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editDeposition/$depositionId?edited=false");
            exit;
        }
    }

    public function deleteImageId($depositionId)
    {
        $depositionsModel = new DepositionsSite();
        $gerenciaPost = new GerenciaPost();

        $deposition = $depositionsModel->getDepositionById($depositionId);

        $arrImage = array("cont" => ++$deposition->cont, "capa" => 0);
        $gerenciaPost->update8191($arrImage, $this->table, "id", $depositionId, false);

        @unlink("img/site_imgs/depositions/$depositionId/$depositionId-$deposition->cont" .  "thumb" . ".jpg");

        header('location:' . URL . $this->route . "/editDeposition/$depositionId?deleted=true");
        exit;
    }

    public function disableDeposition($depositionId, $page)
    {
        $ModelGenerico =  new ModelGenerico();

        $ModelGenerico->disableItem2($depositionId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableDeposition($depositionId, $page)
    {
        $ModelGenerico =  new ModelGenerico();

        $ModelGenerico->enableItem2($depositionId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
