<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $system->title ?> | Log in</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= URL . "plugins/bootstrap/css/bootstrap.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/fontawesome/font-awesome.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/adminlte/css/adminlte.min.css" ?>">
    <link rel="icon" type="imagem/png" href="<?= URL . ($system->logo_favicon_capa ? "img/settings/logo_favicon-{$system->logo_favicon_cont}.{$system->logo_favicon_ext}" : 'img/more/favicon.png') ?>" />

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-box-body">
            <div class="login-logo">
                <img class="logo-lg" src="<?= URL . "img/settings/logo_login-{$system->logo_login_cont}.{$system->logo_login_ext}" ?>">
            </div>
            <?php if (isset($_GET['validPassword']) && $_GET['validPassword'] == "false") { ?>
                <div class="form-group text-center">
                    <p class="label label-warning">Senhas diferentes!</p>
                </div>
            <?php } ?>
            <form action="<?= URL . "login/handleSubmitChangePassWord/" . $_GET['token'] ?>" method="post">
                <div class="form-group has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock" style="width: 15px;"></i></span>
                        <input name="password" autocomplete="off" type="password" class="form-control" placeholder="Senha" required>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock" style="width: 15px;"></i></span>
                        <input name="confirm_password" autocomplete="off" type="password" class="form-control" placeholder="Confirme a senha" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-danger pull-right">Alterar Senha</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

</html>
