<?php
/**
 * Central session bootstrap.
 *
 * Goal: keep the same PHP session across /frontend and /backend paths in Apache (XAMPP),
 * so navigating to admin pages doesn't "lose" the logged-in user.
 */

require_once __DIR__ . '/app.php';

if (session_status() === PHP_SESSION_NONE) {
    // Use a dedicated session name to avoid collisions with other apps on localhost.
    session_name('vacation_booking');

    // Force a wide cookie path so both /vacation-booking/frontend and /vacation-booking/backend share it.
    // Keep it conservative: don't force Secure, because many local setups are plain http.
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}
