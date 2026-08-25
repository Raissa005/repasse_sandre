<?php

namespace RR\controller\project;

use RR\libs\BoxAlert;
use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\model\Branch;
use RR\model\PropertyType;
use RR\model\ImmovableResource;
use PDOException;
use RR\libs\Pagination;
use RR\model\PropertyOwnershipFeature;
use RR\model\PropertyTypeResources;

use function RR\Controller\redirect;

class PropertyTypeController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'property-type';
        $this->dir = 'property-type';
        $this->model = new PropertyType();
        $this->table = 'property_type';
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

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        $immovableResources =  (new ImmovableResource())->getAndFilterAllItem(['status' => 1], 0)->data;

        $popOver = "data-toggle='popover'
                    data-trigger='focus'
                    data-animation='true'
                    data-original-title='<b>Informações</b>'
                    data-content='<strong> Adicionais </strong>'";

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $response = $this->model->submitAddForm($_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . '/editItem/' . $response->lastId);
    }

    public function editItem($itemId)
    {
        $propertyTypeModel = new PropertyType();
        $immovableResourceModel = new ImmovableResource();

        $item = $propertyTypeModel->getItemById8161($itemId);
        $immovableResources =  $immovableResourceModel->getAndFilterAllItem(['status' => 1], 0)->data;
        $immovableResourcesChecked = $propertyTypeModel->getAllPropertyTypeResourceByIdItem($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $response = $this->model->submitEditForm($itemId, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        redirect($this->route . "/editItem/$itemId");
    }

    public function disableItem($itemId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem($itemId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function addAliases($idItem)
    {
        $propertyType = $this->model->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $idItem]]]]);

        $found = false;
        if (!empty($propertyType->alias)) {
            $aliases = explode(', ', $propertyType->alias);
            foreach ($aliases as $item) {
                $item == $_POST['alias'] ? $found = true : $found = false;
                if ($found) break;
            }
            if (!$found) {
                $concat = $propertyType->alias . ", {$_POST['alias']}";
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
