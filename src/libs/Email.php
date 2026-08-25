<?php

namespace RR\libs;

class Email
{
    public $subject;
    public $message;
    public $cc;
    public $bcc;

    public function __construct($subject = "", $message = "", $cc = [], $bcc = [])
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }
}
