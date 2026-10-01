<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Pagination;
use RR\model\SaleRequests;

class RecordOfSoldVehiclesController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->route = 'record-of-sold-vehicles';
        $this->dir = 'record-of-sold-vehicles';
        $this->model = new SaleRequests();
        $this->table = 'sale_requests';
        parent::__construct($this->route);

        $this->title = "Relatório de Veículos Vendidos";
    }

    public function index ()
    {
        Secure::access_admin(true);

        $filtersPrint = isset(explode('?', $_SERVER["REQUEST_URI"])[1]) ? explode('?', $_SERVER["REQUEST_URI"])[1] : '';

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Relatório Veículos Vendidos',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'icon' => 'fas fa-print',
                    'text' => 'Imprimir Relatório',
                    'href' => URL . "{$this->route}/print/?" . htmlspecialchars($filtersPrint, ENT_QUOTES, 'UTF-8')
                ]
            ]
        ];

        $rows = 20;
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
            $busca = '%' . $_GET['name'] . '%';

            $filters[] = (object) [
                'where' => "AND (
                    ucase(customer.name) LIKE ucase(:busca_1)
                    OR
                    ucase(vehicles.name) LIKE ucase(:busca_2)
                    OR
                    ucase(vehicles.year_model) LIKE ucase(:busca_3)
                    OR
                    ucase(vehicles.chassi) LIKE ucase(:busca_4)
                    OR
                    ucase(vehicles.renavam) LIKE ucase(:busca_5)
                    OR
                    ucase(this->table.id) LIKE ucase(:busca_6)
                )",
                'parameters' => [
                    ':busca_1' => $busca,
                    ':busca_2' => $busca,
                    ':busca_3' => $busca,
                    ':busca_4' => $busca,
                    ':busca_5' => $busca,
                    ':busca_6' => $busca
                ]
            ];
        }

        if( isset($_GET['data_de']) && !empty($_GET['data_de']) && isset($_GET['data_ate']) && !empty($_GET['data_ate']) )
        {
            $filters[] = (object) [
                'table' => 'sale_requests',
                'columns' => [
                    'sale_date' => (object)[
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
                (object)['table' => 'customer', 'columns' => ['name' => ['customer_name']]],
                (object)['table' => 'users', 'columns' => [ 'name' => ['user'] ]],
                (object)['table' => 'vehicles_request_sale', 'columns' => ['id_vehicle', 'value_commission' => ['value_commission'], 'value' => ['value_sale']]],
                (object)['table' => 'vehicles', 'columns' => ['name' => ['veiculo'], 'id' => ['id_veiculo'], 'year_model' => ['year_model'], 'chassi' => ['chassi'], 'vehicle_sales_value' => ['vehicle_sales_value'], 'renavam' => ['renavam'], 'id_purchase_request' => ['id_purchase_request'], 'plate' => ['plate']]]
            ],
            [
                'orderBy' => 'sale_requests.sale_date DESC',
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
                'href' => URL . "saleRequests/saleVehicles/{$item->id}",
                'size' => 'sm',
                'title' => 'Vizualizar Pedido Venda',
                'color' => 'warning'
            ]);
        }, $response->data);

        foreach($response->data as $row){
            if($row->sale_date){
                $row->sale_date = date('d/m/Y', strtotime($row->sale_date));
            }

            if($row->value){
                $row->value = number_format($row->value, 2, ',', '.');
            }

            if($row->vehicle_sales_value){
                $row->vehicle_sales_value = number_format($row->vehicle_sales_value, 2, ',', '.');
            }

            if($row->value_commission){
                $row->value_commission = number_format($row->value_commission, 2, ',', '.');
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
                    'text' => 'Cliente / Comprador',
                    'column' => (object)['type' => 'text', 'link' => 'customer_name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data venda',
                    'column' => (object)['type' => 'date', 'link' => 'sale_date'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Veículo',
                    'column' => (object)['type' => 'text', 'link' => 'veiculo']
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Placa',
                    'column' => (object)['type' => 'text', 'link' => 'plate'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Ano modelo',
                    'column' => (object)['type' => 'text', 'link' => 'year_model']
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Chassi',
                    'column' => (object)['type' => 'text', 'link' => 'chassi']
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Renavam',
                    'column' => (object)['type' => 'text', 'link' => 'renavam']
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Comissão',
                    'column' => (object)['type' => 'text', 'link' => 'value_commission']
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Valor Venda',
                    'column' => (object)['type' => 'text', 'link' => 'value']
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
            $busca = '%' . $_GET['name'] . '%';

            $filters[] = (object) [
                'where' => "AND (
                    ucase(customer.name) LIKE ucase(:busca_1)
                    OR
                    ucase(vehicles.name) LIKE ucase(:busca_2)
                    OR
                    ucase(vehicles.year_model) LIKE ucase(:busca_3)
                    OR
                    ucase(vehicles.chassi) LIKE ucase(:busca_4)
                    OR
                    ucase(vehicles.renavam) LIKE ucase(:busca_5)
                    OR
                    ucase(this->table.id) LIKE ucase(:busca_6)
                )",
                'parameters' => [
                    ':busca_1' => $busca,
                    ':busca_2' => $busca,
                    ':busca_3' => $busca,
                    ':busca_4' => $busca,
                    ':busca_5' => $busca,
                    ':busca_6' => $busca
                ]
            ];
        }

        if( isset($_GET['data_de']) && !empty($_GET['data_de']) && isset($_GET['data_ate']) && !empty($_GET['data_ate']) )
        {
            $datas = [
                'due_date_start' => $_GET['data_de'],
                'due_date_end' => $_GET['data_ate']
            ];

            $filters[] = (object) [
                'table' => 'sale_requests',
                'columns' => [
                    'sale_date' => (object)[
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
                (object)['table' => 'customer', 'columns' => ['name' => ['customer_name']]],
                (object)['table' => 'users', 'columns' => [ 'name' => ['user'] ]],
                (object)['table' => 'vehicles_request_sale', 'columns' => ['id_vehicle', 'value_commission' => ['value_commission']]],
                (object)['table' => 'vehicles', 'columns' => ['name' => ['veiculo'], 'id' => ['id_veiculo'], 'year_model' => ['year_model'], 'chassi' => ['chassi'], 'vehicle_sales_value' => ['vehicle_sales_value'], 'renavam' => ['renavam'], 'id_purchase_request' => ['id_purchase_request'], 'plate' => ['plate']]]
            ],
            [
                'orderBy' => 'sale_requests.sale_date DESC'
            ]
        );

        foreach($response->data as $row){
            if($row->sale_date){
                $row->sale_date = date('d/m/Y', strtotime($row->sale_date));
            }

            if($row->value){
                $valueTotal += (float) $row->value;
                $row->value = number_format($row->value, 2, ',', '.');
            }

            if($row->vehicle_sales_value){
                $row->vehicle_sales_value = number_format($row->vehicle_sales_value, 2, ',', '.');
            }

            if($row->value_commission){
                $row->value_commission = number_format($row->value_commission, 2, ',', '.');
            }
        }

        require APP . 'view/' . $this->dir . '/print.php';
    }
}
