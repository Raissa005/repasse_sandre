<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Date;
use RR\libs\Util;

class Attendance extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'attendance';
        $joins = [
            (object)[
                'table' => "users",
                'join' => "inner",
                'where' => "users.id = {$this->table}.created_by"
            ],
            (object)[
                'table' => "branch",
                'join' => "inner",
                'where' => "branch.id = {$this->table}.id_branch"
            ],
            (object)[
                'table' => "communication_channels",
                'join' => "inner",
                'where' => "communication_channels.id = {$this->table}.id_communication_channel"
            ],
            (object)[
                'table' => "attendance_status",
                'join' => "inner",
                'where' => "attendance_status.id = {$this->table}.id_status"
            ],
            (object)[
                'table' => "cities",
                'join' => "inner",
                'where' => "cities.id = {$this->table}.id_city"
            ],
        ];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItems($filters = [], $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        foreach ($filters as $column => $value) {
            if ($value != '') {
                $table = $this->table;

                if (in_array($column, ['status', 'id_branch', 'created_by'])) {
                    $filtersQuery .= " AND {$table}.{$column} = :{$table}_{$column}";
                } else if (in_array($column, [])) {
                    $filtersQuery .= " AND {$table}.{$column} IN (" . (implode(", ", $value)) . ")";
                } else if (in_array($column, ['name'])) {
                    $filtersQuery .= " AND ucase({$table}.{$column}) LIKE ucase(:{$table}_{$column})";
                    $value = "%" . $value . "%";
                } else if ($column == 'id_status_not_in') {
                    $filtersQuery .= " AND {$table}.id_status NOT IN (" . (implode(", ", $value)) . ")";
                }

                if (in_array($column, ['name', 'status', 'id_branch', 'created_by'])) {
                    $parameters[":{$table}_{$column}"] = $value;
                }
            }
        }

        $filtersQuery = trim($filtersQuery);

        $sql = "SELECT
                    {$this->table}.*
                FROM {$this->table}
                WHERE TRUE $filtersQuery";

        $sql .= isset($options['groupBy']) ? " GROUP BY {$options['groupBy']}" : " GROUP BY {$this->table}.id";
        $sql .= isset($options['orderBy']) ? " ORDER BY {$options['orderBy']}" : " ORDER BY {$this->table}.id ASC";

        $sqlRows = $sql;

        if (isset($options['limit'])) {
            $offset = ($options['page'] - 1) * $options['limit'];
            $sql .= " LIMIT {$options['limit']} OFFSET $offset ";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        $queryRows = $this->db->prepare($sqlRows);
        $queryRows->execute($parameters);

        return (object)['data' => $query->fetchAll(), 'count' => $queryRows->rowCount()];
    }

    /**descontinuar */
    public function getAndFilterAllAttendance($rows, $filters, $page)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND atd.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (!empty($filters['id_attendance'])) {
            $filtersQuery .= " AND atd.id NOT IN (:id_attendance)";
            $parameters[':id_attendance'] = $filters['id_attendance'];
        }

        if (isset($filters['id_status_not_in']) && !empty($filters['id_status_not_in']) && $filters['id_status_not_in'] != "") {
            $filtersQuery .= " AND atd.id_status NOT IN (";
            foreach ($filters['id_status_not_in'] as $status) {
                $filtersQuery .= "$status, ";
            }
            $filtersQuery = rtrim($filtersQuery, ", ");
            $filtersQuery .= ")";
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '' && $filters['id_branch'] != '0') {
            $filtersQuery .= " AND (atd.id_branch = :id_branch OR (atd.id_branch is null OR atd.id_branch = ''))";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        if (isset($filters['created_by']) && $filters['created_by'] != '') {
            $filtersQuery .= " AND atd.created_by = :created_by";
            $parameters[':created_by'] = $filters['created_by'];
        }

        if (isset($filters['id_status']) && $filters['id_status'] != '') {
            $filtersQuery .= " AND atd.id_status = :id_status";
            $parameters[':id_status'] = $filters['id_status'];
        }

        if (isset($filters['created_at_inicial']) && $filters['created_at_inicial'] != '') {
            $filtersQuery .= " AND atd.created_at <= :created_at_inicial";
            $parameters[':created_at_inicial'] = $filters['created_at_inicial'];
        }

        if (isset($filters['created_at_final']) && $filters['created_at_final'] != '') {
            $filtersQuery .= " AND atd.created_at >= :created_at_final";
            $parameters[':created_at_final'] = $filters['created_at_final'];
        }

        if (isset($filters['with_return_date']) && $filters['with_return_date'] != '') {
            if ($filters['with_return_date'] == 1) {
                $filtersQuery .= " AND (atd.return_date is not null AND atd.return_date != '0000-00-00 00:00:00')  ";
            } else if ($filters['with_return_date'] == 2) {
                $filtersQuery .= " AND (atd.return_date is null OR atd.return_date = '0000-00-00 00:00:00' ) ";
            }
        }

        if (!empty($filters['date_type'])) {
            if ($filters['date_type'] == 1) {
                if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
                    $filtersQuery .= " AND atd.opening_date BETWEEN :date_start AND :date_end";
                    $parameters[':date_start'] = $filters['date']['start'];
                    $parameters[':date_end'] = date('Y-m-d', strtotime("{$filters['date']['end']} +1 day"));
                } else {
                    if (!empty($_GET['date']['start'])) {
                        $filtersQuery .= " AND atd.opening_date >= :date_start";
                        $parameters[':date_start'] = $filters['date']['start'];
                    }

                    if (!empty($_GET['date']['end'])) {
                        $filtersQuery .= " AND atd.opening_date <= :date_end";
                        $parameters[':date_end'] = date('Y-m-d', strtotime("{$filters['date']['end']} +1 day"));
                    }
                }
            } else if ($filters['date_type'] == 2) {
                if (!empty($filters['date']['start']) && !empty($filters['date']['end'])) {
                    $filtersQuery .= " AND atd.return_date BETWEEN :date_start AND :date_end";
                    $parameters[':date_start'] = $filters['date']['start'];
                    $parameters[':date_end'] = date('Y-m-d', strtotime("{$filters['date']['end']} +1 day"));
                } else {
                    if (!empty($_GET['date']['start'])) {
                        $filtersQuery .= " AND atd.return_date >= :date_start AND (atd.return_date IS NOT NULL AND atd.return_date != '')";
                        $parameters[':date_start'] = $filters['date']['start'];
                    }

                    if (!empty($_GET['date']['end'])) {
                        $filtersQuery .= " AND atd.return_date >= :date_end AND (atd.return_date IS NOT NULL AND atd.return_date != '')";
                        $parameters[':date_end'] = date('Y-m-d', strtotime("{$filters['date']['end']} +1 day"));
                    }
                }
            }
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(atd.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['start_date']) && $filters['start_date'] != "") {
            $filtersQuery .= " AND ( atd.created_at >= :start_date_created ) ";
            $parameters[':start_date_created'] = date('Y-m-d', strtotime($filters['start_date']));
        }

        if (isset($filters['end_date']) && $filters['end_date'] != "") {
            $filtersQuery .= " AND ( atd.created_at <= :end_date_created) ";
            $parameters[':end_date_created'] = date('Y-m-d', strtotime("{$filters['end_date']} +1 month -1 day"));
        }

        if (isset($filters['id_seller_manager']) && $filters['id_seller_manager'] != "") {
            $filtersQuery .= " AND ( atd.created_by = :id_seller_manager OR mst.id_manager = :id_seller_manager) ";
            $parameters[':id_seller_manager'] = $filters['id_seller_manager'];
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    atd.*,
                    u.name as user_name, u.profile_capa, u.profile_cont, u.profile_ext,
                    ats.id as id_status, ats.name as status_name,
                    bra.name as branch_name,
                    cc.name as communication_name,
                    c.name as city_name,
                    (   SELECT
                            phone
                        FROM attendance_phones ap
                        WHERE atd.id = ap.id_attendance
                        ORDER BY ap.id ASC LIMIT 1
                    ) AS phone,
                    (   SELECT
                            phone
                        FROM attendance_phones ap
                        WHERE atd.id = ap.id_attendance AND ap.whatsapp = 1
                        ORDER BY ap.id ASC LIMIT 1
                    ) AS phone_whatsapp
                FROM attendance atd
                INNER JOIN users u ON u.id = atd.created_by
                INNER JOIN branch bra ON bra.id = atd.id_branch
                INNER JOIN communication_channels cc ON cc.id = atd.id_communication_channel
                INNER JOIN attendance_status ats ON ats.id = atd.id_status
                LEFT JOIN cities c ON atd.id_city = c.id
                LEFT JOIN manager_team mst ON mst.id_seller = atd.created_by
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " atd.id ASC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    /** descontinuar */
    public function getAllAttendance()
    {
        $sql = "SELECT
                    *
                FROM attendance atd
                WHERE TRUE
                ORDER BY atd.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getAttendanceById($id)
    {
        $sql = "SELECT
                    atd.id, atd.name, atd.id_branch, atd.opening_date, atd.return_date, atd.id_communication_channel, cc.name as communication_channel, atd.id_status, atd.description, atd.created_by, atd.email, atd.id_city,
                    atd.state, c.name as name_city, u.name as user_name, atd.classification
                FROM attendance atd
                LEFT JOIN cities c ON c.id = atd.id_city
                LEFT JOIN users u ON u.id = atd.created_by
                LEFT JOIN communication_channels cc ON cc.id = atd.id_communication_channel
                WHERE atd.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAttendanceTimelineIdAttendance($attendanceId)
    {
        $sql = "SELECT
                    attd.id, attd.id_attendance, attd.comment, attd.status_icon, si.name as icon, attd.status_timeline, attd.url_attachment, attd.name_attachment, attd.status,
                    attd.created_at, attd.created_by, u.name as user_name
                FROM attendance_timeline attd
                LEFT JOIN users u ON u.id = attd.created_by
                LEFT JOIN status_icon si ON attd.status_icon = si.id
                WHERE attd.status = true
                AND attd.id_attendance = :id
                ORDER BY attd.id DESC ";

        $query = $this->db->prepare($sql);
        $parameters[':id'] = $attendanceId;
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAndFilterAllPropertiesPresentationByAttendance($attendanceId, $filters = [], $rows = 0, $page = 0)
    {
        $parameters = [];
        $filtersQuery = '';
        if (!isset($filters['status_property_presentation'])) {
            $filtersQuery .= " AND dp.status = 1";
        }

        if (!isset($filters['status_attendance'])) {
            $filtersQuery .= " AND a.status = 1";
        }

        if (!isset($filters['status_property'])) {
            $filtersQuery .= " AND p.status = 1";
        }

        $filtersQuery .= " AND a.id = :id_attendance";
        $parameters[':id_attendance'] = $attendanceId;

        if (isset($filters['status_property_presentation']) && $filters['status_property_presentation'] != '') {
            $filtersQuery .= " AND dp.status = :status_property_presentation";
            $parameters[':status_property_presentation'] = $filters['status_property_presentation'];
        }

        if (isset($filters['status_attendance']) && $filters['status_attendance'] != '') {
            $filtersQuery .= " AND a.status = :status_attendance";
            $parameters[':status_attendance'] = $filters['status_attendance'];
        }

        if (isset($filters['status_property']) && $filters['status_property'] != '') {
            $filtersQuery .= " AND p.status = :status_property";
            $parameters[':status_property'] = $filters['status_property'];
        }

        if (isset($filters['ids_properties']) && is_array($filters['ids_properties'])) {
            if (!empty($filters['ids_properties'])) {
                $filtersQuery .= " AND dp.id_product IN (";

                foreach ($filters['ids_properties'] as $value) {
                    $filtersQuery .= "$value, ";
                }

                $filtersQuery = rtrim($filtersQuery, ", ");
                $filtersQuery .= ")";
            }
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    dp.*,
                    p.name as product_name, p.cod as product_cod, p.uf_state as product_uf, p.value as product_value, p.installment_value, p.status as product_status, p.slugify as product_slugify,
                    ppt.name as property_type_name,
                    ppc.name as property_category_name,
                    c.name as city_name,
                    (
                        SELECT
                            COUNT(prts.id)
                        FROM presentations prts
                        WHERE prts.id_displayed_properties = dp.id
                        AND prts.status = 1
                    ) AS count_presentations,
                    (
                        SELECT
                            COUNT(pdtimg.id)
                        FROM products_imgs pdtimg
                        WHERE p.id = pdtimg.id_product
                    ) AS count_products_imgs
                FROM displayed_properties dp
                LEFT JOIN attendance a ON a.id = dp.id_attendance
                LEFT JOIN products p ON p.id = dp.id_product
                LEFT JOIN property_type ppt ON ppt.id = p.id_residential_type
                LEFT JOIN property_category ppc ON ppc.id = p.id_property_category
                LEFT JOIN cities c ON c.id = p.id_city
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " dp.created_at DESC";

        if ($rows > 0) {
            $sql .= " LIMIT $rows OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    /**descontinuar */
    public function getImmovableResourceIdAttendance($attendanceId)
    {
        $sql = "SELECT
                    air.id, air.filter, air.value_money, air.value_text, air.created_by, air.created_at,
                    ir.id as id_immovable_resource, ir.name as immovable_resource,
                    pc.id as id_category, pc.name as category,
                    pt.id as id_property_type, pt.name as property_type,
                    u.name as user
                FROM attendance_filters_interests air
                LEFT JOIN immovable_resource ir ON ir.id = air.immovable_resource
                LEFT JOIN property_category pc ON pc.id = air.category
                LEFT JOIN property_type pt ON pt.id = air.property_type
                LEFT JOIN users u ON u.id = air.created_by
                WHERE air.id_attendance = :id
                ORDER BY air.id ASC";

        $query = $this->db->prepare($sql);
        $parameters = array("id" => $attendanceId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getFiltersInterestByAttedanceId($attendanceId)
    {
        $sql = "SELECT
                    air.*,
                    aft.name as filter_name,
                    u.name as user_name
                FROM attendance_filters_interests air
                INNER JOIN attendance_filter_type aft ON aft.id = air.id_attendance_filter_type
                INNER JOIN users u ON u.id = air.created_by
                WHERE air.id_attendance = :id
                ORDER BY air.id ASC";

        $query = $this->db->prepare($sql);
        $parameters = array("id" => $attendanceId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getFiltersInterestAttedanceId($attendanceId)
    {
        $sql = "SELECT
                    attendance_filters_interests.*,
                    attendance_filter_type.name as filter_name,
                    users.name as user_name,
                    attendance.opening_date
                FROM attendance_filters_interests
                INNER JOIN attendance_filter_type ON attendance_filter_type.id = attendance_filters_interests.id_attendance_filter_type
                INNER JOIN users ON users.id = attendance_filters_interests.created_by
                LEFT JOIN attendance ON attendance.created_by = attendance_filters_interests.created_by
                WHERE attendance_filters_interests.id_attendance = :id
                ORDER BY attendance_filters_interests.id ASC";

        $query = $this->db->prepare($sql);
        $parameters = array("id" => $attendanceId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function comparePhone($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['phone']) && $filters['phone'] != '') {
            $filtersQuery .= " AND ap.phone = :phone";
            $parameters[':phone'] = $filters['phone'];
        }

        if (isset($filters['id_branch']) && $filters['id_branch'] != '') {
            $filtersQuery .= " AND bra.id = :id_branch";
            $parameters[':id_branch'] = $filters['id_branch'];
        }

        $sql = "SELECT
                    a.*
                FROM attendance a
                LEFT JOIN attendance_phones ap ON ap.id_attendance = a.id
                LEFT JOIN branch bra ON bra.id = a.id_branch
                WHERE TRUE $filtersQuery";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function deleteProductAttendance($idAttendance, $idProduct)
    {
        $sql = "DELETE FROM `displayed_properties`
                WHERE id_attendance = $idAttendance
                AND id_product = $idProduct";

        $query = $this->db->prepare($sql);
        $parameters = array();
        $query->execute();

        return true;
    }

    public function getPresentationsIPByIdDisplayed($displayedId)
    {
        $sql = "SELECT
                    p.*
                FROM presentations p
                WHERE p.id_displayed_properties = :displayedId
                ORDER BY p.id DESC";

        $query = $this->db->prepare($sql);
        $parameters = array("displayedId" => $displayedId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getItemsForReport(): array
    {
        if (!isset($_GET['opening_date']['from'])) {
            $_GET['opening_date']['from'] = Date::year_month(date('Y-m-d')) . '-01';
        }

        if (!isset($_GET['opening_date']['to'])) {
            $_GET['opening_date']['to'] = date('Y-m-t');
        }

        $attendance_columns = [
            (object)['columns' => ['*']],
            (object)['table' => 'users', 'columns' => ['id', 'name']],
            (object)['table' => 'communication_channels', 'columns' => ['name']]
        ];

        $attendance_filters = [];

        foreach ($_GET as $key => $value) {
            if (in_array($key, ['url', 'filtering', 'column_id', 'column_customer_name', 'column_user_name', 'column_state', 'column_classification', 'return_date']) || empty($value)) continue;

            if (in_array($key, ['created_by', 'classification', 'id_communication_channel', 'state', 'status'])) {
                /** EQUAL COMPARSION */
                array_push($attendance_filters, (object)['columns' => [$key => (object)['value' => $value]]]);
                continue;
            } elseif (in_array($key, ['name'])) {
                /** LIKE COMPARISON */
                array_push($attendance_filters, (object)['columns' => [$key => (object)['comparison' => 'LIKE', 'value' => $value]]]);
                continue;
            } elseif (in_array($key, ['id_status'])) {
                /** IN COMPARISON */
                array_push($attendance_filters, (object)['columns' => [$key => (object)['comparison' => 'IN', 'value' => $value]]]);
                continue;
            }

            if (in_array($key, ['opening_date'])) {
                if (!empty($_GET['opening_date']['from']) && !empty($_GET['opening_date']['to'])) {
                    /** BETWEEN COMPARISON */
                    array_push($attendance_filters, (object)['columns' => ['opening_date' => (object)['comparison' => 'BETWEEN', 'value1' => date('Y-m-d', strtotime("{$_GET[$key]['from']} -1 day")), 'value2' => date('Y-m-d', strtotime("{$_GET[$key]['to']} +1 day"))]]]);
                    continue;
                } else {
                    if (!empty($_GET[$key]['from'])) {
                        /** FROM COMPARISON */
                        array_push($attendance_filters, (object)['columns' => [$key => (object)['comparison' => '<=', 'value' => date('Y-m-d', strtotime("{$_GET[$key]['from']}"))]]]);
                        continue;
                    }

                    if (!empty($_GET[$key]['to'])) {
                        /** TO COMPARISON */
                        array_push($attendance_filters, (object)['columns' => [$key => (object)['comparison' => '>=', 'value' => date('Y-m-d', strtotime($_GET[$key]['to']))]]]);
                        continue;
                    }
                }
            }
        }

        $attendances = $this->getWithFiltersAllItems($attendance_filters, $attendance_columns)->data;

        return $attendances;
    }

    public function getAttendancesForGraph($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if ($_SESSION['RR']->branch->current->id != 0) {
            $filtersQuery .= " AND att.id_branch = :id_branch";
            $parameters[':id_branch'] = $_SESSION['RR']->branch->current->id;
        }

        if (!empty($_GET['date']['start']) && !empty($_GET['date']['end'])) {
            $filtersQuery .= " AND att.created_at BETWEEN :date_start AND :date_end";
            $parameters[':date_start'] = $filters['date']['start'];
            $parameters[':date_end'] = $filters['date']['end'];
        } else {
            if (!empty($_GET['date']['start'])) {
                $filtersQuery .= " AND att.created_at >= :date_start";
                $parameters[':date_start'] = $filters['date']['start'];
            }

            if (!empty($_GET['date']['end'])) {
                $filtersQuery .= " AND att.created_at <= :date_end";
                $parameters[':date_end'] = $filters['date']['end'];
            }
        }

        $sql = "SELECT
                    DATE_FORMAT(att.created_at, '%d/%m/%Y') AS created_at,
                    count(att.id) AS total,
                    att.id_branch
                FROM attendance att 
                Where TRUE $filtersQuery
                AND att.status = true
                GROUP BY att.id_branch, DATE_FORMAT(att.created_at, '%d/%m/%Y')";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }
}
