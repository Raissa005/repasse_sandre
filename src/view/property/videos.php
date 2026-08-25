<?php

?>
<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
    <section class="container-fluid">
        <div class="row">
            <div class="box-body">
                <?php if ($permission) { ?>
                    <div class="row">
                        <form enctype="multipart/form-data" role="form" action="<?= URL . $this->route . '/handleSubmitVideos/' . $productId ?>" method="POST">
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="name">Nome <span class="text-danger">*</span></label>
                                    <input autocomplete="off" type="text" id="name" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="link_videos">Link video <span class="text-danger">*</span></label>
                                    <input autocomplete="off" type="text" id="link_videos" name="link_videos" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-5 col-lg-5">
                                <button type="submit" class="btn btn-primary pull-right" style="margin-top: 25px;">Adicionar</button>
                            </div>
                        </form>
                    </div>
                <?php } ?>
                <?php if (!empty($productVideos)) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered" style="margin-bottom: 10px;">
                                <thead>
                                    <th>Nome</th>
                                    <th>Link do Vídeo</th>
                                    <?php if ($permission) { ?>
                                        <th class="text-center">Ações</th>
                                    <?php } ?>
                                </thead>
                                <tbody id="order_list" data-table="products_videos">
                                    <?php foreach ($productVideos as $videos) { ?>
                                        <tr id="<?= "item_$videos->id" ?>" class="<?= $permission ? "" : "disableOrder" ?>" style="cursor: pointer;">
                                            <td><?= "$videos->name" ?></td>
                                            <td><a href="<?= $videos->link_videos ?><target="_blank"><?= $videos->link_videos ?></a></td>
                                            <?php if ($permission) { ?>
                                                <td style="width: 180px;" class="text-center">
                                                    <button type="button" id="<?= $videos->id ?>" class="btn btn-danger btn-disable-item" sendTo="<?= $this->route . "/handleDeleteVideos/"  ?>"><i class="fa fa-times"></i></button>
                                                </td>
                                            <?php } ?>
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
                Deseja realmente excluir esse video?
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