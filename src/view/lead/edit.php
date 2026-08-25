<?php

use RR\libs\Date;

?>
<form role="form" action="<?= URL . $this->route . '/handleSubmitEditItem/' . $itemId ?>" method="POST">
    <!-- inicial menu  -->
    <div class="tab-content">
        <div class="tab-pane <?= $_GET['pg1'] == 'editItem' ? "active" : "" ?>" id="editItem" style="background-color: white;">

            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="name">Nome </label>
                            <input autocomplete="off" type="text" class="form-control" id="name" name="name" value="<?= $item->name ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input autocomplete="off" type="text" class="form-control" id="phone" name="phone" value="<?= $item->phone ?>" cellphone disabled>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input autocomplete="off" type="email" class="form-control" id="email" name="email" value="<?= $item->email ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="id_communication_channels">Conheceu <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_communication_channels" id="id_communication_channels" disabled>
                                <?php foreach ($communications as $communication) { ?>
                                    <option value="<?= $communication->id ?>" <?= $item->id_communication_channel == $communication->id ? "selected" : "" ?>><?= $communication->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="name">Data Criação </label>
                            <input type="datetime" class="form-control" id="created_at" name="created_at" value="<?= Date::date_hour($item->created_at)  ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="created_by">Selecione o Usuário <span class="text-danger">*</span></label>
                            <select class="form-control" name="created_by" id="created_by">
                                <?php
                                $aux = "";
                                $auxProfile = "";
                                foreach ($users as $user) {
                                    $auxProfile = $auxProfile != $user->profile_name ? $user->profile_name : $auxProfile;
                                    if ($auxProfile != $aux) {
                                ?>
                                        <optgroup label="<?= $auxProfile ?>">
                                        <?php
                                    } ?>
                                        <option value="<?= $user->id ?>" <?= $item->created_by == $user->id ? "selected" : "" ?>><?= $user->name ?></option>
                                        <?php if ($auxProfile != $aux) {
                                            $aux = $auxProfile;
                                        ?>
                                        </optgroup>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="status">Ativo</label>
                            <select class="form-control" name="status" id="status">
                                <option value="1" <?= $item->status == '1' ? "selected" : "" ?>>Ativo</option>
                                <option value="0" <?= $item->status == '0' ? "selected" : "" ?>>Inativo</option>
                            </select>
                        </div>
                    </div>
                    <?php if (!empty($item->id_attendance)) { ?>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <a href="<?= URL . "attendance/attendance/$item->id_attendance" ?>" style="margin-top: 25px;" class="btn btn-block btn-warning"><i class="fa fa-eye"></i> Ver Atendimento</a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label for="message">Mensagem</label></label>
                            <textarea name="message" class="form-control" id="message" rows="6" disabled><?= $item->message ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                <div class="pull-right" style="margin-left: 5px;">
                    <button type="submit" name="salve" class="btn btn-block btn-primary">Salvar</button>
                </div>

                <?php if (empty($item->id_attendance)) { ?>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-block btn-primary">Criar Atendimento</button>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</form>
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