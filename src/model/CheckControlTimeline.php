<?php

namespace RR\model;

use RR\core\Model;

class CheckControlTimeline extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'check_control_timeline';
        $joins = [
            (object)[
                'table' => 'customer',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.forwarded_by"
            ],
            (object)[
                'table' => 'status_icon',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.status_icon"
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}