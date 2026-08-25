<?php

use RR\libs\Date;

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $systemConfig->title ?> | Contrato </title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="imagem/png" href="<?= URL . $logoFavicon ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "css/" . CSSVERSION . "/customer/print.css" ?>">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="contract">
                        <?= $contractText ?>
                        <?php if ($contract->signed == true) { ?>
                            <h4>&nbsp;</h4>
                            <div class="row">
                                <div class="text" style="text-align: center;"><strong><?= $contract->name ?></strong></div>
                                <hr class="line1" />
                                <div class="text" style="text-align: center;">CPF: <strong><?= $contract->cpf ?></strong></div>
                                <div class="text" style="text-align: center;">Data: <?= Date::date_hour_full($contract->signed_at) ?></div>
                            </div>
                        <?php } else { ?>
                            <form role="form" action="<?= URL . "print/signed/$contract->id" ?>" method="post">
                            <h4>&nbsp;</h4>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group"><br>
                                            <hr class="line1">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group esconder">
                                            <label for="name">Nome Completo:</label>
                                            <input autocomplete="off" type="text" class="form-control" name="name" id="name" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group esconder">
                                            <label for="birth_date">Data de Nascimento:</label>
                                            <input autocomplete="off" type="date" class="form-control" name="birth_date" id="birth_date" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group esconder">
                                            <label for="cpf">CPF:</label>
                                            <input autocomplete="off" type="text" class="form-control" name="cpf" id="cpf" cpf_mask required>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group esconder">
                                            <button style="margin-top: 25px;" class="btn btn-success">Assinar Autorização</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.inputmask.bundle.min.js" ?>"></script>
    <script>
        $("[cpf_mask]").inputmask({
            mask: ['999.999.999-99'],
            keepStatic: true
        });
    </script>
</body>

</html>
