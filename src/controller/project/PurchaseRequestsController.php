<?php

namespace RR\controller\project;

use RR\libs\Date;
use RR\libs\Util;
use RR\model\User;
use RR\model\Menu;
use RR\libs\Secure;
use RR\model\Branch;
use RR\model\Cities;
use RR\model\States;
use RR\model\Customer;
use RR\model\Vehicles;
use RR\libs\Pagination;
use RR\model\BillsToPay;
use RR\model\VehicleDoors;
use RR\model\VehicleFuels;
use RR\model\VehicleTypes;
use RR\model\FormOfPayment;
use RR\model\VehicleBrands;
use RR\model\VehicleColors;
use RR\model\VehicleModels;
use RR\model\PurchaseRequests;
use RR\model\VehicleCategories;
use RR\libs\RecursiveCostCenter;
use RR\model\BillsToPayInstallment;

use function RR\Controller\redirect;

class PurchaseRequestsController extends FrontController
{
    public $dir;
    public $route;

    private $model;

    public function __construct()
    {
        $this->dir = 'purchase-requests';
        $this->route = 'purchase-requests';

        $this->model = new PurchaseRequests;

        parent::__construct($this->route);

        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/purchaseRequests.js");
    }

    private function navTabs(int $itemId)
    {
        $navTabs = [
            (object) ['text' => 'Dados Gerais', 'route' => URL . $this->route . (!$itemId ? '/addItem' : "/editItem/$itemId"), 'class' => $_GET['pg1'] == (!$itemId ? 'addItem' : 'editItem') ? 'active' : '']
        ];

        if ($itemId) {
            array_push($navTabs, (object) ['text' => 'Veículos', 'route' => URL . "{$this->route}/purchaseVehicles/$itemId", 'class' => $_GET['pg1'] == 'purchaseVehicles' ? 'active' : '']);

            $vehicles = (new Vehicles)->getWithFiltersAllItems([(object) ['columns' => ['id_purchase_request' => (object) ['comparison' => 'EQUAL', 'value' => $itemId]]]])->count;

            if ($vehicles > 0 && Secure::access_admin()) {
                array_push($navTabs, (object) ['text' => 'Financeiro', 'route' => URL . "{$this->route}/purchaseFinancial/$itemId", 'class' => $_GET['pg1'] == 'purchaseFinancial' ? 'active' : '']);

                array_push($navTabs, (object) ['text' => 'Comissão', 'route' => URL . "{$this->route}/purchaseCommission/$itemId", 'class' => $_GET['pg1'] == 'purchaseCommission' ? 'active' : '']);
            }
        }

        return $navTabs;
    }

    public function index()
    {
        $this->page = (new Menu())->getMenuByRoute($this->route);
        Secure::individual_menu_access($this->page->id);

        Secure::individual_menu_access(true);

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedidos de Compra',
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


        if (isset($_GET['data_de']) && !empty($_GET['data_de']) && isset($_GET['data_ate']) && !empty($_GET['data_ate'])) {
            $filters[] = (object) [
                'table' => 'purchase_requests',
                'columns' => [
                    'purchase_date' => (object) [
                        'comparison' => 'BETWEEN',
                        'value1' => $_GET['data_de'],
                        'value2' => $_GET['data_ate']
                    ]
                ]
            ];
        }

        if (isset($_GET['name']) && !empty($_GET['name'])) {
            $_GET['name'] = preg_replace('/\s+/', '', $_GET['name']);

            $busca = '%' . $_GET['name'] . '%';
            $buscaPlaca = '%' . substr($_GET['name'], 0, 3) . '-' . substr($_GET['name'], 3) . '%';

            $filters[] = (object) [
                'where' => "AND (
                    ucase(customer.name) LIKE ucase(:busca_1) OR
                    ucase(customer.company_name) LIKE ucase(:busca_2) OR
                    ucase(customer.fancy_name_company) LIKE ucase(:busca_3) OR
                    ucase(purchase_requests.id) LIKE ucase(:busca_4) OR
                    ucase(vehicles.plate) LIKE ucase(:busca_5) OR
                    ucase(vehicles.plate) LIKE ucase(:busca_placa)
                )",
                'parameters' => [
                    ':busca_1' => $busca,
                    ':busca_2' => $busca,
                    ':busca_3' => $busca,
                    ':busca_4' => $busca,
                    ':busca_5' => $busca,
                    ':busca_placa' => $buscaPlaca
                ]
            ];
        }

