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