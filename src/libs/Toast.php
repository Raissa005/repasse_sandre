<?php

namespace RR\libs;

class Toast
{
    public static function checkResponse(bool $error, string $message): void
    {
        !$error ? Toast::successToast($message) : Toast::errorToast($message);
    }

    public static function genericToast(string $icon, string $message): void
    {
        $_SESSION['RR']->toast = (object)[
            'icon' => $icon,
            'title' => $message,
        ];
    }

    public static function errorToast(string $message): void
    {
        self::genericToast('error', $message);
    }

    public static function successToast(string $message): void
    {
        self::genericToast('success', $message);
    }

    public static function warningToast(string $message): void
    {
        self::genericToast('warning', $message);
    }
}
