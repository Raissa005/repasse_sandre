<?php

namespace RR\controller\project;

use RR\libs\BoxAlert;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\Attendance;
use RR\model\AttendanceStatus;
use RR\model\CommunicationChannels;
use RR\model\States;
use RR\model\User;

class ReportAttendanceController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'report-attendance';
        $this->dir = 'report-attendance';
        $this->model = new Attendance();
        $this->table = 'attendance';
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
        $this->title = "Relatório de atendimentos";
    }

    public function index()
    {
        parent::addScript(URL . "js/" . JSVERSION . "/report-attendance/report.js");

        $filter_users = (new User())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1]]]])->data;

        $attendances = $this->model->getItemsForReport();

        $attendances_status = (new AttendanceStatus)->getWithFiltersAllItems()->data;

        $communication_channels = (new CommunicationChannels)->getWithFiltersAllItems(
            [(object)["columns" => ["status" => ["comparision" => "EQUAL", "value" => 1]]]],
            [(object)["columns" => ["id", "name"]]]
        )->data;

        $states = (new States)->getWithFiltersAllItems(
            [(object)["columns" => ["status" => ["comparision" => "EQUAL", "value" => 1]]]],
            [(object)["columns" => ["name", "uf"]]]
        )->data;

        $filters = explode('?', $_SERVER["REQUEST_URI"])[1] ?? '';

        $columns_options = (object)[
            "column_id" => "off",
            "column_customer_name" => "on",
            "column_user_name" => "off",
            "column_state" => "on",
            "column_classification" => "on",
            "column_attendance_status" => "on",
            "column_communication_channel" => "off",
            "column_opening_date" => "off",
            "column_return_date" => "off",
            "column_status" => "on"
        ];

        foreach ($columns_options as $columns_option => $default_value) {
            if (!isset($_GET[$columns_option])) {
                if (isset($_GET["filtering"])) {
                    $_GET[$columns_option] = "off";
                } else {
                    $_GET[$columns_option] = $default_value;
                }
            }
        }

        foreach ($attendances as $attendance) {
            switch ($attendance->status) {
                case 0:
                    $attendance->status_text = 'Inativo';
                    $attendance->status_label = 'danger';
                    break;

                case 1:
                    $attendance->status_text = 'Ativo';
                    $attendance->status_label = 'success';
                    break;
            }

            $id_status = (new AttendanceStatus)->getWithFiltersAllItems(
                [(object)['columns' => ['id' => ['comparison' => 'EQUAL', 'value' => $attendance->id_status]]]],
                [(object)['columns' => ['name', 'icon_status', 'box_color']]]
            )->data[0];

            $attendance->id_status_name = $id_status->name;
            $attendance->id_status_icon_status = $id_status->icon_status;
            $attendance->id_status_box_color = $id_status->box_color;
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function print($type = '')
    {
        $attendance_columns = [
            (object)[
                'columns' => ['*']
            ],
            (object)[
                'table' => 'users',
                'columns' => ['id', 'name'],
            ],
            (object)[
                'table' => 'communication_channels',
                'columns' => ['name']
            ]
        ];

        $attendances = $this->model->getItemsForReport();

        $states = (new States)->getWithFiltersAllItems(
            [(object)["columns" => ["status" => ["comparision" => "EQUAL", "value" => 1]]]],
            [(object)["columns" => ["name", "uf"]]]
        )->data;

        $communication_channels = (new CommunicationChannels)->getWithFiltersAllItems(
            [(object)["columns" => ["status" => ["comparision" => "EQUAL", "value" => 1]]]],
            [(object)["columns" => ["id", "name"]]]
        )->data;


        $columns_options = (object)[
            "column_id" => "off",
            "column_customer_name" => "on",
            "column_user_name" => "off",
            "column_state" => "on",
            "column_classification" => "on",
            "column_attendance_status" => "on",
            "column_communication_channel" => "off",
            "column_opening_date" => "off",
            "column_return_date" => "off",
            "column_status" => "on"
        ];

        foreach ($columns_options as $columns_option => $default_value) {
            if (!isset($_GET[$columns_option])) {
                if (isset($_GET["filtering"])) {
                    $_GET[$columns_option] = "off";
                } else {
                    $_GET[$columns_option] = $default_value;
                }
            }
        }

        foreach ($attendances as $attendance) {
            switch ($attendance->status) {
                case 0:
                    $attendance->status_text = 'Inativo';
                    break;

                case 1:
                    $attendance->status_text = 'Ativo';
                    break;
            }

            $id_status = (new AttendanceStatus)->getWithFiltersAllItems(
                [(object)['columns' => ['id' => ['comparison' => 'EQUAL', 'value' => $attendance->id_status]]]],
                [(object)['columns' => ['name', 'icon_status', 'box_color']]]
            )->data[0];

            $attendance->id_status_name = $id_status->name;
        }

        if ($type == "excel") {
            header("Content-type: application/vnd.ms-excel");
            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=relatorio_atendimento.xls");
            header("Pragma: no-cache");
        }

        require APP . 'view/' . $this->dir . '/print.php';
    }

    private function doStar(int $score, int $max_score): string
    {
        $html = '';

        for ($i = 1; $i <= $max_score; $i++) {
            $star_type = $score >= $i ? 'fas' : 'far';
            $html .= '<i id="star-' . $i . '" class="star text-yellow ' . $star_type . ' fa-star"></i>';
        }

        return $html;
    }
}
