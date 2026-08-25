<?php

namespace RR\components;

class InfoBoxComponent
{
    function __construct(object $info_box)
    {
        $info_box->shadow = isset($info_box->shadow) && !empty($info_box->shadow) ? $info_box->shadow : 'none';

        $this->render($info_box->text, $info_box->number, $info_box->icon, $info_box->bg, $info_box->shadow);
    }

    private function render(string $text, string $number, string $icon, string $bg, string $shadow)
    {
?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-<?= $bg ?>">
                <span class="info-box-icon ">
                    <i class="<?= $icon ?>"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-boc-text"><?= $text ?></span>
                    <span class="info-box-number"><?= $number ?></span>
                </div>
            </div>
        </div>
<?php
    }
}
