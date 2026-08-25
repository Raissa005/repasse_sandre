<?php

use RR\libs\Secure;
?>
<form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
    <div class="content-wrapper">
        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Cadastrar Venda</h3>
                        </div>
                        <input type="hidden" name="due_date" value="<?= date('Y-m-d') ?>">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="sale_date">Data da Venda</label>
                                        <input autocomplete="off" type="date" value="<?= $today ?>" class="form-control" id="sale_date" name="sale_date" required>
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="id_customer">Cliente</label>
                                        <select name="id_customer" id="id_customer" class="form-control" required>
                                            <?php foreach ($customers as $customer) { ?>
                                                <option value="<?= $customer->id ?>"><?= isset($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="id_product">Imóvel</label>
                                        <select name="id_product" id="id_product" class="form-control" required>
                                            <?php foreach ($properties->data as $product) { ?>
                                                <option value="<?= $product->id ?>" <?= !empty($_GET['pg2']) && $_GET['pg2'] == $product->id ? "selected" : "" ?>><?= !empty($product->cod) ? $product->cod . " - " . $product->name : $product->id . " - " . $product->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="sale_value">Valor Negociado</label>
                                        <input autocomplete="off" type="text" class="form-control sale_value" id="sale_value" name="sale_value" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="sale_value">Moeda</label>
                                        <select name="currency" class="form-control currency" id="currency">
                                            <?php foreach ($currencies->data as $currency) { ?>
                                                <option value="<?= $currency->id ?>"><?= $currency->currency_symbol ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 currency_value" hidden>
                                    <div class="form-group">
                                        <label for="currency_value">Valor Moeda</label>
                                        <span class="pull-right">
                                            <a id="updateValue" class="btn btn-xs updateValue">Atualizar Valor</a>
                                        </span>
                                        <input autocomplete="off" type="text" class="form-control currency_value" name="currency_value" id="currency_value" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="percentage-commission">Porcentagem Comissão</label>
                                        <input type="number" name="percentage_commission" class="form-control" id="percentage-commission" min="0" max="100" step="0.05" placeholder="0.1" value="<?= $branch->percentage_commission_sale ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="commission_value">Valor Comissão</label>
                                        <input autocomplete="off" type="text" class="form-control commission_value" name="commission_value" id="commission_value" data-mask-money>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input type="hidden" id="id_attendance" name="id_attendance" value="">
                                        <label for="commission_value">Atendimento Vinculado</label>
                                        <a href=""><input type="text" class="form-control" id="name_attendance" name="name_attendance" readonly></a>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <button type="button" class="btn btn-sm btn-primary" id="modal-attendance" data-toggle="modal" data-target="#attendance"><i class="fas fa-comments"></i> Vincular Atendimento</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="text">Observações</label>
                                        <textarea class="form-control" id="text" name="text" rows="6" style="resize:none"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                                <div class="pull-right">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (Secure::access_admin()) { ?>
                <div class="box box-info">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="number-installments">Quantidade Parcelas</label>
                                            <input type="number" name="number_installments" class="form-control" id="number-installments" min="1" max="100" step="1" placeholder="1" value="1" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id-form-payment">Forma Pagamento</label>
                                            <select name="id_form_payment" id="id-form-payment" class="form-control">
                                                <?php foreach ($form_payment as $form) { ?>
                                                    <option value="<?= $form->id ?>"><?= $form->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="due-date">Data 1º parcela</label>
                                            <input type="date" class="form-control" name="due_date" id="due-date" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label for="sales_manager">Gerente de Vendas</label>
                                            <select name="sales_manager" id="sales_manager" class="form-control" required>
                                                <?php foreach ($users as $user) { ?>
                                                    <option value="<?= $user->id ?>"><?= $user->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_sale_status">Status da Venda</label>
                                            <select name="id_sale_status" id="id_sale_status" class="form-control" required>
                                                <?php foreach ($status as $sts) { ?>
                                                    <option value="<?= $sts->id ?>"><?= $sts->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
    </div>
</form>

<!-- Modals -->
<div class="modal fade" id="attendance" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Atendimentos Concluídos</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="page-attendance-ajax" value="1">
                    <div class="col-md-4 col-lg-4">
                        <label>Nome</label>
                        <div class="form-group">
                            <input type="text" autocomplete="off" class="form-control" placeholder="Nome do Cliente" name="name_search" id="search_name_attendance" value="">
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-8" style="margin-top:25px;">
                        <button type="button" class="btn btn-primary pull-right" name="filter" id="search_attendance"><i class="fa fa-search"></i> Pesquisar</button>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Cód</th>
                                    <th>Nome</th>
                                    <th>Usuário</th>
                                    <th class="text-center" style="width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="attendance-table"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <ul class="pagination pagination-sm no-margin modal-pagination-attendance"></ul>
                </div>
            </div>
        </div>
    </div>
</div>