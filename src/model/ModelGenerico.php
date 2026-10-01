<?php


namespace RR\model;

use RR\core\Model;

class ModelGenerico extends Model
{
    private $table;
    
    function __construct()
    {
        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function pagination()
    {
        if (isset($_GET['page'])) {
            $pagenation = $_GET['page'];
        } else {
            $pagenation = 1;
        }
        return $pagenation;
    }


    public function getItens($qtd, $pagina, $filters, $table)
    {

        $filters_query = "";
        $parameters = array();

        if (isset($filters['status']) && $filters['status'] != "") {
            $filters_query = $filters_query . " AND status = :status";
            $parameters[':status'] = $filters['status'];
        } else {
            $filters_query = $filters_query . " AND status = :status";
            $parameters[':status'] = true;
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filters_query = $filters_query . " AND ucase(name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        self::assertIdentifier($table);
        $offset = ($pagina - 1) * $qtd;

        $sql = "SELECT * FROM {$table} WHERE TRUE $filters_query ORDER BY ";
        $sql .= !isset($filters['order']) ? " name ASC " : " " . $filters['order'] . " ";
        if ($qtd > 0) {
            $sql .= " LIMIT $qtd OFFSET $offset";
        }
        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }


    public function getItemById8161($id, $table)
    {
        self::assertIdentifier($table);
        $sql = "SELECT * FROM {$table} WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function disableItem($id, $table = '')
    {
        self::assertIdentifier($table);
        $sql = "UPDATE {$table} SET status = FALSE WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        return $query->execute($parameters);
    }

    public function disableItem2($id, $table)
    {
        self::assertIdentifier($table);
        $sql = "UPDATE {$table} SET ativo = FALSE WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        return $query->execute($parameters);
    }

    public function deleteItemByCampoGenerico($table, $campo, $value)
    {
        self::assertIdentifier($table);
        self::assertIdentifier($campo);
        $sql = "DELETE FROM {$table} WHERE {$campo} = :value";
        $query = $this->db->prepare($sql);
        $parameters = array(':value' => $value);

        $query->execute($parameters);
    }

    public function enableItem($id, $table = '')
    {
        self::assertIdentifier($table);
        $sql = "UPDATE {$table} SET status = TRUE WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        return $query->execute($parameters);
    }

    public function enableItem2($id, $table)
    {
        self::assertIdentifier($table);
        $sql = "UPDATE {$table} SET ativo = TRUE WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        return $query->execute($parameters);
    }

    public function getItemByName($name, $table)
    {
        self::assertIdentifier($table);
        $sql = "SELECT * FROM {$table} WHERE name = :name";
        $query = $this->db->prepare($sql);
        $parameters = array(':name' => $name);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemByGenericField($value, $table, $field, $status = false)
    {
        $statusFilter = "";
        $parameters = array(':value' => $value);

        if ($status !== false) {
            $statusFilter .= " AND `status` = :status";
            $parameters[':status'] = $status;
        }

        self::assertIdentifier($table);
        self::assertIdentifier($field);
        $sql = "SELECT * FROM {$table} WHERE {$field} = :value $statusFilter";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemByGenericFieldArray(array $array, string $table)
    {
        self::assertIdentifier($table);

        $sql = "SELECT * FROM {$table} WHERE TRUE ";
        $parameters = [];
        foreach ($array as $key => $value) {
            self::assertIdentifier($key);
            $sql .= " AND {$key} = :{$key}";
            $parameters[":{$key}"] = $value;
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllItens($table)
    {
        self::assertIdentifier($table);
        $sql = "SELECT * FROM {$table} WHERE status = 1";
        $query = $this->db->prepare($sql);

        $query->execute();

        return $query->fetchAll();
    }
}
