<?php

use RR\libs\Util;

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:site_name" content="<?= Util::titleCase($product->name) . " - " . Util::titleCase($system->title) ?>">
    <meta property="og:title" content="<?= Util::titleCase($product->name) ?>" />
    <meta property="og:url" content="<?= $_GET['url'] ?>" />
    <meta property="og:image" itemprop="image" content="<?= URL . "img/products_imgs/" . $product->id . "/" . $product->id_property_cover_image . "md." . $product->property_cover_image_extension ?>">
    <meta property="og:image:width" content="500">
    <meta property="og:image:height" content="340">
    <meta property="og:description" content="<?= substr(strip_tags($product->site_description), 0, 170); ?>" />
    <meta property="og:type" content="website" />
    <title><?= Util::titleCase($product->name) . " - " . Util::titleCase($system->title) ?></title>

    <link rel="icon" type="imagem/png" href="<?= URL . "img/settings/logo_favicon-{$system->logo_favicon_cont}.{$system->logo_favicon_ext}" ?>" />

    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/css/bootstrap.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/fontawesome/font-awesome.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "plugins/" . PLUGINSVERSION . "/adminlte/css/adminlte.min.css" ?>">
    <link rel="stylesheet" href="<?= URL . "css/" . CSSVERSION . "/presentations/styles.css" ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&display=swap">

    <style>
        .bg-rodape {
            padding: 20px;
            background-color: <?= $presentationCard->cor_contato_fundo ?>;
            color: <?= $presentationCard->cor_contato_fonte ?>;
        }

        .a-contato-rodape,
        .a-contato-rodape:focus,
        .a-contato-rodape:active {
            width: 40px;
            height: 40px;
            color: <?= $presentationCard->cor_contato_btn_fonte ?>;
            font-size: 18px;
            border-radius: 10%;
            margin-left: 5px;
            margin-right: 5px;
            border: solid 1px <?= $presentationCard->cor_contato_btn_border ?>;
            display: flex;
            justify-content: center;
            align-content: center;
            align-items: center;
            transition: all 0.3s ease-in-out;
            background-color: <?= $presentationCard->cor_contato_btn_fundo ?>;
        }

        .a-contato-rodape:hover {
            color: <?= $presentationCard->cor_contato_btn_fonte_efeito ?>;
            filter: brightness(0.9);
        }

        .footer-copyright {
            font-size: 12px;
            background-color: <?= $presentationCard->cor_rodape_copyright_fundo ?>;
        }

        .copyright {
            text-align: center;
            margin: 7px;
            color: <?= $presentationCard->cor_rodape_copyright_fonte ?>;
        }

        .copyright a {
            color: <?= $presentationCard->cor_rodape_copyright_link_fonte ?>;
        }
    </style>
</head>

