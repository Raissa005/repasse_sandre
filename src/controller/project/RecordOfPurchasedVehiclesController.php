<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\BoxAlert;
use RR\libs\Pagination;
use RR\model\PurchaseRequests;

class RecordOfPurchasedVehiclesController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'record-of-purchased-vehicles';
        $this->dir = 'record-of-purchased-vehicles';
        $this->model = new PurchaseRequests();
        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
        $this->title = "Relatório de Veículos Comprados";
    }

    public function index(){
        Secure::access_admin(true);

        $filtersPrint = isset(explode('?', $_SERVER["REQUEST_URI"])[1]) ? explode('?', $_SERVER["REQUEST_URI"])[1] : '';

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Relatório Veículos Comprados',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'icon' => 'fas fa-print',
                    'target' => '_blank',
                    'text' => 'Imprimir Relatório',
                    'href' => URL . "{$this->route}/print/?{$filtersPrint}"
                ]
            ]
        ];

        $rows = 20;
        $filters = [];
        $page = Pagination::getPage();

        if( isset($_GET['status']) && !empty($_GET['status']) && $_GET['status'] == 1 )
        {
            $filters [] = (object)[
                'columns' => [
                    'status' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => 1
                    ]
                ]
            ];
        }else
        {
            $filters [] = (object)[
                'columns' => [
                    'status' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => null
                    ]
                ]
            ];
        }

        if( isset($_GET['name']) && !empty($_GET['name']) )
        {
            $filters[] = (object) [
                'where' => "AND (
                    ucase(customer.name) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.name) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.year_model) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.plate) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.chassi) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.renavam) LIKE ucase('%". $_GET['name'] . "%')
                    OR
                    ucase(this->table.id) LIKE ucase('%". $_GET['name'] . "%')
                    OR
                    ucase(types_negotiations.name) LIKE ucase('%". $_GET['name'] . "%')
                )"
            ];
        }

        if( isset($_GET['data_de']) && !empty($_GET['data_de']) && isset($_GET['data_ate']) && !empty($_GET['data_ate']) )
        {
            $filters[] = (object) [
                'table' => 'purchase_requests',
                'columns' => [
                    'purchase_date' => (object)[
                        'comparison' => 'BETWEEN',
                        'value1' => $_GET['data_de'],
                        'value2' => $_GET['data_ate']
                    ]
                ]
            ];
        }

        $response = $this->model->getWithFiltersAllItems(
            $filters,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'customer', 'columns' => ['name' => ['name_customer']]],
                (object)['table' => 'vehicles', 'columns' => ['name' => ['veiculo'], 'id' => ['id_veiculo'], 'year_model' => ['year_model'], 'chassi' => ['chassi'], 'vehicle_sales_value' => ['vehicle_sales_value'], 'renavam' => ['renavam'], 'id_purchase_request' => ['id_purchase_request'], 'plate' => ['plate']]],
                (object)['table' => 'vehicle_purchases', 'columns' => ['id_former_owner' => ['id_former_owner'], 'purchase_value' => ['purchase_value'], 'value_commission' => ['value_commission']]],
                (object)['table' => 'types_negotiations', 'columns' => ['name' => ['type_negotiation'] ]]
            ],
            [
                'orderBy' => 'purchase_requests.purchase_date DESC',
                'limit' => $rows,
                'page' => $page
            ]
        );

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function($item){
            $item->action = [];

            array_push($item->action,
            (object)[
                'id' => $item->id_purchase_request,
                'icon' => 'fa fa-eye',
                'href' => URL . "purchaseRequests/purchaseVehicles/{$item->id}",
                'size' => 'sm',
                'title' => 'Vizualizar Pedido de Compra',
                'color' => 'warning'
            ]);
        }, $response->data);

        foreach($response->data as $row){
            if($row->purchase_date){
                $row->purchase_date = date('d/m/Y', strtotime($row->purchase_date));
            }

            if($row->value){
                $row->value = number_format($row->value, 2, ',', '.');
            }

            if($row->value_commission){
                $row->value_commission = number_format($row->value_commission, 2, ',', '.');
            }

            if($row->purchase_value){
                $row->purchase_value = number_format($row->purchase_value, 2, ',', '.');
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
                    'column' => (object)['type' => 'text', 'link' => 'id'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Cliente / Fornecedor',
                    'column' => (object)['type' => 'text', 'link' => 'name_customer'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data da Compra',
                    'column' => (object)['type' => 'date', 'link' => 'purchase_date'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Veículo',
                    'column' => (object)['type' => 'text', 'link' => 'veiculo'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Placa',
                    'column' => (object)['type' => 'text', 'link' => 'plate'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Ano Modelo',
                    'column' => (object)['type' => 'text', 'link' => 'year_model'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Chassi',
                    'column' => (object)['type' => 'text', 'link' => 'chassi'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Renavam',
                    'column' => (object)['type' => 'text', 'link' => 'renavam'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Tipo de negociação',
                    'column' => (object)['type' => 'date', 'link' => 'type_negotiation'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Comissão',
                    'column' => (object)['type' => 'text', 'link' => 'value_commission'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Valor de compra',
                    'column' => (object)['type' => 'text', 'link' => 'purchase_value'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action']
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

        if( isset($_GET['status']) && !empty($_GET['status']) && $_GET['status'] == 1 )
        {
            $filters [] = (object)[
                'columns' => [
                    'status' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => 1
                    ]
                ]
            ];
        }else
        {
            $filters [] = (object)[
                'columns' => [
                    'status' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => null
                    ]
                ]
            ];
        }

        if( isset($_GET['name']) && !empty($_GET['name']) )
        {
            $filters[] = (object) [
                'where' => "AND (
                    ucase(customer.name) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.name) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.year_model) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.plate) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.chassi) LIKE ucase('%" . $_GET['name'] . "%')
                    OR
                    ucase(vehicles.renavam) LIKE ucase('%". $_GET['name'] . "%')
                    OR
                    ucase(this->table.id) LIKE ucase('%". $_GET['name'] . "%')
                    OR
                    ucase(types_negotiations.name) LIKE ucase('%". $_GET['name'] . "%')
                )"
            ];
        }

        if( isset($_GET['data_de']) && !empty($_GET['data_de']) && isset($_GET['data_ate']) && !empty($_GET['data_ate']) )
        {
            $datas = [
                'due_date_start' => $_GET['data_de'],
                'due_date_end' => $_GET['data_ate']
            ];

            $filters[] = (object) [
                'table' => 'purchase_requests',
                'columns' => [
                    'purchase_date' => (object)[
                        'comparison' => 'BETWEEN',
                        'value1' => $_GET['data_de'],
                        'value2' => $_GET['data_ate']
                    ]
                ]
            ];
        }

        $response = $this->model->getWithFiltersAllItems(
            $filters,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'customer', 'columns' => ['name' => ['name_customer']]],
                (object)['table' => 'vehicles', 'columns' => ['name' => ['veiculo'], 'id' => ['id_veiculo'], 'year_model' => ['year_model'], 'chassi' => ['chassi'], 'vehicle_sales_value' => ['vehicle_sales_value'], 'renavam' => ['renavam'], 'id_purchase_request' => ['id_purchase_request'], 'plate' => ['plate']]],
                (object)['table' => 'vehicle_purchases', 'columns' => ['id_former_owner' => ['id_former_owner'], 'purchase_value' => ['purchase_value'], 'value_commission' => ['value_commission']]],
                (object)['table' => 'types_negotiations', 'columns' => ['name' => ['type_negotiation'] ]]
            ],
            [
                'orderBy' => 'purchase_requests.purchase_date DESC'
            ]
        );

        foreach($response->data as $row){
            if($row->purchase_date){
                $row->purchase_date = date('d/m/Y', strtotime($row->purchase_date));
            }

            if($row->value){
                $valueTotal += (float) $row->value;
                $row->value = number_format($row->value, 2, ',', '.');
            }

            if($row->value_commission){
                $row->value_commission = number_format($row->value_commission, 2, ',', '.');
            }

            if($row->purchase_value){
                $row->purchase_value = number_format($row->purchase_value, 2, ',', '.');
            }
        }

        require APP . 'view/' . $this->dir . '/print.php';
    }
}

