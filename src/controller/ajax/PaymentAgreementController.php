<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Date;
use RR\libs\Util;
use RR\model\User;
use RR\model\Sales;
use RR\model\Customer;
use RR\model\BranchUserPosition;
use RR\model\SalesChargePaymentAgreement;
use RR\model\ArrangementPaymentChargesInvoiceReceiveInstallment;
use RR\model\Branch;
use RR\model\CustomerType;

class PaymentAgreementController extends Ajax
{
    public $model;

    public function getFilterSupplierForSale()
    {
        $data = [];

        if (!empty($_POST)) {
            $sale = (new Sales)->getItemById($_POST['saleId']);
            $sellerUser = (new User)->getItemById($sale->created_by);
            $sellerCustomer = (new Customer)->getItemById($sellerUser->id_customer);

            $salesChargePaymentAgreement = (new SalesChargePaymentAgreement)->getWithFiltersAllItems(
                [
                    (object)[
                        'columns' => [
                            'id_sale' => (object)['comparison' => 'EQUAL', 'value' => $sale->id]
                        ]
                    ]
                ],
                [
                    (object)[
                        "table" => "customer",
                        "columns" => ["id", "name", "company_name", "fancy_name_company"]
                    ]
                ]
            );

            $customerSalesChargePaymentAgreement = array_filter(array_map(function ($customer) {
                return $customer->customer_id;
            }, $salesChargePaymentAgreement->data), function ($customer_id) {
                return !empty($customer_id);
            });

            $customers = (new customer)->getWithFiltersAllItems(
                [
                    (object)[
                        "columns" => [
                            "id" => (object)["comparison" => "NOT_IN", "value" => array_merge([!empty($sellerCustomer) ? $sellerCustomer->id : ""], $customerSalesChargePaymentAgreement)],
                            "status" => (object)["comparison" => "EQUAL", "value" => 1],
                        ]
                    ],
                    (object)[
                        "table" => "client_type_resource_types",
                        "columns" => [
                            "id_customer_type" => (object)["comparison" => "EQUAL", "value" => (new CustomerType())->getIdByName('Fornecedor')]
                        ]
                    ],
                ],
                [
                    (object)[
                        "columns" => ["id", "name", "company_name", "fancy_name_company"]
                    ]
                ],
                [
                    "orderBy" => "coalesce(this->table.fancy_name_company, this->table.company_name, this->table.name) asc"
                ]
            );

            $data = array_map(function ($customer) {
                $name = "";
                if (!empty($customer->fancy_name_company)) {
                    $name = $customer->fancy_name_company;
                } else if (!empty($customer->company_name)) {
                    $name = $customer->company_name;
                } else {
                    $name = $customer->name;
                }

                return (object)[
                    "id" => $customer->id,
                    "name" => $name
                ];
            }, $customers->data);
        }

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getFilterSupplierForBillReceiveInstallment()
    {
        $data = [];

        if (!empty($_POST)) {
            $sellerCustomer = (new Customer)->getItemById((new User)->getItemById((new Sales)->getItemById($_POST['saleId'])->created_by)->id_customer);

            $arrangementPaymentChargesInvoiceReceiveInstallment = (new ArrangementPaymentChargesInvoiceReceiveInstallment)->getWithFiltersAllItems(
                [
                    (object)[
                        "columns" => [
                            "id_bill_receive_installment" => (object)["comparison" => "EQUAL", "value" => $_POST['billReceiveInstallmentId']]
                        ]
                    ]
                ],
                [
                    (object)[
                        "table" => "customer",
                        "columns" => ["id", "name", "company_name", "fancy_name_company"]
                    ]
                ]

            );

            $customerArrangementPaymentChargesInvoiceReceiveInstallment = array_filter(array_map(function ($customer) {
                return $customer->customer_id;
            }, $arrangementPaymentChargesInvoiceReceiveInstallment->data), function ($customer_id) {
                return !empty($customer_id);
            });

            $customers = (new Customer)->getWithFiltersAllItems(
                [
                    (object)[
                        "columns" => [
                            'id' => (object)["comparison" => "NOT_IN", "value" => array_merge([$sellerCustomer->id], $customerArrangementPaymentChargesInvoiceReceiveInstallment)],
                            "status" => (object)["comparison" => "EQUAL", "value" => 1]
                        ]
                    ],
                    (object)[
                        "table" => "client_type_resource_types",
                        "columns" => [
                            "id_customer_type" => (object)["comparison" => "EQUAL", "value" => (new CustomerType())->getIdByName('Fornecedor')]
                        ]
                    ]
                ],
                [
                    (object)[
                        "columns" => ["id", "name", "company_name", "fancy_name_company"]
                    ]
                ],
                [
                    "orderBy" => "coalesce(this->table.fancy_name_company, this->table.company_name, this->table.name) asc"
                ]
            );

            $data = array_map(function ($customer) {
                $name = "";
                if (!empty($customer->fancy_name_company)) {
                    $name = $customer->fancy_name_company;
                } else if (!empty($customer->company_name)) {
                    $name = $customer->company_name;
                } else {
                    $name = $customer->name;
                }

                return (object)[
                    "id" => $customer->id,
                    "name" => $name
                ];
            }, $customers->data);
        }

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function createParticipantInPaymentArrangementOnSale()
    {
        $data = [];

        $item = (new Sales)->getItemById($_POST['saleId']);
        $branch = (new Branch)->getItemById($_SESSION['RR']->branch->current->id);

        if (!empty($_POST)) {
            $arrayPost = [
                'currency_id' => 1,
                'id_sale' => $_POST['saleId'],
                'id_cost_center' => $_POST['costCenter'],
                'id_customer' => $_POST['customerId'],
                'origin_commission' => $_POST['origin'],
                'id_form_payment' => $_POST['formPayment'],
                'percentage_commission' => $_POST['percentage'],
                'created_by' => $_SESSION['RR']->branch->current->id
            ];

            $customer = (new Customer)->getItemById($_POST['customerId']);
            $positionName = "";
            $userSearch = (new User)->getWithFiltersAllItems(
                [
                    (object)[
                        "columns" => [
                            "id_customer" => (object)["comparison" => "EQUAL", "value" => $customer->id]

                        ]
                    ]
                ]
            )->data;

            if (!empty($userSearch)) {
                $user = $userSearch[0];

                $positionSearch = (new BranchUserPosition)->getWithFiltersAllItems(
                    [
                        (object)[
                            "columns" => [
                                'id_branch' => (object)['comparison' => "EQUAL", 'value' => $_SESSION['RR']->branch->current->id],
                                'id_user' => (object)['comparison' => "EQUAL", 'value' => $user->id]
                            ]
                        ]
                    ],
                    [
                        (object)[
                            "table" => "user_position",
                            "columns" => ["id", "name"]
                        ]
                    ]

                )->data;

                if (!empty($positionSearch)) {
                    $position = $positionSearch[0];
                    $positionName = $position->user_position_name;
                    $arrayPost['id_user_position'] = $position->user_position_id;
                    $arrayPost['payment_date'] = Date::add_working_days($item->due_date, $position->days_after) ?? Date::add_working_days($item->sale_date, $position->days_after);
                } else {
                    $arrayPost['id_cost_center'] = $_POST['costCenter'];
                    $arrayPost['id_form_payment'] = $_POST['formPayment'];
                    $arrayPost['payment_date'] = Date::add_working_days($item->due_date, $branch->days_after_single) ?? Date::add_working_days($item->sale_date, $branch->days_after_single);
                }
            } else {
                $arrayPost['payment_date'] = Date::add_working_days($item->due_date, $branch->days_after_single) ?? Date::add_working_days($item->sale_date, $branch->days_after_single);
            }

            $response = (new SalesChargePaymentAgreement)->handleFormAdd($arrayPost);
            $this->error = $response->error;
            $this->message = $response->message;

            $name = "";
            if (!empty($customer->fancy_name_company)) {
                $name = $customer->fancy_name_company;
            } else if (!empty($customer->company_name)) {
                $name = $customer->company_name;
            } else {
                $name = $customer->name;
            }

            $data = [
                'id' => $response->lastId,
                'name' => $name,
                'position' => $positionName,
                'origin' => $_POST['origin'],
                'percentage' => $_POST['percentage'],
            ];
        }

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function createParticipantInPaymentArrangementOnBillReceiveInstallment()
    {
        $data = [];

        if (!empty($_POST)) {
            $arrayPost = [
                'id_bill_receive_installment' => $_POST['billReceiveInstallmentId'],
                'id_customer' => $_POST['customerId'],
                'origin_commission' => $_POST['origin'],
                'percentage_commission' => $_POST['percentage'],
            ];

            $customer = (new Customer)->getItemById($_POST['customerId']);
            $positionName = "";
            $userSearch = (new User)->getWithFiltersAllItems([
                    (object)[
                        "columns" => [
                            "id_customer" => (object)["comparison" => "EQUAL", "value" => $customer->id]

                        ]
                    ]
                ]
            )->data;

            if (!empty($userSearch)) {
                $user = $userSearch[0];

                $positionSearch = (new BranchUserPosition)->getWithFiltersAllItems(
                    [
                        (object)[
                            "columns" => [
                                'id_branch' => (object)['comparison' => "EQUAL", 'value' => $_SESSION['RR']->branch->current->id],
                                'id_user' => (object)['comparison' => "EQUAL", 'value' => $user->id]
                            ]
                        ]
                    ],
                    [
                        (object)[
                            "table" => "user_position",
                            "columns" => ["id", "name"]
                        ]
                    ]

                )->data;

                if (!empty($positionSearch)) {
                    $position = $positionSearch[0];
                    $positionName = $position->user_position_name;
                    $arrayPost['id_user_position'] = $position->user_position_id;
                } else {
                    $arrayPost['id_cost_center'] = $_POST['costCenter'];
                    $arrayPost['id_form_payment'] = $_POST['formPayment'];
                }
            }

            $response = (new ArrangementPaymentChargesInvoiceReceiveInstallment)->handleFormAdd($arrayPost);
            $this->error = $response->error;
            $this->message = $response->message;

            $name = "";
            if (!empty($customer->fancy_name_company)) {
                $name = $customer->fancy_name_company;
            } else if (!empty($customer->company_name)) {
                $name = $customer->company_name;
            } else {
                $name = $customer->name;
            }

            $data = [
                'id' => $response->lastId,
                'name' => $name,
                'position' => $positionName,
                'origin' => $_POST['origin'],
                'percentage' => $_POST['percentage'],
            ];
        }

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function removeParticipantFromPaymentAgreementOfTheInvoiceReceiveInstallment()
    {
        if (!empty($_POST)) {
            $response = (new ArrangementPaymentChargesInvoiceReceiveInstallment)->handleFormRemove($_POST['id']);

            if (!$this->error) {
                $this->error = $response->error;
                $this->message = $response->message;
            }

            echo json_encode(['error' => $this->error, 'message' => $this->message]);
        }
    }
}
