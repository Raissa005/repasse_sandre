<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Date;
use RR\libs\Util;
use RR\model\Branch;
use RR\model\Customer;
use RR\model\Vehicles;
use RR\libs\Pagination;
use RR\model\BillsToPay;
use RR\model\PurchaseRequests;
use RR\model\VehiclePurchases;
use RR\model\BillsToPayInstallment;

class PurchaseRequestsController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new PurchaseRequests;

        parent::__construct();
    }

    public function getCustomersSellerAndBuyer()
    {
        if (!empty($_POST)) {
            $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);

            $customer_filters = [
                (object) ['columns' => ['status' => (object) ['value' => 1]]],
                (object) ['table' => 'customer_branches', 'columns' => ['id_branch' => (object) ['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]],
                (object) ["table" => 'client_type_resource_types', "columns" => ['id_customer_type' => (object) ['comparison' => 'IN', 'value' => [1, 2]]]]
            ];

            if (!empty($_POST['name'])) {
                $customer_filters[] = (object) [
                    'where' => " AND (
                    ucase(this->table.name) LIKE ucase('%" . $_POST['name'] . "%')
                    OR ucase(this->table.fancy_name_company) LIKE ucase('%" . $_POST['name'] . "%')
                    OR ucase(this->table.company_name) LIKE ucase('%" . $_POST['name'] . "%'))"
                ];
            }

            if ($_SESSION['RR']->profile->access >= 30 && $branch->restrict_owner_data == 1) {
                $customer_filters[] = (object) ['columns' => ['created_by' => (object) ['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            $response = (new Customer)->getWithFiltersAllItems(
                $customer_filters,
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'cities', 'columns' => ['name' => ['cityName'], 'uf' => ['cityUF']]]
                ],
                [
                    'limit' => $_POST['limit'],
                    'page' => $_POST['page'],
                    'groupBy' => 'this->table.id',
                    'order' => 'COALESCE(this->table.fancy_name_company, this->table.company_name, this->table.name) ASC',
                ]
            );

            $data = array_map(function ($customer) {
                if (!empty($customer->fancy_name_company)) {
                    $customer->name = $customer->fancy_name_company;
                    $customer->cpf_cnpj = $customer->cnpj ?? ' - ';
                } else if (!empty($customer->company_name)) {
                    $customer->name = $customer->company_name;
                    $customer->cpf_cnpj = $customer->cnpj ?? ' - ';
                } else {
                    $customer->name = $customer->name;
                    $customer->cpf_cnpj = $customer->person_registration ?? ' - ';
                }
                return $customer;
            }, $response->data);

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data, 'pagination' => (new Pagination)->pages($response->count, $_POST['limit'], 10, $_POST['page'])]);
            exit;
        }
    }

    public function addVehiclesPurchase()
    {
        if (!empty($_POST)) {
            $purchase = (new PurchaseRequests)->getItemById($_POST['itemId']);

            if (!empty($purchase->value)) {
                $totalPurchase = $purchase->value;
            } else {
                $totalPurchase = 0;
            }

            $countRegistered = 0;
            foreach ($_POST['vehicles'] as $vehicle) {

                $commission = str_replace(['R$', ' '], '', $vehicle['commission']);

                $arrPost = [
                    'name' => $vehicle['name'],
                    'id_type' => $vehicle['typeId'],
                    'id_door' => $vehicle['doorId'],
                    'id_fuel' => $vehicle['fuelId'],
                    'id_brand' => $vehicle['brandId'],
                    'id_model' => $vehicle['modelId'],
                    'id_color' => $vehicle['colorId'],
                    'plate' => $vehicle['plate'] ?? '',
                    'status' => $vehicle['status'] ?? 0,
                    'chassi' => $vehicle['chassi'] ?? '',
                    'year_model' => $vehicle['yearModel'],
                    'renavam' => $vehicle['renavam'] ?? '',
                    'mileage' => $vehicle['mileage'] ?? '',
                    'id_purchase_request' => $purchase->id,
                    'id_category' => $vehicle['categoryId'],
                    'zero_mileage' => $vehicle['zeroMileage'] ? 1 : 0,
                    'year_manufacture' => $vehicle['yearManufacture'],
                    'factory_warranty' => $vehicle['factoryWarranty'] ?? '',
                    'vehicle_sales_value' => Util::unmaskMoney($vehicle['valueSale']) ?? ''
                ];

                $response = (new Vehicles)->insert($arrPost);

                if (!$response->error) {
                    unset($arrPost);
                    $countRegistered++;

                    $arrPost = [
                        'id_type_negotiation' => true,
                        'uf_state' => $vehicle['state'],
                        'id_city' => $vehicle['id_city'],
                        'id_vehicle' => $response->lastId,
                        'purchase_date' => $purchase->purchase_date,
                        'id_former_owner' => $purchase->id_customer,
                        'id_purchase_broker' => $purchase->id_purchase_broker ?? '',
                        'purchase_value' => Util::unmaskMoney($vehicle['value']),
                        'value_commission' => !empty($vehicle['commission']) ? str_replace(',', '.', $commission) : '',
                    ];

                    $purchaseVehicle = (new VehiclePurchases)->insert($arrPost);

                    if (!$purchaseVehicle->error) {
                        $totalPurchase += Util::unmaskMoney($vehicle['value']);
                    }
                }
            }

            if ($totalPurchase > 0) {
                (new PurchaseRequests)->update(['value' => $totalPurchase], 'id', $purchase->id);
            }

            if ($countRegistered == count($_POST['vehicles'])) {
                $error = false;
                $message = "Veículos cadastrados com sucesso!";
            } else {
                $error = true;
                $message = "Apenas $countRegistered foi cadastrados";
            }
        }

        echo json_encode(['error' => $error, 'message' => $message]);
        exit;
    }

    public function getVehicleById()
    {
        if (!empty($_POST)) {
            $response = (new Vehicles)->getItemById(
                $_POST['vehicleId'],
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'vehicle_purchases', 'columns' => ['purchase_value' => ['purchase_value'], 'value_commission' => ['value_commission']]]
                ]
            );

            if ($response) {
                $error = false;
                $message = 'Item encontrado!';
            } else {
                $error = true;
                $message = 'Item não encontrado!';
            }

            echo json_encode(['error' => $error, 'message' => $message, 'vehicle' => $response]);
            exit;
        }
    }

    public function editVehiclesPurchase()
    {
        if (!empty($_POST)) {
            $purchase = (new PurchaseRequests)->getItemById($_POST['itemId']);

            $vehicle = (new Vehicles)->getItemById(
                $_POST['vehicleId'],
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'vehicle_purchases', 'columns' => ['purchase_value' => ['purchase_value']]]
                ]
            );

            $arrPost = [
                'name' => $_POST['vehicles'][0]['name'],
                'id_type' => $_POST['vehicles'][0]['typeId'],
                'id_door' => $_POST['vehicles'][0]['doorId'],
                'id_fuel' => $_POST['vehicles'][0]['fuelId'],
                'id_brand' => $_POST['vehicles'][0]['brandId'],
                'id_model' => $_POST['vehicles'][0]['modelId'],
                'id_color' => $_POST['vehicles'][0]['colorId'],
                'plate' => $_POST['vehicles'][0]['plate'] ?? '',
                'status' => $_POST['vehicles'][0]['status'] ?? 0,
                'chassi' => $_POST['vehicles'][0]['chassi'] ?? '',
                'year_model' => $_POST['vehicles'][0]['yearModel'],
                'renavam' => $_POST['vehicles'][0]['renavam'] ?? '',
                'mileage' => $_POST['vehicles'][0]['mileage'] ?? '',
                'id_purchase_request' => $purchase->id,
                'id_category' => $_POST['vehicles'][0]['categoryId'],
                'zero_mileage' => $_POST['vehicles'][0]['zeroMileage'] ? 1 : 0,
                'year_manufacture' => $_POST['vehicles'][0]['yearManufacture'],
                'factory_warranty' => $_POST['vehicles'][0]['factoryWarranty'] ?? '',
                'vehicle_sales_value' => Util::unmaskMoney($_POST['vehicles'][0]['valueSale']) ?? ''
            ];

            $vehicleResponse = (new Vehicles)->update($arrPost, 'id', $vehicle->id);

            $value = Util::unmaskMoney($_POST['vehicles'][0]['value']);
            if (!$vehicleResponse->error) {
                $purchaseResponse = (new VehiclePurchases)->update(
                    [
                        'purchase_value' => $value,
                        'value_commission' => Util::unmaskMoney($_POST['vehicles'][0]['commission'])
                    ],
                    'id_vehicle',
                    $vehicle->id
                );

                if ($purchaseResponse->error) {
                    $value = ($purchase->value - $vehicle->purchase_value) + $value;

                    (new PurchaseRequests)->update(['value' => $value], 'id', $purchase->id);
                }
            }

            echo json_encode(['error' => $vehicleResponse->error, 'message' => $vehicleResponse->message]);
            exit;
        }
    }

    public function handleSubmitAddBillsToPay()
    {
        $purchase = (new PurchaseRequests)->getItemById(
            $_POST['purchaseId'],
            [
                (object) ['columns' => ['*']],
                (object) ['table' => 'customer', 'columns' => ['name' => ['name']]]
            ]
        );

        if ($_POST['costCenter'] == 12 && !empty($_POST['purchaseBrokerId']) && !empty($_POST['commission']) && $_POST['commission'] == 1)// É uma comissao a pagar
        {
            $arrPost = array(
                'id_customer' => $_POST['purchaseBrokerId'],
                'id_cost_center' => $_POST['costCenter'],
                'created_by' => $_SESSION['RR']->user->id,
                'competence' => ($_POST['competence'] . "-01"),
                'id_form_of_payment' => $_POST['formPaymentId'],
                'id_branch' => $_SESSION['RR']->branch->current->id,
                'description' => "Conta a pagar gerada pelo pedido de compra\n Pedido: #{$purchase->id}\n Vendedor: {$purchase->name}",
            );

            if (isset($purchase->id_commission_to_pay) && !empty($purchase->id_commission_to_pay)) //ja foi lancada uma comissao
            {
                $response = (new BillsToPay)->update($arrPost, 'id', $purchase->id_commission_to_pay);
            } else
            {
                $response = (new BillsToPay)->insert($arrPost);

                if (!$response->error)
                {
                    (new PurchaseRequests)->update(['id_commission_to_pay' => $response->lastId], 'id', $purchase->id);

                    if (isset($_POST['numberOfInstallments']) && $_POST['valueReference'] == 1) {
                        $valueOfInstallments = $_POST['price'] / $_POST['numberOfInstallments'];

                        list($year, $month, $day) = explode("-", $_POST['dueDate']);

                        for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                            $arrPostPortion = [
                                'status_payment' => 1,
                                'number_portion' => $p,
                                'description' => "Parcela número {$p}",
                                'id_bills_to_pay' => $response->lastId,
                                'created_by' => $_SESSION['RR']->user->id,
                                'id_form_of_payment' => $_POST['formPaymentId'],
                                'value_of_installments' => $valueOfInstallments,
                                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                                'id_purchase_broker' => $_POST['purchaseBrokerId']
                            ];

                            $month++;
                            if ((int) $month == 13) {
                                $year++;
                                $month = 1;
                            }

                            $remove = $day;
                            while (!checkdate($month, $remove, $year)) {
                                $remove--;
                            }

                            $_POST['dueDate'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                            (new BillsToPayInstallment())->insert($arrPostPortion);
                        }
                    } else
                    {
                        $valueOfInstallments = $_POST['price'];

                        list($year, $month, $day) = explode("-", $_POST['dueDate']);

                        for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                            $arrPostPortion = [
                                'status_payment' => 1,
                                'number_portion' => $p,
                                'description' => "Parcela número {$p}",
                                'id_bills_to_pay' => $response->lastId,
                                'created_by' => $_SESSION['RR']->user->id,
                                'id_form_of_payment' => $_POST['formPaymentId'],
                                'value_of_installments' => $valueOfInstallments,
                                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                                'id_purchase_broker' => $_POST['purchaseBrokerId']
                            ];

                            $month++;
                            if ((int) $month == 13) {
                                $year++;
                                $month = 1;
                            }

                            $remove = $day;
                            while (!checkdate($month, $remove, $year)) {
                                $remove--;
                            }

                            $_POST['dueDate'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                            (new BillsToPayInstallment())->insert($arrPostPortion);
                        }
                    }
                }
            }
        } else //É apenas uma parcela do financeiro
        {
            $arrPost = array(
                'id_customer' => $purchase->id_customer,
                'id_cost_center' => $_POST['costCenter'],
                'created_by' => $_SESSION['RR']->user->id,
                'competence' => ($_POST['competence'] . "-01"),
                'id_form_of_payment' => $_POST['formPaymentId'],
                'id_branch' => $_SESSION['RR']->branch->current->id,
                'description' => "Conta a pagar gerada pelo pedido de compra\n Pedido: #{$purchase->id}\n Vendedor: {$purchase->name}",
            );

            if (!empty($purchase->id_bills_to_pay)) {
                $response = (new BillsToPay)->update($arrPost, 'id', $purchase->id_bills_to_pay);
            } else {
                $response = (new BillsToPay)->insert($arrPost);

                if (!$response->error) {
                    (new PurchaseRequests)->update(['id_bills_to_pay' => $response->lastId], 'id', $purchase->id);

                    if (isset($_POST['numberOfInstallments']) && $_POST['valueReference'] == 1) {
                        $valueOfInstallments = $_POST['price'] / $_POST['numberOfInstallments'];

                        list($year, $month, $day) = explode("-", $_POST['dueDate']);

                        for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                            $arrPostPortion = [
                                'status_payment' => 1,
                                'number_portion' => $p,
                                'description' => "Parcela número {$p}",
                                'id_bills_to_pay' => $response->lastId,
                                'created_by' => $_SESSION['RR']->user->id,
                                'id_form_of_payment' => $_POST['formPaymentId'],
                                'value_of_installments' => $valueOfInstallments,
                                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                            ];

                            $month++;
                            if ((int) $month == 13) {
                                $year++;
                                $month = 1;
                            }

                            $remove = $day;
                            while (!checkdate($month, $remove, $year)) {
                                $remove--;
                            }

                            $_POST['dueDate'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                            (new BillsToPayInstallment())->insert($arrPostPortion);
                        }
                    } else {
                        $valueOfInstallments = $_POST['price'];

                        list($year, $month, $day) = explode("-", $_POST['dueDate']);

                        for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                            $arrPostPortion = [
                                'status_payment' => 1,
                                'number_portion' => $p,
                                'description' => "Parcela número {$p}",
                                'id_bills_to_pay' => $response->lastId,
                                'created_by' => $_SESSION['RR']->user->id,
                                'id_form_of_payment' => $_POST['formPaymentId'],
                                'value_of_installments' => $valueOfInstallments,
                                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                            ];

                            $month++;
                            if ((int) $month == 13) {
                                $year++;
                                $month = 1;
                            }

                            $remove = $day;
                            while (!checkdate($month, $remove, $year)) {
                                $remove--;
                            }

                            $_POST['dueDate'] = sprintf("%02d-%02d-%02d", $year, $month, $remove);

                            (new BillsToPayInstallment())->insert($arrPostPortion);
                        }
                    }
                }
            }
        }

        if (empty($response->item)) {
            $response->item = $purchase;
        }

        echo json_encode(['error' => $response->error, 'message' => $response->message, 'billsToPay' => $response->item]);
        exit;
    }

    public function addBillsToPayInstallment()
    {
        $allInstallments = (new PurchaseRequests())->getWithFiltersAllItems(
            [
                (object) [
                    'columns' => [
                        'id' => (object) [
                            'comparison' => 'EQUAL',
                            'value' => $_POST['purchaseId']
                        ]
                    ]
                ],
            ],
            [
                (object) [
                    'columns' => [
                        'id_bills_to_pay'
                    ]
                ],
                (object) [
                    'table' => 'bills_to_pay_installments',
                    'columns' => [
                        'number_portion',
                        'value_of_installments'
                    ]
                ]
            ]
        )->data;

        array_map(function ($i) use (&$totalInstallments) {
            if (empty($totalInstallments))
                $totalInstallments = 0;

            $totalInstallments += $i->value_of_installments ?? $i->bills_to_pay_installments_value_of_installments;
        }, $allInstallments);

        $lastInstallment = end($allInstallments);
        $nextInstallment = $lastInstallment->number_portion ?? $lastInstallment->bills_to_pay_installments_number_portion;
        $idBillsPay = (new PurchaseRequests())->getItemById($_POST['purchaseId']);

        if(isset( $_POST['commission'] ) && $_POST['commission'] == 1){
            $arrPost = [
                'number_portion' => $nextInstallment,
                'description' => $_POST['description'],
                'created_by' => $_SESSION['RR']->user->id,
                'id_bills_to_pay' => $idBillsPay->id_commission_to_pay,
                'value_of_installments' => $_POST['valueInstallments'],
                'id_form_of_payment' => $_POST['formPaymentId'],
                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                'id_purchase_broker' => $_POST['purchaseBrokerId']
            ];
        }else{
            $arrPost = [
                'number_portion' => $nextInstallment,
                'description' => $_POST['description'],
                'created_by' => $_SESSION['RR']->user->id,
                'id_bills_to_pay' => $idBillsPay->id_commission_to_pay,
                'value_of_installments' => $_POST['valueInstallments'],
                'id_form_of_payment' => $_POST['formPaymentId'],
                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL
            ];

            (new BillsToPay())->update(['id_cost_center' => $_POST['costCenter']], 'id', $idBillsPay->id_bills_to_pay);
        }

        $response = (new BillsToPayInstallment)->insert($arrPost);

        if(!$response->error && (!isset($_POST['commission']) || $_POST['commission'] != 1 )){
            (new BillsToPay())->update(['id_cost_center' => $_POST['costCenter']], 'id', $_POST['purchaseId']);
        }

        if (!$response->error) {

            $installments = (new BillsToPayInstallment())->getItemById(
                $response->lastId,
                [
                    (object) ['columns' => ['*']],
                    (object) ['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                    (object) ['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                    (object) ['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
                ]
            );

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

                $el->due_date = Date::date($el->due_date);
                !empty($el->amount_paid) ? $el->amount_paid = Util::maskMoney($el->amount_paid) : '';
                $totalInstallments = Util::maskMoney($totalInstallments + $el->value_of_installments);
                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
                !empty($el->value_of_installments) ? $el->value_of_installments = Util::maskMoney($el->value_of_installments) : '';
            }, [$installments]);

            echo json_encode(['error' => $response->error, 'message' => $response->message, 'lastInstallment' => $nextInstallment, 'billsToPayInstallment' => $installments, 'totalInstallments' => $totalInstallments]);
            exit;
        } else {
            echo json_encode(['error' => $response->error, 'message' => $response->message,]);
            exit;
        }
    }
}
