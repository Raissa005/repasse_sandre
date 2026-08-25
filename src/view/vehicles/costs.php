<?php

use RR\libs\Util;
use RR\libs\Date;
use RR\libs\Secure;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

$total = 0;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div>
                        <div class="row">
                            <form action="<?= URL . $this->route . "/handleSubmitAddObservations/$itemId" ?>" method="POST">
                                <div class="box-body" style="padding-bottom: 0px;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Custos do veículo</h3>
                                                <a id="addCost" data-toggle="modal" data-target="#costs" class="btn btn-info pull-right">Adicionar Custo</a>
                                            </div>
                                            <div class="box-body">
                                                <div class="row">
                                                    <?php if (!empty($vehiclePurchase) || !empty($vehicleCosts->data)) { ?>
                                                        <div class="box-body table table-striped">
                                                            <table class="table table-bordered table-striped">
                                                                <thead>
                                                                    <th>Origem</th>
                                                                    <th class="text-center">Data</th>
                                                                    <th>Fornecedor</th>
                                                                    <th>Usuário</th>
                                                                    <th>Total</th>
                                                                    <th class="text-center">Ações</th>
                                                                </thead>
                                                                <tbody>
                                                                    <?php if (!empty($vehiclePurchase)) { ?>
                                                                        <tr>
                                                                            <td>Compra</td>
                                                                            <td class="text-center"><?= Date::date($vehiclePurchase->purchase_date) ?></td>
                                                                            <td><?= $vehiclePurchase->users_name; ?></td>
                                                                            <td><?= $vehiclePurchase->customerName; ?></td>
                                                                            <td><?= Util::maskMoney($vehiclePurchase->purchase_value) ?></td>
                                                                            <td class="text-center">
                                                                                <?php if (Secure::access_superAdm()) { ?>
                                                                                    <a title="Acessar aba Compra" class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/purchaseVehicles/$item->id" ?>">
                                                                                        <i class="fas fa-file-invoice"></i>
                                                                                    </a>
                                                                                <?php } ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                    <?php if (!empty($vehicleCosts->data)) { ?>
                                                                        <tr>
                                                                            <td width='10%'><a id="toggleCosts"><i class="fa fa-plus-circle"></i></a> Custos</td>
                                                                            <td>&nbsp;</td>
                                                                            <td>&nbsp;</td>
                                                                            <td>&nbsp;</td>
                                                                            <td><?= Util::maskMoney($totalCost) ?></td>
                                                                        </tr>
                                                                        <?php foreach ($vehicleCosts->data as $cost) { ?>
                                                                            <tr id="<?= $cost->id_customer ?>" class="costsDropdown" hidden>
                                                                                <input type="hidden" id="vehicleId" name="vehicleId" value="<?= $itemId ?>">
                                                                                <td>&nbsp;</td>
                                                                                <td class="text-center"><?= Date::date($cost->created_at) ?></td>
                                                                                <td><?= $cost->name ?></td>
                                                                                <td>&nbsp;</td>
                                                                                <td><?= Util::maskMoney($cost->total) ?></td>
                                                                                <td class="text-center">
                                                                                    <a class="btn btn-sm btn-primary btn-costs-list" data-id="<?= $cost->id_customer ?>" <?= !Secure::access_secretary() ? "disabled" : "" ?>><i class="fas fa-file-invoice"></i></a>
                                                                                </td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>