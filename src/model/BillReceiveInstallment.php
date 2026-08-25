<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class BillReceiveInstallment extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'bill_receive_installment';
        $joins = [
            (object)[
                'table' => "bill_receive",
                'join' => 'inner',
                'where' => "{$this->table}.id_bill_receive = bill_receive.id"
            ],
            (object)[
                'table' => 'customer',
                'join' => 'left',
                'where' => "{$this->table}.id_purchase_broker = this->table.id",
                'require' => 'bill_receive'
            ],
            (object)[
                'table' => 'form_of_payment',
                'join' => 'left',
                'where' => "{$this->table}.id_form_of_payment = form_of_payment.id"
            ],
            (object)[
                'table' => 'cost_center',
                'join' => 'inner',
                'where' => "bill_receive.id_cost_center = cost_center.id",
                'require' => 'bill_receive'
            ],
            (object)[
                'table' => 'payment_status',
                'join' => 'inner',
                'where' => "{$this->table}.status_payment = payment_status.id",
            ],
            (object)[
                'table' => 'branch',
                'join' => 'inner',
                'where' => "bill_receive.id_branch = this->table.id",
                'require' => 'bill_receive'
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function getAndFiltersAllItems($filters = [], $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (!empty($filters)) {
            $table = 'bill_receive';
            $column = 'id_branch';
            $value = $_SESSION['RR']->branch->current->id;
            $filtersQuery .= " AND {$table}.{$column} = {$value}";

            foreach ($filters as $column => $value) {
                if ($value != '') {
                    $table = $this->table;

                    if (in_array($column, ['id_bill_receive', 'id'])) {
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
                    } else if (in_array($column, ['date'])) {
                        $column = 'due_date';
                        $dateStatrt = date('Y-m-d', strtotime("{$value['start']}"));
                        $dateEnd = date('Y-m-d', strtotime("{$value['end']}"));

                        if (!empty($value['start']) && !empty($value['end'])) {

                            $filtersQuery .= " AND {$table}.{$column} BETWEEN '{$dateStatrt}' AND '{$dateEnd}'";
                        } else if (!empty($value['start'])) {

                            $filtersQuery .= " AND {$table}.{$column} >= '{$dateStatrt}'";
                        } else if (!empty($value['end'])) {

                            $filtersQuery .= " AND {$table}.{$column} <= '{$dateEnd}'";
                        }
                    }

                    if (in_array($column, ['id_bill_receive', 'id'])) {
                        $parameters[":{$table}_{$column}"] = $value;
                    }
                }
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.*,
                    cost_center.name AS cost_center_name,
                    form_of_payment.name AS form_of_payment_name,
                    bill_receive.id_customer
                FROM {$this->table}
                INNER JOIN bill_receive ON {$this->table}.id_bill_receive = bill_receive.id
                INNER JOIN cost_center ON bill_receive.id_cost_center = cost_center.id
                INNER JOIN form_of_payment ON {$this->table}.id_form_of_payment = form_of_payment.id
                WHERE TRUE $filtersQuery";

        $sql .= isset($options['groupBy']) ? " GROUP BY {$options['groupBy']}" : " GROUP BY {$this->table}.id";
        $sql .= isset($options['orderBy']) && !empty($options['orderBy']) ? " ORDER BY {$options['orderBy']}" : " ORDER BY {$this->table}.id ASC";

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

    public function getAllInstallmentByBillReceiveId($billReceiveId)
    {
        $sql = "SELECT
                    bill_receive_installment.*,
                    form_of_payment.name AS form_of_payment_name
                FROM bill_receive_installment
                INNER JOIN form_of_payment ON form_of_payment.id = bill_receive_installment.id_form_of_payment
                WHERE TRUE
                AND bill_receive_installment.id_bill_receive = :bill_receive_id
                AND bill_receive_installment.status = 1
                ORDER BY bill_receive_installment.status_payment, bill_receive_installment.due_date ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(':bill_receive_id' => $billReceiveId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getLastNumberPortionByBillsReceiveId($billReceiveId)
    {
        $sql = "SELECT
                    bill_receive_installment.number_portion
                FROM bill_receive_installment
                WHERE bill_receive_installment.id_bill_receive = :id
                ORDER BY bill_receive_installment.id DESC
                LIMIT 1 ";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $billReceiveId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getItemById8161(int $itemId)
    {
        $sql = "SELECT
                    {$this->table}.*,
                    bill_receive.id_branch, bill_receive.id_form_of_payment as id_form_of_payment_entry, bill_receive.status as status_entry,
                    form_of_payment.name AS form_of_payment_name,
                    cost_center.name AS cost_center_name
                FROM {$this->table}
                INNER JOIN bill_receive ON bill_receive.id = {$this->table}.id_bill_receive
                INNER JOIN form_of_payment ON form_of_payment.id = {$this->table}.id_form_of_payment
                INNER JOIN cost_center ON cost_center.id = bill_receive.id_cost_center
                WHERE {$this->table}.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $itemId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAttachmentsByItemId($id)
    {
        $sql = "SELECT
                    bill_receive_installment_attachment.*
                FROM bill_receive_installment_attachment
                WHERE bill_receive_installment_attachment.id_bill_receive_installment = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function submitAddPortion(int $billReceiveId): object
    {
        $lastPortion = $this->getLastNumberPortionByBillsReceiveId($billReceiveId);

        $arrPost = [
            'number_portion' => ++$lastPortion->number_portion,
            'id_bill_receive' => $billReceiveId,
            'id_form_of_payment' => $_POST['id_form_of_payment'],
            'due_date' => !empty(trim($_POST['due_date'])) ? $_POST['due_date'] : NULL,
            'value_installment' => Util::unmaskMoney($_POST['value_installment']),
            'status_payment' => !empty($_POST['status_payment']) ? $_POST['status_payment'] : 1,
            'description' => $_POST['description'],
            'created_by' => $_SESSION['RR']->user->id,
        ];

        if (!empty($_POST['status_payment'])) {
            $arrPost['pay_day'] = $_POST['due_date'];
            $arrPost['amount_paid'] = Util::unmaskMoney($_POST['value_installment']);
            $arrPost['updated_at'] = date("Y-m-d H:i:s");
            $arrPost['updated_by'] = $_SESSION['RR']->user->id;
        }

        try {
            $this->db->beginTransaction();
            $response = $this->insert($arrPost);
            if ($response->error) throw new PDOException($response->message);

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao inserir parcela.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }

    public function submitEditPortion(int $installmentId): object
    {
        $item = $this->getItemById($installmentId);

        $arrayPost = [
            'value_installment' => isset($_POST['value_installment']) ? Util::unmaskMoney($_POST['value_installment']) : $item->value_installment,
            'due_date' => isset($_POST['due_date']) ? $_POST['due_date'] : $item->due_date,
            'description' => $_POST['description'],
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrayPost, 'id', $installmentId);
            if ($response->error) throw new PDOException($response->message);

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao editar parcela.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }

    public function submitCancelInstallment(int $installmentId): object
    {
        $arrayPost = array(
            'status_payment' => 3,
            'pay_day' => '',
            'amount_paid' => '',
            'payment_transaction' => 0,
            'id_account' => null,
            'number_check' => null,
            'id_bank' => null,
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrayPost, 'id', $installmentId);
            if ($response->error) throw new PDOException($response->message);

            foreach ($this->getWithFiltersAllItems([(object)['columns' => [
                'id' => (object)['comparison' => '=', 'value' => $installmentId],
                'status_payment' => (object)['comparison' => '!=', 'value' => 3]
            ]]])->data as $key => $value) {
                if ($value->status_payment != 2) {
                    $this->update($arrayPost, 'id', $value->id);
                } else if (isset($_POST['payment-transaction']) && !empty($_POST['payment-transaction']) && $_POST['payment-transaction'] == 1) {
                    $this->update(['payment_transaction' => 3, 'value_of_installments' => 0], 'id', $value->id);
                }
            }

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao cancelar parcela.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }

    public function submitActivateInstallment(int $installmentId): object
    {
        $arrayPost = array(
            'status_payment' => 1,
            'pay_day' => '',
            'amount_paid' => '',
            'payment_transaction' => 0,
            'id_account' => null,
            'number_check' => null,
            'id_bank' => null,
            'updated_at' => date("Y-m-d H:i:s"),
            'updated_by' => $_SESSION['RR']->user->id,
        );

        try {
            $this->db->beginTransaction();
            $response = $this->update($arrayPost, 'id', $installmentId);
            if ($response->error) throw new PDOException($response->message);

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao ativar parcela.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }
}
