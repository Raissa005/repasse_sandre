<?php

use RR\components\ContentHeaderComponent4214;
use RR\components\NavTabsComponent;

?>
<div class="content-wrapper">
    <?php new ContentHeaderComponent4214($contentHeader) ?>
    <section class="content">
        <div class="nav-tabs-custom">
            <?php new NavTabsComponent($navTabs); ?>
        </div>

        <div class="row">
            <div class="col-md-12">
                <ul class="timeline">
                    <?php foreach ($log->data as $log) { ?>
                        <?php if ($log->created_at != $log->lastTime) { ?>
                            <li class="time-label">
                                <span class="<?= $log->bg ?>">
                                    <?= "{$log->day} {$log->month}. {$log->year}" ?>
                                </span>
                            </li>
                        <?php } ?>
                        <li>
                            <i class="fa <?= $log->icon . ' ' . $log->iconBg ?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock"></i> <?= $log->hour ?></span>
                                <h3 class="timeline-header"><a href="#"><?= $log->name ?></a> <?= !$log->paid ? 'recebeu' : 'pagou' ?> <span class="<?= $log->text_color ?>"><?= $log->amount ?></span></h3>

                                <div class="timeline-body">
                                    <?= $log->text ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    <li>
                        <i class="fa fa-clock bg-gray"></i>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>