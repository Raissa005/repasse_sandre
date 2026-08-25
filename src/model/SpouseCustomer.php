<?php

namespace RR\model;

use RR\core\Model;

class SpouseCustomer extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'spouse_customer';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
