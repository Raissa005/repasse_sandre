<?php

namespace RR\controller\project;

use RR\libs\Pagination;
use RR\libs\RecursiveCostCenter;
use RR\libs\Secure;
use RR\libs\ShowPage;
use RR\libs\TableDefault;
use RR\libs\Util;
use RR\model\Construction;
use RR\model\ConstructionCostCenters;
use RR\model\ConstructionProperties;
use RR\model\PaymentStatus;
use RR\model\Property;

use function RR\Controller\redirect;

class ConstructionController extends FrontController
{
    public $route = "construction";
    private $model;

    public function __construct()
    {
        parent::__construct($this->route);
        $this->model = new Construction();
        Secure::redirectFunction(!($this->branch->type == 2));
    }

    private function navTabs($itemId): array
    {
        $nav_tabs = [
            (object)['text' => 'Dados Gerais', 'route' => URL . "{$this->route}/edit/$itemId", 'class' => ($_GET['pg1'] == 'edit' ? 'active' : '')],
            (object)['text' => 'Relatório', 'route' => URL . "{$this->route}/report/$itemId", 'class' => ($_GET['pg1'] == 'report' ? 'active' : '')],
        ];

        return $nav_tabs;
    }

    public function index(): void
    {
        $columns = [
            (object)['columns' => ['*']],
            (object)[
                'table' => 'cost_center',
                'columns' => ['name']
            ]
        ];

        $response = $this->model->getItemsWithFilters();

        array_map(function ($item) {
            $item->cost_center_name;
            $status_bg = '';
            $status_text = '';

            $item->action = [
                (object)[
                    'id' => $item->id,
                    'title' => 'Editar',
                    'href' => URL . "{$this->route}/edit/$item->id",
                    'icon' => 'fas fa-pencil-alt',
                    'size' => 'sm',
                    'bg' => 'primary',
                    'styles' => ['width' => '35px'],
                    'attrs' => [],
                ],
                (object)[
                    'id' => $item->id,
                    'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                    'title' => ($item->status ? 'Inativar' : 'Ativar'),
                    'size' => 'sm',
                    'bg' => ($item->status ? 'danger' : 'success'),
                    'class' => ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                    'attrs' => [
                        'sendTo' => $this->route . '/handleStatus/',
                    ]
                ]
            ];

            switch ($item->status) {
                case '1':
                    $status_bg = 'success';
                    $status_text = 'Ativo';
                    break;

                case '0':
                    $status_bg = 'danger';
                    $status_text = 'Inativo';
                    break;
            }

            $item->status = (object)['bg' => $status_bg, 'text' => $status_text];
        }, $response->data);

        $content_header = (object)[
            'route' => URL . $this->route,
            'title' => 'Obras',
            'subtitle' => 'Listagem',
            'buttons' => [
                (object)[
                    'bg' => 'info',
                    'size' => 'sm',
                    'text' => 'Adicionar',
                    'href' => URL . "construction/add",
                ]
            ],
        ];

        $thead = [
            (object)[
                'text' => 'Cód.',
                'class' => 'align-middle text-center',
                'styles' => ['width' => '4,3rem'],
                'link' => 'id',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Nome',
                'class' => 'align-middle text-center',
                'link' => 'name',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Centro de Custo',
                'class' => 'align-middle text-center',
                'link' => 'cost_center_name',
                'field_type' => 'text'
            ],
            (object)[
                'text' => 'Status',
                'class' => 'align-middle text-center',
                'link' => 'status',
                'field_type' => 'badge'
            ],
            (object)[
                'text' => 'Ações',
                'class' => 'align-middle text-center',
                'link' => 'action',
                'field_type' => 'action'
            ],
        ];

        $rows = 20;
        $page = Pagination::getPage();

        $listing_card = (object)[
            'table' => (new TableDefault($thead, $response->data))->model01(),
            'pagination' => (new Pagination)->pages($response->count, $rows),
        ];

        $showItems = (new Pagination())->listItemsOnPage($response->count, $listing_card->pagination, $rows);

        require APP . "view/_templates/header.php";
        require APP . "view/" . $this->route . "/index.php";
        require APP . "view/_templates/footer.php";
    }

