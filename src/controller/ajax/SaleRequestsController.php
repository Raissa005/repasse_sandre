<?php

namespace RR\controller\ajax;

use RR\libs\Util;
use RR\libs\Secure;
use RR\core\Ajax;
use RR\libs\Date;
use RR\model\Branch;
use RR\model\Vehicles;
use RR\model\Customer;
use RR\libs\Pagination;
use RR\model\BillReceive;
use RR\model\BillReceiveInstallment;
use RR\model\SaleRequests;
use RR\model\VehiclesRequestSale;
use stdClass;

class SaleRequestsController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new SaleRequests;

        parent::__construct();
    }

    public function getCustomersSellerAndBuyer()
    {
        if (!empty($_POST)) {
            $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);

            $customer_filters = [
                (object)['columns' => ['status' => (object)['value' => 1]]],
                (object)['table' => 'customer_branches', 'columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]],
                (object)["table" => 'client_type_resource_types', "columns" => ['id_customer_type' => (object)['comparison' => 'IN', 'value' => [1]]]]
            ];

            if (!empty($_POST['name'])) {
                $customer_filters[] = (object)['where' => " AND (
                    ucase(this->table.name) LIKE ucase(:busca_1)
                    OR ucase(this->table.fancy_name_company) LIKE ucase(:busca_2)
                    OR ucase(this->table.company_name) LIKE ucase(:busca_3))",
                    'parameters' => [
                        ':busca_1' => '%' . $_POST['name'] . '%',
                        ':busca_2' => '%' . $_POST['name'] . '%',
                        ':busca_3' => '%' . $_POST['name'] . '%'
                    ]];
            }

            if ($_SESSION['RR']->profile->access >= 30 && $branch->restrict_owner_data == 1) {
                $customer_filters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            $response = (new Customer)->getWithFiltersAllItems(
                $customer_filters,
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'cities', 'columns' => ['name' => ['cityName'], 'uf' => ['cityUF']]]
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

    public function getVehiclesForSale()
    {
        if (!empty($_POST)) {
            $vehicleFilters = [
                (object)['columns' => ['status' => (object)['value' => 1]]],
                (object)['where' => " AND this->table.id NOT IN (SELECT vrs.id_vehicle FROM vehicles_request_sale vrs WHERE vrs.status = true AND vrs.id_vehicle = this->table.id)"]
            ];

            if (!empty($_POST['vehicles'])) {
                array_map(function ($vehicle) use (&$vehiclesId) {
                    if (isset($vehicle['vehicleId'])) {
                        $vehiclesId[] = $vehicle['vehicleId'];
                    }
                }, $_POST['vehicles']);

                if (!empty($vehiclesId)) array_push($vehicleFilters, (object)['columns' => ['id' => (object)['comparison' => 'NOT_EQUAL', 'value' => $vehiclesId]]]);
            }

            if (!empty($_POST['name'])) {
                array_push($vehicleFilters, (object)['where' => " AND (ucase(this->table.name) LIKE ucase(:busca_1) OR ucase(this->table.plate) LIKE ucase(:busca_2))", 'parameters' => [':busca_1' => '%' . $_POST['name'] . '%', ':busca_2' => '%' . $_POST['name'] . '%']]);
            }

            $response = (new Vehicles)->getWithFiltersAllItems(
                $vehicleFilters,
                [
                    (object)['columns' => ['*']]
                ],
                [
                    'limit' => $_POST['limit'],
                    'page' => $_POST['page'],
                    'groupBy' => 'this->table.id',
                    'order' => 'this->table.id ASC',
                ]
            );

            array_map(function ($vehicle) {
                $vehicle->plate = !empty($vehicle->plate) ? $vehicle->plate : ' - ';
                $vehicle->vehicle_sales_value = !empty($vehicle->vehicle_sales_value) ? Util::maskMoney($vehicle->vehicle_sales_value) : Util::maskMoney(0);
            }, $response->data);

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'vehicles' => $response->data, 'pagination' => (new Pagination)->pages($response->count, $_POST['limit'], 10, $_POST['page'])]);
            exit;
        }
    }

    public function getVehicleById()
    {
        if (!empty($_POST)) {
            $response = (new Vehicles)->getItemById(
                $_POST['vehicleId'],
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'vehicle_brands', 'columns' => ['name' => ['brand_name']]],
                    (object)['table' => 'vehicle_models', 'columns' => ['name' => ['model_name']]],
                    (object)['table' => 'vehicle_colors', 'columns' => ['name' => ['color_name']]],
                    (object)['table' => 'vehicles_request_sale', 'columns' => ['value' => ['sale_request_value'], 'value_commission']]
                ]
            );

            if ($response) {
                $error = false;
                $message = 'Item encontrado!';

                array_map(function ($item) {
                    $item->plate = !empty($item->plate) ? $item->plate : ' - ';
                    $item->sale_request_value = !empty($item->sale_request_value) ? Util::maskMoney($item->sale_request_value) : Util::maskMoney(0);
                    $item->vehicle_sales_value = !empty($item->vehicle_sales_value) ? Util::maskMoney($item->vehicle_sales_value) : Util::maskMoney(0);
                }, [$response]);
            } else {
                $error = true;
                $message = 'Veículo não encontrado!';
            }

            echo json_encode(['error' => $error, 'message' => $message, 'vehicle' => $response]);
            exit;
        }
    }

    public function addVehiclesSale()
    {
        // Mesma regra da aba Veículos (só se chega a ela como admin)
        if (!Secure::access_admin()) {
            $this->error = true;
            $this->message = 'Sem permissão para esta ação.';
            $this->sendResponse();
        }

        if (!empty($_POST['saleId'])) {
            $saleRequest = (new SaleRequests)->getItemById($_POST['saleId']);

            !empty($saleRequest->value) ? $totalSaleRequest = $saleRequest->value : $totalSaleRequest = 0;

            $countRegistered = 0;
            foreach ($_POST['vehicles'] as $vehicle) {
                $vehicleSaleRequest = (new VehiclesRequestSale)->getItemWithFilters(
                    [
                        (object)['columns' => ['id_sale_request' => (object)['comaparison' => 'EQUAL', 'value' => $saleRequest->id]]],
                        (object)['columns' => ['id_vehicle' => (object)['comaparison' => 'EQUAL', 'value' => $vehicle['vehicleId']]]]
                    ]
                );

                $arrPost = [
                    'status' => true,
                    'created_at' => date('Y-m-d H:i:s'),
                    'id_vehicle' => $vehicle['vehicleId'],
                    'id_sale_request' => $_POST['saleId'],
                    'created_by' => $_SESSION['RR']->user->id,
                    'value' => Util::unmaskMoney($vehicle['saleValue']),
                    'value_commission' => $_POST['commission'],
                    'due_date_transfer' => date('Y-m-d', strtotime('+90 days', strtotime($saleRequest->sale_date))) ?? ''
                ];

                if (!empty($vehicleSaleRequest)) {
                    $response = (new VehiclesRequestSale)->update($arrPost, 'id', $vehicleSaleRequest->id);
                } else {
                    $response = (new VehiclesRequestSale)->insert($arrPost);
                }

                if (!$response->error) {
                    $countRegistered++;
                    $totalSaleRequest += Util::unmaskMoney($vehicle['saleValue']);
                }
            }

            (new SaleRequests)->update(['value' => $totalSaleRequest], 'id', $saleRequest->id);

            if ($countRegistered == count($_POST['vehicles'])) {
                $error = false;
                $message = "Veículos cadastrados com sucesso!";
            } else {
                $error = true;
                $message = "Apenas $countRegistered foram cadastrados";
            }
        }

        echo json_encode(['error' => $error, 'message' => $message]);
        exit;
    }

    public function editVehiclesSale()
    {
        // Mesma regra da aba Veículos (só se chega a ela como admin)
        if (!Secure::access_admin()) {
            $this->error = true;
            $this->message = 'Sem permissão para esta ação.';
            $this->sendResponse();
        }

        if ($_POST['vehicles']) {
            $saleRequest = (new SaleRequests)->getItemById($_POST['saleId']);

            $vehicleRequestSale = (new VehiclesRequestSale)->getItemWithFilters(
                [
                    (object)['columns' => ['id_sale_request' => (object)['comparison' => 'EQUAL', 'value' => $_POST['saleId']]]],
                    (object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $_POST['vehicles'][0]['vehicleId']]]]
                ]
            );

            if ($vehicleRequestSale) {
                $arrPost = [
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $_SESSION['RR']->user->id,
                    'value' => Util::unmaskMoney($_POST['vehicles'][0]['saleValue']),
                    'value_commission' => $_POST['vehicles'][0]['commission']
                ];

                $response = (new VehiclesRequestSale)->update($arrPost, 'id', $vehicleRequestSale->id);
                (new SaleRequests)->update(['value' => Util::unmaskMoney($_POST['vehicles'][0]['saleValue'])], 'id', $vehicleRequestSale->id_sale_request);

                if (!$response->error) {
                    if (!empty($saleRequest->value) && $vehicleRequestSale->value) (new SaleRequests)->update(['value' => ($saleRequest->value - $vehicleRequestSale->value) + Util::unmaskMoney($_POST['vehicles'][0]['saleValue'])], 'id', $saleRequest->id);
                }
            }
        }

        echo json_encode(['error' => $response->error, 'message' => $response->message]);
        exit;
    }

    public function handleSubmitAddBillReceive()
    {
        // Mesma regra da aba Financeiro/Comissão (só aparece para admin)
        if (!Secure::access_admin()) {
            $this->error = true;
            $this->message = 'Sem permissão para esta ação.';
            $this->sendResponse();
        }

        if (!empty($_POST)) {
            $saleRequest = (new SaleRequests)->getItemById(
                $_POST['saleRequestId'],
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'customer', 'columns' => ['name' => ['name']]]
                ]
            );
            $response = new stdClass();
            $arrPost = [
                'id_cost_center' => $_POST['costCenter'],
                'created_by' => $_SESSION['RR']->user->id,
                'id_customer' => $saleRequest->id_customer,
                'id_form_of_payment' => $_POST['formPaymentId'],
                'id_branch' => $_SESSION['RR']->branch->current->id,
                'description' => "Conta a receber gerada pelo pedido de venda\n Pedido: #{$saleRequest->id}\n Vendedor: {$saleRequest->name}"
            ];

            if(isset($_POST['commission']) && $_POST['commission'] == 1 && !empty($_POST['saleBrokerId']))
            {
                if (!empty($saleRequest->id_commission_receive))
                {
                    $response = (new BillReceive)->update($arrPost, 'id', $saleRequest->id_commission_receive);
                }else
                {
                    $response = (new BillReceive)->insert($arrPost);

                    if (!$response->error)
                    {
                        unset($arrPost);

                        (new SaleRequests)->update(['id_commission_receive' => $response->lastId], 'id', $saleRequest->id);

                        if (isset($_POST['numberOfInstallments']) && $_POST['valueReference'] == 1) {
                            $valueOfInstallments = $_POST['price'] / $_POST['numberOfInstallments'];

                            list($year, $month, $day) = explode("-", $_POST['dueDate']);

                            for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                                $arrPost = [
                                    'number_portion' => $p,
                                    'id_bill_receive' => $response->lastId,
                                    'created_by' => $_SESSION['RR']->user->id,
                                    'value_installment' => $valueOfInstallments,
                                    'id_form_of_payment' => $_POST['formPaymentId'],
                                    'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                                    'status_payment' => !empty($_POST['status_payment']) ? $_POST['status_payment'] : 1,
                                    'description' => "Parcela gerada pelo pedido de venda\n Pedido: #{$saleRequest->id}",
                                    'id_purchase_broker' => $_POST['saleBrokerId']
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

                                (new BillReceiveInstallment())->insert($arrPost);
                            }
                        } else {
                            $valueOfInstallments = $_POST['price'];

                            list($year, $month, $day) = explode("-", $_POST['dueDate']);

                            for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                                $arrPost = [
                                    'number_portion' => $p,
                                    'id_bill_receive' => $response->lastId,
                                    'created_by' => $_SESSION['RR']->user->id,
                                    'value_installment' => $valueOfInstallments,
                                    'id_form_of_payment' => $_POST['formPaymentId'],
                                    'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                                    'status_payment' => !empty($_POST['status_payment']) ? $_POST['status_payment'] : 1,
                                    'description' => "Parcela gerada pelo pedido de venda\n Pedido: #{$saleRequest->id}",
                                    'id_purchase_broker' => $_POST['saleBrokerId']
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

                                (new BillReceiveInstallment())->insert($arrPost);
                            }
                        }
                    }
                }
            }else if(!isset($_POST['commission']) || $_POST['commission'] != 1){
                if (!empty($saleRequest->id_bill_receive)) {

                    $response = (new BillReceive)->update($arrPost, 'id', $saleRequest->id_bill_receive);
                } else {
                    $response = (new BillReceive)->insert($arrPost);

                    if (!$response->error) {
                        unset($arrPost);

                        (new SaleRequests)->update(['id_bill_receive' => $response->lastId], 'id', $saleRequest->id);

                        if (isset($_POST['numberOfInstallments']) && $_POST['valueReference'] == 1) {
                            $valueOfInstallments = $_POST['price'] / $_POST['numberOfInstallments'];

                            list($year, $month, $day) = explode("-", $_POST['dueDate']);

                            for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                                $arrPost = [
                                    'number_portion' => $p,
                                    'id_bill_receive' => $response->lastId,
                                    'created_by' => $_SESSION['RR']->user->id,
                                    'value_installment' => $valueOfInstallments,
                                    'id_form_of_payment' => $_POST['formPaymentId'],
                                    'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                                    'status_payment' => !empty($_POST['status_payment']) ? $_POST['status_payment'] : 1,
                                    'description' => "Parcela gerada pelo pedido de venda\n Pedido: #{$saleRequest->id}"
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

                                (new BillReceiveInstallment())->insert($arrPost);
                            }
                        } else {
                            $valueOfInstallments = $_POST['price'];

                            list($year, $month, $day) = explode("-", $_POST['dueDate']);

                            for ($p = 1; $p <= $_POST['numberOfInstallments']; $p++) {
                                $arrPost = [
                                    'number_portion' => $p,
                                    'id_bill_receive' => $response->lastId,
                                    'created_by' => $_SESSION['RR']->user->id,
                                    'value_installment' => $valueOfInstallments,
                                    'id_form_of_payment' => $_POST['formPaymentId'],
                                    'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                                    'status_payment' => !empty($_POST['status_payment']) ? $_POST['status_payment'] : 1,
                                    'description' => "Parcela gerada pelo pedido de venda\n Pedido: #{$saleRequest->id}"
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

                                (new BillReceiveInstallment())->insert($arrPost);
                            }
                        }
                    }
                }
            }

            if (!isset($response->item) || empty($response->item)) {
                $response->item = $saleRequest;
            }

            echo json_encode(['error' => $response->error, 'message' => $response->message, 'billReceive' => $response->item]);
            exit;
        }
    }

    public function addBillReceiveInstallment()
    {
        // Mesma regra da aba Financeiro/Comissão (só aparece para admin)
        if (!Secure::access_admin()) {
            $this->error = true;
            $this->message = 'Sem permissão para esta ação.';
            $this->sendResponse();
        }


        $allInstallments =  (new SaleRequests())->getWithFiltersAllItems(
            [
                (object) [
                    'columns' => [
                        'id' => (object) [
                            'comparison' => 'EQUAL',
                            'value' => $_POST['saleRequestId']
                        ]
                    ]
                ],
            ],
            [
                (object) [
                    'columns' => [
                        'id_bill_receive' => ['id_bill_receive'],
                        'id_commission_receive' => ['id_commission_receive']
                    ]
                ],
                (object) [
                    'table' => 'bill_receive',
                    'columns' => [
                        'id' => ['id_bill_receive']
                    ]
                ],
                (object) [
                    'table' => 'bill_receive_installment',
                    'columns' => [
                        'number_portion' => ['number_portion'],
                        'value_installment' => ['value_installment']
                    ]
                ]
            ]
        )->data;

        if (is_object($allInstallments)) {
            $allInstallments = get_object_vars($allInstallments);
        }

        $totalInstallments = 0;

        foreach ($allInstallments as $i) {
            $totalInstallments += $i->value_installment;
        }

        $lastInstallment = end($allInstallments);
        $nextInstallment = ++$lastInstallment->number_portion;

        if(isset($_POST['commission']) && $_POST['commission'] == 1 && !empty($_POST['saleBrokerId'])){
            $arrPost = [
                'number_portion' => $nextInstallment,
                'description' => $_POST['description'],
                'created_by' => $_SESSION['RR']->user->id,
                'id_bill_receive' =>  $allInstallments[0]->id_commission_receive,
                'id_form_of_payment' => $_POST['formPaymentId'],
                'value_installment' => $_POST['valueInstallment'],
                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL,
                'id_purchase_broker' => $_POST['saleBrokerId']
            ];
        }else{
            $arrPost = [
                'number_portion' => $nextInstallment,
                'description' => $_POST['description'],
                'created_by' => $_SESSION['RR']->user->id,
                'id_bill_receive' => $allInstallments[0]->id_bill_receive,
                'id_form_of_payment' => $_POST['formPaymentId'],
                'value_installment' => $_POST['valueInstallment'],
                'due_date' => !empty(trim($_POST['dueDate'])) ? $_POST['dueDate'] : NULL
            ];
        }

        $response = (new BillReceiveInstallment)->insert($arrPost);

        if (!$response->error) {
            $installments = (new BillReceiveInstallment())->getItemById(
                $response->lastId,
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'cost_center', 'columns' => ['name' => ['cost_center_name']]],
                    (object)['table' => 'form_of_payment', 'columns' => ['name' => ['form_of_payment_name']]],
                    (object)['table' => 'customer', 'columns' => ['name' => ['customer_name'], 'company_name' => ['customer_company_name'], 'fancy_name_company' => ['customer_fancy_name_company']]]
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
                $totalInstallments = Util::maskMoney($totalInstallments + $el->value_installment);
                $el->customer_name = $el->customer_fancy_name_company ?? $el->customer_company_name ?? $el->customer_name;
                !empty($el->value_installment) ? $el->value_installment = Util::maskMoney($el->value_installment) : '';
            }, [$installments]);

            echo json_encode(['error' => $response->error, 'message' => $response->message, 'lastInstallment' => $nextInstallment, 'billReceiveInstallment' => $installments, 'totalInstallments' => $totalInstallments]);
            exit;
        } else {
            echo json_encode(['error' => $response->error, 'message' => $response->message,]);
            exit;
        }
    }
}
