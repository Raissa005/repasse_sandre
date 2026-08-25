<?php

namespace RR\model;

use RR\core\Model;

class ClientTypeResourceTypes extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'client_type_resource_types';
        $joins = [
            (object)[
                'table' => 'customer',
                'join' => 'inner',
                'where' => "{$this->table}.id_customer = customer.id"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
