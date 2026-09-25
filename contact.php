<?php
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
$errors = [];
$success = false;
$old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        // Sanitize + validate
        $old['name']    = trim($_POST['name'] ?? '');
        $old['email']   = trim($_POST['email'] ?? '');
        $old['subject'] = trim($_POST['subject'] ?? '');
        $old['message'] = trim($_POST['message'] ?? '');

        if ($old['name'] === '' || mb_strlen($old['name']) < 2) {
            $errors[] = 'Please enter your name.';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($old['subject'] === '') {
            $errors[] = 'Please choose a subject.';
        }
        if ($old['message'] === '' || mb_strlen($old['message']) < 10) {
            $errors[] = 'Please write a message (at least 10 characters).';
        }

        if (empty($errors)) {
            // TODO: send email or save to DB
            // mail('hello@tasksgo.app', '[' . $old['subject'] . '] ' . $old['name'], $old['message'], ...);

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // rotate token
            $success = true;
            $old = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact — Tasks Go</title>
    <meta name="description" content="Get in touch with the Tasks Go team. We read every message.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="./assets/css/style.css">

</head>
<body>

<!-- ================= NAV ================= -->
<nav class="navbar navbar-expand-lg navbar-tg sticky-top py-3">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="index.php">
            <span class="feature-icon" style="width:36px;height:36px;font-size:1rem;">
                <i class="bi bi-check2-square"></i>
            </span>
            Tasks Go
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav mx-auto gap-lg-4">
                <li class="nav-item"><a class="nav-link" href="index.php#features">Features</a></li>
                     <li class="nav-item"><a class="nav-link" href="index.php#testimonials">Testimonials</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#pricing">Pricing</a></li>
                <li class="nav-item"><a class="nav-link" href="about-us.php">About</a></li>
                <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
            </ul>
            <div class="d-flex gap-2">
                <a href="login.php" class="btn btn-tg-outline px-3">Log in</a>
                <a href="register.php" class="btn btn-tg px-3">Get started</a>
            </div>
        </div>
    </div>
</nav>

<!-- ================= HERO ================= -->
<header class="contact-hero">
    <div class="container text-center">
        <span class="badge-tg d-inline-flex align-items-center gap-2 mb-4">
            <i class="bi bi-chat-dots"></i> Contact us
        </span>
        <h1 class="display-4 fw-bold mb-3" style="letter-spacing:-.02em;">
            Let's talk.
        </h1>
        <p class="lead text-body-secondary mx-auto mb-0" style="max-width: 560px;">
            Questions, feedback, bug reports, or just want to say hi?
            We read every message and reply within 24 hours.
        </p>
    </div>
</header>

<!-- ================= CONTACT INFO CARDS ================= -->
<section class="pb-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="info-card text-center">
                    <div class="info-icon mx-auto mb-3"><i class="bi bi-envelope"></i></div>
                    <h6 class="fw-semibold mb-1">Email us</h6>
                    <p class="text-body-secondary small mb-2">For general inquiries</p>
                    <a href="mailto:hello@tasksgo.app" class="text-decoration-none fw-medium" style="color: var(--tg-accent);">
                        hello@tasksgo.app
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card text-center">
                    <div class="info-icon mx-auto mb-3"><i class="bi bi-life-preserver"></i></div>
                    <h6 class="fw-semibold mb-1">Support</h6>
                    <p class="text-body-secondary small mb-2">Need help with your account?</p>
                    <a href="mailto:support@tasksgo.app" class="text-decoration-none fw-medium" style="color: var(--tg-accent);">
                        support@tasksgo.app
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card text-center">
                    <div class="info-icon mx-auto mb-3"><i class="bi bi-briefcase"></i></div>
                    <h6 class="fw-semibold mb-1">Business</h6>
                    <p class="text-body-secondary small mb-2">Partnerships &amp; press</p>
                    <a href="mailto:business@tasksgo.app" class="text-decoration-none fw-medium" style="color: var(--tg-accent);">
                        business@tasksgo.app
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= CONTACT FORM ================= -->
<section class="pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="contact-card">

                    <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-start gap-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                            <div>
                                <strong>Message sent!</strong>
                                <div class="small">Thanks for reaching out. We'll get back to you within 24 hours.</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger d-flex align-items-start gap-3 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            <div>
                                <strong>Please fix the following:</strong>
                                <ul class="mb-0 mt-1 small">
                                    <?php foreach ($errors as $e): ?>
                                        <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h2 class="h4 fw-semibold mb-1">Send us a message</h2>
                    <p class="text-body-secondary small mb-4">Fill out the form and we'll be in touch.</p>

                    <form method="POST" action="contact.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Your name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?= htmlspecialchars($old['name'], ENT_QUOTES, 'UTF-8') ?>"
                                       placeholder="Jane Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>"
                                       placeholder="jane@example.com" required>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label">Subject</label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="">Choose a topic…</option>
                                    <option value="general"   <?= $old['subject'] === 'general'   ? 'selected' : '' ?>>General inquiry</option>
                                    <option value="support"   <?= $old['subject'] === 'support'   ? 'selected' : '' ?>>Technical support</option>
                                    <option value="feedback"  <?= $old['subject'] === 'feedback'  ? 'selected' : '' ?>>Product feedback</option>
                                    <option value="bug"       <?= $old['subject'] === 'bug'       ? 'selected' : '' ?>>Report a bug</option>
                                    <option value="business"  <?= $old['subject'] === 'business'  ? 'selected' : '' ?>>Business / partnership</option>
                                    <option value="other"     <?= $old['subject'] === 'other'     ? 'selected' : '' ?>>Something else</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="6"
                                          placeholder="Tell us what's on your mind…" required><?= htmlspecialchars($old['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                <div class="form-text">Minimum 10 characters.</div>
                            </div>
                            <div class="col-12 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="consent" required>
                                    <label class="form-check-label small text-body-secondary" for="consent">
                                        I agree to be contacted about my message.
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-tg px-4">
                                    Send message <i class="bi bi-send ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FAQ ================= -->
<section class="section bg-body-tertiary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <span class="badge-tg mb-3 d-inline-block">FAQ</span>
                    <h2 class="display-6 fw-bold mb-3" style="letter-spacing:-.02em;">
                        Before you write to us…
                    </h2>
                    <p class="text-body-secondary">
                        You might find your answer here. If not, the form above works great.
                    </p>
                </div>

                <div class="accordion" id="faq">
                    <div class="accordion-item border rounded-3 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq1">
                                Is Tasks Go really free?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body text-body-secondary">
                                Yes. The Free plan includes unlimited tasks, 3 projects, and basic reminders — forever, no credit card required. Pro adds unlimited projects, calendar view, and recurring tasks for $6/month.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-3 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do I reset my password?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body text-body-secondary">
                                Go to the login page and click "Forgot password." Enter your email and we'll send a reset link within a minute. If you don't see it, check your spam folder.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-3 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq3">
                                Can I export my data?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body text-body-secondary">
                                Absolutely. Go to Settings → Export. You can download everything as JSON or CSV at any time. Your data is yours.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-3 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq4">
                                Do you offer discounts for students or nonprofits?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body text-body-secondary">
                                Yes — 50% off Pro for verified students and registered nonprofits. Send us a message using the form above with "Business / partnership" as the subject and we'll sort it out.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border rounded-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq5">
                                How do I delete my account?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faq">
                            <div class="accordion-body text-body-secondary">
                                Settings → Account → Delete account. It's permanent and immediate — everything is removed from our servers within 24 hours. No questions asked.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="border-top py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold mb-3" href="index.php">
                    <span class="feature-icon" style="width:32px;height:32px;font-size:.9rem;">
                        <i class="bi bi-check2-square"></i>
                    </span>
                    Tasks Go
                </a>
                <p class="text-body-secondary small mb-0" style="max-width: 320px;">
                    A fast, focused task manager for people who actually want to finish things.
                </p>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-semibold mb-3">Product</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="index.php#features" class="text-body-secondary text-decoration-none">Features</a></li>
                    <li><a href="index.php#pricing" class="text-body-secondary text-decoration-none">Pricing</a></li>
                    <li><a href="#" class="text-body-secondary text-decoration-none">Changelog</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-semibold mb-3">Company</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="about.php" class="text-body-secondary text-decoration-none">About</a></li>
                    <li><a href="#" class="text-body-secondary text-decoration-none">Blog</a></li>
                    <li><a href="contact.php" class="text-body-secondary text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-semibold mb-3">Legal</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="#" class="text-body-secondary text-decoration-none">Privacy</a></li>
                    <li><a href="#" class="text-body-secondary text-decoration-none">Terms</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-semibold mb-3">Follow</h6>
                <div class="d-flex gap-3">
                    <a href="#" class="text-body-secondary"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-body-secondary"><i class="bi bi-github"></i></a>
                    <a href="#" class="text-body-secondary"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small text-body-secondary">
            <span>&copy; <?= date('Y') ?> Tasks Go. All rights reserved.</span>
            <span>Built with care.</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>