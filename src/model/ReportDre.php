<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Util;

class ReportDre extends Model
{
	private $table;

	function __construct()
	{
		$this->table = '';
		$joins = [];

		parent::__construct($this->table, $joins);
	}

	public function getAll($filtros)
	{
		$branches  = (new Branch())->getAllBranch();

		foreach ($branches as $branch) {

			$mesesDados = array();

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

			$start = 1;
			$max = 12;

			// get Centros de custos gerais
			$filtros_query = "";
			$filtros_query_2 = "";
			$parameters = array();

			if (isset($filtros['select_ano']) && $filtros['select_ano'] != "") {
				$filtros_query_2 .= " AND YEAR(btpi.due_date) = :ano_total ";
				$parameters[':ano_total'] = $filtros['select_ano'];
			} else {
				$filtros_query_2 .= " AND YEAR(btpi.due_date) = :ano_total ";
				$parameters[':ano_total'] = date('Y');
			}

			$filtros_query .= " AND btp.id_branch = :id_branch ";
			$parameters[':id_branch'] = $branch->id;

			$sql = "SELECT 
						cc.id, cc.id_father, cc.name, COALESCE(SUM(btpi.value_of_installments), 0.00) AS total_value
					FROM cost_center cc 
					LEFT JOIN bills_to_pay btp ON btp.id_cost_center = cc.id AND btp.status = 1 $filtros_query
					LEFT JOIN bills_to_pay_installments btpi ON btp.id = btpi.id_bills_to_pay AND btpi.status = 1 AND btpi.status_payment IN (1, 2) $filtros_query_2
					GROUP BY cc.id
					ORDER BY cc.id_father ASC, cc.id ASC ";

			$query = $this->db->prepare($sql);
			$query->execute($parameters);
			$return = $query->fetchAll();

			$centrosCustoGlobais = array();

			foreach ($return as $rr) {
				$centrosCustoGlobais[$rr->id] = $rr;
			}

			foreach ($centrosCustoGlobais as &$centroCustoForeachGlobal) {
				if ($centroCustoForeachGlobal->id_father != 0) {
					foreach ($centrosCustoGlobais as &$centroCustoFilhosGlobal) {
						if ($centroCustoFilhosGlobal->id == $centroCustoForeachGlobal->id_father) {
							if (isset($centroCustoFilhosGlobal->filhos)) {
								$centroCustoFilhosGlobal->filhos[$centroCustoForeachGlobal->id] = $centroCustoForeachGlobal;
							} else {
								$centroCustoFilhosGlobal->filhos = array($centroCustoForeachGlobal->id => $centroCustoForeachGlobal);
							}
						}
					}
				}
			}

			foreach ($centrosCustoGlobais as &$centrosCustoGlobaisForeach) {
				$this->somarTotalPai($centrosCustoGlobaisForeach);
			}

			$this->filterCentro($centrosCustoGlobais);

			$centrosCustoGlobais = array_filter($centrosCustoGlobais, function ($centro) {
				return $centro->id_father == 0;
			});

			$centrosCustoGlobais = $this->adicionarCentros($centrosCustoGlobais);

			for ($meses = $start; $meses <= $max; $meses++) {
				$filtros_query = "";
				$parameters = array();

				if (isset($filtros['select_ano']) && $filtros['select_ano'] != "") {
					$filtros_query .= " AND YEAR(btpi.due_date) = :ano_total ";
					$parameters[':ano_total'] = $filtros['select_ano'];
				} else {
					$filtros_query .= " AND YEAR(btpi.due_date) = :ano_total ";
					$parameters[':ano_total'] = date('Y');
				}

				$filtros_query .= " AND btp.id_branch = :id_branch ";
				$parameters[':id_branch'] = $branch->id;

				$filtros_query .= " AND MONTH(btpi.due_date) = :meses ";
				$parameters[':meses'] = $meses;

				$sql = "SELECT 
							btpi.*, SUM(btpi.value_of_installments) AS total_value
						FROM bills_to_pay_installments btpi
						INNER JOIN bills_to_pay btp ON btp.id = btpi.id_bills_to_pay
						WHERE TRUE $filtros_query
						AND btp.status = 1
						AND btpi.status = 1
						AND btpi.status_payment IN (1, 2)";

				$query = $this->db->prepare($sql);
				$query->execute($parameters);

				$mes = $query->fetch();


				if (empty($mes)) {
					$mes = (object) [];
					$mes->total_value = 0;
				}

				// Get bills to receive
				$filtros_query = "";
				$parameters = array();

				if (isset($filtros['select_ano']) && $filtros['select_ano'] != "") {
					$filtros_query .= " AND YEAR(bri.due_date) = :ano_total ";
					$parameters[':ano_total'] = $filtros['select_ano'];
				} else {
					$filtros_query .= " AND YEAR(bri.due_date) = :ano_total ";
					$parameters[':ano_total'] = date('Y');
				}

				$filtros_query .= " AND br.id_branch = :id_branch ";
				$parameters[':id_branch'] = $branch->id;

				$filtros_query .= " AND MONTH(bri.due_date) = :meses ";
				$parameters[':meses'] = $meses;

				$sql = "SELECT 
							bri.*, SUM(bri.value_installment) AS net_profit
						FROM bill_receive_installment bri
						INNER JOIN bill_receive br ON br.id = bri.id_bill_receive
						WHERE TRUE $filtros_query
						AND br.status = 1
						AND bri.status = 1
						AND bri.status_payment IN (1, 2)";

				$query = $this->db->prepare($sql);
				$query->execute($parameters);

				$return = $query->fetch();

				$mes->revenue = !empty($return) && $return->net_profit > 0 ? $return->net_profit : 0.00;

				$filtros_query = "";
				$filtros_query_2 = "";
				$parameters = array();


				if (isset($filtros['select_ano']) && $filtros['select_ano'] != "") {
					$filtros_query_2 .= " AND YEAR(btpi.due_date) = :ano_total ";
					$parameters[':ano_total'] = $filtros['select_ano'];
				} else {
					$filtros_query_2 .= " AND YEAR(btpi.due_date) = :ano_total ";
					$parameters[':ano_total'] = '2021';
				}

				$filtros_query .= " AND btp.id_branch = :id_branch ";
				$parameters[':id_branch'] = $branch->id;

				$filtros_query_2 .= " AND MONTH(btpi.due_date) = :meses ";
				$parameters[':meses'] = $meses;

				$sql = "SELECT 
								cc.id, cc.id_father, cc.name, COALESCE(SUM(btpi.value_of_installments), 0.00) AS total_value
							FROM cost_center cc 
							LEFT JOIN bills_to_pay btp ON btp.id_cost_center = cc.id AND btp.status = 1 $filtros_query
							LEFT JOIN bills_to_pay_installments btpi ON btp.id = btpi.id_bills_to_pay AND btpi.status = 1 AND btpi.status_payment IN (1, 2) $filtros_query_2
							GROUP BY cc.id
							ORDER BY cc.id_father ASC, cc.id ASC ";

				$query = $this->db->prepare($sql);
				$query->execute($parameters);
				$arrayReturn = $query->fetchAll();

				$centrosCusto = array();

				foreach ($arrayReturn as $arr) {
					if (isset($centrosCustoGlobais[$arr->id])) {
						$centrosCusto[$arr->id] = $arr;
					}
				}

				foreach ($centrosCusto as &$centroCustoForeach) {
					if ($centroCustoForeach->id_father != 0) {
						foreach ($centrosCusto as &$centroCustoFilhos) {
							if ($centroCustoFilhos->id == $centroCustoForeach->id_father) {
								if (isset($centroCustoFilhos->filhos)) {
									$centroCustoFilhos->filhos[$centroCustoForeach->id] = $centroCustoForeach;
								} else {
									$centroCustoFilhos->filhos = array($centroCustoForeach->id => $centroCustoForeach);
								}
							}
						}
					}
				}

				$centrosCusto = array_filter($centrosCusto, function ($centro) {
					return $centro->id_father == 0;
				});

				foreach ($centrosCusto as &$centroCustoForeach) {
					$this->somarTotalPai($centroCustoForeach);
				}

				$mes->centro_custo = $centrosCusto;

				$mesesDados[$mesesAno[$meses]] = $mes;
			}
			$branch->meses = $mesesDados;
		}

		return $branches;
	}

