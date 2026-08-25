<?php

namespace RR\model;

use PDOException;
use RR\libs\Util;
use RR\core\Model;

class SummarySale extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'summary_sale';
        $joins = [];

        parent::__construct($this->table,  $joins);
    }

    public function updateSummarySale(array $arrPost, $id, $installment)
    {
        foreach ($arrPost as $key => $value) {
            $columnArray[] = "`{$key}` = :{$key}";
            $parameters[":{$key}"] = ($value != "" ? $value : NULL);
        }
        $parameters[':id'] = $id;
        $parameters[':installment_number'] = $installment;

        $column = implode(",", $columnArray);

        $sql = "UPDATE {$this->table} ss
                SET {$column}
                WHERE ss.sale_id = :id
                AND ss.installment_number = :installment_number";

        $query = $this->db->prepare($sql);
        $responseQuery = $query->execute($parameters);

        return (object)['error' => !$responseQuery, 'message' => $responseQuery ? 'Item editado com successo' : 'Erro ao editar esse item'];
    }

    public function handleFormSummaryPaymentAgreement(int $id, array $post)
    {
        $summarySale = (new SummarySale)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $id]]]]);
        $summaryInvolved = (new SummaryInvolved)->getWithFiltersAllItems([(object)['columns' => ['sale_id' => (object)['comparison' => 'EQUAL', 'value' => $id]]]]);

        try {
            $this->db->beginTransaction();

            if ($summarySale->count > 0) {
                for ($i = 1; $i <= count($post['installments']); $i++) {
                    $customerId = [];
                    $userPositionId = [];

                    if ($summaryInvolved->count > 0) {
                        foreach ($post['installments'][$i] as $key => $value) {
                            if (strpos($key, 'positions') !== false) {
                                if (!in_array($value['id_customer'], $customerId) || !in_array($value['user_position_id'], $userPositionId)) {
                                    $customerId[] = $value['id_customer'];
                                    $userPositionId[] = $value['user_position_id'];

                                    $value['user_position_id'] = !empty($value['user_position_id']) ? $value['user_position_id'] : 0;

                                    $arrPost = [
                                        'sale_id' => $id,
                                        'installment_number' => $i,
                                        'customer_id' => $value['id_customer'],
                                        'currency_id' => $value['position_currency'],
                                        'user_position_id' => $value['user_position_id'],
                                        'payment_date' => $value['position_payment_date'],
                                        'id_cost_center' => $value['id_cost_center'] ?? "",
                                        'id_form_payment' => $value['id_form_payment'] ?? "",
                                        'amount' => Util::unmaskMoney($value['position_amount']),
                                        'currency_value' => $value['id_customer'] != 1 ? Util::unmaskMoney($value['position_currency_value']) : null,
                                    ];

                                    $response = (new SummaryInvolved)->updateSummaryInvolved($arrPost, $id, $i, $value['id_customer'], $value['user_position_id']);
                                }
                            }
                        }
                    } else {
                        foreach ($post['installments'][$i] as $key => $value) {
                            if (strpos($key, 'positions') !== false) {
                                if (!in_array($value['id_customer'], $customerId) || !in_array($value['user_position_id'], $userPositionId)) {
                                    $customerId[] = $value['id_customer'];
                                    $userPositionId[] = $value['user_position_id'];

                                    $value['user_position_id'] = !empty($value['user_position_id']) ? $value['user_position_id'] : 0;

                                    $arrPost = [
                                        'sale_id' => $id,
                                        'installment_number' => $i,
                                        'customer_id' => $value['id_customer'],
                                        'currency_id' => $value['position_currency'],
                                        'user_position_id' => $value['user_position_id'],
                                        'payment_date' => $value['position_payment_date'],
                                        'id_cost_center' => $value['id_cost_center'] ?? "",
                                        'id_form_payment' => $value['id_form_payment'] ?? "",
                                        'amount' => Util::unmaskMoney($value['position_amount']),
                                        'currency_value' => $value['id_customer'] != 1 ? Util::unmaskMoney($value['position_currency_value']) : null
                                    ];

                                    $response = (new SummaryInvolved)->insert($arrPost);
                                }
                            }
                        }
                    }

                    $arrPost = [
                        'currency_id' => $post['installments'][$i]['currency_id'],
                        'currency_value' => $post['installments'][$i]['currency_id'] != 1 ? Util::unmaskMoney($post['installments'][$i]['currency_value']) : null,
                        'amount' => Util::unmaskMoney($post['installments'][$i]['value']),
                        'received_date' => $post['installments'][$i]['due_date']
                    ];

                    $arrPost = array_merge($arrPost, Util::unmaskMoney($post['installments'][$i]['commission_partition_table']));

                    $response = Self::updateSummarySale($arrPost, $id, $i);
                }
            } else {
                for ($i = 1; $i <= count($post['installments']); $i++) {
                    $customerId = [];
                    $userPositionId = [];

                    foreach ($post['installments'][$i] as $key => $value) {
                        if (strpos($key, 'positions') !== false) {
                            if (!in_array($value['id_customer'], $customerId) || !in_array($value['user_position_id'], $userPositionId)) {
                                $customerId[] = $value['id_customer'];
                                $userPositionId[] = $value['user_position_id'];

                                $value['user_position_id'] = !empty($value['user_position_id']) ? $value['user_position_id'] : 0;

                                $arrPost = [
                                    'sale_id' => $id,
                                    'installment_number' => $i,
                                    'customer_id' => $value['id_customer'],
                                    'currency_id' => $value['position_currency'],
                                    'user_position_id' => $value['user_position_id'],
                                    'payment_date' => $value['position_payment_date'],
                                    'id_cost_center' => $value['id_cost_center'] ?? "",
                                    'id_form_payment' => $value['id_form_payment'] ?? "",
                                    'amount' => Util::unmaskMoney($value['position_amount']),
                                    'currency_value' => $value['id_customer'] != 1 ? Util::unmaskMoney($value['position_currency_value']) : null
                                ];

                                $response = (new SummaryInvolved)->insert($arrPost);
                            }
                        }
                    }

                    $arrPost = [
                        'sale_id' => $id,
                        'installment_number' => $i,
                        'currency_id' => $post['installments'][$i]['currency_id'],
                        'currency_value' => $post['installments'][$i]['currency_id'] != 1 ? Util::unmaskMoney($post['installments'][$i]['currency_value']) : null,
                        'amount' => Util::unmaskMoney($post['installments'][$i]['value']),
                        'received_date' => $post['installments'][$i]['due_date']
                    ];

                    $arrPost = array_merge($arrPost, Util::unmaskMoney($post['installments'][$i]['commission_partition_table']));

                    $response = (new SummarySale)->insert($arrPost);
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
}
