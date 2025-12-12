<?php

/**
 * Visual Polish & Micro-interactions Component
 * Animations, loading states, celebrations, and UI enhancements
 * Verdant SMS v3.0
 */
?>

<!-- Visual Polish Styles -->
<style>
    /* ===== Loading States ===== */
    .skeleton {
        background: linear-gradient(90deg,
                rgba(255, 255, 255, 0.05) 25%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0.05) 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes skeleton-loading {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-text {
        height: 16px;
        margin-bottom: 8px;
    }

    .skeleton-text.short {
        width: 40%;
    }

    .skeleton-text.medium {
        width: 70%;
    }

    .skeleton-text.long {
        width: 90%;
    }

    .skeleton-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .skeleton-card {
        padding: 20px;
        background: rgba(20, 20, 20, 0.9);
        border-radius: 12px;
        border: 1px solid rgba(0, 255, 136, 0.1);
    }

    /* ===== Page Transitions ===== */
    .page-enter {
        opacity: 0;
        transform: translateY(20px);
    }

    .page-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .page-exit {
        opacity: 1;
    }

    .page-exit-active {
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    /* ===== Button Micro-interactions ===== */
    .btn-cyber {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-cyber::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s ease, height 0.6s ease;
    }

    .btn-cyber:active::before {
        width: 300px;
        height: 300px;
    }

    .btn-cyber:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 255, 136, 0.3);
    }

    .btn-cyber:active {
        transform: translateY(0);
    }

    /* Button Loading State */
    .btn-loading {
        position: relative;
        pointer-events: none;
        color: transparent !important;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: btn-spin 0.8s linear infinite;
    }

    @keyframes btn-spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ===== Input Focus Effects ===== */
    .input-cyber {
        position: relative;
        transition: all 0.3s ease;
    }

    .input-cyber:focus {
        border-color: #00ff88;
        box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
        outline: none;
    }

    .input-label {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        transition: all 0.3s ease;
        pointer-events: none;
        background: transparent;
        padding: 0 4px;
    }

    .input-cyber:focus+.input-label,
    .input-cyber:not(:placeholder-shown)+.input-label {
        top: 0;
        font-size: 0.75rem;
        color: #00ff88;
        background: rgba(20, 20, 20, 1);
    }

    /* ===== Card Hover Effects ===== */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .card-hover::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #00ff88, #00d4ff);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .card-hover:hover::before {
        transform: scaleX(1);
    }

    /* ===== Toast Notifications ===== */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 100000;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        background: rgba(20, 20, 20, 0.98);
        border: 1px solid rgba(0, 255, 136, 0.3);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        transform: translateX(120%);
        animation: toast-in 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .toast.removing {
        animation: toast-out 0.3s ease forwards;
    }

    @keyframes toast-in {
        to {
            transform: translateX(0);
        }
    }

    @keyframes toast-out {
        to {
            transform: translateX(120%);
            opacity: 0;
        }
    }

    .toast-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .toast.success .toast-icon {
        background: rgba(0, 255, 136, 0.2);
        color: #00ff88;
    }

    .toast.error .toast-icon {
        background: rgba(255, 107, 107, 0.2);
        color: #ff6b6b;
    }

    .toast.warning .toast-icon {
        background: rgba(255, 204, 0, 0.2);
        color: #ffcc00;
    }

    .toast.info .toast-icon {
        background: rgba(0, 212, 255, 0.2);
        color: #00d4ff;
    }

    .toast-content {
        flex: 1;
    }

    .toast-title {
        color: #fff;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .toast-message {
        color: #888;
        font-size: 0.9rem;
    }

    .toast-close {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 4px;
        transition: color 0.3s ease;
    }

    .toast-close:hover {
        color: #fff;
    }

    .toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #00ff88, #00d4ff);
        border-radius: 0 0 12px 12px;
        animation: toast-progress 5s linear forwards;
    }

    @keyframes toast-progress {
        from {
            width: 100%;
        }

        to {
            width: 0%;
        }
    }

    /* ===== Celebration Effects ===== */
    .celebration-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 100001;
    }

    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        animation: confetti-fall 3s ease-out forwards;
    }

    @keyframes confetti-fall {
        0% {
            opacity: 1;
            transform: translateY(-100vh) rotate(0deg);
        }

        100% {
            opacity: 0;
            transform: translateY(100vh) rotate(720deg);
        }
    }

    /* Achievement Popup */
    .achievement-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        background: linear-gradient(135deg, rgba(20, 20, 20, 0.98), rgba(30, 30, 30, 0.98));
        border: 2px solid #ffcc00;
        border-radius: 20px;
        padding: 40px 50px;
        text-align: center;
        z-index: 100002;
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .achievement-popup.active {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .achievement-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #ffcc00, #ff8800);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 20px;
        animation: achievement-bounce 0.6s ease;
    }

    @keyframes achievement-bounce {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }
    }

    .achievement-title {
        color: #ffcc00;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .achievement-description {
        color: #888;
        font-size: 1rem;
    }

    /* ===== Scroll Progress ===== */
    .scroll-progress {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #00ff88, #00d4ff, #8800ff);
        z-index: 99999;
        width: 0%;
        transition: width 0.1s ease;
    }

    /* ===== Ripple Effect ===== */
    .ripple {
        position: relative;
        overflow: hidden;
    }

    .ripple-effect {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* ===== Number Counter Animation ===== */
    .counter {
        display: inline-block;
    }

    .counter.animating {
        animation: counter-pulse 0.3s ease;
    }

    @keyframes counter-pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }
    }

    /* ===== Typing Indicator ===== */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 12px 16px;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        background: #00ff88;
        border-radius: 50%;
        animation: typing-bounce 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) {
        animation-delay: -0.32s;
    }

    .typing-dot:nth-child(2) {
        animation-delay: -0.16s;
    }

    @keyframes typing-bounce {

        0%,
        80%,
        100% {
            transform: scale(0.6);
            opacity: 0.5;
        }

        40% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* ===== Glow Effects ===== */
    .glow-green {
        box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
    }

    .glow-blue {
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    }

    .glow-purple {
        box-shadow: 0 0 20px rgba(136, 0, 255, 0.3);
    }

    .glow-pulse {
        animation: glow-pulse-animation 2s ease-in-out infinite;
    }

    @keyframes glow-pulse-animation {

        0%,
        100% {
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
        }

        50% {
            box-shadow: 0 0 40px rgba(0, 255, 136, 0.5);
        }
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .toast-container {
            top: auto;
            bottom: 20px;
            right: 10px;
            left: 10px;
        }

        .toast {
            min-width: auto;
            max-width: 100%;
        }
    }
</style>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Scroll Progress -->
<div class="scroll-progress" id="scrollProgress"></div>

<!-- Visual Polish JavaScript -->
<script>
    const VisualPolish = {
        init() {
            this.setupScrollProgress();
            this.setupRippleEffects();
            this.setupPageTransitions();
            this.setupCounterAnimations();
        },

        // Scroll Progress
        setupScrollProgress() {
            const progress = document.getElementById('scrollProgress');
            if (!progress) return;

            window.addEventListener('scroll', () => {
                const scrollTop = window.scrollY;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercent = (scrollTop / docHeight) * 100;
                progress.style.width = scrollPercent + '%';
            });
        },

        // Ripple Effects
        setupRippleEffects() {
            document.addEventListener('click', (e) => {
                const target = e.target.closest('.ripple');
                if (!target) return;

                const ripple = document.createElement('span');
                ripple.className = 'ripple-effect';

                const rect = target.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                ripple.style.top = e.clientY - rect.top - size / 2 + 'px';

                target.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        },

        // Page Transitions
        setupPageTransitions() {
            document.body.classList.add('page-enter-active');

            document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"])').forEach(link => {
                link.addEventListener('click', (e) => {
                    const href = link.getAttribute('href');
                    if (href.startsWith('javascript:') || href.startsWith('mailto:')) return;

                    e.preventDefault();
                    document.body.classList.add('page-exit-active');

                    setTimeout(() => {
                        window.location.href = href;
                    }, 200);
                });
            });
        },

        // Counter Animations
        setupCounterAnimations() {
            const counters = document.querySelectorAll('[data-counter]');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            counters.forEach(counter => observer.observe(counter));
        },

        animateCounter(element) {
            const target = parseInt(element.dataset.counter);
            const duration = 2000;
            const start = 0;
            const startTime = performance.now();

            const update = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(start + (target - start) * eased);

                element.textContent = current.toLocaleString();
                element.classList.add('animating');

                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    element.classList.remove('animating');
                }
            };

            requestAnimationFrame(update);
        }
    };

    // Toast System
    const Toast = {
        show(type, title, message, duration = 5000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const icons = {
                success: 'fa-check',
                error: 'fa-times',
                warning: 'fa-exclamation',
                info: 'fa-info'
            };

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
            <div class="toast-icon"><i class="fas ${icons[type]}"></i></div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="Toast.dismiss(this.parentElement)">
                <i class="fas fa-times"></i>
            </button>
            <div class="toast-progress" style="animation-duration: ${duration}ms"></div>
        `;

            container.appendChild(toast);

            setTimeout(() => this.dismiss(toast), duration);
        },

        dismiss(toast) {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 300);
        },

        success(title, message) {
            this.show('success', title, message);
        },
        error(title, message) {
            this.show('error', title, message);
        },
        warning(title, message) {
            this.show('warning', title, message);
        },
        info(title, message) {
            this.show('info', title, message);
        }
    };

    // Celebration System
    const Celebrate = {
        confetti(count = 50) {
            const overlay = document.createElement('div');
            overlay.className = 'celebration-overlay';
            document.body.appendChild(overlay);

            const colors = ['#00ff88', '#00d4ff', '#8800ff', '#ffcc00', '#ff6b6b'];

            for (let i = 0; i < count; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
                confetti.style.animationDelay = Math.random() * 0.5 + 's';
                overlay.appendChild(confetti);
            }

            setTimeout(() => overlay.remove(), 4000);
        },

        achievement(title, description, icon = '🏆') {
            const popup = document.createElement('div');
            popup.className = 'achievement-popup';
            popup.innerHTML = `
            <div class="achievement-icon">${icon}</div>
            <div class="achievement-title">${title}</div>
            <div class="achievement-description">${description}</div>
        `;
            document.body.appendChild(popup);

            setTimeout(() => popup.classList.add('active'), 100);
            this.confetti(30);

            setTimeout(() => {
                popup.classList.remove('active');
                setTimeout(() => popup.remove(), 500);
            }, 3000);
        }
    };

    // Skeleton Loading
    const Skeleton = {
        create(type = 'card') {
            const templates = {
                card: `
                <div class="skeleton-card">
                    <div class="skeleton skeleton-text short"></div>
                    <div class="skeleton skeleton-text long"></div>
                    <div class="skeleton skeleton-text medium"></div>
                </div>
            `,
                row: `
                <div style="display: flex; gap: 12px; align-items: center; padding: 12px;">
                    <div class="skeleton skeleton-avatar"></div>
                    <div style="flex: 1;">
                        <div class="skeleton skeleton-text medium"></div>
                        <div class="skeleton skeleton-text short"></div>
                    </div>
                </div>
            `
            };

            return templates[type] || templates.card;
        }
    };

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        VisualPolish.init();
    });

    // Export for global use
    window.Toast = Toast;
    window.Celebrate = Celebrate;
    window.Skeleton = Skeleton;
</script>