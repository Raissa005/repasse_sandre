<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\Countries;
use PDOException;
use RR\libs\Pagination;

class CountriesController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'countries';
        $this->dir = 'countries';
        $this->model = new Countries();
        $this->table = 'countries';
        parent::__construct($this->route);

        Secure::access_admin(true);
    }

    public function index()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $countries = $this->model->getAndFilterAllCountries($_GET, ['limit' => $rows, 'page' => $page]);
        $pagination = (new Pagination())->pages($countries->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($countries->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addCountries()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addCountries.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddCountries()
    {
        Secure::check_post_method($this->route . "/addCountries");

        $arrPost = array(
            'country_code' => $_POST['country_code'],
            'name' => $_POST['name'],
            'currency_name' => $_POST['currency_name'],
            'currency' => $_POST['currency'],
            'currency_symbol' => $_POST['currency_symbol'],
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => $_SESSION['RR']->user->id
        );

        try {
            $countryId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            header('location:' . URL . $this->route . "/editCountries/$countryId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/addCountries?added=false");
            exit;
        }
    }

    public function editCountries($countryId)
    {
        $country = (new Countries())->getCountriesById($countryId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editCountries.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditCountries($countryId)
    {
        Secure::check_post_method($this->route . "/editCountries/$countryId");

        $arrPost = array(
            'country_code' => $_POST['country_code'],
            'name' => $_POST['name'],
            'currency_name' => $_POST['currency_name'],
            'currency' => $_POST['currency'],
            'currency_symbol' => $_POST['currency_symbol'],
            'status' => $_POST['status'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id
        );

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $countryId, false);

            header('location:' . URL . $this->route . "/editCountries/$countryId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editCountries/$countryId?edited=false");
            exit;
        }
    }

    public function disableCountries($countryId, $page)
    {
        $ModelGenerico =  new ModelGenerico();

        $ModelGenerico->disableItem($countryId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableCountries($countryId, $page)
    {
        $ModelGenerico =  new ModelGenerico();

        $ModelGenerico->enableItem($countryId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
