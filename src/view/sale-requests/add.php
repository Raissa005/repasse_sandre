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
                                                <label for="buyerCustomerId">Buscar Cliente</label>
                                                <button type="button" class="btn btn-primary btn-block" id="modalBuyerCustomer" data-toggle="modal" data-target="#buyerCustomer"><i class="fas fa-user-tag"></i> Cliente / Comprador</button>
                                            </div>
                                        </div>
                                        <input type="hidden" id="buyerCustomerId" name="buyerCustomerId" value="<?= !empty($_GET['pg2']) ? $customer->id : "" ?>">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="nameBuyerCustomer">Nome Cliente / Comprador <span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="nameBuyerCustomer" name="nameBuyerCustomer" value="<?= !empty($_GET['pg2']) ? $customer->option_name : "" ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="cpfBuyerCustomer">CPF/CNPJ Cliente / Comprador</label>
                                                <input autocomplete="off" type="text" class="form-control" id="cpfBuyerCustomer" name="cpfBuyerCustomer" value="<?= !empty($_GET['pg2']) ? $customer->cpf : "" ?>" cpfcnpj disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="cityBuyerCustomerId">Cidade - UF Cliente / Comprador</label>
                                                <input autocomplete="off" type="text" class="form-control" id="cityBuyerCustomer" name="cityBuyerCustomer" value="<?= !empty($_GET['pg2']) ? $customer->cities_name : "" ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="saleDate">Data Pedido <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="saleDate" id="saleDate" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="saleStatus">Status Pedido <span class="text-danger">*</span></label>
                                            <select name="saleStatus" id="saleStatus">
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
