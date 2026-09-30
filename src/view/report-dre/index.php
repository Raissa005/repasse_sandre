<?php

use RR\libs\Util;

?>

<div class="content-wrapper">
	<section class="content container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title" style="margin-top: 7px;">Relatório de DRE</h3>
					</div>
					<div class="box-body ">
						<div class="row">
							<form action="<?= URL . $this->route ?>" method="GET" id="form-banks">
								<div class="col-md-3">
									<div class="form-group">
										<label for="year">Ano</label>
										<select class="form-control" id="select_ano" name="select_ano">
											<?php foreach ($years as $year) { ?>
												<option value="<?= $year->ano ?>" <?= isset($_GET['select_ano']) && $_GET['select_ano'] == $year->ano ? 'selected' : (!isset($_GET['select_ano']) && $year->ano == date('Y') ? 'selected' : '') ?>><?= $year->ano ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="col-md-9" style="margin-top:25px;">
									<div class="form-group pull-right">
										<button type="submit" class="btn btn-primary" name="filter"><i class="fa fa-search"></i> Pesquisar</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="box box-primary">
					<div class="box-body ">
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive">
									<table class="table table-bordered table-condensed" cellspacing="2">
										<thead class="text-center">
											<tr>
												<th style="vertical-align: middle;" class="text-center">Filial</th>
												<?php foreach ($mesesAno as $mes) { ?>
													<th class="text-center"><?= $mes ?></th>
												<?php } ?>
												<th class="text-center">Total</th>
											</tr>
										</thead>
										<tbody class="tbody-trs" cellspacing="2">
											<?php
											foreach ($objects as $object) {
												$total_value = 0.00;
												$revenue = 0.00; ?>
												<tr class="click click_branch tr_branch" id_branch="<?= $object->id ?>">
													<td class="td-icon-name-dre"><?= isset($object->meses['Janeiro']->centro_custo) ? '<i id="id_branch_' . $object->id . '" class="fa fa-chevron-down"></i>' : '' ?><?= $object->name ?></td>
													<?php foreach ($object->meses as $nomeMes =>  $mes) {
														$total_value += $mes->total_value;
														$revenue += $mes->revenue; ?>
														<td class="text-right" title="<?= $object->name . " - " . $nomeMes ?>"><?= Util::maskMoneyInt($mes->revenue - $mes->total_value) ?></td>
													<?php } ?>
													<td class="text-right" title="<?= $object->name ?> - Total"><?= Util::maskMoneyInt($revenue - $total_value) ?></td>
												</tr>
												<?php if ($revenue > 0) { ?>
													<tr style="display: none;" class="id_branch_<?= $object->id ?> tr_resume_faturamento">
														<td style="padding-left: 20px;" class="td-icon-name-dre">Faturamento</td>
														<?php foreach ($object->meses as $nomeMes => $mes) { ?>
															<td class="text-right" title="Faturamento - <?= $nomeMes ?>"><?= Util::maskMoneyInt($mes->revenue) ?></td>
														<?php } ?>
														<td class="text-right" title="Faturamento - Total"><?= Util::maskMoneyInt($revenue) ?></td>
													</tr>
												<?php } ?>
												<?php if ($total_value > 0) { ?>
													<tr style="display: none;" class="id_branch_<?= $object->id ?> click click_expenditure tr_resume_despesa" id_branch="<?= $object->id ?>">
														<td style="padding-left: 20px;" class="td-icon-name-dre">
															<i id="id_branch_<?= $object->id ?>_expenditure" class="fa fa-chevron-down"></i>
															Despesas
														</td>
														<?php foreach ($object->meses as $nomeMes => $mes) { ?>
															<td class="text-right" title="Despesas - <?= $nomeMes ?>"><?= Util::maskMoneyInt($mes->total_value) ?></td>
														<?php } ?>
														<td class="text-right" title="Despesas - Total"><?= Util::maskMoneyInt($total_value) ?></td>
													</tr>
												<?php } ?>
												<?php if (isset($object->meses['Janeiro']->centro_custo)) { ?>
													<?php
													foreach ($object->meses['Janeiro']->centro_custo as $centro) {
														$total_value = 0.00;
													?>
														<tr style="display: none;" class="id_branch_<?= $object->id ?>_expenditure click click_center" id_cost_center="<?= $centro->id ?>" id_branch_pai="<?= $object->id ?>">
															<td style="padding-left: 25px;" class="td-icon-name-dre"><?= isset($centro->filhos) && !empty($centro->filhos) ? '<i id="cost_center_' . $centro->id . '" class="fa fa-chevron-down"></i>' : '' ?><?= $centro->name ?></td>
															<?php foreach ($mesesAno as $mes) {
																$total_value += $object->meses[$mes]->centro_custo[$centro->id]->total_value;
															?>
																<td class="text-right" title="<?= $centro->name . " - " . $mes ?>"><?= Util::maskMoneyInt($object->meses[$mes]->centro_custo[$centro->id]->total_value) ?></td>
															<?php } ?>
															<td class="text-right" title="<?= $centro->name . " - Total" ?>"><?= Util::maskMoneyInt($total_value) ?></td>
														</tr>
													<?php
														if (isset($centro->filhos) && !empty($centro->filhos)) {
															echoTr($centro->filhos, array($centro->id), $mesesAno, $object);
														}
													} ?>
												<?php } ?>
											<?php } ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php

function echoTr($costCentersChild, $array, $mesesAno, $object)
{
	foreach ($costCentersChild as $child) {
		$total_value = 0.00;
		$html = "";
		$html .= '<tr style="display: none;" class="id_pai_' . $child->id_father . ' click click_center id_branch_pai_' . $object->id . ' " id_cost_center="' . $child->id . '" id_branch_pai="' . $object->id . '" > <td class="td-icon-name-dre" style="padding-left: ' . ((count($array) * 20) + 10) . 'px;">' . (isset($child->filhos) && !empty($child->filhos) ? '<i id="cost_center_' . $child->id . '" class="fa fa-chevron-down"></i>' : '') . $child->name . '</td> ';
		foreach ($mesesAno as $month) {
			$elementValue = $object->meses[$month]->centro_custo[reset($array)];
			foreach ($array as $arr) {
				if ($arr != reset($array)) {
					$elementValue = isset($elementValue->filhos[$arr]) ? $elementValue->filhos[$arr] : (object)['total_value' => 0];
				}
			}
			$elementValue = isset($elementValue->filhos[$child->id]) ? $elementValue->filhos[$child->id] : (object)['total_value' => 0];
			$total_value += isset($elementValue->total_value) ? $elementValue->total_value : 0;
			$html .= '<td class="text-right" title="' . $child->name . ' - ' . $month . '">' . Util::maskMoneyInt($elementValue->total_value) . '</td>';
		}
		$html .= '<td class="text-right" title="' . $child->name . ' - Total " >' . Util::maskMoneyInt($total_value) . '</td>';
		echo $html;
		if (isset($child->filhos) && !empty($child->filhos)) {
			// array from within the function only with different name to avoid problem of cost centers brothers being put together as parent in the array
			$daddies  = $array;
			$daddies[] = $child->id;
			echoTr($child->filhos, $daddies, $mesesAno, $object);
		}
	}
}
?>