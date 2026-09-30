<?php

use RR\libs\Secure;
use RR\components\PaginationComponent1245;

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Usuários</h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/add-item' ?>">Adicionar</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <form action="<?= URL . $this->route ?>" method="GET">
                                    <input type="hidden" id="page" value="<?= $pagination->page ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="nome">Nome</label>
                                            <input type="text" class="form-control" id="nome" placeholder="Nome" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
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
                                    </div>
                                    <div class="col-md-3" style="margin-top:25px;">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-block" name="filter"><i class="fa fa-search"></i> Pesquisar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped">
                                <thead>
                                    <th class="text-center" style="width: 60px;">Cód.</th>
                                    <th class="text-center">Imagem</th>
                                    <th>Nome</th>
                                    <th class="text-center">Perfil</th>
                                    <th class="text-center">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($users->data as $user) { ?>
                                        <tr>
                                            <td class="text-center"><?= $user->id ?></td>
                                            <td class="text-center">
                                                <img width="32px" class="img-circle" src="<?= URL . ($user->profile_capa ? "img/users/{$user->id}/{$user->id}-profile-{$user->profile_cont}.{$user->profile_ext}" : "img/users/default/img-user-default.png") ?>">
                                            </td>
                                            <td><?= $user->name ?></td>
                                            <td class="text-center"><?= $user->profile_name ?></td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-primary" <?= !Secure::access_generic($user->access) ? "disabled" : "" ?> href="<?= URL . $this->route . "/edit-item/$user->id" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                <?php if ($user->status == true) { ?>
                                                    <a id="<?= $user->id ?>" <?= !Secure::access_generic($user->access) ? "disabled" : "" ?> class="btn btn-sm btn-danger <?= Secure::access_generic($user->access) ? "btn-disable-item" : "" ?>" sendTo="<?= $this->route . '/disableUser/' ?>"><i class="fa fa-times"></i></a>
                                                <?php } else { ?>
                                                    <a id="<?= $user->id ?>" <?= !Secure::access_generic($user->access) ? "disabled" : "" ?> class="btn btn-sm btn-success <?= Secure::access_generic($user->access) ? "btn-enable-item" : "" ?>" sendTo="<?= $this->route . '/enableUser/' ?>"><i class="fa fa-check"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer clearfix text-center">
                        <?php new PaginationComponent1245($pagination); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>