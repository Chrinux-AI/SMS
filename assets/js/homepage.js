// Homepage Interactive Features for Verdant SMS

document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initScrollEffects();
    initMobileMenu();
    initAnimations();
});

// Navigation Functionality
function initNavigation() {
    const header = document.querySelector('.main-header');
    const dropdownItems = document.querySelectorAll('.nav-item.has-dropdown');

    // Sticky header on scroll
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });

    // Dropdown navigation for mobile
    if (window.innerWidth <= 768) {
        dropdownItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            link.addEventListener('click', (e) => {
                e.preventDefault();
                item.classList.toggle('active');
            });
        });
    }
}

// Mobile Menu Toggle
function initMobileMenu() {
    const toggle = document.querySelector('.mobile-menu-toggle');
    const menu = document.querySelector('.nav-menu');
    const body = document.body;

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('active');
            body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';

            // Update icon
            const icon = toggle.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('active');
                body.style.overflow = '';
                const icon = toggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menu.classList.contains('active')) {
                menu.classList.remove('active');
                body.style.overflow = '';
                const icon = toggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
}

// Scroll Effects
function initScrollEffects() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    const headerOffset = 100;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

    // Parallax effect for hero background
    const heroBackground = document.querySelector('.hero-background');
    if (heroBackground) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            heroBackground.style.transform = `translateY(${scrolled * 0.5}px)`;
        });
    }
}

// Animations on Scroll
function initAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');

                // Stagger animation for grid items
                if (entry.target.classList.contains('feature-card') ||
                    entry.target.classList.contains('category-card') ||
                    entry.target.classList.contains('testimonial-card') ||
                    entry.target.classList.contains('pricing-card')) {

                    const siblings = Array.from(entry.target.parentElement.children);
                    const index = siblings.indexOf(entry.target);
                    entry.target.style.animationDelay = `${index * 0.1}s`;
                }

                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements with data-aos attribute
    document.querySelectorAll('[data-aos]').forEach(el => {
        observer.observe(el);
    });

    // Also observe cards
    document.querySelectorAll('.feature-card, .category-card, .testimonial-card, .pricing-card').forEach(el => {
        observer.observe(el);
    });
}

// Counter Animation for Stats
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);

    const updateCounter = () => {
        start += increment;
        if (start < target) {
            element.textContent = Math.floor(start);
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    };

    updateCounter();
}

// Initialize stat counters when visible
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statNumber = entry.target.querySelector('.stat-number');
            if (statNumber && !statNumber.classList.contains('counted')) {
                const targetValue = parseInt(statNumber.textContent);
                statNumber.classList.add('counted');
                animateCounter(statNumber, targetValue);
            }
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-item').forEach(stat => {
    statsObserver.observe(stat);
});

// Form Validation (for demo request, contact, etc.)
function initFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            let isValid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error');
                    showError(input, 'This field is required');
                } else {
                    input.classList.remove('error');
                    removeError(input);
                }

                // Email validation
                if (input.type === 'email' && input.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(input.value)) {
                        isValid = false;
                        input.classList.add('error');
                        showError(input, 'Please enter a valid email address');
                    }
                }

                // Phone validation
                if (input.type === 'tel' && input.value.trim()) {
                    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                    if (!phoneRegex.test(input.value)) {
                        isValid = false;
                        input.classList.add('error');
                        showError(input, 'Please enter a valid phone number');
                    }
                }
            });

            if (isValid) {
                // Handle form submission (AJAX or normal submit)
                submitForm(form);
            }
        });

        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('error');
                    showError(this, 'This field is required');
                } else {
                    this.classList.remove('error');
                    removeError(this);
                }
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('error') && this.value.trim()) {
                    this.classList.remove('error');
                    removeError(this);
                }
            });
        });
    });
}

function showError(input, message) {
    removeError(input);
    const error = document.createElement('div');
    error.className = 'error-message';
    error.textContent = message;
    error.style.color = '#ff4444';
    error.style.fontSize = '0.85rem';
    error.style.marginTop = '0.25rem';
    input.parentNode.appendChild(error);
}

function removeError(input) {
    const error = input.parentNode.querySelector('.error-message');
    if (error) {
        error.remove();
    }
}

function submitForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');

    if (submitBtn) {
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        // Simulate form submission (replace with actual AJAX call)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            showNotification('Success! Your message has been sent.', 'success');
            form.reset();
        }, 2000);
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div style="
            position: fixed;
            top: 100px;
            right: 20px;
            background: ${type === 'success' ? '#00ff7f' : '#00bfff'};
            color: #0a0a0a;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        ">
            <strong>${type === 'success' ? '✓' : 'ℹ'}</strong> ${message}
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Video Player Functionality (for demo section)
function initVideoPlayer() {
    const videoPlaceholder = document.querySelector('.demo-video-placeholder');
    const playButton = document.querySelector('.demo-play-button');

    if (videoPlaceholder && playButton) {
        playButton.addEventListener('click', () => {
            // Replace with actual video embed or modal
            const videoUrl = 'https://www.youtube.com/embed/YOUR_VIDEO_ID';
            const iframe = document.createElement('iframe');
            iframe.src = videoUrl;
            iframe.width = '100%';
            iframe.height = '100%';
            iframe.frameBorder = '0';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            iframe.allowFullscreen = true;

            videoPlaceholder.innerHTML = '';
            videoPlaceholder.appendChild(iframe);
        });
    }
}

// Pricing Calculator (if pricing page exists)
function initPricingCalculator() {
    const calculatorBtn = document.querySelector('.pricing-calculator-toggle');

    if (calculatorBtn) {
        calculatorBtn.addEventListener('click', () => {
            // Open calculator modal or navigate to pricing page
            window.location.href = 'pricing.php#calculator';
        });
    }
}

// Search Functionality (for modules/features)
function initSearch() {
    const searchInputs = document.querySelectorAll('.search-input');

    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const searchableItems = this.closest('section').querySelectorAll('[data-searchable]');

            searchableItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

// Initialize all interactive features
initFormValidation();
initVideoPlayer();
initPricingCalculator();
initSearch();

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
    .animate-in {
        animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    input.error, textarea.error, select.error {
        border-color: #ff4444 !important;
        box-shadow: 0 0 0 2px rgba(255, 68, 68, 0.2) !important;
    }
`;
document.head.appendChild(style);

// Console welcome message
console.log('%cVerdant SMS', 'font-size: 24px; font-weight: bold; background: linear-gradient(135deg, #00BFFF, #8A2BE2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;');
console.log('%c42-Module School Management System', 'font-size: 14px; color: #00BFFF;');
console.log('%cPowered by Advanced Technology Stack', 'font-size: 12px; color: #666;');
