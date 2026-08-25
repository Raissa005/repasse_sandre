<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">
    <section class="container-fluid">
        <div class="row">
            <form role="form" action="<?= URL . $this->route . '/handleSubmitImmovableResource/' . $productId ?>" method="POST">
                <div class="box-body">
                    <div class="row">
                        <?php foreach ($productOwnershipFeature as $ownershipFeature) { ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="<?= $ownershipFeature->immovable_resource_name ?>"><?= $ownershipFeature->immovable_resource_name ?></label>
                                    <?php if ($ownershipFeature->immovable_resource_data_type == 4) { ?>
                                        <select class="form-control" name="<?= $ownershipFeature->id ?>" id="<?= $ownershipFeature->immovable_resource_name ?>">
                                            <option value="" <?= empty($ownershipFeature->value) ? "selected" : ""  ?> disabled></option>
                                            <option value="Sim" <?= mb_strtolower($ownershipFeature->value) == "sim" ? "selected" : ""  ?>>Sim</option>
                                            <option value="Não" <?= mb_strtolower($ownershipFeature->value) == "não" ? "selected" : "" ?>>Não</option>
                                        </select>
                                    <?php } else { ?>
                                        <input autocomplete="off" type="<?= $ownershipFeature->inputType ?>" class="form-control" id="<?= $ownershipFeature->immovable_resource_name ?>" name="<?= $ownershipFeature->id ?>" value="<?= $ownershipFeature->value ?>" <?= $ownershipFeature->inputAttr ?> <?= $attrInputs ?>>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="box-footer">
                    <a type="button" class="btn btn-warning" href="<?= URL . $this->route ?>">Voltar</a>
                    <div class="pull-right">
                        <?php if ($permission) { ?>
                            <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

</div>
</div>
</section>
</div>