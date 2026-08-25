<?php

use RR\libs\Secure;
use RR\libs\Util;
?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title" style="margin-top: 4px;">Telefone</h3>
        <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#addPhone">Adicionar</button>
    </div>
    <?php if (isset($phones) && !empty($phones)) { ?>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed" style="margin-bottom: 0px;">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($phones as $phone) { ?>
                                    <tr>
                                        <td><?= ucwords(mb_strtolower($phone->name), " ") ?></td>
                                        <td class="pull-right">
                                            <a class="btn btn-primary btn-sm" style="margin-bottom: 3px;" href="tel:<?= $phone->phone ?>"><i class="fas fa-phone-alt"></i> <?= Util::maskTelefone($phone->phone) ?></a>
                                            <?php if ($phone->whatsapp == '1') { ?>
                                                <a class="btn btn-sm" style="background-color: #25D366; color: #fff; margin-bottom: 3px;" target="_black" href="https://api.whatsapp.com/send/?phone=55<?= $phone->phone ?>"><i class="fab fa-whatsapp"></i></a>
                                            <?php }
                                            if (Secure::access_admin()) { ?>
                                                <button type="button" class="btn btn-danger btn-sm btn-disable-item" bodyHtml="Deseja realmente excluir esse Fone?" style="margin-bottom: 3px;" id="<?= $phone->id ?>" sendTo="<?= $this->route . '/handledeletePhone/' ?>"><i class="fas fa-trash-alt"></i></button>
                                            <?php } ?>
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

<!-- Modals -->

<div class="modal fade" id="addPhone" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div style="display: flex; justify-content: center;">
                    <h4 class="modal-title" id="exampleModalCenterTitle">Adicionar Telefone</h4>
                </div>
                <div style="position: absolute; right: 15px; top: 10px;">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form role="form" action="<?= URL . $this->route . '/handleSubmitAddPhone/' . $attendanceId ?>" method="POST" id="form-attendance-phones">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 col-lg-5">
                            <div class="form-group">
                                <label for="name">Nome <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" class="form-control" id="name_phone" name="name_phone" required>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-5">
                            <div class="form-group">
                                <label for="phone">Telefone <span class="text-danger">*</span></label>
                                <input autocomplete="off" type="text" name="phone" id="phone" class="form-control" cellphone required>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label for="whatsapp">
                                    WhatsApp
                                    <input autocomplete="off" type="checkbox" style="width: 2rem; height: 2rem;" name="whatsapp" id="whatsapp">
                                </label>
                            </div>
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
