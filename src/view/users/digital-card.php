<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\Util;
?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitDigitalCard/' . $itemId ?>" enctype="multipart/form-data" method="POST">
                        <div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="card_digital_name">Nome <span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="text" id="card_digital_name" name="card_digital_name" class="form-control" value="<?= isset($item->card_digital_name) && !empty($item->card_digital_name) ? $item->card_digital_name : $item->name ?>" placeholder="<?= Util::titleCase($item->name) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="card_digital_occupation">Título Cargo <span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="text" id="card_digital_occupation" name="card_digital_occupation" class="form-control" value="<?= isset($item->card_digital_occupation) && !empty($item->card_digital_occupation) ? $item->card_digital_occupation : "Corretor de Imóveis" ?>" placeholder="Corretor de Imóveis" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="card_digital">Modelo Cartão Digital<span class="text-danger">*</span></label>
                                                <select class="form-control" name="card_digital" id="card_digital">
                                                    <?php foreach ($cardDigital as $card) { ?>
                                                        <option value="<?= $card->id ?>" <?= $item->card_digital == $card->id ? "selected" : "" ?>><?= $card->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-condensed table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Rede</th>
                                                        <th>Link</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="order_list" data-table="user_networks">
                                                    <?php foreach ($networks as $network) { ?>
                                                        <tr id="<?= "item_$network->id" ?>">
                                                            <td><?= $network->name ?></td>
                                                            <td><input autocomplete="off" type="text" name="<?= "link[$network->id]" ?>" id="<?= "link[$network->id]" ?>" class="form-control" <?= $network->id_networks_card_digital == 4 ? "cellphone" : "" ?> value="<?= $network->link ?>"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="profile_picture" class="btn btn-block btn-default" style="text-transform: uppercase; margin-top: 25px;">Imagem Cartão Digital <small>(600x600)</small></label>
                                        <input type="file" name="profile_picture" onchange="readURL(this, 'onloadImage'), onfilename(this, 'spanFilename');" id="profile_picture" style="display: none;">
                                        <span id="spanFilename"></span>
                                    </div>
                                    <?php if ($item->card_digital_capa) { ?>
                                        <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Imagem" id="<?= $itemId ?>" sendTo="<?= $this->route . '/deleteImageDigitalCardId/' ?>">EXCLUIR IMAGEM</button>
                                    <?php } ?>
                                    <div><img src="<?= $item->card_digital_capa ? URL . "img/users/$itemId/$itemId-dc-$item->card_digital_cont.$item->card_digital_ext" : URL . "img/users/default/img-user-default.png" ?>" class="img-circle" style="width: 100%; max-width: 350px; display: flex; margin-left: auto; margin-right: auto;" id="onloadImage"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <?php if (!empty($item->card_digital_occupation) && !empty($item->card_digital_name) && $item->card_digital_capa == true && !empty($item->card_digital)) { ?>
                                    <a href="<?= URL . "cardPDF/$itemId" ?>" class="btn btn-warning" target="_blank"><i class="fas fa-id-badge"></i> Gerar Cartão Digital</a>
                                <?php } ?>
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- modals -->
<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <form method="POST" class="form-generic-item">
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn" id="btn-submit"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-body">Deseja realmente excluir a Imagem?</div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>