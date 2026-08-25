<?php

namespace RR\controller\ajax;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use RR\core\Model;
use RR\libs\Date;
use RR\libs\Pagination;
use RR\libs\RecursiveCostCenter;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\GerenciaPost;
use RR\model\Attendance;
use RR\model\Banks;
use RR\model\BankAccounts;
use RR\model\BillReceive;
use RR\model\BillReceiveInstallment;
use RR\model\BillsToPayInstallment;
use RR\model\User;
use RR\model\Branch;
use RR\model\Calendar;
use RR\model\CheckControl;
use RR\model\Currencies;
use RR\model\Customer;
use RR\model\CustomerType;
use RR\model\DashboardOrder;
use RR\model\Lead;
use RR\model\ModelGenerico;
use RR\model\Notification;
use RR\model\NotificationRead;
use RR\model\Property;
use RR\model\Sales;
use RR\model\PropertyCategory;
use RR\model\PropertyType;
use RR\model\StandardContract;

class AjaxController
{
    public function __construct()
    {
        session_start();
    }

    public function toast()
    {
        echo json_encode(['error' => false, 'toast' => isset($_SESSION['RR']['toast']) ? $_SESSION['RR']['toast'] : ""]);
        unset($_SESSION['RR']['toast']);
        exit;
    }

    public function addCommentToTheTimelineInAttendance()
    {
        $arrayPost = array(
            'id_attendance' => $_POST['attendanceId'],
            'comment' => $_POST['comment'],
            'status_icon' => $_POST['statusIcon'],
            'status_timeline' => $_POST['statusTimeline'],
            'created_by' => $_POST['createdBy'],
        );

        (new GerenciaPost())->insert7181($arrayPost, "attendance_timeline", false, false);
        echo json_encode(['error' => false]);
        exit;
    }

    public function updateAttendanceStatus()
    {
        (new GerenciaPost())->update8191(['id_status' => $_POST['statusId']], "attendance", "id", $_POST['attendanceId'], false);
        echo json_encode(['error' => false]);
        exit;
    }

