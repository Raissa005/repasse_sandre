<?php

namespace RR\model;

use RR\core\Model;

class VehicleColors extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_colors';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}