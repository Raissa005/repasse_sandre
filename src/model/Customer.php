<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Secure;
use RR\libs\Util;

class Customer extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'customer';
        $joins = [
            (object)[
                'table' => 'client_type_resource_types',
                'join' => 'left',
                'where' => "{$this->table}.id = client_type_resource_types.id_customer"
            ],
            (object)[
                'table' => 'countries',
                'join' => 'inner',
                'where' => "{$this->table}.id_country = country.id"
            ],
            (object)[
                'table' => 'states',
                'join' => 'inner',
                'where' => "{$this->table}.uf_state = states.uf"
            ],
            (object)[
                'table' => 'cities',
                'join' => 'inner',
                'where' => "{$this->table}.id_city = cities.id"
            ],
            (object)[
                'table' => 'marital_status',
                'join' => 'inner',
                'where' => "{$this->table}.id_marital_status = marital_status.id"
            ],
            (object)[
                'table' => 'professions',
                'join' => 'inner',
                'where' => "{$this->table}.id_profession = professions.id"
            ],
            (object)[
                'table' => 'customer_branches',
                'join' => 'inner',
                'where' => "{$this->table}.id = customer_branches.id_customer"
            ],
            (object)[
                'table' => 'branch',
                'join' => 'inner',
                'where' => "{$this->table}.id_branch = branch.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }


    public function getAndFilterAllCustomer($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND cust.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_city']) && $filters['id_city'] != '') {
            $filtersQuery .= " AND cust.id_city = :id_city";
            $parameters[':id_city'] = $filters['id_city'];
        }

        if (isset($filters['id_state']) && $filters['id_state'] != '') {
            $filtersQuery .= " AND stt.id = :uf_state";
            $parameters[':uf_state'] = $filters['id_state'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND (ucase(cust.name) LIKE ucase(:name) OR ucase(cust.fancy_name_company) LIKE ucase(:name) OR (cust.person_registration LIKE :name) OR (cust.cnpj LIKE :name))";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != "") {
            $filtersQuery .= " AND customer_branches.id_branch IN (:id_branch)";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != "") {
            $filtersQuery .= " AND (cust.created_by = :created_by OR ctrt.id_customer_type = 11)";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['id_customer_type']) && $filters['id_customer_type'] != "") {
            $filtersQuery .= " AND ctty.id = :id_customer_type ";
            $parameters[':id_customer_type'] = $filters['id_customer_type'];
        }

        if (isset($filters['customer_type_in']) && !empty($filters['customer_type_in']) && $filters['customer_type_in'] != "") {
            $filtersQuery .= " AND ctty.id IN (";
            foreach ($filters['customer_type_in'] as $type) {
                $filtersQuery .= "$type, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['id_seller_manager']) && $filters['id_seller_manager'] != "") {
            $filtersQuery .= " AND ( cust.created_by = :id_seller_manager OR mst.id_manager = :id_seller_manager) ";
            $parameters[':id_seller_manager'] = $filters['id_seller_manager'];
        }

        $sql = "SELECT
                    cust.*,
                    ct.name as city_name,
                    bra.name as branch_name,
                    (
                        SELECT u.name
                        FROM users u
                        WHERE u.id = cust.created_by
                    ) AS user_name
                FROM customer cust
                LEFT JOIN client_type_resource_types ctrt ON cust.id = ctrt.id_customer
                LEFT JOIN customer_branches ON cust.id = customer_branches.id_customer
                INNER JOIN customer_type ctty ON ctty.id = ctrt.id_customer_type
                LEFT JOIN states stt ON cust.uf_state = stt.uf
                LEFT JOIN cities ct ON ct.id = cust.id_city
                INNER JOIN customer_branches cb ON cb.id_customer = cust.id
                INNER JOIN branch bra ON bra.id = cb.id_branch
                LEFT JOIN manager_team mst ON mst.id_seller = cust.created_by
                WHERE TRUE $filtersQuery
                GROUP BY cust.id";
        $sql .= isset($filters['order']) && $filters['order'] != "" ? " ORDER BY " . $filters['order'] : " ORDER BY COALESCE(cust.fancy_name_company, cust.name) ASC ";

        if ($rows > 0) {
            $offset = ($page - 1) * $rows;
            $sql .= " LIMIT $rows OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllCustomerWithProperties($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND c.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != "") {
            $filtersQuery .= " AND c.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != "") {
            $filtersQuery .= " AND p.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        $sql = "SELECT
                    c.*,
                    COALESCE(c.fancy_name_company, c.name) as identifier_name
                FROM products p
                INNER JOIN customer c ON c.id = p.id_owner
                LEFT JOIN client_type_resource_types ctrt ON c.id = ctrt.id_customer
                INNER JOIN customer_type ctty ON ctty.id = ctrt.id_customer_type
                WHERE TRUE
                AND ctty.id IN (9, 11)
                $filtersQuery
                GROUP BY c.id
                ORDER BY identifier_name ASC ";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getCountWithFiltersForItems(array $filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND cust.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND (ucase(cust.name) LIKE ucase(:name) OR ucase(cust.fancy_name_company) LIKE ucase(:name))";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != "") {
            $filtersQuery .= " AND customer_branches.id_branch IN (:idbranch)";
            $parameters[':idbranch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != "") {
            $filtersQuery .= " AND cust.created_by = :created_by OR ctrt.id_customer_type = 11";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['id_city']) && $filters['id_city'] != '') {
            $filtersQuery .= " AND cust.id_city = :id_city";
            $parameters[':id_city'] = $filters['id_city'];
        }

        if (isset($filters['id_state']) && $filters['id_state'] != '') {
            $filtersQuery .= " AND stt.id = :uf_state";
            $parameters[':uf_state'] = $filters['id_state'];
        }

        if (isset($filters['id_customer_type']) && $filters['id_customer_type'] != "") {
            $filtersQuery .= " AND ctty.id = :id_customer_type ";
            $parameters[':id_customer_type'] = $filters['id_customer_type'];
        }

        if (isset($filters['customer_type_in']) && !empty($filters['customer_type_in']) && $filters['customer_type_in'] != "") {
            $filtersQuery .= " AND ctty.id IN (";
            foreach ($filters['customer_type_in'] as $type) {
                $filtersQuery .= "$type, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['id_seller_manager']) && $filters['id_seller_manager'] != "") {
            $filtersQuery .= " AND ( cust.created_by = :id_seller_manager OR mst.id_manager = :id_seller_manager) ";
            $parameters[':id_seller_manager'] = $filters['id_seller_manager'];
        }

        $sql = "SELECT
                    cust.id
                FROM customer cust
                INNER JOIN branch bra ON bra.id = cust.id_branch
                LEFT JOIN client_type_resource_types ctrt ON cust.id = ctrt.id_customer
                LEFT JOIN customer_branches ON cust.id = customer_branches.id_customer
                INNER JOIN customer_type ctty ON ctty.id = ctrt.id_customer_type
                INNER JOIN states stt ON cust.uf_state = stt.uf
                INNER JOIN cities ct ON ct.id = cust.id_city
                LEFT JOIN manager_team mst ON mst.id_seller = cust.created_by
                WHERE TRUE $filtersQuery
                GROUP BY cust.id";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function getCustomerById($id)
    {
        $sql = "SELECT
                    cust.*,
                    cnt.name as country_name, cnt.id as id_country,
                    stt.name as state_name, stt.id as id_state, stt.uf,
                    ct.name as city_name,
                    msts.id as id_marital, msts.name as marital_name,
                    pro.name as profession,
                    ctrt.id_customer_type
                FROM customer cust
                INNER JOIN countries cnt ON cnt.id = cust.id_country
                LEFT JOIN states stt ON stt.uf = cust.uf_state
                LEFT JOIN cities ct ON ct.id = cust.id_city
                LEFT JOIN client_type_resource_types ctrt ON cust.id = ctrt.id_customer
                INNER JOIN marital_status msts ON msts.id = cust.id_marital_status
                INNER JOIN professions pro ON pro.id = cust.id_profession
                INNER JOIN customer_branches cb ON cb.id_customer = cust.id
                INNER JOIN branch bra ON bra.id = cb.id_branch
                WHERE cust.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getCustomerBySale($id_sale)
    {
        $sql = "SELECT
                    cust.name, cust.id_person_type, cust.nationality, pro.name AS profession, cust.rg, cust.person_registration AS cpf, cust.address, cust.number_address,
                    cust.neighborhood, ct.name AS city, cust.uf_state AS uf, cust.cep, cust.phone, cust.cellphone, cust.id_marital_status, cust.email
                FROM customer cust
                INNER JOIN cities ct ON ct.id = cust.id_city
                INNER JOIN professions pro ON pro.id = cust.id_profession
                LEFT JOIN sales s ON s.id_customer = cust.id
                WHERE s.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id_sale);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getCustomerByIdForContract($id)
    {
        $sql = "SELECT
                    cust.name AS nome,
                    cust.nationality AS nacionalidade,
                    pro.name AS profissao,
                    cust.rg AS rg,
                    cust.person_registration AS cpf,
                    cust.address AS endereco,
                    cust.number_address AS numeroEndereco,
                    cust.neighborhood AS bairroEndereco,
                    ct.name AS cidade,
                    cust.uf_state AS uf,
                    stts.name AS estado,
                    cnt.name AS pais,
                    cust.zip AS zip,
                    cust.cep AS cep,
                    cust.phone AS telefone,
                    cust.cellphone AS celular,
                    cust.id_marital_status,
                    ms.name AS estadoCivil,
                    cust.email, cust.complement AS complementoEndereco,
                    cust.company_name AS nome_razao,
                    cust.fancy_name_company AS nome_fantasia,
                    cust.cnpj AS cnpj
                FROM customer cust
                INNER JOIN cities ct ON ct.id = cust.id_city
                INNER JOIN states stts ON cust.uf_state = stts.uf
                INNER JOIN countries cnt ON cnt.id = cust.id_country
                INNER JOIN professions pro ON pro.id = cust.id_profession
                INNER JOIN marital_status ms ON cust.id_marital_status = ms.id
                WHERE cust.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getSpouseByIdCustomerForContract($id)
    {
        $sql = "SELECT
                    sc.name, sc.person_registration, sc.address, sc.number_address, sc.address_spouse, sc.cep, sc.neighborhood, sc.phone, sc.cellphone, sc.uf_state, sc.status,
                    sc.complement, sc.rg, sc.nationality, c.name as name_city, p.name as name_profession
                FROM spouse_customer sc
                INNER JOIN cities c ON c.id = sc.id_city
                INNER JOIN professions p ON p.id = sc.id_profession
                WHERE sc.id_spouse = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getCustomerByBranch($id, $profile)
    {
        $filtersQuery = "";
        $parameters = [];

        if ($profile != 1) {
            $filtersQuery .= " AND cust.id_branch = :id";
            $parameters['id'] = $id;
        }

        $sql = "SELECT
                    cust.id, cust.name, cust.nationality, cust.rg, cust.person_registration, cust.address, cust.number_address, cust.neighborhood, cust.cep, cust.cellphone,
                    cust.phone, cust.id_marital_status, cust.uf_state, cust.id_branch, stt.name as state_name, stt.id as id_state, ct.id as id_city, ct.name as city_name,
                    msts.id as id_marital, msts.name as marital_name, pro.id as id_profession, pro.name as profession, cust.email
                FROM customer cust
                INNER JOIN states stt ON cust.uf_state = stt.uf
                INNER JOIN cities ct ON ct.id = cust.id_city
                INNER JOIN marital_status msts ON msts.id = cust.id_marital_status
                INNER JOIN professions pro ON pro.id = cust.id_profession
                WHERE TRUE $filtersQuery
                AND cust.status = 1 ";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getSpouseByCustomerId($id)
    {
        $sql = "SELECT
                    cc.id, cc.name, cc.birth_date, cc.nationality, cc.rg, cc.person_registration, cc.address, cc.number_address, cc.neighborhood, cc.cep,
                    cc.cellphone, cc.phone, cc.address_spouse, cc.id_spouse, cc.uf_state, cc.complement,
                    cnt.name as country_name, cnt.id as id_country,
                    stt.name as state_name, stt.id as id_state,
                    ct.id as id_city, ct.name as city_name,
                    pro.id as id_profession, pro.name as profession
                FROM spouse_customer cc
                LEFT JOIN countries cnt ON cnt.id = cc.id_country
                LEFT JOIN states stt ON cc.uf_state = stt.uf
                LEFT JOIN cities ct ON ct.id = cc.id_city
                INNER JOIN professions pro ON pro.id = cc.id_profession
                WHERE cc.id_spouse = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAndCountType($idType)
    {
        $sql = "SELECT
                    cust.id, cust.fancy_name_company,
                    bra.name as branch_name, ctty.id AS id_type
                FROM customer cust
                INNER JOIN branch bra ON bra.id = cust.id_branch
                LEFT JOIN client_type_resource_types ctrt ON cust.id = ctrt.id_customer
                INNER JOIN customer_type ctty ON ctty.id = ctrt.id_customer_type
                WHERE TRUE
                AND cust.status = :status AND cust.id_branch = :id_branch AND ctty.id = :id_type
                GROUP BY cust.id";

        $query = $this->db->prepare($sql);
        $parameters = [':id_type' => $idType, ':status' => 1, ':id_branch' => $_SESSION['RR']->branch->current->id];
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function checkSession()
    {
        if (!isset($_SESSION['RR']->user->id)) {
            header("location:" . URL . "login");
            exit;
        }
    }

    public function submitFormAdd($post)
    {
        $arrCustomer = array(
            'id_person_type' => $post['id_person_type'],
            'name' => $post['name'],
            'birth_date' => !empty(trim($post['birth_date'])) ? $post['birth_date'] : NULL,
            'id_profession' => $post['id_profession'],
            'id_marital_status' => $post['id_marital_status'],
            'nationality' => $post['nationality'],
            'email' => $post['email'],
            'rg' => Util::removeNonNumericCharacters($post['rg']),
            'person_registration' => Util::removeNonNumericCharacters($post['person_registration']),
            'cellphone' => Util::removeNonNumericCharacters($post['cellphone']),
            'phone' => Util::removeNonNumericCharacters($post['phone']),
            'cep' => !empty($post['cep']) ? Util::removeNonNumericCharacters($post['cep']) : "",
            'zip' => !empty($post['zip']) ? $post['zip'] : "",
            'id_country' => $post['id_country'],
            'uf_state' => !empty($post['uf_state']) ? $post['uf_state'] : "",
            'id_city' => !empty($post['id_city']) ? $post['id_city'] : "",
            'neighborhood' => $post['neighborhood'],
            'address' => $post['address'],
            'number_address' => $post['number_address'],
            'complement' => $post['complement'],
            'id_branch' => $_SESSION['RR']->branch->current->id,
            'created_by' => $_SESSION['RR']->user->id
        );

        /**Pessoa Juridica */
        if ($post['id_person_type'] == 2) {
            $arrCustomer['company_name'] = $post['company_name'];
            $arrCustomer['fancy_name_company'] = $post['fancy_name_company'];
            $arrCustomer['cnpj'] = Util::removeNonNumericCharacters($post['cnpj']);
            $arrCustomer['state_registration'] = $post['state_registration'];
        }

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrCustomer);

            if (isset($post['branches']) && !empty($post['branches'])) {
                foreach ($post['branches'] as $branch_id) {
                    (new CustomerBranch)->insert(['id_customer' => $response->lastId, 'id_branch' => $branch_id]);
                }
            }

            /**Adicionando o tipo do cliente */
            foreach ($post['id_customer_type'] as $type) {
                $arrPostCustomerType = array('id_customer_type' => $type, 'id_customer' => $response->lastId);
                (new ClientTypeResourceTypes())->insert($arrPostCustomerType);
            }

            /**Se for casado */
            $spouseTrue = (new MaritalStatus())->getItemById($_POST['id_marital_status']);
            if ($spouseTrue->spouse == 1) {
                $arrPost = array("id_spouse" => $response->lastId, "created_by" => $_SESSION['RR']->user->id);
                (new SpouseCustomer())->insert($arrPost);
            }

            $this->db->commit();
            return (object)['error' => false, 'message' => 'Cliente cadastrado com sucesso!', 'lastId' => $response->lastId];
            exit;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao cadastrar cliente!'];
            exit;
        }
    }

    public function submitEditForm($itemId, $post)
    {
        $arrCustomer = array(
            'name' => $post['name'],
            'id_person_type' => $post['id_person_type'],
            'birth_date' => !empty(trim($post['birth_date'])) ? $post['birth_date'] : NULL,
            'id_profession' => $post['id_profession'],
            'id_marital_status' => $post['id_marital_status'],
            'id_country' => $post['id_country'],
            'uf_state' => !empty($post['uf_state']) ? $post['uf_state'] : "",
            'id_city' => !empty($post['id_city']) ? $post['id_city'] : "",
            'nationality' => $post['nationality'],
            'email' => $post['email'],
            'rg' => Util::removeNonNumericCharacters($post['rg']),
            'person_registration' => Util::removeNonNumericCharacters($post['person_registration']),
            'cellphone' => Util::removeNonNumericCharacters($post['cellphone']),
            'phone' => Util::removeNonNumericCharacters($post['phone']),
            'cep' => !empty($post['cep']) ? Util::removeNonNumericCharacters($post['cep']) : "",
            'zip' => !empty($post['zip']) ? $post['zip'] : "",
            'neighborhood' => $post['neighborhood'],
            'address' => $post['address'],
            'number_address' => $post['number_address'],
            'complement' => $post['complement'],
            'observation' => $post['observation'],
            'balance' => Util::unmaskMoney($_POST['balance']),
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

        if (Secure::access_secretary()) {
            $arrCustomer['created_by'] = $post['created_by'];
            $arrCustomer['blocked'] = isset($post['blocked']) && !empty($post['blocked']) ? 1 : 0;
        }

        /**Pessoa Juridica */
        if ($post['id_person_type'] == 2) {
            $arrCustomer['company_name'] = $post['company_name'];
            $arrCustomer['fancy_name_company'] = $post['fancy_name_company'];
            $arrCustomer['cnpj'] = Util::removeNonNumericCharacters($post['cnpj']);
            $arrCustomer['state_registration'] = $post['state_registration'];
        } else {
            $arrCustomer['company_name'] = null;
            $arrCustomer['fancy_name_company'] = null;
            $arrCustomer['cnpj'] = null;
            $arrCustomer['state_registration'] = null;
        }

        try {
            $this->db->beginTransaction();

            $this->update($arrCustomer, 'id', $itemId);

            if (Secure::access_admin()) {
                (new CustomerBranch)->delete(['id_customer' => $itemId]);
                if (empty($_POST['branches'])) {
                    (new CustomerBranch)->insert(['id_customer' => $itemId, 'id_branch' => $_SESSION['RR']->branch->current->id]);
                } else {
                    foreach ($_POST['branches'] as $branch) {
                        (new CustomerBranch)->insert(['id_customer' => $itemId, 'id_branch' => $branch]);
                    }
                }
            }

            $spouseTrue = (new MaritalStatus())->getItemById($post['id_marital_status']);
            if ($spouseTrue->spouse == 1) {
                $spouseExistent = (new SpouseCustomer())->getItemById($itemId);
                if (!$spouseExistent) {
                    $arrPostSpouse = array("id_spouse" => $itemId, "created_by" => $_SESSION['RR']->user->id);
                    (new SpouseCustomer())->insert($arrPostSpouse);
                }
            } else {
                (new SpouseCustomer())->delete($itemId);
            }

            (new ClientTypeResourceTypes())->delete(['id_customer' => $itemId]);

            /**Adicionando o tipo do cliente */
            foreach ($post['id_customer_type'] as $type) {
                $arrPostType = array('id_customer_type' => $type, 'id_customer' => $itemId);
                (new ClientTypeResourceTypes())->insert($arrPostType);
            }

            $this->db->commit();
            return (object)['error' => false, 'message' => 'Cliente atualizado com sucesso!'];
            exit;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao cadastrar cliente!'];
            exit;
        }
    }

    public function calculateCustomerCredit($customerId)
    {
        $customer = $this->getItemById($customerId);

        if (!isset($customer->balance) && empty($customer->balance)) {
            $customer->balance = 0;
            foreach ((new BillsToPayInstallment)->getWithFiltersAllItems([
                (object)['columns' => ['status_payment' => (object)['comparison' => 'EQUAL', 'value' => 9]]],
                (object)['table' => 'bills_to_pay', 'columns' => ['id_customer' => (object)['comparison' => 'EQUAL', 'value' => $customerId]]]
            ])->data as $bill) {
                if ($bill->payment_transaction == 3) {
                    /**Parcelas que geram crédito */
                    $customer->balance -= $bill->amount_paid - $bill->value_of_installments;
                }

                if ($bill->id_form_of_payment == 9) {
                    /**Todas as parcelas que foram pagas com o crédito */
                    $customer->balance += $bill->amount_paid;
                }
            }

            foreach ((new BillReceiveInstallment)->getWithFiltersAllItems([
                (object)['columns' => ['status_payment' => (object)['comparison' => 'EQUAL', 'value' => 9]]],
                (object)['table' => 'bill_receive', 'columns' => ['id_customer' => (object)['comparison' => 'EQUAL', 'value' => $customerId]]]
            ])->data as $bill) {
                if ($bill->payment_transaction == 3) {
                    /**Parcelas que geram crédito */
                    $customer->balance += $bill->amount_paid - $bill->value_installment;
                }

                if ($bill->id_form_of_payment == 9) {
                    /**Todas as parcelas que foram pagas com o crédito */
                    $customer->balance -= $bill->amount_paid;
                }
            }
        }

        return $customer->balance;
    }
}
