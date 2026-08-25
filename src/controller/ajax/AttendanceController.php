<?php

namespace RR\controller\ajax;

use RR\core\Ajax;
use RR\libs\Pagination;
use RR\libs\Util;
use RR\model\Attendance;
use RR\model\AttendanceFiltersInterests;
use RR\model\Branch;
use RR\model\DisplayedProperties;
use RR\model\ImmovableResource;
use RR\model\Property;
use RR\model\PropertyOwnershipFeature;

class AttendanceController extends Ajax
{
    public function getFiltersInterestByAttedanceId()
    {
        $data = (new Attendance())->getFiltersInterestByAttedanceId($_POST['id']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getPropertiesByFilteringByAttendanceInterest()
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
                        $filters_interest['feature'] = json_decode($interest->json);
                        break;
                    case '2':
                        $filters_interest['category'] = json_decode($interest->json);
                        break;
                    case '3':
                        $filters_interest['location'] = json_decode($interest->json);
                        break;
                    case '4':
                        $filters_interest['price'] = json_decode($interest->json);
                        break;
                    case '5':
                        $filters_interest['type'] = json_decode($interest->json);
                        break;
                }
            }

            $properties_ids = [];
            if (!empty($filters_interest['feature'])) {
                $property_ids_filter = false;
                foreach ($filters_interest['feature'] as $interest) {
                    $property_ownership_feature_filters = [
                        (object)['table' => 'property_branch', 'columns' => ['id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id]]],
                        (object)['table' => 'products', 'columns' => ['status' => (object)['value' => 1]]]
                    ];

                    if ($property_ids_filter) {
                        $property_ownership_feature_filters[] = (object)['table' => 'products', 'columns' => ['id' => (object)['comparison' => 'in', 'value' => $properties_ids]]];
                        if (empty($properties_ids)) {
                            break;
                        }
                    }

                    $resource = (new ImmovableResource())->getItemById($interest->resource);
                    $property_ownership_feature_filters[] = (object)['table' => 'property_type_resources', 'columns' => ['id_immovable_resource' => (object)['value' => $interest->resource]]];
                    if ($resource->data_type == 1 || $resource->data_type == 4) {
                        $property_ownership_feature_filters[] = (object)['columns' => ['value' => (object)['comparison' => 'LIKE', 'value' => $interest->value]]];
                    } else {
                        $property_ownership_feature_filters[] = (object)['where' => "AND this->table.value >= " . intval($interest->value) . ""];
                    }

                    $properties_ids = array_map(function ($property) {
                        return $property->products_id;
                    }, (new PropertyOwnershipFeature)->getWithFiltersAllItems(
                        $property_ownership_feature_filters,
                        [(object)['table' => 'products', 'columns' => ['id']]],
                        ['group' => 'products.id']
                    )->data);
                    $property_ids_filter = true;
                }
            }

            $properties_filters = [
                (object)['table' => 'property_branch', 'columns' => ['id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id]]],
                (object)['columns' => ['status' => (object)['value' => 1]]],
                (object)['table' => 'sales', 'where' => "AND ( NOT (this->table.id_product is not null AND this->table.status = 1))"]
            ];

            $displayed_properties = (new DisplayedProperties)->getWithFiltersAllItems([
                (object)['columns' => ['id_attendance' => (object)['value' => $_POST['attendance_id']]]],
                (object)['table' => 'products', 'columns' => ['status' => (object)['value' => 1]]],
            ])->data;

            if (!empty($displayed_properties) && !empty($properties_ids)) {
                $not_property_ids = array_column($displayed_properties, 'id_product');
                $properties_filters[] = (object)['columns' => ['id' => (object)['comparison' => 'in', 'value' => array_values(array_filter($properties_ids, function ($id) use ($not_property_ids) {
                    return !in_array($id, $not_property_ids);
                }))]]];
            } else {
                if (!empty($filters_interest['feature'])) {
                    if (!empty($properties_ids)) {
                        $properties_filters[] = (object)['columns' => ['id' => (object)['comparison' => 'in', 'value' => $properties_ids]]];
                    } else {
                        $properties_filters[] = (object)['where' => 'AND FALSE'];
                    }
                }
                if (!empty($displayed_properties)) {
                    $properties_filters[] = (object)['columns' => ['id' => (object)['comparison' => 'not_in', 'value' => array_column($displayed_properties, 'id_product')]]];
                }
            }

            if (!empty($filters_interest['category'])) {
                $properties_filters[] = (object)['columns' => ['id_property_category' => (object)['comparison' => 'in', 'value' => $filters_interest['category']]]];
            }
            if (!empty($filters_interest['location'])) {
                if ($filters_interest['location']->id_city) {
                    $properties_filters[] = (object)['columns' => ['id_city' => (object)['comparison' => 'in', 'value' => $filters_interest['location']->id_city]]];
                }

                if (!empty($filters_interest['location']->neighborhood)) {
                    foreach ($filters_interest['location']->neighborhood as $neighborhood) {
                        $properties_filters[] = (object)['where' => " AND neighborhood LIKE '$neighborhood' OR neighborhood LIKE '$neighborhood'"];
                    }
                }
            }
            if (!empty($filters_interest['type'])) {
                $properties_filters[] = (object)['columns' => ['id_residential_type' => (object)['comparison' => 'in', 'value' => $filters_interest['type']]]];
            }
            if (!empty($filters_interest['price'])) {
                if (isset($filters_interest['price']->start_price) && isset($filters_interest['price']->end_price)) {
                    $properties_filters[] = (object)['columns' => ['value' => (object)['comparison' => 'BETWEEN', 'value1' => $filters_interest['price']->start_price, 'value2' => $filters_interest['price']->end_price]]];
                } else if (isset($filters_interest['price']->start_price)) {
                    $properties_filters[] = (object)['columns' => ['value' => (object)['comparison' => 'BIGGER_EQUAL', 'value' => $filters_interest['price']->start_price]]];
                } else if (isset($filters_interest['price']->end_price)) {
                    $properties_filters[] = (object)['columns' => ['value' => (object)['comparison' => 'LESSER_EQUAL', 'value' => $filters_interest['price']->end_price]]];
                }
            }

            $limit = 10;
            $properties = (new Property)->getWithFiltersAllItems($properties_filters, [
                (object)['columns' => ['*']],
                (object)['table' => 'cities', 'columns' => ['name', 'uf']],
                (object)['table' => 'property_category', 'columns' => ['name']],
                (object)['table' => 'property_type', 'columns' => ['name']],
            ], ['limit' => $limit, 'page' => $_POST['page'], 'order' => "this->table.cod"]);
            array_map(function ($property) {
                $property->cities_name = Util::titleCase($property->cities_name);
                $property->value = Util::maskMoney($property->value);
            }, $properties->data);

            echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $properties->data, 'pagination' => (new Pagination)->pages($properties->count, $limit, 10, $_POST['page'])]);
            exit;
        }
    }
}
