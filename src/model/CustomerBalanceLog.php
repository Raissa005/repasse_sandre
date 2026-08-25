<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Util;

class CustomerBalanceLog extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'customer_balance_log';
        $joins = [
            (object)[
                'table' => "sales",
                'join' => "inner",
                'where' => "sales.id = {$this->table}.id_sale"
            ],
            (object)[
                'table' => "customer",
                'join' => "inner",
                'where' => "customer.id = {$this->table}.id_customer"
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function insertLogReceive(int $customerId, float $amount, string $text, ?int $idSale = null)
    {
        $customer = (new Customer)->getItemById($customerId);

        try {
            $this->db->beginTransaction();

            $arrInsert = [
                'id_customer' => $customerId,
                'amount_received' => $amount,
                'type' => 1,
                'text' => $text
            ];

            if ($idSale) {
                $arrInsert['id_sale'] = $idSale;
            }

            $this->insert($arrInsert);
            (new Customer())->update(['balance' => ($customer->balance + $amount)], 'id', $customerId);

            $this->db->commit();
            return (object)['error' => 'false', 'message' => 'Recebimento realizado com sucesso'];
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }

            return (object)['error' => 'true', 'message' => 'Ocorreu um erro ao tentar registrar o recebimento.', 'sale' => $idSale];
        }
    }

    public function insertLogPay(int $customerId, float $amount, string $text, ?int $idSale = null): object
    {
        $customer = (new Customer)->getItemById($customerId);

        try {
            $this->db->beginTransaction();

            $arrInsert = [
                'id_customer' => $customerId,
                'amount_paid' => $amount,
                'type' => 2,
                'text' => $text
            ];

            if (!empty($idSale)) {
                $arrInsert['id_sale'] = $idSale;
            }

            $this->insert($arrInsert);
            (new Customer())->update(['balance' => strval($customer->balance - $amount)], 'id', $customerId);

            $this->db->commit();
            return (object)['error' => 'false', 'message' => 'Pagamento realizado com sucesso'];
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === 'development') {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => 'true', 'message' => 'Ocorreu um erro ao tentar registrar o recebimento.', 'sale' => $idSale];
        }
    }
}
