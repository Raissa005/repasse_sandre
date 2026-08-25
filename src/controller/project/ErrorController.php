<?php

namespace RR\controller\project;

class ErrorController extends FrontController
{
    public $dir = 'error';

    public function __construct()
    {
        parent::__construct("");
    }

    public function index()
    {
        require APP . 'view/_templates/header.php';
        require APP . 'view/error/index.php';
        require APP . 'view/_templates/footer.php';
    }
}
