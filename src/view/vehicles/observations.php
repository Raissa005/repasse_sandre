<?php

use RR\libs\Date;
use RR\libs\Secure;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div>
                        <div class="row">
                            <form action="<?= URL . $this->route . "/handleSubmitAddObservations/$itemId" ?>" method="POST">
                                <div class="box-body" style="padding-bottom: 0px;">
                                    <div class="row">
                                        <?php if (Secure::access_secretary()) { ?>
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
                                                <div class="box-footer">
                                                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                                                    <div class="pull-right">
                                                        <button class="btn btn-primary">Salvar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box-fieldset clearfix">
                                                <div class="title-fieldset">Observações</div>
                                                <div class="form-group">
                                                    <div class="col-md-12" style="margin-top: 15px; margin-bottom: 15px;">
                                                        <?php if (!empty($vehicleObservations->data)) { ?>
                                                            <ul class="timeline">
                                                                <?php for ($i = 0; $i < $vehicleObservations->count; $i++) { ?>
                                                                    <?php if (!empty($vehicleObservations->data)) { ?>
                                                                        <li>
                                                                            <i class="fa fa-comment bg-blue"></i>
                                                                            <div class="timeline-item">
                                                                                <a id="<?= $vehicleObservations->data[$i]->id ?>" class="btn btn-danger btn-sm pull-right btn-disable-item" sendTo="<?= "{$this->route}/disableItemTimeline/" ?>" bodyHtml="Deseja realmente excluir este Item?"><i class="fa fa-trash"></i></a>
                                                                                <span class="time"><i class="fa fa-clock"></i> <?= Date::date_hour($vehicleObservations->data[$i]->created_at) ?></span>
                                                                                <h3 class="timeline-header"><a href="#"><?= ($i + 1) . ' - ' . $vehicleObservations->data[$i]->user_name ?></a></h3>
                                                                                <div class="timeline-body">
                                                                                    <?= \RR\libs\Util::richText($vehicleObservations->data[$i]->observation) ?>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    <?php } ?>
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>