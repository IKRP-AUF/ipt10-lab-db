<?php
declare(strict_types=1);

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'ip10_lab';

// TODO(1): strict error reporting BEFORE connecting -> failures become mysqli_sql_exception
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // TODO(2): object-oriented mysqli constructor
    $conn = new mysqli($host, $user, $pass, $db);
    // TODO(3): agree the character set immediately after connecting
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('DB connect failed: ' . $e->getMessage());
    die('Database unavailable');
}
