<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\NavTabsComponent;
use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content">
        <form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $customerId ?>" method="POST">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($navTabs); ?>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <section class="container-fluid">
                            <div class="row">
                                <div class="box-body">
                                    <input type="hidden" id="idCustomer" value="<?= $customerId ?>">
                                    <input type="hidden" name="disabledSeller" id="disabledSeller" value="<?= $attrInputs ?>">
                                    <div class="row">
                                        <div class="legal_person">
                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label for="cnpj">CNPJ <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input autocomplete="off" type="text" class="form-control" id="cnpj" name="cnpj" value="<?= isset($customer->cnpj) ? $customer->cnpj : '' ?>" cnpj <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label for="company_name">Nome Razão da Empresa <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input autocomplete="off" type="text" class="form-control" id="company_name" name="company_name" value="<?= isset($customer->company_name) ? $customer->company_name : '' ?>" <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label for="fancy_name_company">Nome Fantasia da Empresa <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                    <input autocomplete="off" type="text" class="form-control" id="fancy_name_company" name="fancy_name_company" value="<?= isset($customer->fancy_name_company) ? $customer->fancy_name_company : '' ?>" <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label for="name">IE </label>
                                                    <input autocomplete="off" type="text" class="form-control" id="state_registration" name="state_registration" value="<?= isset($customer->state_registration) ? $customer->state_registration : '' ?>" <?= $attrInputs ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="name">Nome Completo <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $customer->name ?>" class="form-control" id="name" name="name" <?= $attrInputsRequired ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="id_person_type">Tipo Pessoa <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select name="id_person_type" id="id_person_type" class="form-control" <?= $attrInputsRequired ?>>
                                                    <?php foreach ($persons as $person) { ?>
                                                        <option value="<?= $person->id ?>" <?= $person->id == $customer->id_person_type ? "selected" : "" ?>><?= $person->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="birth_date">Data de Nascimento <span class="text-danger"><?= $permission && ($requiredField->birth_date == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="date" class="form-control date" id="birth_date" name="birth_date" value="<?= $customer->birth_date ?>" <?= $permission && ($requiredField->birth_date  == 1)  ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="id_profession">Profissão <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select name="id_profession" id="id_profession_edit" class="form-control" <?= $attrInputsRequired ?>>
                                                    <?php foreach ($professions as $profession) { ?>
                                                        <option value="<?= $profession->id ?>" <?= ($profession->id == $customer->id_profession) ? "selected" : "" ?>><?= $profession->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="id_marital_status">Estado Civil <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select name="id_marital_status" id="id_marital_status" class="form-control" <?= $attrInputsRequired ?>>
                                                    <?php foreach ($maritalStatus as $msts) { ?>
                                                        <option value="<?= $msts->id ?>" spouse="<?= $msts->spouse ?>" <?= ($msts->id == $customer->id_marital_status) ? "selected" : "" ?>><?= $msts->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="nationality">Nacionalidade <span class="text-danger"><?= $permission && ($requiredField->nationality == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="nationality" name="nationality" value="<?= $customer->nationality ?>" <?= $permission && ($requiredField->nationality  == 1)  ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="rg">RG <span class="text-danger"><?= $permission && ($requiredField->rg == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : $customer->rg ?>" class="form-control" id="rg" name="rg" <?= !$restricted ? " minlength='7' maxlength='10' " : "" ?> <?= $permission && ($requiredField->rg == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="cpf">CPF <span class="text-danger"><?= $permission && ($requiredField->cpf == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : $customer->person_registration ?>" class="form-control" id="cpf" name="person_registration" <?= !$restricted ? "cpf_mask" : "" ?> <?= $permission && ($requiredField->cpf == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="cellphone">Celular <span class="text-danger"><?= $permission && ($requiredField->cellphone == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : $customer->cellphone ?>" class="form-control" id="cellphone" name="cellphone" <?= !$restricted ? "cellphone" : "" ?> <?= $permission && ($requiredField->cellphone == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="phone">Telefone <span class="text-danger"><?= $permission && ($requiredField->phone == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : $customer->phone ?>" class="form-control" id="phone" name="phone" <?= !$restricted ? "phone" : "" ?> <?= $permission && ($requiredField->phone == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="email">Email <span class="text-danger"><?= $permission && ($requiredField->email == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="email" class="form-control" id="email" name="email" value="<?= $restricted ? "RESTRITO" : $customer->email ?>" <?= $permission && ($requiredField->email == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3" id="div-cep" <?= $customer->id_country != 41 ? "hidden" : ""?>>
                                            <div class="form-group">
                                                <label for="cep">CEP <span class="text-danger"><?= $permission && ($requiredField->cep == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $customer->cep ?>" class="form-control" id="cep" name="cep" cep_mask <?= $permission && ($requiredField->cep == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3" id="div-zip" <?= $customer->id_country == 41 ? "hidden" : ""?>>
                                            <div class="form-group">
                                                <label for="zip">Código Postal <span class="text-danger"><?= $requiredField->zip == 1 ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="zip" name="zip" value="<?= $customer->zip ?>" <?= $permission && ($requiredField->zip == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="country">Pais <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select name="id_country" id="country" class="form-control" required>
                                                    <?php foreach ($countries as $country) { ?>
                                                        <option value="<?= $country->id ?>" <?= $country->id == $customer->id_country ? "selected" : "" ?>><?= $country->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="uf_state">Estado <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select name="uf_state" id="state" class="form-control" <?= $attrInputsRequired ?> <?= $customer->id_country != 41 ? "disabled" : ""?>>
                                                    <?php foreach ($states as $state) { ?>
                                                        <option value="<?= $customer->id_country == 41 ? $state->uf : ""?>" <?= $state->uf == $customer->uf_state ? "selected" : "" ?>><?= $customer->id_country == 41 ? $state->name : ""?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="id_city">Cidade <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select name="id_city" id="id_city" class="form-control" <?= $attrInputsRequired ?> <?= $customer->id_country != 41 ? "disabled" : ""?>>
                                                    <?php foreach ($cities as $city) { ?>
                                                        <option value="<?= $city->id ?>" <?= $city->id == $customer->id_city ? "selected" : "" ?>><?= $city->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="neighborhood">Bairro <span class="text-danger"><?= $permission && ($requiredField->neighborhood == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : $customer->neighborhood ?>" class="form-control" id="neighborhood" name="neighborhood" <?= $permission && ($requiredField->neighborhood == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="address">Endereço <span class="text-danger"><?= $permission && ($requiredField->address == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : $customer->address ?>" class="form-control" id="address" name="address" <?= $permission && ($requiredField->address == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label for="number_address">Número <span class="text-danger"><?= $permission && ($requiredField->number_address == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" value="<?= $restricted ? "RESTRITO" : $customer->number_address ?>" class="form-control" id="number_address" name="number_address" <?= $permission && ($requiredField->number_address == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="complement">Complemento <span class="text-danger"><?= $permission && ($requiredField->complement == 1) ? "*" : "" ?></span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="complement" name="complement" value="<?= $restricted ? "RESTRITO" : $customer->complement ?>" <?= $permission && ($requiredField->complement == 1) ? "required" : $attrInputs ?>>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6 type_customer">
                                            <div class="form-group">
                                                <label for="id_customer_type">Tipo Cliente <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                                <select name="id_customer_type[]" id="id_customer_type" class="form-control" multiple <?= $attrInputsRequired ?>>
                                                    <?php foreach ($customerTypes as $customerType) { ?>
                                                        <option value="<?= $customerType->id ?>" <?= in_array($customerType->id, $typeSelected) ? "selected" : "" ?>><?= $customerType->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="observation">Observação</label>
                                                <textarea name="observation" id="observation" class="form-control" rows="5"><?= htmlspecialchars($customer->observation ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Configurações e Informações do Cadastro</h3>
                </div>
                <div class="box-body">
                    <div class="container-fluid">
                        <div class="row">
                            <?php if (Secure::access_secretary()) { ?>
                                <div class="row">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="created_by">Trocar Proprietário Cadastro</label>
                                            <select name="created_by" id="created_by" class="form-control">
                                                <?php
                                                $aux = "";
                                                $auxProfile = "";
                                                foreach ($users as $user) {
                                                    $auxProfile = $auxProfile != $user->profile_name ? $user->profile_name : $auxProfile;
                                                    if ($auxProfile != $aux) {
                                                ?>
                                                        <optgroup label="<?= $auxProfile ?>">
                                                        <?php
                                                    } ?>
                                                        <option value="<?= $user->id ?>" <?= $user->id == $customer->created_by ? "selected" : "" ?>><?= $user->name ?></option>
                                                        <?php if ($auxProfile != $aux) {
                                                            $aux = $auxProfile;
                                                        ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="blocked" class="<?= $customer->blocked == 1 ? "text-danger" : "text-info" ?>">Trava Cliente</label>
                                            <select name="blocked" id="blocked" class="form-control">
                                                <option value="0" <?= $customer->blocked == 0 ? "selected" : "" ?>>Desbloqueado</option>
                                                <option value="1" <?= $customer->blocked == 1 ? "selected" : "" ?>>Bloqueado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="balance">Saldo do Cliente</label>
                                            <input autocomplete="off" value="<?= Util::maskMoney($customer->balance ?? $credit) ?>" type="text" class="form-control" id="balance" name="balance" data-mask-money>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="branches">Exibir em outras filiais</label>
                                            <select name="branches[]" id="branches" class="form-control" multiple <?= $attrInputsRequired ?>>
                                                <?php foreach ($branches as $branch) { ?>
                                                    <option value="<?= $branch->id ?>" <?= in_array($branch->id, array_column($customer->branches, 'branch_id')) ? "selected" : "" ?>><?= $branch->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-6 col-lg-6">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <h5>Criador por: <strong><?= $userCreated->name ?></strong></h5>
                                        <h5>Data da Criação: <strong><?= Date::date($customer->created_at) ?></strong></h5>
                                    </div>
                                </div>
                                <?php if ($userUpdated) { ?>
                                    <div class="col-md-6 col-lg-6">
                                        <div class="form-group pull-right" style="margin-bottom: 0px;">
                                            <h5>Atualizado por: <strong><?= $userUpdated->name ?></strong></h5>
                                            <h5>Data da atualizado: <strong><?= Date::date($customer->updated_at) ?></strong></h5>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-block btn-primary" <?= $attrInputs ?>>Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>