<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $this->alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Marca da água </h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitWaterMark' ?>" enctype="multipart/form-data" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="horizontal">Posição Horizontal</label>
                                        <select name="horizontal" id="horizontal" class="form-control">
                                            <option value="1" <?= $item->horizontal == 1 ? "selected" : "" ?>>Esquerda</option>
                                            <option value="2" <?= $item->horizontal == 2 ? "selected" : "" ?>>Direta</option>
                                            <option value="3" <?= $item->horizontal == 3 ? "selected" : "" ?>>Centro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="vertical">Posição Vertical</label>
                                        <select name="vertical" id="vertical" class="form-control">
                                            <option value="1" <?= $item->vertical == 1 ? "selected" : "" ?>>Cima</option>
                                            <option value="2" <?= $item->vertical == 2 ? "selected" : "" ?>>Baixo</option>
                                            <option value="3" <?= $item->vertical == 3 ? "selected" : "" ?>>Centro</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="opacity">Opacidade</label>
                                        <select name="opacity" id="opacity" class="form-control">
                                            <option value="25" <?= $item->opacity == "25" ? "selected" : "" ?>>25%</option>
                                            <option value="50" <?= $item->opacity == "50" ? "selected" : "" ?>>50%</option>
                                            <option value="75" <?= $item->opacity == "75" ? "selected" : "" ?>>75%</option>
                                            <option value="100" <?= $item->opacity == "100" ? "selected" : "" ?>>100%</option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="water_mark_required">Obrigatório</label>
                                        <select name="water_mark_required" id="water_mark_required" class="form-control">
                                            <option value="1" <?= $item->water_mark_required == 1 ? "selected" : "" ?>>Obrigatório</option>
                                            <option value="0" <?= $item->water_mark_required == 0 ? "selected" : "" ?>>Não Obrigatório</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="water_mark" class="btn btn-default btn-block">UPLOAD MARCA DA ÁGUA <small>200 x 100 .PNG</small></label>
                                        <input type="file" name="water_mark" id="water_mark" accept=".png" onchange="readURL(this, 'onloadWaterMark'), onfilename(this, 'spanWaterMark');" style="display: none;">
                                        <span id="spanWaterMark"></span>
                                    </div>
                                    <div>
                                        <?php if ($item->capa == true) { ?>
                                            <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Marca da água" sendTo="<?= $this->route . '/deleteWaterMark/' ?>">EXCLUIR MARCA DA ÁGUA</button>
                                        <?php } ?>
                                    </div>
                                    <img src="<?= $item->capa == true ? "img/more/water_mark-$item->cont.$item->ext" : "" ?>" id="onloadWaterMark" alt="" class="img-responsive">
                                </div>
                            </div>
                        </div>
                        <div class=" box-footer">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- modals -->
<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
            </div>
            <form method="POST" class="form-generic-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn" id="btn-submit"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente excluir a Marca da Água?
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>
