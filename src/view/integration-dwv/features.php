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
        <form action="<?= URL . $this->route . '/handleFeatures' ?>" method="post">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Caracteristicas não encontradas</h3>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th class="text-center">Sistema</th>
                                <th class="text-center featureSysTh" hidden>Caracteristica realcionada (sistema)</th>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($newFeatures as $item) { ?>  
                                    <tr class="featuresTr" id="featuresSelect[<?= $i?>]">
                                        <td style="vertical-align: middle;"><input type="text" class="form-control nameFeature" name="name[]" id="name" class="" value="<?= $item['titles'] ?>"></td>
                                        <td style="vertical-align: middle;"><input type="text" class="form-control" name="alias[]" value="<?= $item['tags'] ?>" readonly=""></td>
                                        <td style="vertical-align: middle;">
                                            <select type="text" class="input-group featuresSelect" name="featuresSelect[]" >
                                                <option value="0">Novo</option>
                                                <?php foreach($featuresSys as $itemSys) { ?>
                                                    <option value="<?= $itemSys->id?>" <?= mb_strtoupper($itemSys->name) == mb_strtoupper($item['titles']) ? 'selected' : '0'?>><?= $itemSys->name?></option>
                                                <?php }
                                                $i++; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php } ?>
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