        $response = $this->model->getWithFiltersAllItems(
            $filters,
            [
                (object) [
                    'columns' => ['*']
                ],
                (object) [
                    'table' => 'customer',
                    'columns' => [
                        'name' => ['name'],
                        'company_name' => ['customer_company_name'],
                        'fancy_name_company' => ['customer_fancy_name_company']
                    ]
                    ],
                    (object)[
                        'table' => 'vehicles',
                        'columns' => [
                            'plate' => ['plate']
                        ]
                    ]
            ],
            [
                'limit' => $rows,
                'page' => $page,
                'orderBy' => 'purchase_requests.id DESC',
                'groupBy' => 'purchase_requests.id'
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
                    'class' => (in_array($_SESSION['RR']->profile->id, [1, 2]) ? '' : 'disabled'),
                    'href' => URL . $this->route . ($item->status ? '/disableItem/' : '/enableItem/') . $item->id
                ]);

            if (!empty($item->customer_fancy_name_company)) {

                $item->customer_name = $item->customer_fancy_name_company;
            } else if (!empty($item->customer_company_name)) {

                $item->customer_name = $item->customer_company_name;
            } else {

                $item->customer_name = $item->name;
            }

            $item->purchase_date = Date::date($item->purchase_date);
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
                    'column' => (object) ['type' => 'text', 'link' => 'purchase_date']
                ],
                (object) [
                    'class' => 'align-middle',
                    'text' => 'Cliente / Fornecedor',
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
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        Secure::access_admin(true);

        if (!isset($itemId))
            $itemId = 0;

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Compra',
            'caption' => 'Adicionar',
        ];

        $navTabs = self::navTabs($itemId);
        $purchasingBrokers = (new User)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::access_admin(true);

        Secure::check_post_method($this->route . "/addItem");

        $arrPost = [
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['RR']->user->id,
            'purchase_date' => $_POST['purchaseDate'],
            'status' => $_POST['purchaseStatus'] ?? 0,
            'id_customer' => $_POST['sellerCustomerId'],
            'id_purchase_broker' => $_POST['purchaseBrokerId'] ?? ''
        ];

        $response = $this->model->insert($arrPost);

        $_SESSION['RR']->toast = (object) [
            'icon' => $response->error === true ? 'error' : 'success',
            'title' => $response->message
        ];

        redirect(!$response->error ? "{$this->route}/purchaseVehicles/$response->lastId" : "{$this->route}/addItem");
    }

    public function editItem($itemId)
    {
        Secure::access_admin(true);

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Compra',
            'caption' => 'Editar',
            'buttons' => []
        ];

        $navTabs = self::navTabs($itemId);

        $item = (new PurchaseRequests)->getItemById($itemId);
        $customer = (new Customer)->getCustomerById($item->id_customer);
        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
        $restrictDataValidation = $_SESSION['RR']->profile->access < 30 || $branch->restrict_owner_data == 0;
        $purchasingBrokers = (new User)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicles = (new Vehicles)->getWithFiltersAllItems([(object) ['columns' => ['id_purchase_request' => (object) ['comparison' => 'EQUAL', 'value' => $itemId]]]])->count;

        if ($item->id_bills_to_pay && $vehicles > 0 && Secure::access_admin()) {
            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'primary',
                    'text' => 'Imprimir',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . $this->route . "/vehicleInstallmentPrinting/{$itemId}",
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
        Secure::access_admin(true);

        Secure::check_post_method($this->route . "/editItem");

        $arrPost = [
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $_SESSION['RR']->user->id,
            'purchase_date' => $_POST['purchaseDate'],
            'status' => $_POST['purchaseStatus'] ?? 0,
            'id_customer' => $_POST['sellerCustomerId'] ?? ''
        ];

        $response = $this->model->update($arrPost, "id", $itemId);

        $_SESSION['RR']->toast = (object) [
            'icon' => $response->error === true ? 'error' : 'success',
            'title' => $response->message
        ];

        redirect(!$response->error ? "{$this->route}/purchaseVehicles/$itemId" : "{$this->route}/editItem/$itemId");
    }

    public function purchaseVehicles($itemId)
    {
        Secure::access_admin(true);

        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        parent::addScript(URL . "js/" . JSVERSION . "/vehicles/vehicles.js");

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Compra',
            'caption' => 'Veículos',
            'buttons' => []
        ];

        $navTabs = self::navTabs($itemId);

        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);
        $cityBranch = (new Cities)->getItemById($branch->id_city);
        $cities = (new Branch)->getCitiesByState($cityBranch->uf);

        $states = (new States)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleDoors = (new VehicleDoors)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleTypes = (new VehicleTypes)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleFuels = (new VehicleFuels)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleBrands = (new VehicleBrands)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleModels = (new VehicleModels)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleColors = (new VehicleColors)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleCategories = (new VehicleCategories)->getWithFiltersAllItems([(object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]])->data;

        $vehiclesPurchased = (new Vehicles)->getWithFiltersAllItems(
            [
                (object) ['columns' => ['id_purchase_request' => (object) ['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'vehicle_brands', 'columns' => ['name' => ['vehicle_brands_name']]],
                (object) ['table' => 'vehicle_models', 'columns' => ['name' => ['vehicle_models_name']]],
                (object) ['table' => 'vehicle_colors', 'columns' => ['name' => ['vehicle_colors_name']]],
                (object) ['table' => 'vehicle_purchases', 'columns' => ['purchase_value' => ['purchase_value'], 'value_commission']]
            ]
        );

        if ($vehiclesPurchased->count > 0) {
            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'primary',
                    'text' => 'Imprimir',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . $this->route . "/vehiclePrinting/{$itemId}",
                ]
            );

            $totalPurchase = 0;
            array_map(function ($vehicle) use (&$totalPurchase) {
                $totalPurchase += $vehicle->purchase_value;
                $vehicle->purchase_value = !empty($vehicle->purchase_value) ? Util::maskMoney($vehicle->purchase_value) : Util::maskMoney(0);
                $vehicle->vehicle_sales_value = !empty($vehicle->vehicle_sales_value) ? Util::maskMoney($vehicle->vehicle_sales_value) : Util::maskMoney(0);
            }, $vehiclesPurchased->data);
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/vehicles.php';
        require APP . 'view/vehicles/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function deleteVehiclesPurchased($itemId)
    {
        Secure::access_admin(true);

        if (!empty($itemId)) {
            $vehicle = (new Vehicles)->getItemById(
                $itemId,
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'vehicle_purchases', 'columns' => ['purchase_value' => ['purchase_value']]]
                ]
            );

            $purchase = (new PurchaseRequests)->getItemById($vehicle->id_purchase_request);

            $response = (new Vehicles)->update(['id_purchase_request' => NULL], 'id', $vehicle->id);

            if (!$response->error) {
                (new PurchaseRequests)->update(['value' => ($purchase->value - $vehicle->purchase_value)], 'id', $purchase->id);
            }

            $_SESSION['RR']->toast = (object) [
                'icon' => $response->error === true ? 'error' : 'success',
                'title' => $response->message
            ];

            redirect("{$this->route}/purchaseVehicles/{$vehicle->id_purchase_request}");
        }
    }

    public function purchaseFinancial($itemId)
    {
        Secure::access_admin(true);

        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/financial.js");
        $this->addScript(URL . "js/" . JSVERSION . "/bills-to-pay/installments.js");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Compra',
            'caption' => 'Financeiro',
            'buttons' => []
        ];

        $navTabs = self::navTabs($itemId);

        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);

        $purchase = (new PurchaseRequests)->getItemById($itemId);

        array_map(function ($item) {
            $item->value = Util::maskMoney($item->value);
        }, [$purchase]);

        $readOnly = false;
        if (!empty($purchase->id_bills_to_pay)) {
            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'info',
                    'text' => 'Lançamento',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . "bills-to-pay/entry/{$purchase->id_bills_to_pay}",
                ]
            );

            $readOnly = true;
            $billsToPay = (new BillsToPay)->getItemById($purchase->id_bills_to_pay);

            array_map(function ($element) use (&$installments) {
                $installments = (new BillsToPayInstallment())->getWithFiltersAllItems(
                    [
                        (object) ['columns' => ['id_bills_to_pay' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                    ],
                    [
                        (object) ['columns' => ['*']],
                        (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                        (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                        (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                    ]
                );

                $element->numberOfInstallments = $installments->count;
                $element->competence = Date::year_month($element->competence);
                $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                    if ($item->status_payment == 1 || $item->status_payment == 2)
                        $accumulator += $item->value_of_installments;
                    return $accumulator;
                }, 0));
            }, [$billsToPay]);

            array_map(function ($el) use (&$totalInstallments) {
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
                $totalInstallments += $el->value_of_installments;
                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
                !empty($el->value_of_installments) ? $el->value_of_installments = Util::maskMoney($el->value_of_installments) : '';
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

    public function purchaseCommission($itemId){
        Secure::access_admin(true);

        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/financial.js");
        $this->addScript(URL . "js/" . JSVERSION . "/bills-to-pay/installments.js");
        parent::addScript(URL . "js/" . JSVERSION . "/recursive-cost-center/tree.js");

        $contentHeader = (object) [
            'route' => URL . $this->route,
            'title' => 'Pedido de Compra',
            'caption' => 'Comissão',
            'buttons' => []
        ];

        $purchasingBrokers = (new Customer())->getWithFiltersAllItems(
            [
                (object) ['columns' => ['status' => (object) ['comparison' => 'EQUAL', 'value' => true]]]
            ]
        )->data;

        $purchase = (new PurchaseRequests)->getWithFiltersAllItems(
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
                (object) ['columns' => ['*'] ],
                (object)[
                    'table' => 'vehicles',
                    'columns' => [
                        'plate' => ['plate']
                    ]
                ],
                (object)[
                    'table' => 'vehicle_purchases',
                    'columns' => [
                        'value_commission' => ['value_commission']
                    ]
                ]
            ],
            [
                'groupBy' => 'value_commission'
            ]
        )->data;

        $costCenters = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $formOfPayments = (new FormOfPayment())->getAndFilterAllItem(['status' => true], 0)->data;
        $navTabs = self::navTabs($itemId);
        $totalCommission = 0;
        $readOnly = false;
        $commission = 0;

        foreach($purchase as $value)
        {
            $totalCommission +=  (float)$value->value_commission;
        }

        if (!empty($purchase[0]->id_commission_to_pay))
        {
            $commission = 1;

            array_push(
                $contentHeader->buttons,
                (object) [
                    'color' => 'info',
                    'text' => 'Lançamento',
                    'attr' => ['target' => '_blank'],
                    'href' => URL . "bills-to-pay/entry/{$purchase[0]->id_commission_to_pay}",
                ]
            );

            $readOnly = true;
            $billsToPay = (new BillsToPay)->getItemById($purchase[0]->id_commission_to_pay);

            array_map(function ($element) use (&$installments) {
                $installments = (new BillsToPayInstallment())->getWithFiltersAllItems(
                    [
                        (object) ['columns' => ['id_bills_to_pay' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                    ],
                    [
                        (object) ['columns' => ['*']],
                        (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                        (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                        (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                    ]
                );

                $element->numberOfInstallments = $installments->count;
                $element->competence = Date::year_month($element->competence);
                $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                    if ($item->status_payment == 1 || $item->status_payment == 2)
                        $accumulator += $item->value_of_installments;
                    return $accumulator;
                }, 0));
            }, [$billsToPay]);

            array_map(function ($el) use (&$totalInstallments) {
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
                $totalInstallments += $el->value_of_installments;

                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';

                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;

                !empty($el->value_of_installments) ? $el->value_of_installments = Util::maskMoney($el->value_of_installments) : '';
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
        $this->page = (new Menu())->getMenuByRoute($this->route);
        Secure::individual_menu_access($this->page->id);

        $item = (new PurchaseRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'users',
                    'columns' => ['name' => ['user_name']]
                ],
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
            }

            $i->value = Util::maskMoney($i->value);
            $i->purchase_date = Date::date($i->purchase_date);
        }, [$item]);

        $vehiclesPurchased = (new Vehicles)->getWithFiltersAllItems(
            [
                (object) ['columns' => ['id_purchase_request' => (object) ['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'vehicle_brands',
                    'columns' => ['name' => ['vehicle_brand_name']]
                ],
                (object) [
                    'table' => 'vehicle_models',
                    'columns' => ['name' => ['vehicle_model_name']]
                ],
                (object) [
                    'table' => 'vehicle_colors',
                    'columns' => ['name' => ['vehicle_color_name']]
                ],
                (object) [
                    'table' => 'vehicle_purchases',
                    'columns' => ['purchase_value' => ['purchase_value']]
                ]
            ]
        );

        array_map(function ($vehicle) {
            $vehicle->purchase_value = !empty($vehicle->purchase_value) ? Util::maskMoney($vehicle->purchase_value) : Util::maskMoney(0);
        }, $vehiclesPurchased->data);

        require APP . 'view/' . $this->dir . '/vehicle-printing.php';
    }

    public function installmentPrinting($itemId)
    {
        Secure::access_admin(true);

        $this->page = (new Menu())->getMenuByRoute($this->route);
        Secure::individual_menu_access($this->page->id);

        $item = (new PurchaseRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'users',
                    'columns' => ['name' => ['user_name']]
                ],
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
            }

            $i->value = Util::maskMoney($i->value);
            $i->purchase_date = Date::date($i->purchase_date);
        }, [$item]);

        $billsToPay = (new BillsToPay)->getItemById($item->id_bills_to_pay);

        array_map(function ($element) use (&$installments) {
            $installments = (new BillsToPayInstallment())->getWithFiltersAllItems(
                [
                    (object) ['columns' => ['id_bills_to_pay' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                ],
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                    (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                    (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                ]
            );

            $element->numberOfInstallments = $installments->count;
            $element->competence = Date::year_month($element->competence);
            $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                if ($item->status_payment == 1 || $item->status_payment == 2)
                    $accumulator += $item->value_of_installments;
                return $accumulator;
            }, 0));
        }, [$billsToPay]);

        array_map(function ($el) use (&$totalInstallments) {
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
            $totalInstallments += $el->value_of_installments;
            !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
            $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            !empty($el->value_of_installments) ? $el->value_of_installments = Util::maskMoney($el->value_of_installments) : '';
        }, $installments->data);

        if ($totalInstallments)
            $totalInstallments = Util::maskMoney($totalInstallments);

        require APP . 'view/' . $this->dir . '/installment-printing.php';
    }

    public function vehicleInstallmentPrinting($itemId)
    {
        Secure::access_admin(true);

        $this->page = (new Menu())->getMenuByRoute($this->route);
        Secure::individual_menu_access($this->page->id);

        $item = (new PurchaseRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'users',
                    'columns' => ['name' => ['user_name']]
                ],
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
            }

            $i->value = Util::maskMoney($i->value);
            $i->purchase_date = Date::date($i->purchase_date);
        }, [$item]);

        $vehiclesPurchased = (new Vehicles)->getWithFiltersAllItems(
            [
                (object) ['columns' => ['id_purchase_request' => (object) ['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'vehicle_brands',
                    'columns' => ['name' => ['vehicle_brand_name']]
                ],
                (object) [
                    'table' => 'vehicle_models',
                    'columns' => ['name' => ['vehicle_model_name']]
                ],
                (object) [
                    'table' => 'vehicle_colors',
                    'columns' => ['name' => ['vehicle_color_name']]
                ],
                (object) [
                    'table' => 'vehicle_purchases',
                    'columns' => ['purchase_value' => ['purchase_value']]
                ]
            ]
        );

        array_map(function ($vehicle) {
            $vehicle->purchase_value = !empty($vehicle->purchase_value) ? Util::maskMoney($vehicle->purchase_value) : Util::maskMoney(0);
        }, $vehiclesPurchased->data);

        $billsToPay = (new BillsToPay)->getItemById($item->id_bills_to_pay);

        array_map(function ($element) use (&$installments) {
            $installments = (new BillsToPayInstallment())->getWithFiltersAllItems(
                [
                    (object) ['columns' => ['id_bills_to_pay' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                ],
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                    (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                    (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                ]
            );

            $element->numberOfInstallments = $installments->count;
            $element->competence = Date::year_month($element->competence);
            $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                if ($item->status_payment == 1 || $item->status_payment == 2)
                    $accumulator += $item->value_of_installments;
                return $accumulator;
            }, 0));
        }, [$billsToPay]);

        array_map(function ($el) use (&$totalInstallments) {
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
            $totalInstallments += $el->value_of_installments;
            !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
            $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            !empty($el->value_of_installments) ? $el->value_of_installments = Util::maskMoney($el->value_of_installments) : '';
        }, $installments->data);

        if ($totalInstallments)
            $totalInstallments = Util::maskMoney($totalInstallments);

        require APP . 'view/' . $this->dir . '/vehicle-installment-printing.php';
    }

    public function commissionInstallmentPrinting($itemId){
        Secure::access_admin(true);

        $this->page = (new Menu())->getMenuByRoute($this->route);
        Secure::individual_menu_access($this->page->id);

        $item = (new PurchaseRequests)->getItemById(
            $itemId,
            [
                (object) ['columns' => ['*']],
                (object) [
                    'table' => 'users',
                    'columns' => ['name' => ['user_name']]
                ],
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
            $i->customer_name = $i->customer_fancy_name_company ?? $i->customer_company_name ?? $i->customer_name;
            $i->value = Util::maskMoney($i->value);
            $i->purchase_date = Date::date($i->purchase_date);
        }, [$item]);

        $billsToPay = (new BillsToPay)->getItemById($item->id_commission_to_pay);

        array_map(function ($element) use (&$installments) {
            $installments = (new BillsToPayInstallment())->getWithFiltersAllItems(
                [
                    (object) ['columns' => ['id_bills_to_pay' => (object) ['comparison' => 'EQUAL', 'value' => $element->id]]]
                ],
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                    (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                    (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                ]
            );

            $element->numberOfInstallments = $installments->count;
            $element->competence = Date::year_month($element->competence);
            $element->value = Util::maskMoney(array_reduce($installments->data, function ($accumulator, $item) {
                if ($item->status_payment == 1 || $item->status_payment == 2)
                    $accumulator += $item->value_of_installments;
                return $accumulator;
            }, 0));
        }, [$billsToPay]);

        array_map(function ($el) use (&$totalInstallments) {
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
            $totalInstallments += $el->value_of_installments;
            !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
            $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
            !empty($el->value_of_installments) ? $el->value_of_installments = Util::maskMoney($el->value_of_installments) : '';
        }, $installments->data);

        if ($totalInstallments)
            $totalInstallments = Util::maskMoney($totalInstallments);

        require APP . 'view/' . $this->dir . '/installment-printing.php';
    }

    public function disableItem($id){
        Secure::access_admin(true);

        $this->model->update([
            'status' => 0
        ], 'id', $id);

        redirect("{$this->route}/");
    }

    public function enableItem($id){
        Secure::access_admin(true);

        $this->model->update([
            'status' => 1
        ], 'id', $id);

        redirect("{$this->route}/");
    }
}
