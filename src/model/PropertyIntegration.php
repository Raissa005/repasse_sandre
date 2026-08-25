<?php

namespace RR\model;

use RR\core\Model;

class PropertyIntegration extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'property_integration';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
