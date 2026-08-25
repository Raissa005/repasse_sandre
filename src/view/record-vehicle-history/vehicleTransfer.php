<?php

use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

?>

<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <form role="form" action="<?= URL . $this->route . "/handerTransfer/$item->id" ?>" method="POST">
                        <div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Veículo Transferido</label>
                                                <select name="transferred">
                                                    <option value="0">Não</option>
                                                    <option value="1" <?= ($saleVehicle[0]->transferred == 1 && !empty($saleVehicle[0]->transferred)) ? 'selected' : '' ?>>Sim</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="due_date_transfer">Vencimento da Transferência</label>
                                                <input type="date" class="form-control" name="due_date_transfer" value="<?= isset($saleVehicle[0]->due_date_transfer) && !empty($saleVehicle[0]->due_date_transfer)
                                                ? $saleVehicle[0]->due_date_transfer
                                                : (!empty($saleVehicle[0]->sale_date) ? date('Y-m-d', strtotime('+90 days', strtotime($saleVehicle[0]->sale_date))) : '' )?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="date_transfer">Data da Transferência</label>
                                                <input type="date" class="form-control" name="date_transfer" value="<?= !empty($saleVehicle[0]->date_transfer) ? $saleVehicle[0]->date_transfer : ""?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Observações sobre o veículo.</h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <textarea class="box-ckeditor" name="observation" id="observation"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route . "/historyVehicle/$item->id" ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                            <div class="col-md-12">
                                <div class="box-fieldset clearfix">
                                    <div class="title-fieldset">Observações</div>
                                    <div class="form-group">
                                        <div class="col-md-12" style="margin-top: 15px; margin-bottom: 15px;">
                                            <?php if (!empty($saleVehicle[0]->observation)) { ?>
                                                <ul class="timeline">
                                                    <?php $i = 0;
                                                    foreach ($saleVehicle as $sale) { ?>
                                                        <li>
                                                            <i class="fa fa-comment bg-blue"></i>
                                                            <div class="timeline-item">
                                                                <a class="btn btn-danger btn-sm pull-right btn-disable-item" href="<?= URL . "{$this->route}/deleteObs/$sale->id_obs/$item->id"?>"><i class="fa fa-trash"></i></a>
                                                                <span class="time"><i class="fa fa-clock"></i> <?= date('d/m/Y', strtotime($sale->created_obs)) ?></span>
                                                                <h3 class="timeline-header"><a href="#"><?= ($i += 1) . ' - ' . $sale->created_by ?></a></h3>
                                                                <div class="timeline-body">
                                                                    <?= $sale->observation ?>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } else { ?>
                                                <p>Sem observações registradas até <?= date('d/m/Y H:i:s' ) ?></p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </section>
</div>
