<?php

namespace RR\controller\project;

use Sabberworm\CSS\Value\Value;
use RR\libs\BoxAlert;
use RR\libs\DwvApi;
use RR\libs\Pagination;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\Branch;
use RR\model\Contract;
use RR\model\Customer;
use RR\model\CustomerType;
use RR\model\GerenciaPost;
use RR\model\ImmovableResource;
use RR\model\Integrations;
use RR\model\MaritalStatus;
use RR\model\ModelGenerico;
use RR\model\Professions;
use RR\model\Property;
use RR\model\PropertyCategory;
use RR\model\PropertyIntegration;
use RR\model\PropertyType;

class IntegrationDwvController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public $alert;

    public function __construct()
    {
        $this->dir = 'integration-dwv';
        $this->route = 'integration-dwv';
        $this->table = 'integrations';
        $this->model = new Integrations();

        parent::__construct($this->route);

        $this->alert = (new BoxAlert());
    }

    public function index()
    {
        $token = $this->model->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => 2], 'status' => (object)['value' => 1]]]]);
        if(empty($token->token)){
            $_SESSION['RR']->toast = (object)[
                'icon' => 'error',
                'title' => 'Token não encontrado ou inativo',
            ];
            header('location:' . URL . 'integration-config/editItem/2');
        }

        $contentHeader = (object)[
            'title' => "Integrações",
            'caption' => 'Listagem',
            'buttons' => [
                (object)[
                    'color' => 'info',
                    'text' => 'Integrar imóveis',
                    'href' => URL . "$this->route/addFullPropertiesIntegration",
                ],
                (object)[
                    'color' => 'success',
                    'text' => 'Download Manual',
                    'href' =>URL . "img/manualDWV.pdf",
                    'attr' => ['download' => "Manual DWV"]
                ],
            ]
        ];

        $rows = 10;
        $page = Pagination::getPage();
        
        $integrationsProperties = (new Property())->getAndFilterAllPropertiesIntegration($rows, $_GET, $page); 
        
        $intPropsIds = [];
        foreach($integrationsProperties->data as $item){
            $propertyIntegration = (new PropertyIntegration())->getItemWithFilters([(object)['columns' => ['id_property_integration' => (object)['value' => $item->id_property_integration]], 'id_integration' => (object)['value' => 2]]]);
            $intPropsIds[] = $propertyIntegration->id_property_integration;
        }

        $i = 0;
        foreach($integrationsProperties->data as $item){
            $itemApi = (new DwvApi())->listPropertyApiByCodIntegration($intPropsIds[$i++])->data->last_updated_at;
            
            if(date('Y-m-d',strtotime($item->updated_at ?? $item->created_at)) < date('Y-m-d',strtotime($itemApi)))
            (new GerenciaPost())->update8191(['synced_integration' => 0], 'products', 'id', $item->id);
        }
        
        $pagination = (new Pagination())->pages($integrationsProperties->count, $rows);
        $showItems = (new Pagination())->listItemsOnPage($integrationsProperties->count, $pagination, $rows);

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function syncDateProperty($id){
        $upd = (new DwvApi())->updatePropertyApi($id);

        header('location:' . URL . $this->route);
    }
    
    public function syncAll()
    {
        foreach($_POST['sync'] as $id)
        $upd = (new DwvApi())->updatePropertyApi($id);
    
        header('location:' . URL . $this->route);
    }

    public function addFullPropertiesIntegration()
    {
        if (!isset($_GET['status'])) {
            $_GET['status'] = true;
        }
        $_SESSION['RR']->propertiesApiIds = null;
        
        $contentHeader = (object)[
            'title' => "Integrações",
            'caption' => 'Adicionar',
            'buttons' => []
        ];

        $propertiesApi = (new DwvApi())->listPropertiesApi();
        $listPropertyIntegration = (new PropertyIntegration())->getWithFiltersAllItems()->data;

        $array = json_decode(json_encode($propertiesApi), true);

        $i = 0;
        if(!empty($listPropertyIntegration)){
            foreach($array['data'] as $item){
                if($item['deleted'] == 1)
                    unset($array['data'][$i]);
                foreach($listPropertyIntegration as $itemSys){
                    if($item['id'] == $itemSys->id_property_integration){  
                        unset($array['data'][$i]);
                    }
                }
                $i++;
            }
        }

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/addPropertiesIntegration.php';
        require APP . 'view/_templates/footer.php';
    }

    public function importPropertiesApi()
    {
        if(empty($_SESSION['RR']->propertiesApiIds))
            $_SESSION['RR']->propertiesApiIds = $_POST;

            $item = reset($_SESSION['RR']->propertiesApiIds);
            $listItem = (new DwvApi())->listPropertyApiByCodIntegration($item)->data;
            $array = json_decode(json_encode($listItem), true);
            $insert = (new DwvApi())->handleAddPropertiesApi($array); // insert property
            $propertyIntegration = (new PropertyIntegration())->getItemWithFilters([(object)['columns' => ['id_property_integration' => (object)['value' => $item]], 'id_integration' => (object)['value' => 2]]]);
            $customerPhone = (new Customer())->getWithFiltersAllItems([(object)['columns' => ['phone' => (object)[ 'value' => $listItem->construction_company->business_contacts[0]->phone_number]]]], [(object)['columns' => ['id']]] ); // verifica count // depois verificar cellphone com info whatsapp
            
            if(!empty($listItem->construction_company->additionals_contacts[0]->whatsapp))
                $customerCellphone = (new Customer())->getWithFiltersAllItems([(object)['columns' => ['cellphone' => (object)[ 'value' => $listItem->construction_company->additionals_contacts[0]->whatsapp]]]], [(object)['columns' => ['id']]] ); // verifica count // depois verificar cellphone com info whatsapp
            if($customerPhone->count != 0){
                (new Property())->update((array)['id_owner' => $customerPhone->data[0]->id], 'id', $propertyIntegration->id_property);   
                
                header('location:' . URL . $this->route . "/type");
            }
            elseif(isset($customerCellphone) && $customerCellphone->count != 0){
                (new Property())->update((array)['id_owner' => $$listItem->construction_company->additionals_contacts[0]->whatsapp], 'id', $propertyIntegration->id_property);

                header('location:' . URL . $this->route . "/type");
            }
            elseif(empty($listItem->construction_company->additionals_contacts[0]->construtora_id))
                header('location:' . URL . $this->route . '/customer/' . $listItem->construction_company->business_contacts[0]->phone_number . '/2'); // integration by phone
            else
                header('location:' . URL . $this->route . '/customer/' . $listItem->construction_company->additionals_contacts[0]->construtora_id . '/1');
    }

    public function customer($idCustomerIntegration, $opt)
    {
        $this->addScript(URL . "js/" . JSVERSION . "/state.js");
        $this->addScript(URL . "js/" . JSVERSION . "/cnpj.js");
        $this->addScript(URL . "js/" . JSVERSION . "/" . $this->dir . "/customer.js");

        $modelGenerico = new ModelGenerico();
        $branchModel = new Branch();
        $professionsModel = new Professions();
        $maritalStatusModel = new MaritalStatus();
        $customerTypeModel = new CustomerType();

        $branch = $branchModel->getItemById8161($_SESSION['RR']->branch->current->id);
        $city = $modelGenerico->getItemById8161($branch->id_city, "cities");
        $state = (new Contract)->getStatesByUF($city->uf);

        $persons = $modelGenerico->getAllItens("person_type");
        $countries = $modelGenerico->getAllItens("countries");
        $states = $modelGenerico->getAllItens("states");
        $cities = $branchModel->getCitiesByState($city->uf);
        $branches = $branchModel->getAllBranch();
        $professions = $professionsModel->getAllProfessions();
        $maritalStatus = $maritalStatusModel->getAllMaritalStatus();
        $customerTypes = $customerTypeModel->getAllCustomerType();
        $requiredField = $modelGenerico->getItemById8161(1, 'customer_required_field');

        $id = reset($_SESSION['RR']->propertiesApiIds);
        $listItem = (new DwvApi())->listPropertyApiByCodIntegration($id)->data;

        $contentHeader = (object)[
            'title' => "{$listItem->title}",
            'caption' => "{$listItem->unit->title}",
            'buttons' => []
        ];


        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/customer.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleAddCustomerIntegration()
    {            
        $response = (new Customer())->submitFormAdd($_POST);
        $_SESSION['RR']->toast = (object)[
            'icon' => ($response->error === true ? 'error' : 'success'),
            'title' => $response->message,
        ];
        
        $integrationPost = array(
            'id_customer' => $response->lastId,
            'id_customer_integration' => $_POST['cod_integration'],
            'id_integration' => 2, // id dwv
        );
        
        switch($_GET['pg2']){
            case 1:
                $integrationPost['id_customer_integration'] = $_POST['cod_integration'];
                break;
            case 2:
                $integrationPost['phone_customer_integration'] = $_POST['cod_integration'];
                break;
        }
            
        (new GerenciaPost())->insert7181($integrationPost, 'customer_integration');
        $propertyIntegration = (new PropertyIntegration())->getItemWithFilters([(object)['columns' => ['id_property_integration' => (object)['value' => reset($_SESSION['RR']->propertiesApiIds)]], 'id_integration' => (object)['value' => 2]]]);
        (new Property())->update((array)['id_owner' => $response->lastId], 'id', $propertyIntegration->id_property);
        header('location:' . URL . $this->route . "/type");
    }


    public function type()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/integration/type.js");

        $id = reset($_SESSION['RR']->propertiesApiIds);

        $listItem = (new DwvApi())->listPropertyApiByCodIntegration($id)->data;
        $typesSys = (new PropertyType())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)[ 'value' => 1]]]],[],[(object)['orderBy' => 'property_type.name ASC']])->data;

            foreach($typesSys as $itemSys)
                if($listItem->unit->floor_plan->category->tag == $itemSys->alias)
                    header('location:' . URL . $this->route . "/category");

        $contentHeader = (object)[
            'title' => "{$listItem->title}",
            'caption' => "{$listItem->unit->title}",
            'buttons' => []
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/type.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleType()
    {
        if($_POST['typeSelect'] == 0){ 
            $response = (new PropertyType())->submitAddForm($_POST);
            (new PropertyType())->addAliases($response->lastId);
            $arrPost = ['id_residential_type' => $response->lastId];
        } else {
            (new PropertyType())->addAliases($_POST['typeSelect']);
            $arrPost = ['id_residential_type' => $_POST['typeSelect']];
        }

        $propertyIntegration = (new PropertyIntegration())->getItemWithFilters([(object)['columns' => ['id_property_integration' => (object)['value' => reset($_SESSION['RR']->propertiesApiIds)]], 'id_integration' => (object)['value' => 2]]]);            
        (new GerenciaPost())->update8191($arrPost, 'products','id', $propertyIntegration->id_property);
    
        header('location:' . URL . $this->route . "/category");
    }

    public function category() 
    {
        $this->addScript(URL . "js/" . JSVERSION . "/integration/category.js");

        $id = reset($_SESSION['RR']->propertiesApiIds);

        $listItem = (new DwvApi())->listPropertyApiByCodIntegration($id)->data;
        $categoriesSys = (new PropertyCategory())->getWithFiltersAllItems([(object)['columns' => ['status' => (object)[ 'value' => 1]]]],[],[(object)['orderBy' => 'property_category.name ASC']])->data;
        
        if(empty($listItem->construction_stage_raw)){
            $listItem->construction_stage = 'Lançamento';
            $listItem->construction_stage_raw = 'new';
        }

        foreach($categoriesSys as $itemSys)
        if($listItem->construction_stage_raw == $itemSys->alias)
        header('location:' . URL . $this->route . "/features");

        $contentHeader = (object)[
            'title' => "{$listItem->title}",
            'caption' => "{$listItem->unit->title}",
            'buttons' => []
        ];


        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/category.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleCategory()
    {
        if($_POST['categorySelect'] == 0){ 
            $response = (new PropertyCategory())->handleSubmitAddCategory($_POST);
            (new PropertyCategory())->addAliases($response->lastId);
            $arrPost = ['id_property_category' => $response->lastId];
        } else {
            (new PropertyCategory())->addAliases($_POST['categorySelect']);
            $arrPost = ['id_property_category' => $_POST['categorySelect']];
        }
        $propertyIntegration = (new PropertyIntegration())->getItemWithFilters([(object)['columns' => ['id_property_integration' => (object)['value' => reset($_SESSION['RR']->propertiesApiIds)]], 'id_integration' => (object)['value' => 2]]]);            
        (new GerenciaPost())->update8191($arrPost, 'products','id', $propertyIntegration->id_property);

    
        header('location:' . URL . $this->route . "/features");
    }

    public function features()
    {
        $this->addScript(URL . "js/" . JSVERSION . "/integration/features.js");

        $id = reset($_SESSION['RR']->propertiesApiIds);

        $listItem = (new DwvApi())->listPropertyApiByCodIntegration($id)->data;
        $arrFeaturesUnsetTags = ['bathroom', 'parking_spaces', 'suites', 'dorms'];
        $arrFeaturesUnsetTitles = ['Banheiros', 'Garagens', 'Suite', 'Quarto'];
        $featuresSys = (new ImmovableResource())->getWithFiltersAllItems([],[],[(object)['orderBy' => 'immovable_resource.name ASC']])->data;
        
        foreach ($listItem->building->features as $item) // get only the features where type are the same with the property
            if ($listItem->unit->floor_plan->category->title == $item->type)
            $features = $item;
        
        $featuresTags = array_merge($features->tags, $arrFeaturesUnsetTags);
        $featuresTitles = array_merge($features->titles, $arrFeaturesUnsetTitles);
        $features->tags = $featuresTags;
        $features->titles = $featuresTitles;
        
        $i = 0; $j = 0;
        foreach($features->tags as $item){
            $allFeatures[$i] = array('tags' => $features->tags[$i], 'titles' => $features->titles[$i]); 
            $findFeature = (new ImmovableResource())->getItemWithFilters([(object)['columns' => ['alias' => (object)['comparison' => 'LIKE', 'value' => $item]]]]);
            if (empty($findFeature)){
                $newFeatures[$j] = array('tags' => $features->tags[$j], 'titles' => $features->titles[$j]);
            } else {
                $featuresLeft[$i] = $findFeature->id;
                $i++;
            }
            $j++;
        }

        if(!empty($featuresLeft))
            $linkFeatures = (new DwvApi())->linkFeaturesApi($featuresLeft, $_SESSION['RR']->propertiesApiIds);
        
        if(empty($newFeatures)){
            $removeItem = array_shift($_SESSION['RR']->propertiesApiIds);
            if(!empty($_SESSION['RR']->propertiesApiIds))
                header('location:' . URL . $this->route . "/importPropertiesApi");
            else
                header('location:' . URL . $this->route . "/index");
        }

        $contentHeader = (object)[
            'title' => "{$listItem->title}",
            'caption' => "{$listItem->unit->title}",
            'buttons' => []
        ];

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/features.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleFeatures()
    {
        $allFeaturesSysIds = [];
        $_POST['data_type'] = 4;
        for($i = 0; $i < count($_POST['name']); $i++){
            if($_POST['featuresSelect'][$i] == 0){ 
                $response = (new ImmovableResource())->handleSubmitAddItem((array)['name' => $_POST['name'][$i], 'alias' => $_POST['alias'][$i], 'data_type' => $_POST['data_type']]);
                (new ImmovableResource())->addAliases($response->lastId, (array)['alias' => $_POST['alias'][$i]]);
                $allFeaturesSysIds[$i] = $response->lastId;
            } else {
                (new ImmovableResource())->addAliases($_POST['featuresSelect'][$i], (array)['alias' => $_POST['alias'][$i]]);
                $allFeaturesSysIds[$i] = $_POST['featuresSelect'][$i];
            }
        }

        if(!empty($allFeaturesSysIds))
            $linkFeatures = (new DwvApi())->linkFeaturesApi($allFeaturesSysIds, $_SESSION['RR']->propertiesApiIds);

        $removeItem = array_shift($_SESSION['RR']->propertiesApiIds);
        if(empty($_SESSION['RR']->propertiesApiIds))
            header('location:' . URL . $this->route . "/index");
        else
            header('location:' . URL . $this->route . "/importPropertiesApi");

    }
}