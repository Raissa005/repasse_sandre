<?php

namespace RR\components;

class ContentHeaderComponent
{
    function __construct(object $contentHeader)
    {
        $contentHeader->buttons = isset($contentHeader->buttons) && !empty($contentHeader->buttons) ? $contentHeader->buttons : [];
        $this->render($contentHeader->title, $contentHeader->subtitle, $contentHeader->buttons);
    }

    private function render(string $title, string $subtitle = '', array $buttons = [])
    {
?>
        <section class="content-header">
            <h1>
                <a href="#" class="text-black"><?= $title ?></a>
                <small class="text-black"><?= $subtitle ?></small>

                <div class="pull-right">
                    <?php foreach ($buttons as $button) {
                        new ButtonComponent($button);
                    } ?>
                </div>
            </h1>
        </section>
<?php
    }
}
