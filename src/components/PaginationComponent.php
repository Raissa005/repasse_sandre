<?php

namespace RR\components;

class PaginationComponent
{

    function __construct(object $pagination)
    {
        $this->render($pagination->page, $pagination->max, $pagination->min);
    }

    private function render(int $page, int $max, int $min)
    {
        // URL atual sem o "&page=N", escapada para o atributo href (a query vem crua do usuário)
        $url = htmlspecialchars(preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']), ENT_QUOTES, 'UTF-8');
?>
        <input type="hidden" id="page" value="<?= $page ?>">
        <ul class="pagination pagination-sm no-margin pull-right">
            <?php if ($page > 1) { ?>
                <li class="page-item"><a class="page-link" href="<?= $url . '&amp;page=' . ($page - 1) ?>">&laquo;</a></li>
            <?php } ?>
            <?php for ($i = $min; $i <= $max; $i++) { ?>
                <li class="page-item <?= $page == $i ? "active" : " " ?>"><a class="page-link" href="<?= $url . '&amp;page=' . $i ?>"><?= $i ?></a></li>
            <?php } ?>
            <?php if ($page < $max) { ?>
                <li class="page-item"><a class="page-link" href="<?= $url . '&amp;page=' . ($page + 1) ?>">&raquo;</a></li>
            <?php } ?>
        </ul>
<?php
    }
}
