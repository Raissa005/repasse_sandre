<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\libs\BoxAlert;
use RR\libs\Secure;
use RR\model\DigitalCard;
use PDOException;
use RR\libs\Pagination;

class DigitalCardController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'digital-card';
        $this->dir = 'digital-card';
        $this->model = new DigitalCard();
        $this->table = 'digital_card';
        parent::__construct($this->route);

        $this->addScript(URL . "js/" . JSVERSION . "/digitalCard.js");
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
        Secure::access_admin(true);
        Secure::check_post_method($this->route . "/addItem?error=error");

        $gerenciaPost = new GerenciaPost();

        $arrPost = array(
            'name' => $_POST['name'],
            'cor_fundo' => $_POST['cor_fundo'],
            'cor_fonte_usuario' => $_POST['cor_fonte_usuario'],
            'cor_fonte_ocupacao' => $_POST['cor_fonte_ocupacao'],
            'cor_fonte_creci' => $_POST['cor_fonte_creci'],
            'cor_fonte_imobiliaria' => $_POST['cor_fonte_imobiliaria'],
            'cor_fonte_legenda' => $_POST['cor_fonte_legenda'],
            'cor_borda_usuario' => $_POST['cor_borda_usuario'],
            'cor_fundo_logo' => $_POST['cor_fundo_logo'],
            'cor_borda_icone' => $_POST['cor_borda_icone'],
            'fonte_usuario' => $_POST['fonte_usuario'],
            'tamanho_fonte_usuario' => $_POST['tamanho_fonte_usuario'],
            'fonte_ocupacao' => $_POST['fonte_ocupacao'],
            'tamanho_fonte_ocupacao' => $_POST['tamanho_fonte_ocupacao'],
            'fonte_creci' => $_POST['fonte_creci'],
            'tamanho_fonte_creci' => $_POST['tamanho_fonte_creci'],
            'fonte_imobiliaria' => $_POST['fonte_imobiliaria'],
            'tamanho_fonte_imobiliaria' => $_POST['tamanho_fonte_imobiliaria'],
            'fonte_legenda' => $_POST['fonte_legenda'],
            'tamanho_fonte_legenda' => $_POST['tamanho_fonte_legenda'],
            'status' => $_POST['status'],
            'created_by' => $_SESSION['RR']->user->id,
        );

        try {
            $itemId = $gerenciaPost->insert7181($arrPost, $this->table, true, false);

            if (!empty($_FILES)) {
                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                if (!empty($_FILES['imageFundo']['tmp_name'])) {
                    if (!file_exists("img/card_digital/$itemId/")) {
                        mkdir("img/card_digital/$itemId/", 0777, true);
                    }

                    $extension = str_replace(".", "", substr($_FILES['imageFundo']['name'], -4));

                    $arrImage = array("ext_fundo" => $extension, "capa_fundo" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                    $filename = $_FILES['imageFundo']['tmp_name'];
                    $path = "img/card_digital/$itemId/";

                    $size = getimagesize($_FILES['imageFundo']['tmp_name']);
                    if ($size[0] == 1490 && $size[1] == 2130) {
                        copy($_FILES['imageFundo']['tmp_name'], $path . "fundo-1" . ".$extension");
                    } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                        $fundo = wideImagePhoto($filename, $path, 1490, 2130, "fundo-1", ".{$extension}", 100);
                    } else if ($extension == "png" || $extension == "PNG") {
                        $fundo = wideImagePhoto($filename, $path, 1490, 2130, "fundo-1", ".{$extension}", 9);
                    }
                }

                if (!empty($_FILES['imageLogo']['tmp_name'])) {
                    if (!file_exists("img/card_digital/$itemId/")) {
                        mkdir("img/card_digital/$itemId/", 0777, true);
                    }

                    $extension = str_replace(".", "", substr($_FILES['imageLogo']['name'], -4));

                    $arrImage = array("ext_logo" => $extension, "capa_logo" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                    $filename = $_FILES['imageLogo']['tmp_name'];
                    $path = "img/card_digital/$itemId/";

                    $size = getimagesize($_FILES['imageLogo']['tmp_name']);
                    if ($size[0] == 600 && $size[1] == 280) {
                        copy($_FILES['imageLogo']['tmp_name'], $path . "logo-1" . ".$extension");
                    } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                        $logo = wideImagePhoto($filename, $path, 600, 280, "logo-1", ".{$extension}", 100);
                    } else if ($extension == "png" || $extension == "PNG") {
                        $logo = wideImagePhoto($filename, $path, 600, 280, "logo-1", ".{$extension}", 9);
                    }
                }
            }

            header('location:' . URL . $this->route . "/editItem/$itemId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addItem?added=false");
            exit;
        }
    }

    public function editItem($itemId)
    {
        $digitalCardModel = new DigitalCard();
        $item = $digitalCardModel->getItemById8161($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId?error=error");

        $modelGenerico = new ModelGenerico();
        $gerenciaPost = new GerenciaPost();

        $arrPost = array(
            'name' => $_POST['name'],
            'cor_fundo' => $_POST['cor_fundo'],
            'cor_fonte_usuario' => $_POST['cor_fonte_usuario'],
            'cor_fonte_ocupacao' => $_POST['cor_fonte_ocupacao'],
            'cor_fonte_creci' => $_POST['cor_fonte_creci'],
            'cor_fonte_imobiliaria' => $_POST['cor_fonte_imobiliaria'],
            'cor_fonte_legenda' => $_POST['cor_fonte_legenda'],
            'cor_borda_usuario' => $_POST['cor_borda_usuario'],
            'cor_fundo_logo' => $_POST['cor_fundo_logo'],
            'cor_borda_icone' => $_POST['cor_borda_icone'],
            'fonte_usuario' => $_POST['fonte_usuario'],
            'tamanho_fonte_usuario' => $_POST['tamanho_fonte_usuario'],
            'fonte_ocupacao' => $_POST['fonte_ocupacao'],
            'tamanho_fonte_ocupacao' => $_POST['tamanho_fonte_ocupacao'],
            'fonte_creci' => $_POST['fonte_creci'],
            'tamanho_fonte_creci' => $_POST['tamanho_fonte_creci'],
            'fonte_imobiliaria' => $_POST['fonte_imobiliaria'],
            'tamanho_fonte_imobiliaria' => $_POST['tamanho_fonte_imobiliaria'],
            'fonte_legenda' => $_POST['fonte_legenda'],
            'tamanho_fonte_legenda' => $_POST['tamanho_fonte_legenda'],
            'status' => $_POST['status'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        try {
            $gerenciaPost->update8191($arrPost, $this->table, 'id', $itemId, false);

            if (isset($_FILES)) {
                $item = $modelGenerico->getItemById8161($itemId, $this->table);

                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                if (!empty($_FILES['imageFundo']['tmp_name'])) {
                    if (!file_exists("img/card_digital/$itemId/")) {
                        mkdir("img/card_digital/$itemId/", 0777, true);
                    }
                    @unlink("img/card_digital/$itemId/fundo-$item->cont_fundo.$item->ext_fundo");

                    $extension = str_replace(".", "", substr($_FILES['imageFundo']['name'], -4));

                    $arrImage = array("cont_fundo" => ++$item->cont_fundo, "ext_fundo" => $extension, "capa_fundo" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                    $filename = $_FILES['imageFundo']['tmp_name'];
                    $path = "img/card_digital/$itemId/";

                    $size = getimagesize($_FILES['imageFundo']['tmp_name']);
                    if ($size[0] == 1490 && $size[1] == 2130) {
                        copy($_FILES['imageFundo']['tmp_name'], $path . "fundo-$item->cont_fundo" . ".$extension");
                    } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                        $fundo = wideImagePhoto($filename, $path, 1490, 2130, "fundo-$item->cont_fundo", ".{$extension}", 100);
                    } else if ($extension == "png" || $extension == "PNG") {
                        $fundo = wideImagePhoto($filename, $path, 1490, 2130, "fundo-$item->cont_fundo", ".{$extension}", 9);
                    }
                }

                if (!empty($_FILES['imageLogo']['tmp_name'])) {
                    if (!file_exists("img/card_digital/$itemId/")) {
                        mkdir("img/card_digital/$itemId/", 0777, true);
                    }
                    @unlink("img/card_digital/$itemId/logo-$item->cont_logo.$item->ext_logo");

                    $extension = str_replace(".", "", substr($_FILES['imageLogo']['name'], -4));

                    $arrImage = array("cont_logo" => ++$item->cont_logo, "ext_logo" => $extension, "capa_logo" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                    $filename = $_FILES['imageLogo']['tmp_name'];
                    $path = "img/card_digital/$itemId/";

                    $size = getimagesize($_FILES['imageLogo']['tmp_name']);
                    if ($size[0] == 600 && $size[1] == 280) {
                        copy($_FILES['imageLogo']['tmp_name'], $path . "logo-$item->cont_logo" . ".$extension");
                    } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                        $logo = wideImagePhoto($filename, $path, 600, 280, "logo-$item->cont_logo", ".{$extension}", 100);
                    } else if ($extension == "png" || $extension == "PNG") {
                        $logo = wideImagePhoto($filename, $path, 600, 280, "logo-$item->cont_logo", ".{$extension}", 9);
                    }
                }
            }

            header('location:' . URL . $this->route . "/editItem/$itemId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId?edited=false");
            exit;
        }
    }

    public function deleteImageFundo($itemId)
    {
        $digitalCardModel = new DigitalCard();
        $item = $digitalCardModel->getItemById8161($itemId);

        try {
            @unlink("img/card_digital/$itemId/fundo-" . $item->cont_fundo . ".$item->ext_fundo");

            $arrPost = array("capa_fundo" => 0, "cont_fundo" => ++$item->cont_fundo);
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", $itemId, false);

            header('location:' . URL . $this->route . "/editItem/$itemId?deleted=true");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId?deleted=true");
            exit;
        }
    }

    public function deleteImageLogo($itemId)
    {
        $digitalCardModel = new DigitalCard();
        $item = $digitalCardModel->getItemById8161($itemId);

        try {
            @unlink("img/card_digital/$itemId/logo-" . $item->cont_logo . ".$item->ext_logo");

            $arrPost = array("capa_logo" => 0, "cont_logo" => ++$item->cont_logo);
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", $itemId, false);

            header('location:' . URL . $this->route . "/editItem/$itemId?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId?deleted=false");
            exit;
        }
    }

    public function disableItem($itemId, $page = "1")
    {
        $modelGenerico =  new ModelGenerico();
        $modelGenerico->disableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $modelGenerico =  new ModelGenerico();
        $modelGenerico->enableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
