<?php

namespace RR\model;

use RR\core\Model;
use PDOException;

class SalesAttachment extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'sales_attachment';
        $joins = [
            (object)[
                'table' => 'sales',
                'join' => 'inner',
                'where' => "{$this->table}.id_sale = this->table.id"
            ],
            (object)[
                'table' => 'users',
                'join' => 'inner',
                'where' => "{$this->table}.created_by = this->table.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}
