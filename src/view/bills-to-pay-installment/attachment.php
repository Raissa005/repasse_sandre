<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\Date;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($nav_tabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <form role="form" enctype="multipart/form-data" action="<?= URL . $this->route . '/handleSubmitAddAttachmentItem/' . $itemId ?>" method="POST">
                        <div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nome Anexo <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" id="name" name="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-3">
                                    <div class="form-group">
                                        <label for="attachmentEntry" class="btn btn-block btn-warning" style="margin-top: 25px;">Escolha o Anexo</label>
                                        <input type="file" id="attachmentEntry" name="attachmentEntry[]" required style="display: none;">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-3">
                                    <div class="form-group" style="margin-top: 25px;">
                                        <button type="submit" class="btn btn-block btn-primary">Adicionar</button>
                                    </div>
                                </div>
                                <?php if (!empty($attachments)) { ?>
                                    <div class="col-xs-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-condensed table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Nome do Anexo</th>
                                                        <th class="text-center">Data de Cadastro</th>
                                                        <th class="text-center">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($attachments as $attachment) { ?>
                                                        <tr>
                                                            <td><?= $attachment->name ?></td>
                                                            <td class="text-center"><?= Date::date_hour($attachment->created_at) ?></td>
                                                            <td class="text-center">
                                                                <a title="Download" href="<?= URL . "attachments/billsToPay/$attachment->id_bills_to_pay_installments/$attachment->filename.$attachment->extension" ?>" download="" class="btn btn-warning"><i class="fas fa-file-download"></i></a>
                                                                <button type="button" id="<?= $attachment->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . "/handleSubmitDeleteAttachmentById/"  ?>"><i class="fas fa-trash-alt"></i></button>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>
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
                Deseja realmente excluir esse anexo?
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