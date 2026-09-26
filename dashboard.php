<?php
define('TASKS_GO', true);

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/lib/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect('login.php');
}

$userId   = (int) $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'there';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ============================================
// FETCH DATA
// ============================================

// Projects
$stmt = $pdo->prepare('SELECT id, name, color FROM projects WHERE user_id = ? ORDER BY name ASC');
$stmt->execute([$userId]);
$projects = $stmt->fetchAll();

// Task counts
$stmt = $pdo->prepare('
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status = "todo" THEN 1 ELSE 0 END) AS todo,
        SUM(CASE WHEN status = "done" THEN 1 ELSE 0 END) AS done,
        SUM(CASE WHEN status = "todo" AND due_date = CURDATE() THEN 1 ELSE 0 END) AS today,
        SUM(CASE WHEN status = "todo" AND due_date < CURDATE() THEN 1 ELSE 0 END) AS overdue
    FROM tasks
    WHERE user_id = ?
');
$stmt->execute([$userId]);
$counts = $stmt->fetch() ?: ['total' => 0, 'todo' => 0, 'done' => 0, 'today' => 0, 'overdue' => 0];

// Tasks
$stmt = $pdo->prepare('
    SELECT t.id, t.title, t.notes, t.priority, t.status, t.due_date, t.created_at,
           t.project_id, p.name AS project_name, p.color AS project_color
    FROM tasks t
    LEFT JOIN projects p ON p.id = t.project_id
    WHERE t.user_id = ?
    ORDER BY
        CASE WHEN t.status = "done" THEN 1 ELSE 0 END ASC,
        CASE t.priority WHEN "high" THEN 1 WHEN "medium" THEN 2 ELSE 3 END ASC,
        CASE WHEN t.due_date IS NULL THEN 1 ELSE 0 END ASC,
        t.due_date ASC,
        t.created_at DESC
    LIMIT 100
');
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();

// Progress
$totalCount = max(1, (int) $counts['total']);
$doneCount  = (int) $counts['done'];
$progress   = (int) round(($doneCount / $totalCount) * 100);

$flashSuccess = flash('success');
$flashError   = flash('error');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">
    <title>Dashboard — Tasks Go</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="dash-body">

<div class="dash-shell">

    <!-- ================= SIDEBAR ================= -->
    <aside class="dash-sidebar" id="sidebar">

        <div class="dash-sidebar-head">
            <a href="dashboard.php" class="dash-logo">
                <span class="dash-logo-mark">
                    <i class="bi bi-check2-square"></i>
                </span>
                <span class="dash-logo-name">Tasks Go</span>
            </a>
            <button class="dash-sidebar-close d-lg-none" id="sidebarClose" aria-label="Close menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="dash-nav">
            <div class="dash-nav-group">
                <div class="dash-nav-label">Views</div>
                <a href="dashboard.php" class="dash-nav-link active" data-view="all">
                    <i class="bi bi-inbox"></i>
                    <span>All tasks</span>
                    <span class="dash-nav-badge" data-count="todo"><?= (int) $counts['todo'] ?></span>
                </a>
                <a href="#" class="dash-nav-link" data-view="today">
                    <i class="bi bi-sun"></i>
                    <span>Today</span>
                    <span class="dash-nav-badge" data-count="today" <?= (int) $counts['today'] === 0 ? 'style="display:none"' : '' ?>><?= (int) $counts['today'] ?></span>
                </a>
                <a href="#" class="dash-nav-link" data-view="overdue">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>Overdue</span>
                    <span class="dash-nav-badge dash-nav-badge-danger" data-count="overdue" <?= (int) $counts['overdue'] === 0 ? 'style="display:none"' : '' ?>><?= (int) $counts['overdue'] ?></span>
                </a>
                <a href="#" class="dash-nav-link" data-view="completed">
                    <i class="bi bi-check2-all"></i>
                    <span>Completed</span>
                    <span class="dash-nav-badge" data-count="done" <?= (int) $counts['done'] === 0 ? 'style="display:none"' : '' ?>><?= (int) $counts['done'] ?></span>
                </a>
            </div>

            <div class="dash-nav-group">
                <div class="dash-nav-label">
                    Projects
                    <button class="dash-nav-add" id="openProjectModal" aria-label="Add project">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <?php if (empty($projects)): ?>
                    <div class="dash-nav-empty">No projects yet</div>
                <?php else: ?>
                    <?php foreach ($projects as $p): ?>
                        <a href="#" class="dash-nav-link" data-project="<?= (int) $p['id'] ?>">
                            <span class="dash-nav-dot" style="background: <?= e($p['color']) ?>;"></span>
                            <span><?= e($p['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </nav>

        <div class="dash-sidebar-foot">
            <div class="dash-user">
                <div class="dash-user-avatar">
                    <?= e(mb_strtoupper(mb_substr($userName, 0, 1))) ?>
                </div>
                <div class="dash-user-info">
                    <div class="dash-user-name"><?= e($userName) ?></div>
                    <a href="logout.php" class="dash-user-logout">
                        <i class="bi bi-box-arrow-right"></i> Log out
                    </a>
                </div>
            </div>
        </div>

    </aside>

    <!-- ================= BACKDROP (mobile) ================= -->
    <div class="dash-backdrop" id="backdrop"></div>

    <!-- ================= MAIN ================= -->
    <main class="dash-main">

        <!-- Topbar -->
        <header class="dash-topbar">
            <button class="dash-menu-btn d-lg-none" id="menuBtn" aria-label="Open menu">
                <i class="bi bi-list"></i>
            </button>

            <div class="dash-search">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="Search tasks…" id="taskSearch" autocomplete="off">
            </div>

            <div class="dash-actions">
                <button class="dash-btn dash-btn-primary" id="openTaskModal">
                    <i class="bi bi-plus-lg"></i>
                    <span class="d-none d-sm-inline">New task</span>
                </button>
            </div>
        </header>

        <!-- Content -->
        <div class="dash-content">

            <?php if ($flashSuccess): ?>
                <div class="dash-flash" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= e($flashSuccess) ?></span>
                    <button type="button" class="dash-flash-close" aria-label="Dismiss">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="dash-flash dash-flash-error" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><?= e($flashError) ?></span>
                    <button type="button" class="dash-flash-close" aria-label="Dismiss">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Greeting -->
            <div class="dash-greeting">
                <div class="dash-greeting-meta">
                    <span class="dash-greeting-date">
                        <i class="bi bi-calendar3"></i>
                        <?= date('l, F j') ?>
                    </span>
                    <?php if ((int) $counts['overdue'] > 0): ?>
                        <span class="dash-greeting-alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <?= (int) $counts['overdue'] ?> overdue
                        </span>
                    <?php endif; ?>
                </div>
                <h1>
                    <?php
                        $hour = (int) date('G');
                        if ($hour < 12)      echo 'Good morning';
                        elseif ($hour < 18)  echo 'Good afternoon';
                        else                 echo 'Good evening';
                    ?>,
                    <span class="dash-greeting-name"><?= e($userName) ?></span>
                </h1>
                <p>
                    <?php if ((int) $counts['todo'] === 0): ?>
                        You're all caught up. Nothing on the list.
                    <?php elseif ((int) $counts['overdue'] > 0): ?>
                        You have <strong><?= (int) $counts['overdue'] ?></strong> overdue
                        and <strong><?= (int) $counts['todo'] ?></strong> to do.
                    <?php else: ?>
                        You have <strong><?= (int) $counts['todo'] ?></strong> task<?= (int)$counts['todo'] === 1 ? '' : 's' ?> on your list.
                    <?php endif; ?>
                </p>
            </div>

            <!-- Stats -->
            <div class="dash-stats" id="dashStats">

                <!-- Hero progress card -->
                <div class="dash-stat dash-stat-hero">
                    <div class="dash-stat-hero-content">
                        <div class="dash-stat-hero-label">Progress</div>
                        <div class="dash-stat-hero-value">
                            <span data-stat="progress"><?= $progress ?></span><span class="dash-stat-hero-pct">%</span>
                        </div>
                        <div class="dash-stat-hero-sub">
                            <span data-stat="done"><?= (int) $counts['done'] ?></span> of
                            <span data-stat="total"><?= (int) $counts['total'] ?></span> completed
                        </div>
                    </div>
                    <div class="dash-stat-hero-ring">
                        <svg viewBox="0 0 36 36" class="dash-progress-ring" aria-hidden="true">
                            <path class="dash-progress-ring-bg"
                                  d="M18 2.0845
                                     a 15.9155 15.9155 0 0 1 0 31.831
                                     a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="dash-progress-ring-fill"
                                  data-stat="progressRing"
                                  stroke-dasharray="<?= $progress ?>, 100"
                                  d="M18 2.0845
                                     a 15.9155 15.9155 0 0 1 0 31.831
                                     a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="dash-stat-hero-ring-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>

                <!-- Compact stats -->
                <div class="dash-stat dash-stat-compact">
                    <div class="dash-stat-compact-icon dash-stat-icon">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div class="dash-stat-compact-body">
                        <div class="dash-stat-value" data-stat="total"><?= (int) $counts['total'] ?></div>
                        <div class="dash-stat-label">Total tasks</div>
                    </div>
                </div>

                <div class="dash-stat dash-stat-compact">
                    <div class="dash-stat-compact-icon dash-stat-icon dash-stat-icon-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="dash-stat-compact-body">
                        <div class="dash-stat-value" data-stat="todo"><?= (int) $counts['todo'] ?></div>
                        <div class="dash-stat-label">To do</div>
                    </div>
                </div>

                <div class="dash-stat dash-stat-compact">
                    <div class="dash-stat-compact-icon dash-stat-icon dash-stat-icon-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="dash-stat-compact-body">
                        <div class="dash-stat-value" data-stat="done"><?= (int) $counts['done'] ?></div>
                        <div class="dash-stat-label">Completed</div>
                    </div>
                </div>

            </div>

            <!-- Task list -->
            <section class="dash-tasks">

                <div class="dash-tasks-head">
                    <h2 id="tasksHeading">Your tasks</h2>
                    <span class="dash-tasks-count" id="tasksCount"><?= count($tasks) ?> shown</span>
                </div>

                <div class="dash-empty" id="emptyState" <?= empty($tasks) ? '' : 'style="display:none"' ?>>
                    <div class="dash-empty-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <h3 id="emptyTitle">No tasks yet</h3>
                    <p id="emptyText">Add your first task and start getting things done.</p>
                    <button class="dash-btn dash-btn-primary" id="openTaskModalEmpty">
                        <i class="bi bi-plus-lg"></i> Add your first task
                    </button>
                </div>

                <div class="dash-task-list" id="taskList" <?= empty($tasks) ? 'style="display:none"' : '' ?>>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                            $isDone    = $task['status'] === 'done';
                            $priority  = $task['priority'] ?? 'medium';
                            $dueDate   = $task['due_date'] ?? null;
                            $isOverdue = $dueDate && !$isDone && strtotime($dueDate) < strtotime('today');
                            $isToday   = $dueDate && !$isDone && $dueDate === date('Y-m-d');
                        ?>
                        <article class="dash-task <?= $isDone ? 'is-done' : '' ?>"
                                 data-id="<?= (int) $task['id'] ?>"
                                 data-status="<?= e($task['status']) ?>"
                                 data-priority="<?= e($priority) ?>"
                                 data-project="<?= (int) ($task['project_id'] ?? 0) ?>"
                                 data-due="<?= e($dueDate ?? '') ?>"
                                 data-title="<?= e($task['title']) ?>"
                                 data-notes="<?= e($task['notes'] ?? '') ?>">

                            <button class="dash-task-check"
                                    data-action="toggle"
                                    data-id="<?= (int) $task['id'] ?>"
                                    aria-label="Mark task <?= $isDone ? 'incomplete' : 'complete' ?>">
                                <i class="bi <?= $isDone ? 'bi-check-lg' : '' ?>"></i>
                            </button>

                            <div class="dash-task-body">
                                <div class="dash-task-title"
                                     data-action="view"
                                     data-id="<?= (int) $task['id'] ?>"
                                     role="button"
                                     tabindex="0"><?= e($task['title']) ?></div>

                                <div class="dash-task-meta">
                                    <?php if ($priority === 'high'): ?>
                                        <span class="dash-tag dash-tag-high">
                                            <i class="bi bi-flag-fill"></i> High
                                        </span>
                                    <?php elseif ($priority === 'low'): ?>
                                        <span class="dash-tag dash-tag-low">
                                            <i class="bi bi-flag"></i> Low
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($task['project_name']): ?>
                                        <span class="dash-tag" style="color: <?= e($task['project_color']) ?>;">
                                            <span class="dash-tag-dot" style="background: <?= e($task['project_color']) ?>;"></span>
                                            <?= e($task['project_name']) ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($dueDate): ?>
                                        <span class="dash-tag <?= $isOverdue ? 'dash-tag-danger' : ($isToday ? 'dash-tag-warn' : '') ?>">
                                            <i class="bi bi-calendar3"></i>
                                            <?= $isToday ? 'Today' : ($isOverdue ? 'Overdue' : date('M j', strtotime($dueDate))) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="dash-task-actions">
                                <button class="dash-icon-btn"
                                        data-action="delete"
                                        data-id="<?= (int) $task['id'] ?>"
                                        aria-label="Delete task">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>

                            <!-- Inline delete confirm -->
                            <div class="dash-task-confirm">
                                <span>Delete this task?</span>
                                <button type="button" class="dash-btn dash-btn-ghost dash-btn-sm" data-action="cancel-delete">Cancel</button>
                                <button type="button" class="dash-btn dash-btn-danger dash-btn-sm" data-action="confirm-delete" data-id="<?= (int) $task['id'] ?>">Delete</button>
                            </div>

                        </article>
                    <?php endforeach; ?>
                </div>

            </section>

        </div>

    </main>

</div>

<!-- ================= ADD / EDIT TASK MODAL ================= -->
<div class="dash-modal" id="taskModal" aria-hidden="true">
    <div class="dash-modal-backdrop" data-close="taskModal"></div>
    <div class="dash-modal-panel" role="dialog" aria-modal="true" aria-labelledby="taskModalTitle">
        <div class="dash-modal-head">
            <h2 id="taskModalTitle">New task</h2>
            <button class="dash-icon-btn" data-close="taskModal" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="taskForm" method="POST" action="actions/add_task.php">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="task_id" id="taskIdField" value="">

            <div class="dash-modal-body">

                <div class="dash-field">
                    <label for="taskTitle" class="dash-label">Task</label>
                    <input type="text"
                           id="taskTitle"
                           name="title"
                           class="dash-input"
                           placeholder="What needs to get done?"
                           maxlength="255"
                           required>
                </div>

                <div class="dash-field">
                    <label for="taskNotes" class="dash-label">Notes <span class="dash-label-opt">(optional)</span></label>
                    <textarea id="taskNotes"
                              name="notes"
                              class="dash-input dash-textarea"
                              placeholder="Add any details…"
                              rows="3"
                              maxlength="2000"></textarea>
                </div>

                <div class="dash-row">
                    <div class="dash-field">
                        <label for="taskPriority" class="dash-label">Priority</label>
                        <select id="taskPriority" name="priority" class="dash-input">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div class="dash-field">
                        <label for="taskDue" class="dash-label">Due date <span class="dash-label-opt">(optional)</span></label>
                        <input type="date"
                               id="taskDue"
                               name="due_date"
                               class="dash-input">
                    </div>
                </div>

                <div class="dash-field">
                    <label for="taskProject" class="dash-label">Project <span class="dash-label-opt">(optional)</span></label>
                    <select id="taskProject" name="project_id" class="dash-input">
                        <option value="">No project</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= (int) $p['id'] ?>"><?= e($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="dash-modal-foot">
                <button type="button" class="dash-btn dash-btn-ghost" data-close="taskModal">Cancel</button>
                <button type="submit" class="dash-btn dash-btn-primary" id="taskSubmitBtn">
                    <i class="bi bi-plus-lg"></i> <span>Add task</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= ADD PROJECT MODAL ================= -->
<div class="dash-modal" id="projectModal" aria-hidden="true">
    <div class="dash-modal-backdrop" data-close="projectModal"></div>
    <div class="dash-modal-panel" role="dialog" aria-modal="true" aria-labelledby="projectModalTitle">
        <div class="dash-modal-head">
            <h2 id="projectModalTitle">New project</h2>
            <button class="dash-icon-btn" data-close="projectModal" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="projectForm" method="POST" action="actions/add_project.php">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

            <div class="dash-modal-body">
                <div class="dash-field">
                    <label for="projectName" class="dash-label">Project name</label>
                    <input type="text"
                           id="projectName"
                           name="name"
                           class="dash-input"
                           placeholder="e.g. Marketing"
                           maxlength="100"
                           required>
                </div>

                <div class="dash-field">
                    <label for="projectColor" class="dash-label">Color</label>
                    <div class="dash-colors">
                        <?php
                            $colors = ['#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f59e0b', '#10b981', '#0ea5e9', '#64748b'];
                            foreach ($colors as $i => $c):
                        ?>
                            <label class="dash-color">
                                <input type="radio" name="color" value="<?= e($c) ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                <span style="background: <?= e($c) ?>;"></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="dash-modal-foot">
                <button type="button" class="dash-btn dash-btn-ghost" data-close="projectModal">Cancel</button>
                <button type="submit" class="dash-btn dash-btn-primary">
                    <i class="bi bi-plus-lg"></i> Add project
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= TASK DETAIL PANEL ================= -->
<div class="dash-panel" id="taskPanel" aria-hidden="true">
    <div class="dash-panel-backdrop" data-close="taskPanel"></div>
    <aside class="dash-panel-body" role="dialog" aria-modal="true" aria-labelledby="taskPanelTitle">

        <header class="dash-panel-head">
            <div class="dash-panel-head-left">
                <button type="button"
                        class="dash-panel-check"
                        id="panelToggle"
                        aria-label="Toggle task status">
                    <i class="bi bi-check-lg"></i>
                </button>
                <span class="dash-panel-status" id="panelStatus">To do</span>
            </div>
            <div class="dash-panel-head-right">
                <button type="button"
                        class="dash-icon-btn"
                        id="panelEditBtn"
                        aria-label="Edit task">
                    <i class="bi bi-pencil"></i>
                </button>
                <button type="button"
                        class="dash-icon-btn"
                        data-close="taskPanel"
                        aria-label="Close panel">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </header>

        <div class="dash-panel-content">

            <h2 class="dash-panel-title" id="taskPanelTitle"></h2>

            <div class="dash-panel-meta" id="panelMeta"></div>

            <div class="dash-panel-section" id="panelNotesSection">
                <div class="dash-panel-section-label">
                    <i class="bi bi-text-paragraph"></i> Notes
                </div>
                <div class="dash-panel-notes" id="panelNotes"></div>
            </div>

            <div class="dash-panel-section">
                <div class="dash-panel-section-label">
                    <i class="bi bi-info-circle"></i> Details
                </div>
                <dl class="dash-panel-details" id="panelDetails"></dl>
            </div>

        </div>

        <footer class="dash-panel-foot">
            <button type="button"
                    class="dash-btn dash-btn-danger"
                    id="panelDeleteBtn">
                <i class="bi bi-trash3"></i> Delete
            </button>
            <button type="button"
                    class="dash-btn dash-btn-primary"
                    id="panelEditBtnFoot">
                <i class="bi bi-pencil"></i> Edit
            </button>
        </footer>

    </aside>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.TASKS_GO = {
        csrfToken: <?= json_encode($_SESSION['csrf_token']) ?>,
        userId: <?= (int) $userId ?>,
        counts: {
            total: <?= (int) $counts['total'] ?>,
            todo: <?= (int) $counts['todo'] ?>,
            done: <?= (int) $counts['done'] ?>,
            today: <?= (int) $counts['today'] ?>,
            overdue: <?= (int) $counts['overdue'] ?>
        }
    };
</script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>