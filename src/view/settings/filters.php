<?php
?>
<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
    <section class="container-fluid">
        <div class="row">
            <form method="post" action="<?= URL . $this->route . "/filters/" ?>" enctype="multipart/form-data">
                <div class="box-body" style="padding-bottom: 0px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group pull-right" style="margin-bottom: 0px;">
                                <button type="button" class="btn btn-success btn-generic-item" sendTo="<?= $this->route . "/ableAllFilters/" ?>" bodyHtml="Deseja realmente ativar TODAS as categorias do site?" footerHtml="Ativar" btnFooter="btn-success">
                                    <i class="far fa-check-square"></i> Ativar todas
                                </button>
                                <button type="button" class="btn btn-warning btn-generic-item" sendTo="<?= $this->route . "/disableAllFilters /" ?>" bodyHtml="Deseja realmente inativar TODAS as categorias do site?" footerHtml="Inativar" btnFooter="btn-danger">
                                    <i class="far fa-square"></i> Inativar todas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-condensed" style="margin-bottom: 10px;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 7rem;">Ordem</th>
                                            <th class="text-center">Item</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order_list" data-table="property_filter">
                                        <?php foreach ($filters->data as $filter) { ?>
                                            <tr id='<?= "item_{$filter->id}" ?>'>
                                                <td class="text-center"><?= $filter->item_order ?></td>
                                                <td class="text-center"><?= $filter->name ?></td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <span class="label bg-<?= $filter->labelClass ?>"><?= $filter->labelText ?></span><br>
                                                </td>
                                                <td class="text-center">
                                                    <a id="<?= $filter->id ?>" class="btn btn-sm btn-<?= $filter->class ?> btn-<?= $filter->modal ?>-item" bodyHtml="Você deseja <?= $filter->status == 1 ? 'desativar' : 'ativar' ?> esta categoria?" sendTo="<?= $this->route . ($filter->status == 1 ? '/handleDisableItem/' : '/handleAbleItem/') ?>" title="<?= $filter->status == 1 ? 'Inativar' : 'Ativar' ?>"><i class="fa fa-<?= $filter->icon ?>"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <button class="btn btn-primary">Salvar Ordem</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

</div>
</section>
</div>


<div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body"></div>
            <form method="POST" class="form-disable-item">
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

<div id="enable-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body"></div>
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

<div id="generic-item-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
            </div>
            <div class="modal-body">
                Você realmente deseja fazer isso?
            </div>
            <form method="POST" class="form-generic-item">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <button type="submit" class="btn" id="btn-submit">OK</button>
                </div>
            </form>
        </div>
    </div>
</div>
