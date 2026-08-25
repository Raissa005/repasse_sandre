<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Tipo de Imóvel</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="prefix">Prefixo</label>
                                        <input autocomplete="off" type="text" class="form-control uppercase" id="prefix" name="prefix" maxlength="5" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-animation="true" data-original-title="O prefixo irá alterar o código implementando nele, exemplo:" data-content="PFX001">
                                    </div>
                                </div>
                            </div>
                            <div class="box-header with-border">
                                <h3 class="box-title">Características</h3>
                            </div>
                            <div class="row box-body" id="immovableResources">
                                <?php foreach ($immovableResources as $resource) { ?>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label for="id_immovable_resource_<?= $resource->id?>">
                                                    <input type="checkbox" name="immovableResource[]" id="id_immovable_resource_<?= $resource->id?>" value="<?= $resource->id?>" checked> <?= $resource->name?>
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
                                <button type="submit" class="btn btn-block btn-primary">Cadastrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
