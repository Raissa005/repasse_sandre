<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class ImmovableResource extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'immovable_resource';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllItem($filters, $options = []): object
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND ir.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(ir.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        if (isset($filters['filter']) && $filters['filter'] != '') {
            $filtersQuery .= " AND ir.filter = :filter";
            $parameters[':filter'] = $filters['filter'];
        }

        $sql = "SELECT
                    ir.*
                FROM immovable_resource ir
                WHERE TRUE $filtersQuery
                ORDER BY ir.name ASC";

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
                    ir.*
                FROM immovable_resource ir
                WHERE ir.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAllPropertyTypeResourceIdItem($immovableResourceId)
    {
        $sql = "SELECT
                    *
                FROM property_type_resources ptr
                WHERE ptr.id_immovable_resource = :id
                ORDER BY ptr.id ASC";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $immovableResourceId);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function handleSubmitAddItem($data)
    {
        $arrPost = array(
            'name' => $data['name'],
            'data_type' => $data['data_type'],
            'slugify' => Util::slugify($data['name']),
            'created_by' => $_SESSION['RR']->user->id
        );

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

    public function addAliases($itemId, $data)
    {
        $propertyFeatures = $this->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $itemId]]]]);

        $found = false;
        if (!empty($propertyFeatures->alias)) {
            $aliases = explode(', ', $propertyFeatures->alias);
            foreach ($aliases as $item) {
                $item == $data['alias'] ? $found = true : $found = false;
                if ($found) break;
            }
            if (!$found) {
                $concat = $propertyFeatures->alias . ", {$data['alias']}";
                $arrPost['alias'] = $concat;
            }
        } else
            $arrPost['alias'] = $data['alias'];

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
