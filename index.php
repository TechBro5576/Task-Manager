<?php
// Configure sessions before starting
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks Go — Get things done, faster</title>
    <meta name="description" content="Tasks Go is a fast, focused task manager built for people who actually want to finish things.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
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
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
                <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
                <li class="nav-item"><a class="nav-link" href="about-us.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
            </ul>
            <div class="d-flex gap-2">
                <a href="login.php" class="btn btn-tg-outline px-3">Log in</a>
                <a href="register.php" class="btn btn-tg px-3">Get started</a>
            </div>
        </div>
    </div>
</nav>

<!-- ================= HERO ================= -->
<header class="hero section pb-0">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <span class="badge-tg d-inline-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-stars"></i> Now with smart reminders
                </span>
                <h1 class="display-3 fw-bold lh-1 mb-4" style="letter-spacing:-.02em;">
                    Get things done.<br>
                    <span style="color: var(--tg-accent);">Without the chaos.</span>
                </h1>
                <p class="lead text-body-secondary mb-5 mx-auto" style="max-width: 620px;">
                    Tasks Go is a fast, focused task manager for people who actually want to finish things.
                    Capture, organize, and complete — in seconds.
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mb-4">
                    <a href="register.php" class="btn btn-tg btn-lg px-4">
                        Start for free <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="#how" class="btn btn-tg-outline btn-lg px-4">
                        <i class="bi bi-play-circle me-1"></i> See how it works
                    </a>
                </div>

                <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
                    <div class="avatar-stack d-flex">
                        <img src="https://i.pravatar.cc/40?img=1" alt="" loading="lazy">
                        <img src="https://i.pravatar.cc/40?img=5" alt="" loading="lazy">
                        <img src="https://i.pravatar.cc/40?img=8" alt="" loading="lazy">
                        <img src="https://i.pravatar.cc/40?img=12" alt="" loading="lazy">
                    </div>
                    <div class="text-start">
                        <div class="text-warning small">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <small class="text-body-secondary">
                            Loved by <strong class="text-body">12,000+</strong> people
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-5">
            <div class="col-lg-10">
                <div class="app-preview">
                    <div class="app-preview-bar">
                        <span class="dot dot-r"></span>
                        <span class="dot dot-y"></span>
                        <span class="dot dot-g"></span>
                        <small class="ms-3 text-body-secondary">tasksgo.app/dashboard</small>
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="mb-0 fw-semibold">Today</h5>
                                <small class="text-body-secondary">3 of 6 completed</small>
                            </div>
                            <button class="btn btn-tg btn-sm">
                                <i class="bi bi-plus-lg me-1"></i> New task
                            </button>
                        </div>
                        <div class="progress mb-4" style="height:6px;">
                            <div class="progress-bar" style="width:50%; background: var(--tg-accent);"></div>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div class="mock-task">
                                <input class="form-check-input m-0" type="checkbox">
                                <span class="mock-title flex-grow-1">Review Q3 marketing plan</span>
                                <span class="badge text-bg-danger-subtle text-danger-emphasis rounded-pill">High</span>
                            </div>
                            <div class="mock-task">
                                <input class="form-check-input m-0" type="checkbox">
                                <span class="mock-title flex-grow-1">Reply to Sarah's email</span>
                                <span class="badge text-bg-warning-subtle text-warning-emphasis rounded-pill">Medium</span>
                            </div>
                            <div class="mock-task done">
                                <input class="form-check-input m-0" type="checkbox" checked>
                                <span class="mock-title flex-grow-1">Morning standup</span>
                                <span class="badge text-bg-success-subtle text-success-emphasis rounded-pill">Done</span>
                            </div>
                            <div class="mock-task">
                                <input class="form-check-input m-0" type="checkbox">
                                <span class="mock-title flex-grow-1">Draft blog post outline</span>
                                <span class="badge text-bg-secondary-subtle text-secondary-emphasis rounded-pill">Low</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ================= LOGO STRIP ================= -->
