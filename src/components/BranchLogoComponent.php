<?php

namespace RR\components;

class BranchLogoComponent
{
    function __construct(string $name, string $logoURL = '#', string $href = '#')
    {
        $this->render($name, $logoURL, $href);
    }

    private function render($name, $logoURL, $href)
    {
?>
        <a href="<?= $href ?>" class="brand-link">
            <img src="<?= $logoURL ?>" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light"><?= $name ?></span>
        </a>
<?php
    }
}
