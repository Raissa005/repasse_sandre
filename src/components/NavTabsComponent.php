<?php

namespace RR\components;

use RR\libs\Util;

class NavTabsComponent
{
    public $data;

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        array_map(function ($li) {
            $li->a_class = isset($li->a_class) ? $li->a_class : '';
            $li->route = isset($li->route) ? $li->route : '#';
            $li->class = isset($li->class) ? $li->class : '';
            $li->id = isset($li->id) ? $li->id : '';
            $li->text = isset($li->text) ? $li->text : '';
            $li->attr = isset($li->attr) ? implode(' ', array_map(function ($attr, $value) {
                return $attr . '="' . $value . '"';
            }, array_keys($li->attr), $li->attr)) : '';
        }, $data);

        $this->data = $data;
        $this->render();
    }

    private function render()
    {
?>
        <ul class="nav nav-tabs">
            <?php foreach ($this->data as $li) { ?>
                <li id="<?= $li->id ?>" class="<?= $li->class ?>" <?= $li->attr ?>>
                    <a href="<?= $li->route ?>" class="<?= $li->a_class ?>"><?= $li->text ?></a>
                </li>
            <?php } ?>
        </ul>
<?php }
}
?>
