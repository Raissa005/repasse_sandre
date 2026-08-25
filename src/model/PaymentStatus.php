<?php

namespace RR\model;

use RR\core\Model;

class PaymentStatus extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'payment_status';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
