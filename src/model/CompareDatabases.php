<?php

namespace RR\model;

use PDO;
use RR\core\Model;
use RR\libs\Util;

class CompareDatabases extends Model
{
    private $table;

    /**
     * @var PDO Database Connection for comparison
     */
    public $db_teste;

    function __construct()
    {
        try {
            $this->db_teste = Self::openDatabaseConnectionTest();
        } catch (\PDOException $error) {
            if (ENVIRONMENT == 'development') {
                echo "<pre>";
                var_dump($error->getMessage());
                var_dump((int)$error->getCode());
                exit;
            }
            exit('Sem conexão com o Banco de Dados do teste.');
        }

        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    private static function openDatabaseConnectionTest()
    {
        define('DB_HOST_TEST', 'mysql.ydeal.tec.br');
        define('DB_NAME_TEST', 'teste_ydealtec');
        define('DB_USER_TEST', 'teste_ydealtec');
        define('DB_PASS_TEST', 'iw72424288');
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => (ENVIRONMENT != 'development' ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_WARNING));
        return new PDO('mysql:host=' . DB_HOST_TEST . ';dbname=' . DB_NAME_TEST . ';charset=utf8', DB_USER_TEST, DB_PASS_TEST, $options);
    }

    public function compareStructureFromTwoDatabase($showSql = true)
    {
        $tables1 = $this->db_teste->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $tables2 = $this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        $t_created = [];

        $tablesDiff = array_diff($tables1, $tables2);
        foreach ($tablesDiff as $tableDiff) {
            $columns = $this->db_teste->query("SHOW COLUMNS FROM {$tableDiff}")->fetchAll(PDO::FETCH_CLASSTYPE);
            $sql = "CREATE TABLE `{$tableDiff}` (\n";
            $key = '';
            foreach ($columns as $column) {
                $sql .= "`{$column->Field}` {$column->Type}";
                if ($column->Null == 'NO') {
                    $sql .= ' NOT NULL';
                }
                if ($column->Default != null && !empty($column->Default)) {
                    if ($column->Type == 'timestamp' || is_numeric($column->Default)) {
                        $sql .= " DEFAULT {$column->Default}";
                    } else {
                        $sql .= " DEFAULT '{$column->Default}'";
                    }
                }
                if ($column->Extra != null && !empty($column->Extra)) {
                    $sql .= " {$column->Extra}";
                }
                if ($column->Key == 'PRI') {
                    $key .= " PRIMARY KEY (`{$column->Field}`)";
                }
                if ($column->Key == 'UNI') {
                    $key .= " UNIQUE KEY (`{$column->Field}`)";
                }
                $sql .= ", \n";
            }
            $sql .= $key;
            $sql .= "\n) ENGINE=InnoDB \nDEFAULT CHARSET=utf8mb4 \nCOLLATE=utf8mb4_general_ci;";

            if ($showSql === true) {
                Util::debug($sql);
                continue;
            }

            $query = $this->db->prepare($sql);
            $query->execute();
            $t_created[] = $tableDiff;
        }

        $c_added = [];
        $tables = $this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $columns1 = $this->db_teste->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_CLASSTYPE);
            $columns2 = $this->db->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_CLASSTYPE);

            $diff = [];
            foreach ($columns1 as $column1) {
                $found = false;
                foreach ($columns2 as $column2) {
                    if ($column1->Field == $column2->Field) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $diff[] = (object)[
                        'Table' => $table,
                        'Field' => $column1->Field,
                        'Type' => $column1->Type,
                        'Null' => $column1->Null,
                        'Default' => $column1->Default,
                        'Extra' => $column1->Extra,
                        'Key' => $column1->Key,
                    ];
                }
            }

            if (!empty($diff)) {
                $sqlColumns = "ALTER TABLE `{$table}` \n";
                $keyColumns = '';
                foreach ($diff as $column) {
                    $sqlColumns .= "ADD COLUMN `{$column->Field}` {$column->Type}";
                    if ($column->Null == 'NO') {
                        $sqlColumns .= ' NOT NULL';
                    }
                    if ($column->Default != null && !empty($column->Default)) {
                        if ($column->Type == 'timestamp' || is_numeric($column->Default)) {
                            $sqlColumns .= " DEFAULT {$column->Default}";
                        } else {
                            $sqlColumns .= " DEFAULT \"{$column->Default}\"";
                        }
                    }
                    if ($column->Extra != null && !empty($column->Extra)) {
                        $sqlColumns .= " {$column->Extra}";
                    }
                    if ($column->Key == 'PRI') {
                        $keyColumns .= " PRIMARY KEY (`{$column->Field}`)";
                    }
                    if ($column->Key == 'UNI') {
                        $keyColumns .= " UNIQUE KEY (`{$column->Field}`)";
                    }
                    $sqlColumns .= ", \n";
                }
                $sqlColumns .= $keyColumns;
                $sqlColumns = substr($sqlColumns, 0, -3);
                $sqlColumns .= ";";


                if ($showSql === true) {
                    Util::debug($sqlColumns);
                    continue;
                }

                $query = $this->db->prepare($sqlColumns);
                $query->execute();
                $c_added[] = (object)['Table' => $table, 'Columns' => $diff];
            }
        }

        if($showSql === true) {
            return;
        }

        return (object)[
            'TablesCreated' => $t_created,
            'ColumnsAdded' => $c_added,
        ];
    }

    /**
     * @param bool $insert
     * @return array Tables Difference
     */
    public function compareTables(bool $insert = false)
    {
        $tables1 = $this->db_teste->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $tables2 = $this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        $t_created = [];
        $tablesDiff = array_diff($tables1, $tables2);
        if ($insert === true) {
            foreach ($tablesDiff as $tableDiff) {
                $columns = $this->db_teste->query("SHOW COLUMNS FROM {$tableDiff}")->fetchAll(PDO::FETCH_CLASSTYPE);
                $sql = "CREATE TABLE `{$tableDiff}` (\n";
                $key = '';
                foreach ($columns as $column) {
                    $sql .= "`{$column->Field}` {$column->Type}";
                    if ($column->Null == 'NO') {
                        $sql .= ' NOT NULL';
                    }
                    if ($column->Default != null && !empty($column->Default)) {
                        if ($column->Type == 'timestamp' || is_numeric($column->Default)) {
                            $sql .= " DEFAULT {$column->Default}";
                        } else {
                            $sql .= " DEFAULT '{$column->Default}'";
                        }
                    }
                    if ($column->Extra != null && !empty($column->Extra)) {
                        $sql .= " {$column->Extra}";
                    }
                    if ($column->Key == 'PRI') {
                        $key .= " PRIMARY KEY (`{$column->Field}`)";
                    }
                    if ($column->Key == 'UNI') {
                        $key .= " UNIQUE KEY (`{$column->Field}`)";
                    }
                    $sql .= ", \n";
                }
                $sql .= $key;
                $sql .= "\n) ENGINE=InnoDB \nDEFAULT CHARSET=utf8mb4 \nCOLLATE=utf8mb4_general_ci;";

                $query = $this->db->prepare($sql);
                $query->execute();
            }
        }
        $t_created[] = $tableDiff;

        return (object)['Tables' => $t_created];
    }

    /**
     * @param null $option only list
     * @param int/one $option all
     * @param int/two $option insert
     * @param int/three $option update
     * @return array Columns
     */
    public function compareColumns(?int $option = null)
    {
        $c_added = [];
        $c_updated = [];
        $tables = $this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $columns1 = $this->db_teste->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_CLASSTYPE);
            $columns2 = $this->db->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_CLASSTYPE);

            /** @var string update */
            if (in_array($option, [1, 3])) {
                $diff = [];
                foreach ($columns1 as $column1) {
                    foreach ($columns2 as $column2) {
                        if ($column1->Field == $column2->Field) {
                            if ($column1->Type != $column2->Type) {
                                $diff[] = (object)[
                                    'Table' => $table,
                                    'Field' => $column1->Field,
                                    'Type' => $column1->Type,
                                    'Null' => $column1->Null,
                                    'Default' => $column2->Default,
                                    'Extra' => $column1->Extra,
                                    'Key' => $column1->Key,
                                ];
                            }
                            break;
                        }
                    }
                }

                if (!empty($diff)) {
                    $sqlUpdate = 'ALTER TABLE `' . $table . '` ';
                    $keyUpdate = '';
                    foreach ($diff as $column) {
                        $sqlUpdate .= "MODIFY COLUMN `{$column->Field}` {$column->Type}";
                        if ($column->Default != null && !empty($column->Default)) {
                            if ($column->Type == 'timestamp' || is_numeric($column->Default)) {
                                $sqlUpdate .= " DEFAULT {$column->Default}";
                            } else {
                                $sqlUpdate .= " DEFAULT '{$column->Default}'";
                            }
                        } else {
                            $sqlUpdate .= ' DEFAULT NULL';
                        }
                        if ($column->Null == 'NO') {
                            $sqlUpdate .= ' NOT NULL';
                        } else {
                            $sqlUpdate .= ' NULL';
                        }
                        if ($column->Extra != null && !empty($column->Extra)) {
                            $sqlUpdate .= " {$column->Extra}";
                        }
                        if ($column->Key == 'PRI') {
                            $keyUpdate .= " PRIMARY KEY (`{$column->Field}`)";
                        }
                        if ($column->Key == 'UNI') {
                            $keyUpdate .= " UNIQUE KEY (`{$column->Field}`)";
                        }
                        $sqlUpdate .= ", \n";
                    }
                    $sqlUpdate .= $keyUpdate;
                    $sqlUpdate = substr($sqlUpdate, 0, -3);
                    $sqlUpdate .= ";";

                    $query = $this->db->prepare($sqlUpdate);
                    $query->execute();
                    $c_updated[] = (object)['Table' => $table, 'Columns' => $diff];
                }
            }

            /** @var string insert */
            if (in_array($option, [1, 2])) {
                $diff = [];
                foreach ($columns1 as $column1) {
                    $found = false;
                    foreach ($columns2 as $column2) {
                        if ($column1->Field == $column2->Field) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $diff[] = (object)[
                            'Table' => $table,
                            'Field' => $column1->Field,
                            'Type' => $column1->Type,
                            'Null' => $column1->Null,
                            'Default' => $column1->Default,
                            'Extra' => $column1->Extra,
                            'Key' => $column1->Key,
                        ];
                    }
                }

                if (!empty($diff)) {
                    $sqlColumns = "ALTER TABLE `{$table}` \n";
                    $keyColumns = '';
                    foreach ($diff as $column) {
                        $sqlColumns .= "ADD COLUMN `{$column->Field}` {$column->Type}";
                        if ($column->Null == 'NO') {
                            $sqlColumns .= ' NOT NULL';
                        }
                        if ($column->Default != null && !empty($column->Default)) {
                            if ($column->Type == 'timestamp' || is_numeric($column->Default)) {
                                $sqlColumns .= " DEFAULT {$column->Default}";
                            } else {
                                $sqlColumns .= " DEFAULT \"{$column->Default}\"";
                            }
                        }
                        if ($column->Extra != null && !empty($column->Extra)) {
                            $sqlColumns .= " {$column->Extra}";
                        }
                        if ($column->Key == 'PRI') {
                            $keyColumns .= " PRIMARY KEY (`{$column->Field}`)";
                        }
                        if ($column->Key == 'UNI') {
                            $keyColumns .= " UNIQUE KEY (`{$column->Field}`)";
                        }
                        $sqlColumns .= ", \n";
                    }
                    $sqlColumns .= $keyColumns;
                    $sqlColumns = substr($sqlColumns, 0, -3);
                    $sqlColumns .= ";";

                    $query = $this->db->prepare($sqlColumns);
                    $query->execute();
                    $c_added[] = (object)['Table' => $table, 'Columns' => $diff];
                }
            }
        }

        return (object)[
            'Added' => $c_added,
            'Updated' => $c_updated,
        ];
    }

    public function createStructureAndDataForNewClients()
    {
        $tables1 = $this->db_teste->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $tables2 = $this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        $t_created = [];

        $tablesDiff = array_diff($tables1, $tables2);
        foreach ($tablesDiff as $tableDiff) {
            $columns = $this->db_teste->query("SHOW COLUMNS FROM {$tableDiff}")->fetchAll(PDO::FETCH_CLASSTYPE);
            $sql = "CREATE TABLE `{$tableDiff}` (\n";
            $key = '';
            foreach ($columns as $column) {
                $sql .= "`{$column->Field}` {$column->Type}";
                if ($column->Null == 'NO') {
                    $sql .= ' NOT NULL';
                }
                if ($column->Default != null && !empty($column->Default)) {
                    if ($column->Type == 'timestamp' || is_numeric($column->Default)) {
                        $sql .= " DEFAULT {$column->Default}";
                    } else {
                        $sql .= " DEFAULT '{$column->Default}'";
                    }
                }
                if ($column->Extra != null && !empty($column->Extra)) {
                    $sql .= " {$column->Extra}";
                }
                if ($column->Key == 'PRI') {
                    $key .= " PRIMARY KEY (`{$column->Field}`)";
                }
                if ($column->Key == 'UNI') {
                    $key .= " UNIQUE KEY (`{$column->Field}`)";
                }
                $sql .= ", \n";
            }
            $sql .= $key;
            $sql .= "\n) ENGINE=InnoDB \nDEFAULT CHARSET=utf8mb4 \nCOLLATE=utf8mb4_general_ci;";

            $query = $this->db->prepare($sql);
            $query->execute();
            $t_created[] = $tableDiff;
        }

        $c_added = [];
        $tables = $this->db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $columns1 = $this->db_teste->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_CLASSTYPE);
            $columns2 = $this->db->query("SHOW COLUMNS FROM {$table}")->fetchAll(PDO::FETCH_CLASSTYPE);

            $diff = [];
            foreach ($columns1 as $column1) {
                $found = false;
                foreach ($columns2 as $column2) {
                    if ($column1->Field == $column2->Field) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $diff[] = (object)[
                        'Table' => $table,
                        'Field' => $column1->Field,
                        'Type' => $column1->Type,
                        'Null' => $column1->Null,
                        'Default' => $column1->Default,
                        'Extra' => $column1->Extra,
                        'Key' => $column1->Key,
                    ];
                }
            }

            if (!empty($diff)) {
                $sqlColumns = "ALTER TABLE `{$table}` \n";
                $keyColumns = '';
                foreach ($diff as $column) {
                    $sqlColumns .= "ADD COLUMN `{$column->Field}` {$column->Type}";
                    if ($column->Null == 'NO') {
                        $sqlColumns .= ' NOT NULL';
                    }
                    if ($column->Default != null && !empty($column->Default)) {
                        if ($column->Type == 'timestamp' || is_numeric($column->Default)) {
                            $sqlColumns .= " DEFAULT {$column->Default}";
                        } else {
                            $sqlColumns .= " DEFAULT \"{$column->Default}\"";
                        }
                    }
                    if ($column->Extra != null && !empty($column->Extra)) {
                        $sqlColumns .= " {$column->Extra}";
                    }
                    if ($column->Key == 'PRI') {
                        $keyColumns .= " PRIMARY KEY (`{$column->Field}`)";
                    }
                    if ($column->Key == 'UNI') {
                        $keyColumns .= " UNIQUE KEY (`{$column->Field}`)";
                    }
                    $sqlColumns .= ", \n";
                }
                $sqlColumns .= $keyColumns;
                $sqlColumns = substr($sqlColumns, 0, -3);
                $sqlColumns .= ";";

                $query = $this->db->prepare($sqlColumns);
                $query->execute();
                $c_added[] = (object)['Table' => $table, 'Columns' => $diff];

                $arr = [
                    'attendance_status',
                    'attendance_filter_type',
                    'banks',
                    'branch',
                    'cities',
                    'configuracao',
                    'configuracao_email',
                    'customer_type',
                    'digital_card',
                    'form_of_payment',
                    'immovable_resource',
                    'marital_status',
                    'menu',
                    'notice',
                    'payment_status',
                    'person_type',
                    'presentation_card',
                    'professions',
                    'property_category',
                    'property_type',
                    'property_type_resources',
                    'rede_social',
                    'standard_contract',
                    'standard_contract_variables',
                    'status',
                    'status_icon',
                    'system_config',
                    'texto',
                    'type_contract',
                    'user_branches',
                    'users',
                    'users_profiles',
                    'users_network',
                    'network_card_digital'
                ];

                if (in_array($column, $arr)) {
                }
            }
        }

        return (object)[
            'TablesCreated' => $t_created,
            'ColumnsAdded' => $c_added,
        ];
    }
}
