<?php

namespace RR\model;

use RR\core\Model;

class Mail extends Model
{
    private $table;
    
    function __construct()
    {
        $this->table = '';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getMailInformation()
    {
        $sql = "SELECT 
                    ce.nome AS `name`, ce.smtp AS host, ce.porta AS port, ce.email AS username, ce.senha2 AS `password`,
                    ce.seguranca AS `security`
                FROM configuracao_email ce";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetch();
    }
}
