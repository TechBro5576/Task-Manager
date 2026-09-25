<?php
// ============================================
// TASKS GO — TASKS API
// Handles: PATCH (toggle), DELETE, GET (list)
// ============================================

define('TASKS_GO', true);

// ---- Session setup (must match dashboard.php) ----
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// ---- Force JSON output ----
header('Content-Type: application/json');

// ---- JSON responder ----
function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// ============================================
// AUTH
// ============================================
if (!isset($_SESSION['user_id'])) {
    json_out(['error' => 'Unauthorized'], 401);
}

$userId = (int) $_SESSION['user_id'];

// ============================================
// CSRF (from header or JSON body)
// ============================================
$csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
$csrfBody   = '';

$raw = file_get_contents('php://input');
if ($raw !== '' && $raw !== false) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $csrfBody = $decoded['csrf_token'] ?? '';
    }
}

$csrfToken = $csrfHeader !== '' ? $csrfHeader : $csrfBody;

if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
    json_out(['error' => 'Invalid CSRF token'], 403);
}

// ============================================
// ROUTE BY METHOD
// ============================================
$method = $_SERVER['REQUEST_METHOD'];

try {

    switch ($method) {

        // --------------------------------------------
        // PATCH — toggle task status (done / todo)
        // --------------------------------------------
        case 'PATCH':
            $data = json_decode($raw, true);

            if (!is_array($data)) {
                json_out(['error' => 'Invalid JSON'], 400);
            }

            $id     = (int) ($data['id'] ?? 0);
            $status = $data['status'] ?? '';

            if ($id <= 0) {
                json_out(['error' => 'Invalid task ID'], 400);
            }

            if (!in_array($status, ['todo', 'done'], true)) {
                json_out(['error' => 'Invalid status'], 400);
            }

            $completedAt = $status === 'done' ? date('Y-m-d H:i:s') : null;

            $stmt = $pdo->prepare('
                UPDATE tasks
                SET status = ?, completed_at = ?
                WHERE id = ? AND user_id = ?
            ');
            $stmt->execute([$status, $completedAt, $id, $userId]);

            // rowCount can be 0 if the status was already the same — verify ownership anyway
            $check = $pdo->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ? LIMIT 1');
            $check->execute([$id, $userId]);

            if (!$check->fetch()) {
                json_out(['error' => 'Task not found'], 404);
            }

            json_out([
                'ok'     => true,
                'id'     => $id,
                'status' => $status
            ]);
            break;

        // --------------------------------------------
        // DELETE — remove a task
        // --------------------------------------------
        case 'DELETE':
            $id = (int) ($_GET['id'] ?? 0);

            if ($id <= 0) {
                json_out(['error' => 'Invalid task ID'], 400);
            }

            $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $userId]);

            if ($stmt->rowCount() === 0) {
                json_out(['error' => 'Task not found'], 404);
            }

            json_out([
                'ok' => true,
                'id' => $id
            ]);
            break;

        // --------------------------------------------
        // GET — list user's tasks (for future use)
        // --------------------------------------------
        case 'GET':
            $stmt = $pdo->prepare('
                SELECT id, title, notes, priority, status, due_date, project_id, created_at
                FROM tasks
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT 100
            ');
            $stmt->execute([$userId]);

            json_out([
                'ok'    => true,
                'tasks' => $stmt->fetchAll()
            ]);
            break;

        // --------------------------------------------
        // Anything else
        // --------------------------------------------
        default:
            json_out(['error' => 'Method not allowed'], 405);
    }

} catch (PDOException $e) {
    log_error('api/tasks failed: ' . $e->getMessage());
    json_out(['error' => 'Server error'], 500);
}