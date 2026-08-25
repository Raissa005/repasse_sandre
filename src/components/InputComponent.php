<?php

namespace RR\components;

class InputComponent
{
    function __construct(object $input)
    {
        if (!isset($input->tag) || (isset($input->tag) && empty($input->tag))) {
            $input->tag = 'input';
        }

        $attrs = '';
        $attrs .= ' id="' . $input->id . '"';

        if (isset($input->name) && !empty($input->name)) {
            $attrs .= ' name="' . $input->name . '"';
        }

        $attrs .= ' class="form-control';
        if (isset($input->class) && !empty($input->class)) {
            $attrs .= ' ' . $input->class;
        }
        $attrs .= '"';

        if (isset($input->value) && !empty($input->value)) {
            $attrs .= ' value="' . $input->value . '"';
        }

        if (isset($input->attrs) && !empty($input->attrs) && is_array($input->attrs)) {
            foreach ($input->attrs as $key => $value) {
                $attrs .= ' ' . $key . '="' . $value . '"';
            }
        }

        if ($input->tag == 'input') {
            if (isset($input->type) && !empty($input->type)) {
                $attrs .= ' type="' . $input->type . '"';
            } else {
                $attrs .= ' type="' . 'text' . '"';
            }
        } else if ($input->tag == 'select') {
        }

        $input->options = isset($input->options) && !empty($input->options) ? $input->options : [];

        $this->render($input->tag, $attrs, $input->options);
    }

    private function render(string $tag, string $attrs, array $options)
    {
        if ($tag == 'input') {
?>
            <input <?= $attrs ?>>
        <?php
        } else if ($tag == 'select') {
        ?>
            <select <?= $attrs ?>>
                <?php foreach ($options as $option) { ?>
                    <option value="<?= $option->value ?>" <?= $option->active ? 'active' : '' ?>><?= $option->text ?></option>
                <?php } ?>
            </select>
        <?php
        }
        ?>
<?php
    }
}
