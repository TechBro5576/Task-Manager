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

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$old = ['email' => ''];

if (is_post()) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $old['email'] = trim($_POST['email'] ?? '');
        $password     = $_POST['password'] ?? '';

        if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($password === '') {
            $errors[] = 'Please enter your password.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$old['email']]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']    = (int) $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                redirect('dashboard.php');
            } else {
                $errors[] = 'Incorrect email or password.';
            }
        }
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">
    <title>Log in — Tasks Go</title>
    <meta name="description" content="Log in to your Tasks Go account.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="auth-body">

<!-- ================= MOBILE HEADER (visible only on small screens) ================= -->
<header class="auth-mobile-header">
    <a href="index.php" class="auth-mobile-back" aria-label="Back to home">
        <i class="bi bi-arrow-left"></i>
    </a>
    <a href="index.php" class="auth-mobile-logo">
        <span class="auth-mobile-mark">
            <i class="bi bi-check2-square"></i>
        </span>
        <span>Tasks Go</span>
    </a>
</header>

<div class="auth-shell">

    <!-- ================= LEFT: BRAND PANEL (desktop only) ================= -->
    <aside class="auth-brand">

        <a href="index.php" class="auth-brand-logo">
            <span class="auth-brand-mark">
                <i class="bi bi-check2-square"></i>
            </span>
            <span class="auth-brand-name">Tasks Go</span>
        </a>

        <div class="auth-brand-content">
            <h2 class="auth-brand-headline">
                Get things done.<br>
                <span>Without the chaos.</span>
            </h2>
            <p class="auth-brand-sub">
                A fast, focused task manager for people who actually want to finish things.
            </p>

            <ul class="auth-brand-list">
                <li><i class="bi bi-check-circle-fill"></i> Add tasks in under two seconds</li>
                <li><i class="bi bi-check-circle-fill"></i> Projects, tags, and priorities</li>
                <li><i class="bi bi-check-circle-fill"></i> Works everywhere you do</li>
            </ul>
        </div>

        <div class="auth-brand-footer">
            <div class="auth-brand-quote">
                <p>"I finally stick with a task app. Tasks Go just works."</p>
                <div class="auth-brand-author">
                    <img src="https://i.pravatar.cc/40?img=32" alt="" width="36" height="36" loading="lazy">
                    <div>
                        <strong>Sarah Chen</strong>
                        <small>Product Designer</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-brand-glow"></div>
    </aside>

    <!-- ================= RIGHT: FORM PANEL ================= -->
    <section class="auth-form-panel">

        <a href="index.php" class="auth-back">
            <i class="bi bi-arrow-left"></i>
            <span>Back to home</span>
        </a>

        <div class="auth-form-wrap">

            <div class="auth-form-header">
                <h1>Welcome back</h1>
                <p>Log in to continue to your tasks.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger d-flex align-items-start gap-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <strong>Couldn't log you in</strong>
                        <ul class="mb-0 mt-1 small">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($msg = flash('success')): ?>
                <div class="alert alert-success d-flex align-items-start gap-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div><?= e($msg) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>

                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

                <div class="auth-field">
                    <label for="email" class="auth-label">Email address</label>
                    <div class="auth-input">
                        <i class="bi bi-envelope"></i>
                        <input type="email"
                               id="email"
                               name="email"
                               value="<?= e($old['email']) ?>"
                               placeholder="you@example.com"
                               inputmode="email"
                               autocomplete="email"
                               autocapitalize="none"
                               autocorrect="off"
                               spellcheck="false"
                               autofocus
                               required>
                    </div>
                </div>

                <div class="auth-field">
                    <div class="auth-label-row">
                        <label for="password" class="auth-label">Password</label>
                        <a href="forgot_password.php" class="auth-link">Forgot?</a>
                    </div>
                    <div class="auth-input">
                        <i class="bi bi-lock"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required>
                        <button type="button"
                                class="auth-toggle toggle-password"
                                data-target="password"
                                aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <label class="auth-check">
                    <input type="checkbox" name="remember" value="1">
                    <span>Keep me signed in</span>
                </label>

                <button type="submit" class="auth-submit">
                    <span>Log in</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <div class="auth-divider"><span>or</span></div>

            <p class="auth-switch">
                Don't have an account?
                <a href="register.php">Create one free</a>
            </p>

            <p class="auth-terms">
                By logging in you agree to our
                <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.
            </p>

        </div>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/login.js"></script>
</body>
</html>