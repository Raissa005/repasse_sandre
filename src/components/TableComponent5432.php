<?php

namespace RR\components;

class TableComponent5432
{
    private $config;
    private $thead;
    private $data;

    /**
     * @param array $thead
     * @param array $data
     * @param object $config
     */
    public function __construct($thead, $data, $config)
    {
        $this->config = (object)[
            'responsive' => isset($config->responsive) && $config->responsive == true ? 'table-responsive' : '',
            'condensed' => isset($config->condensed) && $config->condensed == true ? ' table-condensed' : '',
            'bordered' => isset($config->bordered) && $config->bordered == true ? ' table-bordered' : '',
            'striped' => isset($config->striped) && $config->striped == true ? ' table-striped' : '',
        ];

        if (!empty($thead)) {
            foreach ($thead as $head) {
                if (!isset($head->column->link) || (isset($head->column->link) && empty($head->column->link))) {
                    return 'É necessário enviar o link da coluna';
                }
            }
        }
        $this->thead = $thead;

        $this->data = $data;
        $this->render();
    }

    private function render()
    {
?>
        <div class="<?= $this->config->responsive ?>">
            <table class="table<?= $this->config->condensed ?><?= $this->config->bordered ?><?= $this->config->striped ?>">
                <?php if (!empty($this->thead)) { ?>
                    <thead>
                        <?php foreach ($this->thead as $th) {
                            $htmlAttr = '';
                            if (isset($th->attr)) {
                                foreach ($th->attr as $key => $value) {
                                    $htmlAttr .= "$key='$value' ";
                                }
                            }
                            $th->attr = $htmlAttr;
                        ?>
                            <th style="<?= isset($th->style) ? $th->style : '' ?>" class="<?= isset($th->class) ? $th->class : '' ?>" <?= $th->attr ?>><?= isset($th->text) ? $th->text : '' ?></th>
                        <?php } ?>
                    </thead>
                    <?php if (!empty($this->data)) { ?>

                        <tbody>
                            <?php foreach ($this->data as $item) { ?>
                                <tr class="<?= isset($item->tr) ? $item->tr : '' ?>">
                                    <?php for ($i = 0; $i < count($this->thead); $i++) {
                                        $valueTd = $item->{$this->thead[$i]->column->link};

                                        if (isset($this->thead[$i]->column->type)) {
                                            if ($this->thead[$i]->column->type == 'label') {
                                                $item->{$this->thead[$i]->column->link}->color = isset($item->{$this->thead[$i]->column->link}->color) ? $item->{$this->thead[$i]->column->link}->color : 'default';
                                                $valueTd = "<span class='label label-{$item->{$this->thead[$i]->column->link}->color}'>{$item->{$this->thead[$i]->column->link}->value}</span>";
                                            } else if ($this->thead[$i]->column->type == 'button') {
                                                $valueTd = $item->{$this->thead[$i]->column->link};
                                                $valueTd = '';
                                                foreach ($item->{$this->thead[$i]->column->link} as $button) {
                                                    $button->href = isset($button->href) ? "href='{$button->href}'" : '';
                                                    $button->id = isset($button->id) ? $button->id : '';
                                                    $button->title = isset($button->title) ? $button->title : '';
                                                    $button->size = isset($button->size) ? 'btn-' . $button->size : '';
                                                    $button->color = isset($button->color) ? $button->color : 'default';
                                                    $button->class = isset($button->class) ? $button->class : '';
                                                    $button->target = isset($button->target) ? "target='{$button->target}'" : '';

                                                    $htmlAttr = '';
                                                    if (isset($button->attr)) {
                                                        foreach ($button->attr as $key => $value) {
                                                            $htmlAttr .= "$key='$value' ";
                                                        }
                                                    }
                                                    $button->attr = $htmlAttr;

                                                    $valueTd .= " <a {$button->href} id='{$button->id}' {$button->target} class='btn btn-{$button->color} {$button->size} {$button->class}' title='{$button->title}' {$button->attr}>";
                                                    if (isset($button->icon)) {
                                                        $valueTd .= "<i class='{$button->icon}'></i> ";
                                                    }
                                                    if (isset($button->text) && !empty($button->text)) {
                                                        $valueTd .= "$button->text";
                                                    }
                                                    $valueTd .= "</a> ";
                                                }
                                            } elseif ($this->thead[$i]->column->type == 'image') {
                                                $item->{$this->thead[$i]->column->link}->style = $item->{$this->thead[$i]->column->link}->style ?? '';
                                                $item->{$this->thead[$i]->column->link}->class = $item->{$this->thead[$i]->column->link}->class ?? '';
                                                $valueTd = "<img src='{$item->{$this->thead[$i]->column->link}->url}' class='{$item->{$this->thead[$i]->column->link}->class}' style='{$item->{$this->thead[$i]->column->link}->style}'>";
                                            }
                                        }
                                    ?>
                                        <td class="<?= isset($this->thead[$i]->class) ? $this->thead[$i]->class : '' ?>" style="<?= isset($this->thead[$i]->tdStyle) ? $this->thead[$i]->tdStyle : ''  ?>"><?= $valueTd ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>

                        </tbody>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
<?php
    }
}
?>
