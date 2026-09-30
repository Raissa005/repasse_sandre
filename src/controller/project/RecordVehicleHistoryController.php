<?php

namespace RR\controller\project;

use RR\libs\Pagination;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\BillReceive;
use RR\model\BillReceiveInstallment;
use RR\model\BillsToPay;
use RR\model\BillsToPayInstallment;
use RR\model\Cities;
use RR\model\SaleRequests;
use RR\model\Customer;
use RR\model\VehicleCosts;
use RR\model\States;
use RR\model\TypesNegotiations;
use RR\model\User;
use RR\model\CostCenter;
use RR\model\VehicleTransferObservation;
use RR\model\FormOfPayment;
use RR\model\VehicleAttachments;
use RR\model\VehicleBrands;
use RR\model\VehicleCategories;
use RR\model\VehicleColors;
use RR\model\VehicleDoors;
use RR\model\VehicleFuels;
use RR\model\VehicleModels;
use RR\model\VehicleObservations;
use RR\model\VehiclePurchases;
use RR\model\Vehicles;
use RR\model\VehiclesRequestSale;
use RR\model\VehicleTypes;
use RR\model\PurchaseRequests;

class RecordVehicleHistoryController extends FrontController
{
    public $route;
    public $dir;
    public $model;
    public $table;
    public $alert;
    public $title;

    public function __construct()
    {
        $this->title = 'Relatório do Histórico dos Veículos';

        $this->route = 'record-vehicle-history';
        $this->dir = 'record-vehicle-history';

        $this->model = new Vehicles();
        $this->table = 'vehicles';

        parent::__construct($this->route);
    }

    private function navTabs(int $itemId)
    {
        $navTabs = [
            (object) ['text' => 'Histórico', 'route' => URL . $this->route . "/historyVehicle/$itemId", 'class' => $_GET['pg1'] == 'historyVehicle' ? 'active' : '']
        ];


        $saleVehicle = (new VehiclesRequestSale())->getWithFiltersAllItems(
            [
                (object)['columns' => [
                    'id_vehicle' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => $itemId
                    ]
                ]]
            ]
        );

        if(isset($saleVehicle->data) && !empty($saleVehicle->data)){
            array_push($navTabs, (object) ['text' => 'Tranferência', 'route' => URL . $this->route . "/vehicleTransfer/$itemId", 'class' => $_GET['pg1'] == 'vehicleTransfer' ? 'active' : '']);
        }

