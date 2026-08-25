<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Pagination;
use RR\libs\Util;

class Construction extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'constructions';
        $joins = [
            (object)[
                'table' => 'cost_center',
                'join' => 'inner',
                'where' => 'cost_center.id = constructions.id_cost_center',
            ]
        ];

        parent::__construct($this->table, $joins);
    }

    public function getItemsWithFilters(): object
    {
        $rows = 20;
        $page = Pagination::getPage();

        $columns = [
            (object)['columns' => ['*']],
            (object)[
                'table' => 'cost_center',
                'columns' => ['name']
            ]
        ];

        $filters = [
            (object)[
                'columns' => []
            ]
        ];


        if (isset($_GET['status'])) {
            $filters[0]->columns['status'] = (object)['value' => $_GET['status']];
        }

        if (!empty($_GET['name'])) {
            $filters[0]->columns['name'] = (object)['comparison' => 'LIKE', 'value' => $_GET['name']];
        }

        if (!empty($_GET['cod'])) {
            $filters[0]->columns['id'] = (object)['value' => $_GET['cod']];
        }

        $response = $this->getWithFiltersAllItems($filters, $columns, ['limit' => $rows, 'page' => $page], ['orderBy' => 'name ASC']);

        return $response;
    }

    public function submitAdd(): object
    {
        $arrPost = [
            'name' => $_POST['name'],
            'id_cost_center' => $_POST['id_cost_center'],
            'created_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $ctrlId = [];
            $response = $this->insert($arrPost);
            foreach (explode(',', $_POST['properties']) as $property) {
                if (!empty($property)) {
                    if (!empty($_POST['checkBox'])) {
                        foreach ($_POST['checkBox'] as $key => $value) {
                            if ($key == $property) {
                                (new ConstructionProperties)->insert([
                                    'exchange' => $value,
                                    'id_property' => $property,
                                    'id_construction' => $response->lastId,
                                    'frt_value' => null
                                ]);

                                $ctrlId[] .= $property;
                            }
                        }
                    }

                    if (!in_array($property, $ctrlId)) {
                        (new ConstructionProperties)->insert([
                            'exchange' => null,
                            'id_property' => $property,
                            'id_construction' => $response->lastId,
                            'frt_value' => Util::unmaskMoney($_POST[$property]),
                        ]);

                        $ctrlId[] .= $property;
                    }
                }
            }

            foreach (explode(',', $_POST['id_not_cost_center']) as $cc_not) {
                if (!empty($cc_not)) {
                    (new ConstructionCostCenters)->insert([
                        'id_construction' => $response->lastId,
                        'id_cost_center' => $cc_not,
                        'type' => 1
                    ]);
                }
            }

            foreach (explode(',', $_POST['id_ignore_sum_cc']) as $cc_ignored) {
                if (!empty($cc_ignored)) {
                    (new ConstructionCostCenters)->insert([
                        'id_construction' => $response->lastId,
                        'id_cost_center' => $cc_ignored,
                        'type' => 2
                    ]);
                }
            }

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === "development") {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true,  'message' => 'Erro ao adicionar obra.'];
        }
    }

    public function submitEdit(int $itemId): object
    {
        $arrPost = [
            'name' => $_POST['name'],
            'id_cost_center' => $_POST['id_cost_center'],
            'updated_by' => $_SESSION['RR']->user->id,
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrPost, 'id', $itemId);

            (new ConstructionProperties)->delete(['id_construction' => $itemId]);

            $ctrlId = [];
            foreach (explode(',', $_POST['properties']) as $property) {
                if (!empty($property)) {
                    if (!empty($_POST['checkBox'])) {
                        foreach ($_POST['checkBox'] as $key => $value) {
                            if ($key == $property) {
                                (new ConstructionProperties)->insert([
                                    'exchange' => $value,
                                    'id_property' => $property,
                                    'id_construction' => $itemId,
                                    'frt_value' => null
                                ]);

                                $ctrlId[] .= $property;
                            }
                        }
                    }

                    if (!in_array($property, $ctrlId)) {
                        (new ConstructionProperties)->insert([
                            'exchange' => null,
                            'id_property' => $property,
                            'id_construction' => $itemId,
                            'frt_value' => Util::unmaskMoney($_POST[$property]),
                        ]);

                        $ctrlId[] .= $property;
                    }
                }
            }

            (new ConstructionCostCenters)->delete(['id_construction' => $itemId, 'type' => 1]);
            foreach (explode(',', $_POST['id_not_cost_center']) as $cc_not) {
                if (!empty($cc_not)) {
                    (new ConstructionCostCenters)->insert([
                        'id_construction' => $itemId,
                        'id_cost_center' => $cc_not,
                        'type' => 1
                    ]);
                }
            }

            (new ConstructionCostCenters)->delete(['id_construction' => $itemId, 'type' => 2]);
            foreach (explode(',', $_POST['id_ignore_sum_cc']) as $cc_ignored) {
                if (!empty($cc_ignored)) {
                    (new ConstructionCostCenters)->insert([
                        'id_construction' => $itemId,
                        'id_cost_center' => $cc_ignored,
                        'type' => 2
                    ]);
                }
            }

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === "development") {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true,  'message' => 'Erro ao editar obra.'];
        }
    }
    public function submitStatus($itemId): object
    {
        $item = $this->getItemById($itemId);
        try {
            $this->db->beginTransaction();

            $response = $this->update([
                'status' => $item->status == 1 ? '0' : '1',
                'updated_by' => $_SESSION['RR']->user->id,
            ], 'id', $itemId);

            $this->db->commit();
            return $response;
        } catch (PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT === "DEVELOPMENT") {
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Erro ao alterar status.'];
        }
    }

    public function expenses(int $construction_id, int $cost_center_id): array
    {
        (array)$cost_center_not = array_map(function ($item) {
            return $item->id_cost_center;
        }, (new ConstructionCostCenters)->getWithFiltersAllItems([(object)['columns' => ['id_construction' => (object)['value' => $construction_id], 'type' => (object)['value' => 1]]]])->data);

        (array)$cost_center_ignored = array_map(function ($item) {
            return $item->id_cost_center;
        }, (new ConstructionCostCenters)->getWithFiltersAllItems([(object)['columns' => ['id_construction' => (object)['value' => $construction_id], 'type' => (object)['value' => 2]]]])->data);

        $cost_centers_bills = (new BillsToPay)->recursiveBillsByCostCenter($cost_center_id);
        $cost_centers_bills = array_filter($cost_centers_bills, function ($item) use ($cost_center_not) {
            foreach ($item as $val) {
                return !in_array($val->id_cost_center, $cost_center_not);
            }
        });

        $filters = [(object)['columns' => []]];

        !isset($_GET['status_payment']) ? $filters[0]->columns['status_payment'] = (object)['comparison' => 'IN', 'value' => [1, 2]] : $filters[0]->columns['status_payment'] = (object)['comparison' => 'IN', 'value' => $_GET['status_payment']];

        $cost_centers_expenses = ['sum_cc' => [], 'ignored_cc' => []];
        foreach ($cost_centers_bills as $bills) {
            $cost_center = (new CostCenter)->getItemById($bills[0]->id_cost_center, [(object)['columns' => ['id', 'name']]]);
            foreach ($bills as $bill) {
                $filters[0]->columns['id_bills_to_pay'] = (object)['value' => $bill->id];
                $installments = (new BillsToPayInstallment)->getWithFiltersAllItems($filters)->data;
                foreach ($installments as $installment) {
                    if (in_array($cost_center->id, $cost_center_ignored)) {
                        empty($cost_centers_expenses['ignored_cc'][$cost_center->name]) ? $cost_centers_expenses['ignored_cc'][$cost_center->name] = $installment->value_of_installments : $cost_centers_expenses['ignored_cc'][$cost_center->name] += $installment->value_of_installments;
                    } else {
                        empty($cost_centers_expenses['sum_cc'][$cost_center->name]) ? $cost_centers_expenses['sum_cc'][$cost_center->name] = $installment->value_of_installments : $cost_centers_expenses['sum_cc'][$cost_center->name] += $installment->value_of_installments;
                    }
                }
            }
        }

        $cost_centers_expenses['Total Despesas Construção'] = array_sum($cost_centers_expenses['sum_cc']);

        return $cost_centers_expenses;
    }

    public function sale_expenses($property)
    {
        $sale = (new Sales)->getWithFiltersAllItems([(object)['columns' => ['id_product' => (object)['value' => $property->id_property], 'status' => (object)['value' => 1]]]])->data;
        if (empty($sale)) return (object)['sale' => [], 'data' => [], 'sales' => [], 'keys' => [], 'total_expenses_per_sale' => 0];

        $sale_customer = (new Customer)->getItemById($sale[0]->id_customer);
        $sale_seller = $sale[0]->seller_name ?? (new User)->getItemById($sale[0]->created_by)->name;

        $sales_desc = [
            'Comprador' => $sale_customer->name,
            'Corretor' => $sale_seller,
            'Valor Comissão' => $sale[0]->expense_commission,
            'Valor Operacional Retido (R$)' => $sale[0]->expense_operational_value,
            'Pós Venda Retido (R$)' => $sale[0]->after_sale_retained,
            'Imposto (R$)' => $sale[0]->expense_tax,
            'Comissão Gerencial' => $sale[0]->expense_management_commission,
        ];

        $expenses = (object)[];
        array_map(function ($sale_expense) use (&$expenses) {
            $expenses->{$sale_expense->expense_key} = ($sale_expense->expense_value);
        }, (new Expenses)->getWithFiltersAllItems([(object)['columns' => ['id_sale' => (object)['value' => $sale[0]->id]]]])->data);

        $total_expenses_per_sale = array_sum((array)$expenses);
        array_walk($sales_desc, function ($value, $key) use (&$total_expenses_per_sale) {
            if (in_array($key, ['Comprador', 'Corretor'])) return;
            $total_expenses_per_sale += $value;
        });

        return (object)[
            'sale' => $sale[0],
            'data' => $sales_desc,
            'sales' => $expenses,
            'keys' => array_keys((array)$expenses),
            'total_expenses_per_sale' => $total_expenses_per_sale
        ];
    }

    public function sale_payments($property)
    {
        $sale = (new Sales)->getItemWithFilters([(object)['columns' => ['id_product' => (object)['value' => $property->id_property], 'status' => (object)['value' => 1]]]]);
        if (empty($sale)) return (object)['data' => [], 'keys' => [], 'sale_value' => 0, 'prices' => (object)['properties' => (object)[], 'vehicles' => (object)[], 'form_of_payments' => (object)[], 'total' => 0]];

        $payments = (new PaymentsOfSales)->getWithFiltersAllItems([(object)['columns' => ['id_sale' => (object)['value' => $sale->id], 'status' => (object)['value' => 1]]]])->data;
        if (empty($payments)) return (object)['data' => [], 'keys' => [], 'sale_value' => $sale->sale_value, 'prices' => (object)['properties' => (object)[], 'vehicles' => (object)[], 'form_of_payments' => (object)[], 'total' => 0]];

        $paymentsBurn = (object)[];
        $saleTotal = 0;
        $prices = (object)['properties' => (object)[], 'vehicles' => (object)[], 'form_of_payments' => (object)[], 'total' => 0];
        $arrModel = [];

        array_map(function ($payment) use (&$paymentsBurn, &$saleTotal, &$prices, $arrModel) {
            if ($payment->type_of_payment == 1) $arrModel[1] = (new FormOfPayment)->getItemById($payment->id_form_of_payment)->name;
            if ($payment->type_of_payment == 2) $arrModel[2] = (new Property)->getItemById($payment->id_property)->name;
            if ($payment->type_of_payment == 3) $arrModel[3] = $payment->vehicle;

            $payment->payment_name = $arrModel[$payment->type_of_payment];

            if ($payment->status_portion == 1) {

                if ($payment->value > $payment->amount_paid) {
                    $paymentsBurn->{$payment->payment_name} = $payment->value - $payment->amount_paid;

                    if ($payment->type_of_payment == 1) $saleTotal += $payment->amount_paid;
                }

                if ($payment->value <= $payment->amount_paid && $payment->type_of_payment == 1) {
                    $saleTotal += $payment->value;
                }

                if ($payment->type_of_payment == 1) {
                    !isset($prices->form_of_payments->{$payment->payment_name}) ? $prices->form_of_payments->{$payment->payment_name} = $payment->value  : $prices->form_of_payments->{$payment->payment_name} += $payment->value;
                }
                if ($payment->type_of_payment == 2) $prices->properties->{$payment->payment_name} = $payment->value;
                if ($payment->type_of_payment == 3) $prices->vehicles->{$payment->payment_name} = $payment->value;
                $prices->total += $payment->value;
            }

            if ($payment->status_portion == 0) {
                !empty($prices->form_of_payments->{$payment->payment_name}) ? $prices->form_of_payments->{$payment->payment_name} += $payment->value : $prices->form_of_payments->{$payment->payment_name} = $payment->value;
                $saleTotal += $payment->value;
            }
        }, $payments);

        return (object)[
            'data' => $paymentsBurn,
            'keys' => array_keys((array)$paymentsBurn),
            'sale_value' => $saleTotal,
            'prices' => (object)[
                'form_of_payments' => $prices->form_of_payments,
                'properties' => $prices->properties,
                'vehicles' => $prices->vehicles,
                'total' => $prices->total
            ]
        ];
    }
}
