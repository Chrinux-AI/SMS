<?php

/**
 * Theme Toggle Component
 * Include this file on any page to add theme switching functionality
 * Usage: <?php include 'includes/theme-toggle.php'; ?>
 */
?>
<!-- Theme Toggle Button -->
<button class="theme-toggle-btn" id="themeToggle" title="Toggle Theme" aria-label="Toggle between light and dark theme">
  <i class="fas fa-sun" id="themeIcon"></i>
</button>

<style>
  /* Theme Toggle Button */
  .theme-toggle-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid var(--cyber-cyan, #00BFFF);
    background: rgba(10, 10, 10, 0.9);
    color: var(--cyber-cyan, #00BFFF);
    cursor: pointer;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(0, 191, 255, 0.3);
  }

  .theme-toggle-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 0 30px rgba(0, 191, 255, 0.5);
    background: var(--cyber-cyan, #00BFFF);
    color: #000;
  }

  /* Nature Theme Overrides */
  body.nature-theme .theme-toggle-btn {
    border-color: #10b981;
    color: #10b981;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
  }

  body.nature-theme .theme-toggle-btn:hover {
    background: #10b981;
    color: #fff;
    box-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
  }

  /* Nature Theme Global Styles */
  body.nature-theme {
    --cyber-cyan: #10b981;
    --hologram-purple: #059669;
    --neon-green: #34d399;
    --void-black: #0f1f0f;
    --space-dark: #1a2e1a;
    --text-primary: #e0ffe0;
    --text-secondary: #a0d0a0;
  }

  body.nature-theme .cyber-bg,
  body.nature-theme .cyber-layout {
    background: linear-gradient(135deg, #0f1f0f 0%, #1a2e1a 50%, #0d1a0d 100%);
  }

  body.nature-theme .starfield {
    background-image:
      radial-gradient(2px 2px at 20px 30px, #34d399, transparent),
      radial-gradient(2px 2px at 60px 70px, #10b981, transparent),
      radial-gradient(1px 1px at 50px 50px, #34d399, transparent);
    opacity: 0.2;
  }

  body.nature-theme .cyber-grid {
    background-image:
      linear-gradient(rgba(16, 185, 129, 0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(16, 185, 129, 0.03) 1px, transparent 1px);
  }

  body.nature-theme .holo-card,
  body.nature-theme .register-card,
  body.nature-theme .login-hologram {
    border-color: rgba(16, 185, 129, 0.3);
    box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
  }

  body.nature-theme .cyber-btn-primary,
  body.nature-theme .btn-submit {
    background: linear-gradient(135deg, #10b981, #059669);
  }

  body.nature-theme .cyber-sidebar {
    background: linear-gradient(180deg, #0f1f0f 0%, #0a150a 100%);
    border-right-color: rgba(16, 185, 129, 0.2);
  }

  body.nature-theme .nav-link:hover,
  body.nature-theme .nav-link.active {
    background: rgba(16, 185, 129, 0.1);
    border-left-color: #10b981;
  }

  body.nature-theme .cyber-input:focus {
    border-color: #10b981;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
  }
</style>

<script>
  (function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    // Load saved theme
    const savedTheme = localStorage.getItem('sms-theme') || 'cyberpunk';
    if (savedTheme === 'nature') {
      document.body.classList.add('nature-theme');
      themeIcon.className = 'fas fa-leaf';
    }

    themeToggle.addEventListener('click', function() {
      document.body.classList.toggle('nature-theme');
      const isNature = document.body.classList.contains('nature-theme');

      if (isNature) {
        themeIcon.className = 'fas fa-leaf';
        localStorage.setItem('sms-theme', 'nature');
      } else {
        themeIcon.className = 'fas fa-sun';
        localStorage.setItem('sms-theme', 'cyberpunk');
      }

      // Add rotation animation
      themeToggle.style.transform = 'rotate(360deg)';
      setTimeout(() => {
        themeToggle.style.transform = '';
      }, 300);
    });
  })();
</script>
