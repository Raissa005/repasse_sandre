<?php

namespace RR\model;

use PDOException;
use RR\libs\Util;
use RR\core\Model;
use RR\libs\Secure;

class Property extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'products';
        $joins = [
            (object)['table' => 'cities', 'join' => 'INNER', 'where' => "{$this->table}.id_city = this->table.id"],
            (object)['table' => 'state', 'join' => 'INNER', 'where' => "{$this->table}.uf_state = this->table.uf"],
            (object)['table' => 'property_category', 'join' => 'INNER', 'where' => "{$this->table}.id_property_category = this->table.id"],
            (object)['table' => 'property_type', 'join' => 'INNER', 'where' => "{$this->table}.id_residential_type = this->table.id"],
            (object)['table' => 'users', 'join' => 'INNER', 'where' => "{$this->table}.created_by = this->table.id"],
            (object)['table' => 'property_branch', 'join' => 'LEFT', 'where' => "{$this->table}.id = this->table.id_property"],
            (object)['table' => 'branch', 'join' => 'LEFT', 'where' => "{$this->table}.id_branch = this->table.id"],
            (object)['table' => 'sales', 'join' => 'LEFT', 'where' => "{$this->table}.id = this->table.id_product"]
        ];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItems($filters, $options = [])
    {

        $parameters = [];
        $filtersQuery = '';

        foreach ($filters as $column => $value) {
            if ($value != '') {
                $table = $this->table;

                if (in_array($column, ['status', 'id_city', 'site_status', 'id_branch', 'created_by', 'id_user_owner', 'neighborhood', 'allotment'])) {
                    if ($column == 'id_branch') {
                        /**Filiais com visualização do imóvel */
                        $table = 'property_branch';
                    }
                    $filtersQuery .= " AND {$table}.{$column} = :{$table}_{$column}";
                } else if (in_array($column, ['id', 'id_residential_type', 'id_property_category', 'id_owner', 'property_branch'])) {
                    if (!empty($value)) {

                        if ($column == 'property_branch') {
                            /**Filial do cadastro do imóvel */
                            $column = 'id_branch';
                        }
                        $filtersQuery .= " AND {$table}.{$column} IN (" . (implode(", ", $value)) . ")";
                    }
                } else if (in_array($column, ['cod', 'name', 'address'])) {
                    $filtersQuery .= " AND ucase({$table}.{$column}) LIKE ucase(:{$table}_{$column})";
                    $value = "%" . $value . "%";
                } else if (in_array($column, ['price'])) {
                    if (isset($value['start'])) {
                        $filtersQuery .= " AND {$table}.`value` >= :{$table}_{$column}_start";
                        $parameters[":{$table}_{$column}_start"] = $value['start'];
                    }

                    if (isset($value['end'])) {
                        $filtersQuery .= " AND {$table}.`value` <= :{$table}_{$column}_end";
                        $parameters[":{$table}_{$column}_end"] = $value['end'];
                    }
                } else if ($column == "availability") {
                    if ($value == 1) {
                        $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = {$this->table}.id LIMIT 1) is null THEN true ELSE false END";
                    } else {
                        $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = {$this->table}.id LIMIT 1) is null THEN false ELSE true END";
                    }
                } else if ($column == "availability_sale") {
                    $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sles.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = {$this->table}.id AND sales.id_product != {$value} LIMIT 1) is null THEN true ELSE false END";
                }

                if (in_array($column, ['status', 'id_city', 'site_status', 'id_branch', 'created_by', 'id_user_owner', 'name', 'cod', 'address', 'neighborhood', 'allotment']) && !($column == 'id_branch' && $table == $this->table)) {
                    $parameters[":{$table}_{$column}"] = $value;
                }
            } else if ($column == 'id') {
                return [];
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.*, COALESCE({$this->table}.cod, {$this->table}.id) AS identifier,
                    branch.name AS branch_name,
                    cities.uf, cities.name AS city_name,
                    states.name AS state_name,
                    property_category.name AS category_name,
                    property_type.name AS property_type_name,
                    users.name AS user_name,
                    pc.name AS property_classification_name,
                    cst.name AS property_owner,
                    case
                        when (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = products.id limit 1) is null then true else false
                    end as `availability`
                FROM {$this->table}
                INNER JOIN cities ON cities.id = {$this->table}.id_city
                INNER JOIN states ON states.uf = {$this->table}.uf_state
                INNER JOIN property_category ON property_category.id = {$this->table}.id_property_category
                INNER JOIN property_type ON property_type.id = {$this->table}.id_residential_type
                INNER JOIN customer cst ON cst.id = {$this->table}.id_owner
                LEFT JOIN users ON users.id = {$this->table}.created_by
                LEFT JOIN property_branch ON property_branch.id_property = {$this->table}.id
                LEFT JOIN property_classification pc ON pc.id = {$this->table}.id_property_classification
                LEFT JOIN branch ON branch.id = property_branch.id_branch
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

    public function getAndFilterAllPropertiesIntegration($rows, $filters, $page): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['sync'])  && $filters['sync'] != '') {
            $filtersQuery .= " AND synced_integration LIKE :synced_integration";
            $parameters[':synced_integration'] = $filters['sync'];
        }

        $sql = "SELECT
                    id, cod, name, value, total_area, updated_at, status, synced_integration, created_at, id_property_integration
                FROM products p
                INNER JOIN property_integration pi on p.id = pi.id_property 
                WHERE TRUE $filtersQuery ORDER BY synced_integration ASC";

        $sqlRows = $sql;

        if (isset($rows)) {
            $offset = ($page - 1) * $rows;
            $sql .= " LIMIT {$rows} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        $queryRows = $this->db->prepare($sqlRows);
        $queryRows->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $queryRows->rowCount()];
    }

    public function getPropertyReport($filters, $options = [])
    {

        $parameters = [];
        $filtersQuery = '';

        foreach ($filters as $column => $value) {
            if ($value != '') {
                $table = $this->table;

                if (in_array($column, ['status', 'id_city', 'site_status', 'id_branch', 'created_by', 'id_user_owner', 'neighborhood', 'allotment'])) {
                    if ($column == 'id_branch') {
                        /**Filiais com visualização do imóvel */
                        $table = 'property_branch';
                    }
                    $filtersQuery .= " AND {$table}.{$column} = :{$table}_{$column}";
                } else if (in_array($column, ['id', 'id_residential_type', 'id_property_category', 'id_owner', 'property_branch'])) {
                    if (!empty($value)) {

                        if ($column == 'property_branch') {
                            /**Filial do cadastro do imóvel */
                            $column = 'id_branch';
                        }
                        $filtersQuery .= " AND {$table}.{$column} IN (" . (implode(", ", $value)) . ")";
                    }
                } else if (in_array($column, ['cod', 'name', 'address'])) {
                    $filtersQuery .= " AND ucase({$table}.{$column}) LIKE ucase(:{$table}_{$column})";
                    $value = "%" . $value . "%";
                } else if (in_array($column, ['price'])) {
                    if (isset($value['start'])) {
                        $filtersQuery .= " AND {$table}.`value` >= :{$table}_{$column}_start";
                        $parameters[":{$table}_{$column}_start"] = $value['start'];
                    }

                    if (isset($value['end'])) {
                        $filtersQuery .= " AND {$table}.`value` <= :{$table}_{$column}_end";
                        $parameters[":{$table}_{$column}_end"] = $value['end'];
                    }
                } else if ($column == "availability") {
                    if ($value == 1) {
                        $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = {$this->table}.id LIMIT 1) is null THEN true ELSE false END";
                    } else {
                        $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = {$this->table}.id LIMIT 1) is null THEN false ELSE true END";
                    }
                } else if ($column == "availability_sale") {
                    $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sles.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = {$this->table}.id AND sales.id_product != {$value} LIMIT 1) is null THEN true ELSE false END";
                }

                if (in_array($column, ['status', 'id_city', 'site_status', 'id_branch', 'created_by', 'id_user_owner', 'name', 'cod', 'address', 'neighborhood', 'allotment']) && !($column == 'id_branch' && $table == $this->table)) {
                    $parameters[":{$table}_{$column}"] = $value;
                }
            } else if ($column == 'id') {
                return [];
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.id,
                    {$this->table}.name,
                    {$this->table}.value,
                    {$this->table}.status,
                    COALESCE({$this->table}.cod, {$this->table}.id) AS identifier,
                    GROUP_CONCAT(DISTINCT branch.name) AS branch_name,
                    cities.uf, cities.name AS city_name,
                    states.name AS state_name,
                    property_category.name AS category_name,
                    property_type.name AS property_type_name,
                    users.name AS user_name,
                    case
                        when (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = products.id limit 1) is null then true else false
                    end as `availability`
                FROM {$this->table}
                INNER JOIN cities ON cities.id = {$this->table}.id_city
                INNER JOIN states ON states.uf = {$this->table}.uf_state
                INNER JOIN property_category ON property_category.id = {$this->table}.id_property_category
                INNER JOIN property_type ON property_type.id = {$this->table}.id_residential_type
                LEFT JOIN users ON users.id = {$this->table}.created_by
                LEFT JOIN property_branch ON property_branch.id_property = {$this->table}.id
                LEFT JOIN branch ON branch.id = property_branch.id_branch
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

    public function getItemById1002($id)
    {
        $sql = "SELECT
                    {$this->table}.*,
                    cities.name AS city_name, cities.uf,
                    states.name AS state_name,
                    property_type.name AS property_type_name,
                    property_category.name AS property_category_name,
                    branch.name AS branch_name
                FROM {$this->table}
                INNER JOIN cities ON {$this->table}.id_city = cities.id
                INNER JOIN states ON states.uf = {$this->table}.uf_state
                INNER JOIN property_type ON {$this->table}.id_residential_type = property_type.id
                INNER JOIN property_category ON {$this->table}.id_property_category = property_category.id
                INNER JOIN branch ON {$this->table}.id_branch = branch.id
                WHERE {$this->table}.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    /**
     * @param array $post
     */
    public function createItem($post)
    {
        $arrayPost = [];

        try {
            $this->db->beginTransaction();

            $response = (new ManagerPost())->insert($arrayPost, $this->table, ['name']);

            $_SESSION['YP']['toast'] = (object)[
                'icon' => ($response->error === true ? 'error' : 'success'),
                'title' => $response->message,
            ];

            $this->db->commit();

            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            echo $error->getMessage();
            exit;
        }
    }

    /**
     * @param int $int
     * @param array $post
     */
    public function updateItem($id, $post)
    {
        $fieldRequired = ['name', 'status'];

        $item = $this->getItemById($id);

        $post['cod'] = ($post['cod'] && !empty(trim($post['cod'])) ? trim($post['cod']) : $id);
        $post['value'] = Util::unMaskMoney($post['value']);
        $post['installment_value'] = Util::unMaskMoney($post['installment_value']);
        $post['site_status'] = (!$post['status'] ? 0 : $item->site_status);
        $post['url'] = Util::slugify((!empty($item->site_name) ? trim($item->site_name) : trim($item->name)) . "-" . ($post['cod'] && !empty($post['cod']) ? trim($post['cod']) : $id));
        $post['created_by'] = (isset($post['created_by']) && Secure::access_secretary() ? $post['created_by'] : $item->created_by);

        $arrayPost = array(
            'cod' => $post['cod'],
            'name' => $post['name'],
            'id_residential_type' => $post['id_residential_type'],
            'id_property_category' => $post['id_property_category'],
            'id_property_classification' => $post['id_property_classification'],
            'id_owner' => $post['id_owner'],
            'uf_state' => $post['uf_state'],
            'id_city' => $post['id_city'],
            'neighborhood' => $post['neighborhood'],
            'address' => $post['address'],
            'number' => $post['number'],
            'total_area' => $post['total_area'],
            'complement' => $post['complement'],
            'value' => $post['value'],
            'installment_value' => $post['installment_value'],
            'condition_product' => $post['condition_product'],
            'status' => $post['status'],
            'site_status' => $post['site_status'],
            'url' => $post['url'],
            'description' => $post['description'],
            'created_by' => $post['created_by'],
            'updated_by' => $_SESSION['YP']->user->id,
        );

        try {
            $this->db->beginTransaction();

            $response = (new ManagerPost())->update($arrayPost, $this->table, 'id', $id, $fieldRequired);

            (new ManagerPost())->delete('property_branch', ['id_property' => $id]);

            switch (isset($post['id_branch']) && $post['id_branch'] != "" ? $post['id_branch'] : null) {
                case (null):
                    (new ManagerPost())->insert(['id_property' => $id, 'id_branch' => $item->id_branch], 'property_branch');
                    break;

                case ($post['id_branch'] && count((new Branch)->getAllBranch()) != count($post['id_branch']) && $post['id_branch'] >= 2):
                    foreach ($post['id_branch'] as $branchItem) {
                        (new ManagerPost())->insert(['id_property' => $id, 'id_branch' => $branchItem], 'property_branch');
                    }
                    (new ManagerPost())->insert(['id_property' => $id, 'id_branch' => $item->id_branch], 'property_branch');
                    break;
            }

            $productOwnershipFeature = $this->getAllProductOwnershipFeatureByIdProduct($id);
            if (isset($post['propertyTypeResources'])) {
                $itens = Util::compareArray($productOwnershipFeature, 'id_property_type_resources', $post['propertyTypeResources']);

                if (!empty($itens['add'])) {
                    foreach ($itens['add'] as $add) {
                        (new ManagerPost())->insert(['id_product' => $id, 'id_property_type_resources' => $add, 'created_by' => $_SESSION['YP']->user->id], 'product_ownership_feature');
                    }
                }

                if (!empty($itens['exc'])) {
                    foreach ($itens['exc'] as $exc) {
                        (new ManagerPost())->delete('product_ownership_feature', ['id' => $exc->id]);
                    }
                }
            } else {
                (new ManagerPost())->delete('product_ownership_feature', ['id_product' => $id]);
            }

            $_SESSION['YP']['toast'] = (object)[
                'icon' => ($response->error === true ? 'error' : 'success'),
                'title' => $response->message,
            ];

            $this->db->commit();

            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            echo $error->getMessage();
            exit;
        }
    }

    public function getTheFirstImageOfTheProperty($propertyId)
    {
        $sql = "SELECT
                    products_imgs.id, products_imgs.extension
                FROM products_imgs
                WHERE id_product = :id_product
                ORDER BY status_site DESC, item_order ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $propertyId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function checkTheAvailabilityOfThePropertyForSale($propertyId)
    {
        $sql = "SELECT
                    *
                FROM sales
                WHERE sales.id_product = :id_product";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $propertyId);
        $query->execute($parameters);

        return $query->fetch() ? true : false;
    }

    /**Descontinuar */
    public function getAndFilterAllProducts($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_city']) && $filters['id_city'] != '') {
            $filtersQuery .= " AND pdts.id_city = :id_city";
            $parameters[':id_city'] = $filters['id_city'];
        }

        if (isset($filters['id_city_in']) && !empty($filters['id_city_in']) && $filters['id_city_in'] != "") {
            $filtersQuery .= " AND pdts.id_city IN (";
            foreach ($filters['id_city_in'] as $city) {
                $filtersQuery .= "$city, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['site_status']) && $filters['site_status'] != '') {
            $filtersQuery .= " AND pdts.site_status = :site_status";
            $parameters[':site_status'] = $filters['site_status'];
        }

        if (isset($filters['id_owner']) && $filters['id_owner'] != '') {
            $filtersQuery .= " AND pdts.id_owner = :id_owner";
            $parameters[':id_owner'] = $filters['id_owner'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND pb.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['id_attendance']) && $filters['id_attendance'] != '') {
            $parameters[':id_attendance'] = $filters['id_attendance'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(pdts.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['identical_name']) && $filters['identical_name'] != "") {
            $filtersQuery .= " AND pdts.name = :identical_name";
            $parameters[':identical_name'] = $filters['identical_name'];
        }

        if (isset($filters['cod']) && $filters['cod'] != "") {
            $filtersQuery .= " AND ucase(pdts.cod) LIKE ucase(:cod) ";
            $parameters[':cod'] =  $filters['cod'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != "") {
            $filtersQuery .= " AND pdts.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['initial_price']) && $filters['initial_price'] != "") {
            $filtersQuery .= " AND pdts.value >= :initial_price";
            $parameters[':initial_price'] = Util::unmaskMoney($filters['initial_price']);
        }

        if (isset($filters['final_price']) && $filters['final_price'] != "") {
            $filtersQuery .= " AND pdts.value <= :final_price";
            $parameters[':final_price'] = Util::unmaskMoney($filters['final_price']);
        }

        if (isset($filters['initial_price2']) && $filters['initial_price2'] != "") {
            $filtersQuery .= " AND pdts.value >= :initial_price";
            $parameters[':initial_price'] = ($filters['initial_price2']);
        }

        if (isset($filters['final_price2']) && $filters['final_price2'] != "") {
            $filtersQuery .= " AND pdts.value <= :final_price";
            $parameters[':final_price'] = ($filters['final_price2']);
        }

        if (isset($filters['price'])) {

            if (isset($filters['price']->start_price)) {
                $filtersQuery .= " AND pdts.value >= :start_price";
                $parameters[':start_price'] = ($filters['price']->start_price);
            }

            if (isset($filters['price']->end_price)) {
                $filtersQuery .= " AND pdts.value <= :end_price";
                $parameters[':end_price'] = ($filters['price']->end_price);
            }
        }

        if (isset($filters['category']) && $filters['category'] != "") {
            $filtersQuery .= " AND pdts.id_property_category = :category";
            $parameters[':category'] = $filters['category'];
        }

        if (isset($filters['category_in']) && !empty($filters['category_in']) && $filters['category_in'] != "") {
            $filtersQuery .= " AND pdts.id_property_category IN (";
            foreach ($filters['category_in'] as $category) {
                $filtersQuery .= "$category, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['property_type']) && $filters['property_type'] != "") {
            $filtersQuery .= " AND pdts.id_residential_type = :property_type";
            $parameters[':property_type'] = $filters['property_type'];
        }

        if (isset($filters['property_type_in']) && !empty($filters['property_type_in']) && $filters['property_type_in'] != "") {
            $filtersQuery .= " AND pdts.id_residential_type IN (";
            foreach ($filters['property_type_in'] as $property_type) {
                $filtersQuery .= "$property_type, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['idsProducts']) && is_array($filters['idsProducts'])) {
            if (!empty($filters['idsProducts'])) {
                $filtersQuery .= " AND pdts.id IN (";

                foreach ($filters['idsProducts'] as $value) {
                    $filtersQuery .= "$value, ";
                }

                $filtersQuery = rtrim($filtersQuery, ", ");
                $filtersQuery .= ")";
            } else {
                return [];
            }
        }

        if (isset($filters['registration_start_date']) && $filters['registration_start_date'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at >= :registration_start_date";
            $parameters[':registration_start_date'] = $filters['registration_start_date'];
        }

        if (isset($filters['registration_end_date']) && $filters['registration_end_date'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at < :registration_end_date";
            $parameters[':registration_end_date'] = $filters['registration_end_date'];
        }

        if (isset($filters['date1']) && $filters['date1'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at >= :date1";
            $parameters[':date1'] = $filters['date1'] . " 00:00:00";
        }

        if (isset($filters['date2']) && $filters['date2'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at <= :date2";
            $parameters[':date2'] = $filters['date2'] . " 23:59:59";
        }

        if (isset($filters['expire']) && $filters['expire'] != '' && $filters['expire'] == 0) {
            $expire_at = "<";
            $filtersQuery .= " AND pdts.re_registered_at $expire_at :atualData";
            $parameters[':atualData'] = date("Y-m-d");
        } else if (isset($filters['expire']) && $filters['expire'] != '' && $filters['expire'] == 1) {
            $expire_at = ">=";
            $filtersQuery .= " AND (pdts.re_registered_at $expire_at :atualData OR pdts.re_registered_at is null)";
            $parameters[':atualData'] = date("Y-m-d");
        }

        if (isset($filters['month']) && $filters['month'] != '') {
            $filtersQuery .= " AND month(pdts.re_registered_at) = :mes";
            $parameters[':mes'] = $filters['month'];
        }

        if (isset($filters['year']) && $filters['year'] != '') {
            $filtersQuery .= " AND year(pdts.re_registered_at) = :ano";
            $parameters[':ano'] = $filters['year'];
        }

        if (isset($filters["availability"])) {
            $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_status !=9 AND sales.status = 1 AND sales.id_product = pdts.id LIMIT 1) is null THEN true ELSE false END";
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    pdts.*,
                    COALESCE(pdts.cod, pdts.id) AS filter_cod,
                    br.name AS branch_name,
                    ct.uf, ct.name AS city_name,
                    sts.name AS state_name,
                    pc.name AS category_name,
                    pt.name as property_type_name,
                    (   SELECT
                            u.name
                        FROM users u
                        WHERE u.id = pdts.created_by
                    ) AS user_name,
                    (   SELECT
                            pdtsi.id
                        FROM products_imgs pdtsi
                        WHERE pdtsi.id_product = pdts.id
                        ORDER BY pdtsi.status_site DESC, pdtsi.item_order ASC
                        LIMIT 1
                    ) AS id_property_cover_image,
                    (   SELECT
                            pdtsi.extension
                        FROM products_imgs pdtsi
                        WHERE pdtsi.id_product = pdts.id
                        ORDER BY pdtsi.status_site DESC, pdtsi.item_order ASC
                        LIMIT 1
                    ) AS property_cover_image_extension
                FROM products pdts
                INNER JOIN cities ct ON ct.id = pdts.id_city
                INNER JOIN states sts ON sts.uf = pdts.uf_state
                INNER JOIN property_category pc ON pc.id = pdts.id_property_category
                INNER JOIN property_type pt ON pt.id = pdts.id_residential_type
                LEFT JOIN product_ownership_feature pof ON pdts.id = pof.id_product
                LEFT JOIN property_type_resources ptr ON ptr.id = pof.id_property_type_resources
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                LEFT JOIN branch br ON br.id = pb.id_branch
                WHERE TRUE $filtersQuery
                GROUP BY pdts.id
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " pdts.cod ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getCountWithFiltersForItems($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_city']) && $filters['id_city'] != '') {
            $filtersQuery .= " AND pdts.id_city = :id_city";
            $parameters[':id_city'] = $filters['id_city'];
        }

        if (isset($filters['id_city_in']) && !empty($filters['id_city_in']) && $filters['id_city_in'] != "") {
            $filtersQuery .= " AND pdts.id_city IN (";
            foreach ($filters['id_city_in'] as $city) {
                $filtersQuery .= "$city, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['site_status']) && $filters['site_status'] != '') {
            $filtersQuery .= " AND pdts.site_status = :site_status";
            $parameters[':site_status'] = $filters['site_status'];
        }

        if (isset($filters['id_owner']) && $filters['id_owner'] != '') {
            $filtersQuery .= " AND pdts.id_owner = :id_owner";
            $parameters[':id_owner'] = $filters['id_owner'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND pb.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['id_attendance']) && $filters['id_attendance'] != '') {
            $parameters[':id_attendance'] = $filters['id_attendance'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(pdts.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['identical_name']) && $filters['identical_name'] != "") {
            $filtersQuery .= " AND pdts.name = :identical_name";
            $parameters[':identical_name'] = $filters['identical_name'];
        }

        if (isset($filters['cod']) && $filters['cod'] != "") {
            $filtersQuery .= " AND ucase(pdts.cod) LIKE ucase(:cod) ";
            $parameters[':cod'] =  $filters['cod'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != "") {
            $filtersQuery .= " AND pdts.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['initial_price']) && $filters['initial_price'] != "") {
            $filtersQuery .= " AND pdts.value >= :initial_price";
            $parameters[':initial_price'] = Util::unmaskMoney($filters['initial_price']);
        }

        if (isset($filters['final_price']) && $filters['final_price'] != "") {
            $filtersQuery .= " AND pdts.value <= :final_price";
            $parameters[':final_price'] = Util::unmaskMoney($filters['final_price']);
        }

        if (isset($filters['initial_price2']) && $filters['initial_price2'] != "") {
            $filtersQuery .= " AND pdts.value >= :initial_price";
            $parameters[':initial_price'] = ($filters['initial_price2']);
        }

        if (isset($filters['final_price2']) && $filters['final_price2'] != "") {
            $filtersQuery .= " AND pdts.value <= :final_price";
            $parameters[':final_price'] = ($filters['final_price2']);
        }

        if (isset($filters['price'])) {

            if (isset($filters['price']->start_price)) {
                $filtersQuery .= " AND pdts.value >= :start_price";
                $parameters[':start_price'] = ($filters['price']->start_price);
            }

            if (isset($filters['price']->end_price)) {
                $filtersQuery .= " AND pdts.value <= :end_price";
                $parameters[':end_price'] = ($filters['price']->end_price);
            }
        }

        if (isset($filters['category']) && $filters['category'] != "") {
            $filtersQuery .= " AND pdts.id_property_category = :category";
            $parameters[':category'] = $filters['category'];
        }

        if (isset($filters['category_in']) && !empty($filters['category_in']) && $filters['category_in'] != "") {
            $filtersQuery .= " AND pdts.id_property_category IN (";
            foreach ($filters['category_in'] as $category) {
                $filtersQuery .= "$category, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['property_type']) && $filters['property_type'] != "") {
            $filtersQuery .= " AND pdts.id_residential_type = :property_type";
            $parameters[':property_type'] = $filters['property_type'];
        }

        if (isset($filters['property_type_in']) && !empty($filters['property_type_in']) && $filters['property_type_in'] != "") {
            $filtersQuery .= " AND pdts.id_residential_type IN (";
            foreach ($filters['property_type_in'] as $property_type) {
                $filtersQuery .= "$property_type, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['idsProducts']) && is_array($filters['idsProducts'])) {
            if (!empty($filters['idsProducts'])) {
                $filtersQuery .= " AND pdts.id IN (";

                foreach ($filters['idsProducts'] as $value) {
                    $filtersQuery .= "$value, ";
                }

                $filtersQuery = rtrim($filtersQuery, ", ");
                $filtersQuery .= ")";
            } else {
                return [];
            }
        }

        if (isset($filters['registration_start_date']) && $filters['registration_start_date'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at >= :registration_start_date";
            $parameters[':registration_start_date'] = $filters['registration_start_date'];
        }

        if (isset($filters['registration_end_date']) && $filters['registration_end_date'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at < :registration_end_date";
            $parameters[':registration_end_date'] = $filters['registration_end_date'];
        }

        if (isset($filters['date1']) && $filters['date1'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at >= :date1";
            $parameters[':date1'] = $filters['date1'] . " 00:00:00";
        }

        if (isset($filters['date2']) && $filters['date2'] != "") {
            $filtersQuery .= " AND pdts.re_registered_at <= :date2";
            $parameters[':date2'] = $filters['date2'] . " 23:59:59";
        }

        if (isset($filters['expire']) && $filters['expire'] != '' && $filters['expire'] == 0) {
            $expire_at = "<";
            $filtersQuery .= " AND pdts.re_registered_at $expire_at :atualData";
            $parameters[':atualData'] = date("Y-m-d");
        } else if (isset($filters['expire']) && $filters['expire'] != '' && $filters['expire'] == 1) {
            $expire_at = ">=";
            $filtersQuery .= " AND (pdts.re_registered_at $expire_at :atualData OR pdts.re_registered_at is null)";
            $parameters[':atualData'] = date("Y-m-d");
        }

        if (isset($filters['month']) && $filters['month'] != '') {
            $filtersQuery .= " AND month(pdts.re_registered_at) = :mes";
            $parameters[':mes'] = $filters['month'];
        }

        if (isset($filters['year']) && $filters['year'] != '') {
            $filtersQuery .= " AND year(pdts.re_registered_at) = :ano";
            $parameters[':ano'] = $filters['year'];
        }

        $sql = "SELECT
                    pdts.*,
                    COALESCE(pdts.cod, pdts.id) AS filter_cod,
                    br.name AS branch_name,
                    ct.uf, ct.name AS city_name,
                    sts.name AS state_name,
                    pc.name AS category_name,
                    pt.name as property_type_name,
                    (   SELECT
                            u.name
                        FROM users u
                        WHERE u.id = pdts.created_by
                    ) AS user_name,
                    (   SELECT
                            pdtsi.id
                        FROM products_imgs pdtsi
                        WHERE pdtsi.id_product = pdts.id
                        ORDER BY pdtsi.status_site DESC, pdtsi.item_order ASC
                        LIMIT 1
                    ) AS id_property_cover_image,
                    (   SELECT
                            pdtsi.extension
                        FROM products_imgs pdtsi
                        WHERE pdtsi.id_product = pdts.id
                        ORDER BY pdtsi.status_site DESC, pdtsi.item_order ASC
                        LIMIT 1
                    ) AS property_cover_image_extension
                FROM products pdts
                INNER JOIN cities ct ON ct.id = pdts.id_city
                INNER JOIN states sts ON sts.uf = pdts.uf_state
                INNER JOIN property_category pc ON pc.id = pdts.id_property_category
                INNER JOIN property_type pt ON pt.id = pdts.id_residential_type
                LEFT JOIN product_ownership_feature pof ON pdts.id = pof.id_product
                LEFT JOIN property_type_resources ptr ON ptr.id = pof.id_property_type_resources
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                LEFT JOIN branch br ON br.id = pb.id_branch
                WHERE TRUE $filtersQuery
                GROUP BY pdts.id";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function getAndFilterThePropertiesForAttendance($filters, $attendanceId, $rows, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        $parameters[':id_attendance'] = $attendanceId;

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_city_in']) && !empty($filters['id_city_in']) && $filters['id_city_in'] != "") {
            $filtersQuery .= " AND pdts.id_city IN (";
            foreach ($filters['id_city_in'] as $city) {
                $filtersQuery .= "$city, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND pb.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['price'])) {

            if (isset($filters['price']['start_price'])) {
                $filtersQuery .= " AND pdts.value >= :start_price";
                $parameters[':start_price'] = ($filters['price']['start_price']);
            }

            if (isset($filters['price']['end_price'])) {
                $filtersQuery .= " AND pdts.value <= :end_price";
                $parameters[':end_price'] = ($filters['price']['end_price']);
            }
        }

        if (isset($filters['category_in']) && !empty($filters['category_in']) && $filters['category_in'] != "") {
            $filtersQuery .= " AND pdts.id_property_category IN (";
            foreach ($filters['category_in'] as $category) {
                $filtersQuery .= "$category, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['property_type_in']) && !empty($filters['property_type_in']) && $filters['property_type_in'] != "") {
            $filtersQuery .= " AND pdts.id_residential_type IN (";
            foreach ($filters['property_type_in'] as $property_type) {
                $filtersQuery .= "$property_type, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['idsProducts']) && is_array($filters['idsProducts'])) {
            if (!empty($filters['idsProducts'])) {
                $filtersQuery .= " AND pdts.id IN (";

                foreach ($filters['idsProducts'] as $value) {
                    $filtersQuery .= "$value, ";
                }

                $filtersQuery = rtrim($filtersQuery, ", ");
                $filtersQuery .= ")";
            } else {
                return [];
            }
        }

        if (isset($filters["availability"])) {
            $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_status AND sales.status = 1 AND sales.id_product = pdts.id LIMIT 1) is null THEN true ELSE false END";
        }

        $sql = "SELECT
                    pdts.*,
                    ct.uf, ct.name AS city_name,
                    pc.name AS category_name,
                    pt.name as property_type_name
                FROM products pdts
                INNER JOIN cities ct ON ct.id = pdts.id_city
                INNER JOIN property_category pc ON pc.id = pdts.id_property_category
                INNER JOIN property_type pt ON pt.id = pdts.id_residential_type
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                LEFT JOIN branch br ON br.id = pb.id_branch
                WHERE TRUE $filtersQuery
                GROUP BY pdts.id
                ORDER BY ( SELECT dp.id
                            FROM displayed_properties dp
                            INNER JOIN attendance a ON a.id = dp.id_attendance
                            WHERE dp.id_product = pdts.id
                            AND a.id = :id_attendance ) ASC";

        if ($rows > 0) {
            $offset = ($page - 1) * $rows;
            $sql .= " LIMIT $rows OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function CountThePropertiesForAttendance($filters, $attendanceId)
    {
        $parameters = [];
        $filtersQuery = '';

        $parameters[':id_attendance'] = $attendanceId;

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_city_in']) && !empty($filters['id_city_in']) && $filters['id_city_in'] != "") {
            $filtersQuery .= " AND pdts.id_city IN (";
            foreach ($filters['id_city_in'] as $city) {
                $filtersQuery .= "$city, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND pb.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['price'])) {

            if (isset($filters['price']['start_price'])) {
                $filtersQuery .= " AND pdts.value >= :start_price";
                $parameters[':start_price'] = ($filters['price']['start_price']);
            }

            if (isset($filters['price']['end_price'])) {
                $filtersQuery .= " AND pdts.value <= :end_price";
                $parameters[':end_price'] = ($filters['price']['end_price']);
            }
        }

        if (isset($filters['category_in']) && !empty($filters['category_in']) && $filters['category_in'] != "") {
            $filtersQuery .= " AND pdts.id_property_category IN (";
            foreach ($filters['category_in'] as $category) {
                $filtersQuery .= "$category, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['property_type_in']) && !empty($filters['property_type_in']) && $filters['property_type_in'] != "") {
            $filtersQuery .= " AND pdts.id_residential_type IN (";
            foreach ($filters['property_type_in'] as $property_type) {
                $filtersQuery .= "$property_type, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['idsProducts']) && is_array($filters['idsProducts'])) {
            if (!empty($filters['idsProducts'])) {
                $filtersQuery .= " AND pdts.id IN (";

                foreach ($filters['idsProducts'] as $value) {
                    $filtersQuery .= "$value, ";
                }

                $filtersQuery = rtrim($filtersQuery, ", ");
                $filtersQuery .= ")";
            } else {
                return [];
            }
        }

        if (isset($filters["availability"])) {
            $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = pdts.id LIMIT 1) is null THEN true ELSE false END";
        }

        $sql = "SELECT
                    pdts.*,
                    ct.uf, ct.name AS city_name,
                    pc.name AS category_name,
                    pt.name as property_type_name
                FROM products pdts
                INNER JOIN cities ct ON ct.id = pdts.id_city
                INNER JOIN property_category pc ON pc.id = pdts.id_property_category
                INNER JOIN property_type pt ON pt.id = pdts.id_residential_type
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                LEFT JOIN branch br ON br.id = pb.id_branch
                WHERE TRUE $filtersQuery
                GROUP BY pdts.id
                ORDER BY ( SELECT dp.id
                            FROM displayed_properties dp
                            INNER JOIN attendance a ON a.id = dp.id_attendance
                            WHERE dp.id_product = pdts.id
                            AND a.id = :id_attendance ) ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function getCountAndFilterThePropertiesForAttendance($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_city_in']) && !empty($filters['id_city_in']) && $filters['id_city_in'] != "") {
            $filtersQuery .= " AND pdts.id_city IN (";
            foreach ($filters['id_city_in'] as $city) {
                $filtersQuery .= "$city, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND pb.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['price'])) {

            if (isset($filters['price']->start_price)) {
                $filtersQuery .= " AND pdts.value >= :start_price";
                $parameters[':start_price'] = ($filters['price']->start_price);
            }

            if (isset($filters['price']->end_price)) {
                $filtersQuery .= " AND pdts.value <= :end_price";
                $parameters[':end_price'] = ($filters['price']->end_price);
            }
        }

        if (isset($filters['category_in']) && !empty($filters['category_in']) && $filters['category_in'] != "") {
            $filtersQuery .= " AND pdts.id_property_category IN (";
            foreach ($filters['category_in'] as $category) {
                $filtersQuery .= "$category, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['property_type_in']) && !empty($filters['property_type_in']) && $filters['property_type_in'] != "") {
            $filtersQuery .= " AND pdts.id_residential_type IN (";
            foreach ($filters['property_type_in'] as $property_type) {
                $filtersQuery .= "$property_type, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['idsProducts']) && is_array($filters['idsProducts'])) {
            if (!empty($filters['idsProducts'])) {
                $filtersQuery .= " AND pdts.id IN (";

                foreach ($filters['idsProducts'] as $value) {
                    $filtersQuery .= "$value, ";
                }

                $filtersQuery = rtrim($filtersQuery, ", ");
                $filtersQuery .= ")";
            } else {
                return [];
            }
        }

        if (isset($filters["availability"])) {
            $filtersQuery .= " AND CASE WHEN (SELECT sales.id FROM sales WHERE sales.id_sale_stauts != 9 AND sales.status = 1 AND sales.id_product = pdts.id LIMIT 1) is null THEN true ELSE false END";
        }

        $sql = "SELECT
                    pdts.id
                FROM products pdts
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                LEFT JOIN branch ON branch.id = pb.id_branch
                WHERE TRUE $filtersQuery
                GROUP BY pdts.id";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function getCitiesForProperties($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['uf_state']) && $filters['uf_state'] != '') {
            $filtersQuery .= " AND pdts.uf_state = :uf_state";
            $parameters[':uf_state'] = $filters['uf_state'];
        }

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND pb.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != "") {
            $filtersQuery .= " AND pdts.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        $sql = "SELECT
                   ct.*
                FROM products pdts
                INNER JOIN cities ct ON ct.id = pdts.id_city
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                LEFT JOIN branch bra ON bra.id = pb.id_branch
                WHERE TRUE $filtersQuery
                GROUP BY ct.id
                ORDER BY ct.uf, ct.name ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getIdsAndCodsFromAllProducts()
    {
        $sql = "SELECT pdts.id, pdts.cod FROM products pdts";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getProductsById($id)
    {
        $sql = "SELECT
                    pdts.*, pb.id_branch AS branches,
                    sl.id AS sales_id,
                    ct.name AS city_name, ct.uf,
                    branch.name AS branch_name,
                    pt.name AS property_type_name,
                    pc.name AS property_category_name,
                    (
                        SELECT
                            pdtsi.id
                        FROM products_imgs pdtsi
                        WHERE pdtsi.id_product = pdts.id
                        ORDER BY pdtsi.status_site DESC, pdtsi.item_order ASC
                        LIMIT 1
                    ) AS id_property_cover_image,
                    (
                        SELECT
                            pdtsi.extension
                        FROM products_imgs pdtsi
                        WHERE pdtsi.id_product = pdts.id
                        ORDER BY pdtsi.status_site DESC, pdtsi.item_order ASC
                        LIMIT 1
                    ) AS property_cover_image_extension,
                    case
                        when (SELECT sales.id FROM sales WHERE sales.id_sale_status != 9 AND sales.status = 1 AND sales.id_product = pdts.id limit 1) is null then true else false
                    end as `availability`
                FROM products pdts
                INNER JOIN cities ct ON ct.id = pdts.id_city
                LEFT JOIN sales sl ON sl.id_product = pdts.id
                INNER JOIN states stts ON ct.uf = stts.uf
                INNER JOIN property_type pt ON pt.id = pdts.id_residential_type
                INNER JOIN property_category pc ON pc.id = pdts.id_property_category
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                LEFT JOIN branch ON branch.id = pb.id_branch
                WHERE pdts.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getProductByIdForContract($productId)
    {
        $sql = "SELECT
                    p.`number` AS numeroEndereco, p.name AS nome, p.total_area AS areaTotal, p.address AS endereco, p.neighborhood AS bairroEndereco, p.uf_state AS uf,
                    p.value as valor, p.cod AS codigo, p.complement AS complemento, p.cod,
                    pt.name AS tipoImovel,
                    pc.name AS categoria,
                    ct.name AS cidade,
                    stts.name AS estado
                FROM products p
                INNER JOIN cities ct ON ct.id = p.id_city
                INNER JOIN states stts ON ct.uf = stts.uf
                INNER JOIN property_type pt ON p.id_residential_type = pt.id
                INNER JOIN property_category pc ON p.id_property_category = pc.id
                WHERE p.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $productId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getProductsByBranch($id)
    {
        $sql = "SELECT
                    pdts.id, pdts.name
                FROM products pdts
                LEFT JOIN property_branch ON property_branch.id_property = pdts.id
                LEFT JOIN branch bra ON bra.id = property_branch.id_branch
                WHERE property_branch.id_branch = :id
                AND pdts.status = 1";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllProductOwnershipFeatureByIdProduct($productId)
    {
        $sql = "SELECT
                    pof.*,
                    ir.id as id_immovable_resource, ir.name as immovable_resource_name, ir.data_type as immovable_resource_data_type,
                    pt.id as id_property_type, pt.name as property_type_name
                FROM product_ownership_feature pof
                LEFT JOIN products p ON pof.id_product = p.id
                LEFT JOIN property_type_resources ptr ON pof.id_property_type_resources = ptr.id
                INNER JOIN immovable_resource ir ON ptr.id_immovable_resource = ir.id
                INNER JOIN property_type pt ON ptr.id_property_type = pt.id
                WHERE pof.id_product = :id
                AND ir.status = 1
                AND pt.status = 1
                ORDER BY ir.name ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $productId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function  getAndFilterProductOwnershipFeature($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['id_product']) && $filters['id_product'] != "") {
            $filtersQuery .= " AND pof.id_product = :id_product";
            $parameters[':id_product'] = $filters['id_product'];
        }

        if (isset($filters['product_status']) && $filters['product_status'] != "") {
            $filtersQuery .= " AND p.status = :product_status";
            $parameters[':product_status'] = $filters['product_status'];
        }

        if (isset($filters['immovable_resource_status']) && $filters['immovable_resource_status'] != "") {
            $filtersQuery .= " AND ir.status = :immovable_resource_status";
            $parameters[':immovable_resource_status'] = $filters['immovable_resource_status'];
        }

        if (isset($filters['property_type_status']) && $filters['property_type_status'] != "") {
            $filtersQuery .= " AND pt.status = :property_type_status";
            $parameters[':property_type_status'] = $filters['property_type_status'];
        }

        if (isset($filters['valued']) && $filters['valued'] == 1) {
            $filtersQuery .= " AND (pof.value IS NOT NULL AND pof.value != '')";
        }

        $sql = "SELECT
                    pof.*,
                    p.name as product_name,
                    ir.name as immovable_resource_name,
                    pt.name as property_type_name
                FROM product_ownership_feature pof
                LEFT JOIN products p ON pof.id_product = p.id
                LEFT JOIN property_type_resources ptr ON pof.id_property_type_resources = ptr.id
                INNER JOIN immovable_resource ir ON ptr.id_immovable_resource = ir.id
                INNER JOIN property_type pt ON ptr.id_property_type = pt.id
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && !empty($filters['order']) ? $filters['order'] : " pof.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }
    /**descontinuar */
    public function getAllProductWithFeatureValue($idImmovable, $value)
    {
        $sql = "SELECT
                    pdts.id
                FROM products pdts
                LEFT JOIN product_ownership_feature pof ON pof.id_product = pdts.id
                LEFT JOIN property_type_resources ptr ON ptr.id = pof.id_property_type_resources
                WHERE ptr.id_immovable_resource = :immovable
                AND pof.value RLIKE :value
                ORDER BY pdts.id";

        $query = $this->db->prepare($sql);
        $parameters = array(':immovable' => $idImmovable, ':value' => "[[:<:]]{$value}[[:>:]]");
        $query->execute($parameters);

        return $query->fetchAll();
    }
    /**descontinuar */
    public function getAllProductWithFeatureValue2($immovableId, $value)
    {
        $sql = "SELECT
                    pdts.id
                FROM products pdts
                LEFT JOIN product_ownership_feature pof ON pof.id_product = pdts.id
                LEFT JOIN property_type_resources ptr ON ptr.id = pof.id_property_type_resources
                WHERE ptr.id_immovable_resource = :immovable
                AND pof.value >= :value
                ORDER BY pdts.id";

        $query = $this->db->prepare($sql);
        $parameters = array(':immovable' => $immovableId, ':value' => $value);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllPropertiesByResourceValue(int $resourceId, $value, array $filters = [], $comparison = 0, $options)
    {
        $resource = (new ImmovableResource())->getItemById8161($resourceId);

        if ($comparison == 2 || $comparison === 'GREATER_EQUAL') {
            $queryComparison = '>=';
        } else if ($comparison == 1 || $comparison === 'LESSEAR_EQUAL') {
            $queryComparison = '<=';
        } else {
            $queryComparison = '=';
        }

        $parameters = [];
        $filtersQuery = '';

        if ($options['filter'] != false && $options['filter'] != "false") {
            $filtersQuery .= " AND pof.id_product IN (" . implode(',', $options['properties']) . ")";
        }

        if (isset($filters['id_branch']) && !empty($filters['id_branch'])) {
            $filtersQuery .= " AND pb.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $filtersQuery .= " AND pdts.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        $filtersQuery .= " AND ptr.id_immovable_resource = :resourceId";
        $parameters[':resourceId'] = $resourceId;

        $filtersQuery .= " AND pof.value ";

        if (is_array($value)) {
            $filtersQuery .= "IN (" . implode(',', $value) . ")";
        } else if ($resource->data_type == 1 || $resource->data_type == 4) {
            $filtersQuery .= "LIKE '%$value%'";
        } else {
            $filtersQuery .= "{$queryComparison} $value";
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    pdts.id
                FROM product_ownership_feature pof
                INNER JOIN products pdts ON pdts.id = pof.id_product
                LEFT JOIN property_type_resources ptr ON ptr.id = pof.id_property_type_resources
                LEFT JOIN property_branch pb ON pb.id_property = pdts.id
                WHERE TRUE {$filtersQuery}
                GROUP BY pdts.id";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return array_map(function ($element) {
            return $element->id;
        }, $query->fetchAll());
    }

    /**descontinuar */
    public function getProductWithFeatureValue($productId, $immovableId, $value)
    {
        $sql = "SELECT
                    pdts.id
                FROM products pdts
                LEFT JOIN product_ownership_feature pof ON pof.id_product = pdts.id
                LEFT JOIN property_type_resources ptr ON ptr.id = pof.id_property_type_resources
                WHERE pdts.id = :productId
                AND ptr.id_immovable_resource = :immovable
                AND pof.value >= :value";

        $query = $this->db->prepare($sql);
        $parameters = array(
            ':productId' => $productId,
            ':immovable' => $immovableId,
            ':value' => $value
        );
        $query->execute($parameters);

        return $query->fetch();
    }

    public function checkResourceOnProperty($propertyId, $resourceId, $value, $comparison = 0)
    {
        $resource = (new ImmovableResource())->getItemById8161($resourceId);

        if ($comparison == 2 || $comparison === 'GREATER_EQUAL') {
            $queryComparison = '>=';
        } else if ($comparison == 1 || $comparison === 'LESSEAR_EQUAL') {
            $queryComparison = '<=';
        } else {
            $queryComparison = '=';
        }

        $filtersQuery = '';

        if (is_array($value)) {
            $filtersQuery .= "IN (" . implode(',', $value) . ")";
        } else if ($resource->data_type == 1 || $resource->data_type == 4) {
            $filtersQuery .= "LIKE '%$value%'";
        } else {
            $filtersQuery .= "{$queryComparison} $value";
        }

        $sql = "SELECT
                    pof.*
                FROM product_ownership_feature pof
                INNER JOIN property_type_resources ptr on ptr.id = pof.id_property_type_resources
                WHERE TRUE
                AND ptr.id_immovable_resource = :resourceId
                AND pof.value $filtersQuery
                AND pof.id_product = :propertyId";

        $query = $this->db->prepare($sql);
        $parameters = array(':propertyId' => $propertyId, ':resourceId' => $resourceId);
        $query->execute($parameters);

        return ($query->fetch() == true ? true : false);
    }

    public function getProductsImages($id)
    {
        $sql = "SELECT
                    *
                FROM products_imgs
                WHERE id_product = :id_product
                ORDER BY item_order ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getProductsImagesActiveSite($id)
    {
        $sql = "SELECT
                    *
                FROM products_imgs
                WHERE id_product = :id_product
                AND status_site = 1
                ORDER BY item_order ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    /**descontinuar */
    public function getFirstProductImage($productId)
    {
        $sql = "SELECT
                    *
                FROM products_imgs
                WHERE id_product = :id_product
                ORDER BY status_site DESC, item_order ASC
                LIMIT 1";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $productId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getProductsImagesProgress($id)
    {
        $sql = "SELECT
                    *
                FROM products_imgs_progress
                WHERE id_product = :id_product
                ORDER BY item_order ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getProductsVideos($id)
    {
        $sql = "SELECT
                    *
                FROM products_videos
                WHERE id_product = :id_product
                ORDER BY item_order ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getProductsAttachments($id)
    {
        $sql = "SELECT
                    *
                FROM products_attachments
                WHERE id_product = :id_product
                ORDER BY item_order ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getProductsPriceList($id)
    {
        $sql = "SELECT
                    *
                FROM products_priceList
                WHERE id_product = :id_product
                ORDER BY item_order ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getLogByProductId($id)
    {
        $sql = "SELECT
                    trap.created_at, trap.last_re_registered_at, trap.new_re_registered_at, trap.id_authorization_contract,
                    u.name AS user,
                    pac.code
                FROM timeline_re_registered_at_products trap
                INNER JOIN users u ON u.id = trap.created_by
                LEFT JOIN property_authorization_contract pac ON pac.id = trap.id_authorization_contract
                WHERE trap.id_product = :id_product
                ORDER BY trap.created_at DESC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getLastOrder($id, $table)
    {
        $sql = "SELECT
                    item_order
                FROM {$table}
                WHERE id_product = :id_product
                ORDER BY item_order DESC";

        $query = $this->db->prepare($sql);
        $parameters = array(":id_product" => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAttendanceCountWithProperties(array $filters = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['id_product']) && $filters['id_product'] != "") {
            $filtersQuery .= " AND dpp.id_product = :id_product";
            $parameters[':id_product'] = $filters['id_product'];
        }

        if (isset($filters['start_date']) && $filters['start_date'] != "") {
            $filtersQuery .= " AND ( dpp.created_at >= :start_date_created ) ";
            $parameters[':start_date_created'] = date('Y-m-d', strtotime($filters['start_date']));
        }

        if (isset($filters['end_date']) && $filters['end_date'] != "") {
            $filtersQuery .= " AND ( dpp.created_at <= :end_date_created) ";
            $parameters[':end_date_created'] = date('Y-m-d', strtotime("{$filters['end_date']} +1 month -1 day"));
        }

        $sql = "SELECT
                    atd.id
                FROM displayed_properties dpp
                INNER JOIN attendance atd ON atd.id = dpp.id_attendance
                WHERE TRUE $filtersQuery
                AND atd.status = 1 AND atd.id_status != 10 AND atd.id_status != 11
                GROUP BY atd.id";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function getCountAndFilterViewsSite($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['id_product']) && $filters['id_product'] != "") {
            $filtersQuery .= " AND psv.id_product = :id_product";
            $parameters[':id_product'] = $filters['id_product'];
        }

        if (isset($filters['start_date']) && $filters['start_date'] != "") {
            $filtersQuery .= " AND ( psv.created_at >= :start_date_created ) ";
            $parameters[':start_date_created'] = date('Y-m-d', strtotime($filters['start_date']));
        }

        if (isset($filters['end_date']) && $filters['end_date'] != "") {
            $filtersQuery .= " AND ( psv.created_at <= :end_date_created) ";
            $parameters[':end_date_created'] = date('Y-m-d', strtotime("{$filters['end_date']} +1 month -1 day"));
        }

        $sql = "SELECT
                    *
                FROM products_site_view psv
                WHERE TRUE $filtersQuery";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function getNeighborhoodsByCityId(int $cityId)
    {
        $filtersQuery = '';
        $parameters = [];

        $sql = "SELECT
                     DISTINCT ({$this->table}.neighborhood)
                FROM {$this->table}
                INNER JOIN cities ON cities.id = {$this->table}.id_city
                WHERE {$this->table}.status = 1
                AND {$this->table}.id_city = $cityId
                AND {$this->table}.id_branch = {$_SESSION['YP']->branch->current->id}
                ORDER BY {$this->table}.neighborhood ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllotmentsByCityId(int $cityId)
    {
        $parameters = [];

        $sql = "SELECT
                     DISTINCT ({$this->table}.allotment)
                FROM {$this->table}
                INNER JOIN cities ON cities.id = {$this->table}.id_city
                WHERE {$this->table}.status = 1
                AND {$this->table}.allotment IS NOT NULL
                AND {$this->table}.id_city = $cityId
                AND {$this->table}.id_branch = {$_SESSION['YP']->branch->current->id}
                ORDER BY {$this->table}.allotment ASC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function handleFormAdd(array $post)
    {
        $codCount = $this->getWithFiltersAllItems([
            (object)[
                'columns' => ['cod' => (object)['comparison' => 'EQUAL', 'value' => $post['cod']],]
            ]
        ])->count;
        $type = (new PropertyType())->getItemById($post['id_residential_type']);
        $type->prefix ?: '';

        $cod = $this->recursiveCheckCode(!empty($post['cod']) ? trim($type->prefix . $post['cod']) : '');

        $arrayPost = array(
            'id_owner' => $post['id_owner'],
            'id_property_category' => $post['id_property_category'],
            'id_property_classification' => $post['id_property_classification'],
            'cod' => ($codCount == 0 ? $cod : ''),
            'name' => trim($post['name']),
            'id_residential_type' => $post['id_residential_type'],
            'id_city' => $post['id_city'],
            'uf_state' => $post['uf_state'],
            'neighborhood' => $post['neighborhood'],
            'allotment' => $post['allotment'],
            'address' => $post['address'],
            'number' => $post['number'],
            'total_area' => $post['total_area'],
            'complement' => $post['complement'],
            "lat" => !empty($post["lat"]) ? $post["lat"] : "-27.244972",
            "lng" => !empty($post["lng"]) ? $post["lng"] : "-48.640851",
            'value' => Util::unMaskMoney($post['value']),
            'installment_value' => Util::unMaskMoney($post['installment_value']),
            'condition_product' => $post['condition_product'],
            're_registered_at' => !empty(trim($post['re_registered_at'])) ? $post['re_registered_at'] : NULL,
            "site_name" => trim($post["name"]),
            "site_complement" => Util::slugify($post["complement"]),
            "site_value" => Util::unMaskMoney($post["value"]),
            "site_description" => $post['description'],
            'slugify' => Util::slugify($post['name']),
            'description' => $post['description'],
            'created_at' => $post['created_at'],
            'created_by' => $_SESSION['YP']->user->id,
            'id_user_owner' => $_SESSION['YP']->user->id,
            'id_branch' => $_SESSION['YP']->branch->current->id,
        );

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrayPost);

            if (empty($arrayPost['cod'])) {
                $cod = $this->recursiveCheckCode($type->prefix . $response->lastId);
                $this->update(['cod' => $cod], "id", $response->lastId);
                $arrayPost['cod'] = $cod;
            }

            $this->update(
                ['url' => Util::slugify(trim($arrayPost['name']) . "-" . (!empty($arrayPost['cod']) ? trim($arrayPost['cod']) : $cod))],
                "id",
                $response->lastId
            );

            switch ((!isset($post['id_branch'])) || $post['id_branch']) {
                case ($post['id_branch'] && count((new Branch)->getAllBranch()) != count($post['id_branch']) && $post['id_branch'] >= 2):
                    foreach ($post['id_branch'] as $branchItem) {
                        (new PropertyBranches())->insert(['id_property' => $response->lastId, 'id_branch' => $branchItem]);
                    }
                    break;

                case (empty($post['id_branch'])):
                    (new PropertyBranches())->delete(['id_property' => $response->lastId]);
                    break;
            }
            (new PropertyBranches())->insert(['id_property' => $response->lastId, 'id_branch' => $_SESSION['YP']->branch->current->id]);

            if (isset($post['propertyTypeResources'])) {
                foreach ($post['propertyTypeResources'] as $property_resources_id) {
                    (new PropertyOwnershipFeature)->insert([
                        'id_product' => $response->lastId,
                        'id_property_type_resources' => $property_resources_id,
                        'created_by' => $_SESSION['YP']->user->id
                    ]);
                }
            }

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ops! Erro ao editar esse Item. Consulte os administradores'];
        }
    }

    private function recursiveCheckCode($cod)
    {
        $item = $this->getWithFiltersAllItems([(object)['columns' => ['cod' => (object)['comparison' => 'EQUAL', 'value' => $cod]]]]);

        if ($item->count > 0) {
            $codeNumbers = preg_replace('/[^0-9]/', '', $cod) + 1;
            $codeLetters = preg_replace('/[^A-Z]/', '', $cod);
            $$cod = (is_numeric($cod)) ? $cod + 1 : "{$codeLetters}{$codeNumbers}";
            return $this->recursiveCheckCode($$cod);
        }
        return $cod;
    }

    public function handleFormEdit($itemId, array $insertArray, array $postArray)
    {
        $product = $this->getItemById($itemId, [
            (object)[
                'columns' => ['id', 'id_branch', 'site_name', 'name']
            ]
        ]);
        $type = (new PropertyType())->getItemById($insertArray['id_residential_type']);
        $type->prefix ?: '';

        try {
            $this->db->beginTransaction();

            if (empty($insertArray['cod'])) {
                $cod = $this->recursiveCheckCode($type->prefix . $itemId);
                $this->update(['cod' => $cod], "id", $itemId);
                $insertArray['cod'] = $cod;
                $insertArray['url'] = Util::slugify((!empty($product->site_name) ? trim($product->site_name) : trim($product->name)) . "-" . $cod);
            }

            $response = $this->update($insertArray, "id", $itemId);

            (new PropertyBranches)->delete(['id_property' => $itemId]);
            if (!empty($postArray['id_branch']) && count($postArray['id_branch']) > 1) {

                    foreach ($postArray['id_branch'] as $branchItem) {
                        (new PropertyBranches())->insert(['id_property' => $itemId, 'id_branch' => $branchItem]);
                    }
            } else {

                (new PropertyBranches())->insert(['id_property' => $itemId, 'id_branch' => $_SESSION['YP']->branch->current->id]);
            }

            $productOwnershipFeature = $this->getAllProductOwnershipFeatureByIdProduct($itemId);

            if (isset($postArray['propertyTypeResources'])) {
                $items = Util::compareArray($productOwnershipFeature, 'id_property_type_resources', $postArray['propertyTypeResources']);

                if (!empty($items['add'])) {
                    foreach ($items['add'] as $add) {
                        $insertArray = array('id_product' => $itemId, 'id_property_type_resources' => $add, 'created_by' => $_SESSION['YP']->user->id);
                        (new PropertyOwnershipFeature)->insert($insertArray);
                    }
                }

                if (!empty($items['exc'])) {
                    foreach ($items['exc'] as $exc) {
                        (new ModelGenerico)->deleteItemByCampoGenerico("product_ownership_feature", "id", $exc->id);
                    }
                }
            } else {
                (new ModelGenerico)->deleteItemByCampoGenerico("product_ownership_feature", "id_product", $itemId);
            }

            $this->db->commit();
            return (object)["error" => !$response, "message" => $response ? "Item editado com sucesso" : "Erro ao editar esse item"];
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ops! Erro ao editar esse Item. Consulte os administradores'];
        }
    }
}
