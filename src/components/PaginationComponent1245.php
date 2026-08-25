<?php

namespace RR\components;

class PaginationComponent1245
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->render();
    }

    private function render()
    {
?>
        <input type="hidden" id="page" value="<?= $this->data->page ?>">
        <ul class="pagination pagination-sm no-margin">
            <?php if ($this->data->page > 1) { ?>
                <li><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($this->data->page - 1) ?>">&laquo;</a></li>
            <?php } ?>
            <?php for ($i = $this->data->min; $i <= $this->data->max; $i++) { ?>
                <li class="<?= $this->data->page == $i ? "active" : " " ?>"><a href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . $i ?>"><?= $i ?></a></li>
            <?php } ?>
            <?php if ($this->data->page < $this->data->max) { ?>
                <li><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($this->data->page + 1) ?>">&raquo;</a></li>
            <?php } ?>
        </ul>
<?php }
}
?>
