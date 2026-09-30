<?php

use RR\libs\Util;
?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $item->customer_name ?></h3>
                </div>
                <div class="nav-tabs-custom" style="background-color: transparent !important;">
                    <ul class="nav nav-tabs" style="background-color: white;">
                        <li class="<?= ($_GET['pg1'] == 'entry') ? "active" : "" ?>"><a href="<?= URL . $this->route . "/entry/" . $itemId ?>">Lançamento</a></li>
                        <li class="<?= ($_GET['pg1'] == 'installments') ? "active" : "" ?>"><a href="<?= URL . $this->route . "/installments/" . $itemId ?>">Parcelas</a></li>
                        <li class="pull-right text-success" style="margin: 10px;">Valor pago <span class="text-bold"> <?= Util::maskMoney($item->amount_paid) ?></span></li>
                    </ul>