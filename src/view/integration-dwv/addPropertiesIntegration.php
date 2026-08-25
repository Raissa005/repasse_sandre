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
        <form action="<?= URL . $this->route . '/importPropertiesApi' ?>" method="post">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Selecione imóveis para integração</h3>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <th class="text-center">Selecionar</th>
                                <th class="text-center">Imagem</th>
                                <th>Nome</th>
                                <th>Número</th>
                                <th>Área Total (m²)</th>
                                <th>Local</th>
                                <th>Valor</th>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($array['data'] as $item) { 
                                    ?>
                                    <tr>
                                        <td class="text-center" style="vertical-align: middle; width: 30px;"><input style="height: 20px; width: 20px;"type="checkbox" name="<?= $i++ ?>" id="" value="<?= $item['id'] ?>"></td>
                                        <td class="text-center"><img width="50px" style="margin: auto;" src="<?= $item['building']['cover']['sizes']['circle'] ?>" class="img-responsive img-circle"></td>
                                        <td style="vertical-align: middle;"><?= $item['title'] ?></td>
                                        <td style="vertical-align: middle;"><?= $item['unit']['title'] ?></td>
                                        <td style="vertical-align: middle;"><?= (!empty($item['unit']['total_area']) && $item['unit']['total_area'] != 0.0) ? $item['unit']['total_area'] : $item['unit']['util_area'] ?></td>
                                        <td style="vertical-align: middle;"><?= $item['building']['address']['city'] . ' / ' . $item['building']['address']['state']?></td>
                                        <td style="vertical-align: middle;"><?= Util::maskMoney($item['unit']['price']) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-block btn-primary">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>