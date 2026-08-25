<?php

namespace RR\model;

use RR\core\Model;

class NotificationRead extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'notification_read';
        $joins = [
            (object)[
                'table' => 'notification',
                'join' => 'left',
                'where' => "{$this->table}.id_notification = this->table.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}