<body>
    <div class="container-fluid bg-banner">
        <div class="row">
            <div class="box-banner">
                <div class="box-banner-mascara"></div>
                <div class="box-banner-horizontal-centralizado">
                    <div class="banner-nome"><?= $product->name ?></div>
                    <div class="box-atalho">
                        <a href="#resources" class="a-atalho-icone no-mg-lt"><i class="fas fa-paint-roller"></i></a>
                        <a href="#images" class="a-atalho-icone"><i class="fa fa-camera"></i></a>
                        <!-- <a href="#pagament" class="a-atalho-icone"><i class="fa fa-dollar-sign"></i></a>
                        <a href="#description" class="a-atalho-icone"><i class="fas fa-file-alt"></i></a> -->
                        <a href="#localization" class="a-atalho-icone "><i class="fa fa-map-marked"></i></a>
                        <a href="#user" class="a-atalho-icone no-mg-rt"><i class="fa fa-user"></i></a>
                    </div>
                </div>
                <?php if (isset($product->id_property_cover_image) && !empty($product->id_property_cover_image)) { ?>
                    <img style="width: 100%;" src="<?= URL . "img/products_imgs/" . $product->id . "/" . $product->id_property_cover_image . "lg." . $product->property_cover_image_extension ?>">
                <?php } ?>
            </div>
        </div>
    </div>
    <div id="resources" class="container-fluid bg-carac">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="box-titulo text-center">
                            <p>Veja as</p>
                            <h1>Características</h1>
                            <div></div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="row">
                            <?php foreach ($features as $feature) { ?>
                                <div class="col-md-4 col-lg-3">
                                    <li class="list-caract">
                                        <strong><?= Util::titleCase($feature->immovable_resource_name) ?>: </strong><span class="pull-right"><?= Util::titleCase($feature->value) ?></span>
                                    </li>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="images" class="container-fluid bg-fotos">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="box-titulo text-center">
                    <p>veja as</p>
                    <h1>Nossas Fotos</h1>
                    <div></div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="row">
                    <?php foreach ($images as $image) { ?>
                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                            <a data-fancybox href="<?= URL . "img/products_imgs/$image->id_product/{$image->id}lg.{$image->extension}" ?>" class="a-fancy fancybox" data-fancybox-group="<?= $product->name ?>">
                                <div class="box-img">
                                    <div class="foto-efeito"><i class="fa fa-search"></i></div>
                                    <img class="img-responsive" style="margin: auto;" src="<?= URL . "img/products_imgs/$image->id_product/{$image->id}md.{$image->extension}" ?>" alt="<?= $product->name ?>">
                                </div>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php if ($presentationCard->show_payment == 1 || !empty($product->condition_product)) { ?>
        <div id="pagament" class="container-fluid bg-pagam">
            <div class="row">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="box-titulo text-center">
                                <p>Veja as</p>
                                <h1>Formas de Pagamento</h1>
                                <div></div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12">
                            <div class="row">
                                <p class="text-center">
                                    <strong>Valor:</strong> <?= Util::maskMoney($product->site_value) ?>
                                </p>
                                <p class="text-center">
                                    <?= nl2br($product->condition_product) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div id="description" class="container-fluid bg-desc">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="box-titulo text-center">
                            <!-- <p>Veja as</p> -->
                            <h1>Descrição do Imóvel</h1>
                            <div></div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-12">
                        <div class="row">
                            <div class="text-desc"><?= $product->site_description ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="localization" class="container-fluid bg-localizacao">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="box-titulo text-center">
                    <h1>Localização</h1>
                    <div></div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="row">
                    <div style="margin-bottom: 20px;">
                        <input class="hidden" type="text" name="latlng" id="latlng" />
                        <input class="hidden" type="text" name="lat" id="lat" value="<?= isset($product->lat) ? $product->lat : "" ?>" />
                        <input class="hidden" type="text" name="lng" id="lng" value="<?= isset($product->lng) ? $product->lng : "" ?>" />
                        <div id="map" style="height: 340px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="user" class="container-fluid bg-rodape">
        <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                <div class="cartao-corretor">
                    <?php if ($user && $user->profile_capa == true) { ?>
                        <div class="imagem-usuario">
                            <img src="<?= URL . "img/users/$user->id/$user->id-profile-$user->profile_cont.$user->profile_ext" ?>" style="width: 80px;" class="img-rounded">
                        </div>
                    <?php } ?>
                    <div class="contato-corretor">
                        <div class="contato-nome-corretor"><?= $user ? Util::titleCase($user->name) : "" ?></div>
                        <div class="redes-corretor">
                            <?php if (!empty($user->email)) { ?>
                                <a href="mailto:<?= strtolower($user->email) ?>?subject=<?= Util::titleCase($product->name) ?>&body=<?= $presentationCard->message ?>" class="a-contato-rodape a-contato-mail"><i class="fas fa-envelope"></i></a>
                            <?php } ?>
                            <?php if (!empty($user->phone)) { ?>
                                <a href="https://api.whatsapp.com/send?phone=55<?= $user->phone ?>&text=<?= $presentationCard->message ?>" class="a-contato-rodape a-contato-whatsapp"><i class="fab fa-whatsapp"></i></a>
                            <?php } ?>
                            <?php if (!empty($user->phone)) { ?>
                                <a href="tel:<?= $user->phone ?>" class="a-contato-rodape a-contato-phone"><i class="fas fa-phone-alt"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
                <div class="cartao-empresa">
                    <?php if ($branch && $branch->logo_rodape_capa) { ?>
                        <div class="imagem-empresa">
                            <img src="<?= URL . "img/branch/$branch->id/logo_rodape-$branch->logo_rodape_cont.$branch->logo_rodape_ext" ?>">
                        </div>
                    <?php } else { ?>
                        <div class="imagem-empresa">
                            <img src="<?= URL . "img/settings/logo_rodape-{$system->logo_rodape_cont}.{$system->logo_rodape_ext}" ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid footer-copyright">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                <div class="copyright">
                    <p>Copyright &copy; 2020 <a href="<?= $site->url_global ?>"><?= $system->footer; ?></a> - Todos os direitos reservados - Desenvolvido por <a href="https://www.ydealtecnologia.com.br/">Ydeal Tecnologia</a></p>
                </div>
            </div>
        </div>
    </div>
    <script>
        const url = "<?= URL ?>";
    </script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery-3.5.1.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/jquery/js/jquery-ui.min.js" ?>"></script>
    <script src="<?= URL . "plugins/" . PLUGINSVERSION . "/bootstrap/js/bootstrap.min.js" ?>"></script>
    <script src="<?= URL . "js/" . JSVERSION . "/mapPresentation.js" ?>"></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXb984jOma4yop-zX7bqsy7Hcsgm5CCok&callback=initMap"></script>
</body>

</html>