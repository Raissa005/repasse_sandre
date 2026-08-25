<?php

namespace RR\controller\project;

use Exception;
use RR\libs\BoxAlert;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\ImmovableResource;
use RR\model\PropertyType;
use PDOException;
use RR\libs\Pagination;

class ImmovableResourceController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'immovable-resource';
        $this->dir = 'immovable-resource';
        $this->model = new ImmovableResource();
        $this->table = 'immovable_resource';
        parent::__construct($this->route);

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

        array_map(function ($i) {
            switch ($i->data_type) {
                case '1':
                    $i->data_type_name = 'Texto';
                    break;
                case '2':
                    $i->data_type_name = "Número";
                    break;
                case '3':
                    $i->data_type_name = "Data";
                    break;
                case '4':
                    $i->data_type_name = "Sim / Não";
                    break;
            }
        }, $items->data);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        $propertyTypes = (new PropertyType)->getAndFilterAllItem(['status' => 1], 0)->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = array(
            'name' => $_POST['name'],
            'data_type' => $_POST['data_type'],
            'slugify' => Util::slugify($_POST['name']),
            'created_by' => $_SESSION['RR']->user->id
        );

        try {
            $itemId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            foreach ($_POST['propertyType'] as $propertyType) {
                $arrPost = array('id_property_type' => $propertyType, 'id_immovable_resource' => $itemId);
                (new GerenciaPost())->insert7181($arrPost, 'property_type_resources');
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
        $propertyTypeModel = new PropertyType();
        $immovableResourceModel = new ImmovableResource();

        $item = $immovableResourceModel->getItemById8161($itemId);
        $propertyTypes = $propertyTypeModel->getAndFilterAllItem(['status' => 1], 0)->data;
        $propertyTypesChecked = $immovableResourceModel->getAllPropertyTypeResourceIdItem($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $immovableResourcesModel = new ImmovableResource();
        $gerenciaPost = new GerenciaPost();
        $modelGenerico = new ModelGenerico();

        $arrPost = array(
            'name' => $_POST['name'],
            'alias' => $_POST['alias'],
            'data_type' => $_POST['data_type'],
            'slugify' => Util::slugify($_POST['name']),
            'filter' => $_POST['status'] ? $_POST['filter'] : 0,
            'site_filter' => $_POST['status'] ? $_POST['site_filter'] : 0,
            'status' => $_POST['status'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s")
        );

        try {
            $gerenciaPost->update8191($arrPost, $this->table, 'id', $itemId, false);

            $item = $immovableResourcesModel->getItemById8161($itemId);

            if (!empty($_POST['propertyType'])) {
                $existentResources = $immovableResourcesModel->getAllPropertyTypeResourceIdItem($itemId);
                $itens = Util::compareArray($existentResources, 'id_property_type', $_POST['propertyType']);

                if (!empty($itens['add'])) {
                    foreach ($itens['add'] as $add) {
                        $arrayPost = ['id_property_type' => $add, 'id_immovable_resource' => $itemId];
                        $gerenciaPost->insert7181($arrayPost, 'property_type_resources');
                    }
                }

                if (!empty($itens['exc'])) {
                    foreach ($itens['exc'] as $exc) {
                        $modelGenerico->deleteItemByCampoGenerico("property_type_resources", "id", $exc->id);
                        $modelGenerico->deleteItemByCampoGenerico("product_ownership_feature", "id_property_type_resources", $exc->id);
                    }
                }
            } else {
                $propertyTypeResources = $modelGenerico->getItemByGenericField($itemId, "property_type_resources", "id_immovable_resource");
                foreach ($propertyTypeResources as $resources) {
                    $modelGenerico->deleteItemByCampoGenerico("product_ownership_feature", "id_property_type_resources", $resources->id);
                }
                $modelGenerico->deleteItemByCampoGenerico("property_type_resources", "id_immovable_resource", $itemId);
            }

            require_once APP . 'libs/wideImage/lib/WideImage.php';
            require_once APP . 'libs/wideImage/wide.php';
            require_once APP . 'libs/Resizer.php';

            if (!empty($_FILES['ico']['tmp_name'])) {
                if (!file_exists("img/immovable_resource_ico/$itemId/")) {
                    mkdir("img/immovable_resource_ico/$itemId/", 0777, true);
                }
                @unlink("img/immovable_resource_ico/$itemId/$itemId-$item->cont.$item->ext");

                $extension = str_replace(".", "", substr($_FILES['ico']['name'], -4));
                $arrImage = array("cont" => ++$item->cont, "ext" => $extension, "capa" => true);
                $gerenciaPost->update8191($arrImage, $this->table, "id", $itemId, false);

                $filename = $_FILES['ico']['tmp_name'];
                $path = "img/immovable_resource_ico/$itemId/";

                $size = getimagesize($_FILES['ico']['tmp_name']);
                if ($size[0] == 20 && $size[1] == 20) {
                    copy($_FILES['ico']['tmp_name'], $path . "$itemId-$item->cont.$extension");
                } else {
                    if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                        $ico = wideImagePhoto($filename, $path, 20, 20, "$itemId-$item->cont", ".$extension", 100);
                    } else if ($extension == "png" || $extension == "PNG") {
                        $ico = wideImagePhoto($filename, $path, 20, 20, "$itemId-$item->cont", ".$extension", 9);
                    }
                }
            }

            header('location:' . URL . $this->route . "/editItem/$itemId?edited=true");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId?edited=false");
            exit;
        }
    }

    public function deleteIco($itemId)
    {
        $item = (new ImmovableResource())->getItemById8161($itemId);

        try {
            @unlink("img/immovable_resource_ico/$itemId/" . $itemId . "-" . $item->cont . "." . $item->ext);

            $arrPost = array("capa" => 0, "cont" => ++$item->cont);
            (new GerenciaPost())->update8191($arrPost, $this->table, "id", $itemId, false);

            header('location:' . URL . $this->route . "/editItem/$itemId?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$itemId?deleted=false");
            exit;
        }
    }

    public function disableItem($itemId, $page)
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
    public function addAliases($idItem)
    {
        $immovableResource = $this->model->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $idItem]]]]);

        $found = false;
        if (!empty($immovableResource->alias)) {
            $aliases = explode(', ', $immovableResource->alias);
            foreach ($aliases as $item) {
                $item == $_POST['alias'] ? $found = true : $found = false;
                if ($found) break;
            }
            if (!$found) {
                $concat = $immovableResource->alias . ", {$_POST['alias']}";
                $arrPost['alias'] = $concat;
            }
        } else
            $arrPost['alias'] = $_POST['alias'];

        if (!empty($arrPost['alias'])) {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $idItem, false);
            header('location:' . URL . $this->route . "/editItem/$idItem?edited=true");
            exit;
        } else {
            header('location:' . URL . $this->route . "/editItem/$idItem?edited=false");
            exit;
        }
    }
}
