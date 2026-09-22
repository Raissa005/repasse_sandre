<?php

namespace RR\controller\project;

use Dompdf\Positioner\Fixed;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\BoxAlert;
use RR\libs\Secure;
use RR\model\Lead;
use RR\model\User;
use PDOException;
use RR\libs\Pagination;
use RR\libs\Util;
use RR\model\Attendance;
use RR\model\Notification;
use RR\model\NotificationRead;

class LeadController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->route = 'lead';
        $this->dir = 'lead';
        $this->model = new Lead();
        $this->table = 'lead';
        parent::__construct($this->route);

        /**
         * Módulo Lead pausado a pedido do usuário (2026-09-02): a tabela
         * `lead` não existe neste banco, então qualquer ação aqui já
         * quebraria com erro fatal de SQL. Guard evita isso e evita
         * depender de erro de banco pra manter o recurso desligado — se as
         * tabelas forem recriadas por outro motivo, isso continua desativado
         * até decisão explícita de reativar (ver docs/10-modulos-negocio.md
         * e memória do projeto).
         */
        Secure::redirectFunction(true, 'home', 'error=lead_disabled');

        $this->addScript(URL . "js/" . JSVERSION . "/lead.js");
        $this->alert = (new BoxAlert());
    }

    public function index()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/leadImport.js");

        $filtersUsers = array(
            "id_branch_and_profile" => $_SESSION['RR']->branch->current->id,
            "status" => true,
            "order" => " up.access ASC, u.name ASC",
        );

        $users = (new User)->getAndFilterAllUsers(0, $filtersUsers, 0)->data;
        $communications = (new ModelGenerico)->getAllItens("communication_channels");

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['attendance_open'])) {
            $_GET['attendance_open'] = 2;
        }

        $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;

        $rows = 20;
        $page = Pagination::getPage();

        $items = $this->model->getAndFilterAllItem($_GET, ['limit' => $rows, 'page' => $page]);

        $pagination = (new Pagination())->pages($items->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($items->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function editItem($itemId)
    {
        $modelGenerico = new ModelGenerico();
        $leadModel = new Lead();
        $userModel = new User();
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $item = $leadModel->getItemById8161($itemId);

        if (isset($product) && !empty($product)) {
            Secure::branch($product->id_branch, $this->route);
        }
        $filtersUsers = array(
            'status' => true,
            'id_branch_and_profile' => isset($product) && !empty($product) ? $product->id_branch : $_SESSION['RR']->branch->current->id,
            "order" => " up.access ASC, u.name ASC",
        );

        $communications = $modelGenerico->getAllItens("communication_channels");

        $users = $userModel->getAndFilterAllUsers(0, $filtersUsers, 0)->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId?error=error");

        $gerenciaPost = new GerenciaPost();
        $leadModel = new Lead();

        if (isset($_POST['salve'])) {
            $arrPost = array(
                'status' => $_POST['status'],
                'updated_by' => $_SESSION['RR']->user->id
            );
        } else {
            $arrPost = array(
                'status' => $_POST['status'],
                'created_by' => $_POST['created_by'],
                'updated_by' => $_SESSION['RR']->user->id
            );
        }

        try {
            $gerenciaPost->update8191($arrPost, $this->table, 'id', $itemId, false);

            $item = $leadModel->getItemById8161($itemId);

            if (!empty($item->created_by)) {
                $arrPostAttendance = array(
                    'id_branch' => !empty($item->id_product) ? $product->id_branch : $_SESSION['RR']->branch->current->id,
                    'opening_date' => $item->created_at,
                    'id_communication_channel' => $item->id_communication_channel,
                    'id_status' => 7,
                    'name' => strtoupper($item->name),
                    'email' => $item->email,
                    'description' => $item->message,
                    'created_by' => $_POST['created_by']
                );

                $attendanceId = $gerenciaPost->insert7181($arrPostAttendance, "attendance", true, false);

                if (!empty($attendanceId)) {
                    $attendance = (new Attendance)->getItemWithFilters(
                        [
                            (object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $attendanceId]]]
                        ],
                        [
                            (object)['columns' => ['*']],
                            (object)['table' => 'communication_channels', 'columns' => ['name']]
                        ]
                    );

                    $notification = (new Notification)->insert([
                        "icon" => "fa fa-comments",
                        'route' => "attendance/attendance/" . $attendance->id,
                        'title' => "Você tem um novo atendimento em nome de: <strong>" . $attendance->name . "</strong>.",
                        'description' => "Cliente " . $attendance->name . " veio através do/a " . $attendance->communication_channels_name . ".",
                        'id_branch' => $attendance->id_branch,
                    ]);

                    if (!$notification->error) {
                        (new NotificationRead)->insert([
                            'id_notification' => $notification->lastId,
                            'id_user' => $attendance->created_by,
                        ]);
                    }
                }

                $arrPostAttendancePhone = array(
                    "id_attendance" => $attendanceId,
                    "phone" => $item->phone,
                    "name" => "Principal",
                );

                $gerenciaPost->insert7181($arrPostAttendancePhone, "attendance_phones", false);

                if (!empty($item->id_product)) {
                    $arrPostAttendanceDisplayed = array(
                        'id_attendance' => $attendanceId,
                        'id_product' => $item->id_product,
                        'created_by' => $item->created_by,
                    );

                    $gerenciaPost->insert7181($arrPostAttendanceDisplayed, "displayed_properties", false);
                }

                $arrTimeline = array(
                    "id_attendance" => $attendanceId,
                    "comment" => "Cliente oriundo do Site",
                    "status_icon" => 1,
                    "status_timeline" => 4,
                    "created_by" => $_SESSION['RR']->user->id,
                );

                (new GerenciaPost())->insert7181($arrTimeline, "attendance_timeline", null, false);

                $gerenciaPost->update8191(["id_attendance" => $attendanceId], $this->table, "id", $itemId);
            }

            header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/editItem/' . $itemId . "?edited=false");
            exit;
        }
    }

    public function disableItem($itemId, $page = "1")
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
}
