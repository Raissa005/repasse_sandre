<?php

namespace RR\components;

class TableComponent
{
    function __construct(object $table)
    {

        $config = (object)['attrsHTML' => '', 'responsive' => ''];

        if (isset($table->config->responsive) && $table->config->responsive == true) {
            $config->responsive = 'class="table-responsive"';
        }

        /**Table id */
        if (isset($table->config->id) && !empty($table->config->id)) {
            $config->attrsHTML .= ' id="' . $table->config->id . '"';
        }

        /**Table style */
        if (isset($table->config->styles) && !empty($table->config->styles) && is_array($table->config->styles)) {
            $config->attrsHTML .= ' style="';
            foreach ($table->config->styles as $key => $value) {
                $table->config->attrsHTML .= ' ' . $key . ':' . $value . ';';
            }
            $config->attrsHTML .= '"';
        }

        /**Table attrs */
        if (isset($table->config->attrs) && !empty($table->config->attrs) && is_array($table->config->attrs)) {
            foreach ($table->config->attrs as $key => $value) {
                $config->attrsHTML .= ' ' . $key . '="' . $value . '"';
            }
        }

        /**Table Class */
        $config->attrsHTML .= ' class="table';
        if (
            (isset($table->config->condensed) && $table->config->condensed == true) ||
            (isset($table->config->bordered) && $table->config->bordered == true) ||
            (isset($table->config->striped) && $table->config->striped == true) ||
            (isset($table->config->hover) && $table->config->hover == true) ||
            (isset($table->config->fixed) && $table->config->fixed == true) ||
            (isset($table->config->class) && !empty($table->config->class))
        ) {
            if (isset($table->config->condensed) && $table->config->condensed == true) {
                $config->attrsHTML .= ' table-sm';
            }

            if (isset($table->config->bordered) && $table->config->bordered == true) {
                $config->attrsHTML .= ' table-bordered';
            }

            if (isset($table->config->striped) && $table->config->striped == true) {
                $config->attrsHTML .= ' table-striped';
            }

            if (isset($table->config->hover) && $table->config->hover == true) {
                $config->attrsHTML .= ' table-hover';
            }

            if (isset($table->config->class) && !empty($table->config->class)) {
                $config->attrsHTML .= ' ' . $table->config->class . '';
            }
        }
        $config->attrsHTML .= '"';

        $thead = (object)['attrsHTML' => ''];

        if ((isset($table->thead->class) && !empty($table->thead->class)) || (isset($table->thead->attrs) && !empty($table->thead->attrs))) {
            if (isset($table->thead->class) && !empty($table->thead->class)) {
                $thead->attrsHTML = ' class="' . $table->thead->class . '"';
            }

            if (isset($table->thead->attrs) && !empty($table->thead->attrs) && is_array($table->thead->attrs)) {
                foreach ($table->thead->attrs as $key => $value) {
                    $thead->attrsHTML = ' ' . $key . '="' . $value . '"';
                }
            }
        }

        $thead->tr = (object)['attrsHTML' => ''];
        if ((isset($table->thead->class) && !empty($table->thead->class)) || (isset($table->thead->attrs) && !empty($table->thead->attrs))) {
            if (isset($table->thead->class) && !empty($table->thead->class)) {
                $thead->tr->attrsHTML = ' class="' . $table->thead->class . '"';
            }

            if (isset($table->thead->attrs) && !empty($table->thead->attrs) && is_array($table->thead->attrs)) {
                foreach ($table->thead->attrs as $key => $value) {
                    $thead->tr->attrsHTML = ' ' . $key . '="' . $value . '"';
                }
            }
        }

        $thead->tr->th = [];
        foreach ($table->thead->tr->th as $th) {
            $tr = (object)['link' => $th->link, 'attrsHTML' => '', 'textHTML' => ''];

            if (isset($th->class) && !empty($th->class)) {
                $tr->attrsHTML .= ' class="' . $th->class . '"';
            }

            if (isset($th->styles) && !empty($th->styles) && is_array($th->styles)) {
                $tr->attrsHTML .= ' style="';
                foreach ($th->styles as $key => $value) {
                    $tr->attrsHTML .= ' ' . $key . ': ' . $value . ';';
                }
                $tr->attrsHTML .= '"';
            }

            if (isset($th->attrs) && !empty($th->attrs) && is_array($th->attrs)) {
                foreach ($th->attrs as $key => $value) {
                    $tr->attrsHTML .= ' ' . $key . '="' . $value . '"';
                }
            }

            if ((isset($th->text) && !empty($th->text)) || (isset($th->icon) && !empty($th->icon))) {
                if (isset($th->icon) && !empty($th->icon)) {
                    $tr->textHTML .= '<i class="' . $th->icon . '"></i>';
                }

                if (isset($th->text) && !empty($th->text)) {
                    $tr->textHTML .= ' ' . $th->text;
                }
            }
            array_push($thead->tr->th, $tr);
        }

        $tbody = (object)['attrsHTML' => ''];

        if (isset($table->tbody->class) && !empty($table->tbody->class)) {
            $tbody->attrsHTML = ' class="' . $table->tbody->class . '"';
        }

        if (isset($table->tbody->attrs) && !empty($table->tbody->attrs) && is_array($table->tbody->attrs)) {
            foreach ($table->tbody->attrs as $key => $value) {
                $tbody->attrsHTML = ' ' . $key . '="' . $value . '"';
            }
        }

        $tbody->items = [];
        foreach ($table->tbody->items as $item) {
            $item->trHTML = (object)['attrs' => ''];
            if (
                (isset($item->bg) && !empty($item->bg)) ||
                (isset($item->color) && !empty($item->color)) ||
                (isset($item->class) && !empty($item->class))
            ) {
                $item->trHTML->attrs .= ' class="';
                if (isset($item->bg) && !empty($item->bg)) {
                    $item->trHTML->attrs .= ' ' . $item->bg;
                }

                if (isset($item->color) && !empty($item->color)) {
                    $item->trHTML->attrs .= ' ' . $item->color;
                }

                if (isset($item->class) && !empty($item->class)) {
                    $item->trHTML->attrs .= ' ' . $item->class;
                }

                $item->trHTML->attrs .= '"';
            }

            for ($i = 0; $i < count($table->thead->tr->th); $i++) {

                if (!isset($table->thead->tr->th[$i]->field_type) || (isset($table->thead->tr->th[$i]->field_type) && empty($table->thead->tr->th[$i]->field_type))) {
                    $table->thead->tr->th[$i]->field_type = 'text';
                }

                $item->{$table->thead->tr->th[$i]->link} = (object)[
                    'field_type' => $table->thead->tr->th[$i]->field_type,
                    'value' => $item->{$table->thead->tr->th[$i]->link},
                    'attrsHTML' => $thead->tr->th[$i]->attrsHTML,
                ];
            }

            array_push($tbody->items, $item);
        }

        $this->render($config, $thead, $tbody);
    }

