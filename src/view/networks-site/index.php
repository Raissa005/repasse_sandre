<?php

use RR\components\PaginationComponent1245;

?>
<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;">Redes Sociais</h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addNetwork' ?>">Adicionar</a>
                    </div>
                    <div class="box-body ">
                        <div class="row">
                            <div class="form-group">
                                <form action="<?= URL . $this->route . "/" ?>" method="GET" id="form-redeSocialSite">
                                    <input type="hidden" id="page" value="<?= $pagination->page ?>">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <input autocomplete="off" type="text" class="form-control" placeholder="Nome" name="nome" value="<?= (isset($_GET['nome']) ? $_GET['nome'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Status</label>
                                        <div class="form-group">
                                            <select class="form-control" name="ativo">
                                                <option value="1" <?= (isset($_GET['ativo']) && $_GET['ativo'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                                <option value="0" <?= (isset($_GET['ativo']) && $_GET['ativo'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">

                                    </div>
                                    <div class="col-md-3" style="margin-top:25px;">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-block btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th class="text-center" style="width: 60px;">Cód.</th>
                                    <th class="text-center">Icone</th>
                                    <th>Nome</th>
                                    <th>Link</th>
                                    <th class="text-center">Ordem</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 180px; text-align: center;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($networks->data as $network) : ?>
                                        <tr>
                                            <td class="text-center"><?= $network->id ?></td>
                                            <td class="text-center"> <i class="<?= str_replace("bb", "b", substr_replace("fab", substr($network->icone, 2), 3))  ?>" style="margin: 0px; padding: 0px; font-size: 35px; color: <?= $network->cor ?>; "></i></td>
                                            <td><?= $network->nome ?></td>
                                            <td><?= $network->link ?></td>
                                            <td class="text-center"><span><?= $network->ordem ?></span></td>
                                            <td class="text-center">
                                                <span class="label <?= ($network->ativo == true) ? 'label-success' : 'label-danger' ?>"><?= ($network->ativo == true) ? "Ativo" : "Inativo" ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a class="btn btn-sm btn-primary" href="<?= URL . $this->route . "/editNetwork/$network->id/" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                <?php if ($network->ativo == true) { ?>
                                                    <a id="<?= $network->id ?>" class="btn btn-sm btn-danger btn-disable-item" sendTo="<?= $this->route . '/disableNetwork/' ?>"><i class="fa fa-times"></i></a>
                                                <?php } else { ?>
                                                    <a id="<?= $network->id ?>" class="btn btn-sm btn-success btn-enable-item" sendTo="<?= $this->route . '/enableNetwork/' ?>"><i class="fa fa-check"></i></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
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