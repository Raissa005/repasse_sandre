<?php

namespace RR\components;

class BoxInfoComponent
{
    public $data;

    public function __construct($data)
    {
        array_map(function ($item) {
            $item->bg = "bg-{$item->bg}";
        }, $data);
        
        $this->data = $data;
        $this->render();
    }

    private function render()
    {
?>
        <?php foreach ($this->data as $item) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box <?= $item->bg ?>">
                    <span class="info-box-icon ">
                        <i class="<?= $item->icon ?>"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-boc-text"><?= $item->text ?></span>
                        <span class="info-box-number"><?= $item->value ?></span>
                    </div>
                </div>
            </div>
        <?php } ?>
<?php }
}
?>
