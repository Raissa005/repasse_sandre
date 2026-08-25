<?php

namespace RR\model;

use RR\core\Model;

class StatusCheck extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'status_check';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}