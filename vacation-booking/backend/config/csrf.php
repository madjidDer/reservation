<?php
declare(strict_types=1);

function csrf_init(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new RuntimeException('Session must be started before CSRF init');
    }

    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function csrf_token(): string
{
    csrf_init();
    return $_SESSION['csrf_token'];
}

function csrf_validate(?string $token): bool
{
    csrf_init();
    if (!is_string($token) || $token === '') {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
