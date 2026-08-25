<?php

namespace RR\model;

use RR\core\Model;

class WaterMark extends Model
{
    private $table;

    function __construct()
    {
        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getItemById8161($id)
    {
        $sql = "SELECT
                    item.water_mark_cont as cont,
                    item.water_mark_ext as ext,
                    item.water_mark_capa as capa,
                    item.water_mark_horizontal as horizontal,
                    item.water_mark_vertical as vertical,
                    item.water_mark_opacity as opacity,
                    item.water_mark_required
                FROM system_config item
                WHERE item.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(':id' => $id);
        $query->execute($parameters);

        return $query->fetch();
    }
}
