<?php

namespace RR\controller\ajax;

use RR\components\ListingCardComponent;
use RR\components\PaginationComponent;
use RR\components\TableComponent;
use RR\core\Ajax;
use RR\libs\Pagination;
use RR\libs\ShowPage;
use RR\libs\TableDefault;
use RR\libs\Util;
use RR\model\Property;
use RR\model\PropertyType;
use RR\model\User;


class PropertyController extends Ajax
{
    public function getPropertyNameAndCodById()
    {
        /**
         * @param int/id_property
         * @return object/property
         */
        parent::sendResponse((new Property())->getItemById($_POST['id_property'], [(object)['columns' => ['id', 'cod', 'name']]]));
    }

    public function getPropertiesForPaymentModal()
    {
        /**
         * @param int/page $_POST['page']
         * @param int/rows $_POST['rows']
         * @return array/properties
         */

        $filters = [
            (object)['columns' => ['status' => (object)['value' => 1]]],
            (object)['table' => 'property_branch', 'columns' => ['id_branch' => (object)['value' => $_SESSION['RR']->branch->current->id]]],
        ];

        if (!empty($_POST['cod'])) $filters[0]->columns['cod'] = (object)['value' => $_POST['cod']];
        if (!empty($_POST['name'])) $filters[0]->columns['name'] = (object)['comparison' => 'LIKE', 'value' => $_POST['name']];

        $columns = [];

        $options = ['group' => 'this->table.id', 'order' => 'coalesce(this->table.cod, this->table.id) ASC', 'limit' => $_POST['rows'], 'page' => $_POST['page']];

        parent::sendResponse((new Property)->getWithFiltersAllItems($filters, $columns, $options)->data);
    }

    public function getAllPropertiesByResourceValue()
    {
        $data = (new Property())->getAllPropertiesByResourceValue($_POST['resourceId'], $_POST['value'], $_POST['filters'], $_POST['comparison'], $_POST['options']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getAndFilterThePropertiesForAttendance()
    {
        $data = [
            'properties' => (new Property())->getAndFilterThePropertiesForAttendance($_POST['filters'], $_POST['attendanceId'], $_POST['rows'], $_POST['page']),
            'next_properties' => (new Property())->getAndFilterThePropertiesForAttendance($_POST['filters'], $_POST['attendanceId'], $_POST['rows'], $_POST['page'] + 1)
        ];
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getNeighborhoodsByCityId()
    {
        $data = (new Property)->getNeighborhoodsByCityId($_POST['cityId']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getAllotmentsByCityId()
    {
        $data = (new Property)->getAllotmentsByCityId($_POST['cityId']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getAndFilterAllProperties()
    {
        $columns = [
            (object)['columns' => ['*']],
            (object)['table' => 'property_category', 'columns' => ['name']],
            (object)['table' => 'property_type', 'columns' => ['name']],
            (object)['table' => 'cities', 'columns' => ['name']],
        ];

        $filters = [(object)['columns' => []]];

        if (isset($_POST['name']) && !empty($_POST['name'])) {
            $filters[0]->columns['name'] = (object)['comparison' => 'LIKE', 'value' => $_POST['name']];
        }

        if (isset($_POST['cod']) && !empty($_POST['cod'])) {
            $filters[0]->columns['cod'] = (object)['value' => $_POST['cod']];
        }

        if (isset($_POST['status']) && !empty($_POST['status'])) {
            $filters[0]->columns['status'] = (object)['value' => $_POST['status']];
        }

        $data = [
            'properties' => (new Property())->getWithFiltersAllItems($filters, $columns, [
                'limit' => $_POST['rows'],
                'page' => $_POST['page'],
            ])->data,
            'next_properties' => (new Property())->getWithFiltersAllItems($filters, $columns, [
                'limit' => $_POST['rows'],
                'page' => $_POST['page'] + 1,
            ])->data,
            'all_properties' => (new Property())->getWithFiltersAllItems($filters, $columns)->data,
        ];

        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function CountThePropertiesForAttendance()
    {
        $data = (new Property)->CountThePropertiesForAttendance($_POST['filters'], $_POST['attendanceId']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function checkCode()
    {
        $filters = [
            (object)['columns' => ['cod' => (object)['comparison' => 'EQUAL', 'value' => $_POST['code']]]]
        ];
        $data = (new Property)->getWithFiltersAllItems($filters)->data;
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getResidentialTypeFromId()
    {
        $data = [
            'property_type' => (new PropertyType())->getItemById($_POST['id_residential_type'], [(object)['columns' => ['id', 'name', 'prefix']]]),
            'compareCod' => (new Property())->getWithFiltersAllItems([(object)['columns' => ['cod' => (object)['comparison' => 'EQUAL', 'value' => $_POST['codValue']]]]])->data,
        ];
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }

    public function getUsers()
    {
        $columns = [
            (object)['columns' => ['id', 'name', 'email']]
        ];

        $data = (new User())->getWithFiltersAllItems([], $columns, [])->data;
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
    }
}
