<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About — Tasks Go</title>
    <meta name="description" content="Tasks Go is built by a small team obsessed with helping people finish what they start.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="about-us.css">
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
                <li class="nav-item"><a class="nav-link active" href="about-us.php">About</a></li>
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
<header class="about-hero">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <span class="badge-tg d-inline-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-people"></i> About Tasks Go
                </span>
                <h1 class="display-3 fw-bold lh-1 mb-4" style="letter-spacing:-.02em;">
                    Built for people who<br>
                    <span style="color: var(--tg-accent);">finish what they start.</span>
                </h1>
                <p class="lead text-body-secondary mb-0 mx-auto" style="max-width: 640px;">
                    Tasks Go started as a small frustration: every task app was either too simple
                    or too complicated. So we built the one we wanted to use.
                </p>
            </div>
        </div>
    </div>
</header>

<!-- ================= STATS ================= -->
<section class="py-5 border-top border-bottom">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 stat-block mb-4 mb-md-0">
                <div class="display-5 fw-bold" style="color: var(--tg-accent);">12,000+</div>
                <p class="text-body-secondary mb-0">Active users</p>
            </div>
            <div class="col-md-3 stat-block mb-4 mb-md-0">
                <div class="display-5 fw-bold" style="color: var(--tg-accent);">2.4M</div>
                <p class="text-body-secondary mb-0">Tasks completed</p>
            </div>
            <div class="col-md-3 stat-block mb-4 mb-md-0">
                <div class="display-5 fw-bold" style="color: var(--tg-accent);">99.9%</div>
                <p class="text-body-secondary mb-0">Uptime</p>
            </div>
            <div class="col-md-3 stat-block">
                <div class="display-5 fw-bold" style="color: var(--tg-accent);">2019</div>
                <p class="text-body-secondary mb-0">Founded</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= STORY ================= -->
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="badge-tg mb-3 d-inline-block">Our story</span>
                <h2 class="display-5 fw-bold mb-4" style="letter-spacing:-.02em;">
                    We got tired of task apps that felt like a second job.
                </h2>
                <p class="text-body-secondary mb-3">
                    Tasks Go began in 2019 as a side project between three friends who couldn't
                    find a task manager that was fast, clean, and actually pleasant to use.
                    Every app we tried was either a glorified to-do list or a project management
                    monster that needed its own onboarding.
                </p>
                <p class="text-body-secondary mb-3">
                    So we built our own. Simple enough to add a task in two seconds. Powerful
                    enough to run a small team. And fast — always fast.
                </p>
                <p class="text-body-secondary mb-0">
                    Six years later, Tasks Go helps over 12,000 people finish millions of things.
                    We're still a small team, still obsessed with speed, and still building the
                    app we want to use every day.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=1200&q=80"
                     alt="The Tasks Go team working together"
                     class="img-fluid rounded-4 shadow-sm"
                     loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- ================= VALUES ================= -->
<section class="section bg-body-tertiary">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge-tg mb-3 d-inline-block">What we believe</span>
            <h2 class="display-5 fw-bold mb-3" style="letter-spacing:-.02em;">
                Principles we build by.
            </h2>
            <p class="lead text-body-secondary mx-auto" style="max-width: 560px;">
                These aren't marketing words. They're the rules we ship against.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="value-icon mb-3"><i class="bi bi-lightning-charge"></i></div>
                    <h5 class="fw-semibold mb-2">Speed over features</h5>
                    <p class="text-body-secondary mb-0">
                        We'd rather do 10 things instantly than 100 things slowly. Performance is a feature.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="value-icon mb-3"><i class="bi bi-shield-check"></i></div>
                    <h5 class="fw-semibold mb-2">Your data is yours</h5>
                    <p class="text-body-secondary mb-0">
                        No ads. No tracking. No selling data. Ever. You can export everything, anytime.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="value-icon mb-3"><i class="bi bi-hand-thumbs-up"></i></div>
                    <h5 class="fw-semibold mb-2">Simple by default</h5>
                    <p class="text-body-secondary mb-0">
                        Powerful options exist — but you never have to use them. The defaults are the product.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="value-icon mb-3"><i class="bi bi-chat-heart"></i></div>
                    <h5 class="fw-semibold mb-2">Built with users</h5>
                    <p class="text-body-secondary mb-0">
                        Every major feature came from a real request. We read every message.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="value-icon mb-3"><i class="bi bi-globe"></i></div>
                    <h5 class="fw-semibold mb-2">Works everywhere</h5>
                    <p class="text-body-secondary mb-0">
                        Desktop, tablet, phone, browser. No install required to start.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="feature-card p-4">
                    <div class="value-icon mb-3"><i class="bi bi-heart"></i></div>
                    <h5 class="fw-semibold mb-2">Independent forever</h5>
                    <p class="text-body-secondary mb-0">
                        No VC money, no exit plan. We answer to users, not investors.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= TIMELINE ================= -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <span class="badge-tg mb-3 d-inline-block">Milestones</span>
                <h2 class="display-5 fw-bold mb-3" style="letter-spacing:-.02em;">
                    How we got here.
                </h2>
                <p class="text-body-secondary">
                    From a weekend side project to a tool used by thousands of teams around the world.
                </p>
            </div>
            <div class="col-lg-8">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="text-body-secondary small fw-medium mb-1">2019</div>
                        <h5 class="fw-semibold mb-2">The frustration begins</h5>
                        <p class="text-body-secondary mb-0">
                            Three friends, one shared problem: no task app felt right. So we started building.
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="text-body-secondary small fw-medium mb-1">2020</div>
                        <h5 class="fw-semibold mb-2">First public launch</h5>
                        <p class="text-body-secondary mb-0">
                            Released as a free beta. 500 users in the first month, mostly from word of mouth.
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="text-body-secondary small fw-medium mb-1">2022</div>
                        <h5 class="fw-semibold mb-2">Projects &amp; teams</h5>
                        <p class="text-body-secondary mb-0">
                            Added projects, shared lists, and the calendar view. Tasks Go became a team tool.
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="text-body-secondary small fw-medium mb-1">2024</div>
                        <h5 class="fw-semibold mb-2">10,000 users</h5>
                        <p class="text-body-secondary mb-0">
                            Crossed 10,000 active users and 2 million completed tasks.
                        </p>
                    </div>
                    <div class="timeline-item">
                        <div class="text-body-secondary small fw-medium mb-1">Today</div>
                        <h5 class="fw-semibold mb-2">Still shipping</h5>
                        <p class="text-body-secondary mb-0">
                            Small team, big plans. New features ship every month, always free for personal use.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= TEAM ================= -->
