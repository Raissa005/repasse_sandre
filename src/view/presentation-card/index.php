<div class="content-wrapper">
    <section class="content container-fluid">
        <?php $this->alert->defaultItemAlerts(); ?>
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top: 7px;"><?= $this->itemName ?></h3>
                        <a class="btn btn-info pull-right" href="<?= URL . $this->route . '/addItem' ?>">Adicionar</a>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group">
                                <form action="<?= URL . $this->route ?>" method="GET">
                                    <input type="hidden" id="page" value="<?= $pagination ?>">
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label for="nome">Nome</label>
                                            <input type="text" class="form-control" id="nome" placeholder="Nome" name="name" value="<?= (isset($_GET['name']) ? $_GET['name'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-2">
                                        <div class="form-group">
                                            <label for="status">Ativo</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1' ? "selected='selected'" : ''); ?>>Ativo</option>
                                                <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0' ? "selected='selected'" : ''); ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-lg-7" style="margin-top:25px;">
                                        <button type="submit" class="btn btn-primary pull-right" name="filter"><i class="fa fa-search"></i> Pesquisar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th class="text-center" style="width: 60px;">Cód.</th>
                                    <th>Nome</th>
                                    <th style="text-align: center;">Ativo</th>
                                    <th style="text-align: center;">Ações</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item) : ?>
                                        <tr>
                                            <td class="text-center"><?= $item->id ?></td>
                                            <td><?= $item->name ?></td>
                                            <td class="text-center">
                                                <span class="label <?= ($item->status == true) ? 'label-success' : 'label-danger' ?>"><?= ($item->status == true) ? "Ativo" : "Inativo" ?></span>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <a class="btn btn-primary" href="<?= URL . $this->route . "/editItem/$item->id" ?>"><i class="fas fa-pencil-alt"></i></a>
                                                    <?php if ($item->status == true) { ?>
                                                        <a id="<?= $item->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . '/disableItem/' ?>"><i class="fa fa-times"></i></a>
                                                    <?php } else { ?>
                                                        <a id="<?= $item->id ?>" class="btn btn-success btn-enable-item" sendTo="<?= $this->route . '/enableItem/' ?>"><i class="fa fa-check"></i></a>
                                                    <?php } ?>
                                                </div>
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
                    <div class="box-footer clearfix">
                        <ul class="pagination pagination-sm no-margin pull-right">
                            <?php if (($pagination - 1) >= 1) : ?>
                                <li><a href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination - 1) ?>">&laquo; Anterior</a></li>
                            <?php endif ?>
                            <?php if (count($nextPagination) > 0) : ?>
                                <li><a href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($pagination + 1) ?>">Próxima &raquo;</a></li>
                            <?php endif ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
