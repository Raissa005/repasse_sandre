<?php

namespace RR\model;

use RR\core\Model;

class NetworkCardDigital extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'network_card_digital';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
