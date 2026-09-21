<?php

namespace RR\model;

use RR\core\Model;

class AttendanceDisplayedVehicles extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'attendance_displayed_vehicles';
        $joins = [
            (object)[
                'table' => 'attendance',
                'join' => 'inner',
                'where' => "{$this->table}.id_attendance = this->table.id"
            ],
            (object)[
                'table' => 'vehicles',
                'join' => 'inner',
                'where' => "{$this->table}.id_vehicle = this->table.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}
