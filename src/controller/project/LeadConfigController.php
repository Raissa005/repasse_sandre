<?php

namespace RR\controller\project;

use RR\model\ModelGenerico;
use RR\model\LeadConfig;
use RR\libs\Secure;
use RR\libs\Util;
use RR\model\User;

use function RR\Controller\redirect;

class LeadConfigController extends FrontController
{
    public $route;
    public $dir;
    private $model;
    private $table;

    public function __construct()
    {
        $this->route = 'lead-config';
        $this->dir = 'lead-config';
        $this->model = new LeadConfig();
        $this->table = 'lead_config';
        parent::__construct($this->route);

        /**
         * Módulo Lead pausado a pedido do usuário (2026-09-02): a tabela
         * `lead_config` não existe neste banco, então qualquer ação aqui já
         * quebraria com erro fatal de SQL. Ver mesmo guard em LeadController.
         */
        Secure::redirectFunction(true, 'home', 'error=lead_disabled');

        $this->addScript(URL . "js/" . JSVERSION . "/lead.js");
    }

    public function index()
    {
        parent::addStyle(URL . "plugins/" . PLUGINSVERSION . "/fullcalendar/lib/main.min.css");
        parent::addScript(URL . "plugins/" . PLUGINSVERSION . "/fullcalendar/lib/main.min.js");
        parent::addScript(URL . "plugins/" . PLUGINSVERSION . "/fullcalendar/lib/locales-all.min.js");

        parent::addStyle(URL . "css/" . CSSVERSION . "/{$this->dir}/style.css");

        parent::addScript(URL . "js/" . JSVERSION . "/{$this->dir}/leadCalendar.js");

        $this->addScript(URL . "js/" . JSVERSION . "/leadConfig.js");
        $modelGenerico = new ModelGenerico();
        $leadConfig = new leadConfig();

        $items = $leadConfig->getItemById8161(1);

        $sellers = (new User)->getWithFiltersAllItems([(object)['columns' => ['id_profile' => ['comparison' => 'EQUAL', 'value' => 4]]]])->data;

        require APP . 'view/_templates/header.php';
        require APP . 'view/' . $this->dir . '/index.php';
        require APP . 'view/_templates/footer.php';
    }

    public function handleSubmitLeadConfig()
    {
        Secure::check_post_method($this->route . "?error=error");

        $response = $this->model->handleFormAdd($_POST);

        redirect($this->route);
    }
}
