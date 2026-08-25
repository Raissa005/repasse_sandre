<?php

use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;
?>
<form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $productId ?>" method="POST">
    <div class="tab-pane active">
        <div class="container-fluid" style="background-color: #fff; margin-bottom: 20px;">
            <div class="row">
                <input type="hidden" id="id_product" value="<?= $productId ?>">
                <input type="hidden" id="immovable_record" value="<?= $branch->immovable_record ?>">
                <!-- <input type="hidden" id="id_branch" name="id_branch" value="<?= $product->id_branch ?>">
                <input type="hidden" id="addProducts" value="0"> -->
                <input type="hidden" id="today" value="<?= date("Y-m-d") ?>">
                <div class="box-body">
                    <div class="row">
                        <?php if (Secure::access_secretary() || Secure::creator($product->created_by)) { ?>
                            <div class="realEstateOwner">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="id_customer_owner">Buscar Proprietário</label>
                                        <div class="text-left">
                                            <button type="button" title="Buscar Proprietário" class="btn btn-block btn-primary btn-block" id="modal-owner" data-toggle="modal" data-target="#owner">
                                                <i class="fas fa-user-tag"></i> Buscar Proprietário
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="id_owner" name="id_owner" value="<?= isset($owner->id) ? $owner->id : "" ?>" required>
                            </div>
                        <?php } ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name_owner">Nome do Proprietário</label>
                                <?php if (isset($owner->id) && $restrictDataValidation) { ?>
                                    <span class="pull-right">
                                        <a href="<?= URL . "customer/editItem/" . $owner->id ?>" target="_blank" class="btn btn-xs">Ver Proprietário</a>
                                    </span>
                                <?php } ?>
                                <input autocomplete="off" type="text" class="form-control" id="name_owner" name="name_owner" value="<?= isset($owner->id) && $restrictDataValidation ? (isset($owner->fancy_name_company) ? $owner->fancy_name_company : $owner->name) : "RESTRITO" ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cpf_owner">CPF/CNPJ do Proprietário</label>
                                <input autocomplete="off" type="text" class="form-control" id="cpf_owner" name="cpf_owner" value="<?= $restrictDataValidation ? (isset($owner->cnpj) ? $owner->cnpj : (isset($owner->person_registration) ? $owner->person_registration : '')) : 'RESTRITO' ?>" <?= $restrictDataValidation ? 'cpfcnpj' : '' ?> disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_city_owner">Cidade - UF</label>
                                <input autocomplete="off" type="text" class="form-control" id="city_owner" name="city_owner" value="<?= isset($owner->city_name) && isset($owner->uf) ? $owner->city_name . " - " . $owner->uf : "" ?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cod">Código</label>
                                <input autocomplete="off" type="text" class="form-control" id="cod" name="cod" value="<?= isset($product->cod) ? $product->cod : $product->id ?>" <?= $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">Nome do Imóvel <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $product->name ?>" name="name" <?= $attrInputsRequired ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_residential_type">Tipo Imóvel <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <select name="id_residential_type" id="id_residential_type" class="form-control" <?= $attrInputsRequired ?>>
                                    <?php foreach ($propertyTypes as $propertyType) { ?>
                                        <option value="<?= $propertyType->id ?>" <?= $propertyType->id == $product->id_residential_type ? "selected" : "" ?>><?= $propertyType->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="propertyCategory">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_property_category">Categoria Imóvel <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                    <select name="id_property_category" id="id_property_category" class="form-control" <?= $attrInputsRequired ?>>
                                        <?php foreach ($propertyCategory as $category) { ?>
                                            <option value="<?= $category->id ?>" <?= isset($product->id_property_category) ? ($category->id == $product->id_property_category ? 'selected' : '') : "" ?>><?= $category->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_property_classification">Classificação Imóvel </label>
                                <select name="id_property_classification" id="id_property_classification" class="form-control" <?= $attrInputs ?>>
                                    <option value="0" <?= isset($product->id_property_classification) && $product->id_property_classification == 0 ? "selected" : "" ?>>Sem Classificação</option>
                                    <?php foreach ($propertyClassification as $classification) { ?>
                                        <option value="<?= $classification->id ?>" <?= isset($product->id_property_classification) && $product->id_property_classification == $classification->id ? "selected" : "" ?>><?= $classification->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="uf_state">Estado <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <select name="uf_state" id="state" class="form-control" <?= $attrInputsRequired ?>>
                                    <?php foreach ($states as $state) { ?>
                                        <option value="<?= $state->uf ?>" <?= $state->uf == $product->uf_state ? "selected" : "" ?>><?= $state->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="id_city">Cidade <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <select name="id_city" id="id_city" class="form-control" <?= $attrInputsRequired ?>>
                                    <?php foreach ($cities as $city) { ?>
                                        <option value="<?= $city->id ?>" <?= $city->id == $product->id_city ? "selected" : "" ?>><?= $city->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="neighborhood">Bairro <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" value="<?= $product->neighborhood ?>" type="text" class="form-control" id="neighborhood" name="neighborhood" <?= $attrInputsRequired ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="allotment">Loteamento</label>
                                <input autocomplete="off" value="<?= $product->allotment ?>" type="text" class="form-control" id="allotment" name="allotment" <?= $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="address">Endereço <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" value="<?= $product->address ?>" type="text" class="form-control" id="address" name="address" <?= $attrInputsRequired ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="number">Nº <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                <input autocomplete="off" value="<?= $product->number ?>" type="text" class="form-control" id="number" name="number" <?= $attrInputsRequired ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="complement">Complemento </label>
                                <input autocomplete="off" value="<?= $product->complement ?>" type="text" class="form-control" id="complement" name="complement" <?= $attrInputs ?>>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="total_area">Área Total (m²) <span class="text-danger"></span></label>
                                <input autocomplete="off" value="<?= $product->total_area ?>" type="text" onkeyup="onlyNumbers(this)" maxlength="8" class="form-control" id="total_area" name="total_area" <?= $attrInputs ?>>
                            </div>
                        </div>
                        <?php if ($permission) { ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="re_registered_at">Data de Recadastro <span class="text-danger">*</span></label>
                                    <span class="pull-right">
                                        <button type="button" style="margin-right: 3px;" class="btn btn-xs btn-reCreated-at">Recadastrar</button>
                                        <button type="button" id="open-log-re_registered_at" aria-hidden="true" class="btn btn-xs" <?= $attrInputs ?>>Log</button>
                                    </span>
                                    <input autocomplete="off" type="date" class="form-control" id="re_registered_at" name="re_registered_at" value="<?= $product->re_registered_at ?>" disabled>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="value">Valor à Vista <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                        <input autocomplete="off" value="<?= Util::maskMoney($product->value) ?>" type="text" class="form-control" id="value" name="value" data-mask-money <?= $attrInputsRequired ?>>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="installment_value">Valor Parcelado <span class="text-danger"><?= $textLabelRequired ?></span></label>
                                        <input autocomplete="off" value="<?= Util::maskMoney($product->installment_value) ?>" type="text" class="form-control" id="installment_value" name="installment_value" data-mask-money <?= $attrInputsRequired ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9 col-lg-9">
                            <div class="form-group">
                                <label for="condition_product">Condição </label>
                                <textarea name="condition_product" id="condition_product" class="form-control" rows="5" <?= $attrInputs ?>><?= $product->condition_product ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Características do Imóvel</h3>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="box-body">
                    <div class="row">
                        <div id="editImmovableResource"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="realEstate">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Descrição do Imóvel</h3>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12 ">
                                <textarea class="box-ckeditor" name="description" id="description" <?= $attrInputs ?>><?= $product->description ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Configurações e Informações do Cadastro</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <?php if (Secure::access_secretary()) { ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Alterar Proprietário do Cadastro</label>
                            <select name="id_user_owner" id="id_user_owner" class="form-control">
                                <?php
                                if (empty($product->id_user_owner)) {
                                    echo "<option value=''>Selecione</option>";
                                }
                                $aux = "";
                                $auxProfile = "";
                                foreach ($users as $user) {
                                    $auxProfile = $auxProfile != $user->profile_name ? $user->profile_name : $auxProfile;
                                    if ($auxProfile != $aux) {
                                ?>
                                        <optgroup label="<?= $auxProfile ?>">
                                        <?php
                                    } ?>
                                        <option value="<?= $user->id ?>" <?= $user->id == $product->id_user_owner ? "selected" : "" ?>><?= $user->name ?></option>
                                        <?php if ($auxProfile != $aux) {
                                            $aux = $auxProfile;
                                        ?>
                                        </optgroup>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                <?php }  ?>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" <?= $attrInputs ?>>
                            <option value="1" <?= $product->status == "1" ? "selected" : "" ?>>Ativo</option>
                            <option value="0" <?= $product->status == "0" ? "selected" : "" ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_branch">Exibir nas filiais</label>
                        <div class="form-group">
                            <select name="id_branch[]" id="id_branch" class="form-control" multiple <?= $attrInputs ?>>
                                <?php foreach ($allBranches as $branch) { ?>
                                    <option value="<?= $branch->id ?>" <?= in_array($branch->id, $product->branches) ? "selected" : "" ?>><?= $branch->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <h5>Criador por: <strong><?= $userCreated->name ?></strong></h5>
                        <h5>Data da criação: <strong><?= Date::date($product->created_at) ?></strong></h5>
                    </div>
                </div>
                <?php if ($userUpdated) { ?>
                    <div class="col-md-6">
                        <div class="form-group pull-right" style="margin-bottom: 0px;">
                            <h5>Atualizado por: <strong><?= $userUpdated->name ?></strong> </h5>
                            <h5>Data da atualização: <strong><?= Date::date($product->updated_at) ?></strong></h5>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="box-footer">
        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
        <div class="pull-right">
            <?php if ($permission) { ?>
                <button type="submit" class="btn btn-block btn-primary" id="btn-submit-edit-products">Salvar</button>
            <?php } ?>
        </div>
    </div>
</form>

</div>
</section>
</div>

<!-- modals -->

<div id="generic-message-modal" class="modal fade" tabindex="-1" role="dialog">
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

<div class="modal fade" id="owner" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Clientes Proprietário</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="page-owner-ajax" value="1">
                    <div class="col-md-4 col-lg-4">
                        <label>Nome</label>
                        <div class="form-group">
                            <input type="text" autocomplete="off" class="form-control" placeholder="Nome do Cliente Proprietário" name="name_search" id="search_name_owner" value="">
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-8" style="margin-top:25px;">
                        <button type="button" class="btn btn-primary pull-right" name="filter" id="search_owner"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Cód</th>
                                    <th>Nome</th>
                                    <th class="text-center">CPF/CNPJ</th>
                                    <th class="text-center">Localidade</th>
                                    <th class="text-center" style="width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="customer-owner-table"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <ul class="pagination pagination-sm no-margin modal-pagination-owner"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reCreatedAtModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Recadastrar Imóvel</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleSubmitUpdateReCreatedAt/' . $productId ?>" method="POST" id="form-reCreated">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="re_registered_at_modal">Data de Recadastro<span class="text-danger">*</span></label>
                                <input autocomplete="off" type="date" class="form-control" id="re_registered_at_modal" name="re_registered_at_modal" value="<?= $product->re_registered_at ?>" disabled>
                                <input type="hidden" name="re_registered_at" value="<?= $product->re_registered_at ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_re_registered_at">Nova Data de Recadastro<span class="text-danger">*</span></label>
                                <input autocomplete="off" type="date" class="form-control" id="new_re_registered_at" name="new_re_registered_at" value="<?= $newReCreatedAt ?>" <?= $_SESSION['RR']->profile->access <= 10 || $product->created_by == $_SESSION['RR']->user->id ? "" : "disabled" ?>>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-right" id="btn-submit-products-modal">Salvar</button>
                </div>
            </form>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="log-reCreated-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Log de Cadastros</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 ">
                        <?php if ($logsReCreatedAt) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Data e hora</th>
                                            <th class="text-center">Usuário</th>
                                            <th class="text-center">Data Recadastro</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logsReCreatedAt as $log) { ?>
                                            <tr>
                                                <td class="text-center"><?= Date::date_hour($log->created_at) ?></td>
                                                <td class="text-center"><?= $log->user ?></td>
                                                <td class="text-center"><?= Date::date($log->new_re_registered_at) ?></td>
                                                <td class="text-center">
                                                    <?php if ($log->id_authorization_contract != null) { ?>
                                                        <a href="<?= URL . 'print/contract/' . $log->code ?>" title="Contrato vinculado" class="btn btn-sm btn-warning"><i class="fas fa-file-signature"></i></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
