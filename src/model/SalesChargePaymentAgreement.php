<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class SalesChargePaymentAgreement extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'sales_charge_payment_agreement';
        $joins = [
            (object)[
                'table' => 'user_position',
                'join' => 'left',
                'where' => "{$this->table}.id_user_position = user_position.id"
            ],
            (object)[
                'table' => 'customer',
                'join' => 'left',
                'where' => "{$this->table}.id_customer = customer.id"
            ],

        ];

        parent::__construct($this->table, $joins);
    }

    public function handleFormAdd(array $post)
    {
        $arrayPost = [
            'id_sale' => $post['id_sale'],
            'id_customer' => $post['id_customer'],
            'origin_commission' => $post['origin_commission'],
            'percentage_commission' => $post['percentage_commission'],
            'currency_id' => $post['currency_id'],
            'payment_date' => $post['payment_date'],
        ];

        if (isset($post['id_user_position']) && !empty($post['id_user_position'])) {
            $arrayPost['id_user_position'] = $post['id_user_position'];
        } else if (isset($post['id_cost_center'])) {
            $arrayPost['id_cost_center'] = $post['id_cost_center'];
            $arrayPost['id_form_payment'] = $post['id_form_payment'];
        }

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrayPost);

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

    /**
     * @param int $id
     */
    public function handleFormRemove($id)
    {
        try {
            $this->db->beginTransaction();

            $response = $this->delete($id);

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
