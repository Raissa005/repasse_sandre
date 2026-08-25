<?php

namespace RR\Model;

use RR\Core\Model;

class ManagerPost extends Model
{
    public function insert(array $arrayPost, string $table, array $require = [])
    {
        $required = [];
        // if (!empty($require)) {
        //     foreach ($require as $rqr) {
        //         if (array_search($rqr, $arrayPost) === false || empty(trim($arrayPost[$rqr]))) {
        //             array_push($required, $rqr);
        //         }
        //     }
        // }

        if (!empty($required)) {
            return (object)["error" => true, "message" => "Você deve preencher todos os campos obrigatórios", "require" => $required];
        }

        foreach ($arrayPost as $key => $value) {
            $columnArray[] = "`$key`";
            $columnArrayPDO[] = ($key == 'json' ? "'" . $value . "'" : ":" . $key);
            if ($key != 'json') {
                $parameters[':' . $key] = ($value !== "" ? $value : NULL);
            }
        }

        $column = implode(",", $columnArray);
        $pdo = implode(",", $columnArrayPDO);

        $sql = "INSERT INTO {$table} ({$column}) VALUES ({$pdo})";

        $query = $this->db->prepare($sql);
        $responseQuery = $query->execute($parameters);

        $lastId = ($responseQuery ? $this->db->lastInsertId() : NULL);

        return (object)['error' => !$responseQuery, 'message' => ($responseQuery ? 'Item cadastrado com sucesso' : 'Erro ao cadastrar o item'), 'lastId' => $lastId];
    }

    public function update(array $arrayPost, string $table, string $whereCol, string $whereVal, array $require = [])
    {
        $required = [];
        // if (!empty($require)) {
        //     foreach ($require as $rqr) {
        //         if (array_key_exists($rqr, $arrayPost) === false || empty(trim($arrayPost[$rqr]))) {
        //             array_push($required, $rqr);
        //         }
        //     }
        // }

        if (!empty($required)) {
            return (object)["error" => true, "message" => "Você deve preencher todos os campos obrigatórios", "require" => $required];
        }

        foreach ($arrayPost as $key => $value) {
            $columnArray[] = "`{$key}` = " . ($key == 'json' ? "'{$value}'" : ":{$key}");
            if ($key != 'json') {
                $parameters[':' . $key] = ($value != "" ? $value : NULL);
            }
        }

        $column = implode(",", $columnArray);
        $sql = "UPDATE {$table} SET {$column} WHERE {$whereCol} = :id";

        $query = $this->db->prepare($sql);
        $parameters[':id'] = $whereVal;
        $responseQuery = $query->execute($parameters);

        return (object)['error' => !$responseQuery, 'message' => ($responseQuery ? 'Item editado com successo' : 'Erro ao editar o item')];
    }


    /**
     * @param string $table
     * @param array $field
     */
    public function delete($table, $fields)
    {
        $filterQuery = '';
        $parameters = [];
        foreach ($fields as $key => $value) {
            $filterQuery = " AND {$key} = :{$value}";
            $parameters[":{$value}"] = $value;
        }

        $sql = "DELETE FROM {$table} WHERE TRUE {$filterQuery}";
        $query = $this->db->prepare($sql);

        $responseQuery = $query->execute($parameters);
        return (object)['error' => !$responseQuery, 'message' => ($responseQuery ? 'Item excluido com successo' : 'Erro ao excluir item')];
    }
}
