<?php

namespace RR\components;

class ButtonComponent
{
    function __construct(object $button)
    {
        $attrs = '';
        $text = '';

        if (isset($button->icon) && !empty($button->icon)) {
            $text .= '<i class="' . $button->icon . '"></i>';
        }

        if (isset($button->text) && !empty($button->text)) {
            $text .= " $button->text";
        }

        /**Href */
        if (isset($button->href) && !empty($button->href)) {
            $attrs .= ' href="' . $button->href . '"';
        }

        /**Id */
        if (isset($button->id) && !empty($button->id)) {
            $attrs .= ' id="' . $button->id . '"';
        }

        /**Name */
        if (isset($button->name) && !empty($button->name)) {
            $attrs .= ' name="' . $button->name . '"';
        }

        /**Title */
        if (isset($button->title) && !empty($button->title)) {
            $attrs .= ' title="' . $button->title . '"';
        }

        /**Target */
        if (isset($button->target) && !empty($button->target)) {
            $attrs .= ' target="' . $button->target . '"';
        }

        if (isset($button->styles) && !empty($button->styles) && is_array($button->styles)) {
            $attrs .= ' style="';
            foreach ($button->styles as $key => $value) {
                $attrs .= ' ' . $key . ': ' . $value . ';';
            }
            $attrs .= '"';
        }

        if (isset($button->attrs) && !empty($button->attrs) && is_array($button->attrs)) {
            foreach ($button->attrs as $key => $value) {
                $attrs .= ' ' . $key . '="' . $value . '"';
            }
        }

        /**Class */
        $attrs .= ' class="btn';
        if (
            (isset($button->size) && !empty($button->size)) ||
            (isset($button->color) && !empty($button->color)) ||
            (isset($button->bg) && !empty($button->bg)) ||
            (isset($button->outline) && $button->outline == true) ||
            (isset($button->class) && !empty($button->class))
        ) {
            if ((isset($button->size) && !empty($button->size))) {
                $attrs .= ' btn-' . $button->size . '';
            }

            if ((isset($button->color) && !empty($button->color))) {
                $attrs .= ' text-' . $button->colors . '';
            }

            if ((isset($button->bg) && !empty($button->bg))) {
                $attrs .= ' btn-' . $button->bg . '';
            }

            if ((isset($button->outline) && $button->outline == true)) {
                $attrs .= ' btn-outline';
            }

            if ((isset($button->class) && !empty($button->class))) {
                $attrs .= " $button->class";
            }
        }
        $attrs .= '"';

        $this->render($text, $attrs);
    }

    private function render(string $text, string $attrs)
    {
?>
        <a <?= $attrs ?>><?= $text ?></a>
<?php
    }
}
