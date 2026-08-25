<?php

namespace RR\components;

class BadgeComponent
{
    function __construct($badge)
    {
        $this->render($badge->bg, $badge->text);
    }

    private function render(string $bg, string $text)
    {
?>
        <span class="label label-<?= $bg ?>"><?= $text ?></span>
<?php
    }
}