    private function render(object $table, object $thead, object $tbody)
    {
?>
        <div <?= $table->responsive ?>>
            <table <?= $table->attrsHTML ?>>
                <thead <?= $thead->attrsHTML ?>>
                    <tr <?= $thead->tr->attrsHTML ?>>
                        <?php foreach ($thead->tr->th as $th) { ?>
                            <th <?= $th->attrsHTML ?>><?= $th->textHTML ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody <?= $tbody->attrsHTML ?>>
                    <?php foreach ($tbody->items as $item) { ?>
                        <tr <?= $item->trHTML->attrs ?>>
                            <?php for ($i = 0; $i < count($thead->tr->th); $i++) { ?>
                                <td <?= $item->{$thead->tr->th[$i]->link}->attrsHTML ?>>
                                    <?php
                                    switch ($item->{$thead->tr->th[$i]->link}->field_type) {
                                        case 'badge':
                                            new BadgeComponent($item->{$thead->tr->th[$i]->link}->value);
                                            break;
                                        case 'image':
                                            new ImageComponent($item->{$thead->tr->th[$i]->link}->value);
                                            break;
                                        case 'action':
                                            foreach ($item->{$thead->tr->th[$i]->link}->value as $button) {
                                                new ButtonComponent($button);
                                            }
                                            break;
                                        case 'progress':
                                            /**dev */
                                            break;

                                        default:
                                            echo $item->{$thead->tr->th[$i]->link}->value;
                                            break;
                                    }
                                    ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
<?php
    }
}
