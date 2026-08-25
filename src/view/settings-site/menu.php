<?php

use RR\libs\BoxAlert;

$alert = (new BoxAlert());
?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-header with-border">
                            <h3 class="box-title">Configurações</h3>
                        </div>
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="<?= (!isset($_GET['pg1'])) ? "active" : "" ?>"><a href="<?= URL . $this->route ?>">Dados Gerais</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'images') ? "active" : "" ?>"><a href="<?= URL . $this->route . "/images/" ?>">Imagens</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'popup') ? "active" : "" ?>"><a href="<?= URL . $this->route . "/popup/1" ?>">Popup</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'emails') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/emails/1' ?>">E-mails</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'metaTags') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/metaTags/1' ?>">Meta Tags</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'color') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/color/1' ?>">Cores</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'script') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/script/1' ?>">Script</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'layout') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/layout/1' ?>">Imóveis</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'home') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/home/1' ?>">Página Inicial</a></li>
                                <li class="<?= (isset($_GET['pg1']) && $_GET['pg1'] == 'filters') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/filters/1' ?>">Filtros</a></li>
                            </ul>