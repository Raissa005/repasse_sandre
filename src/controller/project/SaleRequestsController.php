<?php

namespace RR\controller\project;

use RR\libs\Date;
use RR\libs\Util;
use RR\model\User;
use RR\model\Menu;
use RR\libs\Secure;
use RR\model\Branch;
use RR\model\Customer;
use RR\libs\Pagination;
use RR\model\BillReceive;
use RR\model\SaleRequests;
use RR\model\FormOfPayment;
use RR\libs\RecursiveCostCenter;
use RR\model\VehiclesRequestSale;
use RR\model\BillReceiveInstallment;

use function RR\Controller\redirect;

class SaleRequestsController extends FrontController
{
    public $dir;
    public $route;

    private $model;

    public function __construct()
    {
        $this->dir = 'sale-requests';
        $this->route = 'sale-requests';

        $this->model = new SaleRequests;

        parent::__construct($this->route);

        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/saleRequests.js");
    }

    private function navTabs(int $itemId)
    {
        $navTabs = [
            (object) ['text' => 'Dados Gerais', 'route' => URL . $this->route . (!$itemId ? '/addItem' : "/editItem/$itemId"), 'class' => $_GET['pg1'] == (!$itemId ? 'addItem' : 'editItem') ? 'active' : '']
        ];

        if ($itemId) {
            array_push($navTabs, (object) ['text' => 'Veículos', 'route' => URL . "{$this->route}/saleVehicles/$itemId", 'class' => $_GET['pg1'] == 'saleVehicles' ? 'active' : '']);

            $vehiclesRequestSale = (new VehiclesRequestSale)->getWithFiltersAllItems([(object) ['columns' => ['id_sale_request' => (object) ['comparison' => 'EQUAL', 'value' => $itemId]]]])->count;

            if (!empty($vehiclesRequestSale > 0 && Secure::access_admin())) {
                array_push($navTabs, (object) ['text' => 'Financeiro', 'route' => URL . "{$this->route}/saleFinancial/$itemId", 'class' => $_GET['pg1'] == 'saleFinancial' ? 'active' : '']);

                array_push($navTabs, (object) ['text' => 'Comissão', 'route' => URL . "{$this->route}/saleCommission/$itemId", 'class' => $_GET['pg1'] == 'saleCommission' ? 'active' : '']);
            }
        }

        return $navTabs;
    }

    public function index()
    {
        $this->page = (new Menu())->getMenuByRoute($this->route);
        Secure::individual_menu_access($this->page->id);

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedidos de Venda',
            'caption' => 'Listagem',
            'buttons' => [
                (object) [
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                    'class' => in_array($_SESSION['RR']->profile->id, [1, 2]) ? '' : 'disabled'
                ]
            ]
        ];

