<div class="bs-stepper">
    <div class="bs-stepper-header">
        <?php
        $active = "active";
        $lineActive = "line-active";
        foreach ($attendanceSteps as $key => $status) {
            if ($status->id == $attendance->id_status) {
                $lineActive = "";
            }
        ?>
            <div class="step">
                <button type="button" id="<?= $status->id ?>" <?= $status->id == $attendance->id_status ? " title='$status->name' " : "" ?> class="step-trigger <?= $active ?>  <?= $status->id != $attendance->id_status && ($attendance->id_status != 10 && $attendance->id_status != 11) ? "btn-attendance-status" : "" ?>">
                    <span class="bs-stepper-circle <?= $attendance->id_status == 10 && $status->id == 10 ? "bg-green" : '' ?>">
                        <i class="<?= $status->icon_status ?>"></i>
                    </span>
                    <span class="bs-stepper-label text-center text-sm">
                        <?= $status->name ?>
                    </span>
                </button>
            </div>
            <?php if ($key != count($attendanceSteps) - 1) { ?>
                <div class="line <?= $lineActive ?>"></div>
            <?php } ?>
        <?php
            if ($status->id == $attendance->id_status) {
                $active = "";
                $lineActive = "";
            }
        } ?>
    </div>
</div>

<div id="attendance-status-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Alterar Status de Atendimento!</h4>
            </div>
            <div class="modal-body">
                Você realmente deseja alterar o status do atendimento?
            </div>
            <form method="POST" action="<?= URL . $this->route . '/handleSubmitEditStatus/' . $attendanceId ?>" class="form-attendance-status">
                <div class="modal-footer">
                    <div class="pull-left">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>
                    </div>
                    <input type="hidden" id="id_status" name="id_status">
                    <button type="submit" class="btn btn-primary" id="btn-submit">Alterar</button>
                </div>
            </form>
        </div>
    </div>
</div>