        return $navTabs;
    }

    public function index()
    {
        Secure::access_secretary(true);

        $filtersPrint = isset(explode('?', $_SERVER["REQUEST_URI"])[1]) ? explode('?', $_SERVER["REQUEST_URI"])[1] : '';
        $page = Pagination::getPage();
        $filters = [];
        $color = '';
        $rows = 20;

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => $this->title,
            'caption' => 'Listagem',
            'buttons' => [
                (object) [
                    'color' => 'info',
                    'icon' => 'fas fa-print',
                    'text' => 'Imprimir Relatório',
                    'href' => URL . "{$this->route}/print/?{$filtersPrint}"
                ]
            ]
        ];

        $customer = (new Customer())->getWithFiltersAllItems(
            [
                (object) ['columns' => ['status' => (object) ['value' => 1]]],
                (object) ["table" => 'client_type_resource_types', "columns" => ['id_customer_type' => (object) ['comparison' => 'IN', 'value' => [1, 2]]]]
            ],
            [
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'client_type_resource_types',
                    'columns' => 'id_customer_type'
                ]
            ],
            [
                'groupBy' => 'this->table.id'
            ]
        );

        if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
            array_push(
                $filters,
                (object) ['where' => "AND (
                    ucase(this->table.id) LIKE ucase('%". $_GET['pesquisa'] . "%') OR
                    ucase(this->table.plate) LIKE ucase('%". $_GET['pesquisa'] . "%') OR
                    ucase(this->table.plate) LIKE ucase('%". substr($_GET['pesquisa'], 0, 3) . '-' . substr($_GET['pesquisa'], 3) ."%') OR
                    ucase(this->table.name) LIKE ucase('%". $_GET['pesquisa'] . "%')
                )"]);
        }

        if(isset($_GET['data_de']) && !empty($_GET['data_de']))
        {
            if($_GET['data_tipo'] == 0)
            {
                array_push($filters, (object) ['where' => "AND purchase_requests.purchase_date >= '". $_GET['data_de'] . "'" ]);
            }else if($_GET['data_tipo'] == 1)
            {
                array_push($filters, (object) ['where' => "AND sale_requests.sale_date >= '". $_GET['data_de'] ."'" ]);
            }else if($_GET['data_tipo'] == 2)
            {
                array_push($filters, (object) ['where' => "AND vehicles_request_sale.due_date_transfer >= '". $_GET['data_de'] ."'" ]);
            }
        }

        if(isset($_GET['data_ate']) && !empty($_GET['data_ate']))
        {
            if($_GET['data_tipo'] == 0)
            {
                array_push($filters, (object) ['where' => "AND purchase_requests.purchase_date <= '". $_GET['data_ate'] . "'" ]);
            }else if($_GET['data_tipo'] == 1)
            {
                array_push($filters, (object) ['where' => "AND sale_requests.sale_date <= '". $_GET['data_ate'] . "'" ]);
            }else if($_GET['data_tipo'] == 2)
            {
                array_push($filters, (object) ['where' => "AND vehicles_request_sale.due_date_transfer <= '". $_GET['data_ate'] ."'" ]);
            }
        }

        if(isset($_GET['transfer']) && !empty($_GET['transfer'])){
            array_push($filters, (object) ['where' => "AND vehicles_request_sale.transferred = ". $_GET['transfer']]);
        }else{
            array_push($filters, (object) ['where' => "AND (vehicles_request_sale.transferred = 0 OR vehicles_request_sale.transferred IS NULL) "]);
        }

        $response = $this->model->getWithFiltersAllItems(
            $filters,
            [
                (object) [
                    'columns' => ['*']
                ],
                (object) [
                    'table' => 'purchase_requests',
                    'columns' => ['value' => ['purchase_value'], 'purchase_date' => ['purchase_date'], 'id_customer' => ['purchase_customer'], 'id_bills_to_pay' => ['id_bills_to_pay']]
                ],
                (object) [
                    'table' => 'vehicles_request_sale',
                    'columns' => ['id_vehicle' => ['id_vehicle'], 'value_commission' => ['value_commission'], 'due_date_transfer' => ['due_date_transfer'], 'date_transfer' => ['date_transfer'], 'transferred' => ['transferred']]
                ],
                (object) [
                    'table' => 'sale_requests',
                    'columns' => ['sale_date' => ['sale_date'], 'value' => ['sale_value'], 'id_customer' => ['sale_customer'], 'id_bill_receive' => ['id_bill_receive'], 'status' => ['status_sale']]
                ]
            ],
            [
                'page' => $page,
                'limit' => $rows,
                'orderBy' => 'vehicles.id DESC'
            ]
        );

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        foreach ($response->data as $item) {
            $item->profit = 0;
            $totalCost = 0;

            if((!isset($item->due_date_transfer) || empty($item->due_date_transfer)) && !empty($item->sale_date)){
                $item->due_date_transfer = date('Y-m-d', strtotime('+90 days', strtotime($item->sale_date)));
                (new VehiclesRequestSale())->update(['due_date_transfer' => $item->due_date_transfer], 'id_vehicle', $item->id_vehicle);
            }

            if((!isset($item->transferred) || $item->transferred == 0) && !empty($item->due_date_transfer) && $item->due_date_transfer == date('Y-m-d')){
                $item->tr = 'bg-warning';
            }

            if((!isset($item->transferred) || $item->transferred == 0) && !empty($item->due_date_transfer) && $item->due_date_transfer < date('Y-m-d')){
                $item->tr = 'bg-danger';
            }

           $vehicleCosts = (new VehicleCosts)->getCostsGroupByCustomer($item->id)->data;

           if(isset($vehicleCosts) && !empty($vehicleCosts)){
                foreach($vehicleCosts as $vehicle){
                    $totalCost += $vehicle->total;
                }
           }

           if(isset($item->sale_value) && !empty($item->sale_value)){
                $item->profit = (($item->sale_value - $item->purchase_value) - ($item->value_commission ?? 0)) - $totalCost;
           }

        }

        array_map(function ($item) {
            $item->action = [];
            $item->purchase_date = !empty($item->purchase_date) ? date('d/m/Y', strtotime($item->purchase_date)) : "";
            $item->purchase_value = !empty($item->purchase_value) ? Util::maskMoney($item->purchase_value) : "";
            $item->sale_date = !empty($item->sale_date) ? date('d/m/Y', strtotime($item->sale_date)) : "";
            $item->sale_value = !empty($item->sale_value) ? Util::maskMoney($item->sale_value) : "";
            $item->profit = !empty($item->profit) ? Util::maskMoney($item->profit) : "";
            $item->action = [
                (object)[
                    'id' => $item->id,
                'icon' => 'fas fa-eye',
                'href' => URL . "{$this->route}/historyVehicle/{$item->id}",
                'title' => 'Vizualizar informações',
                'size' => 'sm',
                'color' => 'warning',
                ]
            ];

            return $item;
        }, $response->data);

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true
            ],
            'thead' => [
                    (object) [
                        'class' => 'text-center',
                        'text' => 'Placa',
                        'column' => (object) ['type' => 'text', 'link' => 'plate']
                    ],
                    (object) [
                        'class' => 'text-center',
                        'text' => 'Veículo',
                        'column' => (object) ['type' => 'text', 'link' => 'name']
                    ],
                    (object) [
                        'class' => 'text-center',
                        'text' => 'Data Compra',
                        'column' => (object) ['type' => 'text', 'link' => 'purchase_date']
                    ],
                    (object) [
                        'class' => $_SESSION['RR']->profile->access <= 10 ? 'text-center' : 'visibility: hidden',
                        'text' => 'Valor Compra',
                        'column' => (object) ['type' => 'text', 'link' => 'purchase_value']
                    ],
                    (object) [
                        'class' => 'text-center',
                        'text' => 'Data Venda',
                        'column' => (object) ['type' => 'text', 'link' => 'sale_date']
                    ],
                    (object) [
                        'class' => $_SESSION['RR']->profile->access <= 10 ? 'text-center' : 'visibility: hidden',
                        'text' => 'Valor Venda',
                        'column' => (object) ['type' => 'text', 'link' => 'sale_value']
                    ],
                    (object) [
                        'class' => $_SESSION['RR']->profile->access <= 10 ? 'text-center' : 'visibility: hidden',
                        'text' => 'Lucro',
                        'column' => (object) ['type' => 'text', 'link' => 'profit']
                    ],
                    (object) [
                        'style' => 'width: 100px',
                        'class' => 'text-center align-middle',
                        'text' => 'Ações',
                        'column' => (object) ['type' => 'button', 'link' => 'action']
                    ],
                ],
            'data' => $response->data
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function historyVehicle($itemId)
    {
        Secure::access_secretary(true);

        $item = $this->model->getItemById($itemId);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => $item->name,
            'caption' => $item->plate,
        ];

        $navTabs = self::navTabs($itemId);

        array_map(function ($i) {
            $i->vehicle_sales_value = !empty($i->vehicle_sales_value) ? Util::maskMoney($i->vehicle_sales_value) : Util::maskMoney(0);
        }, [$item]);

        $vehicleDoors = (new VehicleDoors())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleTypes = (new VehicleTypes())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleFuels = (new VehicleFuels())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleBrands = (new VehicleBrands())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleModels = (new VehicleModels())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleColors = (new VehicleColors())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleCategories = (new VehicleCategories())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;

        $typesNegotiations = (new TypesNegotiations())->getWithFiltersAllItems()->data;
        $forms_of_payment = (new FormOfPayment())->getWithFiltersAllItems()->data;
        $cost_center = (new CostCenter())->getWithFiltersAllItems()->data;
        $customers = (new Customer)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $purchasingBrokers = (new User)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $states = (new States())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $cities = (new Cities())->getWithFiltersAllItems([(object)['comlumns' => ['status' => (object)['comaprison' => 'EQUAL', 'value' => true]]]])->data;
        $total = 0;
        $count = 0;

        $vehicleObservations = (new VehicleObservations())->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => true],
                        'id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $itemId]
                    ]
                ]
            ]
        );

        $purchaseVehicle = (new VehiclePurchases())->getItemWithFilters(
            [
                (object)['columns' => [
                    'id_vehicle' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => $itemId
                    ]
                ]]
            ],
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'vehicles',
                    'columns' => ['id_purchase_request']
                ],
                (object)[
                    'table' => 'purchase_requests',
                    'columns' => ['id_customer' => ['customer_purchase'], 'id' => ['id_purchase']]
                ]
            ]
        );
        $id_brokersale_purchase = (new PurchaseRequests())->getSaleBrokerByPurchase($purchaseVehicle->id_purchase);

        $saleVehicle = (new VehiclesRequestSale())->getWithFiltersAllItems(
            [
                (object)['columns' => [
                    'id_vehicle' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => $itemId
                    ]
                ]]
            ],
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'sale_requests',
                    'columns' => ['id' => ['id_sale_r'], 'id_customer', 'id_sale_broker', 'value' => ['value_sale_req'], 'sale_date']
                ],
                (object)[
                    'table' => 'bill_receive',
                    'columns' => ['id_form_of_payment', 'id_cost_center']
                ],
                (object)[
                    'table' => 'bill_receive_installment',
                    'columns' => ['due_date', 'id' => ['id_installment'], 'value_installment']
                ]
            ]
        )->data;

        $vehicleAttachments = (new VehicleAttachments())->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_vehicle' => (object)[
                            'comparison' => 'EQUAL',
                            'value' => $itemId
                        ]
                    ]
                ]
            ])->data;

        array_map(function ($item) {
            if (!empty($item->updated_by)) {
                $item->user_name = (new User)->getItemWithFilters([(object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $item->updated_by]]]])->name;
            } else {
                $item->user_name = (new User)->getItemWithFilters([(object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $item->created_by]]]])->name;
            }
        }, $vehicleObservations->data);

        foreach($saleVehicle as $sale){

            if(!empty($sale->id_installment) && isset($sale->id_installment)){
                $count += 1;
            }

            if(!empty($sale->value_installment) && isset($sale->value_installment)){
                $total += $sale->value_installment;
            }

            $id_broker_sale = (new SaleRequests())->getSaleBrokerBySale($sale->id_sale_r);
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/vehicle.php';
        require APP . 'view/_templates/footer.php';
    }

    public function vehicleTransfer($itemId)
    {
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $item = $this->model->getItemById($itemId);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => $item->name,
            'caption' => $item->plate,
        ];

        $navTabs = self::navTabs($itemId);

        $saleVehicle = (new VehiclesRequestSale())->getWithFiltersAllItems(
            [
                (object)['columns' => [
                    'id_vehicle' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => $itemId
                    ]
                ]]
            ],
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'sale_requests',
                    'columns' => ['sale_date' => ['sale_date']]
                ],
                (object)[
                    'table' => 'vehicle_transfer_observation',
                    'columns' => ['observation' => ['observation'], 'created_at' => ['created_obs'], 'created_by' => ['user_obs'], 'id' => ['id_obs']]
                ]
            ]
        )->data;

        $users = (new User)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;

        foreach($users as $user)
        {
            foreach($saleVehicle as $sale){
                if($sale->user_obs == $user->id)
                {
                    $sale->created_by = $user->name;
                }
            };
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/vehicleTransfer.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handerTransfer($id_vehicle)
    {
        if(!empty($id_vehicle))
        {
            $arr = [
                'transferred' => $_POST['transferred'],
                'due_date_transfer' => $_POST['due_date_transfer'],
                'date_transfer' => $_POST['date_transfer']
            ];

            (new VehiclesRequestSale)->update($arr, 'id_vehicle ', $id_vehicle);

            $saleVehicle = (new VehiclesRequestSale())->getWithFiltersAllItems(
                [
                    (object)['columns' => [
                        'id_vehicle' => (object)[
                            'comparison' => 'EQUAL',
                            'value' => $id_vehicle
                        ]
                    ]]
                ]
            )->data;

            if(!empty($saleVehicle) && !empty( $_POST['observation']) && isset( $_POST['observation'])){
                $arrObs = [
                    'observation' => $_POST['observation'],
                    'created_at' => date('Y-m-d'),
                    'created_by' => $_SESSION['RR']->branch->current->id,
                    'id_vehicles_request_sale' => $saleVehicle[0]->id
                ];

                (new VehicleTransferObservation())->insert($arrObs);
            }
        }

        header('Location: '. URL . $this->route . "/vehicleTransfer/$id_vehicle" );
    }

    public function deleteObs($idObs, $id_vehicle){
        if(!empty($idObs)){
            (new VehicleTransferObservation())->update(['status' => 0], 'id', $idObs);
        }

        header('Location: '. URL . $this->route . "/vehicleTransfer/$id_vehicle" );
    }

    public function print(){
        $filters = [];
        $valueTotal = 0;

        if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
            array_push(
                $filters,
                (object) ['where' => "AND (
                    ucase(this->table.id) LIKE ucase('%". $_GET['pesquisa'] . "%') OR
                    ucase(this->table.plate) LIKE ucase('%". $_GET['pesquisa'] . "%') OR
                    ucase(this->table.name) LIKE ucase('%". $_GET['pesquisa'] . "%')
                )"]);
        }

        if(isset($_GET['data_de']) && !empty($_GET['data_de']))
        {
            if($_GET['data_tipo'] == 0)
            {
                array_push($filters, (object) ['where' => "AND purchase_requests.purchase_date >= '". $_GET['data_de'] . "'" ]);
            }else if($_GET['data_tipo'] == 1)
            {
                array_push($filters, (object) ['where' => "AND sale_requests.sale_date >= '". $_GET['data_de'] ."'" ]);
            }
        }

        if(isset($_GET['data_ate']) && !empty($_GET['data_ate']))
        {
            if($_GET['data_tipo'] == 0)
            {
                array_push($filters, (object) ['where' => "AND purchase_requests.purchase_date <= '". $_GET['data_ate'] . "'" ]);
            }else if($_GET['data_tipo'] == 1)
            {
                array_push($filters, (object) ['where' => "AND sale_requests.sale_date <= '". $_GET['data_ate'] . "'" ]);
            }
        }

        $response = $this->model->getWithFiltersAllItems(
            $filters,
            [
                (object) [
                    'columns' => ['*']
                ],
                (object) [
                    'table' => 'purchase_requests',
                    'columns' => ['value' => ['purchase_value'], 'purchase_date' => ['purchase_date'], 'id_customer' => ['purchase_customer'], 'id_bills_to_pay' => ['id_bills_to_pay']]
                ],
                (object) [
                    'table' => 'vehicles_request_sale',
                    'columns' => ['id_vehicle' => ['id_vehicle'], 'value_commission' => ['value_commission']]
                ],
                (object) [
                    'table' => 'sale_requests',
                    'columns' => ['sale_date' => ['sale_date'], 'value' => ['sale_value'], 'id_customer' => ['sale_customer'], 'id_bill_receive' => ['id_bill_receive'], 'status' => ['status_sale']]
                ]
            ],
            [
                'orderBy' => 'vehicles.id DESC'
            ]
        );

        foreach ($response->data as $item) {
            $item->profit = 0;
            $totalCost = 0;

           $vehicleCosts = (new VehicleCosts)->getCostsGroupByCustomer($item->id)->data;

           if(isset($vehicleCosts) && !empty($vehicleCosts)){
                foreach($vehicleCosts as $vehicle){
                    $totalCost += $vehicle->total;
                }
           }

           if(isset($item->sale_value) && !empty($item->sale_value)){
                $item->profit = (($item->sale_value - $item->purchase_value) - ($item->value_commission ?? 0)) - $totalCost;
           }

           $valueTotal +=  $item->profit;
        }

        array_map(function ($item) {
            $item->action = [];
            $item->purchase_date = !empty($item->purchase_date) ? date('d/m/Y', strtotime($item->purchase_date)) : "";
            $item->purchase_value = !empty($item->purchase_value) ? Util::maskMoney($item->purchase_value) : "";
            $item->sale_date = !empty($item->sale_date) ? date('d/m/Y', strtotime($item->sale_date)) : "";
            $item->sale_value = !empty($item->sale_value) ? Util::maskMoney($item->sale_value) : "";
            $item->profit = !empty($item->profit) ? Util::maskMoney($item->profit) : "";
            $item->action = [
                (object)[
                    'id' => $item->id,
                'icon' => 'fas fa-eye',
                'href' => URL . "{$this->route}/historyVehicle/{$item->id}",
                'title' => 'Vizualizar informações',
                'size' => 'sm',
                'color' => 'warning',
                ]
            ];

            return $item;
        }, $response->data);


        require APP . 'view/' . $this->dir . '/print.php';
    }
}
