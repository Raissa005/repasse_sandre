<?php

namespace RR\controller\project;

use RR\libs\BoxAlert;
use RR\libs\Util;
use RR\libs\FileUploader;
use RR\libs\DeleteFile;
use RR\libs\Secure;
use RR\libs\Pagination;
use RR\model\Property;
use RR\model\Branch;
use RR\model\PropertyCategory;
use RR\model\PropertyType;
use RR\model\ImmovableResource;
use RR\model\PropertyClassification;
use RR\model\User;
use RR\model\Customer;
use RR\model\Attendance;
use RR\model\SystemSettings;
use RR\model\WaterMark;
use RR\model\GerenciaPost;
use RR\model\ModelGenerico;
use PDOException;
use RR\libs\Date;
use RR\model\AttendanceStatus;
use RR\model\ManagerTeam;
use RR\model\WebsiteSettings;
use RR\model\DisplayedProperties;
use RR\model\Notification;
use RR\model\NotificationRead;
use RR\model\Sales;

use function RR\Controller\redirect;

class PropertyController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $title;
    public $userSession;
    public $systemConfig;
    public $siteConfig;

    public function __construct()
    {
        $this->route = 'property';
        $this->dir = 'property';
        $this->model = new Property();
        $this->table = 'products';
        parent::__construct($this->route);

        $this->userSession = (new User())->getUserById($_SESSION['RR']->user->id);
        $this->systemConfig = (new SystemSettings())->getItemById(1);
        $this->siteConfig = (new WebsiteSettings)->getItemById(1);

        $this->title = 'Imóveis';
    }

    public function index()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/{$this->dir}/filters.js");
        $this->addScript(URL . "js/" . JSVERSION . "/{$this->dir}/list.js");

        $filters = isset(explode('?', $_SERVER["REQUEST_URI"])[1]) ? explode('?', $_SERVER["REQUEST_URI"])[1] : '';

        if (!isset($_GET['b'])) {
            if (!isset($_GET['columnPropertyIdName'])) {
                $_GET['columnPropertyIdName'] = 'on';
            }

            if (!isset($_GET['columnPropertyType'])) {
                $_GET['columnPropertyType'] = 'on';
            }

            if (!isset($_GET['columnPropertyLocation'])) {
                $_GET['columnPropertyLocation'] = 'on';
            }

            if (!isset($_GET['columnPropertyValue'])) {
                $_GET['columnPropertyValue'] = 'on';
            }

            if (!isset($_GET['columnPropertyStatus'])) {
                $_GET['columnPropertyStatus'] = 'on';
            }
        } else {
            if (!isset($_GET['columnPropertyIdName'])) {
                $_GET['columnPropertyIdName'] = 'off';
            }

            if (!isset($_GET['columnPropertyType'])) {
                $_GET['columnPropertyType'] = 'off';
            }

            if (!isset($_GET['columnPropertyLocation'])) {
                $_GET['columnPropertyLocation'] = 'off';
            }

            if (!isset($_GET['columnPropertyValue'])) {
                $_GET['columnPropertyValue'] = 'off';
            }

            if (!isset($_GET['columnPropertyStatus'])) {
                $_GET['columnPropertyStatus'] = 'off';
            }
        }

        if (isset($_GET['features'])) {
            $featuresFilter = !empty($_GET['features']) ? array_filter(
                $_GET['features'],
                function ($element) {
                    return trim($element) != '';
                }
            ) : [];

            $propertiesIds = [];
            $propertyIdsFilter = false;
            $propertiesComparison = [];

            if (!empty($featuresFilter)) {
                foreach ($featuresFilter as $resource => $value) {
                    $ids = $this->model->getAllPropertiesByResourceValue($resource, $value, ['id_branch' => $_SESSION['RR']->branch->current->id, 'status' => 1], 0, ['filter' => $propertyIdsFilter, 'properties' => $propertiesIds]);
                    if (!empty($propertiesIds)) {
                        $propertiesIds = array_intersect($propertiesIds, $ids);
                        $propertiesComparison = $propertiesIds;

                        if (empty($propertiesComparison)) {
                            break;
                        }
                    } else {
                        $propertiesIds = $ids;
                    }
                    $propertyIdsFilter = true;
                }

                $_GET['id'] = (!empty($propertiesIds)) ? $propertiesIds : ['null'];
            }
        }

        if (isset($_GET['cod'])) {
            $_GET['cod'] = trim($_GET['cod']);
        }

        if (!isset($_GET['id_branch'])) {
            $_GET['id_branch'] = $_SESSION['RR']->branch->current->id;
        }

        if (!isset($_GET['property_branch'])) {
            $_GET['property_branch'] = array_map(function ($element) {
                return $element->id;
            }, (new Branch)->getBranchesByProperties());
        }

        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        if (isset($_GET['id_user_owner']) && $_GET['id_user_owner'] == 'me') {
            $this->page->id = 28;
            $_GET['id_user_owner'] = $_SESSION['RR']->user->id;
        }

        if (!isset($_GET['availability'])) {
            $_GET['availability'] = 1;
        }

        if (isset($_GET['price_m']['start']) && !empty($_GET['price_m']['start'])) {
            $_GET['price']['start'] = Util::unmaskMoney($_GET['price_m']['start']);
        }

        if (isset($_GET['price_m']['end']) && !empty($_GET['price_m']['end'])) {
            $_GET['price']['end'] = Util::unmaskMoney($_GET['price_m']['end']);
        }

        if (!isset($_GET['order'])) {
            $_GET['order'] = 1;
        }

        switch ($_GET['order']) {
            case '1':
                $order = "identifier, {$this->table}.name ASC";
                break;
            case '2':
                $order = "identifier DESC, {$this->table}.name ASC";
                break;
            case '3':
                $order = "{$this->table}.name ASC";
                break;
            case '4':
                $order = "{$this->table}.name DESC";
                break;
            case '5':
                $order = "{$this->table}.value ASC, {$this->table}.name ASC";
                break;
            case '6':
                $order = "{$this->table}.value DESC, {$this->table}.name ASC";
                break;
            case '7':
                $order = "{$this->table}.created_at ASC";
                break;
            case '8':
                $order = "{$this->table}.created_at DESC";
                break;
        }

        $rows = 20;
        $page = Pagination::getPage();

        $response = $this->model->getAndFilterAllItems($_GET, ['limit' => $rows, 'page' => $page, 'orderBy' => $order]);
        $allProperties = $this->model->getWithFiltersAllItems([], [], ['orderBy' => 'products.cod ASC']);

        array_map(function ($property) {
            $image = $this->model->getTheFirstImageOfTheProperty($property->id);
            $property->urlImageXS = URL . (isset($image->id) ? "img/products_imgs/{$property->id}/{$image->id}xs.{$image->extension}" : "img/products_imgs/default/img-property-default.png");
            $property->city_name = ucwords(mb_strtolower($property->city_name), " ");
            $property->branch_name = implode('<br>', explode(',', $property->branch_name));

            if (empty($property->property_classification_name)) $property->property_classification_name = " - ";

            $property->total_area = str_replace(".", ",", $property->total_area);

            if (!empty($this->siteConfig->url_global)) {
                $property->siteURL = $property->site_status ? "{$this->siteConfig->url_global}imovel/{$property->url}" : URL . "{$this->route}/site/{$property->id}";
            }

            if ($property->status == true) {
                if ($property->availability == 1) {
                    $property->labelClass = 'green';
                    $property->labelText = 'Disponível';
                } else {
                    $property->labelClass = 'red';
                    $property->labelText = 'Indisponível';
                }
            } else {
                $property->labelClass = 'gray';
                $property->labelText = 'Inativo';
            }
        }, $response->data);

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        $customers = (new Customer())->getAllCustomerWithProperties(['status' => 1, 'id_branch' => $_SESSION['RR']->branch->current->id]);
        $branches = (new Branch)->getBranchesByProperties();
        $cities = $this->model->getCitiesForProperties(['status' => true, 'id_branch' => $_SESSION['RR']->branch->current->id]);

        array_map(function ($city) {
            $city->name = Util::titleCase($city->name);
        }, $cities);
        $propertyTypes = (new PropertyType())->getAndFilterAllItem(['status' => true], 0);
        $propertyCategories = (new PropertyCategory())->getAndFilterAllPropertyCategory(0, ['status' => true], 0);

        $features = (new ImmovableResource())->getAndFilterAllItem(["status" => true, "filter" => true], 0);
        array_map(function ($feature) {
            switch ($feature->data_type) {
                case '1':
                    /**Texto */
                    $feature->inputType = "text";
                    $feature->inputAttr = "";
                    break;
                case '2':
                    /**Número */
                    $feature->inputType = "number";
                    $feature->inputAttr = "";
                    break;
                case '3':
                    /**Data */
                    $feature->inputType = "date";
                    $feature->inputAttr = "";
                    break;
                case '4':
                    /**Sim / Não */

                    break;

                default:
                    $feature->inputType = "text";
                    $feature->inputAttr = "";
                    break;
            }
        }, $features->data);

        if (Secure::access_secretary()) {
            $users =  (new User())->getAndFilterAllUsers(0, ["id_branch_and_profile" => $_SESSION['RR']->branch->current->id, "status" => true, "order" => " up.access ASC, u.name ASC"], 0)->data;
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitCloneProperty()
    {
        Secure::check_post_method($this->route . "/addItem");

        $property = $this->model->getItemById($_POST['property_id']);

        $arrayPost = array(
            'id_owner' => $property->id_owner,
            'id_property_category' => $property->id_property_category,
            'id_property_classification' => $property->id_property_classification,
            'cod' => $_POST['newCod'],
            'name' => trim($property->name),
            'id_residential_type' => $property->id_residential_type,
            'id_city' => $property->id_city,
            'uf_state' => $property->uf_state,
            'allotment' => $property->allotment,
            'neighborhood' => $property->neighborhood,
            'address' => $property->address,
            'number' => $property->number,
            'total_area' => $property->total_area,
            'complement' => $property->complement,
            "lat" => !empty($property->lat) ? $property->lat : "-27.244972",
            "lng" => !empty($property->lng) ? $property->lng : "-48.640851",
            'value' => Util::maskMoney($property->value),
            'installment_value' => Util::maskMoney($property->installment_value),
            'condition_product' => $property->condition_product,
            're_registered_at' => !empty(trim($property->re_registered_at)) ? $property->re_registered_at : NULL,
            "site_name" => trim($property->name),
            "site_complement" => Util::slugify($property->complement),
            "site_value" => Util::maskMoney($property->value),
            "site_description" => $property->description,
            'slugify' => Util::slugify($property->name),
            'description' => $property->description,
            'created_at' => $property->created_at,
            'site_name_complement' => $property->site_name_complement
        );

        $response = $this->model->handleFormAdd($arrayPost);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];

        if (!$response->error) {
            $interested = $this->interestedUsers($response->lastId);

            if ($interested->count > 0) {
                foreach ($interested->data as $interest) {
                    $statusAttendance = (new AttendanceStatus)->getItemById($interest->id_status);

                    $arrayPostNotification = [
                        'icon' => 'fa fa-building',
                        'route' => "attendance/attendance/" . $interest->id,
                        'title' => "Você tem um atendimento <strong>" . $statusAttendance->name . "</strong> com interesse no imóvel recém cadastrado.",
                        'description' => "Cliente " . $interest->name . " tem interesse no imóvel " . $response->item->cod . " - " . $response->item->name,
                        'id_branch' => $interest->id_branch,
                    ];

                    $notification = (new Notification)->insert($arrayPostNotification);

                    $arrayPostNotificationRead = [
                        'id_notification' => $notification->lastId,
                        'id_user' => $interest->created_by,
                    ];

                    (new NotificationRead)->insert($arrayPostNotificationRead);
                }
            }

            redirect("{$this->route}/editItem/$response->lastId");
        } else {
            redirect("{$this->route}/addItem");
        }
    }

    public function addItem()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/product.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/addressMaps.js");

        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        if (!empty($_GET['pg2'])) {
            $customer = (new Customer)->getItemById(
                $_GET['pg2'],
                [
                    (object)['columns' => ['*']],
                    (object)['table' => 'cities', 'columns' => ['name']]
                ]
            );

            array_map(function ($item) {
                $item->option_name = $item->fancy_name_company ?? ($item->company_name ?? $item->name);
                $item->cpf = $item->cnpj ?? ($item->person_registration ?? "");
            }, [$customer]);
        }

        $modelGenerico = new ModelGenerico();
        $branchModel = new Branch();
        $propertyTypeModel = new PropertyType();
        $propertyCategoryModel = new PropertyCategory();
        $classificationModel = new PropertyClassification();

        $propertyTypes = $propertyTypeModel->getAndFilterAllItem(['status' => 1], 0)->data;
        $propertyCategories = $propertyCategoryModel->getAndFilterAllPropertyCategory(0, ['status' => 1], 0)->data;
        $branch = $branchModel->getItemById8161($_SESSION['RR']->branch->current->id);
        $city = $modelGenerico->getItemById8161($branch->id_city, "cities");
        $branchs = $branchModel->getAllBranch();

        $propertyClassification = $classificationModel->getAndFilterAllItem(["order" => " pcl.name ASC", "status" => 1], 0)->data;

        $states = $modelGenerico->getAllItens("states");
        $cities = $branchModel->getCitiesByState($city->uf);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/_templates/footer.php';
    }

    public function printRegister()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/product.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/addressMaps.js");

        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $propertyType = (new PropertyType)->getAllImmovableResoursesName();
        $branchs = (new Branch)->getAllBranch();

        require APP . 'view/' . $this->dir . '/print-property.php';
    }

    public function print()
    {
        if (isset($_GET['features'])) {
            $featuresFilter = !empty($_GET['features']) ? array_filter(
                $_GET['features'],
                function ($element) {
                    return trim($element) != '';
                }
            ) : [];

            $propertiesIds = [];
            $propertyIdsFilter = false;
            $propertiesComparison = [];

            if (!empty($featuresFilter)) {
                foreach ($featuresFilter as $resource => $value) {
                    $ids = $this->model->getAllPropertiesByResourceValue($resource, $value, ['id_branch' => $_SESSION['RR']->branch->current->id, 'status' => 1], 0, ['filter' => $propertyIdsFilter, 'properties' => $propertiesIds]);
                    if (!empty($propertiesIds)) {
                        $propertiesIds = array_intersect($propertiesIds, $ids);
                        $propertiesComparison = $propertiesIds;

                        if (empty($propertiesComparison)) {
                            break;
                        }
                    } else {
                        $propertiesIds = $ids;
                    }
                    $propertyIdsFilter = true;
                }

                $_GET['id'] = (!empty($propertiesIds)) ? $propertiesIds : ['null'];
            }
        }

        if (!isset($_GET['b'])) {
            if (!isset($_GET['columnPropertyIdName'])) {
                $_GET['columnPropertyIdName'] = 'on';
            }

            if (!isset($_GET['columnPropertyType'])) {
                $_GET['columnPropertyType'] = 'on';
            }

            if (!isset($_GET['columnPropertyLocation'])) {
                $_GET['columnPropertyLocation'] = 'on';
            }

            if (!isset($_GET['columnPropertyValue'])) {
                $_GET['columnPropertyValue'] = 'on';
            }

            if (!isset($_GET['columnPropertyStatus'])) {
                $_GET['columnPropertyStatus'] = 'on';
            }
        } else {
            if (!isset($_GET['columnPropertyIdName'])) {
                $_GET['columnPropertyIdName'] = 'off';
            }

            if (!isset($_GET['columnPropertyType'])) {
                $_GET['columnPropertyType'] = 'off';
            }

            if (!isset($_GET['columnPropertyLocation'])) {
                $_GET['columnPropertyLocation'] = 'off';
            }

            if (!isset($_GET['columnPropertyValue'])) {
                $_GET['columnPropertyValue'] = 'off';
            }

            if (!isset($_GET['columnPropertyStatus'])) {
                $_GET['columnPropertyStatus'] = 'off';
            }
        }

        if (isset($_GET['cod'])) {
            $_GET['cod'] = trim($_GET['cod']);
        }

        if (!isset($_GET['property_branch'])) {
            $_GET['property_branch'] = array_map(function ($element) {
                return $element->id;
            }, (new Branch)->getBranchesByProperties());
        }

        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        if (isset($_GET['id_user_owner']) && $_GET['id_user_owner'] == 'me') {
            $this->page->id = 28;
            $_GET['id_user_owner'] = $_SESSION['RR']->user->id;
        }

        if (!isset($_GET['availability'])) {
            $_GET['availability'] = 1;
        }

        if (isset($_GET['price_m']['start']) && !empty($_GET['price_m']['start'])) {
            $_GET['price']['start'] = Util::unmaskMoney($_GET['price_m']['start']);
        }

        if (isset($_GET['price_m']['end']) && !empty($_GET['price_m']['end'])) {
            $_GET['price']['end'] = Util::unmaskMoney($_GET['price_m']['end']);
        }

        if (!isset($_GET['order'])) {
            $_GET['order'] = 1;
        }

        switch ($_GET['order']) {
            case '1':
                $order = "identifier, {$this->table}.name ASC";
                break;
            case '2':
                $order = "identifier DESC, {$this->table}.name ASC";
                break;
            case '3':
                $order = "{$this->table}.name ASC";
                break;
            case '4':
                $order = "{$this->table}.name DESC";
                break;
            case '5':
                $order = "{$this->table}.value ASC, {$this->table}.name ASC";
                break;
            case '6':
                $order = "{$this->table}.value DESC, {$this->table}.name ASC";
                break;
            case '7':
                $order = "{$this->table}.created_at ASC";
                break;
            case '8':
                $order = "{$this->table}.created_at DESC";
                break;
        }

        $response = $this->model->getPropertyReport($_GET, ['orderBy' => $order]);

        array_map(function ($property) {
            $property->city_name = ucwords(mb_strtolower($property->city_name), " ");

            $property->branch_name = implode('<br>', explode(',', $property->branch_name));

            if ($property->status == true) {
                if ($property->availability == 1) {
                    $property->labelText = 'Disponível';
                } else {
                    $property->labelText = 'Indisponível';
                }
            } else {
                $property->labelText = 'Inativo';
            }
        }, $response->data);

        $cities = $this->model->getCitiesForProperties(['status' => true, 'id_branch' => $_SESSION['RR']->branch->current->id]);

        array_map(function ($city) {
            $city->name = Util::titleCase($city->name);
        }, $cities);

        require APP . 'view/' . $this->dir . '/print.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $response = $this->model->handleFormAdd($_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error ? 'error' : 'success'),
            'title' => $response->message,
        ];

        if (!$response->error) {
            $interested = $this->interestedUsers($response->lastId);

            if ($interested->count > 0) {
                foreach ($interested->data as $interest) {
                    $statusAttendance = (new AttendanceStatus)->getItemById($interest->id_status);

                    $arrayPostNotification = [
                        'icon' => 'fa fa-building',
                        'route' => "attendance/attendance/" . $interest->id,
                        'title' => "Você tem um atendimento <strong>" . $statusAttendance->name . "</strong> com interesse no imóvel recém cadastrado.",
                        'description' => "Cliente " . $interest->name . " tem interesse no imóvel " . $response->item->cod . " - " . $response->item->name,
                        'id_branch' => $interest->id_branch,
                    ];

                    $notification = (new Notification)->insert($arrayPostNotification);

                    $arrayPostNotificationRead = [
                        'id_notification' => $notification->lastId,
                        'id_user' => $interest->created_by,
                    ];

                    (new NotificationRead)->insert($arrayPostNotificationRead);
                }
            }

            redirect("{$this->route}/editItem/$response->lastId");
        } else {
            redirect("{$this->route}/addItem");
        }
    }

    public function editItem($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/product.js");
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");

        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $productsModel = new Property();
        $propertyTypeModel = new PropertyType();
        $propertyCategoryModel = new PropertyCategory();
        $propertyClassificationModel = new PropertyClassification();
        $branchModel = new Branch();
        $userModel = new User();
        $customerModel = new Customer();

        $branch = $branchModel->getItemById($_SESSION['RR']->branch->current->id);
        $product = $this->model->getProductsById($productId);

        if (empty($product)) {
            header('location:' . URL . $this->route . "?error=error");
            exit;
        }

        array_map(function ($item) {
            $item->total_area = str_replace(".", ",", $item->total_area);
        }, [$product]);

        /**Cliente Proprietário desse Imóvel */
        $owner = $customerModel->getCustomerById($product->id_owner);
        $user = $userModel->getUserById($_SESSION['RR']->user->id);
        $branchById = $branchModel->getItemById($product->id_branch);
        $propertyTypes = $propertyTypeModel->getAndFilterAllItem(['status' => 1], 0)->data;
        $states = (new ModelGenerico)->getAllItens("states");
        $cities = $branchModel->getCitiesByState($product->uf_state);
        $propertyCategory = $propertyCategoryModel->getAllPropertyCategory();
        $propertyClassification = $propertyClassificationModel->getAndFilterAllItem(["order" => " pcl.name ASC", "status" => 1], 0)->data;

        $restrictDataValidation = $_SESSION['RR']->profile->access < 30 || $branch->restrict_owner_data == 0 || $owner->id_customer_type == 11 ? 1 : 0;

        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        if (empty($product->branches)) {
            $product->branches = array_map(function ($branch) {
                return $branch->id;
            }, $branchModel->getAllBranch());
        }

        Secure::productsBranches($product->branches, $this->route . "?edited=false");

        $allBranches = $branchModel->getAllBranch();

        $restrictData = $branch->restrict_owner_data == 0 ?: false;

        $users = $userModel->getAndFilterAllUsers(0, ["id_branch_and_profile" => $_SESSION['RR']->branch->current->id, "status" => true, "order" => " up.access ASC, u.name ASC"], 0)->data;

        $newReCreatedAt = !empty($branchById->immovable_record) ? date("Y-m-d", strtotime("+" . $branchById->immovable_record . " month " . date("Y-m-d"))) : date("Y-m-d");

        $permissionSellerManager = in_array($product->created_by, array_map(function ($seller) {
            return $seller->id_seller;
        }, (new ManagerTeam)->getAllSellersFromManager($_SESSION['RR']->user->id)->data));

        $permission = Secure::access_secretary() && Secure::seller_manager() || Secure::creator($product->created_by) || $permissionSellerManager;
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";
        $classAddonDisabled = $permission ? "" : "addon-disabled";

        $logsReCreatedAt = $this->model->getLogByProductId($productId);
        $userCreated = $userModel->getUserById($product->created_by);
        $userUpdated = $userModel->getUserById($product->updated_by);

        /**Menu */
        $immovables = $this->model->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");


        $item = $this->model->getItemById($itemId);

        // Segurança da edição do criador do cadastro
        Secure::redirectFunction((!Secure::creator($item->created_by) && !Secure::access_admin() && Secure::secretary() && !Secure::access_manager()), $this->route);

        $codCount = $this->model->getWithFiltersAllItems([
            (object)[
                'columns' => [
                    'id' => (object)['comparison' => 'NOT_EQUAL', 'value' => $itemId],
                    'cod' => (object)['comparison' => 'EQUAL', 'value' => $_POST['cod']],
                ]
            ]
        ])->count;

        $insertArray = array(
            'cod' => ($codCount == 0) ? trim($_POST['cod']) : '',
            'name' => trim($_POST['name']),
            'id_residential_type' => $_POST['id_residential_type'],
            'id_property_category' => $_POST['id_property_category'],
            'id_property_classification' => $_POST['id_property_classification'],
            'id_owner' => $_POST['id_owner'],
            'uf_state' => $_POST['uf_state'],
            'id_city' => $_POST['id_city'],
            'neighborhood' => $_POST['neighborhood'],
            'allotment' => $_POST['allotment'],
            'address' => $_POST['address'],
            'number' => $_POST['number'],
            'total_area' => Util::removeNonNumericForFloat($_POST['total_area']),
            'complement' => $_POST['complement'],
            'value' => Util::unMaskMoney($_POST['value']),
            'installment_value' => Util::unMaskMoney($_POST['installment_value']),
            'condition_product' => $_POST['condition_product'],
            'status' => $_POST['status'],
            'site_status' => (!$_POST['status'] ? 0 : $item->site_status),
            'url' => Util::slugify((!empty($item->site_name) ? trim($item->site_name) : trim($item->name)) . "-" . ($_POST['cod'] && !empty($_POST['cod']) ? trim($_POST['cod']) : $itemId)),
            'description' => $_POST['description'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
            'id_user_owner' => $_POST['id_user_owner'],
        );

        $response = $this->model->handleFormEdit($itemId, $insertArray, $_POST);

        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error ? 'error' : 'success'),
            'title' => $response->message,
        ];

        if (!$response->error) {
            $interested = $this->interestedUsers($itemId);

            if ($interested->count > 0) {
                foreach ($interested->data as $interest) {
                    $statusAttendance = (new AttendanceStatus)->getItemById($interest->id_status);

                    $arrayPostNotification = [
                        'icon' => 'fa fa-building',
                        'route' => "attendance/attendance/" . $interest->id,
                        'title' => "Você tem um atendimento <strong>" . $statusAttendance->name . "</strong> com interesse no imóvel recém editado.",
                        'description' => "Cliente " . $interest->name . " tem interesse no imóvel " . $item->cod . " - " . $item->name,
                        'id_branch' => $interest->id_branch,
                    ];

                    $notification = (new Notification)->insert($arrayPostNotification);

                    $arrayPostNotificationRead = [
                        'id_notification' => $notification->lastId,
                        'id_user' => $interest->created_by,
                    ];

                    (new NotificationRead)->insert($arrayPostNotificationRead);
                }
            }
        }

        redirect($this->route . "/editItem/$itemId");
    }

    public function handleSubmitUpdateReCreatedAt($productId)
    {
        Secure::check_post_method($this->route . "/editItem/$productId/?error=error");

        $product = (new Property)->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            $arrPost = array(
                'id_product' => $productId,
                'last_re_registered_at' => !empty(trim($_POST['re_registered_at'])) ? $_POST['re_registered_at'] : NULL,
                'new_re_registered_at' => !empty(trim($_POST['new_re_registered_at'])) ? $_POST['new_re_registered_at'] : NULL,
                'created_by' => $_SESSION['RR']->user->id
            );

            (new GerenciaPost())->insert7181($arrPost, "timeline_re_registered_at_products", false, false);
            (new GerenciaPost())->update8191(['re_registered_at' => (!empty(trim($_POST['new_re_registered_at'])) ? $_POST['new_re_registered_at'] : NULL)], "products", "id", $productId, false);

            header('location:' . URL . $this->route . "/editItem/$productId/?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/editItem/$productId/?edited=false");
            exit;
        }
    }

    public function map($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/scriptMap.js");
        $this->addScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyBXb984jOma4yop-zX7bqsy7Hcsgm5CCok&callback=initMap");
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");
        $permission = Secure::access_secretary() || Secure::creator($product->created_by);

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/map.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitMap($productId)
    {
        Secure::check_post_method($this->route . "/map/$productId/?error=error");

        if (!empty($_POST['latlng'])) {
            $latlng = explode(',', str_replace(')', '', str_replace('(', '', $_POST['latlng'])));
            $_POST['lat'] = $latlng[0];
            $_POST['lng'] = $latlng[1];
        }

        $product = (new Property)->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $arrPost = array('lat' => $_POST['lat'], 'lng' => $_POST['lng']);
        try {
            (new GerenciaPost)->update8191($arrPost, $this->table, "id", $productId, false);

            header('location:' . URL . $this->route . "/map/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/map/$productId?edited=false");
            exit;
        }
    }

    public function immovableResource($productId)
    {
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");

        $productOwnershipFeature = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        $permission = Secure::access_secretary() || Secure::creator($product->created_by);
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        array_map(function ($feature) {
            switch ($feature->immovable_resource_data_type) {
                case '1':
                    /**Texto */
                    $feature->inputType = "text";
                    $feature->inputAttr = "";
                    break;
                case '2':
                    /**Número */
                    $feature->inputType = "Number";
                    $feature->inputAttr = "";
                    break;
                case '3':
                    /**Data */
                    $feature->inputType = "date";
                    $feature->inputAttr = "";
                    break;
                case '4':
                    /**Sim / Não */

                    break;

                default:
                    $feature->inputType = "text";
                    $feature->inputAttr = "";
                    break;
            }
        }, $productOwnershipFeature);

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/immovable-resource.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitImmovableResource($productId)
    {
        Secure::check_post_method($this->route . "/immovable-resource/$productId?error=error");

        $product = (new Property())->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            foreach ($_POST as $key => $value) {
                $arrPost = array(
                    'value' => trim($value),
                    'slugify' => Util::slugify(trim($value)),
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => $_SESSION['RR']->user->id,
                );

                (new GerenciaPost())->update8191($arrPost, 'product_ownership_feature', 'id', $key, false);
            }

            header('location:' . URL . $this->route . "/immovable-resource/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/immovable-resource/$productId?edited=false");
            exit;
        }
    }

    public function photos($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/photos.js");
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");

        $productImgs = $productsModel->getProductsImages($productId);
        $waterMark = (new WaterMark())->getItemById8161(1);

        $permission = Secure::access_secretary() || Secure::creator($product->created_by);
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        array_map(function ($img) {
            $imgLg = "img/products_imgs/$img->id_product/{$img->id}lg.$img->extension";
            $sizeLg = getimagesize($imgLg);
            $img->sizes = "<br>" . $sizeLg[0] . " x " . $sizeLg[1];
        }, $productImgs);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/photos.php';
        require APP . 'view/_templates/footer.php';
    }

    public function updateImages($productId)
    {
        Secure::check_post_method($this->route . "/photos/$productId?error=error");

        $gerenciaPost = new GerenciaPost();
        $productsModel = new Property();

        /**Imagens */
        require_once APP . 'libs/wideImage/lib/WideImage.php';
        require_once APP . 'libs/wideImage/wide.php';
        require_once APP . 'libs/Resizer.php';

        $productId = $_POST['id_product'];
        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {

            if (!empty($_FILES['photo']['tmp_name'][0])) {
                if (!file_exists("img/products_imgs/$productId")) {
                    mkdir("img/products_imgs/$productId", 0777, true);
                }

                for ($i = 0; $i <= count($_FILES['photo']['name']) - 1; $i++) {
                    $extension = str_replace(".", "", substr($_FILES['photo']['name'][$i], -4));

                    if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG" || $extension == "png" || $extension == "PNG") {
                        $order = $productsModel->getLastOrder($productId, "products_imgs");
                        $order = $order ? $order->item_order + 1 : 1;
                        $arrayPost = array(
                            'id_product' =>  $productId,
                            'extension' => $extension,
                            "item_order" => $order,
                            "status_site" => 0
                        );
                        $imageId = $gerenciaPost->insert7181($arrayPost, "products_imgs", true, false);

                        $filename = $_FILES['photo']['tmp_name'][$i];
                        $path = "img/products_imgs/$productId/$imageId";
                        $pasta_base = "img/products_imgs/$productId";

                        $waterMark = (new WaterMark())->getItemById8161(1);

                        if ($waterMark->capa != 0 && ($waterMark->water_mark_required == 1 || isset($_POST['water_mark']))) {
                            //$waterMark tem que ter posição(horizontal[1,2,3],vertical[1,2,3]), cont, ext, opacidade
                            $lg = resize1($pasta_base, $imageId, 1024, 1024, 1, "lgTp", $filename, $extension);
                            $lgWaterMark = wideImagePhotoWatermarkNoResize(URL . "img/products_imgs/$productId/{$imageId}lgTp.$extension", $path, 1024, 1024, 'lg', ".$extension", null, $waterMark);
                            @unlink("img/products_imgs/$productId/{$imageId}lgTp.$extension");
                        } else {
                            $lg = resize1($pasta_base, $imageId, 1024, 1024, 1, "lg", $filename, $extension);
                        }

                        if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                            $md = wideImagePhoto($filename, $path, 500, 340, "", "md.{$extension}", 100);
                            $xs = wideImagePhoto($filename, $path, 70, 70, "", "xs.{$extension}", 100);
                        } else if ($extension == "png" || $extension == "PNG") {
                            $md = wideImagePhoto($filename, $path, 500, 340, "", "md.{$extension}", 9);
                            $xs = wideImagePhoto($filename, $path, 70, 70, "", "xs.{$extension}", 9);
                        }
                    }
                }
            }

            if (!empty($_POST['descriptionImage'])) {
                $images = $_POST['descriptionImage'];
                $siteVisualization = $_POST['viewSite'];

                /**Descrição e  status*/
                foreach ($images as $id => $image) {
                    $arrayPost = array("description" => $image, "status_site" => !empty($siteVisualization[$id]) ? 1 : 0);
                    $gerenciaPost->update8191($arrayPost, "products_imgs", "id", $id, false);
                }
                $productImgs = $productsModel->getProductsImagesActiveSite($productId);

                if (empty($productImgs)) {
                    $gerenciaPost->update8191(["site_status" => 0], $this->table, "id", $productId, false);
                }
            }

            header('location:' . URL . $this->route . "/photos/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/photos/$productId?edited=false");
            exit;
        }
    }

    public function handleDeletePhotos($photosId)
    {
        $photos = (new ModelGenerico())->getItemById8161($photosId, "products_imgs");
        Secure::check_post_method($this->route . "/photos/$photos->id_product?error=error");

        $product = (new Property())->getProductsById($photos->id_product);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            DeleteFile::DeleteFile(
                [$photosId],
                "products_imgs",
                [
                    "img/products_imgs/$photos->id_product/{$photos->id}xs.$photos->extension",
                    "img/products_imgs/$photos->id_product/{$photos->id}md.$photos->extension",
                    "img/products_imgs/$photos->id_product/{$photos->id}lg.$photos->extension"
                ]
            );

            $productImgs = (new Property())->getProductsImagesActiveSite($photos->id_product);

            if (empty($productImgs)) {
                (new GerenciaPost())->update8191(["site_status" => 0], $this->table, "id", $photos->id_product, false);
            }
            header('location:' . URL . $this->route . "/photos/$photos->id_product?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/photos/$photos->id_product?deleted=false");
            exit;
        }
    }

    public function deleteAllImages($productId)
    {
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $productImgs = $productsModel->getProductsImages($productId);
        $images = ["ids" => [], "paths" => []];

        try {
            foreach ($productImgs as $img) {
                $images["ids"][] = $img->id;
                array_push(
                    $images["paths"],
                    "img/products_imgs/$img->id_product/{$img->id}xs.$img->extension",
                    "img/products_imgs/$img->id_product/{$img->id}md.$img->extension",
                    "img/products_imgs/$img->id_product/{$img->id}lg.$img->extension"
                );
            }

            DeleteFile::deleteFile($images["ids"], "products_imgs", $images["paths"]);
            (new GerenciaPost())->update8191(["site_status" => 0], $this->table, "id", $productId, false);

            header('location:' . URL . $this->route . "/photos/$productId?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/photos/$productId?deleted=false");
            exit;
        }
    }

    public function disableSiteView($imageId, $productId)
    {
        $productsModel = new Property();
        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            (new GerenciaPost())->update8191(['status_site' => 0], "products_imgs", "id", $imageId, false);

            header('location:' . URL . $this->route . "/photos/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/photos/$productId?edited=false");
            exit;
        }
    }

    public function ableSiteView($imageId, $productId)
    {
        $productsModel = new Property();
        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            (new GerenciaPost())->update8191(['status_site' => 1], "products_imgs", "id", $imageId, false);

            header('location:' . URL . $this->route . "/photos/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/photos/$productId?edited=false");
            exit;
        }
    }

    public function disableAllSiteView($productId)
    {
        $modelGenerico = new ModelGenerico();
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $allImage = $modelGenerico->getItemByGenericField($productId, "products_imgs", "id_product");

        try {
            foreach ($allImage as $image) {
                (new GerenciaPost())->update8191(['status_site' => 0], "products_imgs", "id", $image->id, false);
            }

            header('location:' . URL . $this->route . "/photos/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/photos/$productId?edited=false");
            exit;
        }
    }

    public function ableAllSiteView($productId)
    {
        $modelGenerico = new ModelGenerico();
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $allImage = $modelGenerico->getItemByGenericField($productId, "products_imgs", "id_product");

        try {
            foreach ($allImage as $image) {
                (new GerenciaPost())->update8191(['status_site' => 1], "products_imgs", "id", $image->id, false);
            }

            header('location:' . URL . $this->route . "/photos/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/photos/$productId?edited=false");
            exit;
        }
    }

    public function progress($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");

        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");

        $productImgs = $productsModel->getProductsImagesProgress($productId);
        $waterMark = (new WaterMark())->getItemById8161(1);

        $permission = Secure::access_secretary() || Secure::creator($product->created_by);
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/progress.php';
        require APP . 'view/_templates/footer.php';
    }

    public function updateImagesProgress($productId)
    {
        Secure::check_post_method($this->route . "/progress/$productId?error=error");

        if (empty($_FILES['photo']['tmp_name'][0])) {
            redirect($this->route . "/progress/$productId?error=error");
        }

        $gerenciaPost = new GerenciaPost();
        $productsModel = new Property();

        /**Imagens */
        require_once APP . 'libs/wideImage/lib/WideImage.php';
        require_once APP . 'libs/wideImage/wide.php';
        require_once APP . 'libs/Resizer.php';

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            /**imagens */
            if (!file_exists("img/products_imgs/$productId")) {
                mkdir("img/products_imgs/$productId", 0777, true);
            }

            for ($i = 0; $i <= count($_FILES['photo']['name']) - 1; $i++) {
                $extension = str_replace(".", "", substr($_FILES['photo']['name'][$i], -4));

                if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG" || $extension == "png" || $extension == "PNG") {
                    $order = $productsModel->getLastOrder($productId, "products_imgs_progress");
                    $order = $order ? $order->item_order + 1 : 1;
                    $arrayPost = array(
                        'id_product' =>  $productId,
                        'extension' => $extension,
                        "item_order" => $order,
                        "status_site" => 0,
                        "date_progress" => !empty(trim($_POST['date_progress'])) ? $_POST['date_progress'] : NULL
                    );

                    $imageId = $gerenciaPost->insert7181($arrayPost, "products_imgs_progress", true, false);

                    $filename = $_FILES['photo']['tmp_name'][$i];
                    $path = "img/products_imgs/$productId/$imageId";
                    $pasta_base = "img/products_imgs/$productId";

                    $waterMark = (new WaterMark())->getItemById8161(1);

                    if ($waterMark->capa != 0 && ($waterMark->water_mark_required == 1 || isset($_POST['water_mark']))) {
                        //$waterMark tem que ter posição(horizontal[1,2,3],vertical[1,2,3]), cont, ext, opacidade
                        $lg = resize1($pasta_base, $imageId, 1024, 1024, 1, "lgTp", $filename, $extension);
                        $lgWaterMark = wideImagePhotoWatermarkNoResize(URL . "img/products_imgs/$productId/{$imageId}lgTp.$extension", $path, 1024, 1024, 'and-lg', ".$extension", null, $waterMark);
                        @unlink("img/products_imgs/$productId/{$imageId}lgTp.$extension");
                    } else {
                        $lg = resize1($pasta_base, $imageId, 1024, 1024, 1, "and-lg", $filename, $extension);
                    }

                    if ($extension == "jpg" || $extension == "JPG" || $extension == "jpeg" || $extension == "JPEG") {
                        $md = wideImagePhoto($filename, $path, 500, 340, "", "and-md.{$extension}", 100);
                        $xs = wideImagePhoto($filename, $path, 70, 70, "", "and-xs.{$extension}", 100);
                    } else if ($extension == "png" || $extension == "PNG") {
                        $md = wideImagePhoto($filename, $path, 500, 340, "", "and-md.{$extension}", 9);
                        $xs = wideImagePhoto($filename, $path, 70, 70, "", "and-xs.{$extension}", 9);
                    }
                }
            }

            /**Ativo para o site */
            if (isset($_POST['item_aux']) && !empty($_POST['item_aux'])) {
                $aux = $_POST['item_aux'];
                $siteVisualization = $_POST['viewSite'];
                foreach ($aux as $id => $value) {
                    $arrayPost =  array("status_site" => !empty($siteVisualization[$id]) ? 1 : 0);
                    $gerenciaPost->update8191($arrayPost, "products_imgs_progress", "id", $id, false);
                }
            }

            header('location:' . URL . $this->route . "/progress/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/progress/$productId?edited=false");
            exit;
        }
    }

    public function handleDeleteProgress($photosId)
    {
        $photos = (new ModelGenerico())->getItemById8161($photosId, "products_imgs_progress");
        Secure::check_post_method($this->route . "/progress/$photos->id_product?error=error");

        $product = (new Property)->getProductsById($photos->id_product);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            DeleteFile::DeleteFile(
                [$photosId],
                "products_imgs_progress",
                [
                    "img/products_imgs/$photos->id_product/{$photosId}adn-xs.$photos->extension",
                    "img/products_imgs/$photos->id_product/{$photosId}adn-md.$photos->extension",
                    "img/products_imgs/$photos->id_product/{$photosId}adn-lg.$photos->extension"
                ]
            );

            header('location:' . URL . $this->route . "/progress/$photos->id_product?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/progress/$photos->id_product?deleted=false");
            exit;
        }
    }

    public function deleteAllImagesProgress($productId)
    {
        $productsModel = new Property();

        $product = (new Property)->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $productImgs = $productsModel->getProductsImagesProgress($productId);
        $images = array("ids" => [], "paths" => []);

        try {
            foreach ($productImgs as $img) {
                $images["ids"][] = $img->id;
                array_push(
                    $images["paths"],
                    "img/products_imgs/$productId/{$img->id}and-xs.$img->extension",
                    "img/products_imgs/$productId/{$img->id}and-md.$img->extension",
                    "img/products_imgs/$productId/{$img->id}and-lg.$img->extension"
                );
            }

            DeleteFile::deleteFile($images["ids"], "products_imgs_progress", $images["paths"]);

            header('location:' . URL . $this->route . "/progress/$productId?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/progress/$productId?deleted=false");
            exit;
        }
    }

    public function disableSiteViewProgress($imageId, $productId)
    {
        $productsModel = new Property();
        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            (new GerenciaPost())->update8191(['status_site' => 0], "products_imgs_progress", "id", $imageId, false);

            header('location:' . URL . $this->route . "/progress/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/progress/$productId?edited=false");
            exit;
        }
    }

    public function ableSiteViewProgress($imageId, $productId)
    {
        $productsModel = new Property();
        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            (new GerenciaPost())->update8191(['status_site' => 1], "products_imgs_progress", "id", $imageId, false);

            header('location:' . URL . $this->route . "/progress/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/progress/$productId?edited=false");
            exit;
        }
    }

    public function disableAllSiteViewProgress($productId)
    {
        $modelGenerico = new ModelGenerico();
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $allImage = $modelGenerico->getItemByGenericField($productId, "products_imgs_progress", "id_product");

        try {
            foreach ($allImage as $image) {
                (new GerenciaPost())->update8191(['status_site' => 0], "products_imgs_progress", "id", $image->id, false);
            }

            header('location:' . URL . $this->route . "/progress/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/progress/$productId?edited=false");
            exit;
        }
    }

    public function ableAllSiteViewProgress($productId)
    {
        $modelGenerico = new ModelGenerico();
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $allImage = $modelGenerico->getItemByGenericField($productId, "products_imgs_progress", "id_product");

        try {
            foreach ($allImage as $image) {
                (new GerenciaPost())->update8191(['status_site' => 1], "products_imgs_progress", "id", $image->id, false);
            }

            header('location:' . URL . $this->route . "/progress/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/progress/$productId?edited=false");
            exit;
        }
    }

    public function videos($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");

        $productVideos = $productsModel->getProductsVideos($productId);

        $permission = Secure::access_secretary() || Secure::creator($product->created_by);
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId, false);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/videos.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitVideos($productId)
    {
        Secure::check_post_method($this->route . "/videos/$productId?error=error");

        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $order = $productsModel->getLastOrder($productId, "products_videos")->item_order + 1;
        $arrPost = array(
            "id_product" => $productId,
            "name" => $_POST["name"],
            "link_videos" => $_POST["link_videos"],
            "item_order" => $order
        );

        try {
            (new GerenciaPost)->insert7181($arrPost, 'products_videos', null, false);

            header('location:' . URL . $this->route . "/videos/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/videos/$productId?edited=false");
            exit;
        }
    }

    public function handleDeleteVideos($videosId)
    {
        $videos = (new ModelGenerico())->getItemById8161($videosId, "products_videos");
        Secure::check_post_method($this->route . "/videos/$videos->id_product?error=error");

        $product = (new Property())->getProductsById($videos->id_product);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            (new ModelGenerico())->deleteItemByCampoGenerico("products_videos", "id", $videosId);

            header('location:' . URL . $this->route . "/videos/$videos->id_product?deleted=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/videos/$videos->id_product?deleted=false");
            exit;
        }
    }

    public function attachment($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");

        $productAttachments = $productsModel->getProductsAttachments($productId);

        $permission = Secure::access_secretary() || Secure::creator($product->created_by);
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/attachment.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAttachment($productId)
    {
        Secure::check_post_method($this->route . "/attachment/$productId?error=error");

        if (empty($_FILES)) {
            redirect($this->route . "/attachment/$productId?error=error");
        }

        $product = (new Property())->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            $attachments = FileUploader::uploadFiles($_FILES['attachments'], array_fill(0, count($_FILES['attachments']) + 1, "attachments/products/$productId"));

            $order = (new Property())->getLastOrder($productId, "products_attachments")->item_order + 1;

            foreach ($attachments as $attachment) {
                $arrayPost = array(
                    "id_product" => $productId,
                    "name" => $_POST['name'],
                    "description" => $_POST["description"],
                    "filename" => $attachment['filename'],
                    "extension" => $attachment['extension'],
                    "item_order" => $order
                );

                (new GerenciaPost())->insert7181($arrayPost, "products_attachments", null, false);
            }

            header('location:' . URL . $this->route . "/attachment/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attachment/$productId?edited=false");
            exit;
        }
    }

    public function handleDeleteAttachment($attachmentId)
    {
        $modelGenerico = new ModelGenerico();
        $productsModel = new Property();

        $attachment = $modelGenerico->getItemById8161($attachmentId, "products_attachments");
        $product = $productsModel->getProductsById($attachment->id_product);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            DeleteFile::deleteFile(
                [$attachmentId],
                "products_attachments",
                ["attachments/products/$attachment->id_product/$attachment->filename.$attachment->extension"]
            );

            header('location:' . URL . $this->route . "/attachment/$attachment->id_product?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/attachment/$attachment->id_product?edited=false");
            exit;
        }
    }

    public function priceList($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");

        $productPriceList = $productsModel->getProductsPriceList($productId);

        $permission = Secure::access_secretary() || Secure::creator($product->created_by);
        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/price-list.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitPriceList($productId)
    {
        Secure::check_post_method($this->route . "/price-list/$productId?error=error");

        if (empty($_FILES)) {
            redirect($this->route . "/price-list/$productId?error=error");
        }

        $product = (new Property())->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            $priceLists = FileUploader::uploadFiles($_FILES['price_list'], array_fill(0, count($_FILES['price_list']) + 1, "priceList/products/$productId"));

            $order = (new Property())->getLastOrder($productId, "products_priceList")->item_order + 1;

            foreach ($priceLists as $priceList) {
                $arrayPost = array(
                    "id_product" => $productId,
                    "name" => $_POST['name'],
                    "filename" => $priceList['filename'],
                    "extension" => $priceList['extension'],
                    "item_order" => $order
                );

                (new GerenciaPost())->insert7181($arrayPost, "products_priceList", null, false);
            }

            header('location:' . URL . $this->route . "/price-list/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/price-list/$productId?edited=false");
            exit;
        }
    }

    public function handleDeletePriceList($priceListId)
    {
        $modelGenerico = new ModelGenerico();
        $productsModel = new Property();

        $priceList = $modelGenerico->getItemById8161($priceListId, "products_priceList");
        $product = $productsModel->getProductsById($priceList->id_product);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        try {
            DeleteFile::DeleteFile(
                [$priceListId],
                "products_priceList",
                ["priceList/products/$priceList->id_product/$priceList->filename.$priceList->extension"]
            );

            header('location:' . URL . $this->route . "/price-list/$priceList->id_product?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/price-list/$priceList->id_product?edited=false");
            exit;
        }
    }

    public function site($productId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/site.js");

        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");
        $productImgs = $productsModel->getProductsImagesActiveSite($productId);

        $permission = (Secure::access_secretary() || Secure::creator($product->created_by)) && $product->availability;

        $attrInputs = $permission ? "" : "disabled";
        $attrInputsRequired = $permission ? "required" : "disabled";
        $textLabelRequired = $permission ? "*" : "";

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/site.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitSite($productId)
    {
        Secure::check_post_method($this->route . "/site/$productId?error=error");

        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);

        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary() || !$product->availability, $this->route . '/site/' . $productId, "authorization=false");

        $arrPost = array(
            "site_name" => $_POST["name"],
            'slugify' => Util::slugify($_POST['name']),
            'url' => Util::slugify($_POST['name'] . "-" . trim($product->cod)),
            "site_name_complement" => $_POST["site_name_complement"],
            "site_complement" => Util::slugify($_POST["complement"]),
            "site_value" => Util::unMaskMoney($_POST["value"]),
            "site_description" => $_POST["description"],
            "site_schedule" => $_POST["schedule"],
        );

        if (isset($_POST["status"])) {
            $arrPost["site_status"] = $_POST["status"];
            $arrPost["site_contrast"] = $_POST["contrast"];
        }

        try {
            (new GerenciaPost)->update8191($arrPost, $this->table, "id", $productId, false);

            header('location:' . URL . $this->route . "/site/$productId?edited=true");
            exit;
        } catch (PDOException $error) {
            header('location:' . URL . $this->route . "/site/$productId?edited=false");
            exit;
        }
    }

    public function report($productId)
    {
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        Secure::productsBranches($product->branches, $this->route . "?edited=false");
        Secure::redirectFunction(!(Secure::access_secretary() || Secure::creator($product->created_by)), $this->route, 'authorization=false');
        $permission = Secure::access_secretary() || Secure::creator($product->created_by);

        $_GET["id_product"] = $productId;

        $siteViewCount = $productsModel->getCountAndFilterViewsSite($_GET);
        $attendanceCountProperties = $productsModel->getAttendanceCountWithProperties($_GET);

        $_GET['status'] = true;
        $_GET['id_branch'] = $product->id_branch;
        $_GET['id_status_not_in'] = [10, 11];

        $displayedProperties = (new DisplayedProperties())->getAttendancesByProperty($productId, $_GET);

        if (!empty($displayedProperties)) {
            if (!isset($_GET['id_attendance'])) $_GET['id_attendance'] = "";

            foreach ($displayedProperties as $property) {
                $_GET['id_attendance'] = empty($_GET['id_attendance']) ? $property->id : $_GET['id_attendance'] . ", " . $property->id;
            }
        }

        $attendances = (new Attendance())->getAndFilterAllAttendance(0, $_GET, 0);

        $users = (new User())->getAndFilterAllUsers(["status" => true, "order" => " up.access ASC, u.name ASC"], 0)->data;

        /**Percorrento em todos os atendimentos */
        $attendancesWithInterest = array_filter($attendances, function ($attendance) use ($product) {
            $interestFilters = (new Attendance())->getFiltersInterestAttedanceId($attendance->id);
            if (!empty($interestFilters)) {
                foreach ($interestFilters as $interest) {
                    $interest->json = json_decode($interest->json);

                    switch ($interest->id_attendance_filter_type) {
                        case '1':
                            /**Características */
                            foreach ($interest->json as $resourceOfInterest) {
                                if (!($this->model)->checkResourceOnProperty($product->id, $resourceOfInterest->resource, $resourceOfInterest->value, 'GREATER_EQUAL')) {
                                    return false;
                                }
                            }
                            break;
                        case '2':
                            /**Categoria */
                            if (!in_array($product->id_property_category, $interest->json)) {
                                return false;
                            }
                            break;
                        case '3':
                            /**Localização */
                            if (!empty($interest->json->id_city) ? !in_array($product->id_city, $interest->json->id_city) : !in_array($product->id_city, $interest->json)) {
                                return false;
                            }
                            break;
                        case '4':
                            /**Preço */
                            if (isset($interest->json->start_price) && isset($interest->json->end_price)) {
                                if (!($interest->json->start_price <= $product->value && $interest->json->end_price >= $product->value)) {
                                    return false;
                                }
                            } else if (isset($interest->json->start_price)) {
                                if (!($interest->json->start_price <= $product->value)) {
                                    return false;
                                }
                            } else if (isset($interest->json->end_price)) {
                                if (!($interest->json->end_price >= $product->value)) {
                                    return false;
                                }
                            }
                            break;
                        case '5':
                            /**Tipo Imóvel */
                            if (!in_array($product->id_residential_type, $interest->json)) {
                                return false;
                            }
                            break;
                    }
                }
                return true;
            }
            return false;
        });

        $countAttendanceWithInterest = count($attendancesWithInterest);

        /**Menu */
        $immovables = $productsModel->getAllProductOwnershipFeatureByidProduct($productId);

        // Limita a exibição da aba Vendas.
        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $sales = (new Sales)->getWithFiltersAllItems($arrayFilters);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/report.php';
        require APP . 'view/_templates/footer.php';
    }

    public function sales($productId)
    {
        $product = (new Property)->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        $users = (new User)->getAndFilterAllUsers(0, '', 0)->data;

        $arrayFilters = [
            (object)['columns' => ['id_product' => (object)['comparison' => 'EQUAL', 'value' => $productId]]]
        ];

        if (!empty($_GET['created_by'])) {
            $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_GET['created_by']]]];
        }

        if (!Secure::access_superAdm()) {
            $arrayFilters[] = (object)['columns' => ['id_branch' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->branch->current->id]]];

            if (!Secure::access_secretary()) {
                $arrayFilters[] = (object)['columns' => ['created_by' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }

            if (!Secure::seller_manager()) {
                $arrayFilters[] = (object)['columns' => ['sales_manager' => (object)['comparison' => 'EQUAL', 'value' => $_SESSION['RR']->user->id]]];
            }
        }

        if (!empty($_GET['name'])) {
            $arrayFilters[] = (object)['table' => 'customer', 'columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $_GET['name']]]];
        }

        $rows = 20;
        $page = Pagination::getPage();

        $sales = (new Sales)->getWithFiltersAllItems(
            $arrayFilters,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'customer', 'columns' => ['name']]
            ],
            ['limit' => $rows, 'page' => $page]
        );

        $pagination = (new Pagination())->pages($sales->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($sales->count, $pagination, $rows);

        array_map(function ($item) {
            $item->permission = !Secure::access_superAdm() || !Secure::access_seller() ? false : true;

            $item->action = [];
            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => 'fas fa-eye',
                'href' => $item->permission ? URL . "sales/edit-item/{$item->id}" : "#",
                'title' => 'Editar',
                'size' => 'sm',
                'color' => 'primary',
                'attr' => (!$item->permission ? ['disabled' => true] : ['target' => '_blank'])
            ]);

            $item->sale_date = Date::date($item->sale_date);
            $item->price = Util::maskMoney($item->sale_value);
        }, $sales->data);

        $table = (object) [
            'config' => (object) [
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true,
            ],
            'thead' => [
                (object)[
                    'style' => 'width: 60px',
                    'class' => 'text-center',
                    'text' => 'Cód.',
                    'column' => (object)['type' => 'text', 'link' => 'id'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data Venda',
                    'column' => (object)['type' => 'text', 'link' => 'sale_date'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Cliente',
                    'column' => (object)['type' => 'text', 'link' => 'customer_name'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Valor Venda',
                    'column' => (object)['type' => 'text', 'link' => 'price'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action'],
                ],
            ],
            'data' => $sales->data
        ];

        /**Menu */
        $immovables = (new Property)->getAllProductOwnershipFeatureByidProduct($productId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/menu.php';
        require APP . 'view/' . $this->dir . '/sales.php';
        require APP . 'view/_templates/footer.php';
    }

    public function disableItem($productId, $page)
    {
        $modelGenerico =  new ModelGenerico();
        $gerenciaPost = new GerenciaPost();
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $arrPost = array('site_status' => 0, 'site_contrast' => 0);
        $gerenciaPost->update8191($arrPost, $this->table, 'id', $productId, false);

        $modelGenerico->disableItem($productId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function enableItem($productId, $page)
    {
        $modelGenerico =  new ModelGenerico();
        $productsModel = new Property();

        $product = $productsModel->getProductsById($productId);
        Secure::redirectFunction(!Secure::creator($product->created_by) && !Secure::access_secretary(), $this->route, "authorization=false");

        $modelGenerico->enableItem($productId, $this->table);

        header('location: ' . URL . $this->route . '?disabled=true&page=' . $page);
        exit;
    }

    public function urlGenerator()
    {
        $properties = (new Property())->getAndFilterAllProducts(0, [], 0);

        foreach ($properties as $property) {
            (new GerenciaPost())->update8191(['url' => Util::slugify(trim($property->site_name) . "-" . trim($property->cod)), 'cod' => trim($property->cod), 'name' => trim($property->name), 'site_name' => trim($property->site_name)], $this->table, 'id', $property->id, false);
        }

        header('location: ' . URL . $this->route . '?edited=true');
        exit;
    }

    public function inactivatingNullSiteStatus($token = '')
    {
        if ($token === $this->token) {
            $properties = $this->model->getWithFiltersAllItems([(object)['columns' => ['site_status' => (object)['value' => null]]]]);
            $updtd = [];

            foreach ($properties->data as $property) {
                $response = $this->model->update(['site_status' => '0'], 'id', $property->id);
                $response->error === true ? $updtd[]['error'] = (object)['id' => $property->id, 'name' => $property->name] : $updtd[]['success'] = (object)['id' => $property->id, 'name' => $property->name];
            }

            exit;
        }
    }

    /**
     * @deprecated delete after using
     */
    public function scriptForIdUserOwners($token = '')
    {
        if ($token !== $this->token) return;

        $properties = (new Property)->getWithFiltersAllItems()->data;

        foreach ($properties as $property) {
            (new Property)->update(['id_user_owner' => $property->created_by], 'id', $property->id);
        }

        redirect($this->route);
    }

    public function interestedUsers($productId): object
    {
        $product = (new Property)->getProductsById($productId);
        $product->branches = array_map(function ($branch) {
            return $branch->id_branch;
        }, (new ModelGenerico)->getItemByGenericField($productId, 'property_branch', 'id_property'));

        $filtersDisplayedProperties = [
            'status' => true,
            'id_branch' => $product->id_branch,
            'id_status_not_in' => [10, 11],
        ];

        $displayedProperties = (new DisplayedProperties())->getAttendancesByProperty($productId, $filtersDisplayedProperties);

        $attendances = (new Attendance())->getWithFiltersAllItems([
            (object)['columns' => [
                'id' => ['comparison' => 'NOT_IN', 'value' => !empty($displayedProperties) ? array_column($displayedProperties, 'id') : ''],
                'status' => ['comparison' => 'EQUAL', 'value' => true],
                'id_branch' => ['comparison' => 'EQUAL', 'value' => $product->id_branch],
                'id_status' => ['comparison' => 'NOT_IN', 'value' => [10, 11]],
            ]]
        ])->data;

        // Walk through all appointments.
        $attendancesWithInterest = array_filter($attendances, function ($attendance) use ($product) {
            $interestFilters = (new Attendance())->getFiltersInterestAttedanceId($attendance->id);
            if (!empty($interestFilters)) {
                foreach ($interestFilters as $interest) {
                    $interest->json = json_decode($interest->json);

                    switch ($interest->id_attendance_filter_type) {
                        case '1':
                            // Characteristics.
                            foreach ($interest->json as $resourceOfInterest) {
                                if (!($this->model)->checkResourceOnProperty($product->id, $resourceOfInterest->resource, $resourceOfInterest->value, 'GREATER_EQUAL')) {
                                    return false;
                                }
                            }
                            break;
                        case '2':
                            // Category.
                            if (!in_array($product->id_property_category, $interest->json)) {
                                return false;
                            }
                            break;
                        case '3':
                            // Location.
                            if (!in_array($product->id_city, $interest->json)) {
                                return false;
                            }
                            break;
                        case '4':
                            // Price.
                            if (isset($interest->json->start_price) && isset($interest->json->end_price)) {
                                if (!($interest->json->start_price <= $product->value && $interest->json->end_price >= $product->value)) {
                                    return false;
                                }
                            } else if (isset($interest->json->start_price)) {
                                if (!($interest->json->start_price <= $product->value)) {
                                    return false;
                                }
                            } else if (isset($interest->json->end_price)) {
                                if (!($interest->json->end_price >= $product->value)) {
                                    return false;
                                }
                            }
                            break;
                        case '5':
                            // Property Type.
                            if (!in_array($product->id_residential_type, $interest->json)) {
                                return false;
                            }
                            break;
                    }
                }
                return true;
            }
            return false;
        });

        $countAttendanceWithInterest = count($attendancesWithInterest);

        return (object)['data' => $attendancesWithInterest, 'count' => $countAttendanceWithInterest];
    }
}
