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

// Already logged in? Go to dashboard
if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$old = ['name' => '', 'email' => ''];

if (is_post()) {
    // CSRF check
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $old['name']  = trim($_POST['name'] ?? '');
        $old['email'] = trim($_POST['email'] ?? '');
        $password     = $_POST['password'] ?? '';
        $confirm      = $_POST['password_confirm'] ?? '';
        $agree        = isset($_POST['agree']);

        // Validate name
        if ($old['name'] === '' || mb_strlen($old['name']) < 2) {
            $errors[] = 'Please enter your name (at least 2 characters).';
        } elseif (mb_strlen($old['name']) > 100) {
            $errors[] = 'Name is too long.';
        }

        // Validate email
        if ($old['email'] === '') {
            $errors[] = 'Please enter your email address.';
        } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif (strlen($old['email']) > 255) {
            $errors[] = 'Email is too long.';
        }

        // Validate password
        if ($password === '') {
            $errors[] = 'Please choose a password.';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        } elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one letter and one number.';
        }

        // Confirm password
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        // Terms
        if (!$agree) {
            $errors[] = 'Please agree to the Terms and Privacy Policy.';
        }

        // Check email uniqueness
        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$old['email']]);
            if ($stmt->fetch()) {
                $errors[] = 'An account with that email already exists.';
            }
        }

        // Create account
        if (empty($errors)) {
            try {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
                );
                $stmt->execute([$old['name'], $old['email'], $hash]);

                $userId = (int) $pdo->lastInsertId();

                // Log the user in
                session_regenerate_id(true);
                $_SESSION['user_id']    = $userId;
                $_SESSION['user_name']  = $old['name'];
                $_SESSION['user_email'] = $old['email'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                flash('success', 'Welcome to Tasks Go! Your account is ready.');
                redirect('dashboard.php');

            } catch (PDOException $e) {
                log_error('Registration failed: ' . $e->getMessage());
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }

    // Rotate token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6366f1">
    <title>Create account — Tasks Go</title>
    <meta name="description" content="Create your free Tasks Go account and start finishing things.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body class="auth-body">

<!-- ================= MOBILE HEADER ================= -->
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

    <!-- ================= LEFT: BRAND PANEL ================= -->
    <aside class="auth-brand">

        <a href="index.php" class="auth-brand-logo">
            <span class="auth-brand-mark">
                <i class="bi bi-check2-square"></i>
            </span>
            <span class="auth-brand-name">Tasks Go</span>
        </a>

        <div class="auth-brand-content">
            <h2 class="auth-brand-headline">
                Start finishing.<br>
                <span>Starting today.</span>
            </h2>
            <p class="auth-brand-sub">
                Join thousands of people who use Tasks Go to get things done — without the chaos.
            </p>

            <ul class="auth-brand-list">
                <li><i class="bi bi-check-circle-fill"></i> Free forever for personal use</li>
                <li><i class="bi bi-check-circle-fill"></i> No credit card required</li>
                <li><i class="bi bi-check-circle-fill"></i> Set up in under 30 seconds</li>
            </ul>
        </div>

        <div class="auth-brand-footer">
            <div class="auth-brand-quote">
                <p>"Set up took me less than a minute. I've been using it every day since."</p>
                <div class="auth-brand-author">
                    <img src="https://i.pravatar.cc/40?img=15" alt="" width="36" height="36" loading="lazy">
                    <div>
                        <strong>Marcus Rivera</strong>
                        <small>Engineering Lead</small>
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
                <h1>Create your account</h1>
                <p>Free forever. No credit card required.</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger d-flex align-items-start gap-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div>
                        <strong>Couldn't create your account</strong>
                        <ul class="mb-0 mt-1 small">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" novalidate>

                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

                <!-- Name -->
                <div class="auth-field">
                    <label for="name" class="auth-label">Full name</label>
                    <div class="auth-input">
                        <i class="bi bi-person"></i>
                        <input type="text"
                               id="name"
                               name="name"
                               value="<?= e($old['name']) ?>"
                               placeholder="Jane Doe"
                               autocomplete="name"
                               maxlength="100"
                               autofocus
                               required>
                    </div>
                </div>

                <!-- Email -->
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
                               maxlength="255"
                               required>
                    </div>
                </div>

                <!-- Password -->
                <div class="auth-field">
                    <label for="password" class="auth-label">Password</label>
                    <div class="auth-input">
                        <i class="bi bi-lock"></i>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="At least <?= PASSWORD_MIN_LENGTH ?> characters"
                               autocomplete="new-password"
                               minlength="<?= PASSWORD_MIN_LENGTH ?>"
                               required>
                        <button type="button"
                                class="auth-toggle toggle-password"
                                data-target="password"
                                aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <!-- Password strength meter -->
                    <div class="auth-strength" id="strength-meter" aria-hidden="true">
                        <div class="auth-strength-bar">
                            <div class="auth-strength-fill" id="strength-fill"></div>
                        </div>
                        <small class="auth-strength-text" id="strength-text"></small>
                    </div>
                </div>

                <!-- Confirm password -->
                <div class="auth-field">
                    <label for="password_confirm" class="auth-label">Confirm password</label>
                    <div class="auth-input">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password"
                               id="password_confirm"
                               name="password_confirm"
                               placeholder="Re-enter your password"
                               autocomplete="new-password"
                               minlength="<?= PASSWORD_MIN_LENGTH ?>"
                               required>
                        <button type="button"
                                class="auth-toggle toggle-password"
                                data-target="password_confirm"
                                aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms -->
                <label class="auth-check auth-check-terms">
                    <input type="checkbox" name="agree" value="1" <?= isset($_POST['agree']) ? 'checked' : '' ?>>
                    <span>
                        I agree to the
                        <a href="#" class="auth-link-inline">Terms</a>
                        and
                        <a href="#" class="auth-link-inline">Privacy Policy</a>.
                    </span>
                </label>

                <!-- Submit -->
                <button type="submit" class="auth-submit">
                    <span>Create account</span>
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

            <div class="auth-divider"><span>or</span></div>

            <p class="auth-switch">
                Already have an account?
                <a href="login.php">Log in</a>
            </p>

        </div>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/register.js"></script>
</body>
</html>