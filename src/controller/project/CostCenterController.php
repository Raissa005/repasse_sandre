<?php

namespace RR\controller\project;

use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Toast;
use RR\libs\RecursiveCostCenter;
use RR\libs\Secure;
use RR\model\CostCenter;
use PDOException;
use RR\libs\Util;

class CostCenterController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $title;
    public $caption;

    public function __construct()
    {
        $this->route = 'cost-center';
        $this->dir = 'cost-center';
        $this->model = new CostCenter();
        $this->table = 'cost_center';
        parent::__construct($this->route);

        $this->title = "Centro Custo";
        $this->caption = "Árvore";
    }

    public function index()
    {
        Secure::access_admin(true);

        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addStyle(URL . "css/" . CSSVERSION . "/" . $this->dir . "/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/global.js");

        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        $filterBillsToPay = $filterBillsToReceive = $_GET;
        $filterBillsToPay['id_type'] = 1;
        $filterBillsToReceive['id_type'] = 2;

        $billsToPay = ['data' => (new RecursiveCostCenter())->recursiveTree(0, $filterBillsToPay), 'id_type' => 1];
        $billsToReceive = ['data' => (new RecursiveCostCenter())->recursiveTree(0, $filterBillsToReceive), 'id_type' => 2];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route);

        $arrPost = array(
            'name' => $_POST['name'],
            'id_type' => isset($_POST['id_type']) ? $_POST['id_type'] : 1,
            'id_father' => !empty($_POST['id_father']) ? $_POST['id_father'] : '0',
            'created_by' => $_SESSION['RR']->user->id,
        );

        try {
            (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            Toast::itemAdded();
        } catch (PDOException $error) {
            Toast::itemAddError();
        }

        header('location:' . URL . $this->route);
        exit;
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route);

        $modelGenerico = new ModelGenerico();
        $gerenciaPost = new GerenciaPost();
        $costCenterModel = new CostCenter();

        try {
            if ($_POST['status'] == 0 && isset($_POST['move_data']) && $_POST['move_data'] != 'nowhere') {
                $children = $costCenterModel->getAndFilterAllItem(0, ['id_father' => $itemId], 0);
                $allEntry = $modelGenerico->getItemByGenericField($itemId, "bills_to_pay", "id_cost_center");

                foreach ($children as $child) {
                    $gerenciaPost->update8191(['id_father' => $_POST['move_data']], $this->table, 'id', $child->id, false);
                }

                foreach ($allEntry as $entry) {
                    $gerenciaPost->update8191(['id_cost_center' => $_POST['move_data'],], "bills_to_pay", 'id', $entry->id, false);
                }
            }

            $arrayPost = array(
                'name' => $_POST['name'],
                'id_father' => !empty($_POST['id_father']) ? $_POST['id_father'] : '0',
                'status' => $_POST['status'],
                'updated_by' => $_SESSION['RR']->user->id,
                'updated_at' => date("Y-m-d H:i:s")
            );

            $gerenciaPost->update8191($arrayPost, $this->table, 'id', $itemId, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route);
        exit;
    }

    public function handleSubmitCloneItem($itemId)
    {
        Secure::access_admin(true);
        Secure::check_post_method($this->route);

        $item = (new CostCenter())->getItemById8161($itemId);

        $arrPost = array(
            'name' => $_POST['name'],
            'id_father' => $item->id_father,
            'id_type' => $item->id_type,
            'created_by' => $_SESSION['RR']->user->id
        );

        try {
            $clonedItemId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);
            $children = (new RecursiveCostCenter())->recursiveTree($itemId, ['status' => true]);

            (new RecursiveCostCenter())->recursiveClone($clonedItemId, $children, $item->id_type);

            Toast::itemAdded();
        } catch (PDOException $error) {
            Toast::itemAddError();
        }

        header('location:' . URL . $this->route);
        exit;
    }

    public function disableItem($itemId)
    {
        Secure::access_admin(true);

        try {
            $success = $this->model->disableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route);
        exit;
    }

    public function enableItem($itemId)
    {
        Secure::access_admin(true);

        try {
            $success = $this->model->enableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route);
        exit;
    }
}