        $rows = 20;
        $filters = [];
        $page = Pagination::getPage();

        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        $filters[] = (object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => $_GET['status']]]];

        if (isset($_GET['name']) && !empty($_GET['name'])) {
            $_GET['name'] = preg_replace('/\s+/', '', $_GET['name']);

            $filters[] = (object) [
                'where' => "AND (
                    ucase(customer.name) LIKE ucase('%" . $_GET['name'] . "%') OR
                    ucase(customer.company_name) LIKE ucase('%" . $_GET['name'] . "%') OR
                    ucase(customer.fancy_name_company) LIKE ucase('%" . $_GET['name'] . "%') OR
                    ucase(sale_requests.id) LIKE ucase('%" . $_GET['name'] . "%') OR
                    ucase(vehicles.plate) LIKE ucase('%". $_GET['name'] . "%') OR
                    ucase(vehicles.plate) LIKE ucase('%". substr($_GET['name'], 0, 3) . '-' . substr($_GET['name'], 3) ."%')
                )"
            ];
        }

        if (isset($_GET['data_de']) && !empty($_GET['data_de']) && isset($_GET['data_ate']) && !empty($_GET['data_ate'])) {
            $filters[] = (object) [
                'table' => 'sale_requests',
                'columns' => [
                    'sale_date' => (object) [
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
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'customer',
                    'columns' => [
                        'name' => ['name'],
                        'company_name' => ['customer_company_name'],
                        'fancy_name_company' => ['customer_fancy_name_company']
                    ]
                ],
                (object)[
                    'table' => 'vehicles_request_sale',
                    'columns' => [
                        'id_vehicle' => ['id_vehicle']
                    ]
                ],
                (object) [
                    'table' => 'vehicles',
                    'columns' => [
                        'plate' => ['plate']
                    ]
                ]
            ],
            [
                'limit' => $rows,
                'page' => $page,
                'groupBy' => 'sale_requests.id',
                'orderBy' => 'sale_requests.id DESC'
            ]
        );

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object) [
                'id' => $item->id,
                'icon' => 'fas fa-pencil-alt',
                'href' => URL . "{$this->route}/editItem/{$item->id}",
                'title' => 'Editar',
                'size' => 'sm',
                'color' => 'primary',
                'class' => in_array($_SESSION['RR']->profile->id, [1, 2]) ? '' : 'disabled'
            ], (object) [
                    'id' => $item->id,
                    'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                    'title' => ($item->status ? 'Inativar' : 'Ativar'),
                    'size' => 'sm',
                    'color' => ($item->status ? 'danger' : 'success'),
                    'class' => in_array($_SESSION['RR']->profile->id, [1, 2]) ? '' : 'disabled',
                    'href' => URL . $this->route . ($item->status ? '/disableItem/' : '/enableItem/') . $item->id
                ]);

            if (!empty($item->customer_fancy_name_company)) {

                $item->customer_name = $item->customer_fancy_name_company;
            } else if (!empty($item->customer_company_name)) {

                $item->customer_name = $item->customer_company_name;
            } else {
                $item->customer_name = $item->name;
            }

            $item->sale_date = Date::date($item->sale_date);
            $item->value = !empty($item->value) ? Util::maskMoney($item->value) : Util::maskMoney(0);
            $item->user_name = (new User)->getItemById($item->updated_by ?? $item->created_by)->name;

            $item->status = (object) ['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];
        }, $response->data);

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true,
            ],
            'thead' => [
                (object) [
                    'style' => 'width: 50px',
                    'class' => 'text-center align-middle',
                    'text' => 'Cód.',
                    'column' => (object) ['type' => 'text', 'link' => 'id']
                ],
                (object) [
                    'style' => 'width: 100px',
                    'class' => 'text-center align-middle',
                    'text' => 'Data Pedido',
                    'column' => (object) ['type' => 'text', 'link' => 'sale_date']
                ],
                (object) [
                    'class' => 'align-middle',
                    'text' => 'Cliente / Comprador',
                    'column' => (object) ['type' => 'text', 'link' => 'customer_name']
                ],
                (object) [
                    'class' => 'align-middle',
                    'text' => 'Usuário',
                    'column' => (object) ['type' => 'text', 'link' => 'user_name']
                ],
                (object) [
                    'class' => 'text-center align-middle',
                    'text' => 'Valor',
                    'column' => (object) ['type' => 'text', 'link' => 'value']
                ],
                (object) [
                    'class' => 'text-center align-middle',
                    'text' => 'Status',
                    'column' => (object) ['type' => 'label', 'link' => 'status']
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

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        Secure::access_admin(true);

        if (!isset($itemId))
            $itemId = 0;

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Venda',
            'caption' => 'Adicionar',
        ];

        $navTabs = self::navTabs($itemId);
        $salesBrokers = (new User)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = [
            'sale_date' => $_POST['saleDate'],
            'created_at' => date('Y-m-d H:i:s'),
            'status' => $_POST['saleStatus'] ?? 0,
            'created_by' => $_SESSION['RR']->user->id,
            'id_customer' => $_POST['buyerCustomerId'],
            'id_sale_broker' => $_POST['saleBrokerId']
        ];

        $response = $this->model->insert($arrPost);

        $_SESSION['RR']->toast = (object) [
            'icon' => $response->error === true ? 'error' : 'success',
            'title' => $response->message
        ];

        redirect(!$response->error ? "{$this->route}/saleVehicles/$response->lastId" : "{$this->route}/addItem");
    }

    public function editItem($itemId)
    {
        Secure::access_admin(true);

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Venda',
            'caption' => 'Editar',
            'buttons' => []
        ];

        $navTabs = self::navTabs($itemId);

        $item = (new SaleRequests)->getItemById($itemId);
        $customer = (new Customer)->getCustomerById($item->id_customer);
        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
        $restrictDataValidation = $_SESSION['RR']->profile->access < 30 || $branch->restrict_owner_data == 0;
        $salesBrokers = (new User)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicles = (new VehiclesRequestSale)->getWithFiltersAllItems([(object) ['columns' => ['id_sale_request' => (object) ['comparison' => 'EQUAL', 'value' => $itemId]]]])->count;

        if (!empty($item->id_bill_receive) && $vehicles > 0 && Secure::access_admin()) {
            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'primary',
                    'text' => 'Imprimir',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . $this->route . "/vehicleInstallmentPrinting/{$itemId}"
                ]
            );
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem");

        $arrPost = [
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $_SESSION['RR']->user->id,
            'sale_date' => $_POST['saleDate'],
            'status' => $_POST['saleStatus'] ?? 0,
            'id_customer' => $_POST['buyerCustomerId'],
        ];

        if (Secure::access_admin() && !empty($_POST['saleBrokerId']))
            $arrPost['id_sale_broker'] = $_POST['saleBrokerId'];

        $response = $this->model->update($arrPost, "id", $itemId);

        $_SESSION['RR']->toast = (object) [
            'icon' => $response->error === true ? 'error' : 'success',
            'title' => $response->message
        ];

        redirect("{$this->route}/editItem/$itemId");
    }

    public function saleVehicles($itemId)
    {
        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Venda',
            'caption' => 'Veículos',
            'buttons' => []
        ];

        $navTabs = self::navTabs($itemId);

        $item = (new SaleRequests)->getItemById($itemId);
        if (!empty($item->value))
            $item->value = Util::maskMoney($item->value);

        $vehiclesRequestSale = (new VehiclesRequestSale)->getWithFiltersAllItems(
            [
                (object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]],
                (object) ['columns' => ['id_sale_request' => (object) ['comparison' => 'EQUAL', 'value' => $item->id]]]
            ],
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'vehicle_brands', 'columns' => ['name' => ['vehicle_brand_name']]],
                (object) ['table' => 'vehicle_models', 'columns' => ['name' => ['vehicle_model_name']]],
                (object) ['table' => 'vehicle_colors', 'columns' => ['name' => ['vehicle_color_name']]],
                (object) ['table' => 'vehicles', 'columns' => ['name' => ['name'], 'plate' => ['plate'], 'vehicle_sales_value' => ['vehicle_sales_value']]]
            ]
        );

        if ($vehiclesRequestSale->count > 0) {
            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'primary',
                    'text' => 'Imprimir',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . $this->route . "/vehiclePrinting/{$itemId}",
                ]

            );

            array_map(function ($vehicle) use (&$totalVehicleValue) {
                if (empty($totalVehicleValue))
                    $totalVehicleValue = 0;

                $totalVehicleValue += $vehicle->vehicle_sales_value;

                $vehicle->plate = !empty($vehicle->plate) ? $vehicle->plate : 'não informado';
                $vehicle->value = !empty($vehicle->value) ? Util::maskMoney($vehicle->value) : Util::maskMoney(0);
                $vehicle->vehicle_sales_value = !empty($vehicle->vehicle_sales_value) ? Util::maskMoney($vehicle->vehicle_sales_value) : Util::maskMoney(0);
            }, $vehiclesRequestSale->data);
        }

        if (!empty($totalVehicleValue))
            $totalVehicleValue = Util::maskMoney($totalVehicleValue);

        $blockBtnDelete = false;
        if (!empty($item->id_bill_receive)) {
            $billRceive = (new BillReceive)->getItemById($item->id_bill_receive);

            if ($billRceive)
                $blockBtnDelete = true;
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/vehicles.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function deleteVehiclesSale($itemId)
    {
        $vehiclesRequestSale = (new VehiclesRequestSale)->getItemWithFilters([(object) ['columns' => ['id_vehicle' => (object) ['value' => $itemId]]]]);

        if (!empty($vehiclesRequestSale)) {
            $arrPost = [
                'status' => false,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $_SESSION['RR']->user->id
            ];

            $response = (new VehiclesRequestSale)->update($arrPost, 'id', $vehiclesRequestSale->id);

            if (!$response->error) {
                $saleRequest = (new SaleRequests)->getItemById($vehiclesRequestSale->id_sale_request);

                (new SaleRequests)->update(['value' => ($saleRequest->value - $vehiclesRequestSale->value)], 'id', $saleRequest->id);
            }

            $_SESSION['RR']->toast = (object) [
                'icon' => $response->error === true ? 'error' : 'success',
                'title' => $response->message
            ];
        } else {
            $_SESSION['RR']->toast = (object) [
                'icon' => 'error',
                'title' => 'Item não encontrado!'
            ];
        }

        redirect("{$this->route}/saleVehicles/{$saleRequest->id}");
    }

    public function saleFinancial($itemId)
    {
        parent::addScript(URL . "js/" . JSVERSION . "/bill-receive/global.js");
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/financial.js");

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Venda',
            'caption' => 'Financeiro',
            'buttons' => []
        ];

        $navTabs = self::navTabs($itemId);

        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);

        $saleRequest = (new SaleRequests)->getItemById($itemId);

        array_map(function ($item) {
            $item->value = Util::maskMoney($item->value);
        }, [$saleRequest]);

        $readOnly = false;
        if (!empty($saleRequest->id_bill_receive)) {
            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'info',
                    'text' => 'Lançamento',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . "bill-receive/entry/{$saleRequest->id_bill_receive}",
                ]
            );

            $readOnly = true;
            $billReceive = (new BillReceive)->getItemById($saleRequest->id_bill_receive);

            array_map(function ($element) use (&$installments) {
                $installments = (new BillReceiveInstallment())->getWithFiltersAllItems(
                    [
                        (object) ['columns' => ['id_bill_receive' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                    ],
                    [
                        (object) ['columns' => ['*']],
                        (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                        (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                        (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                    ]
                );

                $element->numberOfInstallments = $installments->count;
                $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                    if ($item->status_payment == 1 || $item->status_payment == 2)
                        $accumulator += $item->value_installment;
                    return $accumulator;
                }, 0));
            }, [$billReceive]);

            $firstElementProcessed = false;
            array_map(function ($el) use (&$totalInstallments, &$firstElementProcessed, &$firstDueDate) {
                if (!$firstElementProcessed) {
                    $firstDueDate = $el->due_date;

                    $firstElementProcessed = true;
                }

                $el->text = "";
                switch ($el->status_payment) {
                    case '1':
                        if ($el->due_date < date("Y-m-d")) {
                            $el->bgtr = 'danger';
                            $el->label = 'Atrasado';
                            $el->text = 'text-red';
                        } else {
                            if ($el->due_date == date("Y-m-d")) {
                                $el->text = 'text-yellow';
                            }
                            $el->bgtr = 'warning';
                            $el->label = 'Aguard. Pagam.';
                        }
                        break;
                    case '2':
                        $el->bgtr = 'success';
                        $el->label = 'Pago';
                        break;
                    case '3':
                        $el->bgtr = 'default';
                        $el->label = 'Cancelado';
                        break;
                }

                if (empty($totalInstallments))
                    $totalInstallments = 0;

                $el->due_date = Date::date($el->due_date);
                $totalInstallments += $el->value_installment;
                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
                !empty($el->value_installment) ? $el->value_installment = Util::maskMoney($el->value_installment) : '';
                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            }, $installments->data);

            if ($installments->count > 0) {
                array_push(
                    $contentHeader->buttons,
                    (object) [
                        'color' => 'primary',
                        'text' => 'Imprimir',
                        'attr' => ['target' => '_blank'],
                        'href' => URL . $this->route . "/installmentPrinting/{$itemId}"
                    ]
                );
            }

            if ($totalInstallments)
                $totalInstallments = Util::maskMoney($totalInstallments);
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/financial.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function saleCommission($itemId){
        parent::addScript(URL . "js/" . JSVERSION . "/bill-receive/global.js");
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/financial.js");

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Venda',
            'caption' => 'Comissão',
            'buttons' => []
        ];

        $navTabs = self::navTabs($itemId);

        $commissionTotal = 0;
        $commission = 1;
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 2]);

        $saleRequest = (new SaleRequests)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id' => (object) [
                            'comparison' =>
                                'EQUAL',
                                'value' => $itemId
                        ]
                    ]
                ],
            ],
            [
                (object)[
                    'columns' => ['*']
                ],
                (object)[
                    'table' => 'vehicles_request_sale',
                    'columns' => ['id_vehicle', 'value_commission' => ['value_commission']]
                ],
                (object)[
                    'table' => 'bill_receive',
                    'columns' => ['id' => ['bill_receiv_id']]
                ],
                (object)[
                    'table' => 'bill_receive_installment',
                    'columns' => ['id_purchase_broker']
                ]
            ],
            [
                (object)[
                    'groupBy' => 'vehicles_request_sale.value_commission'
                ]
            ]
        )->data;

        foreach($saleRequest as $sale){
            $commissionTotal += (float) $sale->value_commission;
        }

        $purchasingBrokers = (new Customer())->getWithFiltersAllItems(
            [
                (object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]
            ])->data;

        $readOnly = false;

        if (!empty($saleRequest[0]->id_commission_receive)) {
            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'info',
                    'text' => 'Lançamento',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . "bill-receive/entry/{$saleRequest[0]->id_commission_receive}",
                ]
            );

            $readOnly = true;
            $billReceive = (new BillReceive)->getItemById($saleRequest[0]->id_commission_receive);

            array_map(function ($element) use (&$installments) {
                $installments = (new BillReceiveInstallment())->getWithFiltersAllItems(
                    [
                        (object) ['columns' => ['id_bill_receive' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                    ],
                    [
                        (object) ['columns' => ['*']],
                        (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                        (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                        (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                    ]
                );

                $element->numberOfInstallments = $installments->count;
                $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                    if ($item->status_payment == 1 || $item->status_payment == 2)
                        $accumulator += $item->value_installment;
                    return $accumulator;
                }, 0));
            }, [$billReceive]);

            $firstElementProcessed = false;
            array_map(function ($el) use (&$totalInstallments, &$firstElementProcessed, &$firstDueDate) {
                if (!$firstElementProcessed) {
                    $firstDueDate = $el->due_date;

                    $firstElementProcessed = true;
                }

                $el->text = "";
                switch ($el->status_payment) {
                    case '1':
                        if ($el->due_date < date("Y-m-d")) {
                            $el->bgtr = 'danger';
                            $el->label = 'Atrasado';
                            $el->text = 'text-red';
                        } else {
                            if ($el->due_date == date("Y-m-d")) {
                                $el->text = 'text-yellow';
                            }
                            $el->bgtr = 'warning';
                            $el->label = 'Aguard. Pagam.';
                        }
                        break;
                    case '2':
                        $el->bgtr = 'success';
                        $el->label = 'Pago';
                        break;
                    case '3':
                        $el->bgtr = 'default';
                        $el->label = 'Cancelado';
                        break;
                }

                if (empty($totalInstallments))
                    $totalInstallments = 0;

                $el->due_date = Date::date($el->due_date);

                $totalInstallments += $el->value_installment;

                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';

                !empty($el->value_installment) ? $el->value_installment = Util::maskMoney($el->value_installment) : '';

                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            }, $installments->data);

            if ($installments->count > 0) {
                array_push(
                    $contentHeader->buttons,
                    (object) [
                        'color' => 'primary',
                        'text' => 'Imprimir',
                        'attr' => ['target' => '_blank'],
                        'href' => URL . $this->route . "/commissionInstallmentPrinting/{$itemId}"
                    ]
                );
            }

            if ($totalInstallments)
                $totalInstallments = Util::maskMoney($totalInstallments);
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/commission.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function vehiclePrinting($itemId)
    {
        $item = (new SaleRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'users', 'columns' => ['name' => ['user_name']]],
                (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name']]]
            ]
        );

        array_map(function ($i) {
            $i->value = Util::maskMoney($i->value);
            $i->sale_date = Date::date($i->sale_date);
        }, [$item]);

        $vehiclesRequestSale = (new VehiclesRequestSale)->getWithFiltersAllItems(
            [
                (object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]],
                (object) ['columns' => ['id_sale_request' => (object) ['comparison' => 'EQUAL', 'value' => $item->id]]]
            ],
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'vehicle_brands', 'columns' => ['name' => ['vehicle_brand_name']]],
                (object) ['table' => 'vehicle_models', 'columns' => ['name' => ['vehicle_model_name']]],
                (object) ['table' => 'vehicle_colors', 'columns' => ['name' => ['vehicle_color_name']]],
                (object) ['table' => 'vehicles', 'columns' => ['name' => ['name'], 'plate' => ['plate'], 'vehicle_sales_value' => ['vehicle_sales_value']]]
            ]
        );

        array_map(function ($vehicle) use (&$totalVehicleValue) {
            if (empty($totalVehicleValue))
                $totalVehicleValue = 0;

            $totalVehicleValue += $vehicle->vehicle_sales_value;

            $vehicle->plate = !empty($vehicle->plate) ? $vehicle->plate : 'não informado';
            $vehicle->value = !empty($vehicle->value) ? Util::maskMoney($vehicle->value) : Util::maskMoney(0);
            $vehicle->vehicle_sales_value = !empty($vehicle->vehicle_sales_value) ? Util::maskMoney($vehicle->vehicle_sales_value) : Util::maskMoney(0);
        }, $vehiclesRequestSale->data);

        $totalVehicleValue = Util::maskMoney($totalVehicleValue);

        require APP . 'view/' . $this->dir . '/vehicle-printing.php';
    }

    public function installmentPrinting($itemId)
    {
        $item = (new SaleRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'users', 'columns' => ['name' => ['user_name']]],
                (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name']]]
            ]
        );

        array_map(function ($i) {
            $i->value = Util::maskMoney($i->value);
            $i->sale_date = Date::date($i->sale_date);
        }, [$item]);

        if (!empty($item->id_bill_receive)) {
            $billReceive = (new BillReceive)->getItemById($item->id_bill_receive);

            array_map(function ($element) use (&$installments) {
                $installments = (new BillReceiveInstallment())->getWithFiltersAllItems(
                    [
                        (object) ['columns' => ['id_bill_receive' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                    ],
                    [
                        (object) ['columns' => ['*']],
                        (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                        (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                        (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                    ]
                );

                $element->numberOfInstallments = $installments->count;
                $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                    if ($item->status_payment == 1 || $item->status_payment == 2)
                        $accumulator += $item->value_installment;
                    return $accumulator;
                }, 0));
            }, [$billReceive]);

            $firstElementProcessed = false;
            array_map(function ($el) use (&$totalInstallments, &$firstElementProcessed, &$firstDueDate) {
                if (!$firstElementProcessed) {
                    $firstDueDate = $el->due_date;

                    $firstElementProcessed = true;
                }

                $el->text = "";
                switch ($el->status_payment) {
                    case '1':
                        if ($el->due_date < date("Y-m-d")) {
                            $el->bgtr = 'danger';
                            $el->label = 'Atrasado';
                            $el->text = 'text-red';
                        } else {
                            if ($el->due_date == date("Y-m-d")) {
                                $el->text = 'text-yellow';
                            }
                            $el->bgtr = 'warning';
                            $el->label = 'Aguard. Pagam.';
                        }
                        break;
                    case '2':
                        $el->bgtr = 'success';
                        $el->label = 'Pago';
                        break;
                    case '3':
                        $el->bgtr = 'default';
                        $el->label = 'Cancelado';
                        break;
                }

                if (empty($totalInstallments))
                    $totalInstallments = 0;

                $el->due_date = Date::date($el->due_date);
                $totalInstallments += $el->value_installment;
                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
                !empty($el->value_installment) ? $el->value_installment = Util::maskMoney($el->value_installment) : '';
                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            }, $installments->data);

            if ($totalInstallments)
                $totalInstallments = Util::maskMoney($totalInstallments);
        }

        require APP . 'view/' . $this->dir . '/installment-printing.php';
    }

    public function vehicleInstallmentPrinting($itemId)
    {
        $item = (new SaleRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'users', 'columns' => ['name' => ['user_name']]],
                (object) [
                    'table' => 'customer',
                    'columns' => [
                        'name' => ['customer_name'],
                        'company_name' => ['customer_company_name'],
                        'fancy_name_company' => ['customer_fancy_name_company']
                    ]
                ]
            ]
        );

        array_map(function ($i) {
            if (!empty($i->customer_fancy_name_company)) {

                $i->customer_name = $i->customer_fancy_name_company;
            } else if (!empty($i->customer_company_name)) {

                $i->customer_name = $i->customer_company_name;
            } else {

                $i->customer_name = $i->name;
            }

            $i->value = Util::maskMoney($i->value);
            $i->sale_date = Date::date($i->sale_date);
        }, [$item]);

        $vehiclesRequestSale = (new VehiclesRequestSale)->getWithFiltersAllItems(
            [
                (object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]],
                (object) ['columns' => ['id_sale_request' => (object) ['comparison' => 'EQUAL', 'value' => $item->id]]]
            ],
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'vehicle_brands', 'columns' => ['name' => ['vehicle_brand_name']]],
                (object) ['table' => 'vehicle_models', 'columns' => ['name' => ['vehicle_model_name']]],
                (object) ['table' => 'vehicle_colors', 'columns' => ['name' => ['vehicle_color_name']]],
                (object) ['table' => 'vehicles', 'columns' => ['name' => ['name'], 'plate' => ['plate'], 'vehicle_sales_value' => ['vehicle_sales_value']]]
            ]
        );

        array_map(function ($vehicle) use (&$totalVehicleValue) {
            if (empty($totalVehicleValue))
                $totalVehicleValue = 0;

            $totalVehicleValue += $vehicle->vehicle_sales_value;

            $vehicle->plate = !empty($vehicle->plate) ? $vehicle->plate : 'não informado';
            $vehicle->value = !empty($vehicle->value) ? Util::maskMoney($vehicle->value) : Util::maskMoney(0);
            $vehicle->vehicle_sales_value = !empty($vehicle->vehicle_sales_value) ? Util::maskMoney($vehicle->vehicle_sales_value) : Util::maskMoney(0);
        }, $vehiclesRequestSale->data);

        $totalVehicleValue = Util::maskMoney($totalVehicleValue);

        if (!empty($item->id_bill_receive)) {
            $billReceive = (new BillReceive)->getItemById($item->id_bill_receive);

            array_map(function ($element) use (&$installments) {
                $installments = (new BillReceiveInstallment())->getWithFiltersAllItems(
                    [
                        (object) ['columns' => ['id_bill_receive' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                    ],
                    [
                        (object) ['columns' => ['*']],
                        (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                        (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                        (object) [
                            'table' => 'customer',
                            'columns' => [
                                'name' => ['customer_name'],
                                'company_name' => ['customer_company_name'],
                                'fancy_name_company' => ['customer_fancy_name_company']
                            ]
                        ]
                    ]
                );

                $element->numberOfInstallments = $installments->count;
                $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                    if ($item->status_payment == 1 || $item->status_payment == 2)
                        $accumulator += $item->value_installment;
                    return $accumulator;
                }, 0));
            }, [$billReceive]);

            $firstElementProcessed = false;
            array_map(function ($el) use (&$totalInstallments, &$firstElementProcessed, &$firstDueDate) {
                if (!$firstElementProcessed) {
                    $firstDueDate = $el->due_date;

                    $firstElementProcessed = true;
                }

                $el->text = "";
                switch ($el->status_payment) {
                    case '1':
                        if ($el->due_date < date("Y-m-d")) {
                            $el->bgtr = 'danger';
                            $el->label = 'Atrasado';
                            $el->text = 'text-red';
                        } else {
                            if ($el->due_date == date("Y-m-d")) {
                                $el->text = 'text-yellow';
                            }
                            $el->bgtr = 'warning';
                            $el->label = 'Aguard. Pagam.';
                        }
                        break;
                    case '2':
                        $el->bgtr = 'success';
                        $el->label = 'Pago';
                        break;
                    case '3':
                        $el->bgtr = 'default';
                        $el->label = 'Cancelado';
                        break;
                }

                if (empty($totalInstallments))
                    $totalInstallments = 0;

                $el->due_date = Date::date($el->due_date);
                $totalInstallments += $el->value_installment;
                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
                !empty($el->value_installment) ? $el->value_installment = Util::maskMoney($el->value_installment) : '';
                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            }, $installments->data);

            if ($totalInstallments)
                $totalInstallments = Util::maskMoney($totalInstallments);
        }

        require APP . 'view/' . $this->dir . '/vehicle-installment-printing.php';
    }

    public function commissionInstallmentPrinting($itemId)
    {
        $item = (new SaleRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'users', 'columns' => ['name' => ['user_name']]],
                (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name']]]
            ]
        );

        array_map(function ($i) {
            $i->value = Util::maskMoney($i->value);
            $i->sale_date = Date::date($i->sale_date);
        }, [$item]);

        if (!empty($item->id_commission_receive)) {
            $billReceive = (new BillReceive)->getItemById($item->id_commission_receive);

            array_map(function ($element) use (&$installments) {
                $installments = (new BillReceiveInstallment())->getWithFiltersAllItems(
                    [
                        (object) ['columns' => ['id_bill_receive' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                    ],
                    [
                        (object) ['columns' => ['*']],
                        (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                        (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                        (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                    ]
                );

                $element->numberOfInstallments = $installments->count;
                $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                    if ($item->status_payment == 1 || $item->status_payment == 2)
                        $accumulator += $item->value_installment;
                    return $accumulator;
                }, 0));
            }, [$billReceive]);

            $firstElementProcessed = false;
            array_map(function ($el) use (&$totalInstallments, &$firstElementProcessed, &$firstDueDate) {
                if (!$firstElementProcessed) {
                    $firstDueDate = $el->due_date;

                    $firstElementProcessed = true;
                }

                $el->text = "";
                switch ($el->status_payment) {
                    case '1':
                        if ($el->due_date < date("Y-m-d")) {
                            $el->bgtr = 'danger';
                            $el->label = 'Atrasado';
                            $el->text = 'text-red';
                        } else {
                            if ($el->due_date == date("Y-m-d")) {
                                $el->text = 'text-yellow';
                            }
                            $el->bgtr = 'warning';
                            $el->label = 'Aguard. Pagam.';
                        }
                        break;
                    case '2':
                        $el->bgtr = 'success';
                        $el->label = 'Pago';
                        break;
                    case '3':
                        $el->bgtr = 'default';
                        $el->label = 'Cancelado';
                        break;
                }

                if (empty($totalInstallments))
                    $totalInstallments = 0;

                $el->due_date = Date::date($el->due_date);
                $totalInstallments += $el->value_installment;
                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
                !empty($el->value_installment) ? $el->value_installment = Util::maskMoney($el->value_installment) : '';
                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            }, $installments->data);

            if ($totalInstallments)
                $totalInstallments = Util::maskMoney($totalInstallments);
        }

        require APP . 'view/' . $this->dir . '/installment-printing.php';
    }

    public function disableItem($id){
        $this->model->update([
            'status' => 0
        ], 'id', $id);

        redirect("{$this->route}/");
    }

    public function enableItem($id){
        $this->model->update([
            'status' => 1
        ], 'id', $id);

        redirect("{$this->route}/");
    }
}
