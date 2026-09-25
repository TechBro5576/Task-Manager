<?php
// ============================================
// TASKS GO — ADD TASK HANDLER
// ============================================

define('TASKS_GO', true);

// ---- Session setup ----
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// ============================================
// AUTH GUARD
// ============================================
if (!isset($_SESSION['user_id'])) {
    redirect('../login.php');
}

$userId = (int) $_SESSION['user_id'];

// ============================================
// METHOD CHECK
// ============================================
if (!is_post()) {
    redirect('../dashboard.php');
}

// ============================================
// CSRF CHECK
// ============================================
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    flash('error', 'Invalid form submission. Please try again.');
    redirect('../dashboard.php');
}

// ============================================
// COLLECT + SANITIZE
// ============================================
$title     = trim($_POST['title'] ?? '');
$notes     = trim($_POST['notes'] ?? '');
$priority  = $_POST['priority'] ?? 'medium';
$dueDate   = trim($_POST['due_date'] ?? '');
$projectId = $_POST['project_id'] ?? '';

$errors = [];

// ---- Title ----
if ($title === '') {
    $errors[] = 'Please enter a task title.';
} elseif (mb_strlen($title) > 255) {
    $errors[] = 'Task title is too long (max 255 characters).';
}

// ---- Notes ----
if ($notes !== '' && mb_strlen($notes) > 2000) {
    $errors[] = 'Notes are too long (max 2000 characters).';
}

// ---- Priority ----
if (!in_array($priority, ['low', 'medium', 'high'], true)) {
    $priority = 'medium';
}

// ---- Due date ----
if ($dueDate !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $dueDate);
    if (!$d || $d->format('Y-m-d') !== $dueDate) {
        $errors[] = 'Please enter a valid due date.';
        $dueDate = '';
    }
} else {
    $dueDate = null;
}

// ---- Project ----
$projectId = ($projectId === '' || $projectId === '0') ? null : (int) $projectId;

if ($projectId !== null) {
    // Confirm the project belongs to this user
    $stmt = $pdo->prepare('SELECT id FROM projects WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$projectId, $userId]);
    if (!$stmt->fetch()) {
        $errors[] = 'Selected project not found.';
        $projectId = null;
    }
}

// ============================================
// SAVE
// ============================================
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    redirect('../dashboard.php');
}

try {
    $stmt = $pdo->prepare('
        INSERT INTO tasks (user_id, project_id, title, notes, priority, status, due_date, created_at)
        VALUES (?, ?, ?, ?, ?, "todo", ?, NOW())
    ');
    $stmt->execute([
        $userId,
        $projectId,
        $title,
        $notes !== '' ? $notes : null,
        $priority,
        $dueDate,
    ]);

    flash('success', 'Task added.');
} catch (PDOException $e) {
    log_error('add_task failed: ' . $e->getMessage());
    flash('error', 'Could not save your task. Please try again.');
}

redirect('../dashboard.php');