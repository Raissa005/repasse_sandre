<form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
    <div class="content-wrapper">
        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Cadastrar Imóvel</h3>
                        </div>
                        <div class="box-body">
                            <input type="hidden" id="lat" name="lat" value="">
                            <input type="hidden" id="lng" name="lng" value="">
                            <input type="hidden" id="immovable_record" value="<?= $branch->immovable_record ?>">
                            <div class="row">
                                <div class="realEstate">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="id_customer_owner">Buscar Proprietário</label>
                                            <button type="button" class="btn btn-primary btn-block" id="modal-owner" data-toggle="modal" data-target="#owner"><i class="fas fa-user-tag"></i> Buscar Proprietário</button>
                                        </div>
                                    </div>
                                    <input type="hidden" id="id_owner" name="id_owner" value="<?= !empty($_GET['pg2']) ? $customer->id : "" ?>">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="name_owner">Nome do Proprietário</label>
                                            <input autocomplete="off" type="text" class="form-control" id="name_owner" name="name_owner" value="<?= !empty($_GET['pg2']) ? $customer->option_name : "" ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="cpf_owner">CPF/CNPJ do Proprietário</label>
                                            <input autocomplete="off" type="text" class="form-control" id="cpf_owner" name="cpf_owner" value="<?= !empty($_GET['pg2']) ? $customer->cpf : "" ?>" cpfcnpj disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="id_city_owner">Cidade - UF</label>
                                            <input autocomplete="off" type="text" class="form-control" id="city_owner" name="city_owner" value="<?= !empty($_GET['pg2']) ? $customer->cities_name : "" ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cod">Código</label>
                                        <input autocomplete="off" type="text" class="form-control" id="cod" name="cod">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Nome do Imóvel <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="id_residential_type">Tipo Imóvel<span class="text-danger">*</span></label>
                                        <select name="id_residential_type" id="id_residential_type" class="form-control" required>
                                            <?php foreach ($propertyTypes as $type) { ?>
                                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="propertyCategory">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_property_category">Categoria Imóvel<span class="text-danger">*</span></label>
                                            <select name="id_property_category" id="id_property_category" class="form-control">
                                                <?php foreach ($propertyCategories as $category) { ?>
                                                    <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="id_property_classification">Classificação Imóvel</label>
                                        <select name="id_property_classification" id="id_property_classification" class="form-control">
                                            <option value="0">Sem Classificação</option>
                                            <?php foreach ($propertyClassification as $classification) { ?>
                                                <option value="<?= $classification->id ?>"><?= $classification->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="uf_state">Estado <span class="text-danger">*</span></label>
                                        <select name="uf_state" id="state" class="form-control" required>
                                            <?php foreach ($states as $state) { ?>
                                                <option value="<?= $state->uf ?>" <?= $state->uf == $city->uf ? "selected" : ""; ?>><?= $state->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_city">Cidade<span class="text-danger">*</span></label>
                                        <select name="id_city" id="id_city" class="form-control" required>
                                            <?php foreach ($cities as $city) { ?>
                                                <option value="<?= $city->id ?>" <?= $city->id == $branch->id_city ? "selected" : "" ?>><?= $city->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="neighborhood">Bairro<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="neighborhood" name="neighborhood" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="allotment">Loteamento</label>
                                        <input autocomplete="off" type="text" class="form-control" id="allotment" name="allotment">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="address">Endereço<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="address" name="address" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="number">Nº<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" class="form-control" id="number" name="number" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="complement">Complemento</label>
                                        <input autocomplete="off" type="text" class="form-control" id="complement" name="complement">
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="value">Valor à Vista<span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="value" name="value" required data-mask-money>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="installment_value">Valor Parcelado<span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="installment_value" name="installment_value" required data-mask-money>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="created_at">Data de Cadastro<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="date" class="form-control created_at" id="created_at" name="created_at" value="<?= date("Y-m-d") ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="re_registered_at">Data de Recadastro<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="date" class="form-control" id="re_registered_at" name="re_registered_at" value="<?= date("Y-m-d", strtotime("+" . $this->branch->immovable_record . " months")) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="total_area">Área Total (m²)<span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" onkeyup="onlyNumbers(this)" maxlength="10" class="form-control" id="total_area" name="total_area">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="id_branch">Filiais</label>
                                        <i class="far fa-question-circle pull-right" data-toggle="popover" data-html="true" data-placement="left" data-original-title="Caso vazio, o imóvel será colocado a filial cadastrada:" data-content="<strong><?= $branch->name ?></strong>">
                                        </i>
                                        <select name="id_branch[]" id="id_branch" class="form-control" multiple>
                                            <?php foreach ($branchs as $brch) { ?>
                                                <option value="<?= $brch->id ?>" selected><?= $brch->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="condition_product">Condição</label>
                                                <textarea autocomplete="off" name="condition_product" id="condition_product" class="form-control" rows="5"></textarea>
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
                            <div class="box-body">

                                <div class="row">
                                    <div id="immovableResource"></div>
                                </div>
                            </div>
                        </div>
                        <div class="realEstate">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Descrição do Imóvel</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <textarea id="box-ckeditor" name="description"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" id="btn-submit-products" class="btn btn-block btn-primary">Cadastrar</button>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</form>

<!-- Modals -->

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