<section class="py-5 border-top border-bottom logo-strip">
    <div class="container">
        <p class="text-center text-body-secondary small text-uppercase mb-4" style="letter-spacing:.12em;">
            Trusted by teams at
        </p>
        <div class="row align-items-center justify-content-center g-4">
            <div class="col-6 col-md-2 text-center"><h5 class="fw-bold mb-0">Acme Corp</h5></div>
            <div class="col-6 col-md-2 text-center"><h5 class="fw-bold mb-0">Globex</h5></div>
            <div class="col-6 col-md-2 text-center"><h5 class="fw-bold mb-0">Initech</h5></div>
            <div class="col-6 col-md-2 text-center"><h5 class="fw-bold mb-0">Umbrella</h5></div>
            <div class="col-6 col-md-2 text-center"><h5 class="fw-bold mb-0">Hooli</h5></div>
        </div>
    </div>
</section>

<!-- ================= FEATURES ================= -->
<section class="section" id="features">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge-tg mb-3 d-inline-block">Features</span>
            <h2 class="display-5 fw-bold mb-3" style="letter-spacing:-.02em;">
                Everything you need. Nothing you don't.
            </h2>
            <p class="lead text-body-secondary mx-auto" style="max-width: 560px;">
                Built to get out of your way so you can focus on the work that matters.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="feature-visual">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Lightning fast</h5>
                    <p class="text-body-secondary mb-0">
                        Add a task in under two seconds. Keyboard shortcuts for everything.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="feature-visual">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Smart reminders</h5>
                    <p class="text-body-secondary mb-0">
                        Get nudged at the right time, not every five minutes.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="feature-visual">
                        <i class="bi bi-folder-fill"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Projects &amp; tags</h5>
                    <p class="text-body-secondary mb-0">
                        Group tasks by project, tag them however you think.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="feature-visual">
                        <i class="bi bi-calendar-week-fill"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Calendar view</h5>
                    <p class="text-body-secondary mb-0">
                        See your week at a glance. Drag to reschedule.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="feature-visual">
                        <i class="bi bi-phone-fill"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Works everywhere</h5>
                    <p class="text-body-secondary mb-0">
                        Desktop, tablet, phone. Your tasks sync instantly.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="feature-visual">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Private by default</h5>
                    <p class="text-body-secondary mb-0">
                        Your data is yours. No ads, no tracking, no nonsense.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= TESTIMONIALS ================= -->
<section class="section bg-body-tertiary" id="testimonials">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge-tg mb-3 d-inline-block">Testimonials</span>
            <h2 class="display-5 fw-bold mb-3" style="letter-spacing:-.02em;">
                People actually finish things with Tasks Go.
            </h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card p-4 h-100 d-flex flex-column">
                    <div class="text-warning mb-3">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="mb-4 flex-grow-1">
                        "I've tried every task app out there. Tasks Go is the first one I've actually stuck with for more than a week."
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://i.pravatar.cc/48?img=32" class="rounded-circle" alt="" width="48" height="48" loading="lazy">
                        <div>
                            <div class="fw-semibold small">Sarah Chen</div>
                            <small class="text-body-secondary">Product Designer</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 h-100 d-flex flex-column">
                    <div class="text-warning mb-3">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="mb-4 flex-grow-1">
                        "The speed is unreal. I add tasks faster than I can forget them. My team adopted it in a day."
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://i.pravatar.cc/48?img=15" class="rounded-circle" alt="" width="48" height="48" loading="lazy">
                        <div>
                            <div class="fw-semibold small">Marcus Rivera</div>
                            <small class="text-body-secondary">Engineering Lead</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 h-100 d-flex flex-column">
                    <div class="text-warning mb-3">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="mb-4 flex-grow-1">
                        "Finally, a task manager that doesn't feel like a second job. Clean, fast, and it just works."
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://i.pravatar.cc/48?img=45" class="rounded-circle" alt="" width="48" height="48" loading="lazy">
                        <div>
                            <div class="fw-semibold small">Priya Patel</div>
                            <small class="text-body-secondary">Freelance Writer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section class="section" id="how">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge-tg mb-3 d-inline-block">How it works</span>
            <h2 class="display-5 fw-bold mb-3" style="letter-spacing:-.02em;">
                Three steps. That's it.
            </h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="feature-icon mb-3 mx-auto" style="width:56px;height:56px;font-size:1.5rem;">1</div>
                <h5 class="fw-semibold">Capture</h5>
                <p class="text-body-secondary">Jot down anything that crosses your mind. It goes to your inbox.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon mb-3 mx-auto" style="width:56px;height:56px;font-size:1.5rem;">2</div>
                <h5 class="fw-semibold">Organize</h5>
                <p class="text-body-secondary">Assign a project, a due date, and a priority. Done in seconds.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon mb-3 mx-auto" style="width:56px;height:56px;font-size:1.5rem;">3</div>
                <h5 class="fw-semibold">Complete</h5>
                <p class="text-body-secondary">Check things off, feel great, repeat tomorrow.</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= PRICING ================= -->
