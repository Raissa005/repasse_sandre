<?php

namespace RR\model;

use RR\core\Model;

class BranchUserPosition extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'branch_user_position';
        $joins = [
            (object)[
                'table' => 'branch',
                'join' => 'inner',
                'where' => "{$this->table}.id_branch = branch.id"
            ],
            (object)[
                'table' => 'user_position',
                'join' => 'inner',
                'where' => "{$this->table}.id_user_position = user_position.id"
            ],
            (object)[
                'table' => 'users',
                'join' => 'left',
                'where' => "{$this->table}.id_user = users.id"
            ],
            (object)[
                'table' => 'users_profiles',
                'join' => 'left',
                'where' => "users.id_profile = users_profiles.id",
                'require' => 'users'
            ]
        ];

        parent::__construct($this->table, $joins);
    }
}
