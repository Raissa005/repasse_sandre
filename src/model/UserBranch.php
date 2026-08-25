<?php

namespace RR\model;

use RR\core\Model;

class UserBranch extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'user_branches';
        $joins = [
            (object)[
                'table' => 'users',
                'join' => 'inner',
                'where' => "{$this->table}.id_user = users.id"
            ],
            (object)[
                'table' => 'branch',
                'join' => 'inner',
                'where' => "{$this->table}.id_branch = branch.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }
}