<section class="section bg-body-tertiary" id="pricing">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge-tg mb-3 d-inline-block">Pricing</span>
            <h2 class="display-5 fw-bold mb-3" style="letter-spacing:-.02em;">
                Simple, honest pricing.
            </h2>
            <p class="lead text-body-secondary">Start free. Upgrade when you need more.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4 h-100">
                    <h5 class="fw-semibold mb-1">Free</h5>
                    <p class="text-body-secondary small mb-3">For personal use</p>
                    <div class="d-flex align-items-baseline mb-4">
                        <span class="display-5 fw-bold">$0</span>
                        <span class="text-body-secondary ms-2">/ forever</span>
                    </div>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
                        <li><i class="bi bi-check2 text-success me-2"></i>Unlimited tasks</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Up to 3 projects</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Basic reminders</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Web + mobile access</li>
                    </ul>
                    <a href="register.php" class="btn btn-tg-outline w-100">Get started</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4 h-100 position-relative" style="border-color: var(--tg-accent); border-width: 2px;">
                    <span class="badge-tg position-absolute top-0 start-50 translate-middle">
                        Most popular
                    </span>
                    <h5 class="fw-semibold mb-1">Pro</h5>
                    <p class="text-body-secondary small mb-3">For power users</p>
                    <div class="d-flex align-items-baseline mb-4">
                        <span class="display-5 fw-bold">$6</span>
                        <span class="text-body-secondary ms-2">/ month</span>
                    </div>
                    <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
                        <li><i class="bi bi-check2 text-success me-2"></i>Everything in Free</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Unlimited projects</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Calendar view</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Recurring tasks</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Priority support</li>
                    </ul>
                    <a href="register.php" class="btn btn-tg w-100">Start free trial</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= CTA ================= -->
<section class="section pt-0">
    <div class="container">
        <div class="cta-band p-5 text-center">
            <h2 class="display-6 fw-bold mb-3" style="letter-spacing:-.02em;">
                Ready to get things done?
            </h2>
            <p class="lead mb-4 opacity-75">
                Join thousands of people who finish what they start.
            </p>
            <a href="register.php" class="btn btn-light btn-lg px-4 fw-medium">
                Start for free <i class="bi bi-arrow-right ms-1"></i>
            </a>
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
                    <li><a href="#features" class="text-body-secondary text-decoration-none">Features</a></li>
                    <li><a href="#pricing" class="text-body-secondary text-decoration-none">Pricing</a></li>
                    <li><a href="#" class="text-body-secondary text-decoration-none">Changelog</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="fw-semibold mb-3">Company</h6>
                <ul class="list-unstyled d-flex flex-column gap-2 small">
                    <li><a href="about-us.php" class="text-body-secondary text-decoration-none">About</a></li>
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