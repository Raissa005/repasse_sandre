<div class="col-xs-12 col-md-8">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="<?= !isset($_GET['properties']) && !isset($_GET['interests']) ? "active" : "" ?>">
                <a href="<?= URL . $this->route . "/attendance/" . $attendanceId; ?>">Timeline</a>
            </li>
            <li class="<?= isset($_GET['interests']) ? "active" : "" ?>">
                <a href="<?= URL . $this->route . "/attendance/" . $attendanceId; ?>?interests">Interesses</a>
            </li>
            <li class="<?= isset($_GET['properties']) ? "active" : "" ?>">
                <a href="<?= URL . $this->route . "/attendance/" . $attendanceId; ?>?properties">Imóveis Apresentados</a>
            </li>
            <?php if (isset($_GET['properties'])) { ?>
                <li class="pull-right">
                    <button class="btn btn-info" id="modal-add-properties-presentations-modal" data-id-attendance="<?= $attendanceId ?>" data-toggle="modal" data-target="#add-properties-presentations-modal">
                        Apresentar novos Imóveis
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
