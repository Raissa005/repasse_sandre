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
        // URL atual sem o "&page=N", escapada para o atributo href (a query vem crua do usuário)
        $url = htmlspecialchars(preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']), ENT_QUOTES, 'UTF-8');
?>
        <input type="hidden" id="page" value="<?= $this->data->page ?>">
        <ul class="pagination pagination-sm no-margin">
            <?php if ($this->data->page > 1) { ?>
                <li><a class="page-link" href="<?= $url . '&amp;page=' . ($this->data->page - 1) ?>">&laquo;</a></li>
            <?php } ?>
            <?php for ($i = $this->data->min; $i <= $this->data->max; $i++) { ?>
                <li class="<?= $this->data->page == $i ? "active" : " " ?>"><a href="<?= $url . '&amp;page=' . $i ?>"><?= $i ?></a></li>
            <?php } ?>
            <?php if ($this->data->page < $this->data->max) { ?>
                <li><a class="page-link" href="<?= $url . '&amp;page=' . ($this->data->page + 1) ?>">&raquo;</a></li>
            <?php } ?>
        </ul>
<?php }
}
?>
