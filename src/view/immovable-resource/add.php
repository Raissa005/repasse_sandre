<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Cadastrar Características Imóvel</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="data_type">Campo preenchido por...<span class="text-danger">*</span></label>
                                        <select name="data_type" id="data_type" class="form-control" required>
                                            <option value="1">Texto</option>
                                            <option value="2">Número</option>
                                            <option value="3">Data</option>
                                            <option value="4">Sim / Não</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Tipo Imóvel</h3>
                        </div>
                        <div class="box-body" id="propertyTypes">
                            <div class="row">
                                <?php foreach ($propertyTypes as $type) { ?>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label for="property_<?= $type->id ?>">
                                                    <input type="checkbox" name="propertyType[]" id="property_<?= $type->id ?>" value="<?= $type->id ?>" checked> <?= $type->name ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
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
    </section>
</div>
