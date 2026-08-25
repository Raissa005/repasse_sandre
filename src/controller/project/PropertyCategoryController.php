<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Util;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\PropertyCategory;
use PDOException;
use RR\libs\Pagination;
use RR\model\HomeCategoryLink;

class PropertyCategoryController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'property-category';
        $this->dir = 'property-category';
        $this->model = new PropertyCategory();
        $this->table = 'property_category';
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $propertyCategory = $this->model->getAndFilterAllPropertyCategory($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($propertyCategory->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($propertyCategory->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addPropertyCategory()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addPropertyCategory.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddPropertyCategory()
    {
        Secure::check_post_method($this->route . "/addPropertyCategory");

        $countCategories = (new HomeCategoryLink())->getWithFiltersAllItems()->count;

        $arrPost = array(
            'name' => $_POST['name'],
            'site_filter' => $_POST['site_filter'],
            'slugify' => Util::slugify($_POST['name']),
            'created_by' => $_SESSION['RR']->user->id
        );

        try {
            $propertyCategoryId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            $arrHome = [
                'id_home' => 1,
                'id_property_category' => $propertyCategoryId,
                'status' => 0,
                'item_order' => $countCategories + 1,
            ];

            (new HomeCategoryLink())->insert($arrHome);

            header('location:' . URL . $this->route . "/editPropertyCategory/$propertyCategoryId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addPropertyCategory?added=false");
            exit;
        }
    }

    public function editPropertyCategory($propertyCategoryId)
    {
        $propertyCategoryModel = new PropertyCategory();
        $propertyCategory = $propertyCategoryModel->getPropertyCategoryById($propertyCategoryId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editPropertyCategory.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditPropertyCategory($propertyCategoryId)
    {
        Secure::check_post_method($this->route . "/editPropertyCategory/$propertyCategoryId");

        $arrPost = [
            'name' => $_POST['name'],
            'slugify' => Util::slugify($_POST['name']),
            'status' => $_POST['status'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        ];

        $arrPost['site_filter'] = $_POST['status'] != 0 ? $_POST['site_filter'] : 0;

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $propertyCategoryId, false);

            header('location:' . URL . $this->route . "/editPropertyCategory/$propertyCategoryId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editPropertyCategory/$propertyCategoryId?edited=false");
            exit;
        }
    }

    public function disablePropertyCategory($propertyCategoryId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem($propertyCategoryId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enablePropertyCategory($propertyCategoryId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem($propertyCategoryId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
