<?php

namespace RR\libs;

class TableDefault
{
    private $head;
    private $data;

    function __construct(array $head, array $data)
    {
        $this->data = $data;
        $this->head = $head;
    }

    public function model01()
    {
        return (object)[
            'config' => (object)[
                'responsive' => true,
                'condensed' => true,
                'bordered' => true,
                'striped' => true,
                'class' => 'text-nowrap',
            ],
            'thead' => (object)[
                'tr' => (object)[
                    'th' => $this->head
                ]
            ],
            'tbody' => (object)[
                'items' => $this->data
            ],
        ];
    }
}
