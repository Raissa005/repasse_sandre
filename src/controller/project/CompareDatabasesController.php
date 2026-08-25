<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Util;
use RR\model\CompareDatabases;

class CompareDatabasesController extends FrontController
{
    public $route;
    public $dir;
    private $model;

    public function __construct()
    {
        $this->route = 'compare-databases';
        $this->model = (new CompareDatabases);
        parent::__construct($this->route);
        Secure::redirectFunction(!Secure::access_dev());
    }

    public function index()
    {
        echo "<pre>" . "<h3>- Compare and Insert Tables and Columns:</h3>" . URL . $this->route . "/compareAndInsertStructureFromTwoDatabase/" . "</pre>";
        echo "<pre>" . "<h3>- Compare Tables:</h3> " . URL . $this->route . "/compareTables/" . "</pre>";
        echo "<pre>" . "<h3>- Compare and Insert Tables:</h3> " . URL . $this->route . "/compareTablesAndInsert/" . "</pre>";
        echo "<pre>" . "<h3>- Columns Comparison List / Update / Insert:</h3> " . URL . $this->route . "/compareColumnsAll/" . "</pre>";
        echo "<pre>" . "<h3>- Columns Comparison List:</h3> " . URL . $this->route . "/compareColumnsList/" . "</pre>";
        echo "<pre>" . "<h3>- Columns Comparison Insert:</h3> " . URL . $this->route . "/compareColumnsInsert/" . "</pre>";
        echo "<pre>" . "<h3>- Columns Comparison Update:</h3> " . URL . $this->route . "/compareColumnsUpdate/" . "</pre>";
    }

    public function compareAndInsertStructureFromTwoDatabase($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareStructureFromTwoDatabase();

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }

    public function compareAndGenerateStructureSqlFromTwoDatabase($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareStructureFromTwoDatabase(true);

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }

    public function compareTables($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareTables(false);

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }

    public function compareTablesAndInsert($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareTables(true);

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }

    public function compareColumnsList($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareColumns(null);

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }

    public function compareColumnsAll($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareColumns(1);

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }

    public function compareColumnsInsert($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareColumns(2);

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }

    public function compareColumnsUpdate($token = '')
    {
        if ($token == $this->token && Secure::access_dev()) {
            $response = $this->model->compareColumns(3);

            echo "<pre>";
            print_r($response);
            echo "</pre>";
            exit;
        }
    }
}
