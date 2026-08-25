<?php

namespace RR\model;

use RR\core\Model;

class CheckControl extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'check_control';
        $joins = [
            (object)[
                'table' => 'customer',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.forwarded_by"
            ],
            (object)[
                'table' => 'banks',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_bank"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}