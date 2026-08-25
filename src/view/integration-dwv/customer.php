<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black">Clientes</a>
            <small>Adicionar</small>
        </h1>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Dados Gerais</h3>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleAddCustomerIntegration/' . $_GET['pg3'] ?>" method="POST">
                <div class="box-body">
                    <div class="row">
                        <div class="legal_person">
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="cnpj">Código Integração <span class="text-danger">*</span></label>
                                    <input value="<?= $idCustomerIntegration ?>" autocomplete="off" type="text" class="form-control" id="cod_integration" name="cod_integration"  readonly>
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
                                    <label for="company_name">Nome Razão da Empresa <span class="text-danger">*</span></label>
                                    <input autocomplete="off" type="text" class="form-control" id="company_name" name="company_name" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="fancy_name_company">Nome Fantasia da Empresa <span class="text-danger">*</span></label>
                                    <input autocomplete="off" type="text" class="form-control" id="fancy_name_company" name="fancy_name_company" required>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="state_registration">IE</label>
                                    <input autocomplete="off" type="text" class="form-control" id="state_registration" name="state_registration">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="name">Nome Completo <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_person_type">Tipo Pessoa <span class="text-danger">*</span></label>
                                <select name="id_person_type" id="id_person_type" class="form-control" required>
                                    <option value="2">JURÍDICA</option>
                                    <?php foreach ($persons as $person) { ?>
                                        <option value="<?= $person->id ?>"><?= $person->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="birth_date">Data de Nascimento <span class="text-danger"><?= $requiredField->birth_date == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="date" class="form-control date" id="birth_date" name="birth_date" <?= $requiredField->birth_date == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_profession">Profissão <span class="text-danger">*</span></label>
                                <select name="id_profession" id="id_profession" class="form-control" required>
                                    <?php foreach ($professions as $profession) { ?>
                                        <option value="<?= $profession->id ?>" <?= $profession->name == "AUTÔNOMO" ? "selected" : "" ?>><?= $profession->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_marital_status">Estado Civil <span class="text-danger">*</span></label>
                                <select name="id_marital_status" id="id_marital_status" class="form-control" required>
                                    <?php foreach ($maritalStatus as $msts) { ?>
                                        <option value="<?= $msts->id ?>" <?= $msts->id == "1" ? "selected" : "" ?> spouse="<?= $msts->spouse ?>"><?= $msts->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="nationality">Nacionalidade <span class="text-danger"><?= $requiredField->nationality == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="nationality" name="nationality" <?= $requiredField->nationality == 1 ? "required" : "" ?> value="Brasileiro(a)">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="rg">RG <span class="text-danger"><?= $requiredField->rg == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="rg" name="rg" minlength="7" maxlength="10" <?= $requiredField->rg == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="cpf">CPF <span class="text-danger"><?= $requiredField->cpf == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="cpf" name="person_registration" cpf_mask <?= $requiredField->cpf == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="cellphone">Celular <span class="text-danger"><?= $requiredField->cellphone == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="cellphone" name="cellphone" value="<?= $listItem->construction_company->additionals_contacts[0]->whatsapp ?>" cellphone <?= $requiredField->cellphone == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="phone">Telefone <span class="text-danger"><?= $requiredField->phone == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="phone" name="phone" value="<?= $listItem->construction_company->business_contacts[0]->phone_number ?>" phone <?= $requiredField->phone == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger"><?= $requiredField->email == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="email" class="form-control" id="email" name="email" value="<?= $listItem->construction_company->email ?>" <?= $requiredField->email == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 type_customer">
                            <div class="form-group">
                                <label for="id_customer_type">Tipo Cliente <span class="text-danger">*</span></label>
                                <select name="id_customer_type[]" id="id_customer_type" class="form-control" multiple required>
                                    <?php foreach ($customerTypes as $customerType) { ?>
                                        <option value="<?= $customerType->id ?>"><?= $customerType->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-lg-3" id="div-cep">
                            <div class="form-group">
                                <label for="cep">CEP <span class="text-danger"><?= $requiredField->cep == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="cep" name="cep" cep_mask <?= $requiredField->cep == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3" id="div-zip" hidden>
                            <div class="form-group">
                                <label for="zip">Código Postal <span class="text-danger"><?= $requiredField->zip == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="zip" name="zip">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="country">Pais <span class="text-danger"></span></label>
                                <select name="id_country" id="country" class="form-control" required>
                                    <?php foreach ($countries as $country) { ?>
                                        <option value="<?= $country->id ?>" <?= $country->id == $state->id_country ? "selected" : "" ?>><?= $country->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="uf_state">Estado <span class="text-danger">*</span></label>
                                <select name="uf_state" id="state" class="form-control" required>
                                    <option value="0"></option>
                                    <?php foreach ($states as $state) { ?>
                                        <option value="<?= $state->uf ?>" <?= $state->uf == $city->uf ? "selected" : "" ?>><?= $state->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_city">Cidade <span class="text-danger">*</span></label>
                                <select name="id_city" id="id_city" class="form-control" required>
                                    <option value="0"></option>
                                    <?php foreach ($cities as $city) { ?>
                                        <option value="<?= $city->id ?>" <?= $city->id == $branch->id_city ? "selected" : "" ?>><?= $city->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="neighborhood">Bairro <span class="text-danger"><?= $requiredField->neighborhood == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="neighborhood" name="neighborhood" <?= $requiredField->neighborhood == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="address">Endereço <span class="text-danger"><?= $requiredField->address == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="address" name="address" <?= $requiredField->address == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="number_address">Número <span class="text-danger"><?= $requiredField->number_address == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="number_address" name="number_address" <?= $requiredField->number_address == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="complement">Complemento <span class="text-danger"><?= $requiredField->complement == 1 ? "*" : "" ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="complement" name="complement" <?= $requiredField->complement == 1 ? "required" : "" ?>>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="branches">Filiais <span class="text-danger"><?= $requiredField->branches == 1 ? "*" : "" ?></span></label>
                                <select name="branches[]" id="branches" multiple <?= $requiredField->branches == 1 ? "required" : "" ?>>
                                    <?php foreach ($branches as $branch) { ?>
                                        <option value="<?= $branch->id ?>"><?= $branch->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <button type="submit" id="btn2" class="btn btn-block btn-primary" onclick="disable(1)">Cadastrar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- Modals -->

<div id=" generic-message-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn" id="btn-confirm" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>