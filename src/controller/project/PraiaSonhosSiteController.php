<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\libs\BoxAlert;
use RR\libs\Secure;
use RR\model\Branch;
use RR\model\PraiaSonhos;
use PDOException;
use RR\libs\Pagination;

class PraiaSonhosSiteController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'praia-sonhos-site';
        $this->dir = 'praia-sonhos-site';
        $this->model = new PraiaSonhos();
        $this->table = 'praia_sonho';
        parent::__construct($this->route);

        $this->addScript(URL . "js/" . JSVERSION . "/praiaSonhos.js");
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->alert = (new BoxAlert());
    }

    public function index()
    {
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
        Secure::access_admin(true);
        $modelGenerico = new ModelGenerico();
        $cities = (new Branch())->getCitiesByState("SC");
        $states = $modelGenerico->getAllItens("states");

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route . "/addItem?error=error");

        $gerenciaPost = new GerenciaPost();
        $praiaSonhosModel = new PraiaSonhos();

        $arrPost = array('nome' => $_POST['nome'], 'slugify' => Util::slugify($_POST['nome']), 'id_cidade' => $_POST['id_cidade']);

        try {
            $itemId = $gerenciaPost->insert7181($arrPost, $this->table, true, false);

            $item = $praiaSonhosModel->getItemById8161($itemId);

            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            if (!empty($_FILES['imageCapa']['tmp_name'])) {
                if (!file_exists("img/site_imgs/praiaSonhos/$itemId/")) {
                    mkdir("img/site_imgs/praiaSonhos/$itemId/", 0777, true);
                }

                $extension = str_replace(".", "", substr($_FILES['imageCapa']['name'], -4));
                $arrImage = array("capa" => true, "cont" => ++$item->cont, "ext" => $extension);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                $filename = $_FILES['imageCapa']['tmp_name'];
                $path = "img/site_imgs/praiaSonhos/$itemId/";

                $size = getimagesize($_FILES['imageCapa']['tmp_name']);
                if ($size[0] == 500 && $size[1] == 500) {
                    copy($_FILES['imageCapa']['tmp_name'], $path . "capa-{$item->cont}.$extension");
                } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                    $imageCapa = wideImagePhoto($filename, $path, 500, 500, "capa-{$item->cont}", ".$extension", 100);
                } else if ($extension == "png" || $extension == "PNG") {
                    $imageCapa = wideImagePhoto($filename, $path, 500, 500, "capa-{$item->cont}", ".$extension", 9);
                }
            }

            if (!empty($_FILES['imageBanner']['tmp_name'])) {
                if (!file_exists("img/site_imgs/praiaSonhos/$itemId/")) {
                    mkdir("img/site_imgs/praiaSonhos/$itemId/", 0777, true);
                }

                $extension = str_replace(".", "", substr($_FILES['imageBanner']['name'], -4));
                $arrImage = array("capa2" => true, "cont2" => ++$item->cont2, "ext2" => $extension);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                $filename = $_FILES['imageBanner']['tmp_name'];
                $path = "img/site_imgs/praiaSonhos/$itemId/";

                $size = getimagesize($_FILES['imageBanner']['tmp_name']);
                if ($size[0] == 1140 && $size[1] == 360) {
                    copy($_FILES['imageBanner']['tmp_name'], $path . "banner-{$item->cont2}.$extension");
                } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                    $imageBanner = wideImagePhoto($filename, $path, 1140, 360, "banner-{$item->cont2}", ".$extension", 100);
                } else if ($extension == "png" || $extension == "PNG") {
                    $imageBanner = wideImagePhoto($filename, $path, 1140, 360, "banner-{$item->cont2}", ".$extension", 9);
                }
            }

            header('location:' . URL . $this->route . "/editItem/$itemId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/addItem?added=false');
            exit;
        }
    }

    public function editItem($itemId)
    {
        $modelGenerico = new ModelGenerico();
        $praiaSonhosModel = new PraiaSonhos();
        $states = $modelGenerico->getAllItens("states");

        $item = $praiaSonhosModel->getItemById8161($itemId);
        $cities = (new Branch())->getCitiesByState($item->uf);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId?error=error");

        $gerenciaPost = new GerenciaPost();
        $praiaSonhosModel = new PraiaSonhos();

        $arrPost = array(
            'nome' => $_POST['nome'],
            'slugify' => Util::slugify($_POST['nome']),
            'id_cidade' => $_POST['id_cidade'],
            "ativo" => $_POST['ativo'],
        );

        try {
            $gerenciaPost->update8191($arrPost, $this->table, 'id', $itemId, false);
            $item = $praiaSonhosModel->getItemById8161($itemId);

            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            if (!empty($_FILES['imageCapa']['tmp_name'])) {
                if (!file_exists("img/site_imgs/praiaSonhos/$itemId/")) {
                    mkdir("img/site_imgs/praiaSonhos/$itemId/", 0777, true);
                }
                @unlink("img/site_imgs/praiaSonhos/$itemId/capa-{$item->cont}.{$item->ext}");
                $extension = str_replace(".", "", substr($_FILES['imageCapa']['name'], -4));
                $arrImage = array("capa" => true, "cont" => ++$item->cont, "ext" => $extension);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                $filename = $_FILES['imageCapa']['tmp_name'];
                $path = "img/site_imgs/praiaSonhos/$itemId/";

                $size = getimagesize($_FILES['imageCapa']['tmp_name']);
                if ($size[0] == 500 && $size[1] == 500) {
                    copy($_FILES['imageCapa']['tmp_name'], $path . "capa-{$item->cont}.$extension");
                } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                    $imageCapa = wideImagePhoto($filename, $path, 500, 500, "capa-{$item->cont}", ".$extension", 100);
                } else if ($extension == "png" || $extension == "PNG") {
                    $imageCapa = wideImagePhoto($filename, $path, 500, 500, "capa-{$item->cont}", ".$extension", 9);
                }
            }

            if (!empty($_FILES['imageBanner']['tmp_name'])) {
                if (!file_exists("img/site_imgs/praiaSonhos/$itemId/")) {
                    mkdir("img/site_imgs/praiaSonhos/$itemId/", 0777, true);
                }
                @unlink("img/site_imgs/praiaSonhos/banner-{$item->cont2}.{$item->ext2}");
                $extension = str_replace(".", "", substr($_FILES['imageBanner']['name'], -4));
                $arrImage = array("capa2" => true, "cont2" => ++$item->cont2, "ext2" => $extension);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                $filename = $_FILES['imageBanner']['tmp_name'];
                $path = "img/site_imgs/praiaSonhos/$itemId/";

                $size = getimagesize($_FILES['imageBanner']['tmp_name']);
                if ($size[0] == 1140 && $size[1] == 360) {
                    copy($_FILES['imageBanner']['tmp_name'], $path . "banner-{$item->cont2}.$extension");
                } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                    $imageCapa = wideImagePhoto($filename, $path, 1140, 360, "banner-{$item->cont2}", ".$extension", 100);
                } else if ($extension == "png" || $extension == "PNG") {
                    $imageCapa = wideImagePhoto($filename, $path, 1140, 360, "banner-{$item->cont2}", ".$extension", 9);
                }
            }

            header('location:' . URL . $this->route . "/editItem/$itemId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId?edited=false");
            exit;
        }
    }

    public function disableItem($itemId, $page = "1")
    {
        $modelGenerico =  new ModelGenerico();
        $modelGenerico->disableItem2($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $modelGenerico =  new ModelGenerico();
        $modelGenerico->enableItem2($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
