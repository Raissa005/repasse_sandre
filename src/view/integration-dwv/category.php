<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\PropertyFilterComponent;
use RR\libs\Secure;
use RR\libs\Util;

?>
<div class="content-wrapper">
<?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content">
        <input type="hidden" id="page" value="<?= $pagination->page ?>">
        <form action="<?= URL . $this->route . '/handleCategory' ?>" method="post">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Categoria do imóvel não encontrada</h3>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th class="text-center">Sistema</th>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td style="vertical-align: middle;"><input type="text" id="nameCategory" class="form-control" name="name" value="<?= $listItem->construction_stage ?>" readonly=""></td>
                                        <td style="vertical-align: middle;"><input type="text" class="form-control" name="alias" value="<?= $listItem->construction_stage_raw ?>" readonly=""></td>
                                        <td style="vertical-align: middle;">
                                            <select type="text" class="input-group categorySelect" name="categorySelect" id="categorySelect">
                                                <option value="0">Novo</option>
                                                <?php foreach($categoriesSys as $itemSys) { ?>
                                                    <option value="<?= $itemSys->id?>" <?= mb_strtoupper($itemSys->name) == mb_strtoupper($listItem->construction_stage) ? 'selected' : ''?>><?= $itemSys->name?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-block btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>