    public function handleStatus(int $itemId): void
    {
        $response = $this->model->submitStatus($itemId);

        $_SESSION['RR']->toast = (object)[
            'icon' => !$response->error ? 'success' : 'error',
            'title' => $response->message
        ];

        redirect($this->route);
    }

    public function add(): void
    {
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->route . "/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->route . "/construction.js");

        $content_header = (object)[
            'title' => "Obras",
            'subtitle' => "Adicionar",
            'buttons' => []
        ];

        $cost_centers = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);

        require APP . "view/_templates/header.php";
        require APP . "view/" . $this->route . "/add.php";
        require APP . "view/_templates/footer.php";
    }

    public function handleAdd(): void
    {
        Secure::check_post_method($this->route);

        $response = $this->model->submitAdd();

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error ? 'error' : 'success',
            'title' => $response->message,
        ];

        redirect($this->route . (!$response->error ? "/edit/$response->lastId" : "/"));
    }

    public function edit(int $itemId): void
    {
        parent::addStyle(URL . "css/" . CSSVERSION . "/cost-center/global.css");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->route . "/tree.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->route . "/construction.js");

        $item = $this->model->getItemById($itemId);

        $content_header = (object)[
            'title' => $item->name,
            'subtitle' => "Editar",
            'buttons' => []
        ];

        $nav_tabs = Self::navTabs($itemId);

        $properties = (new ConstructionProperties)->getWithFiltersAllItems([(object)['columns' => ['id_construction' => (object)['value' => $itemId]]]]);
        array_map(function ($item) {
            $item->frt_value = Util::maskMoney($item->frt_value);
            $image = (new Property)->getTheFirstImageOfTheProperty($item->id_property);
            $item->urlImageLg = URL . (isset($image->id) ? "img/products_imgs/{$item->id_property}/{$image->id}md.{$image->extension}" : "img/products_imgs/default/img-layout-default.png");
            $item->property = (new Property)->getItemById($item->id_property);
        }, $properties->data);

        (string)$properties_value = implode(',', array_map(function ($item) {
            return $item->id_property;
        }, $properties->data));

        $cc_not = (new ConstructionCostCenters)->getWithFiltersAllItems([(object)['columns' => ['id_construction' => (object)['value' => $itemId], 'type' => (object)['value' => 1]]]]);
        (array)$cc_not = array_map(function ($item) {
            return $item->id_cost_center;
        }, $cc_not->data);
        (string)$cc_not_value = implode(',', $cc_not);

        $cc_ignored_sum = (new ConstructionCostCenters)->getWithFiltersAllItems([(object)['columns' => ['id_construction' => (object)['value' => $itemId], 'type' => (object)['value' => 2]]]]);
        (array)$cc_ignored_sum = array_map(function ($item) {
            return $item->id_cost_center;
        }, $cc_ignored_sum->data);
        (string)$cc_ignored = implode(',', $cc_ignored_sum);

        $cost_centers = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1, 'id_type' => 1]);
        $cost_centers_to_filter = (new RecursiveCostCenter())->recursiveTree($item->id_cost_center, ['status' => 1, 'id_type' => 1]);

        usort($properties->data, function ($a, $b) {
            return strcmp($a->property->name, $b->property->name);
        });

        require APP . "view/_templates/header.php";
        require APP . "view/" . $this->route . "/edit.php";
        require APP . "view/_templates/footer.php";
    }

    public function handleEdit(int $itemId): void
    {
        Secure::check_post_method($this->route);

        $response = $this->model->submitEdit($itemId);

        $_SESSION['RR']->toast = (object)[
            'icon' => $response->error ? 'error' : 'success',
            'title' => $response->message,
        ];

        redirect($this->route . "/edit/" . $itemId);
    }

    public function report(int $itemId): void
    {
        $get = isset(explode('?', $_SERVER["REQUEST_URI"])[1]) ? explode('?', $_SERVER["REQUEST_URI"])[1] : '';
        $item = $this->model->getItemById($itemId);
        (object)$nav_tabs = Self::navTabs($itemId);
        $content_header = (object)[
            'title' => $item->name,
            'subtitle' => "Relatórios",
            'buttons' => [
                (object)[
                    'text' => 'Gerar Relatório',
                    'bg' => 'warning',
                    'size' => 'sm',
                    'target' => '_blank',
                    'href' => URL . $this->route . '/printReport/' . $itemId . '?' . $get,
                    'id' => 'printReport'
                ]
            ]
        ];

        $properties = (new ConstructionProperties)->getWithFiltersAllItems([(object)['columns' => ['id_construction' => (object)['value' => $itemId]]]]);
        $expenses = (new Construction)->expenses($item->id, $item->id_cost_center);
        $cost_centers_key = array_keys($expenses['sum_cc']);

        $sale_expenses_key = [];
        $sale_payments_key = [];
        $payment_properties = [];
        $payment_vehicles = [];
        $payment_forms = [];
        $total_profit = 0;
        $total_area = 0;

        $exchange = "";
        $totalAreaWithoutExchange = 0;

        array_map(function ($product) use (&$sale_expenses_key, &$sale_payments_key, &$total_area, &$payment_properties, &$payment_vehicles, &$payment_forms, &$exchange, &$totalAreaWithoutExchange) {
            $product->expenses = $this->model->sale_expenses($product);
            $product->payments = $this->model->sale_payments($product);
            $product->property = (new Property)->getItemById($product->id_property);

            if (!empty($product->exchange)) {
                $totalAreaWithoutExchange += $product->property->total_area;

                $exchange = true;
            }

            $total_area = $total_area + $product->property->total_area;

            array_push($sale_expenses_key, $product->expenses->keys);
            array_push($sale_payments_key, $product->payments->keys);
            array_push($payment_properties, array_keys((array)$product->payments->prices->properties));
            array_push($payment_vehicles, array_keys((array)$product->payments->prices->vehicles));
            array_push($payment_forms, array_keys((array)$product->payments->prices->form_of_payments));
        }, $properties->data);

        array_map(function ($product) use ($expenses, $total_area, $properties, &$total_profit, &$totalAreaWithoutExchange) {
            $product->total_per_house = ($total_area > 0 ? ($expenses['Total Despesas Construção'] / $total_area) * $product->property->total_area : 0) + (array_sum($expenses['ignored_cc']) / $properties->count) + $product->frt_value;

            $squareMeterCost = $expenses['Total Despesas Construção'] / $total_area;

            if (!empty($totalAreaWithoutExchange)) {
                $product->landCost = (($expenses['Total Despesas Construção'] / ($total_area - $totalAreaWithoutExchange)) - $squareMeterCost) * $product->property->total_area;

                if (empty($product->exchange)) {
                    $product->total_per_house += $product->landCost;
                }
            }

            $product->total_expenses = $product->total_per_house + $product->expenses->total_expenses_per_sale;
            $product->total_payment_burn_expenses = array_sum((array)$product->payments->data);
            $product->total_payment_prices = $product->payments->prices->total;

            $product->profit = !empty($product->expenses->sale->sale_value) ? $product->expenses->sale->sale_value - ($product->total_expenses + $product->total_payment_burn_expenses) : $product->expenses->total_expenses_per_sale - ($product->total_expenses + $product->total_payment_burn_expenses);

            if ($product->exchange != 1) {
                $total_profit = $product->profit > 0 ? $total_profit + $product->profit : $total_profit - ($product->profit * -1);

                (new ConstructionProperties)->update(['profit' => $product->profit], "id_property", $product->id_property);
            }
        }, $properties->data);

        usort($properties->data, function ($a, $b) {
            return strcmp($a->property->name, $b->property->name);
        });

        $sale_expenses_key = array_unique(array_merge(...$sale_expenses_key));
        $sale_payments_key = array_unique(array_merge(...$sale_payments_key));
        $payment_properties = array_unique(array_merge(...$payment_properties));
        $payment_vehicles = array_unique(array_merge(...$payment_vehicles));
        $payment_forms = array_unique(array_merge(...$payment_forms));

        $paymentStatus = (new PaymentStatus())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1]]]])->data;

        require APP . "view/_templates/header.php";
        require APP . "view/" . $this->route . "/report.php";
        require APP . "view/_templates/footer.php";
    }

    public function printReport(int $itemId): void
    {
        $item = $this->model->getItemById($itemId);
        $properties = (new ConstructionProperties)->getWithFiltersAllItems([(object)['columns' => ['id_construction' => (object)['value' => $itemId]]]]);
        $expenses = (new Construction)->expenses($item->id, $item->id_cost_center);
        $cost_centers_key = array_keys($expenses['sum_cc']);

        $sale_expenses_key = [];
        $sale_payments_key = [];
        $payment_properties = [];
        $payment_vehicles = [];
        $payment_forms = [];
        $total_profit = 0;
        $total_area = 0;

        $exchange = "";
        $totalAreaWithoutExchange = 0;

        array_map(function ($product) use (&$sale_expenses_key, &$sale_payments_key, &$total_area, &$payment_properties, &$payment_vehicles, &$payment_forms, &$exchange, &$totalAreaWithoutExchange) {
            $product->expenses = $this->model->sale_expenses($product);
            $product->payments = $this->model->sale_payments($product);
            $product->property = (new Property)->getItemById($product->id_property);

            if (!empty($product->exchange)) {
                $totalAreaWithoutExchange += $product->property->total_area;

                $exchange = true;
            }

            $total_area = $total_area + $product->property->total_area;

            array_push($sale_expenses_key, $product->expenses->keys);
            array_push($sale_payments_key, $product->payments->keys);
            array_push($payment_properties, array_keys((array)$product->payments->prices->properties));
            array_push($payment_vehicles, array_keys((array)$product->payments->prices->vehicles));
            array_push($payment_forms, array_keys((array)$product->payments->prices->form_of_payments));
        }, $properties->data);

        array_map(function ($product) use ($expenses, $total_area, $properties, &$total_profit, &$totalAreaWithoutExchange) {
            $product->total_per_house = ($total_area > 0 ? ($expenses['Total Despesas Construção'] / $total_area) * $product->property->total_area : 0) + (array_sum($expenses['ignored_cc']) / $properties->count) + $product->frt_value;

            $squareMeterCost = $expenses['Total Despesas Construção'] / $total_area;

            if (!empty($totalAreaWithoutExchange)) {
                $product->landCost = (($expenses['Total Despesas Construção'] / ($total_area - $totalAreaWithoutExchange)) - $squareMeterCost) * $product->property->total_area;

                if (empty($product->exchange)) {
                    $product->total_per_house += $product->landCost;
                }
            }

            $product->total_expenses = $product->total_per_house + $product->expenses->total_expenses_per_sale;
            $product->total_payment_burn_expenses = array_sum((array)$product->payments->data);
            $product->total_payment_prices = $product->payments->prices->total;

            $product->profit = !empty($product->expenses->sale->sale_value) ? $product->expenses->sale->sale_value - ($product->total_expenses + $product->total_payment_burn_expenses) : $product->expenses->total_expenses_per_sale - ($product->total_expenses + $product->total_payment_burn_expenses);
            $total_profit = $product->profit > 0 ? $total_profit + $product->profit : $total_profit - ($product->profit * -1);
        }, $properties->data);


        usort($properties->data, function ($a, $b) {
            return strcmp($a->property->name, $b->property->name);
        });

        $sale_expenses_key = array_unique(array_merge(...$sale_expenses_key));
        $sale_payments_key = array_unique(array_merge(...$sale_payments_key));
        $payment_properties = array_unique(array_merge(...$payment_properties));
        $payment_vehicles = array_unique(array_merge(...$payment_vehicles));
        $payment_forms = array_unique(array_merge(...$payment_forms));

        $paymentStatus = (new PaymentStatus())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1]]]])->data;

        require APP . 'view/' . $this->route . '/print.php';
    }
}
