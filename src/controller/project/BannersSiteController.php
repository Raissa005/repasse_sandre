<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\BannersSite;
use PDOException;
use RR\libs\Pagination;

class BannersSiteController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    private $modelGenerico;

    public function __construct()
    {
        $this->route = 'banners-site';
        $this->dir = 'banners-site';
        $this->model = new BannersSite();
        $this->table = 'banner';
        parent::__construct($this->route);

        $this->modelGenerico = new ModelGenerico();
    }

    public function index()
    {
        if (!isset($_GET['ativo'])) {
            $_GET['ativo'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $banners = $this->model->getAndFilterAllBanners($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($banners->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($banners->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addBanner()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addBanner.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddBanner()
    {
        Secure::check_post_method($this->route . "/addBanner");

        $gerenciaPost = new GerenciaPost();

        $arrPost = [
            'nome' => $_POST['nome'],
            'ordem' => $_POST['ordem'],
            'link' => $_POST['link'],
            'link_externo' => $_POST['link_externo'],
            'ativo' => $_POST['ativo'],
        ];

        try {
            $bannerId = $gerenciaPost->insert7181($arrPost, $this->table, true, false);

            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            if (!empty($_FILES['imageDesktop']['tmp_name'])) {
                if (!file_exists("img/site_imgs/banners/")) {
                    mkdir("img/site_imgs/banners/", 0777, true);
                }

                $arrImage = array("capa" => true);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $bannerId, false);

                $extension = str_replace(".", "", substr($_FILES['imageDesktop']['name'], -4));
                $filename = $_FILES['imageDesktop']['tmp_name'];
                $path = "img/site_imgs/banners/";

                $size = getimagesize($_FILES['imageDesktop']['tmp_name']);
                if ($size[0] == 1920 && $size[1] == 630) {
                    copy($_FILES['imageDesktop']['tmp_name'], $path . "$bannerId-1.$extension");
                } else {
                    $imageDesktop = wideImagePhoto($filename, $path, 1920, 630, "$bannerId-1", ".$extension", 100);
                }
            }

            if (!empty($_FILES['imageMobile']['tmp_name'])) {
                if (!file_exists("img/site_imgs/banners/")) {
                    mkdir("img/site_imgs/banners/", 0777, true);
                }

                $arrImage2 = array("capa2" => true);
                $gerenciaPost->update8191($arrImage2, $this->table, "id", $bannerId, false);

                $extension = str_replace(".", "", substr($_FILES['imageMobile']['name'], -4));
                $filename = $_FILES['imageMobile']['tmp_name'];
                $path = "img/site_imgs/banners/";

                $size = getimagesize($_FILES['imageMobile']['tmp_name']);
                if ($size[0] == 500 && $size[1] == 550) {
                    copy($_FILES['imageMobile']['tmp_name'], $path . "$bannerId-1m.$extension");
                } else {
                    $imageDesktop = wideImagePhoto($filename, $path, 500, 550, "$bannerId-1m", ".$extension", 100);
                }
            }

            header('location:' . URL . $this->route . "/editBanner/$bannerId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addBanner?added=false");
            exit;
        }
    }

    public function editBanner($bannerId)
    {
        $bannerModel = new BannersSite();
        $banner = $bannerModel->getBannerById($bannerId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editBanner.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditBanner($bannerId)
    {
        Secure::check_post_method($this->route . "editBanner/$bannerId");

        $gerenciaPost = new GerenciaPost();
        $bannerModel = new BannersSite();

        $banner = $bannerModel->getBannerById($bannerId);

        $arrPost = [
            'nome' => $_POST['nome'],
            'ordem' => $_POST['ordem'],
            'link' => $_POST['link'],
            'link_externo' => $_POST['link_externo'],
            'ativo' => $_POST['ativo'],
        ];

        try {
            $gerenciaPost->update8191($arrPost, $this->table, 'id', $bannerId, false);

            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            if (!empty($_FILES['imageDesktop']['tmp_name'])) {
                if (!file_exists("img/site_imgs/banners/")) {
                    mkdir("img/site_imgs/banners/", 0777, true);
                }
                @unlink("img/site_imgs/banners/$bannerId-$banner->cont.jpg");

                $arrImage = ["cont" => $banner->cont, "capa" => true];
                $gerenciaPost->update8191($arrImage, $this->table, "id", $bannerId, false);

                $extension = str_replace(".", "", substr($_FILES['imageDesktop']['name'], -4));
                $filename = $_FILES['imageDesktop']['tmp_name'];
                $path = "img/site_imgs/banners/";

                $size = getimagesize($_FILES['imageDesktop']['tmp_name']);
                if ($size[0] == 1920 && $size[1] == 630) {
                    copy($_FILES['imageDesktop']['tmp_name'], $path . "$bannerId-$banner->cont" . ".$extension");
                } else {
                    $imageDesktop = wideImagePhoto($filename, $path, 1920, 630, "$bannerId-$banner->cont", ".$extension", 100);
                }
            }

            if (!empty($_FILES['imageMobile']['tmp_name'])) {
                if (!file_exists("img/site_imgs/banners/")) {
                    mkdir("img/site_imgs/banners/", 0777, true);
                }
                @unlink("img/site_imgs/banners/" . $bannerId . "-" . $banner->cont2 . "m.jpg");

                $arrImage2 = ["cont2" => $banner->cont2, "capa2" => true];
                $gerenciaPost->update8191($arrImage2, $this->table, "id", $bannerId, false);

                $extension = str_replace(".", "", substr($_FILES['imageMobile']['name'], -4));
                $filename = $_FILES['imageMobile']['tmp_name'];
                $path = "img/site_imgs/banners/";

                $size = getimagesize($_FILES['imageMobile']['tmp_name']);
                if ($size[0] == 500 && $size[1] == 550) {
                    copy($_FILES['imageMobile']['tmp_name'], $path . "$bannerId-$banner->cont2" . "m.$extension");
                } else {
                    $imageDesktop = wideImagePhoto($filename, $path, 500, 550, "$bannerId-$banner->cont2", "m.$extension", 100);
                }
            }

            header('location:' . URL . $this->route . "/editBanner/$bannerId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editBanner/$bannerId?edited=false");
            exit;
        }
    }

    public function deleteImageDesktopId($bannerId)
    {
        $banner = (new BannersSite())->getBannerById($bannerId);

        try {
            @unlink("img/site_imgs/banners/" . $bannerId . "-" . $banner->cont . ".jpg");

            $arrPost = ["capa" => 0, "cont" => ++$banner->cont];
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", $bannerId, false);

            header('location:' . URL . $this->route . "/editBanner/$bannerId?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editBanner/$bannerId?deleted=true");
            exit;
        }
    }

    public function deleteImageMobileId($bannerId)
    {
        $banner = (new BannersSite())->getBannerById($bannerId);

        try {
            @unlink("img/site_imgs/banners/" . $bannerId . "-" . $banner->cont2 . "m.jpg");

            $arrPost = ["capa2" => 0, "cont2" => ++$banner->cont2];
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", $bannerId, false);

            header('location:' . URL . $this->route . "/editBanner/$bannerId?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editBanner/$bannerId?deleted=false");
            exit;
        }
    }

    public function disableBanner($bannerId, $page)
    {
        $this->modelGenerico->disableItem2($bannerId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableBanner($bannerId, $page)
    {
        $this->modelGenerico->enableItem2($bannerId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
