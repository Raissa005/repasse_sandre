<?php

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
                    <form id="formAddItem" action="<?= URL . "{$this->route}/handleSubmitAddItem" ?>" method="POST">
                        <div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="realEstate">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="sellerCustomerId">Buscar Cliente</label>
                                                <button type="button" class="btn btn-primary btn-block" id="modalSellerCustomer" data-toggle="modal" data-target="#sellerCustomer"><i class="fas fa-user-tag"></i> Cliente / Vendedor</button>
                                            </div>
                                        </div>
                                        <input type="hidden" id="sellerCustomerId" name="sellerCustomerId" value="<?= !empty($_GET['pg2']) ? $customer->id : "" ?>">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nameSellerCustomer">Nome Cliente / Vendedor <span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="nameSellerCustomer" name="nameSellerCustomer" value="<?= !empty($_GET['pg2']) ? $customer->option_name : "" ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="cpfSellerCustomer">CPF/CNPJ Cliente / Vendedor</label>
                                                <input autocomplete="off" type="text" class="form-control" id="cpfSellerCustomer" name="cpfSellerCustomer" value="<?= !empty($_GET['pg2']) ? $customer->cpf : "" ?>" cpfcnpj readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="citySellerCustomerId">Cidade - UF Cliente / Vendedor</label>
                                                <input autocomplete="off" type="text" class="form-control" id="citySellerCustomer" name="citySellerCustomer" value="<?= !empty($_GET['pg2']) ? $customer->cities_name : "" ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="purchaseDate">Data Pedido <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="purchaseDate" id="purchaseDate" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="purchaseStatus">Status Pedido <span class="text-danger">*</span></label>
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
    </section>
</div>
