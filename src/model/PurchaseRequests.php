<?php

namespace RR\model;

use RR\core\Model;

class PurchaseRequests extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'purchase_requests';
        $joins = [
            (object)[
                'table' => 'users',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_purchase_broker"
            ],
            (object)[
                'table' => 'customer',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_customer"
            ],
            (object)[
                'table' => 'vehicles',
                'joins' => 'inner',
                'where' => "this->table.id_purchase_request = {$this->table}.id"
            ],
            (object)[
                'table' => 'vehicle_purchases',
                'joins'  => 'inner',
                'where' => 'this->table.id_vehicle = vehicles.id'
            ],
            (object)[
                'table'=> 'bills_to_pay_installments',
                'joins' => 'left',
                'where' => "this->table.id_bills_to_pay = {$this->table}.id_bills_to_pay"
            ],
            (object)[
                'table'=> 'types_negotiations',
                'joins' => 'left',
                'where' => "this->table.id = vehicle_purchases.id_type_negotiation"
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function getSaleBrokerByPurchase($id_purchase){
        $sql = "SELECT
                    pr.*,
                    c.name as customer_name,
                    c.company_name,
                    c.fancy_name_company
                FROM
                    purchase_requests pr
                LEFT JOIN
                    bills_to_pay btp on pr.id_commission_to_pay = btp.id
                LEFT JOIN
                    bills_to_pay_installments btpi ON btpi.id_bills_to_pay = btp.id
                LEFT JOIN
                    customer c ON c.id = btp.id_customer
                WHERE
                    pr.id = $id_purchase";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }
}
