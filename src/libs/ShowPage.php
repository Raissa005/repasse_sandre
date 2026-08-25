<?php

namespace RR\libs;

class ShowPage
{
    static function show($page, $rows, $count)
    {
        $start = (($page - 1) * $rows) + 1;
        if ($page >= (intval($count / $rows) + 1)) {
            $end = $count;
        } else {
            $end = $page * $rows;
        }
        return (object)['start' => $start, 'end' => $end, 'count' => $count];
    }
}
