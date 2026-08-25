<?php

namespace RR\model;

use RR\core\Model;

class AttendanceFiltersInterests extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'attendance_filters_interests';
        $joins = [
            (object)[
                'table' => 'attendance',
                'join' => 'inner',
                'where' => "{$this->table}.id_attendance = this->table.id"
            ],
            (object)[
                'table' => 'attendance_filter_type',
                'join' => 'inner',
                'where' => "{$this->table}.id_attendance_filter_type = this->table.id"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
