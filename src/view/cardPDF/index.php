<?php
$e = function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $e($user->card_digital_name) ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="imagem/png" href="#">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <style>
        @page {
            margin: 0px !important;
        }

        /**Fontes */
        @font-face {
            font-family: gotham;
            src: url("<?= $e($publicDir) ?>fonts/gotham-black.ttf");
        }

        @font-face {
            font-family: lato-black;
            src: url("<?= $e($publicDir) ?>fonts/Lato-Black.ttf");
        }

        @font-face {
            font-family: lato-regular;
            src: url("<?= $e($publicDir) ?>fonts/Lato-Regular.ttf");
        }

        @font-face {
            font-family: roboto-black;
            src: url("<?= $e($publicDir) ?>fonts/Roboto-Black.ttf");
        }

        @font-face {
            font-family: roboto-regular;
            src: url("<?= $e($publicDir) ?>fonts/Roboto-Regular.ttf");
        }

        .gotham {
            font-family: gotham;
        }

        .lato-black {
            font-family: lato-black;
        }

        .lato-regular {
            font-family: lato-regular;
        }

        .roboto-black {
            font-family: roboto-black;
        }

        .roboto-regular {
            font-family: roboto-regular;
        }

        /**Fim Fontes */

        .fundoCard {
            height: 2100px;
            position: relative;
            <?php if ($configDigitalCard->capa_fundo) { ?>background: url("<?= $e($backgroundImage) ?>") no-repeat;
            <?php } else { ?>background-color: <?= $configDigitalCard->cor_fundo ?>;
            <?php } ?>
        }

        .logo-cabecalho {
            width: 100%;
            display: block;
            max-width: 620px;
            margin: 110px auto 0px auto;
        }

        .img-fluid {
            width: 100%;
        }

        .image-user {
            border: 25px solid <?= $configDigitalCard->cor_borda_usuario ?>;
            border-radius: 322.5px;
        }

        .corpo {
            height: 1320px;
        }

        .div-nome-empresa {
            text-align: center;
        }

        .texto-nome-corretor {
            color: <?= $configDigitalCard->cor_fonte_usuario ?>;
            text-align: center;
            padding-top: 35px;
            line-height: 130px;
            font-size: <?= $configDigitalCard->tamanho_fonte_usuario . 'px' ?>;
            margin-bottom: -40px;
        }

        .div-corretor {
            text-align: center;
            padding-top: 30px;
            margin-bottom: 50px;
        }

        .texto-occupation {
            color: <?= $configDigitalCard->cor_fonte_ocupacao ?>;
            font-size: <?= $configDigitalCard->tamanho_fonte_ocupacao . 'px' ?>;
        }

        .div-empresa {
            text-align: center;
            margin-top: 100px;
            margin-bottom: 50px;
        }

        .div-legenda {
            text-align: center;
            margin-top: 90px;
        }

        .texto-legenda {
            color: <?= $configDigitalCard->cor_fonte_legenda ?>;
            font-size: <?= $configDigitalCard->tamanho_fonte_legenda . 'px' ?>;
            padding-left: 100px;
            padding-right: 100px;
        }

        .box-icones-corpo {
            position: relative;
            width: 1200px;
            margin: 0px auto;
            text-align: center;
        }

        .icone-corpo {
            width: 90px;
            padding: 50px;
            border: 1px solid <?= $configDigitalCard->cor_borda_icone ?>;
            border-radius: 95px;
            display: block;
            position: relative;
        }

        .img-100 {
            width: 100%;
        }

        .rodape {
            width: 100%;
            height: 458px;            
            text-align: center;
            background-color: <?= $configDigitalCard->cor_fundo_logo ?>;
            margin: 0px auto 0px auto;            
        }
    </style>
</head>

