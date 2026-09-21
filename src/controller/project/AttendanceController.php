<?php

namespace RR\controller\project;

use RR\libs\Date;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use RR\libs\Util;
use RR\model\Attendance;
use RR\model\Customer;
use RR\model\User;
use RR\model\Branch;
use RR\libs\FileUploader;
use RR\libs\DeleteFile;
use RR\libs\Secure;
use RR\model\AttendanceStatus;
use PDOException;
use RR\model\AttendanceFiltersInterests;
use RR\model\AttendanceDisplayedVehicles;
use RR\model\ManagerTeam;
use RR\model\Vehicles;
use RR\model\VehicleBrands;

use function RR\Controller\redirect;

class AttendanceController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'attendance';
        $this->dir = 'attendance';
        $this->model = new Attendance();
        $this->table = 'attendance';
        parent::__construct($this->route);

        parent::addStyle(URL . "css/" . CSSVERSION . "/" . $this->dir . "/attendance.css");
    }

    public function index()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/kanban.js");
        $this->addStyle(URL . "css/" . CSSVERSION . "/" . $this->dir . "/kanban.css");

        $attendanceModel = new Attendance();
        $attendanceStatusModel = new AttendanceStatus();
        $userModel = new User();

        if (!Secure::seller_manager()) {
            $_GET['id_seller_manager'] = $_SESSION['RR']->user->id;
        }

        /**Status de atendimento para o filtro de atendimento */
        $filterAttendanceStatus = array("status" => true);
        $attendanceStatusForFilter = $attendanceStatusModel->getAndFilterAllItem($filterAttendanceStatus, 0)->data;

        if (isset($_GET['attendance_status_in'])) {
            $filterAttendanceStatus['attendance_status_in'] = $_GET['attendance_status_in'];
        } else {
            $filterAttendanceStatus['standard_filter'] = 1;
        }
        /**Filtro de atendimento */
        $attendanceStatus = $attendanceStatusModel->getAndFilterAllItem($filterAttendanceStatus, 0)->data;

        /**Filtro usuários */
        $filterUser = array('id_branch_and_profile' => $_SESSION['RR']->branch->current->id, "order" => " up.id ASC, u.name ASC ");

        $users = ($_SESSION['RR']->profile->id != 7 ? $userModel->getAndFilterAllUsers($filterUser, 0)->data : $userModel->getAllSellersFromManagerId($_SESSION['RR']->user->id, $_SESSION['RR']->branch->current->id));

        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!Secure::access_secretary()) {
            $_GET['created_by'] = $_SESSION['RR']->user->id;
        }

        $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
        $_GET['order'] = ' -(atd.return_date) DESC';

        $attendanceKanban = [];
        foreach ($attendanceStatus as $status) {
            $_GET['id_status'] = $status->id;

            array_push(
                $attendanceKanban,
                (object) [
                    "statusId" => $status->id,
                    "icon_status" => $status->icon_status,
                    "status_name" => $status->name,
                    "box_color" => $status->box_color,
                    "attendances" => $attendanceModel->getAndFilterAllAttendance(0, $_GET, 0)
                ]
            );
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function addItem()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/add.js");

        $modelGenerico = new ModelGenerico();
        $branchModel = new Branch();
        $userModel = new User();
        $attendanceStatusModel = new AttendanceStatus();

        $branch = $branchModel->getItemById8161($_SESSION['RR']->branch->current->id);
        $city = $modelGenerico->getItemById8161($branch->id_city, "cities");

        $customers_filter = [
            (object)['columns' => ['status' => (object)['value' => 1]]],
            (object)["table" => "customer_branches", "columns" => ["id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]]]
        ];
        if (!Secure::access_admin()) {
            $customers_filter[] = (object)['columns' => ['created_by' => (object)['value' => $_SESSION['RR']->user->id]]];
        }
        $filterUsers = array('id_branch_and_profile' => $_SESSION['RR']->branch->current->id, "order" =>  "up.id ASC, u.name ASC");

        $statusAttendance = $attendanceStatusModel->getAndFilterAllItem(["status" => 1], 0)->data;
        $communication_channels = $modelGenerico->getAllItens("communication_channels");
        $states = $modelGenerico->getAllItens("states");
        $cities = $branchModel->getCitiesByState($city->uf);
        $customers = (new Customer)->getWithFiltersAllItems($customers_filter)->data;
        $users = $userModel->getAndFilterAllUsers($filterUsers, 0)->data;

        $today = date('Y-m-d\TH:i');

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addItem.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddAttendance()
    {
        Secure::check_post_method($this->route . "/addItem");

        $gerenciaPost = new GerenciaPost();
        $arrPost = array(
            "name" => $_POST["name"],
            "email" => $_POST["email"],
            "id_city" => $_POST["id_city"],
            "state" => $_POST["state"],
            "id_branch" => $_SESSION['RR']->branch->current->id,
            "id_communication_channel" => $_POST["id_communication_channel"],
            "id_status" => $_POST["id_status"],
            "description" => $_POST["description"],
            "created_by" => $_SESSION['RR']->user->id,
        );

        if (!empty(trim($_POST["opening_date"]))) {
            $arrPost["opening_date"] = $_POST["opening_date"];
        }

        if (!empty(trim($_POST["return_date"]))) {
            $arrPost["return_date"] = $_POST["return_date"];
        }

        if (Secure::access_secretary()) {
            $arrPost["created_by"] = $_POST['created_by'];
        }

        try {
            $attendanceId = $gerenciaPost->insert7181($arrPost, $this->table, true, false);

            $arrPostPhone = array(
                'id_attendance' => $attendanceId,
                'name' => "Principal",
                'phone' => Util::removeNonNumericCharacters($_POST['phone']),
                'whatsapp' => isset($_POST['whatsapp']) ? '1' : '0'
            );

            $gerenciaPost->insert7181($arrPostPhone, "attendance_phones", false);

            $arrTimeline = array(
                "id_attendance" => $attendanceId,
                "comment" => "Cadastrou o Atendimento",
                "status_icon" => 1,
                "status_timeline" => 4,
                "created_by" => $_SESSION['RR']->user->id,
            );

            $gerenciaPost->insert7181($arrTimeline, "attendance_timeline", null, false);
            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=true");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . '/addItem?added=false');
            exit;
        }
    }

    public function attendance($attendanceId)
    {
        parent::addStyle(URL . "css/" . CSSVERSION . "/" . $this->dir . "/progress-bar.css");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/progress-bar.js");
        parent::addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/classification.js");

        $modelGenerico = new ModelGenerico();
        $attendanceModel = new Attendance();

        $attendance = $attendanceModel->getAttendanceById($attendanceId);
        /**Formatação de data */
        $attendance->opening_date2 = Date::date_hour($attendance->opening_date);
        $attendance->return_date1 = Date::date($attendance->return_date);
        $attendance->return_date2 = Date::date_hour($attendance->return_date);

        $attendance->time_return_date = strtotime(str_replace("/", "-", $attendance->return_date1 . " 00:00:00"));
        $today = strtotime(date("Y-m-d 00:00:00"));

        if ($attendance->time_return_date > $today) {
            $bgReturnDate = "bg-aqua";
        } else if ($attendance->time_return_date == $today) {
            $bgReturnDate = "bg-orange";
        } else {
            $bgReturnDate = "bg-red";
        }

        Secure::redirectFunction(!Secure::creator($attendance->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");
        Secure::branch($attendance->id_branch, $this->route);

        $phones = $modelGenerico->getItemByGenericField($attendanceId, "attendance_phones", "id_attendance");
        $attachments = $modelGenerico->getItemByGenericField($attendanceId, "attendance_attachments", "id_attendance");
        $statusAttendance = (new AttendanceStatus())->getAndFilterAllItem(["status" => 1], 0)->data;

        $attendanceSteps = array_filter($statusAttendance, function ($element) {
            return $element->id != 11;
        });

        $disabledStatus = $attendance->id_status == 10 || $attendance->id_status == 11 ? true : false;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/call-center/global.php';
        require APP . 'view/' . $this->dir . '/call-center/menu.php';

        if (!isset($_GET['interests']) && !isset($_GET['vehicles'])) {
            /**Timeline */
            $timeline = $attendanceModel->getAttendanceTimelineIdAttendance($attendanceId);

            $timelineView['comment'] = array_filter($timeline, function ($t) {
                return $t->status_timeline == 1;
            });

            $timelineView['scheduling'] = array_filter($timeline, function ($t) {
                return $t->status_timeline == 2;
            });

            $timelineView['status'] = array_filter($timeline, function ($t) {
                return $t->status_timeline == 3;
            });

            $timelineView['registration'] = array_filter($timeline, function ($t) {
                return $t->status_timeline == 4;
            });

            require APP . 'view/' . $this->dir . '/call-center/timeline.php';
        } else if (isset($_GET['interests'])) {
            /**Interests */
            parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/interests.js");

            $attendanceFilterType = $modelGenerico->getAllItens('attendance_filter_type');
            $interests = $attendanceModel->getFiltersInterestByAttedanceId($attendanceId);

            $vehicleBrands = (new VehicleBrands())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;

            /**Contagem de veículos encontrados */
            $number_of_vehicles = 0;
            $filters_interest = [];

            $attendance_filters_interests = (new AttendanceFiltersInterests)->getWithFiltersAllItems(
                [(object)['columns' => ['id_attendance' => (object)['value' => $attendanceId]]]],
                [(object)['columns' => ['*']], (object)['table' => 'attendance_filter_type', 'columns' => ['name']]]
            )->data;

            if (!empty($attendance_filters_interests)) {
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

                $displayedVehicles = (new AttendanceDisplayedVehicles())->getWithFiltersAllItems([
                    (object)['columns' => ['id_attendance' => (object)['value' => $attendanceId]]],
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

                $number_of_vehicles = count((new Vehicles)->getAndFilterAllByInterest($vehicleFilters, $excludeVehicleIds));
            }
            require APP . "view/{$this->dir}/call-center/interest.php";
        } else if (isset($_GET['vehicles'])) {
            /**vehicles */
            parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/vehicles.js");
            $vehicleBrands = (new VehicleBrands())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
            $vehiclesPresentations = $attendanceModel->getAndFilterAllVehiclesPresentationByAttendance($attendanceId, [], 0, 0);

            require APP . "view/{$this->dir}/call-center/vehicle.php";
        }

        require APP . 'view/_templates/footer.php';
    }

    public function editAttendance($attendanceId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/attendance.js");

        $attendanceModel = new Attendance();
        $modelGenerico = new ModelGenerico();
        $branchModel = new Branch();

        $attendance = $attendanceModel->getAttendanceById($attendanceId);

        Secure::redirectFunction(!Secure::creator($attendance->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");
        Secure::branch($attendance->id_branch, $this->route);

        $cities = $branchModel->getCitiesByState($attendance->state);
        $states = $modelGenerico->getAllItens("states");
        $communicationChannels = $modelGenerico->getAllItens("communication_channels");

        $customers_filter = [
            (object)['columns' => ['status' => (object)['value' => 1]]],
            (object)["table" => "customer_branches", "columns" => ["id_branch" => (object)["value" => $_SESSION['RR']->branch->current->id]]]
        ];
        if (!Secure::access_admin()) {
            $customers_filter[] = (object)['columns' => ['created_by' => (object)['value' => $_SESSION['RR']->user->id]]];
        }

        $filterUsers = array(
            'status' => true,
            "id_branch_and_profile" => $attendance->id_branch,
            "order" => " up.access ASC, u.name ASC"
        );

        $customers = (new Customer)->getWithFiltersAllItems($customers_filter)->data;
        $users = (new User())->getAndFilterAllUsers($filterUsers, 0)->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/editAttendance.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditAttendance($attendanceId)
    {
        Secure::check_post_method($this->route . "/editAttendance");

        $arrPost = array(
            "name" => $_POST["name"],
            "email" => $_POST["email"],
            "id_city" => $_POST["id_city"],
            "id_branch" => $_SESSION['RR']->branch->current->id,
            "state" => $_POST["state"],
            "opening_date" => $_POST["opening_date"],
            "id_communication_channel" => $_POST["id_communication_channel"],
            "description" => $_POST["description"],
            "updated_at" => $this->now,
            "updated_by" => $_SESSION['RR']->user->id,
        );

        if (Secure::access_admin()) {
            $arrPost['created_by'] = $_POST['created_by'];
        }

        try {
            (new GerenciaPost())->update8191($arrPost, $this->table, 'id', $attendanceId, false);

            $arrTimeline = array(
                "id_attendance" => $attendanceId,
                "comment" => "Alterou informações do atendimento",
                "status_icon" => 2,
                "status_timeline" => 4,
                "created_by" => $_SESSION['RR']->user->id,
            );

            (new GerenciaPost())->insert7181($arrTimeline, "attendance_timeline", null, false);

            header('location:' . URL . $this->route . "/attendance/$attendanceId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editAttendance/$attendanceId?edited=false");
            exit;
        }
    }

    public function handleSubmitEditStatus($attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        $modelGenerico = new ModelGenerico();
        $gerenciaPost = new GerenciaPost();

        $attendance = $modelGenerico->getItemById8161($attendanceId, $this->table);
        $statusOld = $modelGenerico->getItemById8161($attendance->id_status, "attendance_status");

        $arrPostStatus = array('id_status' => $_POST['id_status']);

        if ($_POST['id_status'] == 10 || $_POST['id_status'] == 11) {
            $arrPostStatus["return_date"] = NULL;
        }

        $statusNew = $modelGenerico->getItemById8161($_POST['id_status'], "attendance_status");

        try {

            $arrTimeline = array(
                "id_attendance" => $attendanceId,
                "comment" => "Alterou o status de " . $statusOld->name . " para " . $statusNew->name . ".",
                "status_icon" => 2,
                "status_timeline" => 3,
                "created_by" => $_SESSION['RR']->user->id,
            );

            $gerenciaPost->insert7181($arrTimeline, "attendance_timeline", null, false);
            $gerenciaPost->update8191($arrPostStatus, $this->table, "id", $attendanceId, false);

            header('location:' . URL . $this->route . "/attendance/$attendanceId?edited=true");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendanceId?edited=false");
            exit;
        }
    }

    public function handleSubmitClassification($attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        try {
            (new GerenciaPost())->update8191(['classification' => $_POST['classification']], $this->table, "id", $attendanceId, false);

            header('location:' . URL . $this->route . "/attendance/$attendanceId?edited=true");
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendanceId?edited=false");
            exit;
        }
    }

    public function handleSubmitAddPhone($attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        $arrPostPhone = array(
            'id_attendance' => $attendanceId,
            'name' => $_POST['name_phone'],
            'phone' => Util::removeNonNumericCharacters($_POST['phone']),
            'whatsapp' => isset($_POST['whatsapp']) ? '1' : '0'
        );

        try {
            (new GerenciaPost())->insert7181($arrPostPhone, "attendance_phones", false);

            $arrTimeline = array(
                "id_attendance" => $attendanceId,
                "comment" => "Cadastrou um novo telefone " . $_POST['phone'] . ".",
                "status_icon" => 6,
                "status_timeline" => 4,
                "created_by" => $_SESSION['RR']->user->id,
            );

            (new GerenciaPost())->insert7181($arrTimeline, "attendance_timeline", null, false);

            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=false");
            exit;
        }
    }

    public function handleSubmitVehiclesPresentations($attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        $gerenciaPost = new GerenciaPost();
        $vehiclesPresentations = explode(",", $_POST['vehicles_presentations']);

        try {
            foreach ($vehiclesPresentations as $key => $value) {
                $gerenciaPost->insert7181(['id_attendance' => $attendanceId, "id_vehicle" => $value, "created_by" => $_SESSION['RR']->user->id], "attendance_displayed_vehicles", false, false);

                $vehicle = (new ModelGenerico())->getItemById8161($value, "vehicles");
                $arrTimeline = array(
                    "id_attendance" => $attendanceId,
                    "comment" => "Adicionou o veículo " . $vehicle->name . ".",
                    "status_icon" => 2,
                    "status_timeline" => 4,
                    "created_by" => $_SESSION['RR']->user->id,
                );

                $gerenciaPost->insert7181($arrTimeline, "attendance_timeline", null, false);
            }

            header('location:' . URL . $this->route . "/attendance/$attendanceId?vehicles");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editAttendance/$attendanceId?interests");
            exit;
        }
    }

    public function handleDeletePhone($phoneId)
    {
        $modelGenerico = new ModelGenerico();
        $attendancePhone = $modelGenerico->getItemById8161($phoneId, "attendance_phones");

        $arrTimeline = array(
            "id_attendance" => $attendancePhone->id_attendance,
            "comment" => "Excluiu o Telefone " . Util::maskTelefone($attendancePhone->phone) .  ".",
            "status_icon" => 7,
            "status_timeline" => 4,
            "created_by" => $_SESSION['RR']->user->id,
        );

        try {
            (new GerenciaPost())->insert7181($arrTimeline, "attendance_timeline", null, false);

            $modelGenerico->deleteItemByCampoGenerico("attendance_phones", "id", $phoneId);

            header('location:' . URL . $this->route . "/attendance/$attendancePhone->id_attendance?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendancePhone->id_attendance?deleted=false");
            exit;
        }
    }

    public function handleSubmitInterestFilter(int $attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        $filtersInterest = (new Attendance())->getFiltersInterestByAttedanceId($attendanceId);

        $arrayPost = array(
            "id_attendance" => $attendanceId,
            'id_attendance_filter_type' => $_POST['filter_type'],
            "created_by" => $_SESSION['RR']->user->id,
            'created_at' => date("Y-m-d H:i:s")
        );

        $json = [];
        $filterType = $_POST['filter_type'];

        $arrayFilterInterest = array_filter($filtersInterest, function ($interest) use ($filterType) {
            return $interest->id_attendance_filter_type == $filterType;
        });

        $filterInterest = reset($arrayFilterInterest);

        if (!empty($filterInterest)) {
            $json = json_decode($filterInterest->json);
        }

        switch ($_POST['filter_type']) {
            case '1':
                /**Marca e Modelo */
                $alreadyAdded = array_filter($json, function ($pair) {
                    return $pair['id_brand'] == $_POST['id_brand'] && ($pair['id_model'] ?? '') == ($_POST['id_model'] ?? '');
                });

                if (empty($alreadyAdded)) {
                    array_push($json, [
                        'id_brand' => $_POST['id_brand'],
                        'id_model' => $_POST['id_model'] ?? null,
                    ]);
                }
                break;
            case '2':
                /**Faixa de Preço */
                $json = [
                    'start_price' => Util::unMaskMoney($_POST['start_price']) > 0 ? Util::unMaskMoney($_POST['start_price']) : $json->start_price,
                    'end_price' => Util::unMaskMoney($_POST['end_price']) > 0 ? Util::unMaskMoney($_POST['end_price']) : $json->end_price,
                ];

                break;
            case '3':
                /**Ano/Km */
                $json = [
                    'year_from' => !empty($_POST['year_from']) ? $_POST['year_from'] : ($json->year_from ?? null),
                    'year_to' => !empty($_POST['year_to']) ? $_POST['year_to'] : ($json->year_to ?? null),
                    'km_max' => !empty($_POST['km_max']) ? $_POST['km_max'] : ($json->km_max ?? null),
                ];

                break;
        }

        $arrayPost['json'] = json_encode($json);

        try {
            if (!empty($filterInterest)) {
                (new GerenciaPost)->update8191($arrayPost, "attendance_filters_interests", 'id', $filterInterest->id, false);
            } else {
                (new GerenciaPost)->insert7181($arrayPost, "attendance_filters_interests", null, false);
            }

            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=true&interests");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=false&interests");
            exit;
        }
    }
    /**Descontinuar */
    public function deleteInterestFilterById(int $itemId, int $key)
    {
        $modelGenerico = new ModelGenerico();

        $item = $modelGenerico->getItemById8161($itemId, "attendance_filters_interests");
        $attendance = (new Attendance)->getAttendanceById($item->id_attendance);
        Secure::redirectFunction(!Secure::creator($attendance->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            $json = json_decode($item->json);

            if ($item->id_attendance_filter_type == 2) {
                /**Faixa de Preço */
                if ($key == 0 && isset($json->start_price)) {
                    unset($json->start_price);
                } else if ($key == 1 && isset($json->end_price)) {
                    unset($json->end_price);
                }
            } else if ($item->id_attendance_filter_type == 3) {
                /**Ano/Km */
                if ($key == 0 && isset($json->year_from)) {
                    unset($json->year_from);
                } else if ($key == 1 && isset($json->year_to)) {
                    unset($json->year_to);
                } else if ($key == 2 && isset($json->km_max)) {
                    unset($json->km_max);
                }
            } else {
                /**Marca e Modelo */
                $json = (array_filter($json, function ($element) use ($key) {
                    return $element != $key;
                }, ARRAY_FILTER_USE_KEY));
            }

            if (!empty((array) $json)) {
                if (is_array($json)) {
                    $json = array_values($json);
                }

                $json = json_encode($json);

                (new GerenciaPost())->update8191(['json' => $json], "attendance_filters_interests", 'id', $itemId, false);
            } else {
                $modelGenerico->deleteItemByCampoGenerico("attendance_filters_interests", "id", $itemId);
            }

            header('location:' . URL . $this->route . "/attendance/$attendance->id?deleted=true&interests");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendance->id?deleted=false&interests");
            exit;
        }
    }

    public function handleDeleteVehicleIdDisplayedVehicles($displayedId)
    {
        $modelGenerico = new ModelGenerico();

        $attendanceDisplayedVehicle = $modelGenerico->getItemById8161($displayedId, "attendance_displayed_vehicles");
        $vehicle = $modelGenerico->getItemById8161($attendanceDisplayedVehicle->id_vehicle, "vehicles");

        $arrTimeline = array(
            "id_attendance" => $attendanceDisplayedVehicle->id_attendance,
            "comment" => "Deletou o Veículo " . $vehicle->name . ".",
            "status_icon" => 9,
            "status_timeline" => 4,
            "created_by" => $_SESSION['RR']->user->id,
        );

        try {
            (new GerenciaPost())->insert7181($arrTimeline, "attendance_timeline", null, false);

            $modelGenerico->deleteItemByCampoGenerico("attendance_displayed_vehicles", "id", $displayedId);

            header('location:' . URL . $this->route . "/attendance/$attendanceDisplayedVehicle->id_attendance?deleted=true&vehicles");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendanceDisplayedVehicle->id_attendance?deleted=false&vehicles");
            exit;
        }
    }

    public function handleSubmitAddComment($attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        $modelGenerico = new ModelGenerico();
        $gerenciaPost = new GerenciaPost();
        $attendance = $modelGenerico->getItemById8161($attendanceId, $this->table);

        try {
            if (!empty($_POST["comment"])) {
                $arrPostComment = array(
                    "id_attendance" => $attendanceId,
                    "comment" => $_POST["comment"],
                    "status_icon" => 3,
                    "status_timeline" => 1,
                    "created_by" => $_SESSION['RR']->user->id,
                );

                $gerenciaPost->insert7181($arrPostComment, "attendance_timeline", null, false);
            }

            if (isset($_POST['return_date'])) {
                if (Date::date_hour($attendance->return_date) != Date::date_hour($_POST['return_date'])) {
                    $arrPost = array("return_date" => $_POST['return_date']);
                    $gerenciaPost->update8191($arrPost, $this->table, 'id', $attendanceId, false);

                    $arrPostComment = array(
                        "id_attendance" => $attendanceId,
                        "comment" => "Alterou a data de Retorno para " . Date::date_hour($_POST['return_date']) . ".",
                        "status_icon" => 4,
                        "status_timeline" => 2,
                        "created_by" => $_SESSION['RR']->user->id,
                    );

                    $gerenciaPost->insert7181($arrPostComment, "attendance_timeline", null, false);
                }
            }

            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=false");
            exit;
        }
    }

    public function handleDeleteAttendanceTimeline($itemId)
    {
        $modelGenerico = new ModelGenerico();

        $item = $modelGenerico->getItemById8161($itemId, "attendance_timeline");
        $attendance = (new Attendance)->getAttendanceById($item->id_attendance);
        Secure::redirectFunction(!Secure::access_admin(), "$this->route/attendance/$attendance->id", "authorization=false");

        try {
            $modelGenerico->deleteItemByCampoGenerico("attendance_timeline", "id", $itemId);

            header('location:' . URL . $this->route . "/attendance/$attendance->id?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendance->id?deleted=false");
            exit;
        }
    }

    public function handleSubmitAddAttachments($attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        if (empty($_FILES['attachment']["tmp_name"])) {
            redirect($this->route . "/attendance/$attendanceId");
        }

        try {
            $attachments = FileUploader::uploadFiles(
                $_FILES['attachment'],
                array_fill(0, count($_FILES['attachment']) + 1, "attachments/attendance/$attendanceId")
            );

            foreach ($attachments as $attachment) {

                $arrPost = array(
                    "id_attendance" => $attendanceId,
                    "name" => $_POST["name"],
                    "filename" => $attachment['filename'],
                    "extension" => $attachment['extension'],
                    "created_by" => $_SESSION['RR']->user->id,
                );

                (new GerenciaPost())->insert7181($arrPost, "attendance_attachments", null, false);
            }

            $arrPostTimeline = array(
                "id_attendance" => $attendanceId,
                "comment" => "Adicionou um novo anexo.",
                "url_attachment" => URL . "attachments/attendance/$attendanceId/" . $attachment['filename'] . "." . $attachment['extension'],
                "name_attachment" => $_POST["name"],
                "status_icon" => 5,
                "status_timeline" => 4,
                "created_by" => $_SESSION['RR']->user->id,
            );

            (new GerenciaPost())->insert7181($arrPostTimeline, "attendance_timeline", null, false);

            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attendanceId?added=false");
            exit;
        }
    }

    public function handleSubmitDeleteAttachment($attendanceId)
    {
        Secure::check_post_method($this->route . "/attendance/$attendanceId");

        $attachment = (new ModelGenerico())->getItemById8161($attendanceId, "attendance_attachments");

        $arrTimeline = [
            "id_attendance" => $attachment->id_attendance,
            "comment" => "Excluiu um anexo: " . $attachment->name .  ".",
            "status_icon" => 7,
            "status_timeline" => 4,
            "created_by" => $_SESSION['RR']->user->id,
        ];

        try {
            (new GerenciaPost())->insert7181($arrTimeline, "attendance_timeline", null, false);

            DeleteFile::deleteFile(
                [$attendanceId],
                "attendance_attachments",
                ["attachments/attendance/$attachment->id_attendance/$attachment->filename.$attachment->extension"]
            );

            header('location:' . URL . $this->route . "/attendance/$attachment->id_attendance?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attendance/$attachment->id_attendance?deleted=false");
            exit;
        }
    }

    public function disableAttendance($attendanceId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->disableItem($attendanceId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableAttendance($attendanceId, $page)
    {
        $ModelGenerico =  new ModelGenerico();
        $ModelGenerico->enableItem($attendanceId);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }
}
