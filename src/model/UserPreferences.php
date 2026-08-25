<?php

namespace RR\model;

use RR\core\Model;

class UserPreferences extends Model
{
    private $table;

    function __construct()
    {
        $this->table = "user_preferences";
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}