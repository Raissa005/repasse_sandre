<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\model\Attendance;
use RR\model\User;

class CalendarController extends Ajax
{
    public function getTheAppointmentsForTheCalendar()
    {
        $attendances = (new Attendance())->getAndFilterAllItems(['created_by' => $_POST['created_by'], 'id_branch' => $_SESSION['RR']->branch->current->id, 'status' => 1, 'id_status_not_in' => [10, 11]]);

        $data = array_map(function ($element) {
            $user = (new User())->getUserById($element->created_by);

            $title = "{$element->name} - Atendimento";
            $start = date('Y-m-d H:i:s', strtotime($element->return_date));
            // $end = '';
            if (date('Y-m-d', strtotime($start)) > date('Y-m-d')) {
                $color = '#00c0ef';
            } else if (date('Y-m-d', strtotime($start)) < date('Y-m-d')) {
                $color = '#dd4b39';
            } else {
                $color = '#FF851B';
            }

            $url = URL . 'attendance/attendance/' . $element->id;

            return (object)[
                'title' => $title,
                'start' => $start,
                // 'end' => '',
                'color' => $color,
                'url' => $url
            ];
        }, $attendances->data);

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }
}
