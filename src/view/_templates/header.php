<?php

use RR\components\MenusComponent;

?>
<!DOCTYPE html>
<html>

<head>
    <html lang="pt-BR">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->system_config->title; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="imagem/png" href="<?= URL . $this->logoFavicon ?>" />
    <?= $this->renderStyle() ?>
</head>

<body class="hold-transition skin-blue sidebar-mini <?= $_SESSION['RR']->setting->sidebar == 0 ? "sidebar-collapse" : "" ?>">
    <div class="wrapper">
        <header class="main-header">
            <a href="#" class="logo" style="background-color: #fff; padding: 0px">
                <img class="logo-mini" style="padding: 0; margin: 0; max-width: 50px; max-height: 50px;" src="<?= URL . $this->logoMini ?>">
                <img class="logo-lg" style="padding: 0; margin: 0 auto; max-width: 230px; max-height: 50px;" src="<?= URL . $this->logoMenu ?>">
            </a>
            <nav class="navbar navbar-static-top">
                <a class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <i class="fa fa-bars"></i>
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle dropdown-notification" data-toggle="dropdown">
                                <i class="fa fa-bell"></i>
                                <span class="label <?= "" ?>" style="<?= "" ?>">
                                    <?= "" ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-notification-menu">
                                <li class="header">Você tem
                                    <span class="count-notification-header">
                                        <?= "" ?>
                                    </span> notificações
                                </li>
                                <li>
                                    <ul class="menu">
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a target="_blank" href="<?= URL . 'notification' ?>" class="btn-view-all-notifications">Visualizar tudo</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="https://api.whatsapp.com/send?phone=554832636688" target="_blank"><span class="sup dot">Suporte Técnico</span></a>
                        </li>
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?= $_SESSION['RR']->branch->current->name ?>
                                <span style="margin-left: 10px;" class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" style="width: 180px;">
                                <li>
                                    <ul class="menu" id="branch-select-change" style="max-height: 330px;">
                                        <?php foreach ($_SESSION['RR']->branch->all as $currentBranch) { ?>
                                            <li class="<?= $_SESSION['RR']->branch->current->id == $currentBranch->id ? 'active' : '' ?> change-branch" data-id-branch="<?= $currentBranch->id ?>">
                                                <a href="#">
                                                    <i class="<?= $currentBranch->icon ?>"></i>
                                                    <?= $currentBranch->name ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?= $_SESSION['RR']->user->profileURL ?>" class="user-image">
                                <span class="hidden-xs"><?= $_SESSION['RR']->user->name ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="user-header">
                                    <img src="<?= $_SESSION['RR']->user->profileURL ?>" class="img-circle" alt="<?= $_SESSION['RR']->user->name ?>">
                                    <p>
                                        <?= $_SESSION['RR']->user->name ?>
                                        <small><?= $_SESSION['RR']->profile->name ?></small>
                                    </p>
                                </li>
                                <?php if ($_SESSION['RR']->branch->current->id != 0) { ?>
                                    <li class="user-body">
                                        <div class="row">
                                            <div class="col-xs-12 text-center">
                                                <a class="btn btn-sm btn-default" href="<?= URL . 'attendance?created_by=' . $_SESSION['RR']->user->id ?>">Atendimentos</a>
                                                <a class="btn btn-sm btn-default" href="<?= URL . 'sale-requests?created_by=' . $_SESSION['RR']->user->id ?>">Vendas</a>
                                            </div>
                                        </div>
                                    </li>
                                <?php } ?>
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?= URL . "users/edit-item/" . $_SESSION['RR']->user->id ?>" class="btn btn-default btn-flat">Perfil</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?= URL . "login/logout" ?>" class="btn btn-danger btn-flat"><i class="fas fa-sign-out-alt"></i> Sair</a>
                                    </div>
                                </li>
                                <?php if (!empty($_SESSION['RR']->user->turnBack) && $_SESSION['RR']->user->id != 1) { ?>
                                    <li style="text-align: center" class="user-footer">
                                        <a href="<?= URL . "users/turnUser/1" ?>" class="btn btn-default btn-flat">Retornar às permissões de suporte</a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="main-sidebar">
            <section class="sidebar ">
                <input type="hidden" value="<?= isset($this->page->id) ? $this->page->id : "" ?>" id="id_menu">
                <div action="#" method="get" class="sidebar-form">
                    <div class="input-group">
                        <input type="text" name="filter-menus" id="filter-menus" class="form-control" autocomplete="off" placeholder="Buscar...">
                        <span class="input-group-btn">
                            <button type="submit" name="search" id="search-menu-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
                <?php if (count($this->menus) >= 1) { ?>
                    <ul class="sidebar-menu" data-widget="tree">
                        <?php (new MenusComponent($this->menus)) ?>
                    </ul>
                <?php } ?>
            </section>
        </aside>