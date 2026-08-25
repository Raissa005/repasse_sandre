<?php

namespace RR\model;

use RR\core\Model;

class GerenciaPost extends Model
{
    private $table;

    function __construct()
    {
        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function insert7181($arrayPost, $table, $return = null, $up = false, $json = false)
    {
        foreach ($arrayPost as $key => $value) {
            $columnArray[] = "`$key`";
            $columnArrayPDO[] = ($key == 'json' || $json == true ? "'" . $value . "'" : ":" . $key);
            if ($key != 'json') {
                if ($key == 'password' || !$up || $key == 'icon') {
                    $value = (trim($value) != '' ? (trim($value)) : null);
                    $arrayValue[':' . $key] = $value;
                } else {
                    $value = mb_strtoupper($value, 'UTF-8');
                    $arrayValue[':' . $key] = $value;
                }
            }
        }

        $column = implode(",", $columnArray);
        $pdo = implode(",", $columnArrayPDO);

        $sql = "INSERT INTO {$table} ({$column}) VALUES ({$pdo})";

        $query = $this->db->prepare($sql);
        $parameters = $arrayValue;
        $query->execute($parameters);

        if ($return) {
            $lastId = $this->db->lastInsertId();
            return  $lastId;
        }
    }

    public function update8191($arrayPost, $table, $where_col, $where_val, $up = true, $json = false)
    {
        foreach ($arrayPost as $key => $value) {
            $columnArray[] = "`" . $key . "`" . " = " . ($key == 'json' || $json == true ? "'" . $value . "'" : ":" . $key);
            if ($key != 'json') {
                if ($key == 'password' || !$up || $key == 'icon') {
                    $value = (trim($value) != '' ? (trim($value)) : null);
                    $arrayValue[':' . $key] = $value;
                } else {
                    $value = mb_strtoupper($value, 'UTF-8');
                    $arrayValue[':' . $key] = $value;
                }
            }
        }

        $column = implode(",", $columnArray);

        $sql = "UPDATE {$table} SET {$column} WHERE {$where_col} = :id";

        $query = $this->db->prepare($sql);
        $parameters = $arrayValue;
        $parameters[':id'] = $where_val;

        $query->execute($parameters);
    }
}