<section class="section bg-body-tertiary">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge-tg mb-3 d-inline-block">The team</span>
            <h2 class="display-5 fw-bold mb-3" style="letter-spacing:-.02em;">
                Small team. Big obsession.
            </h2>
            <p class="lead text-body-secondary mx-auto" style="max-width: 560px;">
                Six people spread across three time zones, all focused on the same thing.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card text-center">
                    <img src="https://i.pravatar.cc/300?img=32" alt="Sarah Chen" class="team-avatar mb-3" loading="lazy">
                    <h6 class="fw-semibold mb-0">Sarah Chen</h6>
                    <small class="text-body-secondary">Co-founder &amp; Design</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card text-center">
                    <img src="https://i.pravatar.cc/300?img=15" alt="Marcus Rivera" class="team-avatar mb-3" loading="lazy">
                    <h6 class="fw-semibold mb-0">Marcus Rivera</h6>
                    <small class="text-body-secondary">Co-founder &amp; Engineering</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card text-center">
                    <img src="https://i.pravatar.cc/300?img=45" alt="Priya Patel" class="team-avatar mb-3" loading="lazy">
                    <h6 class="fw-semibold mb-0">Priya Patel</h6>
                    <small class="text-body-secondary">Co-founder &amp; Product</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card text-center">
                    <img src="https://i.pravatar.cc/300?img=12" alt="James Okafor" class="team-avatar mb-3" loading="lazy">
                    <h6 class="fw-semibold mb-0">James Okafor</h6>
                    <small class="text-body-secondary">Backend Engineer</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card text-center">
                    <img src="https://i.pravatar.cc/300?img=47" alt="Elena Novak" class="team-avatar mb-3" loading="lazy">
                    <h6 class="fw-semibold mb-0">Elena Novak</h6>
                    <small class="text-body-secondary">Frontend Engineer</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="team-card text-center">
                    <img src="https://i.pravatar.cc/300?img=68" alt="Tom Becker" class="team-avatar mb-3" loading="lazy">
                    <h6 class="fw-semibold mb-0">Tom Becker</h6>
                    <small class="text-body-secondary">Customer Success</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= CTA ================= -->
<section class="section pt-0" id="contact">
    <div class="container">
        <div class="cta-band p-5 text-center">
            <h2 class="display-6 fw-bold mb-3" style="letter-spacing:-.02em;">
                Want to say hi?
            </h2>
            <p class="lead mb-4 opacity-75 mx-auto" style="max-width: 520px;">
                Questions, feedback, or just want to share what you're working on?
                We read every message.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="mailto:hello@tasksgo.app" class="btn btn-light btn-lg px-4 fw-medium">
                    <i class="bi bi-envelope me-1"></i> hello@tasksgo.app
                </a>
                <a href="register.php" class="btn btn-outline-light btn-lg px-4 fw-medium">
                    Start for free <i class="bi bi-arrow-right ms-1"></i>
                </a>
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
                    <li><a href="mailto:hello@tasksgo.app" class="text-body-secondary text-decoration-none">Contact</a></li>
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