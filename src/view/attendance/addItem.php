<?php

use RR\libs\Secure;

?><div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cadastrar Atendimento</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . '/handleSubmitAddAttendance' ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_customer">Busca Cliente <span class="text-danger">*</span></label>
                                        <select class="form-control" name="id_customer" id="id_customer" required>
                                            <option value="0">Novo Cliente</option>
                                            <?php foreach ($customers as $customer) { ?>
                                                <option value="<?= $customer->id ?>"><?= !empty($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome Cliente <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" name="name" id="name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="phone">Fone <span class="text-danger">*</span></label>
                                        <input autocomplete="off" type="text" name="phone" id="phone" class="form-control" cellphone required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="whatsapp">
                                            <span style="display: block;"> WhatsApp</span>
                                            <input autocomplete="off" type="checkbox" style="width: 2rem; height: 2rem;" name="whatsapp" id="whatsapp">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="email">E-mail </label>
                                        <input autocomplete="off" type="email" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="state">UF <span class="text-danger">*</span></label>
                                        <select name="state" id="state" class="form-control">
                                            <?php foreach ($states as $state) { ?>
                                                <option value="<?= $state->uf ?>" <?= $state->uf == $city->uf ? "selected" : "" ?>><?= $state->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_city">Cidade <span class="text-danger">*</span></label>
                                        <select name="id_city" id="id_city" class="form-control">
                                            <?php foreach ($cities as $city) { ?>
                                                <option value="<?= $city->id ?>" <?= $city->id == $branch->id_city ? "selected" : "" ?>><?= $city->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="opening_date">Data de Abertura <span class="text-danger">*</span></label>
                                        <input type="datetime-local" id="opening_date" name="opening_date" class="form-control" value="<?= $today ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="return_date">Data de Retorno </label>
                                        <input type="datetime-local" id="return_date" name="return_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_communication_channel">Como Conheceu <span class="text-danger">*</span></label>
                                        <select name="id_communication_channel" id="id_communication_channel" class="form-control">
                                            <?php foreach ($communication_channels as $communication_channel) { ?>
                                                <option value="<?= $communication_channel->id ?>"><?= $communication_channel->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_status">Status Atendimento <span class="text-danger">*</span></label>
                                        <select name="id_status" id="id_status" class="form-control">
                                            <?php foreach ($statusAttendance as $status) { ?>
                                                <option value="<?= $status->id ?>"><?= $status->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if (Secure::access_secretary()) {
                                ?>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_by">Usuário <span class="text-danger">*</span></label>
                                            <select class="form-control" id="created_by" name="created_by">
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
                                                        <option value="<?= $user->id ?>" <?= $_SESSION['RR']->user->id == $user->id ? "selected" : "" ?>><?= $user->name ?></option>
                                                        <?php if ($auxProfile != $aux) {
                                                            $aux = $auxProfile;
                                                        ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-bottom: 0px;">
                                        <label for="description">Descrição</label>
                                        <textarea name="description" id="description" style="resize: none;" class="form-control" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-block btn-primary">Cadastrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- modals -->

<div id="attendance-notice-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                </div>
                <button type="button" class="btn" id="btn-confirm" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>
