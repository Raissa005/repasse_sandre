<div class="col-xs-12 col-md-8">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="<?= !isset($_GET['vehicles']) && !isset($_GET['interests']) ? "active" : "" ?>">
                <a href="<?= URL . $this->route . "/attendance/" . $attendanceId; ?>">Timeline</a>
            </li>
            <li class="<?= isset($_GET['interests']) ? "active" : "" ?>">
                <a href="<?= URL . $this->route . "/attendance/" . $attendanceId; ?>?interests">Interesses</a>
            </li>
            <li class="<?= isset($_GET['vehicles']) ? "active" : "" ?>">
                <a href="<?= URL . $this->route . "/attendance/" . $attendanceId; ?>?vehicles">Veículos Apresentados</a>
            </li>
            <?php if (isset($_GET['vehicles'])) { ?>
                <li class="pull-right">
                    <button class="btn btn-info" id="modal-add-vehicles-presentations-modal" data-id-attendance="<?= $attendanceId ?>" data-toggle="modal" data-target="#add-vehicles-presentations-modal">
                        Apresentar novos Veículos
                    </button>
                </li>
            <?php } ?>
            <?php if (isset($_GET['interests'])) { ?>
                <li class="pull-right">
                    <button class="btn btn-info" id="modal-add-interest-modal" data-id-attendance="<?= $attendanceId ?>" data-toggle="modal" data-target="#add-interest-modal">
                        Adicionar Interesse
                        </button>
                </li>
            <?php } ?>
        </ul>
