<?php

namespace RR\model;

use RR\core\Model;

class SaleRequests extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'sale_requests';
        $joins = [
            (object)[
                'table' => 'customer',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_customer"
            ],
            (object)[
                'table' => 'users',
                'joins' => 'inner',
                'where' => "this->table.id = {$this->table}.id_sale_broker"
            ],
            (object)[
                'table' => 'vehicles_request_sale',
                'joins' => 'left',
                'where' => "this->table.id_sale_request = {$this->table}.id"
            ],
            (object)[
                'table' => 'vehicles',
                'joins' => 'left',
                'where' => "this->table.id = vehicles_request_sale.id_vehicle"
            ],
            (object)[
                'table' => 'bill_receive',
                'joins' => 'left',
                'where' => "this->table.id = {$this->table}.id_bill_receive "
            ],
            (object)[
                'table' => 'bill_receive_installment',
                'joins' => 'left',
                'where' => "this->table.id_bill_receive  = {$this->table}.id_bill_receive"
            ],
            (object)[
                'table' => 'vehicle_purchases',
                'joins' => 'left',
                'where' => "this->table.id_vehicle  = vehicles.id"
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function getReportSaleReq($filters, $rows, $page){
        $parameters = [];
        $filtersQuery = '';

        if( isset($filters['status']) && $filters['status'] != " " && $filters['status'] == '0' )
        {
            $filtersQuery .= " AND sr.status = :status";
            $parameters[':status'] = 0;
        }else
        {
            $filtersQuery .= " AND sr.status = :status";
            $parameters[':status'] = 1;
        }

        if( isset($filters['name']) && !empty($filters['name']) )
        {
            $filtersQuery .= " AND (ucase(v.id) LIKE ucase(:name) OR ucase(v.plate) LIKE ucase(:name))";
            $parameters[':name'] = "%". $filters['name'] . "%";
        }

        if( isset($filters['data_de']) && !empty($filters['data_de']))
        {
            $parameters[':data_de'] = date('Y-m-d', strtotime($filters['data_de']));

            if( (isset($filters['date_type']) && $filters['date_type'] == 1) || (!isset($filters['date_type']) || !empty($filters['date_type'])) )
            {
                $filtersQuery .= " AND vp.purchase_date >= :data_de";

            }else if( isset($_GET['date_type']) && $_GET['date_type'] == 0 )
            {
                $filtersQuery .= " AND sr.sale_date >= :data_de";
            }
        }

        if(isset($filters['data_ate']) && !empty($filters['data_ate']))
        {
            $parameters[':data_ate'] = date('Y-m-d', strtotime($filters['data_ate']));

            if( (isset($filters['date_type']) && $filters['date_type'] == 1) || (!isset($filters['date_type']) || !empty($filters['date_type'])) )
            {
                $filtersQuery .= " AND vp.purchase_date <= :data_ate";

            }else if( isset($_GET['date_type']) && $_GET['date_type'] == 0 )
            {
                $filtersQuery .= " AND sr.sale_date <= :data_ate";
            }
        }

        if(  isset($filters['brokers']) && !empty($filters['brokers']) ){
            $placeholders = [];

            foreach($filters['brokers'] as $index => $key){
                $placeholder = ":broker_$index";
                $placeholders[] = $placeholder;
                $parameters[$placeholder] = $key;
            }

            $filtersQuery .= " AND c.id IN (" . implode(',', $placeholders) . ")";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS
                    sr.*,
                    vrs.value_commission as commission_sale,
                    vrs.value as value_sale,
                    v.name as vehicle_name,
                    v.id as id_vehicle,
                    vp.purchase_value,
                    vp.value_commission as commission_purchase,
                    vp.purchase_date,
                    c.name as customer_name,
                    c.company_name,
                    c.fancy_name_company
                FROM
                    sale_requests sr
                LEFT JOIN
                    bill_receive br on sr.id_commission_receive = br.id
                LEFT JOIN
                    bill_receive_installment bri ON bri.id_bill_receive = br.id
                LEFT JOIN
                    customer c ON c.id = bri.id_purchase_broker
                LEFT JOIN
                    vehicles_request_sale vrs ON vrs.id_sale_request = sr.id
                LEFT JOIN
                    vehicles v ON v.id = vrs.id_vehicle
                LEFT JOIN
                    vehicle_purchases vp ON vp.id_vehicle = v.id
                WHERE TRUE $filtersQuery
                GROUP BY sr.id
                ORDER BY
                    sr.sale_date DESC
        ";

        if ($rows > 0) {
            $offset = ($page - 1) * $rows;
            $sql .= " LIMIT $rows OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $this->db->query("SELECT FOUND_ROWS() AS `rows`")->fetch()->rows];
    }

    public function getSaleBrokerBySale($id_sale){
        $sql = "SELECT
                    sr.*,
                    vrs.value_commission as commission_sale,
                    vrs.value as value_sale,
                    v.name as vehicle_name,
                    v.id as id_vehicle,
                    vp.purchase_value,
                    vp.value_commission as commission_purchase,
                    vp.purchase_date,
                    c.name as customer_name,
                    c.company_name,
                    c.fancy_name_company
                FROM
                    sale_requests sr
                LEFT JOIN
                    bill_receive br on sr.id_commission_receive = br.id
                LEFT JOIN
                    bill_receive_installment bri ON bri.id_bill_receive = br.id
                LEFT JOIN
                    customer c ON c.id = bri.id_purchase_broker
                LEFT JOIN
                    vehicles_request_sale vrs ON vrs.id_sale_request = sr.id
                LEFT JOIN
                    vehicles v ON v.id = vrs.id_vehicle
                LEFT JOIN
                    vehicle_purchases vp ON vp.id_vehicle = v.id
                WHERE
                    sr.id = $id_sale";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }
}
