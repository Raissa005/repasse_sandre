<?php

use RR\libs\Secure;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <form action="<?= URL . "{$this->route}/handleSubmitEditItem/$itemId" ?>" method="POST">
                        <div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="realEstate">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="sellerCustomerId">Buscar Cliente</label>
                                                <button type="button" class="btn btn-primary btn-block" id="modalSellerCustomer" data-toggle="modal" data-target="#sellerCustomer" ><i class="fas fa-user-tag"></i> Cliente / Vendedor</button>
                                            </div>
                                        </div>
                                        <input type="hidden" id="sellerCustomerId" name="sellerCustomerId" value="<?= !empty($_GET['pg2']) ? $customer->id : "" ?>">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nameSellerCustomer">Nome Cliente / Vendedor</label>
                                                <?php if (isset($customer->id) && $restrictDataValidation) { ?>
                                                    <span class="pull-right">
                                                        <a href="<?= URL . "customer/editItem/" . $customer->id ?>" target="_blank" class="btn btn-xs">Ver Cliente</a>
                                                    </span>
                                                <?php } ?>
                                                <input autocomplete="off" type="text" class="form-control" id="nameSellerCustomer" name="nameSellerCustomer" value="<?= isset($customer->id) && $restrictDataValidation ? (isset($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name) : "RESTRITO" ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="cpfSellerCustomer">CPF/CNPJ Cliente / Vendedor</label>
                                                <input autocomplete="off" type="text" class="form-control" id="cpfSellerCustomer" name="cpfSellerCustomer" value="<?= $restrictDataValidation ? (isset($customer->cnpj) ? $customer->cnpj : (isset($customer->person_registration) ? $customer->person_registration : '')) : 'RESTRITO' ?>" <?= $restrictDataValidation ? 'cpfcnpj' : '' ?> disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="citySellerCustomerId">Cidade - UF</label>
                                                <input autocomplete="off" type="text" class="form-control" id="citySellerCustomer" name="citySellerCustomer" value="<?= isset($customer->city_name) && isset($customer->uf) ? $customer->city_name . " - " . $customer->uf : "" ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="purchaseDate">Data Pedido</label>
                                            <input type="date" class="form-control" name="purchaseDate" id="purchaseDate" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="purchaseStatus">Status Pedido</label>
                                            <select name="purchaseStatus" id="purchaseStatus">
                                                <option value="1">Aberto</option>
                                                <option value="">Fechado</option>
                                            </select>
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
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
