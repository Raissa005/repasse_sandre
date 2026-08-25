<?php

use RR\components\ContentHeaderComponent;

?>

<div class="content-wrapper">
    <?php new ContentHeaderComponent($content_header) ?>
    <section class="content container-fluid">
        <div class="tab-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-warning">
                        <div class="box-body">
                            <form action="<?= URL . $this::ROUTE . "/download-files/" ?>" method="post" id="form-files">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="token">TOKEN</label>
                                        <select name="user" id="user">
                                            <option value="fab">Fabrício</option>
                                            <option value="mat">Mateus</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="pull">Pull Requests #</label>
                                        <select name="pull" id="pull">
                                            <?php foreach ($options as $option) { ?>
                                                <option value="<?= $option->number ?>"><?= ucfirst($option->state) ?> Pull Request nº <?= $option->number ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
