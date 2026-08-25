<div class="tab-pane <?= $_GET['pg1'] == 'image' ? "active" : "" ?>">
<form role="form" action="<?= URL . $this->route . '/handleSubmitImage/' . $customerId ?>" enctype="multipart/form-data" method="POST">
    <div class="tab-content">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="logo" class="btn btn-default btn-block" style="text-transform: uppercase;">logo <small>(150x150) .png</small> </label>
                            <input type="file" name="logo" accept=".png" id="logo" onchange="readURL(this, 'onloadImageLogo'), onfilename(this, 'spanFilenameLogo');" style="display: none;">
                            <span id="spanFilenameLogo"></span>
                            <?php if ($customer->logo) { ?>
                                <button style="margin-top: 5px;" type="button" id="<?= $customerId ?>" class="btn-disable-item btn btn-danger" title="Excluir logo" bodyHtml="Deseja realmente excluir a logo?" sendTo="<?= $this->route . '/deleteLogo/' ?>">EXCLUIR LOGO</button>
                            <?php } ?>
                        </div>
                        <img src="<?= $customer->logo ? URL . "img/customer/{$customerId}/logo-{$customer->logo_cont}.{$customer->logo_ext}" : "" ?>" style="max-width: 150px; max-height: 150px;" id="onloadImageLogo">
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
