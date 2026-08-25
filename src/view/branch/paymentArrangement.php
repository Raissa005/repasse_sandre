<?php

?>
<form role="form" action="<?= URL . $this->route . '/handleSubmitPaymentArrangement/' . $itemId ?>" method="POST">
    <div class="tab-content">
        <div class="tab-pane active">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr class="info">
                                        <td colspan="3">Valor do Imóvel (Exemplo)</td>
                                        <td><input type="text" id="example-property-value" name="example_property_value" class="form-control" value="<?= $item->example_property_value ?>" data-mask-money></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Porcentagem de Comissão</td>
                                        <td><input autocomplete="off" type="number" id="percentage-commission-sale" class="form-control" name="percentage_commission_sale" value="<?= $item->percentage_commission_sale ?>" step="0.05" placeholder=" 0.1" min="0.0" max="100" data-toggle="popover" data-trigger="focus" data-animation="true" title="Porcentagem Venda do imóvel" data-placement="bottom" data-content="Porcentagem da comissão na venda do imóvel"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">Comissão Bruta</td>
                                        <td class="text-right" id="commission-gross"><?= $arrangement->commission->gross ?></td>
                                    </tr>
                                    <tr class="bg-red no-border">
                                        <td colspan="2">Impostos</td>
                                        <td class="text-center">Porcentagem (%)</td>
                                        <td class="text-right" colspan="2">Valores (R$)</td>
                                    </tr>
                                    <tr class="danger">
                                        <td colspan="2">Impostos Real</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" id="taxes-real" name="percentage_commission_real_rate" class="form-control" value="<?= $item->percentage_commission_real_rate ?>" min="0" max="100" step="0.05" placeholder="0.1">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </td>
                                        <td class="text-danger text-right" colspan="2"><span id="taxes-amount-real"><?= $arrangement->taxes->amount->real ?></span></td>
                                    </tr>
                                    <tr class="danger">
                                        <td colspan="2">Impostos Virtual</td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" id="taxes-virtual" name="percentage_commission_virtual_rate" class="form-control" value="<?= $item->percentage_commission_virtual_rate ?>" min="0" max="100" step="0.05" placeholder="0.1">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </td>
                                        <td class="text-danger text-right" colspan="2"><span id="taxes-amount-virtual"><?= $arrangement->taxes->amount->virtual ?></span></td>
                                    </tr>
                                    <tr class="warning">
                                        <td>Comissão Real</td>
                                        <td class="text-right" colspan="3" id="commission-real"><?= $arrangement->commission->real ?></td>
                                    </tr>
                                    <tr class="warning">
                                        <td>Comissão Virtual</td>
                                        <td class="text-right" colspan="3" id="commission-virtual"><?= $arrangement->commission->virtual ?></td>
                                    </tr>
                                    <tr class="bg-blue">
                                        <td>Envolvidos</td>
                                        <td>Origem</td>
                                        <td class="text-center">Porcentagem (%)</td>
                                        <td class="text-right">Valores (R$)</td>
                                    </tr>
                                    <tr>
                                        <td>Vendedor</td>
                                        <td>
                                            <select name="origin_commission_seller" id="origin-commission-seller" class="form-control">
                                                <?php foreach ($array_origin_percentage as $origin) { ?>
                                                    <option value="<?= $origin->id ?>" <?= $origin->id == $item->origin_commission_seller ? 'selected' : '' ?>><?= $origin->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" min="0" max="100" step="0.05" placeholder="0.1" id="seller-percentage" name="percentage_commission_seller" value="<?= number_format(($arrangement->seller->percentage->calculation), 2) ?>" class="form-control">
                                                <span class="input-group-addon">%</span>
                                            </div>
                                        </td>
                                        <td class="text-right"><span id="seller-amount"><?= $arrangement->seller->amount ?></span></td>
                                    </tr>
                                    <?php foreach ($positions as $position) { ?>
                                        <tr id="position_<?= $position->id ?>" class="position">
                                            <input type="hidden" name="position[<?= $position->id ?>][id]" value="<?= $position->id ?>">
                                            <td><?= $position->name ?></td>
                                            <td>
                                                <select name="position[<?= $position->id ?>][origin_commission]" id="origin-commission" class="form-control">
                                                    <?php foreach ($array_origin_percentage as $origin) { ?>
                                                        <option value="<?= $origin->id ?>" <?= $origin->id == $position->origin_commission ? 'selected' : '' ?>><?= $origin->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" placeholder="0.1" id="percentage-commission" class="form-control" name="position[<?= $position->id ?>][percentage_commission]" value="<?= number_format($position->percentage->calculation, 2) ?>" min="0" max="100" step="0.05">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </td>
                                            <td class="text-right"><span class="amount"><?= $position->amount ?></span></td>
                                        </tr>
                                    <?php } ?>
                                    <tr class="bg-green">
                                        <td colspan="2">Filial</td>
                                        <td class="text-center">Porcentagem (%)</td>
                                        <td class="text-right">Valores (R$)</td>
                                    </tr>
                                    <tr class="success">
                                        <td colspan="2">Comissão Filial Real</td>
                                        <td><span id="branch-percentage-real"><?= number_format($arrangement->branch->percentage->real, 2) ?></span><i>% porcentagem sobre a comissão real</i></td>
                                        <td class="text-right"><span id="branch-amount-real"><?= $arrangement->branch->amount->real ?></span></td>
                                    </tr>
                                    <tr class="success">
                                        <td colspan="2">Comissão Filial Virtual</td>
                                        <td><span id="branch-percentage-virtual"><?= number_format($arrangement->branch->percentage->virtual, 2) ?></span><i>% porcentagem sobre a comissão virtual</i></td>
                                        <td class="text-right"><span id="branch-amount-virtual"><?= $arrangement->branch->amount->virtual ?></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer" style="padding: auto 0px 0px 0px;">
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