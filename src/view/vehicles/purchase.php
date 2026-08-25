<?php

use RR\libs\Util;
use RR\libs\Secure;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<form action="<?= URL . $this->route . "/handleSubmitAddPurchase/$item->id" ?>" method="GET">
    <div class="content-wrapper">
        <?php new ContentHeaderComponent4214($contentHeader) ?>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($navTabs) ?>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="purchaseDate">Data Compra <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="date" class="form-control" id="purchaseDate" name="purchaseDate" <?= !Secure::access_admin() ? 'disabled' : '' ?> value="<?= !empty($purchaseVehicle->purchase_date) ? $purchaseVehicle->purchase_date : '' ?>" require>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="typeNegotiation">Tipo Negociação <span class="text-danger">*</span></label>
                                            <select name="typeNegotiation" id="typeNegotiation" <?= !Secure::access_admin() ? 'disabled' : '' ?> require>
                                                <option value="">Selecione Tipo Negociação...</option>
                                                <?php foreach ($typesNegotiations as $typeNegotiation) { ?>
                                                    <option value="<?= $typeNegotiation->id ?>" <?= !empty($purchaseVehicle->id_type_negotiation) && $typeNegotiation->id == $purchaseVehicle->id_type_negotiation ? 'selected' : '' ?>><?= $typeNegotiation->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="formerOwner">Proprietário Antigo <span class="text-danger">*</span></label>
                                            <?php if (!empty($item->id_former_owner)) { ?>
                                                <span class="pull-right">
                                                    <a href="<?= URL . "customer/editItem/" . $item->id_former_owner ?>" target="_blank" class="btn btn-xs">Ver Proprietário</a>
                                                </span>
                                            <?php } ?>
                                            <select name="formerOwner" id="formerOwner" <?= !Secure::access_admin() ? 'disabled' : '' ?> require>
                                                <option value="">Selecione antigo proprietário...</option>
                                                <?php foreach ($customers as $customer) { ?>
                                                    <option value="<?= $customer->id ?>" <?= !empty($purchaseVehicle->id_former_owner) && $customer->id == $purchaseVehicle->id_former_owner ? 'selected' : '' ?>><?= $customer->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="purchaseValue">Valor Compra <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="text" class="form-control" id="purchaseValue" name="purchaseValue" value="<?= !empty($purchaseVehicle->purchase_value) ? Util::maskMoney($purchaseVehicle->purchase_value) : Util::maskMoney(0) ?>" data-mask-money <?= !Secure::access_admin() ? 'disabled' : '' ?> require>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="vehicleCommission">Valor Comissão <span class="text-danger">*</span></label>
                                            <input autocomplete="off" type="text" class="form-control" id="vehicleCommission"  value="<?= !empty($purchaseVehicle->value_commission) ? Util::maskMoney($purchaseVehicle->value_commission) : 'R$ 0,00' ?>" name="vehicleCommission" placeholder="R$ 0,00" data-mask-money>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="settlementValue">Valor Quitação</label>
                                            <input autocomplete="off" type="text" class="form-control" id="settlementValue" name="settlementValue" value="<?= !empty($purchaseVehicle->settlement_value) ? Util::maskMoney($purchaseVehicle->settlement_value) : Util::maskMoney(0) ?>" data-mask-money <?= !Secure::access_admin() ? 'disabled' : '' ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fipeValue">Valor FIPE</label>
                                            <input autocomplete="off" type="text" class="form-control" id="fipeValue" name="fipeValue" value="<?= !empty($purchaseVehicle->fipe_value) ? Util::maskMoney($purchaseVehicle->fipe_value) : Util::maskMoney(0) ?>" data-mask-money <?= !Secure::access_admin() ? 'disabled' : '' ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="uf_state">Estado <span class="text-danger">*</span></label>
                                            <select name="uf_state" id="state" class="form-control" <?= !Secure::access_admin() ? 'disabled' : '' ?> required>
                                                <?php foreach ($states as $state) { ?>
                                                    <option value="<?= $state->uf ?>" <?= !empty($purchaseVehicle->uf_state) && $state->uf == $purchaseVehicle->uf_state ? 'selected' : '' ?>><?= $state->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="id_city">Cidade <span class="text-danger">*</span></label>
                                            <select name="id_city" id="id_city" class="form-control" <?= !Secure::access_admin() ? 'disabled' : '' ?> required>
                                                <?php foreach ($cities as $city) { ?>
                                                    <option value="<?= $city->id ?>" <?= !empty($purchaseVehicle->id_city) && $city->id == $purchaseVehicle->id_city ? 'selected' : '' ?>><?= $city->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="purchaseBroker">Corretor Compra <span class="text-danger">*</span></label>
                                            <select name="purchaseBroker" id="purchaseBroker" class="form-control" <?= !Secure::access_admin() ? 'disabled' : '' ?> required>
                                                <option value="">Selecione um corretor...</option>
                                                <?php foreach ($purchasingBrokers as $purchasingBroker) { ?>
                                                    <option value="<?= $purchasingBroker->id ?>" <?= !empty($purchaseVehicle->id_purchase_broker) && $purchasingBroker->id == $purchaseVehicle->id_purchase_broker ? 'selected' : '' ?>><?= $purchasingBroker->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>
