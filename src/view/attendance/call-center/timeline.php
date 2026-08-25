<?php

use RR\libs\Secure;
?>

<div class="box-body">
    <div class="row">
        <input type="hidden" id="today" value="<?= date("Y-m-d h:i") ?>">
        <form enctype="multipart/form-data" role="form" action="<?= URL . $this->route . '/handleSubmitAddComment/' . $attendanceId ?>" method="POST" id="form-comment">
            <div class="col-md-12 col-lg-12">
                <div class="form-group">
                    <label for="comment">Comentário</label>
                    <textarea autocomplete="off" name="comment" id="comment" class="form-control" rows="4"></textarea>
                </div>
            </div>
            <?php if (!$disabledStatus) { ?>
                <div class="col-xs-9 col-md-5">
                    <div class="form-group" style="margin-bottom: 0px;">
                        <label for="">Agendar Retorno</label>
                        <input type="datetime-local" class="form-control" id="return_date" name="return_date" value="<?= !empty($attendance->return_date) ? date("Y-m-d\TH:i", strtotime($attendance->return_date)) : "" ?>">
                    </div>
                </div>
            <?php } ?>
            <div class="col-xs-<?= $disabledStatus ? "12" : "3" ?> col-md-<?= $disabledStatus ? "12" : "7" ?>">
                <button type="submit" class="btn btn-primary pull-right" id="btn-submit-comment" style="margin-top: 25px;">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- end nav-tabs-custom -->
