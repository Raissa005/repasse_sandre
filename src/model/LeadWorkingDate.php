<?php

namespace RR\model;

use RR\core\Model;

class LeadWorkingDate extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'lead_working_date';
        $joins = [];

        parent::__construct($this->table,  $joins);
    }
}