<?php

namespace RR\model;

use RR\core\Model;

class CustomerIntegration extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'customer_integration';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
