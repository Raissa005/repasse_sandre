<?php

use RR\libs\BoxAlert;
use RR\libs\Secure;

$alert = (new BoxAlert());
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= 'Atendimento' ?></a>
            <small><?= 'Kanban' ?></small>
            <div class="pull-right">
                <a class="btn btn-sm btn-info" href="<?= URL . $this->route . '/addItem' ?>"><?= 'Adicionar' ?></a>
            </div>
        </h1>
    </section>

    <section class="content container-fluid">
        <?php $alert->defaultItemAlerts(); ?>

        <form action="<?= URL . $this->route ?>" method="GET">
            <input type="hidden" name="b" value="s">
            <div class="box box-info <?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['b']) && $_GET['b'] == 's' ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button> -->
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET['b']) && $_GET['b'] == 's' ? '' : 'display: none;' ?>">
                <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Nome Cliente</label>
                                <input type="text" class="form-control" placeholder="Nome" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                            </div>
                        </div>
                        <?php if (Secure::access_admin() || Secure::access_manager()) { ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="created_by">Usuário</label>
                                    <select class="form-control" id="created_by" name="created_by">
                                        <option value="">Todos</option>
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
                                                <option value="<?= $user->id ?>" <?= isset($_GET['created_by']) && $_GET['created_by'] == $user->id ? "selected" : "" ?>><?= $user->name ?></option>
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
                        <div class="col-md-3">
                            <label>Agendados</label>
                            <div class="form-group">
                                <select class="form-control" name="with_return_date">
                                    <option value="">Todos</option>
                                    <option value="1" <?= (isset($_GET['with_return_date']) && $_GET['with_return_date'] == 1 ? "selected='selected'" : '') ?>>Agendados</option>
                                    <option value="2" <?= (isset($_GET['with_return_date']) && $_GET['with_return_date'] == 2 ? "selected='selected'" : '') ?>>Não agendados</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Status</label>
                            <div class="form-group">
                                <select class="form-control" name="status">
                                    <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                    <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Tipo Data</label>
                            <div class="form-group">
                                <select class="form-control" name="date_type">
                                    <option value="1" <?= (isset($_GET['date_type']) && $_GET['date_type'] == 1 ? "selected='selected'" : '') ?>>Abertura</option>
                                    <option value="2" <?= (isset($_GET['date_type']) && $_GET['date_type'] == 2 ? "selected='selected'" : '') ?>>Retorno</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_start">Data de</label>
                                <input type="date" class="form-control" name="date[start]" id="date_start" value="<?= isset($_GET['date']['start']) ? $_GET['date']['start'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_end">Data até</label>
                                <input type="date" class="form-control" name="date[end]" id="date_end" value="<?= isset($_GET['date']['end']) ? $_GET['date']['end'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="attendance_status_in">Status Atendimento</label>
                                <select name="attendance_status_in[]" id="attendance_status_in" class="form-control" multiple>
                                    <?php foreach ($attendanceStatusForFilter as $status) { ?>
                                        <option value="<?= $status->id ?>" <?= !isset($_GET['attendance_status_in']) ? ($status->standard_filter == 1 ? "selected" : "") : (in_array($status->id, $_GET['attendance_status_in']) ? "selected" : "") ?>><?= $status->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a href="<?= URL . $this->route ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                    <button type="submit" class="btn pull-right btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
            </div>
        </form>

        <div class="col-md-12">
            <div class="row">
                <?php require APP . 'view/' . $this->dir . '/kanban.php' ?>
            </div>
        </div>

    </section>
</div>

