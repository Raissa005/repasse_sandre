<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

class Sales extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'sales';
        $joins = [
            (object)[
                'table' => 'bill_receive',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_bill_receive",
            ],
            (object)[
                'table' => 'users',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.created_by",
            ],
            (object)[
                'table' => 'products',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_product",
            ],
            (object)[
                'table' => 'status',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_sale_status",
            ],
            (object)[
                'table' => 'customer',
                'join' => 'inner',
                'where' => "this->table.id = {$this->table}.id_customer",
            ],
            (object)[
                'table' => 'construction_properties',
                'join' => 'left',
                'where' => "this->table.id_property = {$this->table}.id_product",
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function submitAddForm($post)
    {
        $user = (new User)->getItemById($_SESSION['RR']->user->id);
        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);

        $arrPost = array(
            'id_branch' => $_SESSION['RR']->branch->current->id,
            'id_customer' => $post['id_customer'],
            'id_attendance' => $post['id_attendance'] ?? null,
            'id_product' => $post['id_product'],
            'sale_date' => $post['sale_date'] ?? NULL,
            'text' => $post['text'],
            'sale_value' => Util::unMaskMoney($post['sale_value']),
            'created_by' => $_SESSION['RR']->user->id,
            'due_date' => $post['due_date'] ?? $post['sale_date'],
            'id_sale_status' => $post['id_sale_status'] ?? 1,
            'seller_name' => $post['seller_name'] ?? $user->name,
            'seller_payment_date' => Date::add_working_days($post['due_date'], $branch->days_after_seller) ?? Date::add_working_days($post['sale_date'], $branch->days_after_seller),
            'currency_id' => $post['currency'] ?? 1,
            'currency_value' => $post['currency'] != 1 ? $post['currency_value'] : null,
            'currency_value' => $post['currency'] != 1 ? Util::unmaskMoney($post['currency_value']) : null
        );

        switch ($branch->type) {
            case '1':
                $arrPost['percentage_commission'] = $post['percentage_commission'];
                $arrPost['percentage_commission_real_rate'] = $branch->percentage_commission_real_rate;
                $arrPost['percentage_commission_seller'] = $branch->percentage_commission_seller;
                $arrPost['commission_value'] = Util::unmaskMoney($post['commission_value']);
                if (Secure::access_admin()) {
                    $arrPost['sales_manager'] = $post['sales_manager'];
                    $arrPost['number_installments'] = $post['number_installments'];
                    $arrPost['id_form_payment'] = $post['id_form_payment'];
                } else {
                    $arrPost['number_installments'] = 1;
                }
                break;

            case '2':
                if (isset($arrPost['id_standard_contract']) && !empty($arrPost['id_standard_contract'])) {
                    $arrPost['id_standard_contract'] = $post['id_standard_contract'];
                }

                $arrPost['percentage_commission'] = $branch->percentage_commission_sale;
                $arrPost['percentage_commission_real_rate'] = $branch->percentage_commission_real_rate;
                $arrPost['percentage_commission_seller'] = $branch->percentage_commission_seller;
                $arrPost['id_form_payment'] = $branch->id_form_payment_seller;
                $arrPost['number_installments'] = $post['number_installments'];
                $arrPost['sales_manager'] = $post['sales_manager'];
                $arrPost['fgts_select'] = $post['fgts_select'];
                $arrPost['fgts'] = isset($post['fgts']) ? Util::unMaskMoney($post['fgts']) : 0;
                $arrPost['expense_tax'] = isset($post['expense_tax']) ? Util::unMaskMoney($post['expense_tax']) : 0;
                $arrPost['expense_operational_value'] = isset($post['expense_operational_value']) ? Util::unMaskMoney($post['expense_operational_value']) : 0;
                $arrPost['after_sale_retained'] = isset($post['after_sale_retained']) ? Util::unMaskMoney($post['after_sale_retained']) : 0;
                $arrPost['expense_management_commission'] = isset($post['expense_management_commission']) ? Util::unMaskMoney($post['expense_management_commission']) : 0;
                $arrPost['expense_commission'] = isset($post['expense_commission']) ? Util::unMaskMoney($post['expense_commission']) : 0;

                if ($branch->type == 2) {
                    $saleContracts = array('id_standard_proposal' => $_POST['id_standard_proposal']);
                }

                if ($branch->type == 1) {
                    $saleContracts = array('id_standard_contract' => $_POST['id_standard_contract']);
                }

                break;
        }

        try {
            $this->db->beginTransaction();
            $response = $this->insert($arrPost);
            (new Property())->update(['site_status' => '0'], 'id', $post['id_product']);

            $item = $this->getItemById($response->lastId);

            if ($branch->type == '2') {
                Self::submitContracts($response->lastId, $saleContracts, 'insert');
            }

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao adicionar! Consulte os administradores.'];
            exit;
        }
    }

    public function submitFormStatus($itemId, $post)
    {
        try {
            $this->db->beginTransaction();

            $response = $this->update(['id_sale_status' => $post['id_sale_status']], 'id', $itemId);

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => $this->message_admins];
        }
    }

    public function submitFormEdit($itemId, $post)
    {
        $productModel = new Property();

        $item = $this->getItemById($itemId);
        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
        Secure::redirectFunction(!Secure::access_secretary() && (!$item->created_by || $item->blocked == 1), "sales/editItem/" . $itemId, "authorization=false");

        (new ModelGenerico)->deleteItemByCampoGenerico('summary_sale', 'sale_id', $itemId);
        (new ModelGenerico)->deleteItemByCampoGenerico('summary_involved', 'sale_id', $itemId);

        $arrPost = array(
            'id_customer' => $post['id_customer'],
            'id_product' => $post['id_product'],
            'id_attendance' => $post['id_attendance'] ?? null,
            'sale_date' => !empty(trim($post['sale_date'])) ? $post['sale_date'] : NULL,
            'text' => $post['text'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
            'sale_value' => Util::unmaskMoney($post['sale_value']),
            'number_installments' => $post['number_installments'],
            'seller_payment_date' => Date::add_working_days($post['due_date'], $branch->days_after_seller) ?? Date::add_working_days($post['sale_date'], $branch->days_after_seller),
            'currency_id' => $post['currency'] ?? 1,
            'currency_value' => $post['currency'] != 1 ? Util::unmaskMoney($post['currency_value']) : null
        );

        switch ($branch->type) {
            case '1':
                $arrPost['percentage_commission'] = $post['percentage_commission'];
                $arrPost['due_date'] = $post['due_date'];
                $arrPost['commission_value'] = Util::unmaskMoney($post['commission_value']);
                if (Secure::access_admin()) {
                    $arrPost["blocked"] = $post['blocked'];
                    $arrPost["number_installments"] = $post['number_installments'];
                    $arrPost["id_form_payment"] = $post['id_form_payment'];
                    $arrPost["id_sale_status"] = $post['id_sale_status'];
                    $arrPost['sales_manager'] = $post['sales_manager'];
                    $arrPost['created_by'] = $post['created_by'];
                }
                break;

            case '2':
                $arrPost['sales_manager'] = $post['sales_manager'];

                if (isset($arrPost['id_standard_contract']) && !empty($arrPost['id_standard_contract'])) {
                    $arrPost['id_standard_contract'] = $post['id_standard_contract'];
                }

                $arrPost['id_standard_proposal'] = $post['id_standard_proposal'];
                $arrPost['fgts_select'] = $post['fgts_select'];
                $arrPost["id_sale_status"] = $post['id_sale_status'];
                $arrPost["seller_name"] = $post['seller_name'];
                $arrPost['fgts'] = isset($post['fgts']) ? Util::unMaskMoney($post['fgts']) : 0;
                $arrPost['expense_tax'] = isset($post['expense_tax']) ? Util::unMaskMoney($post['expense_tax']) : 0;
                $arrPost['expense_operational_value'] = isset($post['expense_operational_value']) ? Util::unMaskMoney($post['expense_operational_value']) : 0;
                $arrPost['after_sale_retained'] = isset($post['after_sale_retained']) ? Util::unMaskMoney($post['after_sale_retained']) : 0;
                $arrPost['expense_management_commission'] = isset($post['expense_management_commission']) ? Util::unMaskMoney($post['expense_management_commission']) : 0;
                $arrPost['expense_commission'] = isset($post['expense_commission']) ? Util::unMaskMoney($post['expense_commission']) : 0;
                $arrPost["blocked"] = $post['blocked'];

                if ($branch->type == 2) {
                    $saleContracts = array('id_standard_proposal' => $_POST['id_standard_proposal']);
                }

                if ($branch->type == 1) {
                    $saleContracts = array('id_standard_contract' => $_POST['id_standard_contract']);
                }

                if ((new SalesChargePaymentAgreement())->getWithFiltersAllItems([(object)['columns' => ['id_sale' => (object)['comparison' => 'EQUAL', 'value' => $item->id]]]])->count > 0) {
                }

                break;
        }

        try {
            $this->db->beginTransaction();
            $response = $this->update($arrPost, 'id', $itemId);

            $item = $this->getItemById($itemId);
            $productModel->update(['site_status' => '0'], 'id', $item->id_product);

            if ($branch->type == '2') {
                Self::submitContracts($itemId, $saleContracts, 'update');
            }

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == 'development') {
                echo $error->getMessage();
                exit;
            }
            return ['error' => true, 'message' => 'Erro ao atualizar contrato', 'itemId' => $itemId];
        }
    }

    public function getSalesForCustomers(int $customerId): object
    {
        $filters = [
            (object)[
                'columns' => [
                    'id_customer' => (object)['value' => $customerId],
                    'id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id],
                ]
            ]
        ];

        if (Secure::is_seller()) {
            array_push($filters, (object)['columns' => ['created_by' => (object)['value' => $_SESSION['RR']->user->id]]]);
        }

        $columns = [
            (object)['columns' => ["*"]],
            (object)[
                'table' => 'products',
                'columns' => ['id', 'cod', 'name']
            ],
            (object)[
                'table' => 'status',
                'columns' => ['id', 'name']
            ],
        ];

        $options = ['orderBy' => 'sales.sale_date DESC'];

        return $this->getWithFiltersAllItems($filters, $columns, $options);
    }

    public function getAndFilterAllItem($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';
        $name_option = '';
        'AND ( cust.created_by = :id_seller_manager OR mst.id_manager = :id_seller_manager) ';
        $columnOr = (object)[
            /** $filter->table1.$filter->column1 = :parameter OR $filter->table2.$filter->column2 :parameter */
            'id_seller_manager' => (object)[
                'column1' => 'created_by',
                'table1' => 'customer',
                'column2' => 'id_manager',
                'table2' => 'manager_team',
            ],
        ];

        foreach ($filters as $column => $value) {
            if ($value != '') {
                $table = $this->table;

                if (in_array($column, ['status', 'id_branch', 'created_by', 'id_sale_status', 'sales_manager'])) {
                    $filtersQuery .= " AND {$table}.{$column} = :{$table}_{$column}";
                } else if (in_array($column, [])) {
                    $filtersQuery .= " AND {$table}.{$column} IN (" . (implode(", ", $value)) . ")";
                } else if (in_array($column, ['name'])) {
                    if ($column == 'name') {
                        $table = 'customer';
                        $name_option = "OR ucase(customer.fancy_name_company LIKE :{$table}_{$column})";
                    }
                    $filtersQuery .= " AND ucase({$table}.{$column}) LIKE ucase(:{$table}_{$column}) $name_option";
                    $value = "%" . $value . "%";
                } else if (in_array($column, ['id_seller_manager'])) {
                    $filtersQuery .= " AND {$columnOr->{$column}->table1}.{$columnOr->{$column}->column1} = :{$table}_{$column} OR {$columnOr->{$column}->table2}.{$columnOr->{$column}->column2} = :{$table}_{$column}";
                }

                if (in_array($column, ['status', 'id_sale_status', 'id_branch', 'created_by', 'name', 'sales_manager', 'id_seller_manager'])) {
                    $parameters[":{$table}_{$column}"] = $value;
                }
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.*,
                    branch.name AS branch_name,
                    customer.fancy_name_company, customer.name, COALESCE(customer.fancy_name_company, customer.name) AS customer_name_identifier,
                    `status`.name AS status_name,
                    products.id as product_id, products.name AS product_name, products.cod as product_cod, COALESCE(products.cod, products.id) as product_identifier
                FROM {$this->table}
                INNER JOIN branch ON branch.id = {$this->table}.id_branch
                INNER JOIN customer ON customer.id = {$this->table}.id_customer
                INNER JOIN products ON products.id = {$this->table}.id_product
                INNER JOIN `status` ON `status`.id = {$this->table}.id_sale_status
                LEFT JOIN manager_team ON manager_team.id_seller = {$this->table}.created_by
                WHERE TRUE $filtersQuery";

        if (isset($options['groupBy'])) {
            $sql .= " GROUP BY {$options['groupBy']}";
        } else {
            $sql .= " GROUP BY {$this->table}.id";
        }

        if (isset($options['orderBy'])) {
            $sql .= " ORDER BY {$options['orderBy']}";
        } else {
            $sql .= " ORDER BY {$this->table}.id ASC";
        }

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
    public function getAndFilterAllSales($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND sls.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND sls.id_branch = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['id_sale_status']) && $filters['id_sale_status'] != '') {
            $filtersQuery .= " AND sls.id_sale_status = :id_sale_status";
            $parameters[':id_sale_status'] = $filters['id_sale_status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(cust.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['created_by']) && $filters['created_by'] != '') {
            $filtersQuery .= " AND sls.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        $sql = "SELECT
                    sls.*,
                    br.name AS branch_name,
                    cust.name AS name_customer, cust.fancy_name_company,
                    u.name as user_name,
                    stts.name AS name_status,
                    p.name AS name_product, p.id AS id_product, p.cod as cod_product,
                    (
                        SELECT u.name
                        FROM users u
                        WHERE u.id = sls.created_by
                    ) AS user_name,
                    (
                        SELECT
                            SUM(ps.value)
                        FROM payments_of_sales ps
                        WHERE ps.id_sale = sls.id
                        AND ps.status_portion = 1
                    ) AS total_amount_paid,
                    (
                        SELECT
                            SUM(ps.value)
                        FROM payments_of_sales ps
                        WHERE ps.id_sale = sls.id
                    ) AS total_amount_received
                FROM sales sls
                LEFT JOIN branch br ON br.id = sls.id_branch
                LEFT JOIN customer cust ON cust.id = sls.id_customer
                LEFT JOIN products p ON sls.id_product = p.id
                LEFT JOIN users u ON sls.created_by = u.id
                LEFT JOIN `status` stts ON stts.id = sls.id_sale_status
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " sls.id DESC";

        if ($rows > 0) {
            $offset = ($page - 1) * $rows;
            $sql .= " LIMIT $rows OFFSET $offset ";
        }


        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemById8161($itemId)
    {
        $sql = "SELECT
                    {$this->table}.*,
                    customer.name AS customer_name,
                    products.name AS property_name
                FROM {$this->table}
                INNER JOIN customer ON {$this->table}.id_customer = customer.id
                INNER JOIN products ON {$this->table}.id_product = products.id
                WHERE {$this->table}.id = :itemId";

        $query = $this->db->prepare($sql);
        $parameters = array(':itemId' => $itemId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAllCommissionInstallments($billReceiveId)
    {
        $sql = "SELECT
                    bill_receive_installment.*,
                    form_of_payment.name as form_of_payment
                FROM bill_receive_installment
                INNER JOIN bill_receive ON bill_receive_installment.id_bill_receive = bill_receive.id
                INNER JOIN form_of_payment ON form_of_payment.id = bill_receive_installment.id_form_of_payment
                INNER JOIN sales ON sales.id_bill_receive = bill_receive.id
                WHERE bill_receive.id = :billReceiveId
                AND bill_receive_installment.status = 1";

        $query = $this->db->prepare($sql);
        $parameters = array(':billReceiveId' => $billReceiveId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getCommissionInstallmentsById($billReceiveInstallmentId)
    {
        $sql = "SELECT
                    bill_receive_installment.*,
                    form_of_payment.name as form_of_payment,
                    sales.id as id_sale
                FROM bill_receive_installment
                INNER JOIN bill_receive ON bill_receive_installment.id_bill_receive = bill_receive.id
                INNER JOIN sales ON sales.id = bill_receive.id_link AND bill_receive.id_origin = 1
                INNER JOIN form_of_payment ON form_of_payment.id = bill_receive_installment.id_form_of_payment
                WHERE bill_receive_installment.id = :billReceiveInstallmentId
                AND bill_receive_installment.status = 1";

        $query = $this->db->prepare($sql);
        $parameters = array(':billReceiveInstallmentId' => $billReceiveInstallmentId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAllSales()
    {
        $sql = "SELECT
                    sls.*
                FROM sales sls
                WHERE sls.status = 1
                ORDER BY sls.name ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getAllPaymentsOfSales()
    {
        $sql = "SELECT
                    pos.*
                FROM payments_of_sales pos
                ORDER BY pos.id_portion ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getSalesByIdForContract($id)
    {
        $sql = "SELECT
                    sls.id, sls.status, sls.id_branch, sls.updated_by, sls.updated_at, sls.created_by, sls.created_at, sls.sale_date, sls.id_customer, sls.id_product,
                    sls.sale_value AS valor, sls.id_sale_status, sls.sales_manager, sls.id_standard_contract AS id_contract, sls.id_standard_proposal AS id_proposal,
                    DAY(sls.sale_date) AS `dia`, sls.sale_date AS `mes`, YEAR(sls.sale_date) AS `ano`,
                    us.name AS nomeGerente,
                    us.cpf AS cpfGerente,
                    us.creci AS creciGerente
                FROM sales sls
                LEFT JOIN users us ON us.id = sls.sales_manager
                WHERE sls.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getPaymentsIdSaleForContract($idSales)
    {
        $sql = "SELECT
                    pos.portion_number, pos.id_form_of_payment, pos.value, pos.status_portion,
                    fp.name AS form_payment
                FROM sales s
                LEFT JOIN payments_of_sales pos ON pos.id_sale = s.id
                LEFT JOIN form_of_payment fp ON pos.id_form_of_payment = fp.id
                WHERE s.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $idSales);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAmountPortionsBySale($id)
    {
        $sql = "SELECT
                   count(pos.id) AS amount
                FROM payments_of_sales pos
                LEFT JOIN form_of_payment fop ON fop.id = pos.id_form_of_payment
                LEFT JOIN sales sls ON sls.id = pos.id_sale
                LEFT JOIN customer cust ON cust.id = sls.id_customer
                WHERE TRUE
                AND pos.status = 1
                AND pos.id_sale = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getTotalValuePortionsBySale($id)
    {
        $sql = "SELECT
                   SUM(pos.value) AS totalValue
                FROM payments_of_sales pos
                WHERE pos.id_sale = :id
                AND pos.status = :status";

        $query = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
            ':status' => true
        ];
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getPortionById($id)
    {
        $sql = "SELECT
                    pos.id, pos.status_portion, pos.updated_by, pos.updated_at, pos.created_by, pos.created_at, pos.due_date, pos.id_sale, pos.id_form_of_payment,
                    pos.value, pos.portion_number, pos.observation, pos.id_bank_finance, pos.id_account, pos.number_paymentOrder, pos.own_paymentOrder, pos.owner_paymentOrder,
                    pos.cpfcnpj_paymentOrder, pos.bank_paymentOrder, pos.agency_paymentOrder, pos.number_account_paymentOrder, pos.id_bill_receive_installment,
                    pos.amount_paid, pos.pay_day, pos.type_of_payment, pos.id_property, pos.vehicle, pos.license_plate,
                    cust.name AS customer_name, cust.person_registration as cpf, cust.fancy_name_company as company_name, cust.cnpj,
                    fop.name AS form_of_payment_name
                FROM payments_of_sales pos
                LEFT JOIN form_of_payment fop ON fop.id = pos.id_form_of_payment
                LEFT JOIN sales sls ON sls.id = pos.id_sale
                LEFT JOIN customer cust ON cust.id = sls.id_customer
                WHERE pos.id = :id ";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getLastNumberPortionByBillsToReceiveId()
    {
        $sql = "SELECT
                    bill_receive_installments.number_portion
                FROM bill_receive_installments
                INNER JOIN bill_receive ON bill_receive.id = bill_receive_installments.id_bill_receive
                WHERE bill_receive_installments.`status` = 1
                ORDER BY bill_receive_installments.number_portion DESC
                LIMIT 1 ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetch()->number_portion;
    }

    public function deletePaymentById($id)
    {
        $sql = "DELETE FROM payments_of_sales WHERE id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return true;
    }

    public function deleteAllPaymentSaleBySale($id)
    {
        $sql = "DELETE FROM payments_of_sales WHERE id_sale = :id";

        $query = $this->db->prepare($sql);
        $parameters[':id'] = $id;
        $query->execute($parameters);

        return true;
    }

    public function handleFormPaymentArrangement(int $bill_receive_installment_id, array $post)
    {
        $arrayPost = [
            'origin_commission_seller' => $post['origin_commission_seller'],
            'percentage_commission_seller' => $post['percentage_commission_seller'],
        ];

        try {
            $this->db->beginTransaction();

            $response = (new BillReceiveInstallment)->update($arrayPost, 'id', $bill_receive_installment_id);

            if (!$response->error) {
                if (isset($post['positions'])) {
                    foreach ($post['positions'] as $position) {
                        (new ArrangementPaymentChargesInvoiceReceiveInstallment)->update([
                            'origin_commission' => $position['origin_commission'],
                            'percentage_commission' => $position['percentage_commission'],
                        ], 'id', $position['id']);
                    }
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
            return (object)['error' => true, 'message' => $this->message_admins];
        }
    }

    public function handleFormPaymentAgreement(int $id, array $post)
    {
        $arrayPost = [
            'origin_commission_seller' => $post['origin_commission_seller'],
            'percentage_commission_seller' => $post['percentage_commission_seller'],
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrayPost, 'id', $id);
            if (!$response->error) {
                (new ModelGenerico)->deleteItemByCampoGenerico('summary_involved', 'sale_id', $id);

                if (isset($post['positions'])) {
                    foreach ($post['positions'] as $position) {
                        $arrayPostAgreement = [
                            'origin_commission' => $position['origin_commission'],
                            'percentage_commission' => $position['percentage_commission'],
                        ];

                        if (isset($position['id_customer']) && !empty($position['id_customer'])) {
                            $arrayPostAgreement['id_customer'] = $position['id_customer'];
                        }

                        (new SalesChargePaymentAgreement)->update($arrayPostAgreement, 'id', $position['id']);
                    }
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
            return (object)['error' => true, 'message' => $this->message_admins];
        }
    }

    public function handleFormGenerateInstallments(int $id)
    {
        $summarySale = (new SummarySale)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $id]]]]);
        $summaryInvolved = (new SummaryInvolved)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $id]]]]);

        try {
            $this->db->beginTransaction();

            $item = $this->getItemById($id);

            if (empty($item->id_bill_receive)) {
                $branch_payment_arrangement = (new Branch)->getItemById($_SESSION['RR']->branch->current->id, [
                    (object)[
                        'columns' => [
                            'id_cost_center_receive_commission',
                            'id_form_payment_seller',
                            'percentage_commission_sale',
                            'percentage_commission_virtual_rate',
                            'percentage_commission_real_rate',
                            'percentage_commission_seller',
                            'origin_commission_seller',
                        ]
                    ]
                ]);

                $item->id_bill_receive = (new BillReceive)->insert([
                    'id_sale' => $id,
                    'id_customer' => $item->id_customer,
                    'id_cost_center' => $branch_payment_arrangement->id_cost_center_receive_commission,
                    'id_form_of_payment' => $branch_payment_arrangement->id_form_payment_seller,
                    'id_branch' => $_SESSION['RR']->branch->current->id,
                    'created_by' => $_SESSION['RR']->user->id,
                    'description' => "Código da venda: {$id}",
                ])->lastId;

                (new Sales)->update(['id_bill_receive' => $item->id_bill_receive], 'id', $item->id);
            }

            for ($i = 0; $i < $summarySale->count; $i++) {
                $response = (new BillReceiveInstallment)->insert([
                    'id_bill_receive' =>  $item->id_bill_receive,
                    'id_form_of_payment' => $item->id_form_payment,
                    'number_portion' => $summarySale->data[$i]->installment_number,
                    'due_date' => $summarySale->data[$i]->received_date,
                    'value_installment' => $summarySale->data[$i]->amount,
                    'percentage_commission_seller' => $item->percentage_commission_seller,
                    'origin_commission_seller' => $item->origin_commission_seller,
                    'id_customer_seller' => $item->created_by,
                    'description' => 'Parcela ' . $summarySale->data[$i]->installment_number . ' de ' . $item->number_installments . "\nVenda de código " . $item->id,
                    'created_by' => $_SESSION['RR']->user->id
                ]);

                if (!$response->error) {
                    foreach ($summaryInvolved->data as $involved) {
                        if ($summarySale->data[$i]->installment_number == $involved->installment_number) {
                            $customerId = [];
                            $userPositionId = [];

                            if (!in_array($involved->customer_id, $customerId) || !in_array($involved->user_position_id, $userPositionId)) {
                                $position = (new SalesChargePaymentAgreement)->getItemWithFilters([
                                    (object)[
                                        'columns' => [
                                            'id_customer' => (object)['comparison' => 'EQUAL', 'value' => $involved->customer_id]
                                        ],
                                        'id_user_position' => (object)['comparison' => 'EQUAL', 'value' => $involved->user_position_id]
                                    ]
                                ]);

                                (new ArrangementPaymentChargesInvoiceReceiveInstallment)->insert([
                                    'id_bill_receive_installment' => $response->lastId,
                                    'id_customer' => $involved->customer_id,
                                    'id_user_position' => $involved->user_position_id,
                                    'origin_commission' => $position->origin_commission,
                                    'percentage_commission' => $position->percentage_commission,
                                    'id_cost_center' => $position->id_cost_center,
                                    'id_form_payment' => $position->id_form_payment,
                                    'created_by' => $_SESSION['RR']->user->id,
                                ]);
                            }
                        }
                    }
                } else {
                    break;
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
            return (object)['error' => true, 'message' => $this->message_admins];
        }
    }

    private function submitContracts(int $itemId, array $contracts, string $type)
    {
        $item = $this->getItemById($itemId);

        /**Contracts */
        $saleContract = $this->getSalesByIdForContract($item->id);
        $customerContract = (new Customer)->getCustomerByIdForContract($item->id_customer);
        $spouseContract = (new Customer)->getSpouseByIdCustomerForContract($item->id_customer);
        $productContract = (new Property)->getProductByIdForContract($item->id_product);
        $branchContract = (new Branch)->getBranchIdSaleForContract($item->id);
        $userContract = (new User)->getUserById($item->created_by);
        $resourcesContract = (new Property)->getAllProductOwnershipFeatureByIdProduct($item->id_product);

        $contracts = $type === 'update' ? (new Contract)->getAllContractByIdSale($item->id) : $contracts;
        foreach ($contracts as $contract) {

            switch ($type) {
                case 'insert':
                    $standardContract = (new StandardContract)->getStandardContractById($contract);
                    $contract_variables = (new StandardContractVariables)->getWithFiltersAllItems([(object)['columns' => ['id_standard_contract' => (object)['value' => $standardContract->id]]]])->data;

                    $arrPost = array(
                        'id_sale' => $item->id,
                        'id_standard_contract' => $standardContract->id,
                        'contract_text' => $standardContract->text,
                        'created_by' => $item->created_by,
                    );

                    $idContract = (new Contract)->insert($arrPost)->lastId;
                    break;

                case 'update':
                    if ($contract->id_type_standard == 2) {
                        /**Proposta de Negociação */
                        $contract_variables = (new StandardContractVariables)->getWithFiltersAllItems([(object)['columns' => ['id_standard_contract' => (object)['value' => $_POST['id_standard_proposal']]]]])->data;
                        $standardContract = (new StandardContract)->getItemById($_POST['id_standard_proposal']);

                        (new Contract)->update([
                            'id_standard_contract' => $_POST['id_standard_proposal'],
                            'contract_text' => $standardContract->text
                        ], 'id', $contract->id);
                    } else {
                        /**Contrato */
                        (new ContractsVariables)->getWithFiltersAllItems([(object)['columns' => ['id_contract' => (object)['value' => $contract->id]]]])->data;
                    }
                    break;
            }
        }
    }

    public function insertBillReceiveForPayments(object $item): object
    {
        $branch_config = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
        $array_insert = [
            'id_branch' => $item->id_branch,
            'id_cost_center' => $branch_config->id_cost_center_receive_commission,
            'id_customer' => $item->id_customer,
            'id_form_of_payment' => $branch_config->id_form_payment_single,
            'description' => 'Contas a receber referente a venda de código: ' . $item->id,
            'status' => 1,
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $insert = (new BillReceive)->insert($array_insert);
            $update = $this->update(['id_bill_receive' => $insert->lastId], 'id', $item->id);
            if ($insert->error || $update->error) throw new PDOException();

            $this->db->commit();
            return $insert;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao inserir contas a receber', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }
}
