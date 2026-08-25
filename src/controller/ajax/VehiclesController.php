<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Util;
use RR\model\Customer;
use RR\model\Vehicles;
use RR\model\VehicleCosts;

class VehiclesController extends Ajax
{
    public $model;

    function __construct()
    {
        $this->model = new Vehicles;

        parent::__construct();
    }

    public function getCustomerById()
    {

        $response = (new Customer)->getItemById($_POST['id']);

        echo json_encode(['error' => false, 'message' => $response ? '' : 'Fornecedor não encontrado!', 'customer' => $response]);
        exit;
    }

    public function addCosts()
    {
        if (!empty($_POST['data']) && !empty($_POST['vehicleId'])) {
            foreach ($_POST['data'] as $cost) {
                $arrPost = [
                    'status' => true,
                    'id_vehicle' => $_POST['vehicleId'],
                    'id_customer' => $cost['customerId'],
                    'description' => $cost['description'],
                    'created_by' => $_SESSION['RR']->user->id,
                    'value' => Util::unmaskMoney($cost['costValue']),
                    'created_at' => date("Y-m-d ", strtotime($cost['createdAt'])) . date("H:i:s")
                ];

                $response = (new VehicleCosts)->insert($arrPost);
            }

            echo json_encode(['error' => $response->error, 'message' => $response->message]);
            exit;
        } else {
            echo json_encode(['error' => true, 'message' => 'Lamentamos mas não foi possível finalizar sua solicitação.']);
        }
    }

    public function getCosts()
    {
        $response = (new VehicleCosts)->getWithFiltersAllItems(
            [
                (object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]],
                (object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $_POST['vehicleId']]]]
            ],
            [
                (object)['columns' => ['*']],
                (object)['table' => 'customer', 'columns' => ['name' => ['customerName']]]
            ]
        );

        array_map(function ($item) use (&$total) {
            $total += $item->value;
        }, $response->data);

        echo json_encode([
            'error' => $response ? false : true,
            'message' => $response ? '' : 'Lamentamos mas não foi possível encontrar os itens.',
            'costsList' => $response->data,
            'total' => $total
        ]);
        exit;
    }

    public function editCostList()
    {
        if (!empty($_POST)) {
            $arrPost = [
                'description' => $_POST['description'],
                'updated_by' => $_SESSION['RR']->user->id,
                'value' => Util::unmaskMoney($_POST['value']),
                'updated_at' => $_POST['updated_at'] . " " . date("H:i:s")
            ];

            $response = (new VehicleCosts)->update($arrPost, 'id', $_POST['id']);

            if (!$response->error) {
                $costList = (new VehicleCosts)->getWithFiltersAllItems(
                    [
                        (object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]],
                        (object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $_POST['vehicleId']]]]
                    ],
                    [
                        (object)['columns' => ['*']],
                        (object)['table' => 'customer', 'columns' => ['name' => ['customerName']]]
                    ]
                );

                array_map(function ($item) use (&$total) {
                    $total += $item->value;
                }, $costList->data);
            }

            echo json_encode([
                'error' => $response->error,
                'message' => $response->message,
                'newCostList' => $costList->data,
                'total' => $total
            ]);
            exit;
        } else {
            echo json_encode([
                'error' => true,
                'message' => 'Lamentamos mas não foi possível finalizar sua solicitação.',
                'newCostList' => '',
                'total' => ''
            ]);
        }
    }

    public function deleteCost()
    {
        if (!empty($_POST)) {
            $response = (new VehicleCosts)->delete($_POST['costEdit']['id']);

            if ($response) {
                $costList = (new VehicleCosts)->getWithFiltersAllItems(
                    [
                        (object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]],
                        (object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $_POST['costEdit']['id_vehicle']]]]
                    ],
                    [
                        (object)['columns' => ['*']],
                        (object)['table' => 'customer', 'columns' => ['name' => ['customerName']]]
                    ]
                );

                array_map(function ($item) use (&$total) {
                    $total += $item->value;
                }, $costList->data);
            }

            echo json_encode([
                'error' => $response->error,
                'message' => $response->message,
                'newCostList' => $costList->data,
                'total' => $total
            ]);
            exit;
        } else {
            echo json_encode([
                'error' => true,
                'message' =>
                'Lamentamos mas não foi possível finalizar sua solicitação.',
                'newCostList' => ''
            ]);
        }
    }
}
