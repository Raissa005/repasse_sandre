<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Date;
use RR\libs\Util;

class Contract extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'contracts';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function getContractById($id)
    {
        $sql = "SELECT
                    pac.*
                FROM property_authorization_contract pac
                WHERE pac.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getContractByIdSale($id, $id_type)
    {
        $sql = "SELECT
                    c.id, c.contract_text, sc.id as id_standard_contract
                FROM contracts c
                LEFT JOIN sales s ON c.id_sale = s.id
                LEFT JOIN standard_contract sc ON sc.id = c.id_standard_contract
                LEFT JOIN type_contract tc on tc.id = sc.type_contract
                WHERE s.id = :id
                AND tc.id = :id_type";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id, ":id_type" => $id_type);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAllContractByIdSale($id)
    {
        $sql = "SELECT
                    c.id, c.contract_text, sc.id as id_standard_contract, tc.id as id_type_standard
                FROM contracts c
                LEFT JOIN sales s ON c.id_sale = s.id
                LEFT JOIN standard_contract sc ON sc.id = c.id_standard_contract
                LEFT JOIN type_contract tc on tc.id = sc.type_contract
                WHERE s.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getContractCustomerByCode($code)
    {
        $sql = "SELECT
                    pac.*
                FROM property_authorization_contract pac
                WHERE pac.code = :code";

        $query = $this->db->prepare($sql);
        $parameters = array(":code" => $code);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getAndFiltersContractCustomer($filters)
    {
        $parameters = [];
        $filtersQuery = '';

        if (isset($filters['status']) && $filters['status'] != '') {
            $filtersQuery .= " AND pac.status = :status";
            $parameters[':status'] = $filters['status'];
        }

        if (isset($filters['id_customer']) && $filters['id_customer'] != '') {
            $filtersQuery .= " AND pac.id_customer = :id_customer";
            $parameters[':id_customer'] = $filters['id_customer'];
        }

        $sql = "SELECT
                    pac.*,
                    sc.name AS name_contract
                FROM property_authorization_contract pac
                LEFT JOIN standard_contract sc ON sc.id = pac.id_standard_contract
                WHERE TRUE $filtersQuery
                ORDER BY ";

        $sql .= isset($filters['order']) && $filters['order'] != "" ? " " . $filters['order'] : " pac.status DESC, pac.created_at DESC";

        $query = $this->db->prepare($sql);
        $query->execute($parameters);

        return $query->fetchAll();
    }

    public function getContractVariablesByCostumerId($id)
    {
        $sql = "SELECT
                    cust.id,
                    cust.name AS nome_cliente,
                    cust.person_registration AS cpf_cliente,
                    cust.rg AS rg_cliente,
                    cust.nationality AS nacionalidade_cliente,
                    pro.name AS profissao_cliente,
                    ms.name AS estadoCivil_cliente,
                    scust.name AS conjuge_cliente,
                    scust.address AS conjugeEndereco_cliente,
                    cnt.name AS pais_cliente,
                    cust.zip AS zip_cliente,
                    sta.name AS estado_cliente,
                    cust.uf_state AS uf_cliente,
                    cit.name AS cidade_cliente,
                    cust.cep AS cep_cliente,
                    cust.neighborhood AS bairroEndereco_cliente,
                    cust.address AS endereco_cliente,
                    cust.number_address AS numeroEndereco_cliente,
                    cust.complement AS complementoEndereco_cliente,
                    cust.cellphone AS celular_cliente,
                    cust.email AS email_cliente,
                    cust.name AS nome_juridico_empresa,
                    cust.cnpj AS cnpj_empresa,
                    cust.company_name AS nome_razao_empresa,
                    cust.fancy_name_company AS nome_fantasia_empresa,
                    pt.name AS tipoImovel_produto,
                    p.name AS nome_produto,
                    p.uf_state AS estado_produto,
                    p.uf_state AS uf_produto,
                    p.id_city AS cidade_produto,
                    p.neighborhood AS bairroEndereco_produto,
                    p.address AS endereco_produto,
                    p.allotment AS loteamento_produto,
                    p.number AS numeroEndereco_produto,
                    p.total_area AS areaTotal_produto,
                    bra.name AS nome_filial,
                    bra.cnpj AS cnpj_filial,
                    bra.address AS endereco_filial,
                    bra.number AS numeroEndereco_filial,
                    bra.complement AS complementoEndereco_filial,
                    bra.neighborhood AS bairroEndereco_filial,
                    bra.id_city AS cidade_filial,
                    bra.cep AS cep_filial,
                    bra.email AS email_filial,
                    bra.creci_legal AS creci_filial,
                    cust.status AS autorizacaoImovel_produtos,
                    bra.percentage_commission_sale AS comissao_filial,
                    p.re_registered_at AS diasRecadastro_filial,
                    pac.created_at AS data_criacao,
                    pac.name AS name_contract
                FROM customer cust
                LEFT JOIN spouse_customer scust ON scust.id = cust.id
                LEFT JOIN cities cit ON cit.id = cust.id_city
                LEFT JOIN states sta ON sta.uf = cust.uf_state
                INNER JOIN countries cnt ON cnt.id = cust.id_country
                INNER JOIN marital_status ms ON ms.id = cust.id_marital_status
                INNER JOIN professions pro ON pro.id = cust.id_profession
                INNER JOIN property_authorization_contract pac ON pac.id_customer = cust.id
                INNER JOIN customer_branches ON cust.id = customer_branches.id_customer
                INNER JOIN branch bra ON bra.id = customer_branches.id_branch
                INNER JOIN products p ON p.id_owner = cust.id
                INNER JOIN property_type pt ON pt.id = p.id_residential_type
                WHERE cust.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        $return = $query->fetch();

        $branchCity = $this->getCitiesById($return->cidade_filial);
        $return->cidade_filial = $branchCity->name;

        $return->uf_filial = $branchCity->uf;

        $branchState = $this->getStatesByUF($branchCity->uf);
        $return->estado_filial = $branchState->name;

        $productCity = $this->getCitiesById($return->cidade_produto)->name;
        $return->cidade_produto = $productCity;

        return $return;
    }

    public function  getContractVariablesBySaleId($id)
    {
        $sql = "SELECT
                    sales.id,
                    cust.name AS nome_cliente,
                    cust.person_registration AS cpf_cliente,
                    cust.rg AS rg_cliente,
                    cust.nationality AS nacionalidade_cliente,
                    pro.name AS profissao_cliente,
                    ms.name AS estadoCivil_cliente,
                    scust.name AS conjuge_cliente,
                    scust.address AS conjugeEndereco_cliente,
                    cnt.name AS pais_cliente,
                    cust.zip AS zip_cliente,
                    sta.name AS estado_cliente,
                    cust.uf_state AS uf_cliente,
                    cit.name AS cidade_cliente,
                    cust.cep AS cep_cliente,
                    cust.neighborhood AS bairroEndereco_cliente,
                    cust.address AS endereco_cliente,
                    cust.number_address AS numeroEndereco_cliente,
                    cust.complement AS complementoEndereco_cliente,
                    cust.cellphone AS celular_cliente,
                    cust.email AS email_cliente,
                    cust.company_name AS nome_juridico_empresa,
                    cust.cnpj AS cnpj_empresa,
                    cust.company_name AS nome_razao_empresa,
                    cust.fancy_name_company AS nome_fantasia_empresa,
                    pt.name AS tipoImovel_produto,
                    p.name AS nome_produto,
                    p.uf_state AS estado_produto,
                    p.uf_state AS uf_produto,
                    p.id_city AS cidade_produto,
                    p.neighborhood AS bairroEndereco_produto,
                    p.address AS endereco_produto,
                    p.allotment AS loteamento_produto,
                    p.number AS numeroEndereco_produto,
                    p.total_area AS areaTotal_produto,
                    sales.sale_date AS data_venda,
                    users.name AS nomeGerente_venda,
                    users.cpf AS cpfGerente_venda,
                    users.creci AS creciGerente_venda,
                    sales.sale_value AS valor_venda,
                    sales.number_installments AS parcelas_venda,
                    cust.status AS autorizacaoImovel_produtos,
                    bra.name AS nome_filial,
                    bra.cnpj AS cnpj_filial,
                    bra.address AS endereco_filial,
                    bra.number AS numeroEndereco_filial,
                    bra.complement AS complementoEndereco_filial,
                    bra.neighborhood AS bairroEndereco_filial,
                    bra.id_city AS cidade_filial,
                    bra.cep AS cep_filial,
                    bra.email AS email_filial,
                    bra.creci_legal AS creci_filial
                FROM sales
                INNER JOIN customer cust ON  cust.id = sales.id_customer
                INNER JOIN marital_status ms ON ms.id = cust.id_marital_status
                LEFT JOIN spouse_customer scust ON scust.id = cust.id
                LEFT JOIN cities cit ON cust.id_city = cit.id
                LEFT JOIN states sta ON sta.uf = cust.uf_state
                INNER JOIN professions pro ON  pro.id = cust.id_profession
                INNER JOIN countries cnt ON cnt.id = cust.id_country
                INNER JOIN products p ON sales.id_product = p.id
                INNER JOIN users on sales.sales_manager = users.id
                LEFT JOIN property_type pt ON pt.id = p.id_residential_type
                INNER JOIN branch bra ON bra.id = sales.id_branch
                WHERE sales.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        $return = $query->fetch();

        $branchCity = $this->getCitiesById($return->cidade_filial);
        $return->cidade_filial = $branchCity->name;

        $return->uf_filial = $branchCity->uf;

        $branchState = $this->getStatesByUF($branchCity->uf);
        $return->estado_filial = $branchState->name;

        $productCity = $this->getCitiesById($return->cidade_produto)->name;
        $return->cidade_produto = $productCity;

        return $return;
    }

    public function getInstalmentReceivePrintReceipt($id)
    {
        $sql = "SELECT 
                    bri.number_portion AS numero_parcela,
                    bri.value_installment AS valor_parcela,
                    bri.pay_day AS data_pagamento,
                    bri.description AS descricao,
                    bri.agency AS agencia_pagamento,
                    bri.number_account AS conta_pagamento,
                    bri.owner_check AS titular_cheque,
                    bri.number_check AS numero_cheque,
                    cc.name AS centro_custo,
                    fop.name AS forma_pagamento,
                    bk.name AS nome_banco,
                    cust.name AS nome_cliente,
                    cust.person_registration AS cpf_cliente,
                    cust.rg AS rg_cliente,
                    cust.nationality AS nacionalidade_cliente,
                    pro.name AS profissao_cliente,
                    ms.name AS estadoCivil_cliente,
                    scust.name AS conjuge_cliente,
                    scust.address AS conjugeEndereco_cliente,
                    cnt.name AS pais_cliente,
                    cust.zip AS zip_cliente,
                    sta.name AS estado_cliente,
                    cust.uf_state AS uf_cliente,
                    cit.name AS cidade_cliente,
                    cust.cep AS cep_cliente,
                    cust.neighborhood AS bairroEndereco_cliente,
                    cust.address AS endereco_cliente,
                    cust.number_address AS numeroEndereco_cliente,
                    cust.complement AS complementoEndereco_cliente,
                    cust.cellphone AS celular_cliente,
                    cust.email AS email_cliente,
                    cust.company_name AS nome_juridico_empresa,
                    cust.cnpj AS cnpj_empresa,
                    cust.company_name AS nome_razao_empresa,
                    cust.fancy_name_company AS nome_fantasia_empresa,
                    cust.status AS autorizacaoImovel_produtos,
                    bra.name AS nome_filial,
                    bra.cnpj AS cnpj_filial,
                    bra.address AS endereco_filial,
                    bra.number AS numeroEndereco_filial,
                    bra.complement AS complementoEndereco_filial,
                    bra.neighborhood AS bairroEndereco_filial,
                    bra.id_city AS cidade_filial,
                    bra.cep AS cep_filial,
                    bra.email AS email_filial,
                    bra.creci_legal AS creci_filial
                FROM bill_receive_installment bri
                INNER JOIN bill_receive br ON br.id = bri.id_bill_receive
                LEFT JOIN banks bk ON bk.id = bri.id_bank
                INNER JOIN customer cust ON cust.id = br.id_customer
                INNER JOIN professions pro ON  pro.id = cust.id_profession
                INNER JOIN marital_status ms ON ms.id = cust.id_marital_status
                LEFT JOIN spouse_customer scust ON scust.id = cust.id
                LEFT JOIN cities cit ON cit.id = cust.id_city
                LEFT JOIN states sta ON sta.uf = cust.uf_state
                INNER JOIN countries cnt ON cnt.id = cust.id_country
                INNER JOIN cost_center cc ON cc.id = br.id_cost_center
                INNER JOIN form_of_payment fop ON fop.id = bri.id_form_of_payment
                INNER JOIN branch bra ON bra.id = br.id_branch
                WHERE bri.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        $return = $query->fetch();

        $branchCity = $this->getCitiesById($return->cidade_filial);
        $return->cidade_filial = $branchCity->name;
        $return->uf_filial = $branchCity->uf;

        $branchState = $this->getStatesByUF($branchCity->uf);
        $return->estado_filial = $branchState->name;

        return $return;
    }

    public function getInstalmentToPayPrintReceipt($id)
    {
        $sql = "SELECT 
                    btpi.number_portion AS numero_parcela,
                    btpi.value_of_installments AS valor_parcela,
                    btpi.pay_day AS data_pagamento,
                    btpi.description AS descricao,
                    btpi.agency AS agencia_pagamento,
                    btpi.number_account AS conta_pagamento,
                    btpi.owner_check AS titular_cheque,
                    btpi.number_check AS numero_cheque,
                    cc.name AS centro_custo,
                    fop.name AS forma_pagamento,
                    bk.name AS nome_banco,
                    cust.name AS nome_cliente,
                    cust.person_registration AS cpf_cliente,
                    cust.rg AS rg_cliente,
                    cust.nationality AS nacionalidade_cliente,
                    pro.name AS profissao_cliente,
                    ms.name AS estadoCivil_cliente,
                    scust.name AS conjuge_cliente,
                    scust.address AS conjugeEndereco_cliente,
                    cnt.name AS pais_cliente,
                    cust.zip AS zip_cliente,
                    sta.name AS estado_cliente,
                    cust.uf_state AS uf_cliente,
                    cit.name AS cidade_cliente,
                    cust.cep AS cep_cliente,
                    cust.neighborhood AS bairroEndereco_cliente,
                    cust.address AS endereco_cliente,
                    cust.number_address AS numeroEndereco_cliente,
                    cust.complement AS complementoEndereco_cliente,
                    cust.cellphone AS celular_cliente,
                    cust.email AS email_cliente,
                    cust.company_name AS nome_juridico_empresa,
                    cust.cnpj AS cnpj_empresa,
                    cust.company_name AS nome_razao_empresa,
                    cust.fancy_name_company AS nome_fantasia_empresa,
                    cust.status AS autorizacaoImovel_produtos,
                    bra.name AS nome_filial,
                    bra.cnpj AS cnpj_filial,
                    bra.address AS endereco_filial,
                    bra.number AS numeroEndereco_filial,
                    bra.complement AS complementoEndereco_filial,
                    bra.neighborhood AS bairroEndereco_filial,
                    bra.id_city AS cidade_filial,
                    bra.cep AS cep_filial,
                    bra.email AS email_filial,
                    bra.creci_legal AS creci_filial
                FROM bills_to_pay_installments btpi
                INNER JOIN bills_to_pay btp ON btp.id = btpi.id_bills_to_pay
                LEFT JOIN banks bk ON bk.id = btpi.id_bank
                INNER JOIN customer cust ON cust.id = btp.id_customer
                INNER JOIN professions pro ON  pro.id = cust.id_profession
                INNER JOIN marital_status ms ON ms.id = cust.id_marital_status
                LEFT JOIN spouse_customer scust ON scust.id = cust.id
                LEFT JOIN cities cit ON cit.id = cust.id_city
                LEFT JOIN states sta ON sta.uf = cust.uf_state
                INNER JOIN countries cnt ON cnt.id = cust.id_country
                INNER JOIN cost_center cc ON cc.id = btp.id_cost_center
                INNER JOIN form_of_payment fop ON fop.id = btpi.id_form_of_payment
                INNER JOIN branch bra ON bra.id = btp.id_branch
                WHERE btpi.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        $return = $query->fetch();

        $branchCity = $this->getCitiesById($return->cidade_filial);
        $return->cidade_filial = $branchCity->name;
        $return->uf_filial = $branchCity->uf;

        $branchState = $this->getStatesByUF($branchCity->uf);
        $return->estado_filial = $branchState->name;

        return $return;
    }

    public function getCitiesById($id)
    {
        $sql = "SELECT 
                    c.name,
                    c.uf
                FROM cities c
                WHERE c.id = :id";

        $query = $this->db->prepare($sql);
        $parameters = array(":id" => $id);
        $query->execute($parameters);

        return $query->fetch();
    }

    public function getStatesByUF($uf)
    {
        $sql = "SELECT
                    s.name,
                    s.id_country
                FROM states s
                WHERE s.uf = :uf";

        $query = $this->db->prepare($sql);
        $parameters = array(":uf" => $uf);
        $query->execute($parameters);

        return $query->fetch();
    }
}
