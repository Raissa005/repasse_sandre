<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Toast;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\model\AttendanceStatus;
use PDOException;
use RR\libs\Pagination;
use RR\libs\Util;

class AttendanceStatusController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'attendance-status';
        $this->dir = 'attendance-status';
        $this->model = new AttendanceStatus();
        $this->table = 'attendance_status';
        parent::__construct($this->route);
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

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = array(
            'name' => $_POST['name'],
            'icon_status' => $_POST['icon_status'],
            'box_color' => $_POST['box_color'],
            'order' => $_POST['order'],
            'standard_filter' => $_POST['standard_filter'],
            'created_by' => $_SESSION['RR']->user->id,
        );

        try {
            $itemId = (new GerenciaPost())->insert7181($arrPost, $this->table, true, false);

            Toast::itemAdded();
            header('location:' . URL . $this->route . "/editItem/$itemId");
            exit;
        } catch (PDOException $error) {
            Toast::itemAddError();
            header('location:' . URL . $this->route . "/addItem");
            exit;
        }
    }

    public function editItem($itemId)
    {
        if ($itemId == 10 || $itemId == 11) {
            Secure::redirectFunction(!Secure::access_superAdm(), $this->route, 'authorization=false');
        }

        $attendanceStatusModel = new AttendanceStatus();
        $item = $attendanceStatusModel->getItemById8161($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        if ($itemId == 10 || $itemId == 11) {
            Secure::redirectFunction(!Secure::access_superAdm(), $this->route, 'authorization=false');

            $arrPost = array(
                'order' => $_POST['order'],
                'box_color' => $_POST['box_color'],
                'updated_at' => date("Y-m-d H:i:s"),
                'icon_status' => $_POST['icon_status'],
                'updated_by' => $_SESSION['RR']->user->id,
                'status' => $itemId == 10 || $itemId == 11 ? '1' : $_POST['status']
            );
        } else {
            $arrPost = array(
                'name' => $_POST['name'],
                'order' => $_POST['order'],
                'box_color' => $_POST['box_color'],
                'updated_at' => date("Y-m-d H:i:s"),
                'icon_status' => $_POST['icon_status'],
                'updated_by' => $_SESSION['RR']->user->id,
                'standard_filter' => $_POST['standard_filter'],
                'status' => $itemId == 10 || $itemId == 11 ? '1' : $_POST['status'],
            );
        }

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $itemId, false);

            Toast::itemEdited();
        } catch (PDOException $error) {
            Toast::itemEditError();
        }

        header('location:' . URL . $this->route . "/editItem/$itemId");
        exit;
    }

    public function disableItem($itemId, $page)
    {
        if ($itemId == 10 || $itemId == 11) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        try {
            $success = (new ModelGenerico())->disableItem($itemId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        if ($itemId == 10 || $itemId == 11) {
            Secure::redirectFunction(!Secure::access_dev(), $this->route, 'authorization=false');
        }

        try {
            $success = (new ModelGenerico())->enableItem($itemId, $this->table);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
