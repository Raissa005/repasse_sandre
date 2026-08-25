<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\RecursiveCostCenter;
use RR\libs\Util;

class BillsToPay extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'bills_to_pay';
        $joins = [
            (object)[
                'table' => 'bills_to_pay_installments',
                'join' => 'inner',
                'where' => "{$this->table}.id = this->table.id_bills_to_pay"
            ],
            (object)[
                'table' => 'customer',
                'join' => 'inner',
                'where' => "{$this->table}.id_customer = this->table.id"
            ],
            (object)[
                'table' => 'form_of_payment',
                'join' => 'inner',
                'where' => "{$this->table}.id_form_of_payment = this->table.id"
            ],
            (object)[
                'table' => 'cost_center',
                'join' => 'inner',
                'where' => "{$this->table}.id_cost_center = this->table.id"
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function cancelAndUpdateInstallments(array $arrIds)
    {
        $response = [];
        foreach ($arrIds as $id) {
            $item = (new BillsToPayInstallment)->getItemById($id);

            $arrPost = [
                'status_payment' => $item->status_payment == 3 ? 1 : 3,
                'pay_day' => '',
                'amount_paid' => '',
                'payment_transaction' => 0,
                'id_account' => null,
                'number_check' => null,
                'id_bank' => null,
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_by' => $_SESSION['RR']->user->id,
            ];

            try {
                $this->db->beginTransaction();
                $response[] = (new BillsToPayInstallment)->update($arrPost, 'id', $id);
                $this->db->commit();
            } catch (\PDOException $error) {
                $this->db->rollBack();
                if (ENVIRONMENT == "develop") {
                    echo $error->getMessage();
                    exit;
                }
            }
        }

        $response = array_filter($response, function ($item) {
            return $item->error == true;
        });

        return (object)[
            'error' => $response != false ? false : true,
            'message' => $response != false ? 'Erro ao cancelar pagamento' : 'Pagamento cancelado com sucesso'
        ];
    }

    public function getAndFilterAllItem($filters, $options = [])
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
                } else if (in_array($column, ['date'])) {
                    if (isset($value['start']) && !empty($value['start'])) {
                        $filtersQuery .= " AND ( {$this->table}.competence >= :start_date_competence ) ";
                        $parameters[':start_date_competence'] = date('Y-m-d', strtotime($value['start']));
                    }

                    if (isset($value['end']) && !empty($value['end'])) {
                        $filtersQuery .= " AND ( {$this->table}.competence <= :end_date_competence) ";
                        $parameters[':end_date_competence'] = date('Y-m-d', strtotime("{$value['end']} +1 month -1 day"));
                    }
                }

                if (in_array($column, ['status', 'id_branch', 'id_customer', 'id_form_of_payment', 'id_cost_center', 'id'])) {
                    $parameters[":{$table}_{$column}"] = $value;
                }
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.*,
                    customer.name AS customer_name,
                    customer.fancy_name_company AS customer_fancy_name,
                    cost_center.name AS cost_center_name,
                    form_of_payment.name AS form_of_payment_name
                FROM {$this->table}
                INNER JOIN customer ON {$this->table}.id_customer = customer.id
                INNER JOIN cost_center ON {$this->table}.id_cost_center = cost_center.id
                INNER JOIN form_of_payment ON {$this->table}.id_form_of_payment = form_of_payment.id
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

    public function handleFormAdd(array $post)
    {
        $arrPost = array(
            'id_customer' => $post['id_customer'],
            'id_branch' => $_SESSION['RR']->branch->current->id,
            'competence' => ($post['competence'] . "-01"),
            'id_form_of_payment' => $post['id_form_of_payment'],
            'id_cost_center' => $post['id_cost_center'],
            'description' => $post['description'],
            'created_by' => $_SESSION['RR']->user->id,
        );

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrPost);

            if (!$response->error) {
                if (isset($post['number_of_installments']) && $post['value_reference'] == 1) {
                    $valueOfInstallments = Util::unmaskMoney($post['price']) / $post['number_of_installments'];

                    list($year, $month, $day) = explode("-", $post['due_date']);

                    for ($p = 1; $p <= $post['number_of_installments']; $p++) {
                        $arrPostPortion = [
                            'number_portion' => $p,
                            'id_bills_to_pay' => $response->lastId,
                            'id_form_of_payment' => $post['id_form_of_payment'],
                            'due_date' => !empty(trim($post['due_date'])) ? $post['due_date'] : NULL,
                            'value_of_installments' => $valueOfInstallments,
                            'status_payment' => !empty($post['status_payment']) ? $post['status_payment'] : 1,
                            'description' => $post['description'],
                            'created_by' => $_SESSION['RR']->user->id,
                        ];

                        if (!empty($post['status_payment']) && $post['status_payment'] == 2) {
                            $arrPostPortion['pay_day'] = $post['due_date'];
                            $arrPostPortion['amount_paid'] = $valueOfInstallments;
                            $arrPostPortion['payment_transaction'] = 0;
                            $arrPostPortion['updated_at'] = date("Y-m-d H:i:s");
                            $arrPostPortion['updated_by'] = $_SESSION['RR']->user->id;
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

                        (new BillsToPayInstallment())->insert($arrPostPortion);
                    }
                } else {
                    $valueOfInstallments = Util::unmaskMoney($post['price']);

                    list($year, $month, $day) = explode("-", $post['due_date']);

                    for ($p = 1; $p <= $post['number_of_installments']; $p++) {
                        $arrPostPortion = [
                            'number_portion' => $p,
                            'id_bills_to_pay' => $response->lastId,
                            'id_form_of_payment' => $post['id_form_of_payment'],
                            'due_date' => !empty(trim($post['due_date'])) ? $post['due_date'] : NULL,
                            'value_of_installments' => $valueOfInstallments,
                            'status_payment' => !empty($post['status_payment']) ? $post['status_payment'] : 1,
                            'description' => $post['description'],
                            'created_by' => $_SESSION['RR']->user->id,
                        ];

                        if (!empty($post['status_payment']) && $post['status_payment'] == 2) {
                            $arrPostPortion['pay_day'] = $post['due_date'];
                            $arrPostPortion['amount_paid'] = $valueOfInstallments;
                            $arrPostPortion['updated_at'] = date("Y-m-d H:i:s");
                            $arrPostPortion['updated_by'] = $_SESSION['RR']->user->id;
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

                        (new BillsToPayInstallment())->insert($arrPostPortion);
                    }
                }
            }

            $_SESSION['RR']->toast = (object)[
                'icon' => ($response->error === true ? 'error' : 'success'),
                'title' => $response->message,
            ];

            $this->db->commit();

            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            echo $error->getMessage();
        }
    }

    public function getAmountOfInstallmentsOfRelease($billsToPayId)
    {
        $sql = "SELECT
                    (bills_to_pay_installments.id)
                FROM bills_to_pay_installments
                WHERE TRUE
                AND bills_to_pay_installments.id_bills_to_pay = :id
                AND bills_to_pay_installments.status = 1
                AND bills_to_pay_installments.status_payment != 3
                AND bills_to_pay_installments.status_payment != 9 ";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $billsToPayId);
        $query->execute($parameters);

        return $query->rowCount();
    }

    public function recursiveBillsByCostCenter($costCenterId)
    {
        $cost_centers = array_unique((new RecursiveCostCenter)->recursiveGetChildren($costCenterId));

        $bills = [];
        foreach ($cost_centers as $cc) {
            $bill = (new BillsToPay)->getWithFiltersAllItems([
                (object)[
                    'columns' => [
                        'id_cost_center' => (object)['value' => $cc]
                    ]
                ]
            ])->data;
            if (!empty($bill))
                array_push($bills, $bill);
        }

        return $bills;
    }
}
