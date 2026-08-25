<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
    <section class="container-fluid">
        <div class="row">
            <div class="box-body">
                <?php if ($permission) { ?>
                    <div class="row">
                        <form enctype="multipart/form-data" role="form" action="<?= URL . $this->route . '/handleSubmitAttachment/' . $productId ?>" method="POST">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name">Nome <span class="text-danger">*</span></label>
                                    <input autocomplete="off" type="text" id="name" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="description">Descrição</label>
                                    <input autocomplete="off" type="text" id="description" name="description" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="attachments" class="btn btn-primary" style="margin-top: 25px;">ESCOLHA O ARQUIVO</label>
                                    <input type="file" id="attachments" name="attachments[]" required style="display: none;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary pull-right" style="margin-top: 25px;">Adicionar</button>
                            </div>
                        </form>
                    </div>
                <?php } ?>
                <div class="row">
                    <?php if (!empty($productAttachments)) { ?>
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered" style="margin-bottom: 10px;">
                                <thead>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th class="text-center">Ações</th>
                                </thead>
                                <tbody id="order_list" data-table="products_attachments">
                                    <?php foreach ($productAttachments as $attachment) { ?>
                                        <tr id="<?= "item_$attachment->id" ?>" class="<?= $permission ? "" : "disableOrder" ?>" style="cursor: pointer;">
                                            <td><?= "$attachment->name" ?></td>
                                            <td><?= $attachment->description ?></td>
                                            <td style="width: 11rem;" class="text-center">
                                                <a class="btn btn-warning" href="<?= URL . "attachments/products/$attachment->id_product/$attachment->filename.$attachment->extension" ?>" download="">
                                                    <i class="fas fa-file-download"></i>
                                                </a>
                                                <?php if ($permission) { ?>
                                                    <button type="button" id="<?= $attachment->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . "/handleDeleteAttachment/"  ?>">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                </div>
            </div>
        </div>
    </section>
</div>
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
            <div class="modal-body">
                Deseja realmente excluir esse anexo?
            </div>
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
