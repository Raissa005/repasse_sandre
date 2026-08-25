<?php

namespace RR\model;

use RR\core\Model;

class AttendanceFilterType extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'attendance_filter_type';
        $joins = [
            (object)[
                'table' => 'attendance_filters_interests',
                'join' => 'inner',
                'where' => "{$this->table}.id = this->table.id_attendance_filter_type"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
