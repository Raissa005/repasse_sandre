<?php

namespace RR\model;

use PDOException;
use RR\core\Model;
use RR\libs\Util;

class PropertyCategory extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'property_category';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAndFilterAllPropertyCategory($filters, $options = []): object
    {

        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pc.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(pc.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    pc.id, pc.name, pc.status, pc.site_filter
                FROM property_category pc
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " pc.id ASC";

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

    public function getAllPropertyCategory()
    {
        $sql = "SELECT
                    pc.id, pc.name, pc.status, pc.site_filter
                FROM property_category pc
                WHERE pc.status = 1
                ORDER BY pc.name ASC";

        $query = $this->db->prepare($sql);
        $parameters = array();
        $query->execute();

        return $query->fetchAll();
    }

    public function getPropertyCategoryById($id)
    {
        $sql = "SELECT
                    pc.id, pc.name, pc.status, pc.site_filter
                FROM property_category pc
                WHERE pc.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function handleSubmitAddCategory($post)
    {
        $post['prefix'] = strtoupper($post['prefix']);
        $arrPost = array('name' => $post['name'], 'prefix' => $post['prefix'], 'slugify' => Util::slugify($post['name']),'created_by' => $_SESSION['RR']->user->id, 'status' => 1);

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
    
    public function addAliases($itemId)
    {
        $propertyCategory = $this->getItemWithFilters([(object)['columns' => ['id' => (object)['value' => $itemId]]]]);

        $found = false;
        if (!empty($propertyCategory->alias)) {
            $aliases = explode(', ', $propertyCategory->alias);
            foreach ($aliases as $item) {
                $item == $_POST['alias'] ? $found = true : $found = false;
                if ($found) break;
            }
            if (!$found) {
                $concat = $propertyCategory->alias . ", {$_POST['alias']}";
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
