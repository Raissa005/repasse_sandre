<?php

namespace RR\model;

use RR\core\Model;

class VehicleDoors extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'vehicle_doors';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}