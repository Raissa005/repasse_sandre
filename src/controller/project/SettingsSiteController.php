<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Secure;
use RR\model\SettingsSite;
use PDOException;
use RR\model\HomeCategoryLink;
use RR\model\ImmovableResource;
use RR\model\WebsiteFilters;

use function RR\Controller\redirect;

class SettingsSiteController extends FrontController
{
    public $dir;
    public $route;
    private $model;
    private $table;
    private $config;

    public function __construct()
    {
        $this->route = 'settings-site';
        $this->dir = 'settings-site';
        $this->model = new SettingsSite();
        $this->table = 'configuracao';
        $this->config = $this->model->getItemById(1);
        parent::__construct($this->route);
    }

    private function navTabs()
    {
        $nav_tabs = [
            (object)['text' => 'Dados Gerais', 'route' => URL . "{$this->route}", 'class' => ($_GET['url'] == "{$this->route}" ? 'active' : '')],
            (object)['text' => 'Imagens', 'route' => URL . "{$this->route}/images/", 'class' => ($_GET['pg1'] == "images" ? 'active' : '')],
            (object)['text' => 'Popup', 'route' => URL . "{$this->route}/popup/1", 'class' => ($_GET['pg1'] == "popup" ? 'active' : '')],
            (object)['text' => 'E-mails', 'route' => URL . "{$this->route}/emails/1", 'class' => ($_GET['pg1'] == "emails" ? 'active' : '')],
            (object)['text' => 'Meta Tags', 'route' => URL . "{$this->route}/metaTags/1", 'class' => ($_GET['pg1'] == "metaTags" ? 'active' : '')],
            (object)['text' => 'Cores', 'route' => URL . "{$this->route}/color/1", 'class' => ($_GET['pg1'] == "color" ? 'active' : '')],
            (object)['text' => 'Script', 'route' => URL . "{$this->route}/script/1", 'class' => ($_GET['pg1'] == "script" ? 'active' : '')],
            (object)['text' => 'Imóveis', 'route' => URL . "{$this->route}/layout/1", 'class' => ($_GET['pg1'] == "layout" ? 'active' : '')],
            (object)['text' => 'Página Inicial', 'route' => URL . "{$this->route}/home/1", 'class' => ($_GET['pg1'] == "home" ? 'active' : '')],
            (object)['text' => 'Filtros', 'route' => URL . "{$this->route}/filters/1", 'class' => ($_GET['pg1'] == "filters" ? 'active' : '')],
        ];

        return $nav_tabs;
    }

