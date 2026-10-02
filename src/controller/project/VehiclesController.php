<?php

namespace RR\controller\project;

use RR\libs\Util;
use RR\model\User;
use RR\libs\Secure;
use RR\libs\Toast;
use RR\libs\FileUploader;
use PDOException;
use RR\model\States;
use RR\model\Cities;
use RR\model\Customer;
use RR\model\Vehicles;
use RR\libs\Pagination;
use RR\model\VehicleDoors;
use RR\model\VehicleFuels;
use RR\model\VehicleTypes;
use RR\model\VehicleCosts;
use RR\model\VehicleBrands;
use RR\model\VehicleColors;
use RR\model\VehicleModels;
use RR\model\VehicleImages;
use RR\model\SystemSettings;
use RR\model\VehiclePurchases;
use RR\model\VehicleCategories;
use RR\model\TypesNegotiations;
use RR\model\VehicleAttachments;
use RR\model\VehicleObservations;

use function RR\Controller\redirect;

class VehiclesController extends FrontController
{
    public $dir;
    public $route;

    private $model;

    public function __construct()
    {
        $this->dir = 'vehicles';
        $this->route = 'vehicles';

        $this->model = new Vehicles;

        parent::__construct($this->route);
        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/vehicles.js");
    }

    private function navTabs(int $itemId)
    {
        $navTabs = [
            (object)['text' => 'Editar', 'route' => URL . "{$this->route}/editItem/$itemId", 'class' => ($_GET['pg1'] == 'editItem' ? 'active' : '')],
            (object)['text' => 'Anexos', 'route' => URL . "{$this->route}/attachments/$itemId", 'class' => ($_GET['pg1'] == 'attachments' ? 'active' : '')],
            /**Aba "Fotos" desativada a pedido do usuário (2026-09-02) — código/rota mantidos, só tirada da navegação. */
            (object)['text' => 'Observações', 'route' => URL . "{$this->route}/observations/$itemId", 'class' => ($_GET['pg1'] == 'observations' ? 'active' : '')]
        ];

        if (Secure::access_admin()) {
            array_push($navTabs, (object)['text' => 'Compra', 'route' => URL . "{$this->route}/purchaseVehicles/$itemId", 'class' => ($_GET['pg1'] == 'purchaseVehicles' ? 'active' : '')]);
            array_push($navTabs, (object)['text' => 'Custos', 'route' => URL . "{$this->route}/vehicleCosts/$itemId", 'class' => ($_GET['pg1'] == 'vehicleCosts' ? 'active' : '')]);
        }

        return $navTabs;
    }

