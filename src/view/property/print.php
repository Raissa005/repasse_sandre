<?php

use RR\libs\Date;
use RR\libs\Util;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório de Imóveis</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>" rel="stylesheet">
    <link href="<?= URL . "css/" . CSSVERSION . "/record-bills-to-pay/" . "record.css" ?>" rel="stylesheet">
</head>

<body>
    <div class="content container-fluid">
        <div class="row">
            <section class="container">
                <div class="row">
                    <img src="<?= URL . $this->logoMini ?>" alt="Logo" class="branch-logo" style="position: absolute; padding-top: 10px;">
                    <h4 class="text-center"> Relatório de Imóveis</h4>
                </div>
            </section>
            <section class="container">
                <div class="row">
                    <ul class="list-inline text-center">
                        <li>Total de Imóves: <?= $response->count ?></li>
                    </ul>
                </div>
            </section>
            <div class="container">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-condensed table-bordered table-striped">
                            <thead>
                                <?php if (isset($_GET['columnPropertyIdName']) && $_GET['columnPropertyIdName'] == 'on') { ?>
                                    <th>Código - Nome</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyType']) && $_GET['columnPropertyType'] == 'on') { ?>
                                    <th class="text-center">Tipo - Categoria</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyLocation']) && $_GET['columnPropertyLocation'] == 'on') { ?>
                                    <th class="text-center">Localização</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyValue']) && $_GET['columnPropertyValue'] == 'on') { ?>
                                    <th class="text-center">Valor</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyStatus']) && $_GET['columnPropertyStatus'] == 'on') { ?>
                                    <th class="text-center">Status</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyBranch']) && $_GET['columnPropertyBranch'] == 'on') { ?>
                                    <th class="text-center">Filiais</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyCreationDate']) && $_GET['columnPropertyCreationDate'] == 'on') { ?>
                                    <th class="text-center">Data Criação</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyUser']) && $_GET['columnPropertyUser'] == 'on') { ?>
                                    <th class="text-center">Usuário</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyNeighborhood']) && $_GET['columnPropertyNeighborhood'] == 'on') { ?>
                                    <th class="text-center">Bairro</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyClassification']) && $_GET['columnPropertyClassification'] == 'on') { ?>
                                    <th class="text-center">Classificação</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyTotalArea']) && $_GET['columnPropertyTotalArea'] == 'on') { ?>
                                    <th class="text-center">Área Total (m²)</th>
                                <?php } ?>
                                <?php if (isset($_GET['columnPropertyOwner']) && $_GET['columnPropertyOwner'] == 'on') { ?>
                                    <th class="text-center">Proprietário</th>
                                <?php } ?>
                            </thead>
                            <tbody>
                                <?php foreach ($response->data as $item) { ?>
                                    <tr>
                                        <?php if (isset($_GET['columnPropertyIdName']) && $_GET['columnPropertyIdName'] == 'on') { ?>
                                            <td style="vertical-align: middle;"><?= $item->identifier . ' - ' . $item->name ?></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyType']) && $_GET['columnPropertyType'] == 'on') { ?>
                                            <td class="text-center"><?= "{$item->property_type_name} <br> {$item->category_name}" ?></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyLocation']) && $_GET['columnPropertyLocation'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><?= "$item->city_name - $item->uf" ?></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyValue']) && $_GET['columnPropertyValue'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><strong><?= Util::maskMoney($item->value) ?></strong></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyStatus']) && $_GET['columnPropertyStatus'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= $item->labelText ?></span><br></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyBranch']) && $_GET['columnPropertyBranch'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= $item->branch_name ?></span><br></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyCreationDate']) && $_GET['columnPropertyCreationDate'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= Date::date($item->created_at) ?></span><br></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyUser']) && $_GET['columnPropertyUser'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= $item->user_name ?></span><br></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyNeighborhood']) && $_GET['columnPropertyNeighborhood'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= $item->neighborhood ?></span><br></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyClassification']) && $_GET['columnPropertyClassification'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= $item->property_classification_name ?></span><br></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyTotalArea']) && $_GET['columnPropertyTotalArea'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= $item->total_area ?></span><br></td>
                                        <?php } ?>
                                        <?php if (isset($_GET['columnPropertyOwner']) && $_GET['columnPropertyOwner'] == 'on') { ?>
                                            <td class="text-center" style="vertical-align: middle;"><span><?= $item->property_owner ?></span><br></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

</html>