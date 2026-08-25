<?php

namespace RR\model;

use RR\core\Model;

class LinkedCheckControl extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'linked_check_control';
        $joins = [
            (object)[
                'table' => 'check_control',
                'joins' =>  'inner',
                'where' => "this->table.id = {$this->table}.id_check"
            ],
            (object)[
                'table' => 'banks',
                'joins' => 'inner',
                'where' => 'this->table.id = check_control.id_bank'
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}