    public function index()
    {
        Secure::access_admin(true);
        
        $this->addScript(URL . "js/" . JSVERSION . "/settingsSite.js");
        $settingsModel = new SettingsSite();
        $settings = $settingsModel->getSettingById('1');

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitGeneralSettings()
    {
        Secure::check_post_method($this->route);

        $arrPost = array(
            'status_site' => $_POST['status_site'],
            'nome' => $_POST['nome'],
            'google_analytics' => $_POST['google_analytics'],
            'telefone' => $_POST['telefone'],
            'endereco' => $_POST['endereco'],
            'rodape' => $_POST['rodape'],
            'localizacao' => $_POST['localizacao'],
            'localizacao2' => $_POST['localizacao2'],
            'tawk' => $_POST['tawk'],
            'whatsapp_link' => '55' . $_POST['whatsapp_link'],
            'central_atendimento' => $_POST['central_atendimento'],
            'praia_sonho' => $_POST['praia_sonho'],
            'modulo_menu_filtros_imovel' => $_POST['modulo_menu_filtros_imovel'],
            'codigo' => $_POST['codigo'],
            'codigo_tipo' => $_POST['codigo_tipo'],
            'cronograma' => $_POST['cronograma'],
            'andamento' => $_POST['andamento'],
            'creci' => $_POST['creci'],
            'whatsapp_tamanho_botao' => $_POST['whatsapp_tamanho_botao'],
            'whatsapp_direcao' => $_POST['whatsapp_direcao']
        );

        try {
            $response = $this->model->update($arrPost, 'id', 1);

            $_SESSION['RR']->toast = (object)[
                'icon' => ($response->error === true ? 'error' : 'success'),
                'title' => $response->message,
            ];

            redirect($this->route);
        } catch (PDOException $error) {
            redirect($this->route);
        }
    }

    public function images()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/settingsSite.js");
        $settingsModel = new SettingsSite();
        $settings = $settingsModel->getSettingById(1);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/images.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitImages()
    {
        if (isset($_FILES)) {
            $settingId = 1;
            $gerenciaPost = new GerenciaPost();
            $settingModel = new SettingsSite();

            try {
                $setting = $settingModel->getSettingById($settingId);

                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                if (!empty($_FILES['logo_site']['tmp_name'])) {
                    if (!file_exists("img/site_imgs/settings/")) {
                        mkdir("img/site_imgs/settings/", 0777, true);
                    }
                    @unlink("img/site_imgs/settings/logo_site-$setting->cont_logo.png");

                    $arrImage = array("cont_logo" => ++$setting->cont_logo, "capa_logo" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $settingId, false);

                    $extension = str_replace(".", "", substr($_FILES['logo_site']['name'], -4));
                    $filename = $_FILES['logo_site']['tmp_name'];
                    $path = "img/site_imgs/settings/";

                    $size = getimagesize($_FILES['logo_site']['tmp_name']);
                    if ($size[0] == 220 && $size[1] == 115) {
                        copy($_FILES['logo_site']['tmp_name'], $path . "logo_site-" . $setting->cont_logo . ".$extension");
                    } else {
                        $logo_site = wideImagePhoto($filename, $path, 220, 115, "logo_site-" . $setting->cont_logo, ".$extension", 9);
                        // $logo_site = resize1($path, "logo_site", 211, 179, 1, "-" . $setting->cont_logo, $filename, $extension);
                    }
                }

                if (!empty($_FILES['logo_rodape']['tmp_name'])) {
                    if (!file_exists("img/site_imgs/settings/")) {
                        mkdir("img/site_imgs/settings/", 0777, true);
                    }
                    @unlink("img/site_imgs/settings/logo_rodape-$setting->cont_rodape.png");

                    $arrImage = array("cont_rodape" => ++$setting->cont_rodape, "capa_rodape" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $settingId, false);

                    $extension = str_replace(".", "", substr($_FILES['logo_rodape']['name'], -4));
                    $filename = $_FILES['logo_rodape']['tmp_name'];
                    $path = "img/site_imgs/settings/";

                    $size = getimagesize($_FILES['logo_rodape']['tmp_name']);
                    if ($size[0] == 220 && $size[1] == 115) {
                        copy($_FILES['logo_rodape']['tmp_name'], $path . "logo_rodape-" . $setting->cont_rodape . ".$extension");
                    } else {
                        $logo_rodape = wideImagePhoto($filename, $path, 220, 115, "logo_rodape-" . $setting->cont_rodape, ".$extension", 9);
                        // $logo_rodape = resize1($path, "logo_rodape", 143, 83, 1, "-" . $setting->cont_rodape, $filename, $extension);
                    }
                }

                if (!empty($_FILES['logo_share']['tmp_name'])) {
                    if (!file_exists("img/site_imgs/settings/")) {
                        mkdir("img/site_imgs/settings/", 0777, true);
                    }
                    @unlink("img/site_imgs/settings/logo_share-$setting->cont_share.png");

                    $arrImage = array("cont_share" => ++$setting->cont_share, "capa_share" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $settingId, false);

                    $extension = str_replace(".", "", substr($_FILES['logo_share']['name'], -4));
                    $filename = $_FILES['logo_share']['tmp_name'];
                    $path = "img/site_imgs/settings/";

                    $size = getimagesize($_FILES['logo_share']['tmp_name']);
                    if ($size[0] == 200 && $size[1] == 200) {
                        copy($_FILES['logo_share']['tmp_name'], $path . "logo_share-" . $setting->cont_share . ".$extension");
                    } else {
                        $logo_share = wideImagePhoto($filename, $path, 200, 200, "logo_share-" . $setting->cont_share, ".$extension", 9);
                        // $logo_share = resize1($path, "logo_share", 200, 200, 1, "-" . $setting->cont_share, $filename, $extension);
                    }
                }

                if (!empty($_FILES['favicon']['tmp_name'])) {
                    if (!file_exists("img/site_imgs/settings/")) {
                        mkdir("img/site_imgs/settings/", 0777, true);
                    }
                    @unlink("img/site_imgs/settings/favicon-$setting->cont_favicon.png");

                    $arrImage = array("cont_favicon" => ++$setting->cont_favicon, "capa_favicon" => true);
                    $gerenciaPost->update8191($arrImage, $this->table, "id", $settingId, false);

                    $extension = str_replace(".", "", substr($_FILES['favicon']['name'], -4));
                    $filename = $_FILES['favicon']['tmp_name'];
                    $path = "img/site_imgs/settings/";

                    $size = getimagesize($_FILES['favicon']['tmp_name']);
                    if ($size[0] == 20 && $size[1] == 20) {
                        copy($_FILES['favicon']['tmp_name'], $path . "favicon-" . $setting->cont_favicon . ".$extension");
                    } else {
                        $favicon = wideImagePhoto($filename, $path, 20, 20, "favicon-" . $setting->cont_favicon, ".$extension", 9);
                        // $favicon = resize1($path, "favicon", 20, 20, 1, "-" . $setting->cont_favicon, $filename, $extension);
                    }
                }

                if ($setting->praia_sonho == 1) {
                    if (!empty($_FILES['image_praia_sonhos']['tmp_name'])) {
                        if (!file_exists("img/site_imgs/settings/")) {
                            mkdir("img/site_imgs/settings/", 0777, true);
                        }
                        @unlink("img/site_imgs/settings/praia_sonhos-{$setting->praia_cont}.{$setting->praia_ext}");

                        $extension = str_replace(".", "", substr($_FILES['image_praia_sonhos']['name'], -4));
                        $arrImage = array("praia_cont" => ++$setting->praia_cont, "praia_ext" => $extension,  "praia_capa" => true);
                        $gerenciaPost->update8191($arrImage, $this->table, "id", $settingId, false);

                        $filename = $_FILES['image_praia_sonhos']['tmp_name'];
                        $path = "img/site_imgs/settings/";

                        $size = getimagesize($_FILES['image_praia_sonhos']['tmp_name']);
                        if ($size[0] == 1920 && $size[1] == 650) {
                            copy($_FILES['image_praia_sonhos']['tmp_name'], $path . "praia_sonhos-" . $setting->praia_cont . ".$extension");
                        } else if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                            $praiaSonhos = wideImagePhoto($filename, $path, 1920, 650, "praia_sonhos-" . $setting->praia_cont, ".$extension", 100);
                            // $favicon = resize1($path, "favicon", 20, 20, 1, "-" . $setting->cont_favicon, $filename, $extension);
                        } else if ($extension == "png" || $extension == "PNG") {
                            $praiaSonhos = wideImagePhoto($filename, $path, 1920, 650, "praia_sonhos-" . $setting->praia_cont, ".$extension", 9);
                        }
                    }
                }

                header('location:' . URL . $this->route . "/images?edited=true");
                exit;
            } catch (PDOException $error) {
                header('location:' . URL . $this->route . "/images ?edited=false");
                exit;
            }
        }
    }

