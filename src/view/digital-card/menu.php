<div class="content-wrapper">
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $item->name ?></h3>
                </div>
                <div class="nav-tabs-custom" style="background-color: transparent !important;">
                    <ul class="nav nav-tabs" style="background-color: white;">
                        <li class="<?= ($_GET['pg1'] == 'editItem') ? "active" : "" ?>"><a href="<?= URL . $this->route . "/editItem/" . $itemId ?>">Editar</a></li>
                    </ul>
