<?php

use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;

?>
<form role="form" action="<?= URL . $this->route . (!in_array($item->id_sale_status, [3, 9]) ? '/handleSubmitEditItem/' : '/handleSubmitStatus/') . $itemId ?>" method="POST" id="form-sales">
    <div class="tab-pane <?= $_GET['pg1'] == 'editItem' ? "active" : "" ?>">
        <input type="hidden" id="id_sale" value="<?= $itemId ?>">
        <div class="box-body">
            <?php if ($_SESSION['RR']->profile->access <= 30) { ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sale_date">Data da Venda</label>
                            <input autocomplete="off" type="date" value="<?= $item->sale_date ?>" class="form-control" id="sale_date" name="sale_date" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_customer">Cliente</label>
                            <span class="pull-right">
                                <a href="<?= URL . "customer/editItem/" . $item->id_customer ?>" target="_blank" class="btn btn-xs">Ver Cliente</a>
                            </span>
                            <input type="hidden" id="id_customer_selected" value="<?= $item->id_customer ?>">
                            <select name="id_customer" id="id_customer" class="form-control" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                                <?php foreach ($customers as $customer) { ?>
                                    <option value="<?= $customer->id ?>" <?= $customer->id == $item->id_customer ? "selected" : "" ?>>
                                        <?= isset($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_product">Imóvel</label>
                            <span class="pull-right">
                                <a href="<?= URL . "property/editItem/" . $item->id_product ?>" target="_blank" class="btn btn-xs">Ver Imóvel</a>
                            </span>
                            <input type="hidden" id="id_product_selected" value="<?= $item->id_product ?>">
                            <select name="id_product" id="id_product_edit" class="form-control" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                                <?php foreach ($properties as $property) { ?>
                                    <option value="<?= $property->id ?>" <?= $property->id == $item->id_product ? "selected" : "" ?>>
                                        <?= !empty($property->cod) ? $property->cod . " - " . $property->name : $property->id . " - " . $property->name ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sale_value">Valor Negociado</label>
                            <input autocomplete="off" type="text" value="<?= Util::maskMoney($item->sale_value) ?>" class="form-control sale_value" name="sale_value" id="sale_value" data-mask-money <?= $attrStatus ?>>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="sale_value">Moeda</label>
                            <select name="currency" class="form-control currency" id="currency" <?= $attrStatus ?>>
                                <?php foreach ($currencies->data as $currency) { ?>
                                    <option value="<?= $currency->id ?>" <?= $currency->id == $item->currency_id ? "selected" : "" ?>><?= $currency->currency_symbol ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 currency_value" <?= $currencyValue ?>>
                        <div class="form-group">
                            <label for="currency_value">Valor Moeda</label>
                            <span class="pull-right">
                                <a id="updateValue" class="btn btn-xs updateValue">Atualizar Valor</a>
                            </span>
                            <input autocomplete="off" type="text" value="<?= Util::maskCurrency($item->currency_value, $item->currency_id) ?>" class="form-control currency_value" name="currency_value" id="currency_value" <?= $attrStatus ?> readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="percentage-commission">Porcentagem Comissão</label>
                            <input type="number" name="percentage_commission" class="form-control" id="percentage-commission" min="0" max="100" step="0.01" placeholder="0.1" value="<?= $item->percentage_commission ?>" <?= $attrStatus ?>>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="commission_value">Valor Comissão</label>
                            <input autocomplete="off" type="text" value="<?= Util::maskMoney($item->commission_value) ?>" class="form-control commission_value" id="commission_value" name="commission_value" <?= $attrStatus ?> data-mask-money>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="hidden" id="id_attendance" name="id_attendance" value="<?= $attendance->id ?? "" ?>">
                            <label for="commission_value">Atendimento Vinculado</label>
                            <?php if (!empty($attendance->id)) { ?>
                                <span class="pull-right">
                                    <a href="<?= URL . "attendance/attendance/" . $attendance->id ?>" target="_blank" class="btn btn-xs">Ver Atendimento</a>
                                </span>
                            <?php } ?>
                            <input type="text" class="form-control" id="name_attendance" name="name_attendance" value="<?= !empty($attendance->id) ? $attendance->id . " - " . $attendance->name : "Sem atendimento Vinculado" ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group" style="margin-top: 25px;">
                            <button type="button" class="btn btn-sm btn-primary" id="modal-attendance" data-toggle="modal" data-target="#attendance"><i class="fas fa-comments"></i> Vincular Atendimento</button>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="text">Observações</label>
                        <textarea class="form-control" id="text" name="text" rows="6" style="resize: none;" <?= $attrInputs ?> <?= $attrStatus ?>><?= $item->text ?></textarea>
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
                                    <input type="number" name="number_installments" class="form-control" id="number-installments" min="1" max="100" step="1" placeholder="1" value="<?= $item->number_installments ?>" <?= $attrStatus ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id-form-payment">Forma Pagamento</label>
                                    <select name="id_form_payment" id="id-form-payment" class="form-control" <?= $attrStatus ?>>
                                        <?php foreach ($form_payment as $form) { ?>
                                            <option value="<?= $form->id ?>" <?= $item->id_form_payment == $form->id ? 'selected' : '' ?>><?= $form->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="due-date">Data 1º parcela</label>
                                    <input type="date" class="form-control" name="due_date" id="due-date" value="<?= $item->seller_payment_date ?? date('Y-m-d') ?>" <?= $attrStatus ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Configurações e Informações do Cadastro</h3>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="box-body">
                    <div class="row">
                        <?php if (Secure::access_admin()) { ?>
                            <div class="col-md-3 ">
                                <div class="form-group">
                                    <label for="created_by">Alterar Criador</label>
                                    <select name="created_by" id="created_by" class="form-control" <?= $attrStatus ?>>
                                        <?php foreach ($users as $user) { ?>
                                            <option value="<?= $user->id ?>" <?= $user->id == $item->created_by ? 'selected' : '' ?>><?= $user->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 ">
                                <div class="form-group">
                                    <label for="sales_manager">Gerente de Vendas</label>
                                    <select name="sales_manager" id="sales_manager" class="form-control" <?= $attrStatus ?>>
                                        <?php foreach ($seller_managers as $manager) { ?>
                                            <option value="<?= $manager->id ?>" <?= $manager->id == $item->sales_manager ? 'selected' : '' ?>><?= $manager->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="blocked">Bloquear Venda</label>
                                    <select name="blocked" id="blocked" class="form-control" <?= $attrStatus ?>>
                                        <option value="0" <?= $item->blocked == 0 ? "selected" : "" ?>>Não Bloqueado</option>
                                        <option value="1" <?= $item->blocked == 1 ? "selected" : "" ?>>Bloqueado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="id_sale_status">Status</label>
                                    <select name="id_sale_status" id="id_sale_status" class="form-control" <?= $attrInputsRequired ?>>
                                        <?php foreach ($status as $sts) { ?>
                                            <option value="<?= $sts->id ?>" <?= $sts->id == $item->id_sale_status ? "selected" : "" ?>><?= $sts->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <h5>Criador por: <strong><?= $userCreatedBy->name ?></strong></h5>
                                <h5>Data da criação: <strong><?= Date::date($item->created_at) ?></strong></h5>
                            </div>
                        </div>
                        <?php if ($userUpdatedBy) { ?>
                            <div class="col-md-6">
                                <div class="form-group pull-right">
                                    <h5>Atualizado por: <strong><?= $userUpdatedBy->name ?></strong></h5>
                                    <h5>Data da atualização: <strong><?= Date::date($item->updated_at) ?></strong></h5>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary" <?= $attrInputs ?>>Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

</section>
</div>

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