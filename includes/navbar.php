<?php
//session_start();

$is_logged_in = isset($_SESSION['user_id']);
$full_name = $_SESSION['full_name'] ?? '';

// Extract first name from full name
$first_name = '';
if (!empty($full_name)) {
    $name_parts = explode(' ', $full_name);
    $first_name = $name_parts[0];
}
?>

<header class="navbar">
    <div class="nav-container">
        <div class="logo-section">
            <span class="material-symbols-outlined logo-icon">location_searching</span>
            <h2>
                <a href="dashboard.php" class="logo-link">Smart Lost & Found</a>
            </h2>
        </div>

        <button class="hamburger-menu" id="hamburger-toggle" aria-label="Toggle Menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>

        <nav class="nav-links" id="nav-links">
            <a href="dashboard.php" class="nav-item-link">Home</a>
            <a href="search.php" class="nav-item-link">Search</a>

            <?php if ($is_logged_in): ?>
                <a href="upload-item.php" class="report-btn">
                    <span class="material-symbols-outlined">add_circle</span> Report Item
                </a>
                <div class="user-menu">
                    <div class="user-avatar">
                        <span class="material-symbols-outlined">account_circle</span>
                    </div>
                    <span class="user-name"><?php echo htmlspecialchars($first_name); ?></span>
                    <a href="logout.php" class="logout-btn">
                        <span class="material-symbols-outlined">logout</span> Logout
                    </a>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            <?php endif; ?>

            <!-- Sliding underline indicator for standard tabs -->
            <div class="nav-indicator" id="nav-indicator"></div>
        </nav>
    </div>
</header>

<link rel="stylesheet" href="assets/css/navbar.css">

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sticky scroll shrink logic
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile Menu Toggle logic
        const hamburger = document.getElementById('hamburger-toggle');
        const navLinks = document.getElementById('nav-links');

        if (hamburger && navLinks) {
            hamburger.addEventListener('click', (e) => {
                e.stopPropagation();
                hamburger.classList.toggle('active');
                navLinks.classList.toggle('open');
            });

            // Close mobile menu if clicked outside
            document.addEventListener('click', (e) => {
                if (!navLinks.contains(e.target) && !hamburger.contains(e.target)) {
                    hamburger.classList.remove('active');
                    navLinks.classList.remove('open');
                }
            });
        }

        // Sliding Underline Tab Indicator logic
        const links = document.querySelectorAll('.nav-links .nav-item-link');
        const indicator = document.getElementById('nav-indicator');

        function updateIndicator(element) {
            if (element && indicator) {
                if (window.innerWidth > 768) {
                    indicator.style.width = `${element.offsetWidth}px`;
                    indicator.style.left = `${element.offsetLeft}px`;
                    indicator.style.opacity = '1';
                }
            } else if (indicator) {
                indicator.style.opacity = '0';
            }
        }

        // Get current page filename
        let currentPath = window.location.pathname.split('/').pop();
        if (currentPath === '') {
            currentPath = 'dashboard.php';
        }

        let activeLink = null;

        links.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (currentPath === linkHref || (currentPath === 'index.php' && linkHref === 'dashboard.php')) {
                link.classList.add('active');
                activeLink = link;
            }

            link.addEventListener('mouseenter', (e) => {
                updateIndicator(e.target);
            });

            link.addEventListener('mouseleave', () => {
                updateIndicator(activeLink);
            });
        });

        // Initialize indicator position
        setTimeout(() => {
            updateIndicator(activeLink);
        }, 100);

        // Recalculate on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768 && indicator) {
                indicator.style.opacity = '0';
            } else {
                updateIndicator(activeLink);
            }
        });
    });
</script>