<?php

namespace RR\model;

use RR\core\Model;

/**
 * A4: tentativas de login por e-mail digitado (tabela `login_attempts`, migration 2026_10_02_0921).
 * Regra decidida pelo usuário (2026-10-02): 5 erros em até 15 minutos bloqueiam o e-mail por 15 minutos; login certo
 * ou recuperação de senha concluída apagam a linha. Conta também e-mails inexistentes, para não revelar quais existem.
 */
class LoginAttempt extends Model
{
    private $table;

    const MAX_ATTEMPTS = 5;
    const WINDOW_SECONDS = 15 * 60;
    const LOCK_SECONDS = 15 * 60;
    const LOCKED_MESSAGE = 'Muitas tentativas de login. Tente novamente em 15 minutos ou use "Esqueci a senha".';

    function __construct()
    {
        $this->table = 'login_attempts';
        parent::__construct($this->table, []);
    }

    /** E-mail como chave: minúsculo, sem espaços e no tamanho da coluna. */
    public static function normalizeEmail($email): string
    {
        return mb_substr(mb_strtolower(trim((string) $email), 'UTF-8'), 0, 191, 'UTF-8');
    }

    /** @param object|false $attempt linha de getByEmail() */
    public static function isLocked($attempt, int $now): bool
    {
        return !empty($attempt) && !empty($attempt->locked_until) && strtotime($attempt->locked_until) > $now;
    }

    /**
     * Estado depois de mais um erro: recomeça do 1 se não havia linha, se o último erro foi há mais de 15 minutos ou se
     * um bloqueio anterior já venceu; no 5º erro seguido, bloqueia por 15 minutos.
     * @param object|false $attempt linha de getByEmail()
     */
    public static function nextFailure($attempt, int $now): object
    {
        $attempts = 1;

        if (!empty($attempt) && empty($attempt->locked_until) && !empty($attempt->last_attempt_at)
            && $now - strtotime($attempt->last_attempt_at) <= self::WINDOW_SECONDS) {
            $attempts = (int) $attempt->attempts + 1;
        }

        return (object)[
            'attempts' => $attempts,
            'locked_until' => $attempts >= self::MAX_ATTEMPTS ? date('Y-m-d H:i:s', $now + self::LOCK_SECONDS) : null,
        ];
    }

    public function getByEmail(string $email)
    {
        $query = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $query->execute([':email' => $email]);

        return $query->fetch();
    }

    public function registerFailure(string $email, object $state, int $now): void
    {
        $sql = "INSERT INTO {$this->table} (email, attempts, last_attempt_at, locked_until)
                VALUES (:email, :attempts, :last_attempt_at, :locked_until)
                ON DUPLICATE KEY UPDATE
                    attempts = VALUES(attempts),
                    last_attempt_at = VALUES(last_attempt_at),
                    locked_until = VALUES(locked_until),
                    updated_at = VALUES(last_attempt_at)";

        $query = $this->db->prepare($sql);
        $query->execute([
            ':email' => $email,
            ':attempts' => $state->attempts,
            ':last_attempt_at' => date('Y-m-d H:i:s', $now),
            ':locked_until' => $state->locked_until,
        ]);
    }

    public function clear(string $email): void
    {
        $query = $this->db->prepare("DELETE FROM {$this->table} WHERE email = :email");
        $query->execute([':email' => $email]);
    }
}
