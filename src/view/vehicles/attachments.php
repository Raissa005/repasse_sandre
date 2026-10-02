<?php

use RR\libs\Secure;
use RR\components\NavTabsComponent;
use RR\components\ContentHeaderComponent4214;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content container-fluid">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs) ?>
            <div class="tab-content">
                <div class="tab-pane active">
                    <div>
                        <form enctype="multipart/form-data" action="<?= URL . $this->route . "/handleSubmitAddAttachments/$item->id" ?>" method="POST">
                            <div class="row">
                                <?php if (Secure::access_admin()) { ?>
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
                                            <input type="file" id="attachments" name="attachments[]" style="margin-top: 25px;" class="visually-hidden" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary pull-right" style="margin-right: -200px; margin-top: 20px; margin-bottom: 10px;">Adicionar</button>
                                    </div>
                                <?php } ?>
                            </div>
                        </form>
                        <div class="row">
                            <?php if (!empty($vehicleAttachments)) { ?>
                                <div class="col-md-12">
                                    <table class="table table-striped table-bordered" style="margin-bottom: 10px;">
                                        <thead>
                                            <th>Nome</th>
                                            <th>Descrição</th>
                                            <th class="text-center">Ações</th>
                                        </thead>
                                        <tbody id="order_list" data-table="products_attachments">
                                            <?php foreach ($vehicleAttachments as $attachment) { ?>
                                                <tr id="<?= "item_$attachment->id" ?>" class="<?= $permission ? "" : "disableOrder" ?>" style="cursor: pointer;">
                                                    <td><?= "$attachment->name" ?></td>
                                                    <td><?= htmlspecialchars($attachment->description ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td style="width: 11rem;" class="text-center">
                                                    <a class="btn btn-warning"
                                                    href="<?= URL . "public/vehicle/" . urlencode($attachment->id_vehicle ) . "/attachments/" . urlencode($attachment->filename) ?>"
                                                    download="<?= htmlspecialchars($attachment->filename) ?>">
                                                    <i class="fas fa-file-download"></i>
                                                    </a>

                                                        <?php if (Secure::access_admin()) { ?>
                                                            <a href="<?= URL . "vehicles/handleDeleteAttachment/$itemId/$attachment->id" ?>" class="btn btn-danger btn-disable-item"><i class="fa fa-times"></i></a>
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
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    document.getElementById('attachments').addEventListener('change', function(event) {
    if (event.target.files.length > 0) {
        let fileName = event.target.files[0].name;
        let fileNameWithoutExt = fileName.substring(0, fileName.lastIndexOf(".")) || fileName;
        document.getElementById('name').value = fileNameWithoutExt;
    } else {
        document.getElementById('name').value = '';
    }
});
</script>
