<?php

namespace RR\core;

use PDO;
use RR\libs\Util;

class Model
{
    /**
     * @var PDO Database Connection
     */
    public $db;
    public $token;
    private $table;
    private $joins;

    protected $message_admins = "Ops! Ocorreu um problema ao realizar esse função &#128546. Entre em contato os <a target=\"_blank\" href=\"https://api.whatsapp.com/send?phone=554832636688\">administradores</a>";

    /**
     * Garante que um nome de tabela/coluna usado em SQL interpolado contém só
     * caracteres válidos de identificador. Barra injeção quando o nome vem
     * (direta ou indiretamente) de input do usuário. Lança em caso inválido —
     * todas as chamadas internas usam nomes fixos, então nunca dispara em uso normal.
     */
    protected static function assertIdentifier($name): string
    {
        if (!is_string($name) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
            throw new \InvalidArgumentException('Identificador de tabela/coluna inválido.');
        }
        return $name;
    }

    /**
     * Whenever model is created, open a database connection.
     */
    function __construct($table = '', $joins = [])
    {
        $this->table = $table;
        $this->joins = $joins;
        $this->token = preg_replace('/[0-9\W\s]/', '', TOKEN);

        try {
            $this->db = $this->openDatabaseConnection();
        } catch (\PDOException $e) {
            if (ENVIRONMENT == 'development') {
                echo $e->getMessage();
                exit;
            }
            exit('Sem conexão com o Banco de Dados.');
        }
    }

    /**
     * Open the database connection with the credentials from application/config/config.php
     */
    private static function openDatabaseConnection()
    {
        // set the (optional) options of the PDO connection. in this case, we set the fetch mode to
        // "objects", which means all results will be objects, like this: $result->user_name !
        // For example, fetch mode FETCH_ASSOC would return results like this: $result["user_name] !
        // @see http://www.php.net/manual/en/pdostatement.fetch.php
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => (ENVIRONMENT != 'development' ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_WARNING));

