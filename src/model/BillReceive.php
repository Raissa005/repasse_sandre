<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class BillReceive extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'bill_receive';
        $joins = [
            (object)[
                'table' => 'customer',
                'join' => 'inner',
                'where' => "{$this->table}.id_customer = customer.id"
            ],
            (object)[
                'table' => 'bill_receive_installment',
                'join' => 'left',
                'where' => "{$this->table}.id = bill_receive_installment.id_bill_receive"
            ],
            (object)[
                'table' => 'form_of_payment',
                'join' => 'inner',
                'where' => "{$this->table}.id_form_of_payment = form_of_payment.id"
            ],
            (object)[
                'table' => 'branch',
                'join' => 'inner',
                'where' => "{$this->table}.id_branch = branch.id"
            ],
            (object)[
                'table' => 'cost_center',
                'join' => 'inner',
                'where' => "{$this->table}.id_cost_center = cost_center.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function inactivateAndCancelAllInstallments(int $itemId)
    {
        $bill_receive_installments = (new BillReceiveInstallment)->getWithFiltersAllItems(
            [(object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $itemId]]]]
        )->data;

        try {
            $this->db->beginTransaction();

            foreach ($bill_receive_installments as $bill) {
                (new BillReceiveInstallment)->update(['status_payment' => 3], 'id', $bill->id);
            }

            $this->db->commit();
            \RR\libs\Toast::successToast('Parcelas canceladas com sucesso');
        } catch (\PDOException $error) {
            $this->db->rollBack();
            \RR\libs\Toast::genericError();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
        }
    }

    public function getAndFiltersAllItems($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        foreach ($filters as $column => $value) {
            if ($value != '') {
                $table = $this->table;

                if (in_array($column, ['status', 'id_branch', 'id_customer', 'id_form_of_payment', 'id_cost_center', 'id'])) {
                    $filtersQuery .= " AND {$table}.{$column} = :{$table}_{$column}";
                } else if (in_array($column, [])) {
                    $filtersQuery .= " AND {$table}.{$column} IN (" . (implode(", ", $value)) . ")";
                } else if (in_array($column, [])) {
                    $filtersQuery .= " AND ucase({$table}.{$column}) LIKE ucase(:{$table}_{$column})";
                    $value = "%" . $value . "%";
                } else if (in_array($column, ['status_not_in'])) {
                    if ($column == 'status_not_in') {
                        $column = 'status_payment';
                    }
                    $filtersQuery .= " AND {$table}.{$column} NOT IN (" . (implode(", ", $value)) . ")";
                }

                if (in_array($column, ['status', 'id_branch', 'id_customer', 'id_form_of_payment', 'id_cost_center', 'id'])) {
                    $parameters[":{$table}_{$column}"] = $value;
                }
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT 
                    {$this->table}.*,                    
                    cost_center.name AS cost_center_name,
                    form_of_payment.name AS form_of_payment_name,
                    customer.name AS customer_name
                FROM {$this->table}              
                INNER JOIN customer ON {$this->table}.id_customer = customer.id 
                INNER JOIN cost_center ON {$this->table}.id_cost_center = cost_center.id 
                INNER JOIN form_of_payment ON {$this->table}.id_form_of_payment = form_of_payment.id 
                WHERE TRUE $filtersQuery";

        $sql .= isset($options['groupBy']) ? " GROUP BY {$options['groupBy']}" : " GROUP BY {$this->table}.id";
        $sql .= isset($options['orderBy']) ? " ORDER BY {$options['orderBy']}" : " ORDER BY {$this->table}.id DESC";

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

    public function getAmountOfInstallmentsOfRelease($billReceiveId)
    {
        $sql = "SELECT
                    (bill_receive_installment.id)
                FROM bill_receive_installment
                WHERE TRUE 
                AND bill_receive_installment.id_bill_receive = :id
                AND bill_receive_installment.status = 1
                AND bill_receive_installment.status_payment != 3
                AND bill_receive_installment.status_payment != 9 ";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $billReceiveId);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function getEntryById($id)
    {
        $sql = "SELECT
                    {$this->table}.*,
                    ( SELECT
                        SUM(value_of_installments)
                      FROM bill_receive_installment
                      WHERE bill_receive_installment.id_bill_receive = {$this->table}.id
                      AND bill_receive_installment.status_payment != 3
                      AND bill_receive_installment.status_payment != 9
                      AND bill_receive_installment.status = 1
                    ) AS total_amount,
                    ( SELECT
                        SUM(value_of_installments)
                      FROM bill_receive_installment
                      WHERE bill_receive_installment.id_bill_receive = {$this->table}.id
                      AND bill_receive_installment.status = 1
                      AND bill_receive_installment.status_payment = 2
                    ) AS amount_paid
                FROM {$this->table}                
                WHERE {$this->table}.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getItemById8161(int $itemId)
    {
        $sql = "SELECT {$this->table}.*                    
                FROM {$this->table}                
                WHERE {$this->table}.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = [':id' => $itemId];
        $query->execute($parameters);

        return $query->fetch();
    }

    public function handleFormAdd(array $post)
    {
        $arrayPost = [
            'id_cost_center' => $post['id_cost_center'],
            'id_customer' => $post['id_customer'],
            'id_form_of_payment' => $post['id_form_of_payment'],
            'description' => $post['description'],
            'id_branch' => $_SESSION['RR']->branch->current->id,
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrayPost);

            if (!$response->error) {
                if (isset($post['number_of_installments']) && $post['value_reference'] == 1) {
                    $valueOfInstallments = Util::unmaskMoney($post['price']) / $post['number_of_installments'];

                    list($year, $month, $day) = explode("-", $post['due_date']);

                    for ($p = 1; $p <= $post['number_of_installments']; $p++) {
                        $arrayPostInstament = [
                            'number_portion' => $p,
                            'id_bill_receive' => $response->lastId,
                            'id_form_of_payment' => $post['id_form_of_payment'],
                            'due_date' => !empty(trim($post['due_date'])) ? $post['due_date'] : NULL,
                            'value_installment' => $valueOfInstallments,
                            'status_payment' => !empty($post['status_payment']) ? $post['status_payment'] : 1,
                            'description' => $post['description'],
                            'created_by' => $_SESSION['RR']->user->id,
                        ];

                        if (!empty($post['status_payment']) && $post['status_payment'] == 2) {
                            $arrayPostInstament['pay_day'] = $post['due_date'];
                            $arrayPostInstament['amount_paid'] = $valueOfInstallments;
                            $arrayPostInstament['updated_at'] = date("Y-m-d H:i:s");
                            $arrayPostInstament['updated_by'] = $_SESSION['RR']->user->id;
                        }

                        $month++;
                        if ((int) $month == 13) {
                            $year++;
                            $month = 1;
                        }

                        $remove = $day;
                        while (!checkdate($month, $remove, $year)) {
                            $remove--;
                        }

                        $post['due_date'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                        (new BillReceiveInstallment())->insert($arrayPostInstament);
                    }
                } else {
                    $valueOfInstallments = Util::unmaskMoney($post['price']);

                    list($year, $month, $day) = explode("-", $post['due_date']);

                    for ($p = 1; $p <= $post['number_of_installments']; $p++) {
                        $arrayPostInstament = [
                            'number_portion' => $p,
                            'id_bill_receive' => $response->lastId,
                            'id_form_of_payment' => $post['id_form_of_payment'],
                            'due_date' => !empty(trim($post['due_date'])) ? $post['due_date'] : NULL,
                            'value_installment' => $valueOfInstallments,
                            'status_payment' => !empty($post['status_payment']) ? $post['status_payment'] : 1,
                            'description' => $post['description'],
                            'created_by' => $_SESSION['RR']->user->id,
                        ];

                        if (!empty($post['status_payment']) && $post['status_payment'] == 2) {
                            $arrayPostInstament['pay_day'] = $post['due_date'];
                            $arrayPostInstament['amount_paid'] = $valueOfInstallments;
                            $arrayPostInstament['updated_at'] = date("Y-m-d H:i:s");
                            $arrayPostInstament['updated_by'] = $_SESSION['RR']->user->id;
                        }

                        $month++;
                        if ((int) $month == 13) {
                            $year++;
                            $month = 1;
                        }

                        $remove = $day;
                        while (!checkdate($month, $remove, $year)) {
                            $remove--;
                        }

                        $post['due_date'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                        (new BillReceiveInstallment())->insert($arrayPostInstament);
                    }
                }
            }

            \RR\libs\Toast::checkResponse($response->error, $response->message);

            $this->db->commit();

            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
            \RR\libs\Toast::genericError();
            return (object)['error' => true, 'message' => 'Erro ao salvar o lançamento.'];
        }
    }

    public function handleFormEdit(int $id, array $post)
    {
        $arrayPost = [
            'id_cost_center' => $post['id_cost_center'],
            'id_customer' => $post['id_customer'],
            'id_form_of_payment' => $post['id_form_of_payment'],
            'description' => $post['description'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrayPost, 'id', $id);

            if (!$response->error) {
                $installments = (new BillReceiveInstallment)->getWithFiltersAllItems([
                    (object)['columns' => [
                        'id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $id],
                        'status_payment' => (object)['comparison' => 'NOT_IN', 'value' => [2, 3]]
                    ]]
                ]);

                if (!empty($installments->data)) {
                    $arrPost = [
                        'id_form_of_payment' => $post['id_form_of_payment'],
                        'updated_at' => date("Y-m-d H:i:s"),
                        'updated_by' => $_SESSION['RR']->user->id
                    ];

                    foreach ($installments->data as $installment) {
                        (new BillReceiveInstallment)->update($arrPost, 'id', $installment->id);
                    }
                }
            }

            if ($post['status'] != true) {
                foreach ((new BillReceiveInstallment)->getWithFiltersAllItems([
                    (object)['columns' => ['id_bill_receive' => (object)['comparison' => 'EQUAL', 'value' => $id]]]
                ])->data as $instament) {
                    (new BillReceiveInstallment)->update(
                        [
                            "updated_at" => date("Y-m-d H:i:s"),
                            "updated_by" => $_SESSION['RR']->user->id
                        ],
                        'id',
                        $instament->id
                    );
                }
            }

            \RR\libs\Toast::checkResponse($response->error, $response->message);

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ocorreu um erro ao atualizar o registro'];
        }
    }
}
