<?php

namespace RR\model;

use RR\core\Model;

class WebsiteSettings extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'configuracao';
        $joins = [];

        parent::__construct($this->table, $joins);
    }
}
