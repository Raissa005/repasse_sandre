<?php

namespace RR\model;

use RR\core\Model;

class ManagerTeam extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'manager_team';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAllSellersFromManager(int $managerId)
    {
        $manager_filter = [
            (object)[
                'columns' => [
                    'id_manager' => (object)['value' => $managerId]
                ]
            ],
        ];

        $response = $this->getWithFiltersAllItems($manager_filter);

        return $response;
    }
}
