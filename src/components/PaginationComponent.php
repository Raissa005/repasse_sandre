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
?>
        <input type="hidden" id="page" value="<?= $page ?>">
        <ul class="pagination pagination-sm no-margin pull-right">
            <?php if ($page > 1) { ?>
                <li class="page-item"><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($page - 1) ?>">&laquo;</a></li>
            <?php } ?>
            <?php for ($i = $min; $i <= $max; $i++) { ?>
                <li class="page-item <?= $page == $i ? "active" : " " ?>"><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . $i ?>"><?= $i ?></a></li>
            <?php } ?>
            <?php if ($page < $max) { ?>
                <li class="page-item"><a class="page-link" href="<?= preg_replace('/(&page=[0-9]{1,}){1}/', '', $_SERVER['REQUEST_URI']) . '&page=' . ($page + 1) ?>">&raquo;</a></li>
            <?php } ?>
        </ul>
<?php
    }
}
