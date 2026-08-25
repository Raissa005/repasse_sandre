<?php

namespace RR\controller\project;

use RR\libs\Date;
use RR\libs\Util;
use RR\model\User;
use RR\model\Sales;
use RR\model\Status;
use RR\libs\BoxAlert;
use RR\libs\Pagination;

class ReportSalesController extends FrontController
{
    public $dir;
    public $route;
    private $model;
    private $table;

    public $alert;
    public $title;

    public function __construct()
    {
        $this->table = 'sales';
        $this->model = new Sales();
        $this->dir = 'report-sales';
        $this->route = 'report-sales';
        parent::__construct($this->route);

        $this->alert = new BoxAlert();
        $this->title = 'Relatório de Vendas';
    }

    public function index()
    {
        $teste = explode('report-sales', $_SERVER['REQUEST_URI']);
        $filters = end($teste);

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => $this->title,
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => '&nbspImprimir Relatório',
                    'href' => URL . "{$this->route}/print{$filters}",
                    'icon' => 'fa fa-print',
                    'attr' => ['target' => '_blank'],
                ]
            ],
        ];

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['date']['end'])) {
            $_GET['date']['end'] = date("Y-m-d");
        }

        if (!isset($_GET['date']['start'])) {
            date_modify(date_create($_GET['date']['end']), "-30 days");
            $_GET['date']['start'] = date_format(date_create($_GET['date']['end']), "Y-m-d");
        }

        $filtersSales = [
            (object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']]]]
        ];

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersSales, (object)['table' => 'sales', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        }

        if (!empty($_GET['id_sale_status'])) {
            array_push($filtersSales, (object)['columns' => ['id_sale_status' => (object)['comparison' => '=', 'value' => $_GET['id_sale_status']]]]);
        }

        if (!empty($_GET['customer_name'])) {
            array_push($filtersSales, (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['customer_name']]]]);
        }

        if (!empty($_GET['date'])) {
            if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                array_push($filtersSales, (object)['columns' => ['sale_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
            } else {
                if (!empty($_GET['date']['start'])) {
                    array_push($filtersSales, (object)['columns' => ['sale_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                }
                if (!empty($_GET['date']['end'])) {
                    array_push($filtersSales, (object)['columns' => ['sale_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                }
            }
        }

        $columnsSales =  [
            (object)['columns' => ['*']],
            (object)['table' => 'customer', 'columns' => ['name']],
            (object)['table' => 'products', 'columns' => ['name', 'cod', 'total_area']],
            (object)['table' => 'status', 'columns' => ['id', 'name']],
            (object)['table' => 'construction_properties', 'columns' => ['id', 'frt_value', 'profit']],
        ];

        $rows = 20;
        $page = Pagination::getPage();

        $salesStatus = (new Status())->getWithFiltersAllItems()->data;
        $sellers = (new User())->getWithFiltersAllItems([(object)['columns' => ['id_profile' => (object)['value' => 4]]]])->data;
        $sales = $this->model->getWithFiltersAllItems($filtersSales, $columnsSales, ['limit' => $rows, 'page' => $page, 'orderBy' => 'sales.sale_date ASC']);

        array_map(function ($item) use (&$totalOPValue, &$totalRFTValue, &$totalNetProfit, &$totalAfterSalesRetained) {
            switch ($item->id_sale_status) {
                case 1:
                    $item->label = 'label-warning';
                    break;
                case 2:
                    $item->label = 'label-default';
                    break;
                case 3:
                    $item->label = 'label-success';
                    break;
                case 9:
                    $item->label = 'label-danger';
                    break;
            }

            $item->sale_date = Date::date($item->sale_date);
            $item->sale_value = Util::maskMoney($item->sale_value);
            $item->after_sale_retained = Util::maskMoney($item->after_sale_retained);
            $item->expense_operational_value = Util::maskMoney($item->expense_operational_value);
            $item->construction_properties_profit = Util::maskMoney($item->construction_properties_profit);
            $item->construction_properties_frt_value = Util::maskMoney($item->construction_properties_frt_value);

            $totalOPValue = $totalOPValue + Util::unmaskMoney($item->expense_operational_value);
            $totalNetProfit = $totalNetProfit + Util::unmaskMoney($item->construction_properties_profit);
            $totalRFTValue = $totalRFTValue + Util::unmaskMoney($item->construction_properties_frt_value);
            $totalAfterSalesRetained = $totalAfterSalesRetained + Util::unmaskMoney($item->after_sale_retained);
        }, $sales->data);

        $pagination = (new Pagination())->pages($sales->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($sales->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function print()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['date']['end'])) {
            $_GET['date']['end'] = date("Y-m-d");
        }

        if (!isset($_GET['date']['start'])) {
            date_modify(date_create($_GET['date']['end']), "-30 days");
            $_GET['date']['start'] = date_format(date_create($_GET['date']['end']), "Y-m-d");
        }

        $filtersSales = [
            (object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']]]]
        ];

        if ($_SESSION['RR']->branch->current->id != 0) {
            array_push($filtersSales, (object)['table' => 'sales', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]]);
        }

        if (!empty($_GET['id_sale_status'])) {
            array_push($filtersSales, (object)['columns' => ['id_sale_status' => (object)['comparison' => '=', 'value' => $_GET['id_sale_status']]]]);
        }

        if (!empty($_GET['customer_name'])) {
            array_push($filtersSales, (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['customer_name']]]]);
        }

        if (!empty($_GET['date'])) {
            if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                array_push($filtersSales, (object)['columns' => ['sale_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime($_GET['date']['start'])), 'value2' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
            } else {
                if (!empty($_GET['date']['start'])) {
                    array_push($filtersSales, (object)['columns' => ['sale_date' => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET['date']['start']))]]]);
                }
                if (!empty($_GET['date']['end'])) {
                    array_push($filtersSales, (object)['columns' => ['sale_date' => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime($_GET['date']['end']))]]]);
                }
            }
        }

        $columnsSales =  [
            (object)['columns' => ['*']],
            (object)['table' => 'customer', 'columns' => ['name']],
            (object)['table' => 'status', 'columns' => ['id', 'name']],
            (object)['table' => 'products', 'columns' => ['name', 'cod', 'total_area']],
            (object)['table' => 'construction_properties', 'columns' => ['id', 'frt_value', 'profit']]
        ];

        $response = $this->model->getWithFiltersAllItems($filtersSales, $columnsSales,  ['orderBy' => 'sales.sale_date ASC']);

        array_map(function ($item) use (&$totalOPValue, &$totalRFTValue, &$totalNetProfit, &$totalAfterSalesRetained) {
            switch ($item->id_sale_status) {
                case 1:
                    $item->label = 'label-warning';
                    break;
                case 2:
                    $item->label = 'label-default';
                    break;
                case 3:
                    $item->label = 'label-success';
                    break;
                case 9:
                    $item->label = 'label-danger';
                    break;
            }

            $item->sale_date = Date::date($item->sale_date);
            $item->sale_value = Util::maskMoney($item->sale_value);
            $item->after_sale_retained = Util::maskMoney($item->after_sale_retained);
            $item->expense_operational_value = Util::maskMoney($item->expense_operational_value);
            $item->construction_properties_profit = Util::maskMoney($item->construction_properties_profit);
            $item->construction_properties_frt_value = Util::maskMoney($item->construction_properties_frt_value);

            $totalOPValue = $totalOPValue + Util::unmaskMoney($item->expense_operational_value);
            $totalNetProfit = $totalNetProfit + Util::unmaskMoney($item->construction_properties_profit);
            $totalRFTValue = $totalRFTValue + Util::unmaskMoney($item->construction_properties_frt_value);
            $totalAfterSalesRetained = $totalAfterSalesRetained + Util::unmaskMoney($item->after_sale_retained);
        }, $response->data);

        require APP . 'view/' . $this->dir . '/print.php';
    }
}