    public function popup($settingPopupId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/site_popup.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");
        $settingModel = new SettingsSite();

        $popup = $settingModel->getSettingById('1');

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/popup.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitPopup($settingPopupId)
    {
        Secure::check_post_method($this->route . "/popup/$settingPopupId");

        $popup = (new SettingsSite())->getSettingById('1');

        switch ($_POST['popup_tipo']) {
            case '1':
                $arrPost = array(
                    'popup_ativo' => $_POST['popup_ativo'],
                    'popup_tipo' => $_POST['popup_tipo'],
                    'popup_cor_fonte' => $_POST['popup_cor_fonte'],
                    'popup_background' => $_POST['popup_background'],
                    'popup_texto' => $_POST['popup_texto'],
                );
                break;
            case '2':
                $arrPost = array(
                    'popup_ativo' => $_POST['popup_ativo'],
                    'popup_tipo' => $_POST['popup_tipo'],
                );

                require_once APP . 'libs/wideImage/lib/WideImage.php';
                require_once APP . 'libs/wideImage/wide.php';
                require_once APP . 'libs/Resizer.php';

                if (!empty($_FILES['popup_imagem']['tmp_name'])) {
                    if (!file_exists("img/site_imgs/settings/")) {
                        mkdir("img/site_imgs/settings/", 0777, true);
                    }
                    @unlink("img/site_imgs/settings/popup-$popup->popup_imagem_cont.$popup->popup_imagem_ext");
                    $extension = str_replace(".", "", substr($_FILES['popup_imagem']['name'], -4));

                    $arrImage = array("popup_imagem_cont" => ++$popup->popup_imagem_cont, "popup_imagem_ext" => $extension, "popup_imagem_capa" => true);
                    (new GerenciaPost())->update8191($arrImage, $this->table, "id", $settingPopupId, false);

                    $filename = $_FILES['popup_imagem']['tmp_name'];
                    $path = "img/site_imgs/settings/";

                    $size = getimagesize($_FILES['popup_imagem']['tmp_name']);
                    if ($size[0] == 20 && $size[1] == 20) {
                        copy($_FILES['popup_imagem']['tmp_name'], $path . "popup-" . $popup->cont_imagem_cont . ".$extension");
                    } else {
                        // $favicon = wideImagePhoto($filename, $path, 20, 20, "favicon-" . $setting->cont_favicon, ".$extension", 9);
                        $popup = resize1($path, "popup", 1024, 1024, 1, "-" . $popup->popup_imagem_cont, $filename, $extension);
                    }
                }

                break;
            case '3':
                $arrPost = array(
                    'popup_ativo' => $_POST['popup_ativo'],
                    'popup_tipo' => $_POST['popup_tipo'],
                    'popup_video' => $_POST['popup_video'],
                );
                break;
        }

        try {
            (new GerenciaPost())->update8191($arrPost, "configuracao", 'id', $settingPopupId, false);

            header('location:' . URL . $this->route . "/popup/$settingPopupId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/popup/$settingPopupId?edited=false");
            exit;
        }
    }

    public function deleteImagePopupId($imagePopupId)
    {
        $settingModel = new SettingsSite();
        $popup = $settingModel->getSettingById($imagePopupId);

        try {
            @unlink("img/site_imgs/banners/" . "popup" . "-" . $popup->popup_imagem_cont . "." . $popup->popup_imagem_ext);

            $arrPost = array("popup_imagem_capa" => 0, "popup_imagem_cont" => ++$popup->popup_imagem_cont);
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", $imagePopupId, false);

            header('location:' . URL . $this->route . "/popup/$imagePopupId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/popup/$imagePopupId?edited=false");
            exit;
        }
    }

    public function home($settingHomeId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");

        $categories_columns = [
            (object)[
                'columns' => ['id', 'status', 'item_order']
            ],
            (object)[
                'table' => 'property_category',
                'columns' => ['name'],
            ],
        ];

        $categories_options = [
            'orderBy' => 'home_category_link.item_order ASC'
        ];

        $categories = (new HomeCategoryLink())->getWithFiltersAllItems([], $categories_columns, $categories_options)->data;

        array_map(function ($category) {
            if ($category->status == true) {
                $category->labelClass = 'green';
                $category->labelText = 'Disponível';
            } else {
                $category->labelClass = 'gray';
                $category->labelText = 'Inativo';
            }
        }, $categories);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/home.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleDisableItem($homeCategoryId)
    {
        $response = (new HomeCategoryLink())->update(['status' => '0', 'updated_by' => $_SESSION['RR']->user->id], 'id', $homeCategoryId);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/home/1");
    }

    public function handleEnableItem($homeCategoryId)
    {
        $response = (new HomeCategoryLink())->update(['status' => '1', 'updated_by' => $_SESSION['RR']->user->id], 'id', $homeCategoryId);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/home/1");
    }

    public function disableAllCategories()
    {
        $categories = (new HomeCategoryLink())->getWithFiltersAllItems([], [(object)['columns' => ['id']]])->data;

        foreach ($categories as $category) {
            $response = (new HomeCategoryLink())->update(['status' => '0', 'updated_by' => $_SESSION['RR']->user->id], 'id', $category->id);
        }

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/home/1");
    }

    public function ableAllCategories()
    {
        $categories = (new HomeCategoryLink())->getWithFiltersAllItems([], [(object)['columns' => ['id']]])->data;

        foreach ($categories as $category) {
            $response = (new HomeCategoryLink())->update(['status' => '1', 'updated_by' => $_SESSION['RR']->user->id], 'id', $category->id);
        }

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/home/1");
    }

    public function filters($id)
    {
        $nav_tabs = Self::navTabs();
        $content_header = (object)[
            'title' => "Configurações",
            'subtitle' => 'Filtros',
            'buttons' => []
        ];

        $filters = (new WebsiteFilters)->getWithFiltersAllItems();

        $arrFilters = [
            'tipo' => (object)[
                'id' => 1,
                'name' => 'Tipo',
            ],
            'categoria' => (object)[
                'id' => 2,
                'name' => 'Categoria',
            ],
            'cidade' => (object)[
                'id' => 3,
                'name' => 'Cidade',
            ],
            'bairro' => (object)[
                'id' => 4,
                'name' => 'Bairro',
            ],
            'text' => (object)[
                'id' => 5,
                'name' => 'Texto',
            ],
            'valor_minimo' => (object)[
                'id' => 6,
                'name' => 'Valor de',
            ],
            'valor_maximo' => (object)[
                'id' => 7,
                'name' => 'Valor até',
            ]
        ];

        $lastId = count($arrFilters) + 1;

        array_map(function ($resource) use (&$arrFilters, &$lastId) {
            $arrFilters[$resource->slugify] = (object)[
                'id' => $lastId,
                'name' => $resource->name,
            ];
            $lastId++;
        }, (new ImmovableResource)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1], 'site_filter' => (object)['value' => 1]]]])->data);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/filters.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitFilters($id)
    {
        $response = (new WebsiteFilters)->submitEditFilters();

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect("{$this->route}/filters/{$id}");
    }

    public function emails($settingEmailId)
    {
        $settingModel = new SettingsSite();
        $email = $settingModel->getSettingEmailById($settingEmailId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/emails.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEmail($settingEmailId)
    {
        Secure::check_post_method($this->route . "/emails/$settingEmailId");

        $arrPost = array(
            'nome' => $_POST['nome'],
            'email' => $_POST['email'],
            'smtp' => $_POST['host'],
            'porta' => $_POST['porta'],
            'seguranca' => $_POST['seguranca'],
            'destinatario' => $_POST['destinatario'],
        );

        if ($_POST['senha'] != "") {
            $arrPost['senha2'] = $_POST['senha'];
        }

        try {
            (new GerenciaPost())->update8191($arrPost, "configuracao_email", 'id', $settingEmailId, false);

            header('location:' . URL . $this->route . "/emails/$settingEmailId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/emails/$settingEmailId?edited=false");
            exit;
        }
    }

    public function metaTags($metaTagsId)
    {
        $modelGenerico = new ModelGenerico();
        $settingModel = new SettingsSite();
        $metaTags = $settingModel->getSettingMetaTagsById($metaTagsId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/metaTags.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitSettingMetaTags($metaTagsId)
    {
        Secure::check_post_method($this->route);

        $arrPost = array(
            'palavra_chave' => $_POST['palavra_chave'],
            'descricao' => $_POST['descricao'],
            'frase_curta' => $_POST['frase_curta'],
        );

        try {
            (new GerenciaPost())->update8191($arrPost, "configuracao_metatags", 'id', $metaTagsId, false);

            header('location:' . URL . $this->route . "/metaTags/$metaTagsId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/metaTags/$metaTagsId?edited=false");
            exit;
        }
    }

    public function color($colorId)
    {
        $settingModel = new SettingsSite();
        $color = $settingModel->getSettingById($colorId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/color.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitColor($colorId)
    {
        Secure::check_post_method($this->route . "/color/$colorId/?error=error");

        $arrPost = array(
            "base_cor_fonte" => $_POST["base_cor_fonte"],
            "filtro_botao_cor_fonte" => $_POST["filtro_botao_cor_fonte"],
            "filtro_botao_cor_fonte_efeito" => $_POST["filtro_botao_cor_fonte_efeito"],
            "filtro_botao_cor_fundo" => $_POST["filtro_botao_cor_fundo"],
            "filtro_botao_cor_fundo_efeito" => $_POST["filtro_botao_cor_fundo_efeito"],
            "imovel_box_botao_cor_fonte" => $_POST["imovel_box_botao_cor_fonte"],
            "imovel_box_botao_cor_fonte_efeito" => $_POST["imovel_box_botao_cor_fonte_efeito"],
            "imovel_box_botao_cor_fundo" => $_POST["imovel_box_botao_cor_fundo"],
            "imovel_box_botao_cor_fundo_efeito" => $_POST["imovel_box_botao_cor_fundo_efeito"],
            "topo_cor_fundo" => $_POST["topo_cor_fundo"],
            "topo_cor_fonte" => $_POST["topo_cor_fonte"],
            "topo_botao_cor" => $_POST["topo_botao_cor"],
            "topo_botao_cor_efeito" => $_POST["topo_botao_cor_efeito"],
            "menu_cor_fundo" => $_POST["menu_cor_fundo"],
            "menu_cor_fonte" => $_POST["menu_cor_fonte"],
            "menu_cor_fonte_efeito" => $_POST["menu_cor_fonte_efeito"],
            "banner_cor_fundo" => $_POST["banner_cor_fundo"],
            "filtro_cor_fundo" => $_POST["filtro_cor_fundo"],
            "filtro_campo_cor_fonte" => $_POST["filtro_campo_cor_fonte"],
            "filtro_campo_cor_fundo" => $_POST["filtro_campo_cor_fundo"],
            "filtro_valor_cor_fundo" => $_POST["filtro_valor_cor_fundo"],
            "filtro_botao_avancado_cor_fonte" => $_POST["filtro_botao_avancado_cor_fonte"],
            "filtro_botao_avancado_cor_fonte_efeito" => $_POST["filtro_botao_avancado_cor_fonte_efeito"],
            "empresa_home_cor_fundo" => $_POST["empresa_home_cor_fundo"],
            "empresa_home_apoio_titulo_cor_fonte" => $_POST["empresa_home_apoio_titulo_cor_fonte"],
            "empresa_home_titulo_cor_fonte" => $_POST["empresa_home_titulo_cor_fonte"],
            "empresa_home_texto_cor_fonte" => $_POST["empresa_home_texto_cor_fonte"],
            // "empresa_home_botao_cor_fonte" => $_POST["empresa_home_botao_cor_fonte"],
            // "empresa_home_botao_cor_fonte_efeito" => $_POST["empresa_home_botao_cor_fonte_efeito"],
            // "empresa_home_botao_cor_fundo" => $_POST["empresa_home_botao_cor_fundo"],
            // "empresa_home_botao_cor_fundo_efeito" => $_POST["empresa_home_botao_cor_fundo_efeito"],
            // "empresa_home_botao_cor_borda" => $_POST["empresa_home_botao_cor_borda"],
            "contato_home_cor_fundo" => $_POST["contato_home_cor_fundo"],
            "contato_home_apoio_titulo_cor_fonte" => $_POST["contato_home_apoio_titulo_cor_fonte"],
            "contato_home_titulo_cor_fonte" => $_POST["contato_home_titulo_cor_fonte"],
            // "contato_home_botao_cor_fonte" => $_POST["contato_home_botao_cor_fonte"],
            // "contato_home_botao_cor_fonte_efeito" => $_POST["contato_home_botao_cor_fonte_efeito"],
            // "contato_home_botao_cor_fundo" => $_POST["contato_home_botao_cor_fundo"],
            // "contato_home_botao_cor_fundo_efeito" => $_POST["contato_home_botao_cor_fundo_efeito"],
            "contato_home_box_cor_fundo" => $_POST["contato_home_box_cor_fundo"],
            "contato_home_box_cor_sombra" => $_POST["contato_home_box_cor_sombra"],
            "contato_home_box_cor_fonte" => $_POST["contato_home_box_cor_fonte"],
            // "mapa_botao_cor_fonte" => $_POST["mapa_botao_cor_fonte"],
            // "mapa_botao_cor_fonte_efeito" => $_POST["mapa_botao_cor_fonte_efeito"],
            // "mapa_botao_cor_fundo" => $_POST["mapa_botao_cor_fundo"],
            // "mapa_botao_cor_fundo_efeito" => $_POST["mapa_botao_cor_fundo_efeito"],
            "rodape_cor_fundo" => $_POST["rodape_cor_fundo"],
            "rodape_cor_fonte" => $_POST["rodape_cor_fonte"],
            "rodape_cor_fonte_efeito" => $_POST["rodape_cor_fonte_efeito"],
            "direito_cor_fundo" => $_POST["direito_cor_fundo"],
            "direito_cor_fonte" => $_POST["direito_cor_fonte"],
            "direito_cor_fonte_efeito" => $_POST["direito_cor_fonte_efeito"],
            "imovel_cor_fundo" => $_POST["imovel_cor_fundo"],
            "imovel_apoio_titulo_cor_fonte" => $_POST["imovel_apoio_titulo_cor_fonte"],
            "imovel_titulo_cor_fonte" => $_POST["imovel_titulo_cor_fonte"],
            "imovel_box_cor_fundo" => $_POST["imovel_box_cor_fundo"],
            "imovel_box_cor_fonte" => $_POST["imovel_box_cor_fonte"],
            "imovel_box_titulo_cor_fonte" => $_POST["imovel_box_titulo_cor_fonte"],
            "imovel_box_tag_cor_fonte" => $_POST["imovel_box_tag_cor_fonte"],
            "imovel_box_tag_cor_fundo" => $_POST["imovel_box_tag_cor_fundo"],
            "imovel_mais_imoveis_cor_fonte" => $_POST["imovel_mais_imoveis_cor_fonte"],
            "imovel_mais_imoveis_cor_fonte_efeito" => $_POST["imovel_mais_imoveis_cor_fonte_efeito"],
            "imovel_mais_imoveis_cor_fundo" => $_POST["imovel_mais_imoveis_cor_fundo"],
            "imovel_mais_imoveis_cor_fundo_efeito" => $_POST["imovel_mais_imoveis_cor_fundo_efeito"],
            "pagina_cor_fundo" => $_POST["pagina_cor_fundo"],
            "pagina_apoio_titulo_cor" => $_POST["pagina_apoio_titulo_cor"],
            "pagina_titulo_cor" => $_POST["pagina_titulo_cor"],
            "pagina_cor_fonte" => $_POST["pagina_cor_fonte"],
            "imovel_box_detalhe_cor_fundo" => $_POST['imovel_box_detalhe_cor_fundo'],
            "imovel_box_detalhe_cor_fonte" => $_POST['imovel_box_detalhe_cor_fonte'],
            "central_atendimento_cor_fundo" => $_POST['central_atendimento_cor_fundo'],
            "central_atendimento_cor_fonte" => $_POST['central_atendimento_cor_fonte'],
            "central_atendimento_cor_fonte_efeito" => $_POST['central_atendimento_cor_fonte_efeito'],
            "praia_sonho_cor_fundo" => $_POST['praia_sonho_cor_fundo'],
            "praia_sonho_cor_fonte" => $_POST['praia_sonho_cor_fonte'],
            "praia_sonho_cor_fonte_efeito" => $_POST['praia_sonho_cor_fonte_efeito'],
            "filtro_lateral_cor_fonte" => $_POST["filtro_lateral_cor_fonte"],
            "filtro_lateral_cor_fonte_efeito" => $_POST["filtro_lateral_cor_fonte_efeito"],
            "filtro_and_cor_fonte" => $_POST["filtro_and_cor_fonte"],
            "filtro_and_cor_fonte_efeito" => $_POST["filtro_and_cor_fonte_efeito"],
            "box_galeria_cor_fundo" => $_POST["box_galeria_cor_fundo"],
            "filtro_cor_barra_lateral_subtitulo" => $_POST["filtro_cor_barra_lateral_subtitulo"],
            "filtro_cor_barra_lateral_titulo" => $_POST["filtro_cor_barra_lateral_titulo"],
            "whatsapp_cor_botao" => !empty($_POST["whatsapp_cor_botao"]) ? $_POST["whatsapp_cor_botao"] : '#147118',
            "cor_caixa_contato" => !empty($_POST["cor_caixa_contato"]) ? $_POST["cor_caixa_contato"] : '#fff',
            "cor_fonte_contato" => !empty($_POST["cor_fonte_contato"]) ? $_POST["cor_fonte_contato"] : '#515151',
            "cor_titulo_contato" => !empty($_POST["cor_titulo_contato"]) ? $_POST["cor_titulo_contato"] : '#353535',
        );

        try {
            (new GerenciaPost())->update8191($arrPost, "configuracao", 'id', $colorId, false, false);

            header('location:' . URL . $this->route . '/color/' . $colorId . "/?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/color/' . $colorId . "/?edited=false");
            exit;
        }
    }

    public function script($scriptId)
    {
        $modelGenerico = new ModelGenerico();
        $settingModel = new SettingsSite();
        $script = $settingModel->getSettingScriptById($scriptId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/script.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitScript($scriptId)
    {
        Secure::check_post_method($this->route . "/script/$scriptId");

        $arrPost = array(
            'script_header' => $_POST['script_header'],
            'script_body_top' => $_POST['script_body_top'],
            'script_body_bottom' => $_POST['script_body_bottom'],
        );

        try {
            (new GerenciaPost())->update8191($arrPost, "configuracao", 'id', $scriptId, false);

            header('location:' . URL . $this->route . "/script/$scriptId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/script/$scriptId?edited=false");
            exit;
        }
    }

    public function layout($layoutId)
    {
        $this->addStyle(URL . "css/" . CSSVERSION . "/layout-site/button.css");
        $this->addStyle(URL . "css/" . CSSVERSION . "/layout-site/layoutStyle.css");
        $configStyle = (new SettingsSite)->getAllSiteSettings();

        $settingsModel = new SettingsSite();
        $layout = $settingsModel->getSettingLayoutById($layoutId);

        $image = URL . "img/products_imgs/default/layout-example.png";

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/layout-style.php';
        require APP . 'view/' . $this->dir . '/layout.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitLayoutSettings($layoutId)
    {
        Secure::check_post_method($this->route . "/layout/$layoutId");

        $arrPost = array(
            'layout_top_left' => $_POST['layout_top_left'],
            'layout_bottom_left' => $_POST['layout_bottom_left'],
            'layout_bottom_right' => $_POST['layout_bottom_right'],
            'layout_desc_value' => $_POST['layout_desc_value']
        );

        try {
            (new GerenciaPost())->update8191($arrPost, "configuracao", 'id', $layoutId, false);

            header('location:' . URL . $this->route . "/layout/$layoutId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/layout/$layoutId?edited=false");
            exit;
        }
    }
}
