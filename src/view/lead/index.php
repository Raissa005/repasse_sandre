<?php

use RR\libs\Date;
use RR\components\PaginationComponent1245;

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Leads</h3>
                        <div class="col-md-3 pull-right">
                            <button type="button" class="btn btn-sm btn-primary pull-right" id="modalLeadImport" data-toggle="modal" data-target="#file"><i class="fa fa-file-upload"></i> Importar Formulário Facebook</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <form action="<?= URL . $this->route ?>" method="GET">
                                    <input type="hidden" id="page" value="<?= $pagination->page ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nome">Nome</label>
                                            <input autocomplete="off" type="text" class="form-control" id="nome" placeholder="Nome" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="id_communication_channel">Conheceu</label>
                                            <select class="form-control" id="id_communication_channel" name="id_communication_channel">
                                                <option value="">Todos</option>
                                                <?php foreach ($communications as $communication) { ?>
                                                    <option value="<?= $communication->id ?>" <?= isset($_GET['id_communication_channel']) && !empty($_GET['id_communication_channel']) && $_GET['id_communication_channel'] == $communication->id ? "selected" : "" ?>><?= $communication->name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                                <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="attendance_open">Atendimento Aberto</label>
                                            <select class="form-control" id="attendance_open" name="attendance_open">
                                                <option value="0" <?= (isset($_GET['attendance_open']) && $_GET['attendance_open'] == '0' ? "selected='selected'" : ''); ?>>Todos</option>
                                                <option value="1" <?= (isset($_GET['attendance_open']) && $_GET['attendance_open'] == '1' ? "selected='selected'" : ''); ?>>Sim</option>
                                                <option value="2" <?= (isset($_GET['attendance_open']) && $_GET['attendance_open'] == '2' ? "selected='selected'" : ''); ?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pull-right">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-block btn-primary mt-25"><i class="fa fa-search"></i> Pesquisar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th>Nome</th>
                                    <th class="text-center">Conheceu</th>
                                    <th class="text-center">Vendedor</th>
                                    <th class="text-center">Data de Abertura</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($items->data as $item) : ?>
                                        <tr>
                                            <td><?= mb_substr($item->name, 0, 20, 'UTF-8'); ?></td>
                                            <td class="text-center"><?= $item->communication_channel_name ?></td>
                                            <td class="text-center"><?= !empty($item->user_name) ? $item->user_name : " - " ?></td>
                                            <td class="text-center"><?= Date::date_hour($item->created_at) ?></td>
                                            <td class="text-center">
                                                <span class="label <?= ($item->status == true) ? 'label-success' : 'label-danger' ?>"><?= ($item->status == true) ? "Ativo" : "Inativo" ?></span>
                                            </td>
                                            <td class="text-center mt-50">
                                                <a class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/editItem/$item->id" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                <?php if ($item->status == true) { ?>
                                                    <a id="<?= $item->id ?>" class="btn btn-sm btn-danger btn-disable-item" sendTo="<?= $this->route . '/disableItem/' ?>"><i class="fa fa-times"></i></a>
                                                <?php } else { ?>
                                                    <a id="<?= $item->id ?>" class="btn btn-sm btn-success btn-enable-item" sendTo="<?= $this->route . '/enableItem/' ?>"><i class="fa fa-check"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="csv"></div>
                    </div>
                    <div class="box-footer clearfix text-center">
                        <?php new PaginationComponent1245($pagination); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modals -->
<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Deseja realmente desativar este item?
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

<div id="import-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Importar formulários do Facebook</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Arquivo para importação(.csv):</label>
                                <input type="file" name="filename" id="filename" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" class="form-import-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" id="import" class="btn btn-primary" name="import">Importar </button>
                </div>
            </form>
        </div>
    </div>
</div>