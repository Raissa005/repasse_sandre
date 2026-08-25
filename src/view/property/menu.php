<?php

use RR\libs\Secure;
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?= !empty($product->cod) ? $product->cod : $product->id ?>
            <small class="text-black" style="font-size: 16px; font-weight: 400;"><?= $product->name ?></small>
            <span class="pull-right">
                <?php if ($product->status == 1 && $product->availability == 1) { ?>
                    <a class="btn btn-sm btn-info" href="<?= URL . '/sales/add-item/' . $product->id ?>">Iniciar Venda</a>
                <?php } ?>
                <a class="label <?= $product->availability ? 'label-light-success text-green' : 'label-light-warning text-yellow' ?>" <?= $product->availability ? '' : 'href="' . URL . 'sales/edit-item/' .  $product->sales_id  . '"' ?> target="_blank">
                    <?= $product->availability ? 'Disponível' : 'Vendido' ?></a>
                <a class="btn <?= $product->site_status ? 'btn-success' : 'btn-default' ?>" title="Site" target="<?= $product->site_status ? '_blank' : '' ?>" href="<?= $product->site_status ? $this->siteConfig->url_global . "imovel/" . $product->url : URL . $this->route . '/site/' . $product->id ?>">
                    <i class="fas fa-globe"></i>
                    Imóvel no Site
                </a>
            </span>
        </h1>
    </section>

    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="nav-tabs-custom" style="background-color: transparent !important;">
                    <ul class="nav nav-tabs" style="background-color: #fff;">
                        <li class="<?= ($_GET['pg1'] == 'editItem') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/editItem/" . $product->id; ?>">
                                Gerais
                            </a>
                        </li>
                        <?php if (isset($immovables) && !empty($immovables)) { ?>
                            <li class="<?= ($_GET['pg1'] == 'immovable-resource') ? "active" : "" ?>">
                                <a href="<?= URL . $this->route . "/immovable-resource/" . $product->id; ?>">
                                    Características
                                </a>
                            </li>
                        <?php } ?>
                        <li class="<?= ($_GET['pg1'] == 'map') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/map/" . $product->id; ?>">
                                Mapa
                            </a>
                        </li>
                        <li class="<?= ($_GET['pg1'] == 'photos') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/photos/" . $product->id; ?>">
                                Galeria
                            </a>
                        </li>
                        <li class="<?= ($_GET['pg1'] == 'progress') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/progress/" . $product->id; ?>">
                                Andamento
                            </a>
                        </li>
                        <li class="<?= ($_GET['pg1'] == 'videos') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/videos/" . $product->id; ?>">
                                Vídeos
                            </a>
                        </li>
                        <li class="<?= ($_GET['pg1'] == 'attachment') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/attachment/" . $product->id; ?>">
                                Anexos
                            </a>
                        </li>
                        <li class="<?= ($_GET['pg1'] == 'priceList') ? "active" : "" ?>">
                            <a href="<?= URL . $this->route . "/priceList/" . $product->id; ?>">
                                Tabela preço
                            </a>
                        </li>
                        <?php if ($this->userSession->permission_publish == 1) { ?>
                            <li class="<?= ($_GET['pg1'] == 'site') ? "active" : "" ?>">
                                <a href="<?= URL . $this->route . "/site/" . $product->id; ?>">
                                    Site
                                </a>
                            </li>
                        <?php } ?>
                        <?php if (Secure::access_secretary() || Secure::creator($product->created_by)) { ?>
                            <li class="<?= ($_GET['pg1'] == 'report') ? "active" : "" ?>">
                                <a href="<?= URL . $this->route . "/report/" . $product->id; ?>">
                                    Relatório
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($sales->count > 0) { ?>
                            <li class="<?= ($_GET['pg1'] == 'sales') ? "active" : "" ?>">
                                <a href="<?= URL . $this->route . "/sales/" . $product->id; ?>">
                                    Vendas
                                </a>
                            </li>
                        <?php } ?>
                    </ul>