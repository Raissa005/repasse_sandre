<?php

namespace RR\model;

use PDOException;
use RR\core\Model;

class ArrangementPaymentChargesInvoiceReceiveInstallment extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'arrangement_payment_charges_invoice_receive_installment';
        $joins = [
            (object)[
                'table' => 'customer',
                'join' => 'inner',
                'where' => "{$this->table}.id_customer = customer.id"
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function handleFormAdd(array $post)
    {
        $arrayPost = [
            'id_bill_receive_installment' => $post['id_bill_receive_installment'],
            'id_customer' => $post['id_customer'],
            'origin_commission' => $post['origin_commission'],
            'percentage_commission' => $post['percentage_commission'],
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
