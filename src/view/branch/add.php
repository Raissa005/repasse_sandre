<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Filial</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" enctype="multipart/form-data" method="POST">
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
                                        <label for="name_legal">Nome jurídico</label>
                                        <input autocomplete="off" type="text" class="form-control" id="name_legal" name="name_legal">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="cnpj">CNPJ <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="cnpj" name="cnpj" cnpj required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="creci_legal">Creci jurídico</label>
                                        <input autocomplete="off" type="text" class="form-control" id="creci_legal" name="creci_legal" maxlength="10">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="state">Estado <span class="text-danger">*</span></label>
                                        <select name="state" id="state" class="form-control" required>
                                            <?php foreach ($states as $state) { ?>
                                                <option value="<?= $state->uf ?>" <?= $state->uf == "SC" ? "selected" : "" ?>><?= $state->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_city">Cidade <span class="text-danger">*</span></label>
                                        <select name="id_city" id="id_city" class="form-control" required>
                                            <?php foreach ($cities as $city) { ?>
                                                <option value="<?= $city->id ?>"><?= $city->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="neighborhood">Bairro <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="neighborhood" name="neighborhood" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="address">Rua <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="address" name="address" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="number">Nº <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="number" name="number" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="complement">Complemento </label>
                                        <input autocomplete="off" type="text" class="form-control" id="complement" name="complement">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="restrict">Restringir dados do proprietário</label>
                                        <select name="restrict" id="restrict" class="form-control">
                                            <option value="0">Não Restringir</option>
                                            <option value="1">Restringir</option>
                                        </select>
                                    </div>
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
        </div>
    </section>
</div>
