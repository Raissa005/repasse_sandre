<form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
    <div class="tab-content">
        <div class="tab-pane active">
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
                            <label for="name_legal">Nome jurídico</label>
                            <input autocomplete="off" type="text" class="form-control" id="name_legal" name="name_legal" value="<?= $item->name_legal ?>">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="cnpj">CNPJ <span class="text-danger">*</span></label>
                            <input autocomplete="off" type="text" class="form-control" id="cnpj" name="cnpj" value="<?= $item->cnpj ?>" cnpj>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="creci_legal">Creci jurídico </label>
                            <input autocomplete="off" type="text" class="form-control" id="creci_legal" name="creci_legal" value="<?= $item->creci_legal ?>" maxlength="10">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input autocomplete="off" type="email" class="form-control" id="email" name="email" value="<?= $item->email ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="state">Estado <span class="text-danger">*</span></label>
                            <select name="state" id="state" class="form-control" required>
                                <?php foreach ($states as $state) { ?>
                                    <option value="<?= $state->uf ?>" <?= $state->uf == $item->uf ? "selected" : "" ?>> <?= $state->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="id_city">Cidade <span class="text-danger">*</span></label>
                            <select name="id_city" id="id_city" class="form-control" required>
                                <?php foreach ($cities as $city) { ?>
                                    <option value="<?= $city->id ?>" <?= $item->id_city == $city->id ? "selected" : "" ?>><?= $city->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="neighborhood">Bairro <span class="text-danger">*</span></label>
                            <input autocomplete="off" type="text" class="form-control" id="neighborhood" name="neighborhood" value="<?= $item->neighborhood ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="address">Rua <span class="text-danger">*</span></label>
                            <input autocomplete="off" type="text" class="form-control" id="address" name="address" value="<?= $item->address ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="number">Nº <span class="text-danger">*</span></label>
                            <input autocomplete="off" type="text" class="form-control" id="number" name="number" value="<?= $item->number ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="complement">Complemento</label>
                            <input autocomplete="off" type="text" class="form-control" id="complement" name="complement" value="<?= isset($item->complement) && !empty($item->complement) ? $item->complement : "" ?>">
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="restrict">Restringir dados do proprietário</label>
                            <select name="restrict" id="restrict" class="form-control">
                                <option value="1" <?= $item->restrict_owner_data == 1 ? "selected" : "" ?>>Restringir</option>
                                <option value="0" <?= $item->restrict_owner_data == 0 ? "selected" : "" ?>>Não Restringir</option>
                            </select>
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
        </div>
    </div>
</form>

</div>
</section>
</div>