    public function dropdownNotification()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }

        if (!isset($_GET['message_read'])) {
            $_GET['message_read'] = "0";
        }

        if (!Secure::access_superAdm()) {
            if (!Secure::access_manager()) {
                $_GET['id_user'] = $_SESSION['RR']->user->id;
                $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
            } else {
                $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
            }
        } else {
            if ($_SESSION['RR']->branch->current->id != 0) {
                $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
            }
        }

        $notifications = (new Notification())->getAndFilterAllItem(5, $_GET, 1);

        array_map(function ($item) {
            $notificationRead = (new NotificationRead)->getItemWithFilters(
                [
                    (object)['columns' => ['id_notification' => (object)['value' => $item->id]]]
                ]
            );

            if ($notificationRead->id_user == $_SESSION['RR']->user->id) {
                $item->intended_user = 1;
            }
        }, $notifications);

        echo json_encode(['error' => false, 'notifications' => $notifications]);
        exit;
    }

    public function dropdownNotificationCount()
    {
        $arrayPost = array(
            "status" => true,
            "message_read" => "0"
        );

        if (!Secure::access_superAdm()) {
            if (!Secure::access_manager()) {
                $arrayPost['id_user'] = $_SESSION['RR']->user->id;
                $arrayPost['id_branch'] = $_SESSION['RR']->branch->current->id;
            } else {
                $arrayPost['id_branch'] = $_SESSION['RR']->branch->current->id;
            }
        } else {
            if ($_SESSION['RR']->branch->current->id != 0) {
                $arrayPost['id_branch'] = $_SESSION['RR']->branch->current->id;
            }
        }

        $notifications = (new Notification())->getCountAndFilterAllItem($arrayPost);

        array_map(function ($item) {
            $notificationRead = (new NotificationRead)->getItemWithFilters([
                (object)['columns' => ['id_notification' => (object)['value' => $item->id]]]
            ]);

            if ($notificationRead->id_user == $_SESSION['RR']->user->id) {
                $item->intended_user = 1;
            }
        }, $notifications);

        echo json_encode(['error' => false, 'dropdownNotifications' => $notifications]);
        exit;
    }

    public function deleteItemAfterSevenDays()
    {
        $items = (new Notification)->getWithFiltersAllItems(
            [
                (object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]],
                (object)['table' => 'notification_read', 'columns' => [
                    'message_read' => (object)['comparison' => 'EQUAL', 'value' => true],
                    'seen_at' => (object)['comparison' => 'NOT_EQUAL', 'value' => true]
                ]]
            ],
            [
                (object)['columns' => ['*']],
                (object)['table' => 'notification_read', 'columns' => ['seen_at']]
            ]
        );

        foreach ($items->data as $item) {
            (new Notification())->deleteItemAfterSevenDays($item->id, $item->notification_read_seen_at);
        }

        exit;
    }

    public function sidebarCollapse()
    {
        $_SESSION['RR']->setting->sidebar = $_POST['sidebar'];
        echo json_encode(['error' => false]);
        exit;
    }

    public function sumDate()
    {
        $obj = date("Y-m-d", strtotime("+ " . $_POST['sum'], strtotime($_POST['date'])));

        echo json_encode(['error' => false, 'obj' => $obj]);
        exit;
    }

    public function compareDate()
    {
        if (strtotime($_POST['data2']) > strtotime("+ " . $_POST['sum'], strtotime($_POST['data1']))) {
            $obj = "data2 maior limite";
        } else if (strtotime($_POST['data2']) < strtotime($_POST['data1'])) {
            $obj = "data1 maior";
        } else {
            $obj = true;
        }

        echo json_encode(['error' => false, 'obj' => $obj]);
        exit;
    }

    public function recursiveCostCenterTree()
    {
        $costCenter = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1]);

        echo json_encode(['error' => false, 'costCenter' => $costCenter]);
        exit;
    }
    /**Alterado para 'CostCenter' */
    public function recursiveCostCenterView()
    {
        $items = (new RecursiveCostCenter())->recursiveTree(0, ['status' => 1]);

        $options = (new RecursiveCostCenter())->recursiveOptionView($items, $_POST['itemSelected'], isset($_POST['ownId']) ? $_POST['ownId'] : "", isset($_POST['showChildren']) ? $_POST['showChildren'] : true);
        echo json_encode(['error' => false, 'options' => $options]);
        exit;
    }

    public function getPortionById()
    {
        $portion = (new Sales())->getPortionById($_POST['id']);
        $portion->value = str_replace(".", ",", $portion->value);

        echo json_encode(['error' => false, 'portion' => $portion]);
        exit;
    }

    public function getLogStatusByIdContrato()
    {
        $obj = (new User())->getUserById($_POST['id']);

        echo json_encode($obj);
    }

    public function getAllStates()
    {
        $obj = (new ModelGenerico)->getAllItens("states");
        echo json_encode(['error' => false, 'states' => $obj]);
    }

    public function getCitiesForProperties()
    {
        $obj = (new Property())->getCitiesForProperties(['uf_state' => $_POST['uf'], 'status' => true, 'id_branch' => $_SESSION['RR']->branch->current->id, 'created_by' => isset($_POST['created_by']) ? $_POST['created_by'] : '']);
        echo json_encode(['error' => false, 'cities' => $obj]);
    }

    public function getCityByState()
    {
        $obj = (new Branch())->getCitiesByState($_POST['uf']);
        echo json_encode(['error' => false, 'cities' => $obj]);
    }

    public function getCityByStateId()
    {
        $obj = (new Branch())->getCitiesByStateId($_POST['id']);
        echo json_encode(['error' => false, 'cities' => $obj]);
    }

    public function getNeighborhoodsByCityId()
    {
        $obj = (new Property())->getWithFiltersAllItems(
            [
                (object)['columns' => [
                    'status' => (object)['comparison' => 'EQUAL', 'value' => true],
                    'id_city' => (object)['comparison' => 'EQUAL', 'value' => $_POST['id_city']],
                    'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]
                ]]
            ],
            [
                (object)['columns' => ['neighborhood']]
            ],
            ['groupBy' => 'this->table.neighborhood']
        )->data;

        echo json_encode(['error' => false, 'neighborhoods' => $obj]);
    }

    public function getCustomerByBranch()
    {
        $obj = (new Customer())->getCustomerByBranch($_POST['id_branch'], $_SESSION['RR']->profile->id);
        echo json_encode(['error' => false, 'customers' => $obj]);
    }

    public function getProductByBranch()
    {
        $obj = (new Property())->getProductsByBranch($_POST['id_branch']);
        echo json_encode(['error' => false, 'products' => $obj]);
    }

    public function getExclusiveByBranch()
    {
        $customerModel = new Customer();
        $productsModel = new Property();
        $branchModel = new Branch();
        $contractModel = new StandardContract();
        $userModel = new User();

        $branch = $branchModel->getItemById8161($_POST['id_branch']);

        $filterCustomer = array(
            'status' => true,
            'id_branch' => $_POST['id_branch'],
            'id_type_customer' => $_POST['id_type_customer'],
        );

        $filterProduct = array(
            'status' => true,
            'id_branch' => $_POST['id_branch'],
        );

        $filterContract = array(
            'status' => true,
            'id_type_contract' => 1
        );

        $filterProposal = array(
            'status' => true,
            'id_type_contract' => 2
        );

        $filterUsers = array(
            'status' => true,
            'id_profile' => 2,
            'id_branch' => $branch->id,
        );

        $customers = $customerModel->getAndFilterAllCustomer(0, $filterCustomer, 0);
        $products = $productsModel->getAndFilterAllProducts(0, $filterProduct, 0);
        $users = $userModel->getAndFilterAllUsers(0, $filterUsers, 0)->data;
        $contracts = $contractModel->getAndFilterAllStandardContract(0, $filterContract, 0);
        $proposal = $contractModel->getAndFilterAllStandardContract(0, $filterProposal, 0);
        $currencies = (new Currencies)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]]);

        echo json_encode(['error' => false, 'customers' => $customers, 'products' => $products, 'contracts' => $contracts, 'proposal' => $proposal, 'users' => $users, 'currencies' => $currencies]);
    }

    public function getProductById()
    {
        $product = (new Property())->getProductsById($_POST['id_product']);

        echo json_encode(['error' => false, 'product' => $product]);
    }

    public function getAllItensFromGenericTable()
    {
        $formOfPayment = (new ModelGenerico())->getAllItens($_POST['table']);

        echo json_encode(['error' => false, 'response' => $formOfPayment]);
    }

    public function updatePortion()
    {
        $portionPost = $_POST['portion'];

        if (trim($portionPost['portion_number']) == "" || trim($portionPost['value']) == "" || trim($portionPost['due_date']) == "") {
            echo json_encode(['error' => true, 'message' => 'Preencha os campos']);
            exit;
        }

        $arrayPost['portion_number'] = preg_replace("/[^0-9]/", '', $portionPost['portion_number']);
        $arrayPost['value'] = str_replace(",", ".", str_replace(".", "", $portionPost['value']));
        $arrayPost['due_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $portionPost['due_date'])));
        $arrayPost['observation'] = $portionPost['observation'];
        $arrayPost['id_form_of_payment'] = $portionPost['id_form_of_payment'];
        $arrayPost['id_sale'] = $portionPost['id_sale'];
        $arrayPost['id_branch'] = $portionPost['id_branch'];
        $arrayPost['created_by'] = $_SESSION['RR']->user->id;

        (new GerenciaPost())->update8191($arrayPost, "payments_of_sales", 'id', $portionPost['id_portion']);

        echo json_encode(['error' => false, 'portion' => $arrayPost]);
        exit;
    }

    /**Descontinuar essa função */
    public function getAndFilterAccountsPortion()
    {
        $bankAccountsModel = new BankAccounts();
        $arrayFilter = array('status' => $_POST['status']);
        $arrayPost = $bankAccountsModel->getAndFilterAllAccounts($arrayFilter);

        echo json_encode(['error' => false, 'accounts' => $arrayPost]);
    }

    public function getAndFilterAllBankAccounts()
    {
        $bankAccountsModel = new BankAccounts();
        $arrayFilter = array('status' => $_POST['status']);
        $arrayPost = $bankAccountsModel->getAndFilterAllBankAccounts(0, $arrayFilter, 0);

        echo json_encode(['error' => false, 'accounts' => $arrayPost]);
    }

    public function getAndFilterAllBillsToPayInstallment()
    {
        $billsToPayModel = new BillsToPayInstallment();
        $arrayFilter = array(
            'status' => isset($_POST['status']) ? $_POST['status'] : "",
            "id_customer" => isset($_POST['id_customer']) ? $_POST['id_customer'] : "",
            "id_bills_to_pay" => isset($_POST['id_bills_to_pay']) ? $_POST['id_bills_to_pay'] : "",
        );

        $arrayPost = $billsToPayModel->getAndFilterAllItem($arrayFilter);

        echo json_encode(['error' => false, 'installments' => $arrayPost]);
    }

    public function getAndFilterAllBillReceive()
    {
        $arrayFilters = [];

        if (isset($_POST['id_bill_receive']) && !empty($_POST['id_bill_receive'])) {
            $arrayFilters[] = (object)['columns' => ['id_bill_receive' => (object)['comparison' => '=', 'value' => $_POST['id_bill_receive']]]];
        }

        if (isset($_POST['status'])) {
            $arrayFilters[] = (object)['columns' => ['status' => (object)['comparison' => '=', 'value' => $_POST['status']]]];
        }

        $response = (new BillReceiveInstallment())->getWithFiltersAllItems($arrayFilters);
        echo json_encode(['error' => false, 'installments' => $response->data]);
    }

    public function getAndFilterBanksPortion()
    {
        $account = new Banks();
        $arrFilter = array('finance_bank' => $_POST['finance_bank']);
        $arrayPost = $account->getAndFilterAllBanksPortion($arrFilter);

        echo json_encode(['error' => false, 'banks' => $arrayPost]);
    }

    /**Descontinuar essa função */
    public function getAllBanksPortion()
    {
        $banks = new Banks();
        $arrayPost = $banks->getAllBanks();

        echo json_encode(['error' => false, 'banks' => $arrayPost]);
    }

    public function getAndFilterAllBanks()
    {
        $bankModel = new Banks();
        $arrayFilter = array('status' => $_POST['status']);
        $banks = $bankModel->getAndFilterAllBanks(0, $arrayFilter, 0);

        echo json_encode(['error' => false, 'banks' => $banks]);
    }

    public function getAmountPortionsBySale()
    {
        $saleModal = new Sales();
        $arrayPost = $saleModal->getAmountPortionsBySale($_POST['id_sale']);
        echo json_encode(['error' => false, 'amountPortions' => $arrayPost]);
    }

    public function getAllPropertyTypeResourceByIdPropertyType()
    {
        $propertyTypeModel = new PropertyType();

        $arrayPost = $propertyTypeModel->getAllPropertyTypeResourceByIdItem($_POST['id_property_type']);
        echo json_encode(['error' => false, 'propertyTypeResources' => $arrayPost]);
    }

    public function getAllProductOwnershipFeatureByIdProduct()
    {
        $productsModal = new Property();

        $arrayPost = $productsModal->getAllProductOwnershipFeatureByIdProduct($_POST['id_product']);
        echo json_encode(['error' => false, 'productOwnershipFeature' => $arrayPost]);
    }

    public function getCustomerTypeResources()
    {
        $customerTypeModal = new CustomerType();
        $arrayPost = $customerTypeModal->getAllCustomerTypeResourceIdCustomerType($_POST['id_customer_type']);
        echo json_encode(['error' => false, 'customerTypeResources' => $arrayPost]);
    }

    public function getItemById8161()
    {
        $branchModal = new Branch();
        $arrayPost = $branchModal->getItemById8161($_POST['id_branch']);
        echo json_encode(['error' => false, 'branch' => $arrayPost]);
    }

    public function getAllPropertyCategory()
    {
        $propertyCartegoryModal = new PropertyCategory();
        $arrayPost = $propertyCartegoryModal->getAllPropertyCategory();
        echo json_encode(['error' => false, 'propertyCategory' => $arrayPost]);
    }

    public function getCustomerById()
    {
        $arrayPost = (new Customer)->getCustomerById($_POST['id_customer']);
        echo json_encode(['error' => false, 'customer' => $arrayPost]);
    }

    public function updateFilesOrder()
    {
        $gerenciaPost = new GerenciaPost();

        $list = array();
        parse_str($_POST['list'], $list);

        $arrayPost = ["item_order" => 1];

        foreach ($list['item'] as $id) {
            $gerenciaPost->update8191($arrayPost, $_POST['table'], "id", $id, false);
            $arrayPost['item_order']++;
        }
    }

    public function updateFilesOrdem()
    {
        $gerenciaPost = new GerenciaPost();

        $list = array();
        parse_str($_POST['list'], $list);

        foreach ($list['item'] as $key => $id) {
            $arrayPost = ['ordem' => $key];
            $gerenciaPost->update8191($arrayPost, $_POST['table'], "id", $id, false);
        }
    }

    public function getAndFilterAllUsers()
    {
        $usersModel = new User();

        $page = $_POST['page'];
        $rows = $_POST['rows'];

        $users = $usersModel->getAndFilterAllUsers($rows, $_POST, $page);
        $pagination = (new Pagination())->pages($users->count, $rows);

        echo json_encode(['error' => false, 'users' => $users, "nextPage" => $pagination->page]);
        exit;
    }

    public function getAndFilterAllCustomer()
    {
        $customerModel = new Customer();

        $page = $_POST['page'];
        $rows = $_POST['rows'];

        $customerOwner = $customerModel->getAndFilterAllCustomer($rows, $_POST, $page);
        $nextPage = !empty($customerModel->getAndFilterAllCustomer($rows, $_POST, $page + 1));

        echo json_encode(['error' => false, 'owners' => $customerOwner, "nextPage" => $nextPage]);
        exit;
    }

    public function getAndFilterAllProducts()
    {
        $productsModel = new Property();

        $page = $_POST['page'];
        $rows = $_POST['rows'];
        if (isset($_POST["statusFilterIdsProduct"]) && $_POST["statusFilterIdsProduct"] != 'false') {
            $_POST["idsProducts"] = $_POST["filterIdsProducts"];
        }

        $products = $productsModel->getAndFilterAllProducts($rows, $_POST, $page);
        $nextPage = !empty($productsModel->getAndFilterAllProducts($rows, $_POST, $page + 1));

        echo json_encode([
            'error' => false,
            'products' => $products,
            "nextPage" => $nextPage
        ]);
        exit;
    }

    public function getAndFilterAllPropertiesPresentationByAttendance()
    {
        $productsModel = new Property();

        $page = $_POST['page'];
        $rows = $_POST['rows'];

        $products = $productsModel->getAndFilterAllProducts($rows, $_POST, $page);
        $nextPage = !empty($productsModel->getAndFilterAllProducts($rows, $_POST, $page + 1));

        $propertiesPresentations = (new Attendance())->getAndFilterAllPropertiesPresentationByAttendance($_POST['attendanceId'], ['status_property_presentation' => true, 'status_attendance' => true, 'status_property' => true], 0, 0);

        echo json_encode([
            'error' => false,
            'propertiesPresentations' => $propertiesPresentations,
            "nextPage" => $nextPage
        ]);
        exit;
    }

    public function getAllPropertyTypeIdTypeBranch()
    {
        $arrayPost = (new PropertyType)->getAndFilterAllItem(['status' => 1], 0);
        echo json_encode(['error' => false, 'property_type' => $arrayPost]);
    }

    public function changeBranch()
    {
        (new User())->checkSession();
        $cache = new FilesystemAdapter();
        $cache->delete('menus_' . $_SESSION['RR']->cache->id);

        $branch = (new Branch())->getItemById8161($_POST['branchId']);

        if ($branch) {
            $_SESSION['RR']->branch->current->id = $branch->id;
            $_SESSION['RR']->branch->current->name = $branch->name;
            $_SESSION['RR']->branch->current->type = $branch->type;
        } else {
            $_SESSION['RR']->branch->current->id = $_POST['branchId'];
            $_SESSION['RR']->branch->current->name = "Super ADM";
        }

        echo json_encode(['error' => false]);
        exit;
    }

    public function getAndFilterAllAttendanceForCalendar()
    {
        $calendar = (new Calendar())->getAndFilterAllAttendanceForCalendar(0, $_POST['filterAttendance'], 0);
        $products = (new Property())->getAndFilterAllProducts(0, $_POST['filterProduct'], 0);

        echo json_encode(["attendance" => $calendar, "products" => $products]);
        exit;
    }

    public function getImmovableResourceIdAttendance()
    {
        $attendanceFiltersInterests = (new Attendance())->getImmovableResourceIdAttendance($_POST['id_attendance']);

        echo json_encode(["error" => false, "filtersInterests" => $attendanceFiltersInterests]);
        exit;
    }

    public function getAllProductWithFeatureValue()
    {
        $productsModel = new Property();

        $allIdsProductsWithFilter = array();
        $idsProducts = [];

        if (isset($_POST['features']) && !empty($_POST['features'])) {

            foreach ($_POST['features'] as $feature => $value) {
                $arrayObj = $productsModel->getAllProductWithFeatureValue2($feature, $value);
                $arrayString = array_map(function ($object) {
                    return $object->id;
                }, $arrayObj);

                array_push($allIdsProductsWithFilter, $arrayString);
            }

            $idsProducts = Util::findIntersectionInMatrix($allIdsProductsWithFilter);
        }
        echo json_encode(["error" => false, "idsProducts" => $idsProducts]);
        exit;
    }

    public function comparePhone()
    {
        $filters = array('id_branch' => $_SESSION['RR']->branch->current->id, "phone" => $_POST['phone']);
        $attendance = (new Attendance())->comparePhone($filters);

        echo json_encode(["error" => false, "attendance" => $attendance]);
        exit;
    }

    public function getPresentationsIPByIdDisplayed()
    {
        $attendanceModel = new Attendance();
        $presentations = $attendanceModel->getPresentationsIPByIdDisplayed($_POST['id_displayed_properties']);

        echo json_encode(["error" => false, "presentations" => $presentations]);
        exit;
    }

    public function searchProductIdenticalByCod()
    {
        $filters['cod'] = $_POST['cod'];
        $product = (new Property())->getAndFilterAllProducts(0, $filters, 0);

        echo json_encode(["error" => false, "products" => $product]);
        exit;
    }

    public function requeredCustomerField()
    {
        $requiredField = (new ModelGenerico())->getItemById8161(1, 'customer_required_field');
        echo json_encode(["error" => false, "requiredField" => $requiredField]);
        exit;
    }

    //Update dashboard display order
    public function addOrderDashboard()
    {
        $id_user = $_POST['id_user'];

        $dashboard = (new DashboardOrder)->getOrdinationFromUser($id_user);

        $list = $_POST['list'];
        $output = array();
        $list = parse_str($list, $output);
        $order_by = 1;

        foreach ($output['item'] as $id_dashboard) {
            if ($dashboard > 0) {
                (new DashboardOrder)->upDateOrderDashboard($id_user, $id_dashboard, $order_by);
            } else {
                (new DashboardOrder)->addOrderDashboard($id_user, $id_dashboard, $order_by);
            }
            $order_by++;
        }
    }

    public function setActiveBoxDashboard()
    {
        (new DashboardOrder)->setActiveBoxDashboard($_POST['id_user'], $_POST['id']);
    }

    public function setDeactivateBoxDashboard()
    {
        (new DashboardOrder)->setDeactivateBoxDashboard($_POST['id_user'], $_POST['id']);
    }

    public function getValueCurrency()
    {
        $currencies = (new Currencies)->getItemById($_POST['currencyId']);

        echo json_encode(["error" => false, "currencies" => $currencies]);
        exit;
    }

    public function getAttendanceById()
    {
        $attendance = (new Attendance)->getItemById($_POST['id']);
        echo json_encode(['error' => false, 'attendance' => $attendance]);
    }

    public function getAccounts()
    {
        $item = (new CheckControl)->getItemById($_POST['checkId']);
        $accounts = (new BankAccounts)->getWithFiltersAllItems([(object)['columns' => ['status' => ['value' => true]]]]);

        $message = '';
        $error = false;
        if ($accounts->count <= 0) {
            $error = true;
            $message = 'Você precisa ter uma "Conta Bancária" cadastrada para continuar!';
        }

        echo json_encode(['error' => $error, 'message' => $message, 'check' => $item]);
    }
}
