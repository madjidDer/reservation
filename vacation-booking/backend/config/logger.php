<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

function app_log(string $message, array $context = []): void
{
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    $line = '[' . date('c') . '] ' . $message;
    if (!empty($context)) {
        $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    $line .= PHP_EOL;

    error_log($line, 3, $logDir . '/app.log');
}
