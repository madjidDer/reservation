<?php
declare(strict_types=1);

require_once __DIR__ . '/logger.php';

function rate_limit_check(string $key, int $maxAttempts, int $windowSeconds): bool
{
    $dir = __DIR__ . '/../../logs/ratelimit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $file = $dir . '/' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $key) . '.json';
    $now = time();

    $data = ['start' => $now, 'count' => 0];
    if (is_file($file)) {
        $raw = @file_get_contents($file);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        if (is_array($decoded) && isset($decoded['start'], $decoded['count'])) {
            $data = $decoded;
        }
    }

    $start = (int)($data['start'] ?? $now);
    $count = (int)($data['count'] ?? 0);

    if (($now - $start) > $windowSeconds) {
        $start = $now;
        $count = 0;
    }

    $count++;
    $data = ['start' => $start, 'count' => $count];
    @file_put_contents($file, json_encode($data));

    return $count <= $maxAttempts;
}

function rate_limit_key(string $prefix): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return $prefix . '_' . $ip;
}