</div>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" aria-expanded="true" href="#t_comment">Comentários</a>
        </li>
        <li class="">
            <a data-toggle="tab" aria-expanded="true" href="#t_scheduling">Agendamentos</a>
        </li>
        <li class="">
            <a data-toggle="tab" aria-expanded="true" href="#t_status">Status</a>
        </li>
        <li class="">
            <a data-toggle="tab" aria-expanded="true" href="#t_registration">Cadastros</a>
        </li>
    </ul>

    <div class="tab-content" style="padding: 0;">
        <div class="tab-pane active" id="t_comment">
            <div class="box-body bg-gray-light">
                <ul class="timeline">
                    <?php
                    $aux = "";
                    foreach ($timelineView['comment'] as $item) {
                        $currentDate = date("d/m/Y", strtotime($item->created_at));
                        $hour = date("H:i", strtotime($item->created_at));

                        if ($aux == "" || $aux != $currentDate) {
                            $aux = $currentDate;
                    ?>
                            <li class="time-label">
                                <span class="bg-aqua"><?= $aux ?></span>
                            </li>
                        <?php } ?>
                        <li style="margin-right: 0px;">
                            <i class="<?= $item->icon ?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock">
                                    </i> <?= $hour ?>
                                </span>
                                <h3 class="timeline-header" style="font-weight: 600; color: #0073b7">
                                    <?= $item->user_name ?>
                                </h3>
                                <div class="timeline-body" style="padding-bottom: 0px;">
                                    <?= $item->comment ?>
                                </div>
                                <div class="timeline-footer">
                                    <?php if (!empty($item->url_attachment)) { ?>
                                        <a href="<?= $item->url_attachment ?>" class="btn btn-warning btn-xs" download="">+
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                    <?php } ?>
                                    <a style="margin-right: 3px; color: #fff;">
                                        <i class="fas fa-circle btn-xs"></i>
                                    </a>
                                    <?php if (Secure::access_secretary()) { ?>
                                        <button type="button" class="btn btn-danger btn-xs pull-right btn-disable-item" bodyHtml="Deseja realmente excluir esse Comentário?" title="Excluir comentário" style="margin-bottom: 3px;" id="<?= $item->id ?>" sendTo="<?= $this->route . '/handleDeleteAttendanceTimeline/' ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="tab-pane" id="t_scheduling">
            <div class="box-body bg-gray-light">
                <ul class="timeline">
                    <?php
                    $aux = "";
                    foreach ($timelineView['scheduling'] as $item) {
                        $currentDate = date("d/m/Y", strtotime($item->created_at));
                        $hour = date("H:i", strtotime($item->created_at));

                        if ($aux == "" || $aux != $currentDate) {
                            $aux = $currentDate;
                    ?>
                            <li class="time-label">
                                <span class="bg-aqua"><?= $aux ?></span>
                            </li>
                        <?php } ?>
                        <li style="margin-right: 0px;">
                            <i class="<?= $item->icon ?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock">
                                    </i> <?= $hour ?>
                                </span>
                                <h3 class="timeline-header" style="font-weight: 600; color: #0073b7">
                                    <?= $item->user_name ?>
                                </h3>
                                <div class="timeline-body" style="padding-bottom: 0px;">
                                    <?= $item->comment ?>
                                </div>
                                <div class="timeline-footer">
                                    <?php if (!empty($item->url_attachment)) { ?>
                                        <a href="<?= $item->url_attachment ?>" class="btn btn-warning btn-xs" download="">+
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                    <?php } ?>
                                    <a style="margin-right: 3px; color: #fff;"><i class="fas fa-circle btn-xs"></i></a>
                                    <?php if (Secure::access_secretary()) { ?>
                                        <button type="button" class="btn btn-danger btn-xs pull-right btn-disable-item" bodyHtml="Deseja realmente excluir esse Comentário?" title="Excluir comentário" style="margin-bottom: 3px;" id="<?= $item->id ?>" sendTo="<?= $this->route . '/handleDeleteAttendanceTimeline/' ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="tab-pane" id="t_status">
            <div class="box-body bg-gray-light">
                <ul class="timeline">
                    <?php
                    $aux = "";
                    foreach ($timelineView['status'] as $item) {
                        $currentDate = date("d/m/Y", strtotime($item->created_at));
                        $hour = date("H:i", strtotime($item->created_at));

                        if ($aux == "" || $aux != $currentDate) {
                            $aux = $currentDate;
                    ?>
                            <li class="time-label">
                                <span class="bg-aqua"><?= $aux ?></span>
                            </li>
                        <?php } ?>
                        <li style="margin-right: 0px;">
                            <i class="<?= $item->icon ?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock">
                                    </i> <?= $hour ?>
                                </span>
                                <h3 class="timeline-header" style="font-weight: 600; color: #0073b7">
                                    <?= $item->user_name ?>
                                </h3>
                                <div class="timeline-body" style="padding-bottom: 0px;">
                                    <?= $item->comment ?>
                                </div>
                                <div class="timeline-footer">
                                    <?php if (!empty($item->url_attachment)) { ?>
                                        <a href="<?= $item->url_attachment ?>" class="btn btn-warning btn-xs" download="">+
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                    <?php } ?>
                                    <a style="margin-right: 3px; color: #fff;"><i class="fas fa-circle btn-xs"></i></a>
                                    <?php if (Secure::access_secretary()) { ?>
                                        <button type="button" class="btn btn-danger btn-xs pull-right btn-disable-item" bodyHtml="Deseja realmente excluir esse Comentário?" title="Excluir comentário" style="margin-bottom: 3px;" id="<?= $item->id ?>" sendTo="<?= $this->route . '/handleDeleteAttendanceTimeline/' ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="tab-pane" id="t_registration">
            <div class="box-body bg-gray-light">
                <ul class="timeline">
                    <?php
                    $aux = "";
                    foreach ($timelineView['registration'] as $item) {
                        $currentDate = date("d/m/Y", strtotime($item->created_at));
                        $hour = date("H:i", strtotime($item->created_at));

                        if ($aux == "" || $aux != $currentDate) {
                            $aux = $currentDate;
                    ?>
                            <li class="time-label">
                                <span class="bg-aqua"><?= $aux ?></span>
                            </li>
                        <?php } ?>
                        <li style="margin-right: 0px;">
                            <i class="<?= $item->icon ?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock">
                                    </i> <?= $hour ?>
                                </span>
                                <h3 class="timeline-header" style="font-weight: 600; color: #0073b7">
                                    <?= $item->user_name ?>
                                </h3>
                                <div class="timeline-body" style="padding-bottom: 0px;">
                                    <?= $item->comment ?>
                                </div>
                                <div class="timeline-footer">
                                    <?php if (!empty($item->url_attachment)) { ?>
                                        <a href="<?= $item->url_attachment ?>" class="btn btn-warning btn-xs" download="">+
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                    <?php } ?>
                                    <a style="margin-right: 3px; color: #fff;"><i class="fas fa-circle btn-xs"></i></a>
                                    <?php if (Secure::access_secretary()) { ?>
                                        <button type="button" class="btn btn-danger btn-xs pull-right btn-disable-item" bodyHtml="Deseja realmente excluir esse Comentário?" title="Excluir comentário" style="margin-bottom: 3px;" id="<?= $item->id ?>" sendTo="<?= $this->route . '/handleDeleteAttendanceTimeline/' ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>

    </div>
</div>

</div>
</div>
</div>
</div>
</section>
</div>
