<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
    <section class="container-fluid">
        <div class="row">
            <div class="box-body">
                <?php if ($permission) { ?>
                    <div class="row">
                        <form enctype="multipart/form-data" role="form" action="<?= URL . $this->route . '/handleSubmitPriceList/' . $productId ?>" method="POST">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name">Nome <span class="text-danger">*</span></label>
                                    <input autocomplete="off" type="text" id="name" class="form-control" name="name" placeholder="Nome" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price_list" class="btn btn-primary" style="margin-top: 25px;">ESCOLHA O ARQUIVO</label>
                                    <input type="file" id="price_list" name="price_list[]" style="display: none;" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary pull-right" style="margin-top: 25px;">Adicionar</button>
                            </div>
                        </form>
                    </div>
                <?php } ?>
                <?php if (!empty($productPriceList)) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered" style="margin-bottom: 10px;">
                                <thead>
                                    <th>Nome</th>
                                    <th class="text-center">Ações</th>
                                </thead>
                                <tbody id="order_list" data-table="products_priceList">
                                    <?php foreach ($productPriceList as $list) { ?>
                                        <tr id="<?= "item_$list->id" ?>" class="<?= $permission ? "" : "disableOrder" ?>" style="cursor: pointer;">
                                            <td><?= $list->name ?></td>
                                            <td style="width: 11rem;" class="text-center">
                                                <a class="btn btn-warning" href="<?= URL . "priceList/products/$list->id_product/$list->filename.$list->extension" ?>" download="">
                                                    <i class="fas fa-file-download"></i>
                                                </a>
                                                <?php if ($permission) { ?>
                                                    <button type="button" id="<?= $list->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . "/handleDeletePriceList/"  ?>">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
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
                Deseja realmente excluir esse item?
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
