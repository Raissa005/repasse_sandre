<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $system->title ?> | Recuperar Senha</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/fontawesome/font-awesome.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/adminlte/css/adminlte.min.css" ?>">
    <link rel="icon" type="imagem/png" href="<?= URL . ($system->logo_favicon_capa ? "img/settings/logo_favicon-{$system->logo_favicon_cont}.{$system->logo_favicon_ext}" : 'img/more/favicon.png') ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-box-body">
            <div class="login-logo">
                <img class="logo-lg" style="" src="<?= URL . "img/settings/logo_login-{$system->logo_login_cont}.{$system->logo_login_ext}" ?>">
            </div>
            <form action="<?= URL . "login/sendRecoverPasswordMail" ?>" method="post">
                <div class="form-group has-feedback">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-envelope" style="width: 15px;"></i></span>
                        <input type="email" autocomplete="off" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a href="<?= URL . 'login' ?>" class="btn btn-danger">Voltar</a>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-warning">Enviar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
</body>

</html>
