<?php

namespace RR\model;

use RR\core\Model;

class Expenses extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'sales_expenses';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function addExpense(string $name, float $amount, int $saleId): object
    {
        $arr = [
            'id_sale' => $saleId,
            'expense_key' => trim($name),
            'expense_value' => $amount,
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arr);

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            if (ENVIRONMENT === "development") {
                echo $error->getMessage();
                exit;
            }
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao adicionar despesa'];
        }
    }

    public function deleteExpense(int $id): object
    {
        try {
            $this->db->beginTransaction();

            $response = $this->delete($id);

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            if (ENVIRONMENT === "development") {
                echo $error->getMessage();
                exit;
            }
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao excluir despesa'];
        }
    }
}
