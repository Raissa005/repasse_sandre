<?php

use RR\libs\Util;
?>
<div class="content-wrapper">
    <section class="content container-fluid" style="padding-top: 5px;">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?= Util::titleCase($text->nome) ?></h3>
                        </div>
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="<?= ($_GET['pg1'] == 'editText') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/editText/' . $textId ?>">Editar Texto</a></li>
                                <li class="<?= ($_GET['pg1'] == 'imagesText') ? "active" : "" ?>"><a href="<?= URL . $this->route . '/imagesText/' . $textId ?>">Imagens</a></li>
                            </ul>
