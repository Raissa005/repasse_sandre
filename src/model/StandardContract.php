<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Util;

class StandardContract extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'standard_contract';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

}
