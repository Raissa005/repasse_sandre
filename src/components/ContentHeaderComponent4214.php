<?php

namespace RR\components;

class ContentHeaderComponent4214
{
    public $data;

    public function __construct($data)
    {
        $data->route = isset($data->route) ? $data->route : '#';
        $data->title = isset($data->title) ? $data->title : 'Título';
        $data->caption = isset($data->caption) ? $data->caption : '';

        if (isset($data->buttons) && !empty($data->buttons)) {
            array_map(function ($button) {
                $button->href = isset($button->href) ? "href='$button->href'" : "";
                $button->size = isset($button->size) ? "btn-$button->size" : "btn-sm";
                $button->color = isset($button->color) ? "btn-$button->color" : "btn-default";
                $button->icon = isset($button->icon) ? "<i class='$button->icon'></i>" : "";
                $button->text = isset($button->text) ? $button->text : "";
                $button->class = isset($button->class) ? $button->class : "";
                
                $htmlAttr = '';
                if (isset($button->attr)) {
                    foreach ($button->attr as $key => $value) {
                        $htmlAttr .= "$key='$value' ";
                    }
                }
                $button->attr = $htmlAttr;
            }, $data->buttons);
        }

        $this->data = $data;
        $this->render();
    }

    private function render()
    {
?>
        <section class="content-header">
            <h1>
                <a href="<?= $this->data->route ?>" class="text-black"><?= $this->data->title ?></a>
                <small><?= $this->data->caption ?></small>
                <?php if (isset($this->data->buttons) && !empty($this->data->buttons)) { ?>
                    <div class="pull-right">
                        <?php foreach ($this->data->buttons as $button) { ?>
                            <a <?= $button->href ?> class="btn <?= $button->size ?> <?= $button->color ?> <?= $button->class ?>" <?= $button->attr ?>><?= $button->icon ?> <?= $button->text ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </h1>
        </section>

<?php }
}
?>
