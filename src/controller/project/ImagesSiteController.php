<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Secure;
use RR\model\ImagesSite;
use PDOException;

class ImagesSiteController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'images-site';
        $this->dir = 'images-site';
        $this->model = new ImagesSite();
        $this->table = 'imagem';
        parent::__construct($this->route);
    }

    public function index()
    {
        $modelGenerico = new ModelGenerico();
        $imagesModel = new ImagesSite();

        if (!isset($_GET['ativo'])) {
            $_GET['ativo'] = true;
        }

        $pagination = $modelGenerico->pagination();
        $nextPagination = $imagesModel->getAndFilterAllImages(20, $_GET, $pagination + 1);
        $images = $imagesModel->getAndFilterAllImages(20, $_GET, $pagination);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addImage()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/imagesSite.js");

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addImage.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddImage()
    {
        Secure::check_post_method($this->route . "/addImage");

        $gerenciaPost = new GerenciaPost();
        $imagesModel = new ImagesSite();

        $arrPost = array(
            'nome' => $_POST['nome'],
            'ordem' => $_POST['ordem'],
            'link' => $_POST['link'],
            'ativo' => $_POST['ativo'],
        );

        try {
            $imageId = $gerenciaPost->insert7181($arrPost, $this->table, true, false);

            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            if (isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
                if (!file_exists("img/site_imgs/images/$imageId/")) {
                    mkdir("img/site_imgs/images/$imageId/", 0777, true);
                }

                $arrImage = array("capa" => true);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $imageId, false);

                $extension = str_replace(".", "", substr($_FILES['image']['name'], -4));
                $filename = $_FILES['image']['tmp_name'];
                $path = "img/site_imgs/images/$imageId/";

                $image = resize1($path, "$imageId", 124, 57, 1, "-1thumb", $filename, $extension);
            }

            header('location:' . URL . $this->route . "/editImage/$imageId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addImage?added=false");
            exit;
        }
    }

    public function editImage($imageId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/imagesSite.js");
        $this->addScript(URL . "js/" . JSVERSION . "/utils.js");

        $imagesModel = new ImagesSite();
        $image = $imagesModel->getImageById($imageId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editImage.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditImage($imageId)
    {
        Secure::check_post_method($this->route . "/editImage/$imageId");

        $gerenciaPost = new GerenciaPost();
        $imagesModel = new ImagesSite();

        $image = $imagesModel->getImageById($imageId);

        $arrPost = array(
            'nome' => $_POST['nome'],
            'ordem' => $_POST['ordem'],
            'link' => $_POST['link'],
            'ativo' => $_POST['ativo'],
        );

        try {
            $gerenciaPost->update8191($arrPost, $this->table, 'id', $imageId, false);

            if (isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
                @unlink("img/site_imgs/images/$imageId/$imageId-$image->cont" . "thumb.png");

                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                if (!file_exists("img/site_imgs/images/$imageId/")) {
                    mkdir("img/site_imgs/images/$imageId/", 0777, true);
                }

                $arrImage = array("cont" => ++$image->cont, "capa" => true);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $imageId, false);

                $extension = str_replace(".", "", substr($_FILES['image']['name'], -4));
                $filename = $_FILES['image']['tmp_name'];
                $path = "img/site_imgs/images/$imageId/";

                $image = resize1($path, "$imageId", 124, 57, 1, "-$image->cont" . "thumb", $filename, $extension);
            }

            header('location:' . URL . $this->route . "/editImage/$imageId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editImage/$imageId?edited=false");
            exit;
        }
    }

    public function deleteImageId($imageId)
    {
        $gerenciaPost = new GerenciaPost();
        $imageModel = new ImagesSite();

        $image = $imageModel->getImageById($imageId);
        @unlink("img/site_imgs/images/$imageId/$imageId-$image->cont" . "thumb.png");

        $arrPost = array('capa' => 0, "cont" => ++$image->cont);

        try {
            $gerenciaPost->update8191($arrPost, $this->table, "id", $imageId, false);

            header('location:' . URL . $this->route . "/editImage/$imageId?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editImage/$imageId?deleted=false");
            exit;
        }
    }

    public function disableImage($imageId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem2($imageId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableImage($imageId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem2($imageId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
