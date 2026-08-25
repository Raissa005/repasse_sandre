<div class="tab-pane active">
    <section class="container-fluid">
        <div class="row">
            <form role="form" action="<?= URL . $this->route . '/handleSubmitSpouse/' . $customerId ?>" method="POST">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="name">Nome Completo <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" type="text" value="<?= isset($spouse->name) ? $spouse->name : "" ?>" class="form-control input-spouse" id="name" name="name" <?= $attrInputsRequired ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="birth_date">Data de Nascimento <span class="text-danger"><?= $permission && $requiredField->birth_date == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="date" class="form-control date" id="birth_date" name="birth_date" value="<?= isset($spouse->birth_date) ? $spouse->birth_date : "" ?>" <?= $permission && ($requiredField->birth_date == 1) ? "required" : $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_profession">Profissão <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <select name="id_profession" id="id_profession" class="form-control input-spouse" <?= $attrInputsRequired ?>>
                                    <?php foreach ($professions as $profession) { ?>
                                        <option value="<?= $profession->id ?>" <?= isset($spouse->id_profession) && $profession->id == $spouse->id_profession ? "selected" : "" ?>><?= $profession->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="nationality">Nacionalidade <span class="text-danger"><?= $permission && $requiredField->nationality == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="nationality" name="nationality" value="<?= !empty($spouse->nationality) ? $spouse->nationality : "Brasileiro(a)" ?>" <?= $permission && ($requiredField->nationality == 1) ? "required" : $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="rg">RG <span class="text-danger"><?= $permission && $requiredField->rg == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : (isset($spouse->rg) ? $spouse->rg : "") ?>" class="form-control input-spouse" id="rg" name="rg" <?= !$restricted ? " minlength='7' maxlength='10' " : "" ?> <?= $permission && ($requiredField->rg == 1) ? "required" : $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="person_registration">CPF <span class="text-danger"><?= $permission && $requiredField->cpf == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control input-spouse" value="<?= $restricted ? "RESTRITO" : (isset($spouse->person_registration) ? $spouse->person_registration : "") ?>" id="person_registration" name="person_registration" <?= !$restricted ? "cpf_mask" : "" ?> <?= $permission && ($requiredField->cpf == 1) ? "required" : $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="cellphone">Celular <span class="text-danger"><?= $permission && $requiredField->cellphone == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : (isset($spouse->cellphone) ? $spouse->cellphone : "") ?>" class="form-control input-spouse" id="cellphone" name="cellphone" <?= !$restricted ? "cellphone" : "" ?> <?= $permission && ($requiredField->cellphone == 1) ? "required" : $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="phone">Telefone <span class="text-danger"><?= $permission && $requiredField->phone == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" value="<?= ($restricted ? "RESTRITO" : (isset($spouse->phone) ? $spouse->phone : "")) ?>" class="form-control" id="phone" name="phone" <?= !$restricted ? "phone" : "" ?> <?= $permission && ($requiredField->phone == 1) ? "required" : $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group" style="padding-top: 30px; display: flex;">
                                <input type="checkbox" class="form-group" value="1" id="checkAddress" name="checkAddress" <?= (isset($spouse->address_spouse) && $spouse->address_spouse == 1) ? "checked" : "" ?> <?= $attrInputs ?>>
                                <label for="checkAddress" style="cursor: pointer; margin-left: 6px;">Mesmo endereço do cliente</label>
                            </div>
                        </div>
                    </div>
                    <div id="addressFields">
                        <div class="row">
                            <div class="col-md-3 col-lg-3" id="div-cep">
                                <div class="form-group">
                                    <label for="cep">CEP <span class="text-danger"><?= $permission && $requiredField->cep == 1 ? "*" : "" ?></span></label>
                                    <input autocomplete="off" type="text" value="<?= isset($spouse->cep) ? $spouse->cep : ""  ?>" class="form-control input-spouse-address" id="cep" name="cep" cep_mask <?= $permission && ($requiredField->cep == 1) ? "" : $attrInputs ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3" id="div-zip" hidden>
                                <div class="form-group">
                                    <label for="zip">Código Postal <span class="text-danger"><?= $requiredField->zip == 1 ? "*" : ""?></span></label>
                                    <input autocomplete="off" type="text" class="form-control" id="zip" name="zip">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="country">Pais <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                    <select name="id_country" id="country" class="form-control">
                                        <?php foreach ($countries as $country) { ?>
                                            <option value="<?= $country->id ?>" <?= isset($spouse->id_country) ? ($country->id == $spouse->id_country ? "selected" : "") : ($country->id == 41 ? "selected" : "") ?>><?= $country->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="state">Estado <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                    <select name="state" id="state" class="form-control input-spouse-address" <?= $attrInputs ?>>
                                        <?php foreach ($states as $state) { ?>
                                            <option value="<?= $state->uf ?>" <?= isset($spouse->uf_state) ? ($state->uf == $spouse->uf_state ? "selected" : "") : ($state->uf == "SC" ? "selected" : "") ?>><?= $state->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="id_city">Cidade <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                    <select name="id_city" id="id_city" class="form-control input-spouse-address" <?= $attrInputs ?>>
                                        <?php foreach ($cities as $city) { ?>
                                            <option value="<?= $city->id ?>" <?= (isset($spouse->id_city) && $city->id == $spouse->id_city) ? "selected" : "" ?>><?= $city->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="neighborhood">Bairro <span class="text-danger"><?= $permission && $requiredField->neighborhood == 1 ? "*" : "" ?></span></label>
                                    <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : (isset($spouse->neighborhood) ? $spouse->neighborhood : "")  ?>" class="form-control input-spouse-address" id="neighborhood" name="neighborhood" <?= $permission && ($requiredField->neighborhood == 1) ? "" : $attrInputs ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="address">Endereço <span class="text-danger"><?= $permission && $requiredField->address == 1 ? "*" : "" ?></span></label>
                                    <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : (isset($spouse->address) ? $spouse->address : "")  ?>" class="form-control input-spouse-address" id="address" name="address" <?= $permission && ($requiredField->address == 1) ? "" : $attrInputs ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="number_address">Número <span class="text-danger"><?= $permission && $requiredField->number_address == 1 ? "*" : "" ?></span></label>
                                    <input autocomplete="off" type="text" class="form-control input-spouse-address" value="<?= $restricted ? "RESTRITO" : (isset($spouse->number_address) ? $spouse->number_address : "")  ?>" id="number_address" name="number_address" <?= $permission && ($requiredField->number_address == 1) ? "" : $attrInputs ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="complement">Complemento <span class="text-danger"><?= $permission && $requiredField->complement == 1 ? "*" : "" ?></span></label>
                                    <input autocomplete="off" type="text" class="form-control" id="complement" name="complement" value="<?= $restricted ? "RESTRITO" : (isset($spouse->complement) ? $spouse->complement : "") ?>" <?= $permission && ($requiredField->complement == 1) ? "" : $attrInputs ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <h5>Criador por: <strong><?= $userCreated->name ?></strong></h5>
                            </div>
                        </div>
                        <?php if ($userUpdated) { ?>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <h5 class="pull-right">Atualizado por: <strong><?= $userUpdated->name ?></strong> </h5>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-block btn-primary" <?= $attrInputs ?>>Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

</div>
</section>
</div>