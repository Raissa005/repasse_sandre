<?php

namespace RR\model;

use RR\core\Model;

class UserPosition extends Model
{    
    private $table;

    function __construct()
    {
        $this->table = 'user_position';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItems($filters = [], $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if ($value != '') {
                    $table = $this->table;

                    if (in_array($column, ['status'])) {
                        $filtersQuery .= " AND {$table}.{$column} = :{$table}_{$column}";
                    } else if (in_array($column, [])) {
                        $filtersQuery .= " AND {$table}.{$column} IN (" . (implode(", ", $value)) . ")";
                    } else if (in_array($column, ['name'])) {
                        $filtersQuery .= " AND ucase({$table}.{$column}) LIKE ucase(:{$table}_{$column})";
                        $value = "%" . $value . "%";
                    }

                    if (in_array($column, ['status', 'name'])) {
                        $parameters[":{$table}_{$column}"] = $value;
                    }
                }
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.*                                    
                FROM {$this->table}                
                WHERE TRUE $filtersQuery";

        $sql .= isset($options['groupBy']) ? " GROUP BY {$options['groupBy']}" : " GROUP BY {$this->table}.id";
        $sql .= isset($options['orderBy']) ? " ORDER BY {$options['orderBy']}" : " ORDER BY {$this->table}.id ASC";

        $sqlRows = $sql;

        if (isset($options['limit'])) {
            $offset = ($options['page'] - 1) * $options['limit'];
            $sql .= " LIMIT {$options['limit']} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        $queryRows = $this->db->prepare($sqlRows);
        $queryRows->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $queryRows->rowCount()];
    }

    public function getItemById8161($itemId)
    {
        $sql = "SELECT
                    {$this->table}.*
                FROM {$this->table}
                WHERE {$this->table}.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $itemId);

        $query->execute($parameters);

        return $query->fetch();
    }
}
