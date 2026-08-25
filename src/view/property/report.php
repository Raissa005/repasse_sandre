<?php

use RR\libs\Date;
use RR\libs\Util;

?>
<div class="tab-pane active" style="background-color: #fff; margin-bottom: 20px;">

    <div class="container-fluid">
        <div class="row">
        </div>
    </div>
</div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-6">
        <form role="form" action="<?= URL . $this->route . '/report/' . $productId ?>" method="GET">
            <input type="hidden" name="filtering" value="true">
            <div class="box box-info <?= (isset($_GET["filtering"])) ? '' : 'collapsed-box' ?>" ">
                <div class=" box-header with-border" data-widget="collapse">
                <h3 class="box-title">Filtros</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?= isset($_GET['filtering']) ? 'fa-minus' : 'fa-plus' ?>"></i></button>
                </div>
            </div>
            <div class="box-body" style="<?= isset($_GET["filtering"]) ? '' : 'display: none;' ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Data De</label>
                            <input type="date" class="form-control" name="start_date" id="start_date" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : "" ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_month">Data Até</label>
                            <input type="date" name="end_date" id="end_date" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : "" ?>" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="created_by">Criado por</label>
                        <select name="created_by" id="created_by">
                            <option value="">Todos</option>
                            <?php foreach ($users as $item) { ?>
                                <option value="<?= $item->id ?>" <?= isset($_GET['created_by']) && $_GET['created_by'] == $item->id ? "selected" : "" ?>><?= $item->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Cliente</label>
                            <input type="text" name="name" id="name" value="<?= isset($_GET['name']) ? $_GET['name'] : "" ?>" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <a href="<?= URL . "property/" . "report/" . $productId ?>" class="btn btn-warning"><i class="fa fa-eraser"></i> Remover Filtros</a>
                <button type="submit" class="btn btn-primary pull-right"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
    </div>
    </form>
</div>
</div>

<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box bg-green">
            <span class="info-box-icon">
                <i class="fas fa-globe"></i>
            </span>
            <div class="info-box-content">
                <span class="info-boc-text">Visualizações Site</span>
                <span class="info-box-number"><?= $siteViewCount ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box bg-yellow">
            <span class="info-box-icon">
                <i class="fas fa-comments"></i>
            </span>
            <div class="info-box-content">
                <span class="info-boc-text">Apresentado em Atendimento</span>
                <span class="info-box-number"><?= $attendanceCountProperties ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
            <span class="info-box-icon">
                <i class="fas fa-clipboard-check"></i>
            </span>
            <div class="info-box-content">
                <span class="info-boc-text">Lista Interesses</span>
                <span class="info-box-number"><?= $countAttendanceWithInterest ?></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="box box-warning">
            <div class="box-body">
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover no-space">
                            <thead>
                                <tr>
                                    <th class="text-center">Atendimentos</th>
                                </tr>
                            </thead>
                        </table>
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Nome Cliente</th>
                                    <th class="text-center">Corretor</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            <tbody>
                                <?php foreach ($displayedProperties as $displayedProperty) { ?>
                                    <tr>
                                        <td class="text-center" style="vertical-align: middle;"><?= $displayedProperty->id ?></td>
                                        <td class="text-center" style="vertical-align: middle;"><?= $displayedProperty->name ?></td>
                                        <td class="text-center" style="vertical-align: middle;"><?= $displayedProperty->user_name ?></td>
                                        <td style="vertical-align: middle;" class="text-center">
                                            <a href="<?= URL . "attendance/" . "attendance/{$displayedProperty->id}?properties"  ?>" class="btn btn-primary btn-sm">
                                                <i class="fa fa-comments"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-body">
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover no-space">
                            <thead>
                                <th class="text-center">Interessados</th>
                            </thead>
                        </table>
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Nome Cliente</th>
                                    <th class="text-center">Corretor</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendancesWithInterest as $interstAttendance) { ?>
                                    <tr>
                                        <td class="text-center" style="vertical-align: middle;"><?= $interstAttendance->id ?></td>
                                        <td class="text-center" style="vertical-align: middle;"><?= $interstAttendance->name ?></td>
                                        <td class="text-center" style="vertical-align: middle;"><?= $interstAttendance->user_name ?></td>

                                        <td style="vertical-align: middle;" class="text-center">
                                            <a href="<?= URL . "attendance/" . "attendance/{$interstAttendance->id}?interests"  ?>" class="btn btn-primary btn-sm">
                                                <i class="fa fa-comments"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



</div>
</section>
</div>
