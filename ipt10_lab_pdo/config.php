<?php
declare(strict_types=1);

$host = '127.0.0.1';
$db   = 'ip10_lab';

try {
    $pdo = new PDO(
        // TODO(21): DSN built from the same host/db values used with mysqli
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            // TODO(22): manual transaction control. Every write page must now
            // beginTransaction() ... commit() (or rollBack() on error), otherwise
            // the change is discarded when the connection closes.
            PDO::ATTR_AUTOCOMMIT         => false,
        ]
    );
} catch (PDOException $e) {
    error_log('DB connect failed: ' . $e->getMessage());
    die('Database unavailable');
}
