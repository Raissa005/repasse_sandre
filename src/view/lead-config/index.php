<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Configuração de Distribuição de Leads</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitLeadConfig' ?>" method="POST" id="submit-distribution-type">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="distribution_type">Distribuição de Leads</label>
                                        <select name="distribution_type" id="distribution_type" class="form-control">
                                            <option value="0" <?= !empty($items->distribution_type) && $items->distribution_type == 0 ? "selected" : "" ?>>Aleatório</option>
                                            <option value="1" <?= !empty($items->distribution_type) && $items->distribution_type == 1 ? "selected" : "" ?>>Plantão do Dia</option>
                                            <option value="2" <?= !empty($items->distribution_type) && $items->distribution_type == 2 ? "selected" : "" ?>>Atendimento em Aberto</option>
                                            <option value="3" <?= !empty($items->distribution_type) && $items->distribution_type == 3 ? "selected" : "" ?>>Manual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="serviceHours">
                                        <div class="col-md-2" id='external-events'>
                                            <h4>Vendedores</h4>
                                            <div id='external-events-list'>
                                                <?php foreach ($sellers as $seller) { ?>
                                                    <div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event' style="background-color: <?= $seller->color ? $seller->color : '' ?>; border: none;" data-event='{"id_group": "<?= $seller->id ?>", "title": "<?= $seller->name ?>", "color": "<?= $seller->color ?>"}'>
                                                        <div class='fc-event-main'><?= $seller->name ?></div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-10" id='calendar-wrap'>
                                            <div id='calendar'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
    </section>
</div>

<!-- modals -->
<div id="generic-message-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                </div>
                <button type="button" class="btn" id="btn-confirm" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>