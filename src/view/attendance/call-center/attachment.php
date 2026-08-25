<?php

use RR\libs\Secure;

?>
<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title" style="margin-top: 4px;">Anexos</h3>
        <button class="btn btn-warning btn-sm pull-right" data-toggle="modal" data-target="#<?= "addAttachment" ?>">Adicionar</button>
    </div>
    <?php if (isset($attachments) && !empty($attachments)) { ?>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed" style="margin-bottom: 0px;">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attachments as $attachment) { ?>
                                    <tr>
                                        <td><?= $attachment->name ?></td>
                                        <td>
                                            <div class="text-center">
                                                <a href="<?= URL . "attachments/attendance/$attachment->id_attendance/$attachment->filename.$attachment->extension" ?>" title="Donwload do Anexo" class="btn btn-warning btn-sm" download=""><i class="fas fa-file-download"></i></a>
                                                <?php if (Secure::access_admin()) { ?>
                                                    <a class="btn btn-danger btn-sm" href="<?= URL . $this->route . "/handleSubmitDeleteAttachment/$attachment->id" ?>"><i class="fas fa-trash-alt"></i></a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
</div>

<!-- Modals -->

<div class="modal fade" id="addAttachment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Adicionar Anexos</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form enctype="multipart/form-data" role="form" action="<?= URL . $this->route . '/handleSubmitAddAttachments/' . $attendanceId ?>" method="POST" id="form-comment">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="name">Nome Anexo <span class="text-danger">*</span></label>
                                <input autocomplete="off" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <label for="attachment" class="btn btn-warning btn-block" style="margin-top: 25px;">
                                <i class="fas fa-file-upload"></i> Escolha um Anexo
                            </label>
                            <input type="file" name="attachment[]" id="attachment" style="display: none;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>
