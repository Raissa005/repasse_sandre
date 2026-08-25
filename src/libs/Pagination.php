<?php

namespace RR\libs;

class Pagination
{

    public static function getPage()
    {
        return isset($_GET['page']) ? $_GET['page'] : 1;
    }

    public function pages($numberOfPages, $rows = 20,  $numPages = 10, $page = null)
    {
        $count = ceil($numberOfPages / $rows);
        $numPages = $numPages < 3 ? 3 : $numPages;
        if (!$page) {
            $page = self::getPage();
        }
        $cmin = 0;
        $cmax = 0;

        /**page max */
        if ($page <= $count - (floor($numPages / 2))) {
            $max = $page + (floor($numPages / 2));
        } else {
            $max = $count;
            $cmin = $page - ($count - (floor($numPages / 2)));
        }

        /**page min */
        if ($page >= (ceil($numPages / 2))) {
            $min = $page - (floor($numPages / 2) - ($numPages % 2 == 0 ? 1 : 0));
        } else {
            $min = 1;
            $cmax = (ceil($numPages / 2)) - $page;
        }

        $max = $max + $cmax > $count ? $count : $max + $cmax;
        $min = $min - $cmin < 1 ? 1 : $min - $cmin;

        return (object)["page" => $page, "max" => intval($max), "min" => intval($min)];
    }

    // function to show the amount of items returned in SQL being displayed per page.
    public function listItemsOnPage($count, $pagination, $rows)
    {
        if ($pagination->page < $pagination->max) {
            $max = ($count - ($count - ($pagination->page * $rows)));
            $min = (($pagination->page * $rows) - $rows) + 1;

            return (object)["min" => $min, "max" => $max, "total" => $count];
        } else {
            $max = $count;
            $min = (($pagination->page * $rows) - $rows) + 1;

            return (object)["min" => $min, "max" => $max, "total" => $count];
        }
    }
}
