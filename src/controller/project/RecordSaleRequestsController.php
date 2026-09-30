<?php

namespace RR\controller\project;

use RR\libs\Pagination;
use RR\model\SaleRequests;
use RR\model\VehicleCosts;
use RR\model\Customer;
use RR\model\BillReceiveInstallment;
use RR\model\BillReceive;
use RR\libs\Secure;


class RecordSaleRequestsController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'record-sale-requests';
        $this->dir = 'record-sale-requests';
        $this->model = new SaleRequests();
        $this->table = 'sale_requests';
        parent::__construct($this->route);

        $this->title = "Relatório de Pedidos de Venda";
    }

    public function index ()
    {
        Secure::access_admin(true);

        $filtersPrint = isset(explode('?', $_SERVER["REQUEST_URI"])[1]) ? explode('?', $_SERVER["REQUEST_URI"])[1] : '';

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Relatório Pedidos de Venda',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'icon' => 'fas fa-print',
                    'text' => 'Imprimir Relatório',
                    'href' => URL . "{$this->route}/print/?{$filtersPrint}"
                ]
            ]
        ];

        $rows = 20;
        $page = Pagination::getPage();
        $purchasingBrokers = (new Customer())->getWithFiltersAllItems(
            [
                (object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]
            ]
        )->data;

        $response = $this->model->getReportSaleReq($_GET, $rows, $page);

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        foreach($response->data as $row){
            $vehicleCosts = (new VehicleCosts)->getCostsGroupByCustomer($row->id_vehicle)->data;
            $row->id_broker_sale = "";
            $totalCost = 0;

            if(isset($vehicleCosts) && !empty($vehicleCosts)){
                 foreach($vehicleCosts as $vehicle){
                     $totalCost += $vehicle->total;
                 }
            }

            $row->lucro = ($row->value_sale - $row->commission_sale) - ($row->purchase_value + $row->commission_purchase) -  $totalCost;
            $row->lucro = number_format($row->lucro, 2, ',', '.');
            $row->id_vehicle = '<a href="' . URL . 'vehicles/editItem/' . $row->id_vehicle . '" target="_blank">' . $row->id_vehicle . '</a>';

            if(!empty($row->fancy_name_company) || !empty($row->company_name) || !empty($row->customer_name)){
                $row->id_broker_sale = !empty($row->fancy_name_company)
                ? $row->fancy_name_company
                : (!empty($row->company_name)
                    ? $row->company_name
                    : $row->customer_name);
            }

            if($row->purchase_value){
                $row->purchase_value = number_format($row->purchase_value, 2, ',', '.') ?? 0;
            }

            if($row->value_sale){
                $row->value_sale = number_format($row->value_sale, 2, ',', '.');
            }

            if($row->commission_sale){
                $row->commission_sale = number_format($row->commission_sale, 2, ',', '.');
            }

            if($row->commission_purchase){
                $row->commission_purchase = number_format($row->commission_purchase, 2, ',', '.');
            }

            if($row->purchase_date){
                $row->purchase_date = date('d/m/Y', strtotime($row->purchase_date ));
            }

            if($row->sale_date){
                $row->sale_date = date('d/m/Y', strtotime($row->sale_date ));
            }
        }

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true,
            ],
            'thead' => [
                (object)[
                    'style' => 'width: 60px',
                    'class' => 'text-center',
                    'text' => 'Cód.',
                    'column' => (object)[
                        'type' => 'text',
                        'link' =>  'id_vehicle'
                    ],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Veículo',
                    'column' => (object)['type' => 'text', 'link' => 'vehicle_name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data compra',
                    'column' => (object)['type' => 'date', 'link' => 'purchase_date'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Valor compra',
                    'column' => (object)['type' => 'text', 'link' => 'purchase_value'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Comissão Compra',
                    'column' => (object)['type' => 'text', 'link' => 'commission_purchase']
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data Venda',
                    'column' => (object)['type' => 'date', 'link' => 'sale_date'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Valor Venda',
                    'column' => (object)['type' => 'text', 'link' => 'value_sale'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Repassador',
                    'column' => (object)['type' => 'date', 'link' => 'id_broker_sale'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Comissão Venda',
                    'column' => (object)['type' => 'text', 'link' => 'commission_sale']
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Lucro',
                    'column' => (object)['type' => 'text', 'link' => 'lucro']
                ]
            ],
            'data' => $response->data
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function print(){
        Secure::access_admin(true);

        $valueTotal = 0;
        $totalVenda = 0;
        $totalCompra = 0;

        $response = $this->model->getReportSaleReq($_GET, 0, 1);

        foreach($response->data as $row){
            $row->lucro = ($row->value_sale - $row->commission_sale) - ($row->purchase_value + $row->commission_purchase);
            $valueTotal += $row->lucro;
            $totalVenda += $row->value_sale;
            $totalCompra += $row->purchase_value;
            $row->lucro = number_format($row->lucro, 2, ',', '.');
            $row->id_broker_sale = "";

            if($row->purchase_value){
                $row->purchase_value = number_format($row->purchase_value, 2, ',', '.');
            }

            if($row->value_sale){
                $row->value_sale = number_format($row->value_sale, 2, ',', '.');
            }

            if($row->commission_sale){
                $row->commission_sale = number_format($row->commission_sale, 2, ',', '.');
            }

            if($row->commission_purchase){
                $row->commission_purchase = number_format($row->commission_purchase, 2, ',', '.');
            }

            if(!empty($row->fancy_name_company) || !empty($row->company_name) || !empty($row->customer_name)){
                $row->id_broker_sale = !empty($row->fancy_name_company)
                ? $row->fancy_name_company
                : (!empty($row->company_name)
                    ? $row->company_name
                    : $row->customer_name);
            }
        }

        require APP . 'view/' . $this->dir . '/print.php';
    }
}
