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

    public static function infoToast(string $message): void
    {
        self::genericToast('info', $message);
    }

    public static function itemAdded(): void
    {
        self::successToast('Item cadastrado com sucesso');
    }

    public static function itemAddError(): void
    {
        self::errorToast('Não foi possível cadastrar o Item');
    }

    public static function itemEdited(): void
    {
        self::successToast('Item alterado com sucesso');
    }

    public static function itemEditError(): void
    {
        self::errorToast('Não foi possível editar o item');
    }

    public static function itemDeleted(): void
    {
        self::successToast('Item deletado com sucesso');
    }

    public static function itemDeleteError(): void
    {
        self::errorToast('Erro ao deletar esse item');
    }

    public static function itemDisabled(): void
    {
        self::successToast('Item desativado com sucesso');
    }

    public static function itemEnabled(): void
    {
        self::successToast('Item ativado com sucesso');
    }

    public static function itemExists(): void
    {
        self::warningToast('Não foi possível editar, este item já está cadastrado');
    }

    public static function unauthorized(): void
    {
        self::errorToast('Você não tem autorização para alterar e visualizar esse Item');
    }

    public static function genericError(): void
    {
        self::errorToast('Ops! Algo de errado aconteceu');
    }

    /**
     * Imprime (e consome) o toast pendente na sessão. Chamado uma vez no
     * layout comum (footer) para que nenhuma view precise chamar nada.
     */
    public static function render(): void
    {
        if (empty($_SESSION['RR']->toast)) {
            return;
        }

        $toast = $_SESSION['RR']->toast;
        unset($_SESSION['RR']->toast);

        echo '<script>Toast.fire(' . json_encode([
            'icon' => $toast->icon,
            'title' => $toast->title,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ');</script>';
    }
}