<body class="hold-transition">
    <div class="fundoCard">
        <div class="cabecalho">
            <div class="logo-cabecalho">
                <img class="img-fluid image-user" src="<?= $e($userImage) ?>" />
            </div>
        </div>

        <div class="corpo">
            <div class="texto-nome-corretor <?= $e($configDigitalCard->fonte_usuario) ?>"><?= $e($user->card_digital_name) ?></div>

            <div class="div-corretor gotham">
                <div class="texto-occupation <?= $e($configDigitalCard->fonte_ocupacao) ?>"><?= $e($user->card_digital_occupation) ?></div>
            </div>

            <div class="box-icones-corpo">
                <table border="0" cellspacing="0" cellpadding="0" width="100%" style="text-align: center;">
                    <tr>
                        <?php foreach ($networks as $network) {
                            if (!empty($network->link)) {
                        ?>
                                <td style="text-align: center;">
                                    <a class="icone-corpo" href="<?php if ($network->id_networks_card_digital == 4) {
                                                                        echo "https://api.whatsapp.com/send/?phone=55";
                                                                    } else if ($network->id_networks_card_digital == 3) {
                                                                        echo "mailto:";
                                                                    } ?><?= $e($network->link) ?>" target="_blank" style="margin: auto;">
                                        <img src="<?= $e($publicDir . "img/network_ico/" . basename((string) $network->filename) . ".png") ?>" class="img-100">
                                    </a>
                                </td>
                        <?php }
                        } ?>
                    </tr>
                </table>
            </div>

            <div class="box-icones-corpo">
                <table border="0" cellspacing="0" cellpadding="0" width="100%" style="text-align: center;">
                    <tr>
                        <?php if (!empty($config->url_global)) { ?>
                            <td style="text-align: center;">
                                <a class="icone-corpo" href="<?= $e($config->url_global) ?>" target="_blank" style="margin: auto;">
                                    <img src="<?= $publicDir . "img/network_ico/website.png" ?>" class="img-100">
                                </a>
                            </td>
                        <?php  } ?>
                        <?php foreach ($networkCompany as $network) {
                            if (strpos($network->link, 'facebook')) { ?>
                                <td style="text-align: center;">
                                    <a class="icone-corpo" href="<?= $e($network->link) ?>" target="_blank" style="margin: auto;">
                                        <img src="<?= $publicDir . "img/network_ico/facebook.png" ?>" class="img-100">
                                    </a>
                                </td>
                            <?php } else if (strpos($network->link, 'instagram')) { ?>
                                <td style="text-align: center;">
                                    <a class="icone-corpo" href="<?= $e($network->link) ?>" target="_blank" style="margin: auto;">
                                        <img src="<?= $publicDir . "img/network_ico/instagram.png" ?>" class="img-100">
                                    </a>
                                </td>
                            <?php } else if (strpos($network->link, 'linkedln')) { ?>
                                <td style="text-align: center;">
                                    <a class="icone-corpo" href="<?= $e($network->link) ?>" target="_blank" style="margin: auto;">
                                        <img src="<?= $publicDir . "img/network_ico/linkedln.png" ?>" class="img-100">
                                    </a>
                                </td>
                            <?php } else if (strpos($network->link, 'twitter')) { ?>
                                <td style="text-align: center;">
                                    <a class="icone-corpo" href="<?= $e($network->link) ?>" target="_blank" style="margin: auto;">
                                        <img src="<?= $publicDir . "img/network_ico/twitter.png" ?>" class="img-100">
                                    </a>
                                </td>
                            <?php } else if (strpos($network->link, 'whatsapp') || strpos($network->link, 'wa.me')) { ?>
                                <td style="text-align: center;">
                                    <a class="icone-corpo" href="<?= $e($network->link) ?>" target="_blank" style="margin: auto;">
                                        <img src="<?= $publicDir . "img/network_ico/whatsapp.png" ?>" class="img-100">
                                    </a>
                                </td>
                            <?php } ?>

                        <?php } ?>
                    </tr>
                </table>
            </div>

            <div class="div-legenda">
                <div class="<?= $e($configDigitalCard->fonte_creci) ?> texto-legenda">Toque nos ícones para entrar em contato e conhecer nossa estrutura.</div>
            </div>
        </div>
    </div>
    <div class="rodape">
        <img style="width: 600px; margin-top: 100px;" src="<?= $configDigitalCard->capa_logo ? $e($footerImage) : "" ?>" />
    </div>

</body>

</html>
