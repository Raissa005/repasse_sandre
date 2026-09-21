<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Pagination;
use RR\libs\Util;
use RR\model\Attendance;
use RR\model\AttendanceDisplayedVehicles;
use RR\model\AttendanceFiltersInterests;
use RR\model\Vehicles;

class AttendanceController extends Ajax
{
    public function getFiltersInterestByAttedanceId()
    {
        $data = (new Attendance())->getFiltersInterestByAttedanceId($_POST['id']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getVehiclesByFilteringByAttendanceInterest()
    {
        if (!empty($_POST)) {
            $filters_interest = [];

            $attendance_filters_interests = (new AttendanceFiltersInterests)->getWithFiltersAllItems(
                [(object)['columns' => ['id_attendance' => (object)['value' => $_POST['attendance_id']]]]],
                [(object)['columns' => ['*']], (object)['table' => 'attendance_filter_type', 'columns' => ['name']]]
            )->data;

            foreach ($attendance_filters_interests as $interest) {
                switch ($interest->id_attendance_filter_type) {
                    case '1':
                        $filters_interest['brand_model'] = json_decode($interest->json);
                        break;
                    case '2':
                        $filters_interest['price'] = json_decode($interest->json);
                        break;
                    case '3':
                        $filters_interest['year_km'] = json_decode($interest->json);
                        break;
                }
            }

            $displayedVehicles = (new AttendanceDisplayedVehicles)->getWithFiltersAllItems([
                (object)['columns' => ['id_attendance' => (object)['value' => $_POST['attendance_id']]]],
                (object)['table' => 'vehicles', 'columns' => ['status' => (object)['value' => 1]]],
            ])->data;
            $excludeVehicleIds = array_column($displayedVehicles, 'id_vehicle');

            $vehicleFilters = [];
            if (!empty($filters_interest['brand_model'])) {
                $vehicleFilters['brand_model_pairs'] = $filters_interest['brand_model'];
            }
            if (!empty($filters_interest['price'])) {
                if (isset($filters_interest['price']->start_price)) {
                    $vehicleFilters['start_price'] = $filters_interest['price']->start_price;
                }
                if (isset($filters_interest['price']->end_price)) {
                    $vehicleFilters['end_price'] = $filters_interest['price']->end_price;
                }
            }
            if (!empty($filters_interest['year_km'])) {
                if (isset($filters_interest['year_km']->year_from)) {
                    $vehicleFilters['year_from'] = $filters_interest['year_km']->year_from;
                }
                if (isset($filters_interest['year_km']->year_to)) {
                    $vehicleFilters['year_to'] = $filters_interest['year_km']->year_to;
                }
                if (isset($filters_interest['year_km']->km_max)) {
                    $vehicleFilters['km_max'] = $filters_interest['year_km']->km_max;
                }
            }

            $limit = 10;
            $allVehicles = (new Vehicles)->getAndFilterAllByInterest($vehicleFilters, $excludeVehicleIds);
            $count = count($allVehicles);
            $vehicles = array_slice($allVehicles, ($_POST['page'] - 1) * $limit, $limit);

            array_map(function ($vehicle) {
                $vehicle->vehicle_sales_value = Util::maskMoney($vehicle->vehicle_sales_value);
            }, $vehicles);

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $vehicles, 'pagination' => (new Pagination)->pages($count, $limit, 10, $_POST['page'])]);
            exit;
        }
    }

    public function searchVehiclesForPresentation()
    {
        if (!empty($_POST)) {
            $rows = 10;
            $page = $_POST['page'];

            $filters = [
                'name' => $_POST['name'] ?? '',
                'id' => $_POST['cod'] ?? '',
                'id_brand' => $_POST['id_brand'] ?? '',
            ];

            $vehiclesModel = new Vehicles();
            $vehicles = $vehiclesModel->getAndFilterAllForSearch($filters, $rows, $page);
            $nextPage = !empty($vehiclesModel->getAndFilterAllForSearch($filters, $rows, $page + 1));

            $displayedVehicles = (new AttendanceDisplayedVehicles)->getWithFiltersAllItems([
                (object)['columns' => ['id_attendance' => (object)['value' => $_POST['attendance_id']]]],
                (object)['table' => 'vehicles', 'columns' => ['status' => (object)['value' => 1]]],
            ])->data;
            $displayedVehicleIds = array_column($displayedVehicles, 'id_vehicle');

            array_map(function ($vehicle) use ($displayedVehicleIds) {
                $vehicle->already_presented = in_array($vehicle->id, $displayedVehicleIds);
                $vehicle->vehicle_sales_value = Util::maskMoney($vehicle->vehicle_sales_value);
            }, $vehicles);

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'vehicles' => $vehicles, 'nextPage' => $nextPage]);
            exit;
        }
    }
}
