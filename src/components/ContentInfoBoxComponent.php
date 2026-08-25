<?php

namespace RR\components;

class ContentInfoBoxComponent
{
    function __construct(array $info_boxs)
    {
        $this->render($info_boxs);
    }

    private function render($info_boxs)
    {
?>
        <div class="row">
            <?php foreach ($info_boxs as $info_Box) {
                new InfoBoxComponent($info_Box);
            } ?>
        </div>
<?php
    }
}
