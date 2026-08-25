<?php

use RR\libs\Secure;
use RR\libs\Util;

?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= isset($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name ?></a>
            <small>
                <?php
                switch ($_GET['pg1']) {
                    case 'editItem':
                        echo 'Dados Gerais';
                        break;
                    case 'image':
                        echo 'Imagem';
                        break;
                    case 'spouse':
                        echo 'Cônjuge';
                        break;
                    case 'billToPay':
                        echo 'Contas a Pagar';
                        break;
                    case 'attachment':
                        echo 'Anexos';
                        break;
                }
                ?>
            </small>
            <?php if ($_GET['pg1'] == 'property' && in_array(9, $typeSelected) || in_array(11, $typeSelected)) { ?>
                <div class="pull-right">
                    <a class="btn btn-sm btn-info" href="<?= URL . '/property/addItem/' . $customer->id ?>">Adicionar Imóvel</a>
                </div>
            <?php } ?>
        </h1>
    </section>
    <section class="content">
        <?= $this->alert->defaultItemAlerts(); ?>
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?= ($_GET['pg1'] == 'editItem') ? "active" : "" ?>">
                    <a href="<?= URL . $this->route . "/edit-item/" . $customer->id; ?>">Dados Gerais</a>
                </li>
                <?php if ($menuSpouse) { ?>
                    <li class="<?= ($_GET['pg1'] == 'spouse') ? "active" : "" ?>">
                        <a href="<?= URL . $this->route . "/spouse/" . $customer->id; ?>">Cônjuge</a>
                    </li>
                <?php } ?>
                <?php if ($menuConstructorImage) { ?>
                    <li class="<?= ($_GET['pg1'] == 'image') ? "active" : "" ?>">
                        <a href="<?= URL . $this->route . "/image/" . $customer->id; ?>">Imagem</a>
                    </li>
                <?php } ?>
                <?php if (Secure::creator($customer->created_by) || Secure::access_secretary()) { ?>
                    <?php if ($menuBillToPay) { ?>
                        <li class="<?= ($_GET['pg1'] == 'billToPay') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/billToPay/" . $customer->id; ?>">Contas a Pagar</a>
                        </li>
                    <?php } ?>
                    <li class="<?= ($_GET['pg1'] == 'attachments') ? "active" : "" ?>">
                        <a href="<?= URL . $this->route . "/attachment/" . $customer->id; ?>">Anexos</a>
                    </li>
                <?php } ?>
            </ul>
            <?php if (isset($_GET['pg1']) && $_GET['pg1'] == 'property' && $menuConstructorImage) { ?>
                <div class="box-body">
                    <div class="column">
                        <div class="img-logo">
                            <img src="<?= $customer->logo ? URL . "img/customer/{$customerId}/logo-{$customer->logo_cont}.{$customer->logo_ext}" : URL . "img/customer/default/default.png" ?>" style="max-width: 150px; max-height: 150px; border-radius: 3px;" id="onloadImageLogo">
                        </div>
                        <div class="text-logo">
                            <h3 style="margin-top: 0px;"><?= $customer->fancy_name_company ?></h3>
                            <h5><b>Proprietário: </b> <?= $customer->name ?></h5>
                            <h5><b>E-mail: </b><?= $customer->email ? $customer->email : '' ?></h5>
                            <h5><b>Telefone: </b><?= $customer->phone ? "<span phone>{$customer->phone}</span>" : '' ?></h5>
                            <h5><b>Celular: </b><?= $customer->cellphone ? "<span cellphone>{$customer->cellphone}</span>" : '' ?></h5>
                        </div>
                        <div class="text-logo">
                            <h3 style="margin-top: 0px;">Endereço</h3>
                            <h5><b>Pais: </b><?= $customer->country_name ? $customer->country_name : '' ?></h5>
                            <h5><b>Estado: </b><?= $customer->state_name ? $customer->state_name . ' - ' . $customer->uf : '' ?></h5>
                            <h5><b>Cidade: </b><?= $customer->state_name ? $customer->city_name : '' ?></h5>
                            <h5><b>Bairro: </b><?= $customer->neighborhood ? $customer->neighborhood : '' ?></h5>
                            <h5><b>Rua & Nº: </b><?= $customer->address ? $customer->address : '' ?> - <?= $customer->number_address ? $customer->number_address : '' ?></h5>
                        </div>
                    </div>
                </div>
            <?php } ?>