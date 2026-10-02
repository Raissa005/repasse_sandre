<?php

namespace RR\controller\project;

use PDOException;
use RR\libs\Util;
use RR\model\User;
use RR\libs\Secure;
use RR\libs\Toast;
use RR\libs\FileUploader;
use RR\model\Menu;
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

    public function __construct()
    {
        $this->route = 'settings';
        $this->dir = 'settings';
        $this->model = new SystemSettings();
        $this->table = 'system_config';
        parent::__construct($this->route);
    }

    private function navTabs()
    {
        $navTabs = [
            (object)['text' => 'Dados Gerais', 'route' => URL . $this->route . '/', 'class' => ($_GET['url'] == "{$this->route}/" ? 'active' : '')],
            (object)['text' => 'Imagens', 'route' => URL . $this->route . '/images/', 'class' => ($_GET['url'] == $this->route . '/images/' ? 'active' : '')],
        ];

        // C3: permissões de tela só para Superadm, Administrador e Desenvolvedor
        if (Secure::access_admin()) {
            array_push($navTabs, (object)['text' => 'Menus', 'route' => URL . $this->route . '/menus/', 'class' => ($_GET['url'] == $this->route . '/menus/' ? 'active' : '')]);
        }

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
        Secure::check_post_method($this->route);

        $arrPost = array(
            'title' => $_POST['title'],
            'footer' => $_POST['footer'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", 1, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route);
        exit;
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
        foreach (['logo_menu', 'logo_mini', 'logo_login', 'logo_favicon', 'logo_rodape'] as $field) {
            if ($sizeError = FileUploader::sizeLimitError($_FILES[$field] ?? null, FileUploader::MAX_SIZE_IMAGE)) {
                Toast::warningToast($sizeError);
                redirect($this->route . "/images");
            }

            if ($typeError = FileUploader::typeError($_FILES[$field] ?? null, FileUploader::ALLOWED_IMAGE)) {
                Toast::warningToast($typeError);
                redirect($this->route . "/images");
            }
        }

        if (isset($_FILES)) {
            $gerenciaPost = new GerenciaPost();
            $setting = (new SystemSettings())->getItemById8161();
            try {

                $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
                $invalidExtension = false;

                if (!empty($_FILES['logo_menu']['tmp_name'])) {

                    $extension = FileUploader::allowedExtension($_FILES['logo_menu']['name'], $_FILES['logo_menu']['tmp_name'], FileUploader::ALLOWED_IMAGE);
                    $size = @getimagesize($_FILES['logo_menu']['tmp_name']);

                    if ($extension !== null && $size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_menu']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_menu_cont + 1;

                            // Só apaga a imagem antiga e aponta o banco para a nova depois que o arquivo existe
                            if (Util::resizeImageCrop($filename, 230, 50, $path . "logo_menu-" . $newCont . ".$extension")) {
                                @unlink("img/settings/logo_menu-$setting->logo_menu_cont.$setting->logo_menu_ext");
                                $setting->logo_menu_cont = $newCont;
                                $gerenciaPost->update8191(["logo_menu_cont" => $newCont, "logo_menu_ext" => $extension, "logo_menu_capa" => true], $this->table, "id", 1, false);
                            } else {
                                $invalidExtension = true;
                            }
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_mini']['tmp_name'])) {

                    $extension = FileUploader::allowedExtension($_FILES['logo_mini']['name'], $_FILES['logo_mini']['tmp_name'], FileUploader::ALLOWED_IMAGE);
                    $size = @getimagesize($_FILES['logo_mini']['tmp_name']);

                    if ($extension !== null && $size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_mini']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_mini_cont + 1;

                            // Só apaga a imagem antiga e aponta o banco para a nova depois que o arquivo existe
                            if (Util::resizeImageCrop($filename, 50, 50, $path . "logo_mini-" . $newCont . ".$extension")) {
                                @unlink("img/settings/logo_mini-$setting->logo_mini_cont.$setting->logo_mini_ext");
                                $setting->logo_mini_cont = $newCont;
                                $gerenciaPost->update8191(["logo_mini_cont" => $newCont, "logo_mini_ext" => $extension, "logo_mini_capa" => true], $this->table, "id", 1, false);
                            } else {
                                $invalidExtension = true;
                            }
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_login']['tmp_name'])) {

                    $extension = FileUploader::allowedExtension($_FILES['logo_login']['name'], $_FILES['logo_login']['tmp_name'], FileUploader::ALLOWED_IMAGE);
                    $size = @getimagesize($_FILES['logo_login']['tmp_name']);

                    if ($extension !== null && $size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_login']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_login_cont + 1;

                            // Só apaga a imagem antiga e aponta o banco para a nova depois que o arquivo existe
                            if (Util::resizeImageCrop($filename, 320, 100, $path . "logo_login-" . $newCont . ".$extension")) {
                                @unlink("img/settings/logo_login-$setting->logo_login_cont.$setting->logo_login_ext");
                                $setting->logo_login_cont = $newCont;
                                $gerenciaPost->update8191(["logo_login_cont" => $newCont, "logo_login_ext" => $extension, "logo_login_capa" => true], $this->table, "id", 1, false);
                            } else {
                                $invalidExtension = true;
                            }
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_favicon']['tmp_name'])) {

                    $extension = FileUploader::allowedExtension($_FILES['logo_favicon']['name'], $_FILES['logo_favicon']['tmp_name'], FileUploader::ALLOWED_IMAGE);
                    $size = @getimagesize($_FILES['logo_favicon']['tmp_name']);

                    if ($extension !== null && $size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_favicon']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_favicon_cont + 1;

                            // Só apaga a imagem antiga e aponta o banco para a nova depois que o arquivo existe
                            if (Util::resizeImageCrop($filename, 20, 20, $path . "logo_favicon-" . $newCont . ".$extension")) {
                                @unlink("img/settings/logo_favicon-$setting->logo_favicon_cont.$setting->logo_favicon_ext");
                                $setting->logo_favicon_cont = $newCont;
                                $gerenciaPost->update8191(["logo_favicon_cont" => $newCont, "logo_favicon_ext" => $extension, "logo_favicon_capa" => true], $this->table, "id", 1, false);
                            } else {
                                $invalidExtension = true;
                            }
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if (!empty($_FILES['logo_rodape']['tmp_name'])) {

                    $extension = FileUploader::allowedExtension($_FILES['logo_rodape']['name'], $_FILES['logo_rodape']['tmp_name'], FileUploader::ALLOWED_IMAGE);
                    $size = @getimagesize($_FILES['logo_rodape']['tmp_name']);

                    if ($extension !== null && $size !== false && in_array($size[2], $allowedImageTypes)) {
                        try {
                            if (!file_exists("img/settings/")) {
                                mkdir("img/settings/", 0777, true);
                            }

                            $filename = $_FILES['logo_rodape']['tmp_name'];
                            $path = "img/settings/";
                            $newCont = $setting->logo_rodape_cont + 1;

                            // Só apaga a imagem antiga e aponta o banco para a nova depois que o arquivo existe
                            if (Util::resizeImageCrop($filename, 220, 115, $path . "logo_rodape-" . $newCont . ".$extension")) {
                                @unlink("img/settings/logo_rodape-$setting->logo_rodape_cont.$setting->logo_rodape_ext");
                                $setting->logo_rodape_cont = $newCont;
                                $gerenciaPost->update8191(["logo_rodape_cont" => $newCont, "logo_rodape_ext" => $extension, "logo_rodape_capa" => true], $this->table, "id", 1, false);
                            } else {
                                $invalidExtension = true;
                            }
                        } catch (\Throwable $error) {
                            $invalidExtension = true;
                        }
                    } else {
                        $invalidExtension = true;
                    }
                }

                if ($invalidExtension) {
                    Toast::errorToast('Não foi possível salvar a imagem: extensão ou dimensão inválida');
                    header('location:' . URL . $this->route . "/images");
                    exit;
                }

                Toast::itemEdited();
                header('location:' . URL . $this->route . "/images");
                exit;
            } catch (PDOException $error) {
                Toast::itemEditError();
                header('location:' . URL . $this->route . '/images');
                exit;
            }
        }

        Toast::genericError();
        header('location:' . URL . $this->route . "/images");
        exit;
    }

    public function menus(): void
    {
        Secure::access_admin(true);
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
        Secure::access_admin(true);

        // C3: só os perfis que a tela lista para quem está editando (o Administrador não vê Superadm nem Desenvolvedor)
        $allowedProfiles = array_map(function ($profile) {
            return (int) $profile->id;
        }, (new User())->getAllUsersProfilesBellow($_SESSION['RR']->profile->access));

        if (!in_array($profileId, $allowedProfiles, true)) {
            Toast::warningToast('Sem permissão para alterar as permissões deste perfil.');
            redirect("{$this->route}/menus");
        }

        // C3: fora o Superadm, ninguém desativa "Configurações" do próprio perfil (perderia o acesso a esta tela)
        $settingsMenu = (new Menu())->getMenuByRoute($this->route);
        $currentAccess = (new Menu())->getMenuAccess($menuId, $profileId)->data;
        $deactivating = !empty($currentAccess) && $currentAccess[0]->status == 1;

        if (!Secure::access_superAdm() && $deactivating && $profileId == $_SESSION['RR']->profile->id
            && !empty($settingsMenu) && in_array($menuId, [(int) $settingsMenu->id, (int) $settingsMenu->id_menu_parent], true)) {
            Toast::warningToast('Não é possível desativar "Configurações" para o seu próprio perfil.');
            redirect("{$this->route}/menus/#{$profileId}");
        }

        $response = (new MenuAccess())->updateProfileMenu($profileId, $menuId);
        $cache = new FilesystemAdapter();
        $cache->clear();

        Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/menus/#{$profileId}");
    }

}
