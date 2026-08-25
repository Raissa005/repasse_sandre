<div class="content-wrapper">
    <section class="content container-fluid" style="padding-top: 0px;">
        <div class="row">
            <input type="hidden" id="id_attendance" value="<?= $attendance->id ?>">
            <div class="col-xs-12">
                <?php require APP . 'view/' . $this->dir . '/call-center/step.php'; ?>
                <div class="box-header with-border" style="padding-right: 0px;">
                    <h3 class="box-title" style="margin-top: 7px;"><?= $attendance->id . " - " . $attendance->name ?></h3>
                    <?php if (!$disabledStatus && !empty($attendance->return_date) && $attendance->return_date != "0000-00-00 00:00:00") { ?>
                        <h3 class="box-title pull-right <?= $bgReturnDate ?>" style="font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle; padding: 6px 12px; border-radius: 3px;"><?= $attendance->return_date2 ?></h3>
                    <?php } ?>
                </div>
                <div class="row">
                    <div class="col-md-4 col-lg-4 col-padding">
                        <!-- Ficha de Atendimento -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 style="margin-top: 4px;" class="box-title">Ficha de Atendimento</h3>
                                <a style="vertical-align: middle;" href="<?= URL . $this->route . "/editAttendance/$attendance->id" ?>" class="btn btn-primary btn-sm pull-right">Editar</a>
                            </div>
                            <div class="box-body no-padding">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <ul class="list-group" style="margin-bottom: 0px;">
                                            <li class="list-group-item"><i class="fas fa-calendar-plus"></i> <strong>Data Abertura:</strong><span class="pull-right"><?= $attendance->opening_date2 ?></span></li>
                                            <li class="list-group-item"><i class="fas fa-map-marked-alt"></i> <strong>Endereço:</strong><span class="pull-right"><?= ucwords(mb_strtolower($attendance->name_city), " ") . " - " . $attendance->state ?></span></li>
                                            <?php if (isset($attendance->email) && !empty($attendance->email) && $attendance->email != "") { ?>
                                                <li class="list-group-item"><i class="fas fa-envelope"></i> <strong>Email:</strong><span class="pull-right"><a href="mailto:<?= strtolower($attendance->email) ?>"><?= strtolower($attendance->email) ?></a></span></li>
                                            <?php } ?>
                                            <li class="list-group-item"><i class="fas fa-user"></i> <strong>Usuário:</strong><span class="pull-right"><?= $attendance->user_name ?></span></li>
                                            <li class="list-group-item"><i class="fas fa-network-wired"></i> <strong>Como Conheceu:</strong><span class="pull-right"><?= $attendance->communication_channel ?></span></li>
                                            <?php if (isset($attendance->description) && !empty($attendance->description) && $attendance->description != "") { ?>
                                                <li class="list-group-item"><i class="fas fa-comment-alt"></i> <strong>Descrição:</strong></li>
                                                <li class="list-group-item"><?= $attendance->description ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Qualificação -->

                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Qualificação do Atendimento</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <form role="form" action="<?= URL . $this->route . '/handleSubmitClassification/' . $attendanceId ?>" method="POST">
                                        <div class="col-xs-9">
                                            <div class="form-group" style="margin-bottom: 0px;">
                                                <div class="box-classification">
                                                    <i style="font-size: 1.5rem;" id="star-1" class="star text-yellow <?= $attendance->classification >= 1 ? "fas" : "far" ?> fa-star"></i>
                                                    <i style="font-size: 1.5rem;" id="star-2" class="star text-yellow <?= $attendance->classification >= 2 ? "fas" : "far" ?> fa-star"></i>
                                                    <i style="font-size: 1.5rem;" id="star-3" class="star text-yellow <?= $attendance->classification >= 3 ? "fas" : "far" ?> fa-star"></i>
                                                    <i style="font-size: 1.5rem;" id="star-4" class="star text-yellow <?= $attendance->classification >= 4 ? "fas" : "far" ?> fa-star"></i>
                                                    <i style="font-size: 1.5rem;" id="star-5" class="star text-yellow <?= $attendance->classification >= 5 ? "fas" : "far" ?> fa-star"></i>
                                                </div>
                                                <input type="hidden" name="classification" id="classification" value="<?= $attendance->classification ?>">
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <button type="submit" class="btn btn-warning pull-right">Salvar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Status de Atendimento -->

                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Status do Atendimento</h3>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <form role="form" action="<?= URL . $this->route . '/handleSubmitEditStatus/' . $attendanceId ?>" method="POST" id="form-attendance-status">
                                        <div class="col-xs-9">
                                            <div class="form-group" style="margin-bottom: 0px;">
                                                <select class="form-control" name="id_status" id="id_status" <?= $attendance->id_status == 10 || $attendance->id_status == 11 ? "disabled" : "" ?>>
                                                    <?php foreach ($statusAttendance as $status) { ?>
                                                        <option value="<?= $status->id ?>" <?= $status->id == $attendance->id_status ? "selected" : "" ?>><?= $status->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if (!$disabledStatus) { ?>
                                            <div class="col-xs-3">
                                                <button type="submit" class="btn btn-primary pull-right">Salvar</button>
                                            </div>
                                        <?php } ?>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php require APP . 'view/' . $this->dir . '/call-center/phone.php'; ?>
                        <?php require APP . 'view/' . $this->dir . '/call-center/attachment.php'; ?>


                        <!-- Modals -->

                        <div id="disable-item-modal" class="modal fade" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Aviso!</h4>
                                    </div>
                                    <div class="modal-body">
                                        Deseja realmente excluir esse Item?
                                    </div>
                                    <form method="POST" class="form-disable-item">
                                        <div class="modal-footer">
                                            <div class="pull-left">
                                                <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                                            </div>
                                            <button type="submit" class="btn btn-danger" name="disable">Excluir</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
