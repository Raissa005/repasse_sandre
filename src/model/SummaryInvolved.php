<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Util;

class SummaryInvolved extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'summary_involved';
        $joins = [];

        parent::__construct($this->table,  $joins);
    }

    public function updateSummaryInvolved(array $arrPost, $id, $installment, $customerId, $userPositionId)
    {
        foreach ($arrPost as $key => $value) {
            $columnArray[] = "`{$key}` = :{$key}";
            $parameters[":{$key}"] = ($value != "" ? $value : NULL);
        }

        $parameters[':id'] = $id;
        $parameters[':customer_id'] = $customerId;
        $parameters[':installment_number'] = $installment;
        $parameters[':user_position_id'] = $userPositionId;

        $column = implode(",", $columnArray);

        $sql = "UPDATE {$this->table} si
                SET {$column}
                WHERE si.sale_id = :id
                AND si.customer_id = :customer_id
                AND si.installment_number = :installment_number
                AND si.user_position_id = :user_position_id";

        $query = $this->db->prepare($sql);
        $responseQuery = $query->execute($parameters);

        return (object)['error' => !$responseQuery, 'message' => $responseQuery ? 'Item editado com successo' : 'Erro ao editar esse item'];
    }

    public function getTotalInvolved(int $saleId)
    {
        $sql = "SELECT
                    si.customer_id,
                    si.user_position_id,
                    SUM(si.amount) AS total
                FROM summary_involved si
                WHERE si.sale_id = $saleId
                GROUP BY si.customer_id, si.user_position_id";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }
}
