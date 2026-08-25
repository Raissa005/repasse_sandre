<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

use function RR\Controller\redirect;

class PropertyType extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'property_type';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND prt.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(prt.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    prt.*
                FROM property_type prt
                WHERE TRUE $filtersQuery
                ORDER BY  ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " prt.name ASC";

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

    public function getItemById8161($id)
    {
        $sql = "SELECT
                    prt.*
                FROM property_type prt
                WHERE prt.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }


    public function getAllPropertyTypeResourceByIdItem($propertyTypeId)
    {
        $sql = "SELECT
                    ptr.*,
                    pt.name as property_type_name,
                    ir.name as immovable_resource_name
                FROM property_type_resources ptr
                LEFT JOIN immovable_resource ir ON ir.id = ptr.id_immovable_resource
                LEFT JOIN  property_type pt ON ptr.id_property_type = pt.id
                WHERE pt.id = :id
                AND ir.status = 1
                ORDER BY ir.name ASC ";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $propertyTypeId);
        $query->execute($parameters);

        return $query->fetchAll();
    }
    
    public function getAllImmovableResoursesName()
    {
        $sql = "SELECT
                    *                
                FROM immovable_resource
                WHERE status = 1
                ORDER BY name ASC ";

        $query = $this->db->prepare($sql);
        $parameters = array();
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function submitAddForm($post)
    {
        $post['prefix'] = strtoupper($post['prefix']);
        $arrPost = array('name' => $post['name'], 'prefix' => $post['prefix'], 'slugify' => Util::slugify($post['name']),'created_by' => $_SESSION['RR']->user->id);

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrPost);

            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if(ENVIRONMENT == "developmente"){
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ops! Erro ao adicionar esse Item. Consulte os administradores'];
        }
    }

    public function submitEditForm($itemId, $post)
    {
        $propertyTypeResourcesModel = new PropertyTypeResources();
        $propertyOwnershipFeatureModel = new PropertyOwnershipFeature();

        $arrPost = [
            'name' => $post['name'],
            'slugify' => Util::slugify($post['name']),
            'status' => $post['status'],
            'site_filter' => $post['site_filter'],
            'prefix' => strtoupper($post['prefix']),
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        if ($post['status'] == 0) {
            $arrPost['site_filter'] = 0;
        }

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrPost, 'id', $itemId);
            
            if (!empty($post['immovableResource'])) {
                $existentResources = $this->getAllPropertyTypeResourceByIdItem($itemId);
                $itens = Util::compareArray($existentResources, 'id_immovable_resource', $post['immovableResource']);

                if (!empty($itens['add'])) {
                    foreach ($itens['add'] as $add) {
                        $arrayPost = ['id_property_type' => $itemId, 'id_immovable_resource' => $add];
                        (new PropertyTypeResources())->insert($arrayPost);
                    }
                }

                if (!empty($itens['exc'])) {
                    foreach ($itens['exc'] as $exc) {
                        $propertyTypeResourcesModel->delete($exc->id);
                        $propertyOwnershipFeatureModel->delete(['id_property_type_resources' => $exc->id]);
                    }
                }
                
            } else {
                
                $propertyTypeResources = $propertyTypeResourcesModel->getWithFiltersAllItems([(object)['columns' => ['id_property_type' => (object)['comparison' => '=', 'value' => $itemId]]]])->data;
                foreach ($propertyTypeResources as $resources) {
                    $propertyOwnershipFeatureModel->delete(['id_property_type_resources' => $resources->id]);
                }
                $propertyTypeResources->delete(['id_property_type' => $itemId]);
            }
            
            $this->db->commit();
            return $response;
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if(ENVIRONMENT == "development"){
                echo $error->getMessage();
                exit;
            }
            return (object)['error' => true, 'message' => 'Ops! Erro ao editar esse Item. Consulte os administradores'];
        }
    }

    public function addAliases($itemId)
    {
        $propertyType = $this->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $itemId]]]]);

        $found = false;
        if (!empty($propertyType->alias)) {
            $aliases = explode(', ', $propertyType->alias);
            foreach ($aliases as $item) {
                $item == $_POST['alias'] ? $found = true : $found = false;
                if ($found) break;
            }
            if (!$found) {
                $concat = $propertyType->alias . ", {$_POST['alias']}";
                $arrPost['alias'] = $concat;
            }
        } else
            $arrPost['alias'] = $_POST['alias'];
            
        try {
            $this->db->beginTransaction();
            $response = $this->update($arrPost, 'id', $itemId);
            if ($response->error) throw new PDOException($response->message);

            $this->db->commit();
            return $response;

        } catch (PDOException $error) {
            $this->db->rollBack();
            return (object)['error' => true, 'message' => 'Erro ao inserir parcela.', 'details' => (object)['code' => $error->getCode(), 'message' => $error->getMessage()]];
        }
    }
}
