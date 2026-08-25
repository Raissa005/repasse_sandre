<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $this->alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" enctype="multipart/form-data" method="POST">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Editar Praia dos Sonhos </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="nome">Nome Praia dos Sonhos <span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="nome" name="nome" value="<?= $item->nome ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="state">Estado</label>
                                        <select class="form-control" name="state" id="state">
                                            <?php foreach ($states as $state) { ?>
                                                <option value="<?= $state->uf ?>" <?= $state->uf == $item->uf ? "selected" : "" ?>><?= $state->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_city">Cidade</label>
                                        <select class="form-control" name="id_cidade" id="id_city">
                                            <?php foreach ($cities as $city) { ?>
                                                <option value="<?= $city->id ?>" <?= $city->id == $item->id_cidade ? "selected" : "" ?>><?= $city->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="ativo">Status</label>
                                        <select class="form-control" name="ativo" id="ativo">
                                            <option value="1" <?= $item->ativo == "1" ? "selected" : "" ?>>Ativado</option>
                                            <option value="0" <?= $item->ativo == "0" ? "selected" : "" ?>>Desativado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box box-warning">
                        <div class="box-header with-border">
                            <h3 class="box-title">Imagens Praia dos Sonhos </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageCapa" class="btn btn-default btn-block" style="text-transform: uppercase;">upload de capa <small>(500x500)</small></label>
                                        <input type="file" name="imageCapa" id="imageCapa" onchange="readURL(this, 'onloadImageCapa'), onfilename(this, 'spanFilenameCapa');" style="display: none;">
                                        <span id="spanFilenameCapa"></span>
                                    </div>
                                    <img class="img-responsive img-rounded" style="margin-left: auto; margin-right: auto;" src="<?= URL . "img/site_imgs/praiaSonhos/$itemId/capa-$item->cont.$item->ext" ?>" id="onloadImageCapa" alt="" style="width: 300px;">
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="imageBanner" class="btn btn-default btn-block" style="text-transform: uppercase;">upload de Banner <small>(1140x360)</small></label>
                                        <input type="file" name="imageBanner" id="imageBanner" onchange="readURL(this, 'onloadImageBanner'), onfilename(this, 'spanFilenameBanner');" style="display: none;">
                                        <span id="spanFilenameBanner"></span>
                                    </div>
                                    <img class="img-responsive img-rounded" style="margin-left: auto; margin-right: auto;" src="<?= URL . "img/site_imgs/praiaSonhos/$itemId/banner-$item->cont2.$item->ext2" ?>" id="onloadImageBanner" alt="" style="width: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                        </div>
                    </div>
                </form>
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
