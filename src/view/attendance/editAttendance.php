<?php

use RR\libs\Secure;

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Editar Atendimento</h3>
                    </div>
                    <form role="form" action="<?= URL . $this->route . "/handleSubmitEditAttendance/$attendanceId" ?>" method="POST">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_customer">Busca Cliente<span class="" style="color: red;">*</span></label>
                                        <select class="form-control" name="id_customer" id="id_customer" required>
                                            <option value="0">NOVO CLIENTE</option>
                                            <?php foreach ($customers as $customer) { ?>
                                                <option value="<?= $customer->id ?>"><?= !empty($customer->fancy_name_company) ? $customer->fancy_name_company : $customer->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="name">Nome Cliente<span class="" style="color: red;">*</span></label>
                                        <input autocomplete="off" type="text" name="name" id="name" class="form-control" value="<?= $attendance->name ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="email">E-mail</label>
                                        <input autocomplete="off" type="email" class="form-control" id="email" name="email" value="<?= $attendance->email ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_city">Cidade</label>
                                        <select name="id_city" id="id_city" class="form-control">
                                            <?php foreach ($cities as $city) { ?>
                                                <option value="<?= $city->id ?>" <?= $attendance->id_city == $city->id ? "selected" : "" ?>><?= $city->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="state">UF</label>
                                        <select name="state" id="state" class="form-control">
                                            <?php foreach ($states as $state) { ?>
                                                <option value="<?= $state->uf ?>" <?= $state->uf == $attendance->state ? "selected" : "" ?>><?= $state->uf ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="opening_date">Data de Abertura<span class="" style="color: red;">*</span></label>
                                        <input type="datetime-local" id="opening_date" name="opening_date" class="form-control" value="<?= date("Y-m-d\TH:i", strtotime($attendance->opening_date)) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label for="id_communication_channel">Como Conheceu<span class="" style="color: red;">*</span></label>
                                        <select name="id_communication_channel" id="id_communication_channel" class="form-control">
                                            <?php foreach ($communicationChannels as $communication_channel) { ?>
                                                <option value="<?= $communication_channel->id ?>" <?= $attendance->id_communication_channel == $communication_channel->id ? "selected" : "" ?>><?= $communication_channel->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if (Secure::access_secretary()) { ?>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="created_by">Vendedor</label>
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
                                                        <option value="<?= $user->id ?>" <?= $attendance->created_by == $user->id ? "selected" : "" ?>><?= $user->name ?></option>
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
                                <div class="col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <label for="description">Descrição</label>
                                        <textarea name="description" id="description" class="form-control" rows="6"><?= htmlspecialchars($attendance->description ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <a type="button" class="btn btn-danger" href="<?= URL . $this->route . "/attendance/$attendanceId" ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>