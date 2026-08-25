<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $this->alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-md-12">
            <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Adicionar Apelido - <?= $item->name?></h3>
                            </div>
                            <div class="row box-body" id="propertyTypes">
                                <form role="form" action="<?= URL . $this->route . '/addAliases/' . $itemId ?>" method="POST">
                                    <div class="col-md-5 col-lg-5">
                                        <div class="form-group">
                                            <input type="text" name="alias" class="form-control" placeholder="Apelido">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <button type="submit" class="btn btn-primary">Salvar Apelido</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title" style="margin-top: 7px;">Editar Tipo Imóvel</h3>
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
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="prefix">Prefixo</label>
                                        <input autocomplete="off" type="text" class="form-control uppercase" id="prefix" value="<?= $item->prefix ? $item->prefix : null ?>" name="prefix" maxlength="5" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-animation="true" data-original-title="O prefixo irá alterar o código implementando nele, exemplo:" data-content="<?= $item->prefix ? $item->prefix : 'PFX'?>001">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="site_filter">Site</label>
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
                        </div>
                    </div>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Características</h3>
                        </div>
                        <div class="row box-body" id="immovableResources">
                            <?php foreach ($immovableResources as $immovableResource) { ?>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label for="immovable_<?= $immovableResource->id ?>">
                                                <input type="checkbox" name="immovableResource[]" id="immovable_<?= $immovableResource->id ?>" value="<?= $immovableResource->id ?>" <?php foreach ($immovableResourcesChecked as $immovableResourceChecked) {
                                                                                                                                                                                            echo $immovableResourceChecked->id_immovable_resource == $immovableResource->id ? 'checked' : "";
                                                                                                                                                                                        } ?>> <?= $immovableResource->name ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
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
