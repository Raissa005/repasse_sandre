<?php

namespace RR\controller\project;

use RR\model\Home;

class HomeController extends FrontController
{
    public $dir;
    public $route;
    
    private $table;
    private $model;

    public function __construct()
    {
        $this->dir = 'home';
        $this->route = 'home';
        $this->table = 'home';
        $this->model = (new Home);
        
        parent::__construct($this->route);
    }

    public function index()
    {
        if (!empty($_SESSION['RR']->user->id)) {
            header('location: ' . URL . $this->route . "/dashboard");
            exit;
        } else {
            header('location: ' . URL . "login/index");
            exit;
        }
    }

    public function dashboard()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }
}
