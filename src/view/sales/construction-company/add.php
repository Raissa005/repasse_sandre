<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Venda</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddItem' ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
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
                                        <label for="id_product">Produto</label>
                                        <select name="id_product" id="id_product" class="form-control" required>
                                            <?php foreach ($properties->data as $product) { ?>
                                                <option value="<?= $product->id ?>"><?= !empty($product->cod) ? $product->cod . " - " . $product->name : $product->id . " - " . $product->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label for="sale_value">Valor à vista</label>
                                        <input autocomplete="off" type="text" class="form-control sale_value" disabled data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label for="sale_value">Valor Parcelado</label>
                                        <input autocomplete="off" type="text" class="form-control installment_value" disabled data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label for="sale_value">Valor Negociado</label>
                                        <input autocomplete="off" type="text" class="form-control sale_value" id="sale_value" name="sale_value" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="id_sale_status">Status</label>
                                        <select name="id_sale_status" id="id_sale_status" class="form-control" required>
                                            <?php foreach ($status as $sts) { ?>
                                                <option value="<?= $sts->id ?>"><?= $sts->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="id_standard_proposal">Proposta</label>
                                        <select name="id_standard_proposal" id="id_standard_proposal" class="form-control" required>
                                            <?php foreach ($proposals as $proposal) { ?>
                                                <option value="<?= $proposal->id ?>"><?= $proposal->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="number-installments">Quantidade Parcelas</label>
                                        <input type="number" name="number_installments" class="form-control" id="number-installments" min="1" max="100" step="1" placeholder="1" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="sale_date">Data da Venda</label>
                                        <input autocomplete="off" type="date" value="<?= $today ?>" class="form-control" id="sale_date" name="sale_date" required>
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
                                        <label for="seller_name">Corretor</label>
                                        <input autocomplete="off" type="text" class="form-control" id="seller_name" name="seller_name" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="form-group">
                                        <label for="fgts_select">FGTS</label>
                                        <select name="fgts_select" id="fgts_select" class="form-control">
                                            <option value="0">Não</option>
                                            <option value="1">Sim</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="expense_commission">Comissão</label>
                                        <input type="text" name="expense_commission" class="form-control" id="expense_commission" min="0" placeholder="R$" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="expense_operational_value">Valor Operacional Retido</label>
                                        <input type="text" name="expense_operational_value" class="form-control" id="expense_operational_value" min="0" placeholder="R$" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="after_sale_retained">Pós Venda Retido</label>
                                        <input type="text" name="after_sale_retained" class="form-control" id="after_sale_retained" min="0" placeholder="R$" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="expense_tax">Imposto</label>
                                        <input type="text" name="expense_tax" class="form-control" id="expense_tax" min="0" placeholder="R$" data-mask-money>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="expense_management_commission">Comissão Gerencial</label>
                                        <input type="text" name="expense_management_commission" class="form-control" id="expense_management_commission" min="0" placeholder="R$" data-mask-money>
                                    </div>
                                </div>
                                <div class="fgts">
                                    <div class="col-md-3 ">
                                        <div class="form-group">
                                            <label for="fgts">FGTS Valor</label>
                                            <input type="text" name="fgts" id="fgts" class="form-control" value="" data-mask-money maxlength="14">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="text">Observações</label>
                                        <textarea class="form-control" id="text" name="text" rows="6" cols="80"></textarea>
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