<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\libs\DeleteFile;
use RR\libs\Secure;
use RR\model\TextsSite;
use PDOException;
use RR\libs\Pagination;

class TextsSiteController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'texts-site';
        $this->dir = 'texts-site';
        $this->model = new TextsSite();
        $this->table = 'texto';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['ativo'])) {
            $_GET['ativo'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $texts = $this->model->getAndFilterAllTexts($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($texts->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($texts->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function editText($textId)
    {
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $modelGenerico = new ModelGenerico();
        $textsModel = new TextsSite();

        $text = $textsModel->getTextById($textId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/editText.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditText($textId)
    {
        Secure::check_post_method($this->route . "/editText/$textId");

        $arrPost = [
            'nome' => $_POST['nome'],
            'descricao' => $_POST['descricao'],
            'galeria' => $_POST['galeria'],
            'slugify' => Util::slugify($_POST['nome']),
        ];

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $textId, false);

            header('location:' . URL . $this->route . "/editText/$textId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editText/$textId?edited=false");
            exit;
        }
    }

    public function imagesText($textId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");

        $modelGenerico = new ModelGenerico();
        $textsModel = new TextsSite();

        $text = $textsModel->getTextById($textId);
        $images = $textsModel->getAllImagesByFolder($textId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/imagesText.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitImagesText($textId)
    {
        if (!empty($_FILES['images']['tmp_name'][0])) {
            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            $gerenciaPost = new GerenciaPost();
            $textModel = new TextsSite();

            try {
                if (!file_exists("img/site_imgs/text_imgs/$textId/")) {
                    mkdir("img/site_imgs/text_imgs/$textId/", 0777, true);
                }

                for ($i = 0; $i <= count($_FILES['images']['name']) - 1; $i++) {
                    $extension = str_replace(".", "", substr($_FILES['images']['name'][$i], -4));

                    if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG" || $extension == "png" || $extension == "PNG") {
                        $ordem = $textModel->getLastOrder($textId);
                        $ordem = $ordem ? $ordem->ordem + 1 : 1;

                        $arrayPost = array('id_pasta' =>  $textId, "ordem" => $ordem);
                        $idImage = $gerenciaPost->insert7181($arrayPost, "texto_foto", true, false);

                        $filename = $_FILES['images']['tmp_name'][$i];
                        $path = "img/site_imgs/text_imgs/$textId/" . $idImage;
                        $pasta_base = "img/site_imgs/text_imgs/$textId";

                        $lg = resize1($pasta_base, $idImage, 1024, 1024, 1, "lg", $filename, $extension);
                        if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                            $md = wideImagePhoto($filename, $path, 720, 500, "", "md.{$extension}", 100);
                            $xs = wideImagePhoto($filename, $path, 70, 70, "", "xs.{$extension}", 100);
                        } else if ($extension == "png" || $extension == "PNG") {
                            $md = wideImagePhoto($filename, $path, 720, 500, "", "md.{$extension}", 9);
                            $xs = wideImagePhoto($filename, $path, 70, 70, "", "xs.{$extension}", 9);
                        }
                    }
                }

                header('location:' . URL . $this->route . "/imagesText/$textId?edited=true");
                exit;
            } catch (PDOException $error) {
                header('location:' . URL . $this->route . "/imagesText/$textId?edited=false");
                exit;
            }
        }
    }

    public function deleteImageId($idImage)
    {
        $image = (new ModelGenerico())->getItemById8161($idImage, "texto_foto");
        Secure::check_post_method($this->route . "/imagesText/$image->id_pasta");

        try {
            DeleteFile::DeleteFile(
                [$idImage],
                "texto_foto",
                [
                    "img/site_imgs/text_imgs/$image->id_pasta/{$image->id}xs.jpg",
                    "img/site_imgs/text_imgs/$image->id_pasta/{$image->id}md.jpg",
                    "img/site_imgs/text_imgs/$image->id_pasta/{$image->id}lg.jpg"
                ]
            );

            header('location:' . URL . $this->route . '/imagesText/' . $image->id_pasta);
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/imagesText/' . $image->id_pasta);
            exit;
        }
    }

    public function deleteAllImages($idImages)
    {
        $modelGenerico = new ModelGenerico();
        $textImages = $modelGenerico->getItemByGenericField($idImages, "texto_foto", "id_pasta");

        $images = ["ids" => [], "paths" => []];

        foreach ($textImages as $image) {
            $images["ids"][] = $image->id;
            array_push(
                $images["paths"],
                "img/site_imgs/text_imgs/$image->id_pasta/{$image->id}xs.jpg",
                "img/site_imgs/text_imgs/$image->id_pasta/{$image->id}md.jpg",
                "img/site_imgs/text_imgs/$image->id_pasta/{$image->id}lg.jpg"
            );
        }

        DeleteFile::deleteFile($images["ids"], "texto_foto", $images["paths"]);

        header('location:' . URL . $this->route . '/imagesText/' . $idImages);
        exit;
    }
}
