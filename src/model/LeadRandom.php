<?php

namespace RR\model;

use RR\core\Model;

class LeadRandom extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'lead_random';
        $joins = [];

        parent::__construct($this->table,  $joins);
    }
}