    public function index()
    {
        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículos',
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Adicionar',
                    'href' => URL . "$this->route/addItem",
                ]
            ]
        ];

        $rows = 20;
        $page = Pagination::getPage();

        if (!isset($_GET['status'])) {
            $_GET['status'] = 1;
        }

        $filters = [(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => $_GET['status']]]]];

        if (!empty($_GET['name']) && isset($_GET['name'])) {
            $_GET['name'] = preg_replace('/\s+/', '', $_GET['name']);

            $busca = '%' . $_GET['name'] . '%';
            $buscaPlaca = '%' . substr($_GET['name'], 0, 3) . '-' . substr($_GET['name'], 3) . '%';

            array_push($filters, (object)[
                'where' => "
                    AND(
                            ucase(this->table.name) LIKE ucase(:busca_1)
                        OR
                            ucase(this->table.plate) LIKE ucase(:busca_2) OR ucase(this->table.plate) LIKE ucase(:busca_placa)
                        OR
                            ucase(vehicle_brands.name) LIKE ucase(:busca_3)
                        OR
                            ucase(vehicle_models.name) LIKE ucase(:busca_4)
                        OR
                            ucase(vehicle_colors.name) LIKE ucase(:busca_5)
                        OR
                            ucase(this->table.year_manufacture) LIKE ucase(:busca_6)
                        OR
                            ucase(this->table.id) LIKE ucase(:busca_7)
                    )",
                'parameters' => [
                    ':busca_1' => $busca,
                    ':busca_2' => $busca,
                    ':busca_placa' => $buscaPlaca,
                    ':busca_3' => $busca,
                    ':busca_4' => $busca,
                    ':busca_5' => $busca,
                    ':busca_6' => $busca,
                    ':busca_7' => $busca
                ]
                ]
            );
        }

        if (isset($_GET['data_de']) && !empty($_GET['data_de']) && isset($_GET['data_ate']) && !empty($_GET['data_ate'])) {
            array_push($filters,
                (object)[
                    'table' => 'vehicle_purchases',
                    'columns' => [
                        'purchase_date' => (object)[
                            'comparison' => 'BETWEEN',
                            'value1' => $_GET['data_de'],
                            'value2' => $_GET['data_ate']
                        ]
                    ]
                ]
            );
        }


        $response = $this->model->getWithFiltersAllItems(
            $filters,
            [
                (object)['columns' => ['*']],
                (object)['table' => 'vehicle_brands', 'columns' => ['name' => ['vehicle_brands_name']]],
                (object)['table' => 'vehicle_models', 'columns' => ['name' => ['vehicle_models_name']]],
                (object)['table' => 'vehicle_colors', 'columns' => ['name' => ['vehicle_colors_name']]],
                (object)['table' => 'vehicle_purchases', 'columns' => ['purchase_date' => ['purchase_date']]]
            ],
            [
                'limit' => $rows,
                'page' => $page,
                'orderBy' => 'vehicle_purchases.purchase_date DESC'
            ]
        );

        $pagination = (new Pagination())->pages($response->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($response->count, $pagination, $rows);

        array_map(function ($item) {
            $item->action = [];
            array_push($item->action, (object)[
                'id' => $item->id,
                'icon' => 'fas fa-pencil-alt',
                'href' => URL . "{$this->route}/editItem/{$item->id}",
                'title' => 'Editar',
                'size' => 'sm',
                'color' => 'primary',
            ], (object)[
                'id' => $item->id,
                'icon' => ($item->status ? 'fa fa-times' : 'fa fa-check'),
                'title' => ($item->status ? 'Inativar' : 'Ativar'),
                'size' => 'sm',
                'color' => ($item->status ? 'danger' : 'success'),
                'class' => ($item->status ? 'btn-disable-item' : 'btn-enable-item'),
                'attr' => [
                    'sendTo' => $this->route . ($item->status ? '/disableItem/' : '/enableItem/')
                ]
            ]);

            $item->status = (object)['value' => ($item->status ? 'Ativo' : 'inativo'), 'color' => ($item->status ? 'success' : 'danger')];
        }, $response->data);

        foreach($response->data as $row)
        {
            if(isset($row->purchase_date))
            {
                $row->purchase_date = date('d/m/Y', strtotime($row->purchase_date));
            }
        }

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
                    'class' => 'text-left',
                    'text' => 'Nome',
                    'column' => (object)['type' => 'text', 'link' => 'name'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Marca',
                    'column' => (object)['type' => 'text', 'link' => 'vehicle_brands_name'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Cor',
                    'column' => (object)['type' => 'text', 'link' => 'vehicle_colors_name'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Ano Fab.',
                    'column' => (object)['type' => 'text', 'link' => 'year_manufacture'],
                ],
                (object)[
                    'class' => 'text-left',
                    'text' => 'Ano Mod.',
                    'column' => (object)['type' => 'text', 'link' => 'year_model'],
                ],
                (object)[
                    'class' => 'text-left text-uppercase',
                    'text' => 'Placa',
                    'column' => (object)['type' => 'text', 'link' => 'plate'],
                ],
                (object)[
                    'class' => 'text-center',
                    'text' => 'Data Compra',
                    'column' => (object)['type' => 'date', 'link' => 'purchase_date'],
                ],
                (object)[
                    'style' => 'width: 180px',
                    'class' => 'text-center',
                    'text' => 'Ações',
                    'column' => (object)['type' => 'button', 'link' => 'action'],
                ],
            ],
            'data' => $response->data
        ];

        require APP . "view/_templates/header.php";
        require APP . "view/{$this->dir}/index.php";
        require APP . "view/_templates/footer.php";
    }

    public function addItem()
    {
        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículos',
            'caption' => 'Adicionar',
        ];

        $vehicleDoors = (new VehicleDoors)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleTypes = (new VehicleTypes)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleFuels = (new VehicleFuels)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleBrands = (new VehicleBrands)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleModels = (new VehicleModels)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleColors = (new VehicleColors)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleCategories = (new VehicleCategories)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/add.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddItem()
    {
        Secure::check_post_method($this->route . "/addItem");

        $arrPost = [
            'name' => $_POST['name'],
            'id_brand' => $_POST['brands'],
            'id_model' => $_POST['models'],
            'id_category' => $_POST['categories'],
            'id_type' => $_POST['types'],
            'id_door' => $_POST['doors'],
            'id_color' => $_POST['colors'],
            'id_fuel' => $_POST['fuels'],
            'year_manufacture' => $_POST['yearManufacture'] ?? '',
            'year_model' => $_POST['yearModel'] ?? '',
            'chassi' => $_POST['chassi'] ?? '',
            'factory_warranty' => $_POST['factoryWarranty'] ?? '',
            'plate' => strtoupper($_POST['plate']) ?? '',
            'renavam' => $_POST['renavam'] ?? '',
            'zero_mileage' => $_POST['zeroMileage'] ?? '',
            'mileage' => $_POST['mileage'] ?? '',
            'status' => $_POST['status'] ?? '',
            'vehicle_sales_value' => Util::unmaskMoney($_POST['vehicleSalesValue']) ?? '',
            'ipva' => $_POST['ipva'] ?? '',
        ];

        $response = $this->model->insert($arrPost);

        Toast::checkResponse($response->error, $response->message);

        redirect(!$response->error ? "{$this->route}/editItem/$response->lastId" : "{$this->route}/addItem");
    }

    public function editItem($itemId)
    {
        $item = $this->model->getItemById($itemId);

        if (!empty($item->plate)) {
            $vehicle = "$item->name - $item->plate";
        } else {
            $vehicle = "$item->name";
        }

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículo',
            'caption' => $vehicle,
        ];

        array_map(function ($i) {
            $i->vehicle_sales_value = !empty($i->vehicle_sales_value) ? Util::maskMoney($i->vehicle_sales_value) : Util::maskMoney(0);
        }, [$item]);

        $vehicleDoors = (new VehicleDoors)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleTypes = (new VehicleTypes)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleFuels = (new VehicleFuels)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleBrands = (new VehicleBrands)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleModels = (new VehicleModels)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleColors = (new VehicleColors)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $vehicleCategories = (new VehicleCategories)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;

        $navTabs = Self::navTabs($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/edit.php';
        require APP . 'view/' . $this->dir . '/modals.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitEditItem($itemId)
    {
        Secure::check_post_method($this->route . "/editItem/$itemId");

        $arrPost = [
            'name' => $_POST['name'],
            'id_brand' => $_POST['brands'],
            'id_model' => $_POST['models'],
            'id_category' => $_POST['categories'],
            'id_type' => $_POST['types'],
            'id_door' => $_POST['doors'],
            'id_color' => $_POST['colors'],
            'id_fuel' => $_POST['fuels'],
            'year_manufacture' => $_POST['yearManufacture'] ?? '',
            'year_model' => $_POST['yearModel'] ?? '',
            'chassi' => $_POST['chassi'] ?? '',
            'factory_warranty' => $_POST['factoryWarranty'] ?? '',
            'plate' => strtoupper($_POST['plate']) ?? '',
            'renavam' => $_POST['renavam'] ?? '',
            'zero_mileage' => $_POST['zeroMileage'] ?? '',
            'mileage' => $_POST['mileage'] ?? '',
            'status' => $_POST['status'] ?? '',
            'vehicle_sales_value' => Util::unmaskMoney($_POST['vehicleSalesValue']) ?? '',
            'ipva' => $_POST['ipva'] ?? ''
        ];

        $response = $this->model->update($arrPost, "id", $itemId);

        Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/editItem/$itemId");
    }

    public function attachments($itemId)
    {
        $item = (new Vehicles)->getItemById($itemId);

        if (!empty($item->plate)) {
            $vehicle = "$item->name - $item->plate";
        } else {
            $vehicle = "$item->name";
        }

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículo',
            'caption' => $vehicle,
        ];

        $vehicleAttachments = (new VehicleAttachments)->getWithFiltersAllItems(
        [
            (object)[
                'columns' => [
                    'id_vehicle' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => $itemId
                    ]
                ]
            ]
        ])->data;

        $navTabs = Self::navTabs($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/attachments.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddAttachments($itemId)
    {
        Secure::check_post_method($this->route . "/attachments/$itemId");

        // sobra do N1: campo vazio chega como name = [''] e gravava anexo sem arquivo
        if (!FileUploader::hasSelectedFile($_FILES['attachments'] ?? null)) {
            Toast::warningToast('Nenhum arquivo foi escolhido. Selecione o arquivo e depois clique em "Adicionar".');
            redirect($this->route . "/attachments/$itemId");
        }

        if ($sizeError = FileUploader::sizeLimitError($_FILES['attachments'] ?? null, FileUploader::MAX_SIZE_ATTACHMENT)) {
            Toast::warningToast($sizeError);
            redirect($this->route . "/attachments/$itemId");
        }

        if ($typeError = FileUploader::typeError($_FILES['attachments'] ?? null, FileUploader::ALLOWED_ATTACHMENT)) {
            Toast::warningToast($typeError);
            redirect($this->route . "/attachments/$itemId");
        }

        // N11: grava cada arquivo enviado (antes só o [0]), como os anexos dos outros módulos
        $files = $_FILES['attachments'];
        $hasError = false;
        $response = null;
        foreach ((array) $files['name'] as $i => $fileName) {
            if ($fileName === '' || ($files['error'][$i] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) continue;

            $response = (new VehicleAttachments)->insertAttachments([
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'fileName' => $fileName,
                'tmp_name' => $files['tmp_name'][$i]
            ], $itemId);

            if ($response->error) $hasError = true;
        }

        ($hasError || !$response) ? Toast::errorToast('Não foi possível enviar um ou mais arquivos') : Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/attachments/$itemId");
    }

    public function handleDeleteAttachment($itemId, $attachmentId)
    {
        // N13: a lixeira só aparece para admin
        Secure::access_admin(true);

        $response = (new VehicleAttachments)->deleteFile($itemId, $attachmentId);

        Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/attachments/$itemId");
    }

    public function purchaseVehicles($itemId)
    {
        // M2: aba Compra só para admin, igual à navegação
        Secure::access_admin(true);
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");

        $item = (new Vehicles)->getItemById($itemId);

        if (!empty($item->plate)) {
            $vehicle = "$item->name - $item->plate";
        } else {
            $vehicle = "$item->name";
        }

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículo',
            'caption' => $vehicle,
        ];

        $purchaseVehicle = (new VehiclePurchases)->getItemWithFilters(
            [
                (object)['columns' => [
                    'id_vehicle' => (object)[
                        'comparison' => 'EQUAL',
                        'value' => $itemId
                    ]
                ]]
            ]
        );

        $typesNegotiations = (new TypesNegotiations)->getWithFiltersAllItems()->data;
        $customers = (new Customer)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $purchasingBrokers = (new User)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;

        $states = (new States)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['comparison' => 'EQUAL', 'value' => true]]]])->data;
        $cities = (new Cities)->getWithFiltersAllItems([(object)['comlumns' => ['status' => (object)['comaprison' => 'EQUAL', 'value' => true]]]])->data;

        $navTabs = Self::navTabs($itemId);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/purchase.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddPurchase($itemId)
    {
        Secure::access_admin(true);

        $commission = str_replace(['R$', ' '], '', $_GET['vehicleCommission']);
        $commission = str_replace('.', '',  $commission);
        $commission = str_replace(',', '.', $commission);

        $arrPost = [
            'id_vehicle' => $itemId,
            'id_city' => $_GET['id_city'],
            'uf_state' => $_GET['uf_state'],
            'purchase_date' => $_GET['purchaseDate'],
            'id_former_owner' => $_GET['formerOwner'],
            'id_purchase_broker' => $_GET['purchaseBroker'],
            'id_type_negotiation' => $_GET['typeNegotiation'],
            'purchase_value' => Util::unmaskMoney($_GET['purchaseValue']),
            'fipe_value' => !empty($_GET['fipeValue']) ? Util::unmaskMoney($_GET['fipeValue']) : '',
            'settlement_value' => !empty($_GET['settlementValue']) ? Util::unmaskMoney($_GET['settlementValue']) : '',
            'value_commission' => $commission
        ];

        $purchaseVehicle = (new VehiclePurchases)->getItemWithFilters([(object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]]);

        if (empty($purchaseVehicle)) {
            $response = (new VehiclePurchases)->insert($arrPost);
        } else {
            $response = (new VehiclePurchases)->update($arrPost, 'id_vehicle', $itemId);
        }

        Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/purchaseVehicles/$itemId");
    }

    public function photos($itemId)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/order/orderList.js");
        $this->addScript(URL . "js/" . JSVERSION . "/vehicles/photos.js");

        $item = (new Vehicles)->getItemById($itemId);

        if (!empty($item->plate)) {
            $vehicle = "$item->name - $item->plate";
        } else {
            $vehicle = "$item->name";
        }

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículo',
            'caption' => $vehicle,
        ];

        $navTabs = Self::navTabs($itemId);

        $vehicleImages = (new VehicleImages)->getWithFiltersAllItems(
            [
                (object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [],
            ['orderBy' => 'this->table.item_order']
        );

        $waterMark = (new SystemSettings)->getItemWithFilters(
            [
                (object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => 1]]]
            ],
            [
                (object)['columns' => ['water_mark_cont' => ['cont'], 'water_mark_ext' => ['ext'], 'water_mark_capa' => ['capa'], 'water_mark_horizontal' => ['horizontal'], 'water_mark_vertical' => ['vertical'], 'water_mark_opacity' => ['opcity'], 'water_mark_required']]
            ]
        );

        array_map(function ($img) use ($itemId) {
            $imgLg = "vehicle/$itemId/images/$img->filename-lg.$img->extension";
            $sizeLg = getimagesize($imgLg);
            $img->sizes = "<br>" . $sizeLg[0] . " x " . $sizeLg[1];
        }, $vehicleImages->data);

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/modals.php";
        require APP . "view/{$this->dir}/photos.php";
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddImages($itemId)
    {
        Secure::check_post_method($this->route . "/photos/$itemId");

        if (empty($_FILES)) {
            Toast::genericError();
            redirect($this->route . "/photos/$itemId");
        }

        if ($sizeError = FileUploader::sizeLimitError($_FILES['photos'] ?? null, FileUploader::MAX_SIZE_VEHICLE_PHOTO)) {
            Toast::warningToast($sizeError);
            redirect($this->route . "/photos/$itemId");
        }

        if ($typeError = FileUploader::typeError($_FILES['photos'] ?? null, FileUploader::ALLOWED_IMAGE)) {
            Toast::warningToast($typeError);
            redirect($this->route . "/photos/$itemId");
        }

        if (!empty($_FILES['photos']['tmp_name'][0])) {
            for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
                $file = [
                    'fileName' => $_FILES['photos']['name'][$i],
                    'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                ];

                $response = (new VehicleImages)->insertImages($file, $itemId);
            }
        } else if (!empty($_POST['descriptionImage'])) {
            $images = $_POST['descriptionImage'];

            foreach ($images as $id => $image) {
                $response = (new VehicleImages)->update(["description" => $image], "id", $id);
            }
        }

        Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/photos/$itemId");
    }

    public function handleSubmitDeleteImage($imageId)
    {
        $response = (new VehicleImages)->deleteImage($imageId);

        Toast::checkResponse($response->result->error, $response->result->message);

        redirect("{$this->route}/photos/$response->itemId");
    }

    public function handleSubmitDeleteAllImages($itemId)
    {
        $vehicleImages = (new VehicleImages)->getWithFiltersAllItems([(object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]])->data;

        foreach ($vehicleImages as $image) {
            $response = (new VehicleImages)->deleteImage($image->id);
        }

        Toast::checkResponse(
            $response->result->error,
            $response->result->error === true ? 'Erro ao excluir os itens' : 'Itens excluidos com successo'
        );

        redirect("{$this->route}/photos/$itemId");
    }

    public function observations($itemId)
    {
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/ckeditor.js");
        $this->addScript(URL . "plugins/" . PLUGINSVERSION . "/ckeditor/config.js");

        $item = (new Vehicles)->getItemById($itemId);

        if (!empty($item->plate)) {
            $vehicle = "$item->name - $item->plate";
        } else {
            $vehicle = "$item->name";
        }

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículo',
            'caption' => $vehicle,
        ];

        $navTabs = Self::navTabs($itemId);

        $vehicleObservations = (new VehicleObservations)->getWithFiltersAllItems(
            [
                (object)[
                    'columns' => [
                        'status' => (object)['comparison' => 'EQUAL', 'value' => true],
                        'id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $itemId]
                    ]
                ]
            ]
        );

        array_map(function ($item) {
            if (!empty($item->updated_by)) {
                $item->user_name = (new User)->getItemWithFilters([(object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $item->updated_by]]]])->name;
            } else {
                $item->user_name = (new User)->getItemWithFilters([(object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $item->created_by]]]])->name;
            }
        }, $vehicleObservations->data);

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/modals.php";
        require APP . "view/{$this->dir}/observations.php";
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitAddObservations($itemId)
    {
        Secure::check_post_method($this->route . "/observations/$itemId");

        $arrPost = [
            'status' => true,
            'id_vehicle' => $itemId,
            'observation' => $_POST['observation'],
            'created_by' => $_SESSION['RR']->user->id
        ];

        $response = (new VehicleObservations)->insert($arrPost);

        Toast::checkResponse($response->error, $response->message);

        redirect("{$this->route}/observations/$itemId");
    }

    public function vehicleCosts($itemId)
    {
        // M2: aba Custos só para admin, igual à navegação
        Secure::access_admin(true);
        $item = (new Vehicles)->getItemById($itemId);

        if (!empty($item->plate)) {
            $vehicle = "$item->name - $item->plate";
        } else {
            $vehicle = "$item->name";
        }

        $contentHeader = (object)[
            'route' => URL . $this->route,
            'title' => 'Veículo',
            'caption' => $vehicle,
        ];

        $navTabs = Self::navTabs($itemId);

        $customers = (new Customer)->getWithFiltersAllItems()->data;

        $vehiclePurchase = (new VehiclePurchases)->getItemWithFilters(
            [
                (object)['columns' => ['id_vehicle' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]
            ],
            [
                (object)['columns' => ['*']],
                (object)['table' => 'users', 'columns' => ['name' => ['users_name']]],
                (object)['table' => 'customer', 'columns' => ['name' => ['customerName']]]
            ]
        );

        $vehicleCosts = (new VehicleCosts)->getCostsGroupByCustomer($itemId);

        $totalCost = 0;
        array_map(function ($cost) use (&$totalCost) {
            $totalCost += $cost->total;
        }, $vehicleCosts->data);

        require APP . 'view/_templates/header.php';
        require APP . "view/{$this->dir}/modals.php";
        require APP . "view/{$this->dir}/costs.php";
        require APP . 'view/_templates/footer.php';
    }

    public function disableItemTimeline($itemId)
    {
        $vehicle = (new VehicleObservations)->getItemWithFilters([(object)['columns' => ['id' => (object)['comparison' => 'EQUAL', 'value' => $itemId]]]]);

        (new VehicleObservations)->disableItem($itemId);

        redirect("{$this->route}/observations/$vehicle->id_vehicle");
        exit;
    }

    public function disableItem($itemId, $page)
    {
        try {
            $success = $this->model->disableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemDisabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }

    public function enableItem($itemId, $page)
    {
        try {
            $success = $this->model->enableItem($itemId);
        } catch (PDOException $error) {
            $success = false;
        }

        $success ? Toast::itemEnabled() : Toast::genericError();

        header('location: ' . URL . $this->route . '?page=' . $page);
        exit;
    }
}
