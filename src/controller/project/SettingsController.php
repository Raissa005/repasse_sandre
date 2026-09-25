<?php

namespace RR\controller\project;

use PDOException;
use RR\libs\Util;
use RR\model\User;
use RR\libs\Secure;
use RR\libs\BoxAlert;
use RR\model\MenuAccess;
use RR\model\GerenciaPost;
use RR\model\SystemSettings;
use RR\model\SettingsSite;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function RR\Controller\redirect;

class SettingsController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'settings';
        $this->dir = 'settings';
        $this->model = new SystemSettings();
        $this->table = 'system_config';
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
    }

    private function navTabs()
    {
        $navTabs = [
            (object)['text' => 'Dados Gerais', 'route' => URL . $this->route . '/', 'class' => ($_GET['url'] == "{$this->route}/" ? 'active' : '')],
            (object)['text' => 'Imagens', 'route' => URL . $this->route . '/images/', 'class' => ($_GET['url'] == $this->route . '/images/' ? 'active' : '')],
            (object)['text' => 'Menus', 'route' => URL . $this->route . '/menus/', 'class' => ($_GET['url'] == $this->route . '/menus/' ? 'active' : '')]
        ];

        return $navTabs;
    }

    public function index()
    {
        $item = $this->model->getItemById8161(1);

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/",
            'title' => 'Configurações',
            'caption' => 'Dados Gerais',
        ];

        $navTabs = Self::navTabs();

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitSettings()
    {
        Secure::check_post_method($this->route . "?error=error");

        $arrPost = array(
            'title' => $_POST['title'],
            'footer' => $_POST['footer'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", 1, false);

            header('location:' . URL . $this->route . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '?edited=false');
            exit;
        }
    }

    public function images()
    {
        $setting = (new SystemSettings())->getItemById8161();

        $contentHeader = (object)[
            'route' => URL . "{$this->route}/",
            'title' => 'Configurações',
            'caption' => 'Imagens',
        ];

        $navTabs = Self::navTabs();

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/images.php';
        require APP . 'view/_templates/footer.php';
    }


    public function handleSubmitImages()
    {
        if (isset($_FILES)) {
            $gerenciaPost = new GerenciaPost();
            $setting = (new SystemSettings())->getItemById8161();
            try {
                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
                $invalidExtension = false;

                if (!empty($_FILES['logo_menu']['tmp_name'])) {

                    $extension = str_replace(".", "", substr($_FILES['logo_menu']['name'], -4));
                    $size = @getimagesize($_FILES['logo_menu']['tmp_name']);

                    if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_menu']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_menu_cont + 1;

                            if ($size[0] == 230 && $size[1] == 50) {
                                copy($_FILES['logo_menu']['tmp_name'], $path . "logo_menu-" . $newCont . ".$extension");
                            } else {
                                wideImagePhoto($filename, $path, 230, 50, "logo_menu-" . $newCont, ".$extension", 9);
                            }

                            @unlink("img/settings/logo_menu-$setting->logo_menu_cont.$setting->logo_menu_ext");
                            $setting->logo_menu_cont = $newCont;
                            $gerenciaPost->update8191(["logo_menu_cont" => $newCont, "logo_menu_ext" => $extension, "logo_menu_capa" => true], $this->table, "id", 1, false);
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_mini']['tmp_name'])) {

                    $extension = str_replace(".", "", substr($_FILES['logo_mini']['name'], -4));
                    $size = @getimagesize($_FILES['logo_mini']['tmp_name']);

                    if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_mini']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_mini_cont + 1;

                            if ($size[0] == 50 && $size[1] == 50) {
                                copy($_FILES['logo_mini']['tmp_name'], $path . "logo_mini-" . $newCont . ".$extension");
                            } else {
                                wideImagePhoto($filename, $path, 50, 50, "logo_mini-" . $newCont, ".$extension", 9);
                            }

                            @unlink("img/settings/logo_mini-$setting->logo_mini_cont.$setting->logo_mini_ext");
                            $setting->logo_mini_cont = $newCont;
                            $gerenciaPost->update8191(["logo_mini_cont" => $newCont, "logo_mini_ext" => $extension, "logo_mini_capa" => true], $this->table, "id", 1, false);
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_login']['tmp_name'])) {

                    $extension = str_replace(".", "", substr($_FILES['logo_login']['name'], -4));
                    $size = @getimagesize($_FILES['logo_login']['tmp_name']);

                    if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_login']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_login_cont + 1;

                            if ($size[0] == 320 && $size[1] == 100) {
                                copy($_FILES['logo_login']['tmp_name'], $path . "logo_login-" . $newCont . ".$extension");
                            } else {
                                wideImagePhoto($filename, $path, 320, 100, "logo_login-" . $newCont, ".$extension", 9);
                            }

                            @unlink("img/settings/logo_login-$setting->logo_login_cont.$setting->logo_login_ext");
                            $setting->logo_login_cont = $newCont;
                            $gerenciaPost->update8191(["logo_login_cont" => $newCont, "logo_login_ext" => $extension, "logo_login_capa" => true], $this->table, "id", 1, false);
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_favicon']['tmp_name'])) {

                    $extension = str_replace(".", "", substr($_FILES['logo_favicon']['name'], -4));
                    $size = @getimagesize($_FILES['logo_favicon']['tmp_name']);

                    if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_favicon']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_favicon_cont + 1;

                            if ($size[0] == 20 && $size[1] == 20) {
                                copy($_FILES['logo_favicon']['tmp_name'], $path . "logo_favicon-" . $newCont . ".$extension");
                            } else {
                                wideImagePhoto($filename, $path, 20, 20, "logo_favicon-" . $newCont, ".$extension", 9);
                            }

                            @unlink("img/settings/logo_favicon-$setting->logo_favicon_cont.$setting->logo_favicon_ext");
                            $setting->logo_favicon_cont = $newCont;
                            $gerenciaPost->update8191(["logo_favicon_cont" => $newCont, "logo_favicon_ext" => $extension, "logo_favicon_capa" => true], $this->table, "id", 1, false);
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_rodape']['tmp_name'])) {

                    $extension = str_replace(".", "", substr($_FILES['logo_rodape']['name'], -4));
                    $size = @getimagesize($_FILES['logo_rodape']['tmp_name']);

                    if ($size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_rodape']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_rodape_cont + 1;

                            if ($size[0] == 220 && $size[1] == 115) {
                                copy($_FILES['logo_rodape']['tmp_name'], $path . "logo_rodape-" . $newCont . ".$extension");
                            } else {
                                wideImagePhoto($filename, $path, 220, 115, "logo_rodape-" . $newCont, ".$extension", 9);
                            }

                            @unlink("img/settings/logo_rodape-$setting->logo_rodape_cont.$setting->logo_rodape_ext");
                            $setting->logo_rodape_cont = $newCont;
                            $gerenciaPost->update8191(["logo_rodape_cont" => $newCont, "logo_rodape_ext" => $extension, "logo_rodape_capa" => true], $this->table, "id", 1, false);
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if ($invalidExtension) {
                    header('location:' . URL . $this->route . "/images?edited=true&invalidExt=1");
                    exit;
                }

                header('location:' . URL . $this->route . "/images?edited=true");
                exit;
            } catch (PDOException $error) {
                header('location:' . URL . $this->route . '/images?edited=false');
                exit;
            }
        }

        header('location:' . URL . $this->route . "/images?error=error");
        exit;
    }

    public function menus(): void
    {
        $this->addScript(URL . "js/" . JSVERSION . "/settings/menu.js");

        $contentHeader = (object)[
            'route' => URL . "{$this->route}",
            'title' => 'Configurações',
            'caption' => 'Menus',
        ];

        $navTabs = Self::navTabs();

        $profiles = (new User())->getAllUsersProfilesBellow($_SESSION['RR']->profile->access);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menus.php';
        require APP . 'view/_templates/footer.php';
    }

    public function updateMenu(int $profileId, int $menuId): void
    {
        $response = (new MenuAccess())->updateProfileMenu($profileId, $menuId);
        $cache = new FilesystemAdapter();
        $cache->clear();

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/menus/#{$profileId}");
    }

}
