<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $system->title ?> | Log in</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/fontawesome/font-awesome.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/adminlte/css/adminlte.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/sweetalert2/sweetalert2.min.css" ?>">
    <link rel="icon" type="imagem/png" href="<?= URL . ($system->logo_favicon_capa ? "img/settings/logo_favicon-{$system->logo_favicon_cont}.{$system->logo_favicon_ext}" : 'img/more/favicon.png') ?>" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="login-box-body">
            <div class="login-logo">
                <img class="logo-lg" src="<?= URL . ($system->logo_login_capa ? "img/settings/logo_login-{$system->logo_login_cont}.{$system->logo_login_ext}" : 'img/more/login.png') ?>">
            </div>
            <form action="<?= URL . "login/signIn" ?>" method="post">
                <div class="form-group has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope" style="width: 15px;"></i></span>
                        <input type="email" autocomplete="off" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock" style="width: 15px;"></i></span>
                        <input name="password" autocomplete="off" type="password" class="form-control" placeholder="Senha" required>
                    </div>
                </div>
                <a href="<?= URL .  "login/recoverPassword/" ?>">Esqueceu a sua senha?</a>
                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary pull-right">Entrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/sweetalert2/sweetalert2.min.js" ?>"></script>
    <script src="<?= URL . "js/" . JSVERSION . "/toast-config.js" ?>"></script>
    <?= \RR\libs\Toast::render() ?>
</body>

</html>