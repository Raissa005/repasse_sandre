<?php

use RR\components\ContentHeaderComponent;
use RR\components\NavTabsComponent;
use RR\libs\Secure;
?>
<form role="form" action="<?= URL . "{$this->route}/handleSubmitEditItem/$itemId" ?>" enctype="multipart/form-data" method="POST" id="form-users">
    <div class="content-wrapper">
        <?php new ContentHeaderComponent($content_header) ?>
        <section class="content container-fluid">
            <div class="nav-tabs-custom">
                <?php new NavTabsComponent($nav_tabs) ?>
                <div class="tab-content">
                    <div class="tab-pane active">
                        <div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name">Nome <span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="text" class="form-control" id="name" value="<?= $item->name ?>" name="name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="cpf">CPF </label>
                                                <input autocomplete="off" type="text" class="form-control" id="cpf" name="cpf" value="<?= isset($item->cpf) ? $item->cpf : '' ?>" cpf_mask>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="creci">Creci </label>
                                                <input autocomplete="off" type="text" class="form-control" id="creci" minlength="5" maxlength="12" name="creci" value="<?= isset($item->creci) ? $item->creci : '' ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="phone">Celular </label>
                                                <input autocomplete="off" type="text" class="form-control" id="phone" name="phone" value="<?= isset($item->phone) ? $item->phone : '' ?>" cellphone>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="email">E-mail <span class="text-danger">*</span></label>
                                                <small id="validate-email" class="pull-right"></small>
                                                <input autocomplete="off" type="email" class="form-control" id="email" value="<?= $item->email ?>" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group box-password">
                                                <label for="password">Senha <span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="password" class="form-control" id="password" name="password">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group box-password">
                                                <small id="validate-password" class="pull-right"></small>
                                                <label for="password_confirm">Confirmação senha <span class="text-danger">*</span></label>
                                                <input autocomplete="off" type="password" class="form-control" id="password_confirm" name="password_confirm">
                                            </div>
                                        </div>
                                        <div class="col-md-4 user_profile">
                                            <div class="form-group">
                                                <label for="id_profile">Tipo usuário <span class="text-danger">*</span></label>
                                                <select name="id_profile" id="id_profile" class="form-control">
                                                    <?php foreach ($users_profiles as $profile) { ?>
                                                        <option value="<?= $profile->id ?>" <?= $item->id_profile == $profile->id ? "selected" : "" ?>><?= $profile->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="id_customer">Vincular Pessoa</label>
                                                <select name="id_customer" id="id_customer" class="form-control">
                                                    <?php foreach ($dadosUnicos as $customer) { ?>
                                                        <option value="<?= $customer->id ?>" <?= $item->id_customer == $customer->id ? "selected" : "" ?>><?= $customer->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if (Secure::access_admin()) { ?>
                                            <div class="col-md-4 user_permission_publish">
                                                <div class="form-group">
                                                    <label for="permission_publish">Publicar Site <span class="text-danger">*</span></label>
                                                    <select name="permission_publish" id="permission_publish" class="form-control">
                                                        <option value="0" <?= $item->permission_publish ? "selected" : "" ?>>Não</option>
                                                        <option value="1" <?= $item->permission_publish ? "selected" : "" ?>>Sim</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 user_permission_publish">
                                                <div class="form-group">
                                                    <label for="show_team">Exibir na Equipe do Site <span class="text-danger">*</span></label>
                                                    <select name="show_team" id="show_team" class="form-control">
                                                        <option value="0" <?= $item->show_team ? "selected" : "" ?>>Não</option>
                                                        <option value="1" <?= $item->show_team ? "selected" : "" ?>>Sim</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 select-branches">
                                                <div class="form-group">
                                                    <label for="id_branch">Filiais <span class="text-danger">*</span></label>
                                                    <select name="id_branch[]" id="id_branch" class="form-control" multiple>
                                                        <?php foreach ($branches as $branch) { ?>
                                                            <option value="<?= $branch->id ?>" <?= in_array($branch->id, $idsBranch) ? "selected" : "" ?>><?= $branch->name ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="profile_picture" class="btn btn-block btn-default" style="text-transform: uppercase; margin-top: 25px;">Imagem perfil <small>(160x160)</small></label>
                                        <input type="file" name="profile_picture" onchange="readURL(this, 'onloadImage'), onfilename(this, 'spanFilename');" id="profile_picture" style="display: none;">
                                        <span id="spanFilename"></span>
                                    </div>
                                    <?php if ($item->profile_capa == true) { ?>
                                        <button type="button" class="btn-disable-item btn btn-danger" style="margin-bottom: 5px;" title="Excluir Imagem" id="<?= $itemId ?>" sendTo="<?= $this->route . '/deleteImageProfileId/' ?>">EXCLUIR IMAGEM</button>
                                    <?php } ?>
                                    <div><img src="<?= $item->profile_capa == true ? URL . "img/users/$itemId/$itemId-profile-$item->profile_cont.$item->profile_ext" : URL . "img/users/default/img-user-default.png" ?>" class="img-circle" style="width: 100%; max-width: 200px; display: flex; margin-left: auto; margin-right: auto;" id="onloadImage"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                            <div class="pull-right">
                                <button type="submit" id="btn3" class="btn btn-block btn-primary">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sellers" class="box box-info" hidden>
                <div class="box-header with-border">
                    <h3 class="box-title">Adicionar Vendedores</h3>
                </div>
                <div class="container-fluid">
                    <div class="box-body">
                        <div class="row">
                            <label for="id_seller">Vendedores<span class="text-danger">*</span></label>
                            <select name="id_seller[]" id="id_seller" class="form-control" multiple>
                                <?php foreach ($sellers->all->data as $seller) { ?>
                                    <option value="<?= $seller->id ?>" <?= in_array((object)['id_seller' => $seller->id], $sellers->selected->data) ? 'selected' : '' ?>><?= $seller->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>

<!-- modals -->
<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <form method="POST" class="form-disable-item">
                <div class="modal-body">Deseja realmente excluir a Imagem?</div>
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

<div id="generic-message-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn" id="btn-confirm" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>