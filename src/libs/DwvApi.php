<?php

namespace RR\libs;

use PDOException;
use RR\core\Model;
use RR\model\Branch;
use RR\model\Cities;
use RR\model\GerenciaPost;
use RR\model\ImmovableResource;
use RR\model\Integrations;
use RR\model\Property;
use RR\model\PropertyBranches;
use RR\model\PropertyCategory;
use RR\model\PropertyIntegration;
use RR\model\PropertyOwnershipFeature;
use RR\model\PropertyType;
use RR\model\PropertyTypeResources;

class DwvApi
{

    public $token;

    public function __construct()
    {
        $this->token = (new Integrations())->getItemById(2)->token;
    }

    public function listPropertiesApi()
    {
        $ch = curl_init('https://dwvapp.com.br/integration/properties/');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);

        return $response;
    }

    public function listPropertyApiByCodIntegration($id)
    {
        $ch = curl_init('https://dwvapp.com.br/integration/properties/' . $id);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response);

        return $response;
    }

    public function handleAddPropertiesApi(array $data)
    {
        $id_city = (new Cities())->getItemWithFilters([(object)['columns' => ['name' => (object)['value' => mb_strtoupper($data['building']['address']['city'])]]]])->id;

        $residential_types = (new PropertyType())->getWithFiltersAllItems([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $data['unit']['floor_plan']['category']['title']], 'status' => (object)['value' => 1]]]])->count;
        if ($residential_types == 1)
            $id_residential_type = (new PropertyType())->getItemWithFilters([(object)['columns' => ['name' => (object)['comparison' => 'LIKE', 'value' => $data['unit']['floor_plan']['category']['title']], 'status' => (object)['value' => 1]]]])->id;
        else
            $id_residential_type = (new PropertyType())->getItemWithFilters([(object)['columns' => ['alias' => (object)['comparison' => 'LIKE', 'value' => $data['unit']['floor_plan']['category']['tag']]]]])->id;

            if(!empty($data['construction_stage_raw']))
                $id_property_category = (new PropertyCategory())->getItemWithFilters([(object)['columns' => ['alias' => (object)['comparison' => 'LIKE','value' => $data['construction_stage_raw']]]]])->id;

        $arrayPost = array(
            'id_property_category' => !empty($id_property_category) ? $id_property_category : '',
            'name' => trim(ucwords(mb_strtolower($data['title'])) . ' - ' .  $data['unit']['title']),
            'id_residential_type' => $id_residential_type,
            'id_city' => $id_city,
            'uf_state' => $data['building']['address']['state'],
            'neighborhood' => ucwords(mb_strtolower($data['building']['address']['neighborhood'])),
            'address' => ucwords(mb_strtolower($data['building']['address']['street_name'])),
            'number' => $data['building']['address']['street_number'],
            'total_area' => (!empty($data['unit']['total_area']) && $data['unit']['total_area'] != '0.00') ? $data['unit']['total_area'] : $data['unit']['util_area'],
            'complement' => $data['building']['address']['complement'],
            'lat' => !empty($data['building']['address']['latitude']) ? $data['building']['address']['latitude'] : '-27.244972',
            'lng' => !empty($data['building']['address']['longitude']) ? $data['building']['address']['longitude'] : '-48.640851',
            'value' => $data['unit']['price'],
            'installment_value' => $data['unit']['price'],
            're_registered_at' => !empty(trim($data['last_updated_at'])) ? $data['last_updated_at'] : NULL,
            'site_name' => trim($data['title']),
            'site_complement' => Util::slugify($data['building']['address']['complement']),
            'site_value' => $data['unit']['price'],
            'site_description' => $data['description'],
            'slugify' => Util::slugify(strtolower($data['title'])),
            'url' => Util::slugify(strtolower($data['title'])) . '-' . $data['unit']['id'],
            'description' => $data['description'],
            'created_at' => date("Y-m-d H:i:s"),
            'created_by' => $_SESSION['RR']->user->id,
            'id_user_owner' => $_SESSION['RR']->user->id,
            'id_branch' => $_SESSION['RR']->branch->current->id,
            'status' => ($data['status'] == 'auto_inactive' && $data['status'] == 'inactive') ? 0 : 1, 
            'synced_integration' => 1,
        );
        
        $response = (new GerenciaPost())->insert7181($arrayPost, 'products', true);

        if (empty($arrayPost['cod'])) {
            (new GerenciaPost())->update8191(['cod' => $response], 'products', "id", $response);
        }

        $integrationPost = array(
            'id_property' => $response,
            'id_property_integration' => $data['id'],
            'id_integration' => 2, // id dwv
        );

        (new GerenciaPost())->insert7181($integrationPost, 'property_integration');

        (new PropertyBranches())->insert(['id_property' => $response, 'id_branch' => $_SESSION['RR']->branch->current->id]);
    }

    public function linkFeaturesApi($features, $propSysId)
    {
        $getCodIntegrationByCodSys = (new PropertyIntegration())->getItemWithFilters([(object)['columns' => ['id_property_integration' => (object)['value' => reset($propSysId)]], 'id_integration' => (object)['value' => 2]]]);
        $response = (new Property())->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $getCodIntegrationByCodSys->id_property]]]]);
        
        $propertyApi = $this->listPropertyApiByCodIntegration($getCodIntegrationByCodSys->id_property_integration)->data;
        $data =  json_decode(json_encode($propertyApi), true); // convert object to array
        
        foreach($features as $item){
            $id_property_type_resources = (new PropertyTypeResources())->getItemWithFilters([(object)['columns' => ['id_property_type' => (object)['value' => $response->id_residential_type], 'id_immovable_resource' => (object)['value' => $item]]]])->id; // get the first ocurrency if exists
            if(empty($id_property_type_resources))
                $id_property_type_resources = (new PropertyTypeResources())->insert([ 'id_property_type' => $response->id_residential_type, 'id_immovable_resource' => $item])->lastId;
            
            $product_ownership_feature = (new PropertyOwnershipFeature())->insert(['id_product' => $response->id, 'id_property_type_resources' => $id_property_type_resources, 'value' => 'Sim', 'created_at' => date("Y-m-d H:i:s"), 'created_by' => $_SESSION['RR']->user->id]);
        }

            $featuresWithValues = array(
                'parking_spaces' => $data['unit']['parking_spaces'],
                'dorms' => $data['unit']['dorms'],
                'suites' => $data['unit']['suites'],
                'bathroom' => $data['unit']['bathroom'],
            );

        foreach ($featuresWithValues as $key => $item) {
            $id_immovable = (new ImmovableResource())->getItemWithFilters([(object)['columns' => ['alias' => (object)['comparison' => 'LIKE', 'value' => $key]]]]);
            $id_property_type_resources = (new PropertyTypeResources())->getItemWithFilters([(object)['columns' => ['id_property_type' => (object)['value' => $response->id_residential_type], 'id_immovable_resource' => (object)['value' => $id_immovable->id]]]])->id;
            $idProductOwnershipFeature = (new PropertyOwnershipFeature())->getItemWithFilters([(object)['columns' => ['id_product' => (object)['value' => $response->id], 'id_property_type_resources' => (object)['value' => $id_property_type_resources]]]])->id;

            $arrPostFeaturesWithValues = array(
                'value' => $item,
            );

            (new GerenciaPost())->update8191($arrPostFeaturesWithValues, 'product_ownership_feature','id', $idProductOwnershipFeature);
        }
    }

    public function updatePropertyApi($id)
    {
        $getCodIntegrationByCodSys = (new PropertyIntegration())->getItemWithFilters([(object)['columns' => ['id_property' => (object)['value' => $id]], 'id_integration' => (object)['value' => 2]]]);
        $response = $this->listPropertyApiByCodIntegration($getCodIntegrationByCodSys->id_property_integration)->data;
        $data =  json_decode(json_encode($response), true); // convert object to array
        $id_city = (new Cities())->getItemWithFilters([(object)['columns' => ['name' => (object)['value' => mb_strtoupper($data['building']['address']['city'])]]]])->id;

        $arrUpd = array(
            'name' => trim(ucwords(mb_strtolower($data['title'])) . ' - ' .  $data['unit']['title']),
            'id_city' => $id_city,
            'uf_state' => $data['building']['address']['state'],
            'neighborhood' => ucwords(mb_strtolower($data['building']['address']['neighborhood'])),
            'address' => ucwords(mb_strtolower($data['building']['address']['street_name'])),
            'number' => $data['building']['address']['street_number'],
            'total_area' => !empty($data['unit']['total_area']) ? $data['unit']['total_area'] : $data['unit']['util_area'],
            'complement' => $data['building']['address']['complement'],
            'lat' => !empty($data['building']['address']['latitude']) ? $data['building']['address']['latitude'] : '-27.244972',
            'lng' => !empty($data['building']['address']['longitude']) ? $data['building']['address']['longitude'] : '-48.640851',
            'value' => $data['unit']['price'],
            'installment_value' => $data['unit']['price'],
            're_registered_at' => !empty(trim($data['last_updated_at'])) ? $data['last_updated_at'] : NULL,
            'site_name' => trim($data['title']),
            'site_complement' => Util::slugify($data['building']['address']['complement']),
            'site_value' => $data['unit']['price'],
            'site_description' => $data['description'],
            'slugify' => Util::slugify(strtolower($data['title'])),
            'url' => Util::slugify(strtolower($data['title'])) . '-' . $data['unit']['id'],
            'description' => $data['description'],
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
            'status' => ($data['status'] == 'auto_inactive' && $data['status'] == 'inactive') ? 0 : 1,
            'synced_integration' => 1
        );

            (new GerenciaPost())->update8191($arrUpd, 'products', 'id', $id, false);
    }
}
