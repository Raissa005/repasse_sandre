<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->system_config->title; ?> | Relatório de Atendimentos</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <link href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>" rel="stylesheet">
</head>

<body>
    <div class="content container-fluid">
        <div class="row">
            <section class="container">
                <div class="row">
                    <h4 class="text-center"> Relatório de Atendimentos</h4>
                </div>
            </section>
            <section class="container">
                <div class="row">
                    <ul class="list-inline text-center">
                        <li>Filial: <?= $_SESSION['RR']->branch->current->name ?></li>
                        <li>Nome do cliente: <?= !empty($_GET["name"]) ? $_GET["name"] : "Todos" ?></li>
                        <li>Nome do usuário: <?= !empty($_GET["users_name"]) ? $_GET["users_name"] : "Todos" ?></li>
                        <li>Estado: <?= !empty($_GET["state"]) ? $_GET["state"] : "Todos" ?></li>
                        <li>Classificação: <?= !empty($_GET["classification"]) ? $_GET["classification"] : "Todas" ?></li>
                        <li>Como conheceu: <?= !empty($_GET["communication_channels_name"]) ? $_GET["communication_channels_name"] : "Todos" ?></li>
                        <li>Data de retorno: <?= !empty($_GET["return_date"]) ? $_GET["return_date"] : "Todas" ?></li>
                        <li>Status: <?= !empty($_GET["status"]) ? $_GET["status"] : "Todos" ?></li>
                    </ul>
                </div>
            </section>

            <div class="container">
                <div class="row">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead>
                            <?php if ($_GET["column_id"] == "on") { ?>
                                <th class="text-center" style="max-width: 70px">Código</th>
                            <?php } ?>
                            <?php if ($_GET["column_customer_name"] == "on") { ?>
                                <th class="text-center">Nome do cliente</th>
                            <?php } ?>
                            <?php if ($_GET["column_user_name"] == "on") { ?>
                                <th class="text-center">Nome do usuário</th>
                            <?php } ?>
                            <?php if ($_GET["column_state"] == "on") { ?>
                                <th class="text-center">Estado</th>
                            <?php } ?>
                            <?php if ($_GET["column_classification"] == "on") { ?>
                                <th class="text-center">Classificação</th>
                            <?php } ?>
                            <?php if ($_GET["column_attendance_status"] == "on") { ?>
                                <th class="text-center">Status de atendimento</th>
                            <?php } ?>
                            <?php if ($_GET["column_communication_channel"] == "on") { ?>
                                <th class="text-center">Como conheceu</th>
                            <?php } ?>
                            <?php if ($_GET["column_opening_date"] == "on") { ?>
                                <th class="text-center">Data de abertura</th>
                            <?php } ?>
                            <?php if ($_GET["column_return_date"] == "on") { ?>
                                <th class="text-center">Data de retorno</th>
                            <?php } ?>
                            <?php if ($_GET["column_status"] == "on") { ?>
                                <th class="text-center">Status</th>
                            <?php } ?>
                        </thead>
                        <tbody>
                            <?php foreach ($attendances as $attendance) { ?>
                                <tr>
                                    <?php if ($_GET["column_id"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->id ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_customer_name"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->name ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_user_name"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->users_name ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_state"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->state ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_classification"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->classification ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_attendance_status"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->id_status_name ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_communication_channel"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->communication_channels_name ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_opening_date"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->opening_date ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_return_date"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->return_date ?>
                                        </td>
                                    <?php } ?>
                                    <?php if ($_GET["column_status"] == "on") { ?>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <?= $attendance->status_text ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

</html>