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
    <link rel="icon" type="imagem/png" href="<?= URL . ($system->logo_favicon_capa ? "img/settings/logo_favicon-{$system->logo_favicon_cont}.{$system->logo_favicon_ext}" : 'img/more/favicon.png') ?>" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">

    <div class="login-box">
        <div class="login-box-body">
            <div class="login-logo">
                <img class="logo-lg" src="<?= URL . ($system->logo_login_capa ? "img/settings/logo_login-{$system->logo_login_cont}.{$system->logo_login_ext}" : 'img/more/login.png') ?>">
            </div>
            <?php if (isset($_GET)) { ?>
                <div class="form-group text-center">
                    <?php if (isset($_GET['error']) && $_GET['error'] == "error") { ?>
                        <p class="label label-danger">Link inválido ou não encontrado!</p>
                    <?php } else if (isset($_GET['invalidToken']) && $_GET['invalidToken'] == "true") { ?>
                        <p class="label label-warning">Token inválido!</p>
                    <?php } else if (isset($_GET['tokenExpired']) && $_GET['tokenExpired'] == "true") { ?>
                        <p class="label label-warning">O tempo para redefinir a senha expirou!</p>
                    <?php } else if (isset($_GET['edited']) && $_GET['edited'] == "true") { ?>
                        <p class="label label-success">Senha alterada com successo!</p>
                    <?php } else if (isset($_GET['error']) && $_GET['error'] == "user") { ?>
                        <p class="label label-danger">Ops! Usuário não encontrado!</p>
                    <?php } else if (isset($_GET['sendEmail']) && $_GET['sendEmail'] == "true") { ?>
                        <p class="label label-success">Acabamos de enviar um email de recuperação para você!</p>
                    <?php } ?>
                </div>
            <?php } ?>
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
                <?php if (!empty($_SESSION['RR']->toast)) { ?>
                    <p style="color: red;">E-mail ou senha invalido!</p>
                <?php } ?>
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
</body>

</html>