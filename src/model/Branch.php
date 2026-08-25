<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class Branch extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'branch';
        $joins = [
            (object)[
                'table' => 'user_branches',
                'join' => 'inner',
                'where' => "{$this->table}.id = user_branches.id_branch"
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function submitInsertForm($post)
    {
        $arrPost = array(
            "name" => $post['name'],
            "email" => $post['email'],
            "number" => $post['number'],
            "id_city" => $post['id_city'],
            "address" => $post['address'],
            "complement" => $post['complement'],
            'name_legal' => $post['name_legal'],
            'creci_legal' => $post['creci_legal'],
            "neighborhood" => $post['neighborhood'],
            "created_by" => $_SESSION['RR']->user->id,
            "restrict_owner_data" => $post['restrict'],
            "cnpj" => Util::removeNonNumericCharacters($post['cnpj'])
        );

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrPost);

            $usersPosition = (new UserPosition)->getAndFilterAllItems();
            foreach ($usersPosition->data as $position) {
                (new BranchUserPosition)->insert(['id_branch' => $response->lastId, 'id_user_position' => $position->id, 'origin_commission' => $position->origin_commission, 'created_by' => $_SESSION['RR']->user->id]);
            }

            if (!empty($post['property_branch'])) {
                $properties = explode(',', $post['property_branch']);
                foreach ($properties as $property) {
                    (new PropertyBranches)->insert([
                        'id_branch' => $response->lastId,
                        'id_property' => $property,
                    ]);
                }
            }

            foreach ((new User)->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['comparison' => 'IN', 'value' => [1, 5]]]]])->data as $user) {
                (new UserBranch)->insert(['id_user' => $user->id, 'id_branch' => $response->lastId]);
            }

            $this->db->commit();

            return (object)['error' => false, 'message' => 'Filial cadastrada com sucesso!', 'lastId' => $response->lastId];
        } catch (PDOException $error) {
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }

            return (object)['error' => true, 'message' => 'Erro ao cadastrar filial'];
        }
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
                    {$this->table}.*,
                    cities.name as city_name, cities.uf
                FROM {$this->table}
                INNER JOIN cities ON cities.id = {$this->table}.id_city
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

    /**Descontinuar */
    public function getAndFilterAllBranch($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND bra.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(bra.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    bra.*,
                    stts.uf,
                    ct.name AS city_name
                FROM branch bra
                LEFT JOIN cities ct ON ct.id = bra.id_city
                LEFT JOIN states stts ON stts.uf = ct.uf
                WHERE TRUE $filtersQuery
                ORDER BY bra.id ASC
                LIMIT $rows
                OFFSET $offset ";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getBranchesByProperties()
    {
        $sql = "SELECT
                    branch.*
                FROM products
                LEFT JOIN property_branch ON property_branch.id_property = products.id
                INNER JOIN branch ON property_branch.id_branch = branch.id
                WHERE products.status = 1
                AND branch.status = 1
                GROUP BY branch.id
                ORDER BY branch.name ASC";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    /**Descontinuar */
    public function getAllBranch()
    {
        $sql = "SELECT
                    bra.id, bra.name,
                    bra.id_city,
                    ct.name AS city_name
                FROM branch bra
                LEFT JOIN cities ct ON ct.id = bra.id_city
                WHERE bra.status = 1
                ORDER BY bra.name ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * @param int $itemId
     */
    public function getItemById8161($itemId)
    {
        $sql = "SELECT
                    {$this->table}.*,
                    cities.name AS city_name, cities.uf
                FROM {$this->table}
                INNER JOIN cities ON cities.id = {$this->table}.id_city
                WHERE {$this->table}.id = :itemId";

        $query = $this->db->prepare($sql);
        $parameters = array(':itemId' => $itemId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getBranchUsersPositions(int $branchId)
    {
        $sql = "SELECT
                    branch_user_position.id,
                    user_position.name,
                    branch_user_position.percentage_commission, COALESCE(branch_user_position.origin_commission, user_position.origin_commission) as origin_commission
                FROM user_position
                INNER JOIN branch_user_position ON branch_user_position.id_user_position = user_position.id AND user_position.status = 1
                WHERE branch_user_position.id_branch = :branchId";

        $query = $this->db->prepare($sql);
        $parameters = array(':branchId' => $branchId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getCitiesByState($uf)
    {
        $sql = "SELECT
                    ct.id, ct.name, stts.uf
                FROM cities ct
                LEFT JOIN states stts ON ct.uf = stts.uf
                WHERE stts.uf = :uf";

        $query = $this->db->prepare($sql);
        $parameters = array(':uf' => $uf);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getCitiesByStateId($id)
    {
        $sql = "SELECT
                    ct.id, ct.name, stts.uf
                FROM cities ct
                LEFT JOIN states stts ON ct.uf = stts.uf
                WHERE stts.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getBranchIdSale($id)
    {
        $sql = "SELECT
                    bra.*,
                    stts.uf,
                    ct.name AS city_name
                FROM branch bra
                LEFT JOIN cities ct ON ct.id = bra.id_city
                LEFT JOIN states stts ON ct.uf = stts.uf
                LEFT JOIN sales s ON s.id_branch = bra.id
                WHERE s.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getBranchIdSaleForContract($id)
    {
        $sql = "SELECT
                    bra.id, bra.name AS nome, bra.id_city, ct.name AS cidade, stts.id AS id_state, stts.uf, stts.name AS estado, bra.address AS endereco,
                    bra.neighborhood AS bairroEndereco, bra.number AS numeroEndereco, bra.complement AS complementoEndereco, bra.cnpj AS cnpj, bra.email, bra.cep
                FROM branch bra
                LEFT JOIN cities ct ON ct.id = bra.id_city
                LEFT JOIN states stts ON ct.uf = stts.uf
                LEFT JOIN sales s ON s.id_branch = bra.id
                WHERE s.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getBranchForContractById($branchId)
    {
        $sql = "SELECT
                    bra.id, bra.name AS nome, bra.id_city, bra.address AS endereco, bra.neighborhood AS bairroEndereco,bra.number AS numeroEndereco, bra.percentage_commission_sale AS comissao,
                    bra.complement AS complementoEndereco, bra.cnpj, bra.email, bra.cep, bra.creci_legal AS creci, bra.name_legal AS nomeJuridico, bra.immovable_record,
                    stts.id AS id_state, stts.uf, stts.name AS estado,
                    ct.name AS cidade
                FROM branch bra
                LEFT JOIN cities ct ON ct.id = bra.id_city
                LEFT JOIN states stts ON ct.uf = stts.uf
                LEFT JOIN sales s ON s.id_branch = bra.id
                WHERE bra.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $branchId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getBranchsByUser($userId)
    {
        $sql = "SELECT
                    branch.*
                FROM user_branches
                LEFT JOIN branch ON branch.id = user_branches.id_branch
                LEFT JOIN users ON users.id = user_branches.id_user
                WHERE users.id = :userId";

        $query = $this->db->prepare($sql);
        $parameters = array(":userId" => $userId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getBranchForSelect($user)
    {
        $sql = "SELECT
                    u.id, u.name, u.id_profile, up.access, b.name, ub.id_branch
                FROM users u
                LEFT JOIN users_profiles up ON u.id_profile = up.id
                LEFT JOIN user_branches ub ON u.id = ub.id_user
                LEFT JOIN branch b ON ub.id_branch = b.id
                WHERE u.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $user);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getBranchByArray($array)
    {
        $sql = "SELECT
                    *
                FROM branch b
                WHERE b.status = 1 ";

        if (!empty($array)) {
            $endOfArray = count($array) - 1;

            $sql .= "AND b.id IN (";
            foreach ($array as $key => $id) {
                $sql .= $key === $endOfArray ? "$id->id_branch" : "$id->id_branch,";
            }

            $sql .= ")";
        }

        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function deleteBranchUser($user)
    {
        $sql = "DELETE FROM user_branches
                WHERE id_user = :id_seller
                AND id_branch IN (
                    SELECT
                        T.*
                    FROM (
                        SELECT
                            ub2.id_branch
                        FROM user_branches ub2
                        WHERE ub2.id_user = :id_user
                    ) AS T
                )";

        $query = $this->db->prepare($sql);
        $parameters = array(
            ":id_seller" => $user,
            ":id_user" => $_SESSION['RR']->user->id
        );
        $query->execute($parameters);

        return true;
    }

    public function handleFormPosition(int $id, array $post)
    {
        try {
            $this->db->beginTransaction();

            $this->update([
                'id_cost_center_receive_commission' => $post['id_cost_center_receive_commission'],
                'id_customer_tax' => $post['id_customer_tax'],
                'id_cost_center_tax' => $post['id_cost_center_tax'],
                'id_form_payment_tax' => $post['id_form_payment_tax'],
                'id_form_payment_seller' => $post['id_form_payment_seller'],
                'id_cost_center_commission_seller' => $post['id_cost_center_commission_seller'],
                'days_after_tax' => $post['days_after_tax'],
                'days_after_seller' => $post['days_after_seller'],
                'id_form_payment_single' => $post['id_form_payment_single'],
                'days_after_single' => $post['days_after_single'],
            ], 'id', $id);

            foreach ($post['position'] as $key => $value) {
                $response = (new BranchUserPosition)->update(
                    [
                        'id_user' => $value['id_user'],
                        'id_form_payment' => $value['id_form_payment'],
                        'id_cost_center' => $value['id_cost_center'],
                        'days_after' => $value['days_after']
                    ],
                    'id',
                    $key
                );
            }

            $this->db->commit();

            return $response;
        } catch (PDOException $error) {

            $this->db->rollBack();
            if (ENVIRONMENT == 'development' || ENVIRONMENT == 'dev') {
                echo $error->getMessage();
                exit;
            }
            return (object)[
                'error' => true,
                'message' => $this->message_admins
            ];
        }
    }

    public function submitPaymentOfSales(int $itemId): object
    {
        $array = [
            "id_cost_center_receive_commission" => $_POST['id_cost_center_receive_commission'],
            "id_form_payment_single" => $_POST['id_form_payment_single'],
        ];

        try {
            $this->db->beginTransaction();
            $response = $this->update($array, 'id', $itemId);

            if ($response->error) throw new PDOException();

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao editar o item.', 'details' => (object)['message' => $error->getMessage(), 'code' => $error->getCode()]];
        }
    }
}
