<?php

/* Detect localhost */
$is_local =
    $_SERVER['HTTP_HOST'] === 'localhost'
    || $_SERVER['SERVER_NAME'] === 'localhost';

/* Hide PHP errors on live server */
if (!$is_local) {

    ini_set('display_errors', 0);
    error_reporting(0);

}

if (session_status() === PHP_SESSION_NONE) {
    session_start();

    // If the user is NOT logged in, treat them as a guest
    if (!isset($_SESSION['user_id'])) {

        // If this guest does not yet have a token, generate one
        if (!isset($_SESSION['guest_token'])) {

            // Generate a cryptographically secure random token (32 bytes → 64 hex characters)
            $_SESSION['guest_token'] = bin2hex(random_bytes(32));
        }
    }
}

$host = "DB_HOST";
$user = "DB_USER";
$pass = "DB_PASSWORD";
$db   = "DB_NAME";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
}
catch (mysqli_sql_exception $e) {

    error_log($e->getMessage());

    $error_type = "db";
    require_once __DIR__ . "/error.php";
    exit;
}