<?php

namespace RR\model;

use RR\core\Model;

class SettingsSite extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'configuracao';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getAllSiteSettings()
    {
        $sql = "SELECT
                    c.*
                FROM configuracao c
                ORDER BY c.id ASC";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetch();
    }

    public function getAndFilterAllSettings($rows, $filters, $page)
    {

        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['ativo']) && $filters['ativo'] != '') {
            $filtersQuery .= " AND c.ativo = :ativo";
            $parameters[':ativo'] = $filters['ativo'];
        }


        if (isset($filters['nome']) && $filters['nome'] != "") {
            $filtersQuery .= " AND ucase(c.nome) LIKE ucase(:nome)";
            $parameters[':nome'] = '%' . $filters['nome'] . '%';
        }

        $offset = ($page - 1) * $rows;

        $sql = "SELECT
                    c.*
                FROM configuracao c
                WHERE TRUE $filtersQuery
                ORDER BY c.id ASC";

        if ($rows > 0) {

            $sql .= " LIMIT $rows
                      OFFSET $offset";
        }

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getAllSettings()
    {
        $sql = "SELECT
                    *
                FROM configuracao c
                WHERE TRUE
                ORDER BY c.id ASC ";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public function getSettingById($id)
    {
        $sql = "SELECT
                    c.*
                FROM configuracao c
                WHERE c.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getSettingEmailById($id)
    {
        $sql = "SELECT
                    e.*
                FROM configuracao_email e
                WHERE e.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getSettingMetaTagsById($id)
    {
        $sql = "SELECT
                    m.*
                FROM configuracao_metatags m
                WHERE m.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getSettingScriptById($id)
    {
        $sql = "SELECT
                    c.script_header, c.script_body_top, c.script_body_bottom
                FROM configuracao c
                WHERE c.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }

    public function getSettingLayoutById($id)
    {
        $sql = "SELECT
                    c.layout_top_left, c.layout_bottom_left, c.layout_bottom_right, c.layout_desc_value
                FROM configuracao c
                WHERE c.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);

        $query->execute($parameters);

        return $query->fetch();
    }
}
