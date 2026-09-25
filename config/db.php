<?php
// ============================================
// TASKS GO — DATABASE CONNECTION (PDO)
// ============================================

if (!defined('TASKS_GO')) {
    define('TASKS_GO', true);
}

require_once __DIR__ . '/config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        // Throw exceptions on error — never silent failures
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

        // Return rows as associative arrays
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // Use real prepared statements — protects against SQL injection
        PDO::ATTR_EMULATE_PREPARES   => false,

        // Reuse connections where possible
        PDO::ATTR_PERSISTENT         => false,
    ]);
} catch (PDOException $e) {
    if (APP_DEBUG) {
        die('Database connection failed: ' . $e->getMessage());
    } else {
        error_log('DB connection error: ' . $e->getMessage());
        die('We are experiencing technical difficulties. Please try again later.');
    }
}