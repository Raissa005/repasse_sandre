<?php

namespace RR\model;

use RR\core\Model;

class ConstructionCostCenters extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'construction_cost_centers';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
