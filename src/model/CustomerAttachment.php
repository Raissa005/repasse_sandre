<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\FileUploader;
use RR\libs\Util;

class CustomerAttachment extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'customer_attachments';
        $joins = [
            (object)[
                'table' => 'customer',
                'join' => 'inner',
                'where' => "{$this->table}.id_customer = customer.id"
            ],
            (object)[
                'table' => 'users',
                'join' => 'inner',
                'where' => "{$this->table}.created_by = users.id"
            ],
            (object)[
                'table' => 'users_profiles',
                'join' => 'inner',
                'where' => "users_profiles.id = users.id_profile"
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}

