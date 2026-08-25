<?php

namespace RR\components;

class UserDropdownComponent
{
    /**
     * 
     */
    function __construct()
    {
        $this->render();
    }

    private function render()
    {
?>
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="<?= URL . 'plugins/adminlte/img/user2-160x160.jpg' ?>" class="user-image img-circle elevation-2" alt="User Image">
                <span class="d-none d-md-inline"><?= $_SESSION['RR']->user->name ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-primary">
                    <img src="<?= URL . 'plugins/adminlte/img/user2-160x160.jpg' ?>" class="img-circle elevation-2" alt="User Image">
                    <p>
                        <?= $_SESSION['RR']->user->name ?> - <?= $_SESSION['RR']->profile->name ?>
                        <small>Membro desde <?= strftime("%B de %Y", strtotime($_SESSION['RR']->user->created_at)) ?></small>
                    </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
                    <div class="row align-items-center justify-content-between">
                        <a href="#" class="btn btn-sm btn-default">Atendimentos</a>
                        <a href="#" class="btn btn-sm btn-default">Imóveis</a>
                        <a href="#" class="btn btn-sm btn-default">Vendas</a>
                    </div>
                    <!-- /.row -->
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <a href="#" class="btn btn-default btn-flat">Perfil</a>
                    <a href="<?= URL . "login/logout" ?>" class="btn btn-danger btn-flat float-right">Sair</a>
                </li>
            </ul>
        </li>
<?php
    }
}