        // generate a database connection, using the PDO connector
        // @see http://net.tutsplus.com/tutorials/php/why-you-should-be-using-phps-pdo-for-database-access/
        return new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASS, $options);
    }

    protected function mountSqlFromColumns($columns = [])
{
    $query = '';
    $link_tables = [];

    if (!empty($columns)) {
        array_map(function ($column) {
            $column = (object) $column;
            return $column;
        }, $columns);

        foreach ($columns as $object) {
            if (!isset($object->table) || (isset($object->table) && empty($object->table))) {
                $object->table = $this->table;
            } else {
                array_push($link_tables, $object->table);
            }

            if (is_array($object->columns)) {
                foreach ($object->columns as $column_key => $column_value) {
                    if (is_array($column_value)) {
                        $column = $column_key;
                        $alias = $column_value[0];
                    } else {
                        $column = $column_value;
                        $alias = null; // Define como nulo se não houver alias
                    }

                    // Constrói a parte da query para essa coluna
                    $query .= "\n{$object->table}.{$column}";

                    // Adiciona o alias, se existir
                    if ($alias !== null) {
                        $query .= " AS {$alias}";
                    }

                    $query .= ",";
                }
            } else {
                $column = $object->columns;
                $alias = null; // Define como nulo se não houver alias

                // Constrói a parte da query para essa coluna
                $query .= "\n{$object->table}.{$column}";

                // Adiciona o alias, se existir
                if ($alias !== null) {
                    $query .= " AS {$alias}";
                }

                $query .= ",";
            }
        }
        $query = trim(rtrim($query, ","));
    } else {
        $query = "\n{$this->table}.*";
    }

    return (object)['query' => $query, 'link_tables' => $link_tables];
}

    protected function mountSqlFromJoins($joins = [], $link_tables = [])
    {
        $query = '';
        if (!empty($joins) && !empty($link_tables)) {
            foreach ($joins as $join) {
                if (in_array($join->table, $link_tables)) {
                    if (isset($join->require) && !empty($join->require) && !in_array($join->require, $link_tables)) {
                        $link_tables[] = $join->require;
                        $query .= $this->mountSqlFromJoins($this->joins, [$join->require])->query;
                    }
                    $join->join = strtoupper(isset($join->join) ? $join->join : 'LEFT');
                    $query .= "\n{$join->join} JOIN {$join->table} ON " . str_replace('this->table', $join->table, $join->where) . "";
                }
            }
        }

        return (object)['query' => $query];
    }

    protected function mountSqlFromFilters($filters = [])
    {
        $query = '';
        $parameters = [];
        $link_tables = [];

        if (!empty($filters)) {
            array_map(function ($filter) {
                $filter = (object)$filter;

                if (isset($filter->columns) && !empty($filter->columns)) {
                    $filter->columns = array_map(function ($column) {
                        return (object)$column;
                    }, $filter->columns);
                }

                return $filter;
            }, $filters);

            foreach ($filters as $filter) {
                if (!isset($filter->table) || (isset($filter->table) && empty($filter->table))) {
                    $filter->table = $this->table;
                } else {
                    array_push($link_tables, $filter->table);
                }

                if (isset($filter->columns) && !empty($filter->columns)) {
                    foreach ($filter->columns as $column => $field) {
                        $field->comparison = !isset($field->comparison) ?
                            'EQUAL' :
                            strtoupper($field->comparison);

                        if ($field->comparison == "=" || $field->comparison == "EQUAL" || $field->comparison == "IN") {
                            if (!is_array($field->value)) {

                                if ($field->value == null) {
                                    $query .= "\nAND {$filter->table}.{$column} is null";
                                } else {
                                    $query .= "\nAND {$filter->table}.{$column} = :{$filter->table}_{$column}";
                                    $parameters[":{$filter->table}_{$column}"] = $field->value;
                                }
                            } else {
                                $queryParameters = '';
                                for ($i = 0; $i < count($field->value); $i++) {
                                    $queryParameters .= " :{$filter->table}_{$column}_{$i},";
                                    $parameters[":{$filter->table}_{$column}_{$i}"] = $field->value[$i];
                                }
                                $queryParameters = trim(rtrim($queryParameters, ","));

                                $query .= "\nAND {$filter->table}.{$column} IN ({$queryParameters})";
                            }
                        } else if ($field->comparison == "!=" || $field->comparison == "NOT_EQUAL" || $field->comparison == "NOT_IN") {
                            if (!is_array($field->value)) {
                                if ($field->value == null) {
                                    $query .= "\nAND {$filter->table}.{$column} is not null";
                                } else {
                                    $query .= "\nAND {$filter->table}.{$column} != :{$filter->table}_{$column}";
                                    $parameters[":{$filter->table}_{$column}"] = $field->value;
                                }
                            } else {
                                $queryParameters = '';
                                for ($i = 0; $i < count($field->value); $i++) {
                                    $queryParameters .= " :{$filter->table}_{$column}_{$i},";
                                    $parameters[":{$filter->table}_{$column}_{$i}"] = $field->value[$i];
                                }
                                $queryParameters = trim(rtrim($queryParameters, ","));

                                $query .= "\nAND {$filter->table}.{$column} NOT IN ({$queryParameters})";
                            }
                        } else if ($field->comparison == "%" || $field->comparison == "%%" || $field->comparison == "LIKE") {
                            $query .= "\nAND {$filter->table}.{$column} LIKE :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = "%{$field->value}%";
                        } else if ($field->comparison == "R%" || $field->comparison == "%R" || $field->comparison == "RLIKE") {
                            $query .= "\nAND {$filter->table}.{$column} LIKE :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = "{$field->value}%";
                        } else if ($field->comparison == "L%" || $field->comparison == "%L" || $field->comparison == "LLIKE") {
                            $query .= "\nAND {$filter->table}.{$column} LIKE :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = "%{$field->value}";
                        } else if ($field->comparison == "!%" || $field->comparison == "!%%" || $field->comparison == "NOT_LIKE") {
                            $query .= "\nAND {$filter->table}.{$column} NOT LIKE :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = "%{$field->value}%";
                        } else if ($field->comparison == "!R%" || $field->comparison == "!%R" || $field->comparison == "NOT_RLIKE") {
                            $query .= "\nAND {$filter->table}.{$column} NOT LIKE :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = "{$field->value}%";
                        } else if ($field->comparison == "!L%" || $field->comparison == "!%L" || $field->comparison == "NOT_LLIKE") {
                            $query .= "\nAND {$filter->table}.{$column} NOT LIKE :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = "%{$field->value}";
                        } else if ($field->comparison == ">" || $field->comparison == "BIGGER" || $field->comparison == "BIGGER_THAN") {
                            $query .= "\nAND {$filter->table}.{$column} > :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = $field->value;
                        } else if ($field->comparison == "<" || $field->comparison == "LESS" || $field->comparison == "LESSER_THAN") {
                            $query .= "\nAND {$filter->table}.{$column} < :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = $field->value;
                        } else if ($field->comparison == ">=" || $field->comparison == "BIGGER_EQUAL") {
                            $query .= "\nAND {$filter->table}.{$column} >= :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = $field->value;
                        } else if ($field->comparison == "<=" || $field->comparison == "LESSER_EQUAL") {
                            $query .= "\nAND {$filter->table}.{$column} <= :{$filter->table}_{$column}";
                            $parameters[":{$filter->table}_{$column}"] = $field->value;
                        } else if ($field->comparison == "BETWEEN") {
                            $query .= "\nAND {$filter->table}.{$column} BETWEEN :{$filter->table}_{$column}_1 AND :{$filter->table}_{$column}_2";
                            $parameters[":{$filter->table}_{$column}_1"] = $field->value1;
                            $parameters[":{$filter->table}_{$column}_2"] = $field->value2;
                        }
                    }
                }

                if (isset($filter->where) && !empty($filter->where)) {
                    $filter->where = str_replace('this->table', $filter->table, $filter->where);
                    $query .= "\n{$filter->where}";

                    // Valores de placeholders usados no 'where' (ex.: ':busca_1' => '%x%'),
                    // para não concatenar input do usuário no SQL.
                    if (isset($filter->parameters) && is_array($filter->parameters)) {
                        $parameters = array_merge($parameters, $filter->parameters);
                    }
                }
            }
        }

        return (object)['query' => $query, 'parameters' => $parameters, 'link_tables' => $link_tables];
    }

    public function getWithFiltersAllItems(
        $filters = [],
        $columns = [],
        $options = []
    ) {
        $filters = $this->mountSqlFromFilters($filters);
        $columns = $this->mountSqlFromColumns($columns);
        $joins = $this->mountSqlFromJoins($this->joins, array_merge($columns->link_tables, $filters->link_tables));

        $sql = "SELECT SQL_CALC_FOUND_ROWS {$columns->query} \nFROM {$this->table} {$joins->query} \nWHERE TRUE {$filters->query}";

        if ((isset($options['groupBy']) || isset($options['group'])) && (!empty($options['groupBy']) || !empty($options['group']))) {
            $options['group'] = str_replace('this->table', $this->table, (isset($options['group']) ? $options['group'] : $options['groupBy']));
            $sql .= "\nGROUP BY {$options['group']}";
        }

        if ((isset($options['orderBy']) || isset($options['order'])) && (!empty($options['orderBy']) || !empty($options['order']))) {
            $options['order'] = str_replace('this->table', $this->table, (isset($options['order']) ? $options['order'] : $options['orderBy']));
            $sql .= "\nORDER BY {$options['order']}";
        }

        if (isset($options['limit']) && $options['limit'] > 0 && isset($options['page'])) {
            $offset = ($options['page'] - 1) * $options['limit'];
            $sql .= "\nLIMIT {$options['limit']} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($filters->parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $this->db->query("SELECT FOUND_ROWS() AS `rows`")->fetch()->rows];
    }

    public function getItemWithFilters(
        $filters = [],
        $columns = [],
        $options = []
    ) {
        $filters = $this->mountSqlFromFilters($filters);
        $columns = $this->mountSqlFromColumns($columns);
        $joins = $this->mountSqlFromJoins($this->joins, array_merge($columns->link_tables, $filters->link_tables));

        $sql = "SELECT SQL_CALC_FOUND_ROWS {$columns->query} \nFROM {$this->table} {$joins->query} \nWHERE TRUE {$filters->query}";

        if ((isset($options['groupBy']) || isset($options['group'])) && (!empty($options['groupBy']) || !empty($options['group']))) {
            $options['group'] = str_replace('this->table', $this->table, (isset($options['group']) ? $options['group'] : $options['groupBy']));
            $sql .= "\nGROUP BY {$options['group']}";
        }

        if ((isset($options['orderBy']) || isset($options['order'])) && (!empty($options['orderBy']) || !empty($options['order']))) {
            $options['order'] = str_replace('this->table', $this->table, (isset($options['order']) ? $options['order'] : $options['orderBy']));
            $sql .= "\nORDER BY {$options['order']}";
        }

        if (isset($options['limit']) && $options['limit'] > 0 && isset($options['page'])) {
            $offset = ($options['page'] - 1) * $options['limit'];
            $sql .= "\nLIMIT {$options['limit']} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($filters->parameters);

        return $query->fetch();
    }

    /**
     * Obter um registro pelo ID
     * @param int $itemId ID do registro
     * @param array $columns Recebe um array de objetos
     * @return object error = bool, message = string, lastId = int
     */
    public function getItemById(
        $itemId,
        $columns = []
    ) {
        $columns = $this->mountSqlFromColumns($columns);
        $joins = $this->mountSqlFromJoins($this->joins, $columns->link_tables);

        $sql = "SELECT
                    {$columns->query}
                FROM {$this->table}
                {$joins->query}
                WHERE {$this->table}.id = :id";

        $query = $this->db->prepare($sql);
        $query->execute([':id' => $itemId]);

        return $query->fetch();
    }

    public function insert(array $array_post): object
    {
        foreach ($array_post as $key => $value) {
            $columnArray[] = "`$key`";
            $columnArrayPDO[] = ":{$key}";
            $parameters[":{$key}"] = ($value !== "" ? $value : NULL);
        }

        $column = implode(",", $columnArray);
        $pdo = implode(",", $columnArrayPDO);

        $sql = "INSERT INTO {$this->table} ({$column}) VALUES ({$pdo})";
        $query = $this->db->prepare($sql);
        $responseQuery = $query->execute($parameters);
        $lastId = $this->db->lastInsertId();
        $item = $this->getItemById($lastId);

        return (object)['error' => !$responseQuery, 'message' => $responseQuery ? 'Item cadastrado com sucesso' : 'Erro ao cadastrar esse item', 'lastId' => $lastId, 'item' => $item];
    }

    public function update(array $array_post, string $where_col, string $where_val)
    {
        foreach ($array_post as $key => $value) {
            $columnArray[] = "`{$key}` = :{$key}";
            $parameters[":{$key}"] = ($value != "" ? $value : NULL);
        }
        $parameters[':id'] = $where_val;

        $column = implode(",", $columnArray);

        $sql = "UPDATE {$this->table} SET {$column} WHERE {$where_col} = :id";
        $query = $this->db->prepare($sql);
        $responseQuery = $query->execute($parameters);

        return (object)['error' => !$responseQuery, 'message' => $responseQuery ? 'Item editado com successo' : 'Erro ao editar esse item'];
    }

    /**
     * @param array|int|string $column
     * array = ['id' => 1]
     * int = 1 Id do registro
     * string = AND id = 1
     */
    public function delete($column)
    {
        $where = '';
        $parameters = [];

        if (is_array($column)) {
            foreach ($column as $key => $value) {
                $where .= " AND {$key} = :{$key}";
                $parameters[":{$key}"] = $value;
            }
        } else if (is_numeric($column)) {
            $where .= " AND id = :id";
            $parameters[':id'] = $column;
        } else if (is_string($column)) {
            $where .= " $column";
        } else {
            return (object)['error' => true, 'message' => 'Parâmetro inválido'];
        }
        $where = ltrim($where, ' AND');

        $sql = "DELETE FROM {$this->table} WHERE {$where}";

        $query = $this->db->prepare($sql);
        $responseQuery = $query->execute($parameters);

        return (object)['error' => !$responseQuery, 'message' => $responseQuery ? 'Item excluido com successo' : 'Erro ao excluir esse item'];
    }

    public function enableItem($id)
    {
        $parameters[':id'] = $id;

        $sql = "UPDATE {$this->table} SET status = TRUE WHERE id = :id";
        $query = $this->db->prepare($sql);

        return $query->execute($parameters);
    }

    public function disableItem($id)
    {
        $parameters[':id'] = $id;

        $sql = "UPDATE {$this->table} SET status = FALSE WHERE id = :id";
        $query = $this->db->prepare($sql);

        return $query->execute($parameters);
    }
}
