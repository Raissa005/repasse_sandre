<?php

namespace RR\controller\project;

use RR\libs\Secure;
use RR\libs\Util;
use RR\model\ReportDre;

class ReportDreController extends FrontController
{
	public $route;
	public $dir;
	private $model;
    private $table;

	public function __construct()
	{
        $this->route = 'report-dre';
        $this->dir = 'report-dre';
		$this->model = new ReportDre();
        $this->table = '';
		parent::__construct($this->route);
	}

	public function index()
	{
		$this->addStyle(URL . "css/" . CSSVERSION . "/reportDre.css");
		$this->addScript(URL . "js/" . JSVERSION . "/reportDre.js");
		$reportDre = new ReportDre();

		if (!isset($_GET['status'])) {
			$_GET['status'] = true;
		}

		if (!isset($_GET['select_ano'])) {
			$_GET['select_ano'] = date('Y');
		}

		$years = $reportDre->getYears();

		$objects = $reportDre->getAll($_GET);

		$mesesAno = array(
			1 => 'Janeiro',
			'Fevereiro',
			'Março',
			'Abril',
			'Maio',
			'Junho',
			'Julho',
			'Agosto',
			'Setembro',
			'Outubro',
			'Novembro',
			'Dezembro'
		);

		require APP . 'view/_templates/header.php';
		require APP . 'view/' . $this->dir . '/index.php';
		require APP . 'view/_templates/footer.php';
	}
}