	public function getYears()
	{
		$sql = "SELECT DISTINCT YEAR(due_date) AS ano FROM bills_to_pay_installments WHERE status = 1 ORDER BY ano ASC ";

		$query = $this->db->prepare($sql);
		$query->execute();

		$return = $query->fetchAll();

		return $return;
	}

	public function filterCentro(&$centroCusto)
	{
		foreach ($centroCusto as $key => $centro) {
			if ($centro->total_value <= 0) {
				unset($centroCusto[$key]);
			} else {
				if (!empty($centro->filhos)) {
					$this->filterCentro($centro->filhos);
				}
			}
		}
		return true;
	}

	public function somarTotalPai(&$centroDeCusto)
	{
		if (isset($centroDeCusto->filhos)) {
			$centroDeCusto->total_value += array_reduce($centroDeCusto->filhos, function ($accumulator, $filho) {
				return $accumulator += $this->somarTotalPai($filho);
			}, 0);
		}

		return $centroDeCusto->total_value;
	}

	public function adicionarCentros(&$centros)
	{
		$return = array();
		foreach ($centros as $centro) {
			$return[$centro->id] = $centro->id;
			if (isset($centro->filhos) && !empty($centro->filhos)) {
				$array = $this->adicionarCentros($centro->filhos);
				if (!empty($array)) {
					foreach ($array as $key => $value) {
						$return[$key] = $key;
					}
				}
			}
		}
		return $return;
	}
}
