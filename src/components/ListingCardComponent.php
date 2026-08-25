<?php

namespace RR\components;

class ListingCardComponent
{
    function __construct(object $listing_card)
    {
        $listing_card->title = isset($listing_card->title) && !empty($listing_card->title) ? $listing_card->title : 'Listagem';

        $this->render($listing_card->title, $listing_card->table, $listing_card->pagination);
    }

    private function render(string $title, object $table, object $pagination)
    {
?>
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= $title ?></h3>
            </div>
            <div class="box-body no-padding">
                <?php new TableComponent($table) ?>
            </div>
            <div class="box-footer clearfix text-center">
                <?php new PaginationComponent1245($pagination); ?>
            </div>
        </div>
<?php
    }
}
