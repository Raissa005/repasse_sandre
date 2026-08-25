<?php

use RR\libs\Date;
use RR\libs\Secure;
use RR\libs\Util;
?>
<div class="tab-pane <?= $_GET['pg1'] == 'editItem' ? "active" : "" ?>">
    <form role="form" action="<?= URL . $this->route . (!in_array($item->id_sale_status, [3, 9]) ? '/handleSubmitEditItem/' : '/handleSubmitStatus/') . $itemId ?>" method="POST" id="form-sales">
        <input type="hidden" id="id_sale" value="<?= $itemId ?>">
        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_customer">Cliente</label>
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
                        <label for="id_product">Produto</label>
                        <input type="hidden" id="id_product_selected" value="<?= $item->id_product ?>">
                        <select name="id_product" id="id_product_edit" class="form-control" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                            <?php foreach ($properties as $product) { ?>
                                <option value="<?= $product->id ?>" <?= $product->id == $item->id_product ? "selected" : "" ?>>
                                    <?= !empty($product->cod) ? $product->cod . " - " . $product->name : $product->id . " - " . $product->name ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="sale_value">Valor à vista</label>
                        <input autocomplete="off" type="text" value="<?= Util::maskMoney($property->value) ?>" class="form-control sale_value" disabled data-mask-money>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="sale_value">Valor Parcelado</label>
                        <input autocomplete="off" type="text" value="<?= Util::maskMoney($property->installment_value) ?>" class="form-control installment_value" disabled data-mask-money>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="sale_value">Valor Negociado</label>
                        <input autocomplete="off" type="text" value="<?= Util::maskMoney($item->sale_value) ?>" class="form-control sale_value" id="sale_value" name="sale_value" data-mask-money <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_standard_proposal">Proposta</label>
                        <input type="hidden" id="id_proposal_selected" value="<?= $item->id_standard_proposal ?>">
                        <select name="id_standard_proposal" id="id_standard_proposal" class="form-control" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                            <?php foreach ($proposals as $proposal) { ?>
                                <option value="<?= $proposal->id ?>" <?= $proposal->id == $item->id_standard_proposal ? 'selected' : '' ?>><?= $proposal->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="number-installments">Quantidade Parcelas</label>
                        <input type="number" name="number_installments" class="form-control" id="number-installments" min="1" max="100" step="1" placeholder="1" value="<?= $item->number_installments ?>" <?= $attrStatus ?> required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="sale_date">Data da Venda</label>
                        <input autocomplete="off" type="date" value="<?= $item->sale_date ?>" class="form-control" id="sale_date" name="sale_date" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="id_sales_manager_selected">Gerente de Vendas</label>
                        <select name="sales_manager" id="id_sales_manager_selected" class="form-control" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                            <?php foreach ($users as $user) { ?>
                                <option value="<?= $user->id ?>" <?= $user->id == $item->sales_manager ? 'selected' : '' ?>><?= $user->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="seller_name">Corretor</label>
                        <input autocomplete="off" type="text" class="form-control" value="<?= $item->seller_name ?>" id="seller_name" name="seller_name" maxlength="255" <?= $attrStatus ?>>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="fgts_select">FGTS</label>
                        <select name="fgts_select" id="fgts_select" class="form-control" <?= $attrInputsRequired ?> <?= $attrStatus ?>>
                            <option value="0" <?= $item->fgts_select == "0" ? "selected" : "" ?>>Não</option>
                            <option value="1" <?= $item->fgts_select == "1" ? "selected" : "" ?>>Sim</option>
                        </select>
                    </div>
                </div>
                <div class="fgts">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fgts">FGTS Valor</label>
                            <input type="text" name="fgts" id="fgts" class="form-control" value="<?= Util::maskMoney($item->fgts) ?>" data-mask-money maxlength="14" <?= $attrInputs ?> <?= $attrStatus ?>>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="expense_commission">Comissão</label>
                        <input type="text" name="expense_commission" class="form-control" id="expense_commission" min="0" placeholder="R$" data-mask-money value="<?= $item->expense_commission ? Util::maskMoney($item->expense_commission) : '' ?>" <?= $attrStatus ?>>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="expense_operational_value">Valor Operacional</label>
                        <input type="text" name="expense_operational_value" class="form-control" id="expense_operational_value" min="0" placeholder="R$" data-mask-money value="<?= $item->expense_operational_value ? Util::maskMoney($item->expense_operational_value) : '' ?>" <?= $attrStatus ?>>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="after_sale_retained">Pós Venda Retido</label>
                        <input type="text" name="after_sale_retained" class="form-control" id="after_sale_retained" min="0" placeholder="R$" data-mask-money value="<?= $item->after_sale_retained ? Util::maskMoney($item->after_sale_retained) : '' ?>" <?= $attrStatus ?>>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="expense_tax">Imposto</label>
                        <input type="text" name="expense_tax" class="form-control" id="expense_tax" min="0" placeholder="R$" data-mask-money value="<?= $item->expense_tax ? Util::maskMoney($item->expense_tax) : '' ?>" <?= $attrStatus ?>>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="expense_management_commission">Comissão Gerencial</label>
                        <input type="text" name="expense_management_commission" class="form-control" id="expense_management_commission" min="0" placeholder="R$" data-mask-money value="<?= $item->expense_management_commission ? Util::maskMoney($item->expense_management_commission) : '' ?>" <?= $attrStatus ?>>
                    </div>
                </div>
                <?php if (Secure::access_secretary()) { ?>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="blocked">Bloquear Venda</label>
                            <select name="blocked" id="blocked" class="form-control">
                                <option value="0" <?= $item->blocked == 0 ? "selected" : "" ?>>Não Bloqueado</option>
                                <option value="1" <?= $item->blocked == 1 ? "selected" : "" ?>>Bloqueado</option>
                            </select>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="text">Observações</label>
                        <textarea class="form-control" id="text" name="text" rows="6" style="resize: none;" <?= $attrInputs ?> <?= $attrStatus ?>><?= $item->text ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
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
                <a type="button" class="btn btn-info" target="_blank" href="<?= URL . "{$this->route}/printContract/$item->id/2" ?>"><i class="fas fa-print"></i> Imprimir Proposta</a>
                <button type="submit" class="btn btn-primary" <?= $attrInputs ?>>Salvar</button>
            </div>
        </div>
    </form>
</div>

</div>
</section>
</div>
