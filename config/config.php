<?php
// ============================================
// TASKS GO — APP CONFIG
// ============================================

// Prevent direct access
if (!defined('TASKS_GO')) {
    define('TASKS_GO', true);
}

// App
define('APP_NAME', 'Tasks Go');
define('APP_URL', 'task-manager1156.gt.tc');

// Database
define('DB_HOST', 'sql103.infinityfree.com');   // e.g. sql123.infinityfree.com
define('DB_NAME', 'if0_43011885_taskmanager'); // InfinityFree auto-prefixes this
define('DB_USER', 'if0_43011885');              // same as the prefix
define('DB_PASS', 'SamuelMolo1408');    // the password you set
define('DB_CHARSET', 'utf8mb4');

// Sessions
define('SESSION_NAME', 'tasks_go_session');
define('SESSION_LIFETIME', 60 * 60 * 24 * 7); // 7 days

// Security
define('PASSWORD_MIN_LENGTH', 8);
define('CSRF_TOKEN_NAME', 'csrf_token');

// Environment — flip to false when you deploy
define('APP_DEBUG', true);
define('SESSION_IDLE_TIMEOUT', 60 * 30);        // 30 minutes
define('SESSION_ABSOLUTE_TIMEOUT', 60 * 60 * 24 * 30); // 30 days