<?php

use RR\libs\Date;
use RR\libs\RecursiveCostCenter;
use RR\libs\Util;
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <a href="<?= URL . $this->route ?>" class="text-black"><?= $this->title ?></a>
            <small><?= isset($this->caption) ? $this->caption : 'Listagem' ?></small>
            <div class="pull-right">
                <a class="btn btn-sm btn-success" target="_blank" href="<?= URL . $this->route . '/print/excel?' . $filters ?>"><i class="fas fa-file-excel"></i> Gerar Excel</a>
                <a class="btn btn-sm btn-info" target="_blank" href="<?= URL . $this->route . '/print/?' . $filters ?>"><i class="fas fa-print"></i> Imprimir Relatório</a>
            </div>
        </h1>
    </section>

    <section class="content">
        <input type="hidden" id="page" value="<?= 1 ?>">

        <form action="<?= URL . $this->route ?>" method="GET">
            <input type="hidden" name="filtering" value="true">
            <div class="box box-info <?= (isset($_GET["filtering"])) ? '' : 'collapsed-box' ?>">
                <div class="box-header with-border" data-widget="collapse">
                    <h3 class="box-title">Filtros</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['filtering']) ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                    </div>
                </div>
                <div class="box-body" style="<?= isset($_GET["filtering"]) ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="name">Nome do cliente</label>
                                <select name="name" id="name" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($attendances as $attendance) { ?>
                                        <option value="<?= $attendance->name ?>" <?= isset($_GET['name']) && $_GET['name'] == $attendance->name ? "selected" : "" ?>><?= $attendance->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="created_by">Nome do usuário</label>
                                <select name="created_by" id="created_by" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($filter_users as $user) { ?>
                                        <option value="<?= $user->id ?>" <?= isset($_GET['created_by']) && $_GET['created_by'] == $user->id ? "selected" : "" ?>><?= $user->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="state">Estado</label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($states as $state) { ?>
                                        <option value="<?= $state->uf ?>" <?= isset($_GET['state']) && $_GET['state'] == $state->uf ? "selected" : "" ?>><?= $state->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="classification">Classificação</label>
                                <input type="number" name="classification" id="classification" min="0" max="5" class="form-control" value="<?= isset($_GET["classification"]) ? $_GET["classification"] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="id_communication_channel">Como conheceu</label>
                                <select name="id_communication_channel" id="id_communication_channel" class="form-control">
                                    <option value="">Todos</option>
                                    <?php foreach ($communication_channels as $communication_channel) { ?>
                                        <option value="<?= $communication_channel->id ?>" <?= (isset($_GET['id_communication_channel']) && $_GET['id_communication_channel'] == $communication_channel->id) ? "selected" : "" ?>><?= $communication_channel->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="opening_date_from">Data de abertura de:</label>
                                <input type="date" class="form-control" name="opening_date[from]" id="opening_date_from" value="<?= isset($_GET['opening_date']['from']) ? $_GET['opening_date']['from'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="opening_date_to">Data de abertura até:</label>
                                <input type="date" class="form-control" name="opening_date[to]" id="opening_date_to" value="<?= isset($_GET['opening_date']['to']) ? $_GET['opening_date']['to'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="return_date">Data de retorno:</label>
                                <input type="month" class="form-control" name="return_date" id="return_date" value="<?= isset($_GET['return_date']) ? $_GET['return_date'] : "" ?>">
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" <?= (isset($_GET["status"]) && $_GET["status"] == "1") ? "selected" : "" ?>>Ativo</option>
                                    <option value="0" <?= (isset($_GET["status"]) && $_GET["status"] == "0") ? "selected" : "" ?>>Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="id_status">Status de Atendimento</label>
                                <select name="id_status[]" id="id_status" multiple>
                                    <?php foreach ($attendances_status as $a_status) { ?>
                                        <option value="<?= $a_status->id ?>" <?= in_array($a_status->id, $_GET['id_status'] ?? []) ? "selected" : "" ?>><?= $a_status->name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h5 class="text-bold">Exibir Colunas</h5>
                                <label for="column_id">Código
                                    <input type="checkbox" class="custon-checkbox" name="column_id" id="column_id" <?= isset($_GET['column_id']) && $_GET['column_id'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_customer_name">Nome do cliente
                                    <input type="checkbox" class="custon-checkbox" name="column_customer_name" id="column_customer_name" <?= isset($_GET['column_customer_name']) && $_GET['column_customer_name'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_user_name">Nome do usuário
                                    <input type="checkbox" class="custon-checkbox" name="column_user_name" id="column_user_name" <?= isset($_GET['column_user_name']) && $_GET['column_user_name'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_state">Estado
                                    <input type="checkbox" class="custon-checkbox" name="column_state" id="column_state" <?= isset($_GET['column_state']) && $_GET['column_state'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_classification">Classificação
                                    <input type="checkbox" class="custon-checkbox" name="column_classification" id="column_classification" <?= isset($_GET['column_classification']) && $_GET['column_classification'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_attendance_status">Status de atendimento
                                    <input type="checkbox" class="custon-checkbox" name="column_attendance_status" id="column_attendance_status" <?= isset($_GET['column_attendance_status']) && $_GET['column_attendance_status'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_communication_channel">Como conheceu
                                    <input type="checkbox" class="custon-checkbox" name="column_communication_channel" id="column_communication_channel" <?= isset($_GET['column_communication_channel']) && $_GET['column_communication_channel'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_opening_date">Data de abertura
                                    <input type="checkbox" class="custon-checkbox" name="column_opening_date" id="column_opening_date" <?= isset($_GET['column_opening_date']) && $_GET['column_opening_date'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_return_date">Data de retorno
                                    <input type="checkbox" class="custon-checkbox" name="column_return_date" id="column_return_date" <?= isset($_GET['column_return_date']) && $_GET['column_return_date'] == 'on' ? 'checked' : '' ?>>
                                </label>
                                <label for="column_status">Status
                                    <input type="checkbox" class="custon-checkbox" name="column_status" id="column_status" <?= isset($_GET['column_status']) && $_GET['column_status'] == 'on' ? 'checked' : '' ?>>
                                </label>
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

        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Listagem</h3>
                    </div>
                    <div class="box-body no-padding">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <?php if ($_GET["column_id"] == "on") { ?>
                                        <th class="text-center" style="max-width: 70px">Código</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_customer_name"] == "on") { ?>
                                        <th class="text-center">Nome do cliente</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_user_name"] == "on") { ?>
                                        <th class="text-center">Nome do usuário</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_state"] == "on") { ?>
                                        <th class="text-center">Estado</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_classification"] == "on") { ?>
                                        <th class="text-center">Classificação</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_attendance_status"] == "on") { ?>
                                        <th class="text-center">Status de atendimento</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_communication_channel"] == "on") { ?>
                                        <th class="text-center">Como conheceu</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_opening_date"] == "on") { ?>
                                        <th class="text-center">Data de abertura</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_return_date"] == "on") { ?>
                                        <th class="text-center">Data de retorno</th>
                                    <?php } ?>
                                    <?php if ($_GET["column_status"] == "on") { ?>
                                        <th class="text-center">Status</th>
                                    <?php } ?>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendances as $attendance) { ?>
                                        <tr class="attendance_row c-pointer" data-id="<?= $attendance->id ?>">
                                            <?php if ($_GET["column_id"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?= $attendance->id ? $attendance->id : "-" ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_customer_name"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?= $attendance->name ? $attendance->name : "-" ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_user_name"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?= $attendance->users_name ? $attendance->users_name : "-" ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_state"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?= $attendance->state ? $attendance->state : "-" ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_classification"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <div class="box-classification" style="font-size: 20px; min-width: 120px">
                                                        <?= $attendance->classification ? Self::doStar($attendance->classification, 5) : "-" ?>
                                                    </div>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_attendance_status"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <span class="label label-default" style="background: <?= $attendance->id_status_box_color ?>; color: white">
                                                        <i class="<?= $attendance->id_status_icon_status ?>"></i> <?= $attendance->id_status_name ?>
                                                    </span>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_communication_channel"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?= $attendance->communication_channels_name ? $attendance->communication_channels_name : "-" ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_opening_date"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?= $attendance->opening_date ? Date::date_hour($attendance->opening_date) : "-" ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_return_date"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <?= $attendance->return_date ? Date::date_hour($attendance->return_date) : "-" ?>
                                                </td>
                                            <?php } ?>
                                            <?php if ($_GET["column_status"] == "on") { ?>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <span class="label label-<?= $attendance->status_label ?>"><?= $attendance->status_text ?></span>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

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
                Deseja realmente cancelar este item?
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-danger" name="disable">Desativar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="enable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente ativar este item?
            </div>
            <form method="POST" class="form-enable-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn btn-success" name="enable">Ativar</button>
                </div>
            </form>
        </div>
    </div>
</div>
