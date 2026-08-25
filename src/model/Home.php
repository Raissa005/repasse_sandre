<?php

namespace RR\model;

use PDO;
use PDOException;
use RR\core\Model;
use RR\libs\Util;

class Home extends Model
{
    private $table;

    function __construct()
    {
        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getTenAttendances($filters)
    {
        $rows = 10;
        $page = 1;
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND atd.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != '') {
            $filtersQuery .= " AND atd.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['return_date']) && $filters['return_date'] != '') {
            $filtersQuery .= " AND year(atd.return_date) = year(:return_date) AND month(atd.return_date) = month(:return_date) AND day(atd.return_date) = day(:return_date) ";
            $parameters[':return_date'] = $filters['return_date'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    atd.*
                FROM attendance atd
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " atd.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getTenProperties($filters)
    {
        $rows = 10;
        $page = 1;
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != "") {
            $filtersQuery .= " AND pdts.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['registration_start_date']) && $filters['registration_start_date'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at >= :registration_start_date";
            $parameters[':registration_start_date'] = $filters['registration_start_date'];
        }

        if (isset($filters['registration_end_date']) && $filters['registration_end_date'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at < :registration_end_date";
            $parameters[':registration_end_date'] = $filters['registration_end_date'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND property_branch.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    pdts.*, pt.name as property_type_name, pc.name as category_name 
                FROM products pdts
                INNER JOIN property_category pc ON pc.id = pdts.id_property_category
                INNER JOIN property_type pt ON pt.id = pdts.id_residential_type
                LEFT JOIN property_branch ON property_branch.id_property = pdts.id
                LEFT JOIN branch ON branch.id = property_branch.id_branch
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " pdts.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getTenSales($filters): array
    {
        $rows = 10;
        $page = 1;
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['id_branch']) && !empty($filters['id_branch'])) {
            $filtersQuery .= " AND sl.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && !empty($filters['created_by'])) {
            $filtersQuery .= " AND sl.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['sales_manager']) && !empty($filters['sales_manager'])) {
            $filtersQuery .= " AND sl.sales_manager = :sales_manager";
            $parameters[':sales_manager'] = $filters['sales_manager'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    sl.*,
                    cp.profit,
                    cp.frt_value,
                    cp.id_construction,
                    pt.cod AS product_cod,
                    st.name AS status_name,
                    pt.name AS product_name,
                    ct.name AS customer_name
                FROM sales sl
                    INNER JOIN products pt ON pt.id = sl.id_product
                    INNER JOIN customer ct ON ct.id = sl.id_customer
                    INNER JOIN status st ON st.id = sl.id_sale_status
                    LEFT JOIN construction_properties cp ON cp.id_property = sl.id_product
                WHERE TRUE $filtersQuery
                    AND sl.status = 1
                ORDER BY sl.sale_date DESC";
        
        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getTenBillsToPayInstallment($filters)
    {
        $rows = 10;
        $page = 1;
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && !empty($filters['status'])) {
            $filtersQuery .= " AND btpi.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['status_payment']) && !empty($filters['status_payment'])) {
            $filtersQuery .= " AND btpi.status_payment = :status_payment";
            $parameters[':status_payment'] = $filters['status_payment'];
        }

        if (isset($filters['created_by']) && !empty($filters['created_by'])) {
            $filtersQuery .= " AND btpi.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    btpi.*,
                    btp.id_branch,
                    ct.company_name,
                    ct.fancy_name_company,
                    ct.name AS customer_name,
                    cc.name AS cost_center_name,
                    fop.name AS form_of_pay_name
                FROM bills_to_pay_installments btpi
                INNER JOIN form_of_payment fop ON btpi.id_form_of_payment = fop.id
                INNER JOIN bills_to_pay btp ON btpi.id_bills_to_pay = btp.id
                INNER JOIN customer ct ON btp.id_customer = ct.id
                INNER JOIN cost_center cc ON btp.id_cost_center = cc.id
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " btpi.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getPreferencesByUser($id_user)
    {
        $sql = "SELECT *
                FROM user_preferences
                WHERE :id_user = id_user";

        $query = $this->db->prepare($sql);
        $parameters = array(':id_user' => $id_user);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function insertPreferences($id_user)
    {
        $arrayPost = array(
            'id_user' => $id_user,
            'by_date' => $_GET['selectByDate'],
            'id_cost_center' => $_GET['selectCostCenter'],
        );

        try {
            $exist = self::getPreferencesByUser($id_user);

            if (isset($exist) && !empty($exist)) {
                (new UserPreferences)->update($arrayPost, 'id_user', $id_user);
            } else {
                (new UserPreferences)->insert($arrayPost);
            }
        } catch (PDOException $error) {
            header('location:' . URL . "home/dashboard/");
            exit;
        }
    }

    public function compareStructureFromTwoDatabase()
    {

        try {
            $this->db_teste = $this->openDatabaseConnectionTest();
        } catch (\PDOException $error) {
            if (ENVIRONMENT == 'development') {
                echo "<pre>";
                var_dump($error->getMessage());
                var_dump((int)$error->getCode());
                exit;
            }
            exit('Sem conexão com o Banco de Dados do teste.');
        }

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
            }
        }

        return (object)[
            'TablesCreated' => $t_created,
            'ColumnsAdded' => $c_added,
        ];
    }

    private static function openDatabaseConnectionTest()
    {
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => (ENVIRONMENT != 'development' ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_WARNING));
        return new PDO('mysql' . ':host=' . 'localhost' . ';dbname=' . '2021_more' . ';charset=' . 'utf8', 'root', '', $options);
    }
}
