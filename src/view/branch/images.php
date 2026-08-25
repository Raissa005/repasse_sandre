<form role="form" action="<?= URL . $this->route . '/handleSubmitImages/' . $itemId ?>" enctype="multipart/form-data" method="POST">
    <div class="tab-content">
        <div class="tab-pane <?= $_GET['pg1'] == 'images' ? "active" : "" ?>" id="editItem" style="background-color: white;">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="logo_menu" class="btn btn-default btn-block" style="text-transform: uppercase;">logo menu <small>(230x50) .png</small> </label>
                            <input type="file" name="logo_menu" accept=".png" id="logo_menu" onchange="readURL(this, 'onloadImageLogoMenu'), onfilename(this, 'spanFilenameLogoMenu');" style="display: none;">
                            <span id="spanFilenameLogoMenu"></span>
                            <?php if ($item->logo_menu_capa) { ?>
                                <button style="margin-top: 5px;" type="button" id="<?= $itemId ?>" class="btn-disable-item btn btn-danger" title="Excluir logo menu" bodyHtml="Deseja realmente excluir a logo menu?" sendTo="<?= $this->route . '/deleteLogoMenu/' ?>">EXCLUIR LOGO MENU</button>
                            <?php } ?>
                        </div>
                        <img src="<?= $item->logo_menu_capa ? URL . "img/branch/{$itemId}/logo_menu-{$item->logo_menu_cont}.{$item->logo_menu_ext}" : "" ?>" style="max-width: 150px; max-height: 150px;" id="onloadImageLogoMenu">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="logo_mini" class="btn btn-default btn-block" style="text-transform: uppercase;">logo mini <small>(50x50) .png</small> </label>
                            <input type="file" name="logo_mini" accept=".png" id="logo_mini" onchange="readURL(this, 'onloadImageLogoMini'), onfilename(this, 'spanFilenameLogoMini');" style="display: none;">
                            <span id="spanFilenameLogoMini"></span>
                            <?php if ($item->logo_mini_capa) { ?>
                                <button style="margin-top: 5px;" type="button" id="<?= $itemId ?>" class="btn-disable-item btn btn-danger" bodyHtml="Deseja realmente excluir a logo mini?" title="Excluir logo mini" sendTo="<?= $this->route . '/deleteLogoMini/' ?>">EXCLUIR LOGO MINI</button>
                            <?php } ?>
                        </div>
                        <img src="<?= $item->logo_mini_capa ? URL . "img/branch/{$itemId}/logo_mini-{$item->logo_mini_cont}.{$item->logo_mini_ext}" : "" ?>" style="max-width: 150px; max-height: 150px;" id="onloadImageLogoMini">
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="logo_rodape" class="btn btn-default btn-block" style="text-transform: uppercase;">logo rodapé <small>(220x100) .png</small> </label>
                            <input type="file" name="logo_rodape" accept=".png" id="logo_rodape" onchange="readURL(this, 'onloadImageLogoRodape'), onfilename(this, 'spanFilenameLogoRodape');" style="display: none;">
                            <span id="spanFilenameLogoRodape"></span>
                            <?php if ($item->logo_rodape_capa) { ?>
                                <button style="margin-top: 5px;" type="button" id="<?= $itemId ?>" class="btn-disable-item btn btn-danger" title="Excluir logo rodapé cartão imóvel" bodyHtml="Deseja realmente excluir a logo rodapé do Cartão do Imóvel?" sendTo="<?= $this->route . '/deleteLogoRodape/' ?>">EXCLUIR LOGO RODAPE CARTÃO IMÓVEL</button>
                            <?php } ?>
                        </div>
                        <img src="<?= $item->logo_rodape_capa ? URL . "img/branch/{$itemId}/logo_rodape-$item->logo_rodape_cont.$item->logo_rodape_ext" : "" ?>" style="max-width: 150px; max-height: 150px;" id="onloadImageLogoRodape">
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                <div class="pull-right">
                    <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</form>

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
            <div class="modal-body">
            </div>
            <form method="POST" class="form-generic-item">
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
            <div class="modal-body">
                Deseja realmente excluir a logo?
            </div>
            <form method="POST" class="form-disable-item">
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
