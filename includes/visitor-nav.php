<?php

/**
 * Visitor Navigation Component
 * Public navigation for unauthenticated visitors
 */
?>
<header class="visitor-header">
    <nav class="visitor-nav">
        <a href="/" class="logo">
            <div class="logo-icon"><i class="fas fa-leaf"></i></div>
            <span class="logo-text">Verdant SMS</span>
        </a>

        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Menu">
            <i class="fas fa-bars"></i>
        </button>

        <ul class="nav-links" id="navLinks">
            <li><a href="/"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="/visitor/about.php"><i class="fas fa-info-circle"></i> About</a></li>
            <li><a href="/visitor/features.php"><i class="fas fa-star"></i> Features</a></li>
            <li><a href="/visitor/demo-request.php"><i class="fas fa-calendar-check"></i> Request Demo</a></li>
            <li><a href="/visitor/faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
            <li><a href="/visitor/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
        </ul>

        <div class="nav-auth">
            <a href="/login.php" class="btn btn-outline">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="/register.php" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </div>
    </nav>
</header>

<style>
    .visitor-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: rgba(10, 10, 15, 0.95);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 191, 255, 0.2);
        z-index: 1000;
    }

    .visitor-nav {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .logo-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #00BFFF, #8A2BE2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .logo-text {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #00BFFF, #00FF7F);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .nav-links {
        display: flex;
        list-style: none;
        gap: 0.5rem;
        margin: 0;
        padding: 0;
    }

    .nav-links a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .nav-links a:hover {
        color: #00BFFF;
        background: rgba(0, 191, 255, 0.1);
    }

    .nav-links a.active {
        color: #00BFFF;
        background: rgba(0, 191, 255, 0.15);
    }

    .nav-auth {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #00BFFF, #8A2BE2);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 191, 255, 0.4);
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #00BFFF;
        color: #00BFFF;
    }

    .btn-outline:hover {
        background: #00BFFF;
        color: #0a0a0f;
    }

    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.5rem;
    }

    @media (max-width: 1024px) {
        .mobile-menu-btn {
            display: block;
        }

        .nav-links {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            flex-direction: column;
            background: rgba(10, 10, 15, 0.98);
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 191, 255, 0.2);
        }

        .nav-links.active {
            display: flex;
        }

        .nav-auth {
            display: none;
        }

        .nav-links.active~.nav-auth {
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            padding: 1rem;
            gap: 0.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        menuBtn?.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });

        // Highlight current page in nav
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-links a').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });
</script>