<?php

namespace RR\components;

class ImageComponent
{
    function __construct($image)
    {
        $attrs = ' src="' . $image->url . '"';

        if (isset($image->alt) && !empty($image->alt)) {
            $attrs .= ' alt="' . $image->alt . '"';
        }

        if (isset($image->width) && !empty($image->width)) {
            $attrs .= ' width="' . $image->width . '"';
        }

        if (isset($image->class) && !empty($image->class)) {
            $attrs .= ' class="' . $image->class . '"';
        }

        $this->render($attrs);
    }

    private function render(string $attrs)
    {
?>
        <img <?= $attrs ?>>
<?php
    }
}
