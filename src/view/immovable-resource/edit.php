<div class="content-wrapper">
    <section class="content container-fluid">
        <?= $this->alert->defaultItemAlerts(); ?>
        <div class="row">


            <div class="col-md-12">
                <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" enctype="multipart/form-data" method="POST">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title" style="margin-top: 7px;">Editar Características Imóvel</h3>
                            <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addItem' ?>">Adicionar</a>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $item->name ?>" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="data_type">Campo preenchido por...<span class="text-danger">*</span></label>
                                        <select name="data_type" id="data_type" class="form-control" required>
                                            <option value="1" <?= $item->data_type == 1 ? "selected" : "" ?>>Texto</option>
                                            <option value="2" <?= $item->data_type == 2 ? "selected" : "" ?>>Número</option>
                                            <option value="3" <?= $item->data_type == 3 ? "selected" : "" ?>>Data</option>
                                            <option value="4" <?= $item->data_type == 4 ? "selected" : "" ?>>Sim / Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="filter">Filtro Sistema</label>
                                        <select name="filter" id="filter" class="form-control">
                                            <option value="1" <?= $item->filter == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->filter == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="site_filter">Filtro Site</label>
                                        <select name="site_filter" id="site_filter" class="form-control">
                                            <option value="1" <?= $item->site_filter == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->site_filter == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" <?= $item->status == 1 ? "selected" : "" ?>>Ativo</option>
                                            <option value="0" <?= $item->status == 0 ? "selected" : "" ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="ico" class="btn btn-default btn-block">UPLOAD ICONE <small>(20x20)</small></label>
                                        <input type="file" name="ico" id="ico" accept="image/" onchange="readURL(this), onfilename(this, 'spanFilenameIco');" style="display: none;">
                                        <span id="spanFilenameIco"></span>
                                    </div>
                                    <div>
                                        <?php if (file_exists("img/immovable_resource_ico/$itemId/$itemId" . "-" . $item->cont . "." . $item->ext)) { ?>
                                            <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Icone" id="<?= $itemId ?>" sendTo="<?= $this->route . '/deleteIco/' ?>">EXCLUIR ICONE</button>
                                        <?php } ?>
                                    </div>
                                    <img src="<?= URL . "img/immovable_resource_ico/$itemId/$itemId-" . $item->cont . "." . $item->ext ?>" id="onloadImage" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Tipo de Imóvel</h3>
                        </div>
                        <div class="row box-body" id="propertyTypes">
                            <?php foreach ($propertyTypes as $propertyType) { ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label for="property_<?= $propertyType->id ?>">
                                                <input type="checkbox" name="propertyType[]" id="property_<?= $propertyType->id ?>" value="<?= $propertyType->id ?>" <?php foreach ($propertyTypesChecked as $propertyTypeChecked) {
                                                                                                                                                                            echo $propertyTypeChecked->id_property_type == $propertyType->id ? 'checked' : "";
                                                                                                                                                                        } ?>><?= $propertyType->name ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Adicionar Apelido - <?= mb_convert_case($item->name, MB_CASE_TITLE, "UTF-8") ?></h3>
                        </div>
                        <div class="row box-body" id="propertyTypes">
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="name">Apelido </label>
                                    <input type="text" name="alias" class="form-control" value="<?= $item->alias ?>" placeholder="Apelido">
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

<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente excluir a Icone?
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