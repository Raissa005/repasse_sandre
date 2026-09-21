<?php

namespace RR\model;

use PDOException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use RR\core\Model;
use RR\libs\Util;

class CustomerType extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'customer_type';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function addSubmitForm($post)
    {
        $arrayPost = array(
            'name' => trim($post['name']),
            'status' => $post['status'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => $_SESSION['RR']->user->id
        );

        try {
            $this->db->beginTransaction();

            $response = $this->insert($arrayPost);

            $this->db->commit();

            return $response;
        } catch (PDOException $error) {
            $this->db->rollback();

            if (ENVIRONMENT === "development") {
                echo $error;
                die();
            }

            return (object)['error' => true, 'message' => 'Não foi possível adicionar este item. Por favor, tente novamente.'];
        }
    }

    public function editSubmitForm($id, $post)
    {
        $arrayPost = [
            'name' => trim($post['name']),
            'status' => (empty($post['status']) ? 1 : $post['status']),
            'updated_by' => $_SESSION['RR']->user->id,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            $this->db->beginTransaction();

            $response = $this->update($arrayPost, 'id', $id);

            if (!$response->error) {
                $menus = (new Menu)->getWithFiltersAllItems([
                    (object)['columns' => [
                        'id_menu_parent' => (object)['comparison' => 'EQUAL', 'value' => 24],
                        'get' => (object)['comparison' => 'NOT_EQUAL', 'value' => null]
                    ]]
                ]);

                foreach ($menus->data as $menu) {
                    $customerTypeId = explode("=", $menu->get);

                    if ($id == end($customerTypeId)) {
                        (new Menu)->update(['name' => trim($post['name'])], 'id', $menu->id);
                    }
                }

                (new FilesystemAdapter)->delete('menus_' . $_SESSION['RR']->cache->id);
            }

            $this->db->commit();

            return $response;
        } catch (PDOException $error) {
            $this->db->rollback();

            if (ENVIRONMENT === "development") {
                echo $error;
                die();
            }

            return (object)['error' => true, 'message' => 'Não foi possível editar este item. Por favor, tente novamente.'];
        }
    }

    public function getAndFilterAllCustomerType($filters, $options = [])
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND ctt.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['name']) && $filters['name'] != "") {
            $filtersQuery .= " AND ucase(ctt.name) LIKE ucase(:name)";
            $parameters[':name'] = '%' . $filters['name'] . '%';
        }

        $sql = "SELECT
                    ctt.id, ctt.name, ctt.status, ctt.disableable
                FROM customer_type ctt
                WHERE TRUE $filtersQuery
                ORDER BY ctt.id ASC";

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

    public function getAllCustomerType()
    {
        $sql = "SELECT
                    ctt.id, ctt.name, ctt.status
                FROM customer_type ctt
                WHERE ctt.status = 1
                ORDER BY ctt.name ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    /**
     * Busca o id de um tipo de cliente pelo nome, em vez de depender de um id
     * fixo no código — o id numérico não é garantido ser o mesmo entre
     * ambientes (dev/produção), já que depende da ordem/histórico de
     * migrations rodadas em cada banco.
     */
    public function getIdByName(string $name)
    {
        $sql = "SELECT id FROM customer_type WHERE name = :name AND status = 1 LIMIT 1";

        $query = $this->db->prepare($sql);
        $query->execute([':name' => $name]);
        $row = $query->fetch();

        return $row->id ?? null;
    }

    public function getCustomerTypeById($id)
    {
        $sql = "SELECT
                    ctt.id, ctt.name, ctt.status
                FROM customer_type ctt
                WHERE ctt.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAllCustomerResourcesClientByIdCustomer($id)
    {
        $sql = "SELECT
                    crc.id, ctr.id as id_customer_type_resources, ct.name as name_customer_type, ct.id as id_customer_type, cr.name as name_customer_resource, cr.id as id_customer_resource, crc.value
                FROM customer_resource_client crc
                LEFT JOIN customer_type_resources ctr ON crc.id_customer_type_resources = ctr.id
                LEFT JOIN customer_resource cr ON ctr.id_customer_resource = cr.id
                LEFT JOIN customer_type ct ON ctr.id_customer_type = ct.id
                WHERE crc.id_customer = :id
                AND cr.status = 1";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllCustomerTypeResourceIdCustomerType($arrIdCustomerType)
    {
        $sql = "SELECT
                    ctr.id as id_customer_type_resources, ctr.id_customer_type, ct.name as name_customer_type, ctr.id_customer_resource, cr.name as name_customer_resource
                FROM  customer_type_resources ctr
                LEFT JOIN customer_resource cr ON cr.id = ctr.id_customer_resource
                LEFT JOIN customer_type ct ON ct.id = ctr.id_customer_type 
                WHERE ct.id IN 
                    (";

        foreach ($arrIdCustomerType as $key => $id) {
            $sql .= $key == count($arrIdCustomerType) - 1 ? "$id)" : "$id,";
        }

        $sql .= " AND cr.status = 1
                 AND  ct.status = 1
                 GROUP BY ctr.id_customer_resource
                 ORDER BY cr.name ASC";

        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function deleteAllCustomerTypeResourcesByIdCustomerType($id)
    {
        $sql = "DELETE FROM customer_type_resources
                WHERE id_customer_type = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return true;
    }
}
