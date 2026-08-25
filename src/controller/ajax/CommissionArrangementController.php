<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\CommissionArrangement;
use RR\libs\Util;
use RR\model\ArrangementPaymentChargesInvoiceReceiveInstallment;
use RR\model\BillReceiveInstallment;
use RR\model\Sales;
use RR\model\User;

class CommissionArrangementController extends Ajax
{
    public $commission;

    public function commissionCalculation()
    {
        if (!isset($_POST['positions'])) {
            $_POST['positions'] = [];
        }

        $_POST['saleId'] = $_POST['saleId'] ?? null;
        
        $commissionArrangement = new CommissionArrangement($_POST['value'], $_POST['percentage'], (object)$_POST['taxes'], $_POST['saleId']);
        $data = (object)[
            'taxes' => $commissionArrangement->taxes(),
            'commission' => $commissionArrangement->commission(),
            'seller' => $commissionArrangement->seller($_POST['seller']['origin'], $_POST['seller']['percentage']),
            'positions' => $commissionArrangement->positions($_POST['positions']),
            'branch' => $commissionArrangement->branch((object)['type' => $_POST['seller']['origin'], 'percentage' => $_POST['seller']['percentage']], $_POST['positions']),
        ];

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getPaymentArrangementInstallment()
    {
        $sale = (new Sales)->getItemById($_POST['sale_id'], [
            (object)[
                'columns' => [
                    'created_by',
                    'percentage_commission_virtual_rate',
                    'percentage_commission_real_rate',
                ]
            ]
        ]);

        $seller = (new User)->getItemById($sale->created_by);

        $percentanges = (new BillReceiveInstallment)->getItemById($_POST['bill_receive_installment_id'], [
            (object)[
                'columns' => [
                    'value_installment',
                    'origin_commission_seller',
                    'percentage_commission_seller',
                ]
            ]
        ]);

        $positions = (new ArrangementPaymentChargesInvoiceReceiveInstallment)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'id_bill_receive_installment' => (object)['comparison' => 'EQUAL', 'value' => $_POST['bill_receive_installment_id']]
                    ]
                ]
            ],
            [
                (object)['columns' => ['id', 'percentage_commission', 'origin_commission']],
                (object)[
                    'table' => 'customer',
                    'columns' => ['id', 'name', 'fancy_name_company', 'company_name']
                ]
            ],
            ['groupBy' => 'customer.name']
        )->data;

        array_map(function ($position) {
            if (!empty($position->customer_fancy_name_company)) {
                $position->customer_name = $position->customer_fancy_name_company;
            } else if (!empty($position->customer_company_name)) {
                $position->customer_name = $position->customer_company_name;
            }
        }, $positions);

        $data = (object)[
            'value' => $percentanges->value_installment,
            'taxes' => (object)[
                'real' => $sale->percentage_commission_real_rate,
                'virtual' => $sale->percentage_commission_virtual_rate,
            ],
            'seller' => (object)[
                'name' => $seller->name,
                'origin' => $percentanges->origin_commission_seller,
                'percentage' => $percentanges->percentage_commission_seller,
            ],
            'positions' => $positions
        ];

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }
}
