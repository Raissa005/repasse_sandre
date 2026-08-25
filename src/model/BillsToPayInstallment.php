<?php

namespace RR\model;

use PDO;
use RR\core\Model;
use RR\libs\Date;
use RR\libs\RecursiveCostCenter;
use RR\libs\Util;

class BillsToPayInstallment extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'bills_to_pay_installments';
        $joins = [
            (object)[
                'table' => 'bills_to_pay',
                'join' => 'left',
                'where' => "{$this->table}.id_bills_to_pay = this->table.id"
            ],
            (object)[
                'table' => 'customer',
                'join' => 'left',
                'where' => "bills_to_pay.id_customer = this->table.id",
                'require' => 'bills_to_pay'
            ],
            (object)[
                'table' => 'cost_center',
                'join' => 'left',
                'where' => "bills_to_pay.id_cost_center = this->table.id",
                'require' => 'bills_to_pay'
            ],
            (object)[
                'table' => 'form_of_payment',
                'join' => 'left',
                'where' => "this->table.id = bills_to_pay.id_form_of_payment",
                'require' => 'bills_to_pay'
            ],
            (object)[
                'table' => 'branch',
                'join' => 'left',
                'where' => "bills_to_pay.id_branch = this->table.id",
                'require' => 'bills_to_pay'
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($filters = [], $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if ($value != '') {
                    $table = $this->table;

                    if (in_array($column, ['status', 'id_branch', 'id_customer', 'id_form_of_payment', 'payment_transaction', 'id_bills_to_pay'])) {
                        if (in_array($column, ['id_branch', 'id_customer', 'id_form_of_payment', 'payment_transaction', 'id_cost_center'])) {
                            $table = 'bills_to_pay';
                        }
                        $filtersQuery .= " AND {$table}.{$column} = :{$table}_{$column}";
                    } else if (in_array($column, ['status_payment'])) {
                        $filtersQuery .= " AND {$table}.{$column} IN (" . (implode(", ", $value)) . ")";
                    } else if (in_array($column, [])) {
                        $filtersQuery .= " AND ucase({$table}.{$column}) LIKE ucase(:{$table}_{$column})";
                        $value = "%" . $value . "%";
                    } else if (in_array($column, ['date'])) {
                        if (isset($value['start']) && !empty($value['start'])) {
                            $filtersQuery .= " AND ( {$this->table}.due_date >= :start_date_due ) ";
                            $parameters[':start_date_due'] = date('Y-m-d', strtotime($value['start']));
                        }

                        if (isset($value['end']) && !empty($value['end'])) {
                            $filtersQuery .= " AND ( {$this->table}.due_date <= :end_date_due) ";
                            $parameters[':end_date_due'] = date('Y-m-d', strtotime("{$value['end']} +1 month -1 day"));
                        }
                    }

                    if (in_array($column, ['status', 'id_branch', 'id_customer', 'id_form_of_payment', 'payment_transaction', 'id_bills_to_pay'])) {
                        $parameters[":{$table}_{$column}"] = $value;
                    }
                }
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.*,
                    COALESCE(customer.fancy_name_company, customer.name) AS customer_name,
                    cost_center.name AS cost_center_name,
                    form_of_payment.name AS form_of_payment_name
                FROM {$this->table}
                INNER JOIN bills_to_pay ON {$this->table}.id_bills_to_pay = bills_to_pay.id
                INNER JOIN cost_center ON bills_to_pay.id_cost_center = cost_center.id
                INNER JOIN customer ON bills_to_pay.id_customer = customer.id
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

    public function getEntryById($id)
    {
        $sql = "SELECT
                    btp.*,
                    COALESCE(c.fancy_name_company, c.name) AS customer_name,
                    ( SELECT
                        SUM(value_of_installments)
                      FROM bills_to_pay_installments btpi
                      WHERE btpi.id_bills_to_pay = btp.id
                      AND btpi.status_payment != 3
                      AND btpi.status_payment != 9
                      AND btpi.status = 1
                    ) AS total_amount,
                    ( SELECT
                        SUM(value_of_installments)
                      FROM bills_to_pay_installments btpi
                      WHERE btpi.id_bills_to_pay = btp.id
                      AND btpi.status = 1
                      AND btpi.status_payment = 2
                    ) AS amount_paid
                FROM bills_to_pay btp
                LEFT JOIN customer c ON c.id = btp.id_customer
                WHERE btp.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAllInstallmentsByBillsToPayId($billsToPayId)
    {
        $sql = "SELECT
                    btpi.*,
                    fop.name AS form_of_payment_name
                FROM bills_to_pay_installments btpi
                INNER JOIN form_of_payment fop ON fop.id = btpi.id_form_of_payment
                WHERE btpi.id_bills_to_pay = :id
                AND btpi.status = 1
                ORDER BY btpi.status_payment, btpi.due_date ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $billsToPayId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getLastNumberPortionByBillsToPayId($billsToPayId)
    {
        $sql = "SELECT
                    btpi.number_portion
                FROM bills_to_pay_installments btpi
                WHERE btpi.id_bills_to_pay = :id
                ORDER BY btpi.id DESC
                LIMIT 1 ";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $billsToPayId);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getItemById8161($id)
    {
        $sql = "SELECT
                    btpi.*,
                    btp.id_branch, btp.id_customer, btp.id_form_of_payment as id_form_of_payment_entry, btp.status as status_entry,
                    c.name AS customer_name,
                    c.fancy_name_company AS customer_fancy_name_company,
                    fop.name AS form_of_payment_name,
                    cc.name AS cost_center_name
                FROM bills_to_pay_installments btpi
                LEFT JOIN bills_to_pay btp ON btp.id = btpi.id_bills_to_pay
                LEFT JOIN customer c ON c.id = btp.id_customer
                LEFT JOIN form_of_payment fop ON fop.id = btpi.id_form_of_payment
                LEFT JOIN cost_center cc ON cc.id = btp.id_cost_center
                WHERE btpi.id = :id";
        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAttachmentsByItemId($id)
    {
        $sql = "SELECT
                    btpia.*
                FROM bills_to_pay_installments_attachment btpia
                WHERE btpia.id_bills_to_pay_installments = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function ActivateAndDesactivateInstallmentForm(int $itemId, array $arrPost): object
    {
        $arrPost = [
            'status_payment' => $arrPost['status_payment'],
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

            $response = $this->update($arrPost, 'id', $itemId);

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT == "develop") {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Houve um erro ao Ativar esses items!'];
        }
    }

    public function getInstallmentsByCostCenter(int $costCenterId): array
    {
        $filters = [
            (object)[
                'columns' => [
                    'status' => ['value' => 1],
                ]
            ],
            (object)[
                'table' => 'bills_to_pay',
                'columns' => [
                    'id_cost_center' => ['value' => $costCenterId],
                ]
            ]
        ];

        $columns = [
            (object)['columns' => ['*']],
            (object)['table' => 'branch', 'columns' => ['id' => ['branch_id'], 'name' => ['branch_name']]],
            (object)['table' => 'cost_center', 'columns' => ['id' => ['cost_center_id'], 'name' => ['cost_center_name']]]
        ];

        $response = $this->getWithFiltersAllItems($filters, $columns)->data;

        return $response;
    }

    public function getValuesFromMonths(array $bills): array
    {
        $months = [];
        $m_names = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        array_map(function ($bill) use (&$months, $m_names) {
            $id_year = date('Y', strtotime($bill->due_date));
            $id_month = date('m', strtotime($bill->due_date));
            $amount = $bill->amount_paid ?? $bill->value_of_installments;

            $months[$id_year][$id_month] = (object)[
                'id' => $id_month,
                'name' => $m_names[$id_month - 1],
                'amount' => ($months[$id_year][$id_month]->amount ?? 0) + $amount,
            ];
            asort($months[$id_year]);
        }, $bills);

        return $months;
    }

    public function getItemsForCards()
    {
        $filters = [
            (object)[
                'table' => 'bills_to_pay',
                'columns' => [
                    'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id],
                ]
            ]
        ];

        if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
            array_push($filters, (object)['columns' => ['due_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime("{$_GET['date']['start']} -1 day")), 'value2' => date('Y-m-d', strtotime($_GET['date']['end'] . "+1 day"))]]]);
        } else {
            if (!empty($_GET['date']['start'])) {
                array_push($filters, (object)['columns' => ['due_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime("{$_GET['date']['start']}"))]]]);
            }

            if (!empty($_GET['date']['end'])) {
                array_push($filters, (object)['columns' => ['due_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
            }
        }

        return $this->getWithFiltersAllItems($filters);
    }

    public function getBillsToPayFromDashboard($filters): array
    {
        $filtersQuery = "";
        $parameters = [];

        if (isset($_SESSION['RR']->branch->current->id) && !empty($_SESSION['RR']->branch->current->id)) {
            $filtersQuery .= " AND btp.id_branch = :id_branch ";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['id_cost_center']) && !empty($_GET['id_cost_center'])) {
            $costCentersIds = (new RecursiveCostCenter)->recursiveGetChildren($_GET['id_cost_center'], [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => 1],
                        'id_type' => (object)['comparison' => 'EQUAL', 'value' => 1]
                    ]
                ]
            ]);

            $filtersQuery .= " AND btp.id_cost_center IN (";

            foreach ($costCentersIds as $value) {
                $filtersQuery .= "$value, ";
            }

            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['by_date'])) {

            switch ($filters['by_date']) {
                case 1:
                    $filtersQuery .= " AND YEAR(btpi.due_date) = :y ";
                    $parameters[':y'] = $filters['selectYear'];
                    $by_date = " btpi.due_date ";
                    break;
                case 2:
                    $filtersQuery .= " AND YEAR(btpi.pay_day) = :y ";
                    $parameters[':y'] = $filters['selectYear'];
                    $by_date = " btpi.pay_day ";
                    break;
                case 3:
                    $filtersQuery .= " AND YEAR(btp.competence) = :y ";
                    $parameters[':y'] = $filters['selectYear'];
                    $by_date = " btp.competence ";
                    break;
            }
        } else {
            $filtersQuery .= " AND YEAR(btp.competence) = :y ";
            $parameters[':y'] = date('Y');
            $by_date = " btp.competence ";
        }

        $sql = "SELECT
                    $by_date,
                    btpi.amount_paid,
                    btpi.value_of_installments
                FROM bills_to_pay_installments btpi
                INNER JOIN bills_to_pay btp ON btpi.id_bills_to_pay = btp.id
                INNER JOIN branch br ON btp.id_branch = br.id
                WHERE TRUE $filtersQuery
                AND btpi.status = 1
                AND btpi.status_payment = 2";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function GetValueInMonthsForPanel(array $bills): array
    {
        $months = [];
        $m_names = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        array_map(function ($bill) use (&$months, $m_names) {
            if (!empty($bill->due_date)) {

                $id_year = date('Y', strtotime($bill->due_date));
                $id_month = date('m', strtotime($bill->due_date));
                $amount = $bill->amount_paid ?? $bill->value_of_installments;
            } elseif (!empty($bill->pay_day)) {

                $id_year = date('Y', strtotime($bill->pay_day));
                $id_month = date('m', strtotime($bill->pay_day));
                $amount = $bill->amount_paid ?? $bill->value_of_installments;
            } elseif (!empty($bill->competence)) {

                $id_year = date('Y', strtotime($bill->competence));
                $id_month = date('m', strtotime($bill->competence));
                $amount = $bill->amount_paid ?? $bill->value_of_installments;
            }
            $months[$id_year][$id_month] = (object)[
                'id' => $id_month,
                'name' => $m_names[$id_month - 1],
                'amount' => ($months[$id_year][$id_month]->amount ?? 0) + $amount,
            ];
            asort($months[$id_year]);
        }, $bills);

        return $months;
    }
}
