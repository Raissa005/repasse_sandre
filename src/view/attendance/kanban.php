<?php

use RR\libs\Date; ?>
<div id="doubleScroll" class="kanban-fluid">
    <div id="doubleDiv" class="kanban">
        <?php foreach ($attendanceKanban as $status) { ?>
            <div class="kabanColumn">
                <div class="box boxKabanColumn" style="border-top-color: <?= $status->box_color ?>;">
                    <div class="box-header with-border">
                        <i class="<?= $status->icon_status ?>"></i>
                        <h4 class="box-title"><?= $status->status_name ?></h4>
                        <div class="box-tools pull-rigth">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body kanban-column <?= $status->statusId == 10 || $status->statusId == 11 ? "disableAttendance" : "" ?> " data-status-name="<?= $status->statusId ?>">
                        <?php foreach ($status->attendances as $attendance) {
                            $returnDateColor = "";
                            $attendance->time_return_date = strtotime(str_replace("/", "-", Date::date($attendance->return_date) . " 00:00:00"));
                            $today = strtotime(date("Y-m-d 00:00:00"));

                            if (!empty($attendance->return_date) && $attendance->return_date != "0000-00-00 00:00:00") {
                                if ($attendance->time_return_date > $today) {
                                    $returnDateColor = "aqua";
                                } else if ($attendance->time_return_date < $today) {
                                    $returnDateColor = "red";
                                } else if ($attendance->time_return_date == $today) {
                                    $returnDateColor = "orange";
                                }
                            } else {
                                $returnDateColor = "lightSlateGray";
                            }
                        ?>
                            <div class="card" data-attendance-id="<?= $attendance->id ?>" style="border-left-color: <?= $returnDateColor ?>;">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h5><?= ucwords(mb_strtolower($attendance->name), " ") ?></h5>
                                        <span title="<?= $attendance->user_name ?>"><img src="<?= $attendance->profile_capa ? URL . "img/users/{$attendance->created_by}/{$attendance->created_by}-profile-{$attendance->profile_cont}.{$attendance->profile_ext}" : URL . "img/users/default/img-user-default.png" ?>" alt="<?= $attendance->user_name ?>"></span>
                                    </div>
                                    <h6 class="card-subtitle text-muted">
                                        <div class="date text-<?= $returnDateColor ?>"><?= "<i class='fas fa-calendar-day'></i> " . (!empty($attendance->return_date) && $attendance->return_date != "0000-00-00 00:00:00" ? Date::date($attendance->return_date) : "--/--/----")  ?></div>
                                        <div class="time text-<?= $returnDateColor ?>"><?= "<i class='fas fa-clock'></i> " . (!empty($attendance->return_date) && $attendance->return_date != "0000-00-00 00:00:00" ? Date::hour($attendance->return_date) : "--:--") ?></div>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="classification">
                                        <i class="text-yellow <?= $attendance->classification >= 1 ? "fas" : "far" ?> fa-star"></i>
                                        <i class="text-yellow <?= $attendance->classification >= 2 ? "fas" : "far" ?> fa-star"></i>
                                        <i class="text-yellow <?= $attendance->classification >= 3 ? "fas" : "far" ?> fa-star"></i>
                                        <i class="text-yellow <?= $attendance->classification >= 4 ? "fas" : "far" ?> fa-star"></i>
                                        <i class="text-yellow <?= $attendance->classification >= 5 ? "fas" : "far" ?> fa-star"></i>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="card-features">
                                        <a href="<?= URL . $this->route . "/attendance/$attendance->id?interests=true" ?>" title="Interesse" class="card-link btn btn-xs bg-aqua" style="position: relative;">
                                            <i class="fas fa-filter"></i>
                                        </a>

                                        <a href="<?= URL . $this->route . "/attendance/$attendance->id?vehicles=true" ?>" title="Veículos Apresentados" class="card-link btn btn-xs bg-yellow" style="position: relative;">
                                            <i class="fas fa-binoculars"></i>
                                        </a>

                                    </div>
                                    <div class="card-tools">
                                        <?php if (!empty($attendance->email)) { ?>
                                            <a href="mailto:<?= mb_strtolower($attendance->email) ?>" title="Email" class="card-link btn btn-xs bg-red">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if (!empty($attendance->phone)) { ?>
                                            <a href="tel:<?= $attendance->phone ?>" title="Telefone" class="card-link btn btn-xs bg-light-blue">
                                                <i class=" fas fa-phone-alt"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if (!empty($attendance->phone_whatsapp)) { ?>
                                            <a href="<?= "https://api.whatsapp.com/send/?phone=55$attendance->phone_whatsapp" ?>" target="_blank" title="WhatsApp" class="card-link btn btn-xs bg-green">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        <?php } ?>
                                        <a href="<?= URL . $this->route . "/attendance/$attendance->id" ?>" title="Editar" class="card-link btn btn-xs bg-blue">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
