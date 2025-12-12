<?php

/**
 * Onboarding Tour Component
 * Role-aware first-time user onboarding using custom modal system
 * Verdant SMS v3.0
 */

// Check if user has completed onboarding
function has_completed_onboarding()
{
    if (!isset($_SESSION['user_id'])) return true;

    try {
        $user = db()->fetch("SELECT onboarding_completed FROM users WHERE id = ?", [$_SESSION['user_id']]);
        return $user && $user['onboarding_completed'] == 1;
    } catch (Exception $e) {
        // Column might not exist yet
        return true;
    }
}

// Mark onboarding as completed
function complete_onboarding($user_id)
{
    try {
        db()->query("UPDATE users SET onboarding_completed = 1 WHERE id = ?", [$user_id]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Get role-specific tour steps
function get_tour_steps($role)
{
    $common_steps = [
        [
            'target' => '.sidebar-brand',
            'title' => 'Welcome to Verdant SMS! 🌿',
            'content' => 'This is your personalized dashboard. Let us show you around!',
            'position' => 'right'
        ],
        [
            'target' => '.cyber-sidebar',
            'title' => 'Navigation Menu',
            'content' => 'Use this sidebar to access all features. On mobile, tap the hamburger icon to open it.',
            'position' => 'right'
        ],
        [
            'target' => '#themeBtn, .theme-toggle-btn',
            'title' => 'Customize Your Theme 🎨',
            'content' => 'Choose from 8 beautiful themes! Click here to personalize your experience.',
            'position' => 'left'
        ],
        [
            'target' => '#samsBotToggle',
            'title' => 'AI Assistant 🤖',
            'content' => 'Need help? Click here to chat with our AI assistant. It knows your role and can help with anything!',
            'position' => 'left'
        ]
    ];

    $role_steps = [];

    switch ($role) {
        case 'admin':
        case 'superadmin':
            $role_steps = [
                [
                    'target' => '.stat-orb, .dashboard-card',
                    'title' => 'System Overview',
                    'content' => 'Monitor key metrics at a glance - students, teachers, attendance, and more.',
                    'position' => 'bottom'
                ],
                [
                    'target' => '[href*="users"], [href*="manage"]',
                    'title' => 'User Management',
                    'content' => 'Manage all users, approve registrations, and control access.',
                    'position' => 'right'
                ],
                [
                    'target' => '[href*="settings"]',
                    'title' => 'System Settings',
                    'content' => 'Configure school info, enable features, and customize the system.',
                    'position' => 'right'
                ]
            ];
            break;

        case 'teacher':
        case 'class-teacher':
            $role_steps = [
                [
                    'target' => '[href*="attendance"]',
                    'title' => 'Take Attendance',
                    'content' => 'Quickly mark attendance for your classes with our smart system.',
                    'position' => 'right'
                ],
                [
                    'target' => '[href*="grades"], [href*="assignments"]',
                    'title' => 'Grades & Assignments',
                    'content' => 'Manage assignments, enter grades, and generate report cards.',
                    'position' => 'right'
                ],
                [
                    'target' => '[href*="messages"], [href*="communication"]',
                    'title' => 'Parent Communication',
                    'content' => 'Message parents directly and send class announcements.',
                    'position' => 'right'
                ]
            ];
            break;

        case 'student':
            $role_steps = [
                [
                    'target' => '.attendance-chart, [href*="attendance"]',
                    'title' => 'Your Attendance',
                    'content' => 'Track your attendance record and see your statistics.',
                    'position' => 'bottom'
                ],
                [
                    'target' => '[href*="grades"], [href*="results"]',
                    'title' => 'Grades & Results',
                    'content' => 'View your grades, download report cards, and track progress.',
                    'position' => 'right'
                ],
                [
                    'target' => '[href*="assignments"]',
                    'title' => 'Assignments',
                    'content' => 'Submit assignments, check deadlines, and view feedback.',
                    'position' => 'right'
                ]
            ];
            break;

        case 'parent':
            $role_steps = [
                [
                    'target' => '.children-list, .child-card',
                    'title' => 'Your Children',
                    'content' => 'View all your registered children and switch between their profiles.',
                    'position' => 'bottom'
                ],
                [
                    'target' => '[href*="attendance"]',
                    'title' => 'Attendance Tracking',
                    'content' => 'Monitor your child\'s attendance and receive alerts for absences.',
                    'position' => 'right'
                ],
                [
                    'target' => '[href*="fees"], [href*="payment"]',
                    'title' => 'Fee Payments',
                    'content' => 'View fee dues, payment history, and make online payments.',
                    'position' => 'right'
                ]
            ];
            break;

        default:
            $role_steps = [
                [
                    'target' => '.cyber-main',
                    'title' => 'Your Dashboard',
                    'content' => 'This is your main workspace. Explore the features in the sidebar!',
                    'position' => 'center'
                ]
            ];
    }

    $final_step = [
        [
            'target' => 'body',
            'title' => 'You\'re All Set! 🎉',
            'content' => 'Explore your dashboard and don\'t hesitate to use the AI assistant if you need help. Happy learning!',
            'position' => 'center'
        ]
    ];

    return array_merge($common_steps, $role_steps, $final_step);
}

$show_tour = !has_completed_onboarding();
$tour_steps = get_tour_steps($_SESSION['role'] ?? 'student');
?>

<?php if ($show_tour): ?>
    <!-- Onboarding Tour Overlay -->
    <div id="onboardingTour" class="tour-overlay">
        <div class="tour-backdrop"></div>
        <div class="tour-spotlight" id="tourSpotlight"></div>
        <div class="tour-tooltip" id="tourTooltip">
            <div class="tour-tooltip-arrow"></div>
            <div class="tour-tooltip-content">
                <div class="tour-step-indicator">
                    <span id="tourCurrentStep">1</span> / <span id="tourTotalSteps"><?php echo count($tour_steps); ?></span>
                </div>
                <h3 class="tour-title" id="tourTitle"></h3>
                <p class="tour-content" id="tourContent"></p>
                <div class="tour-actions">
                    <button class="tour-btn tour-btn-skip" onclick="skipTour()">Skip Tour</button>
                    <div class="tour-nav">
                        <button class="tour-btn tour-btn-prev" id="tourPrev" onclick="prevStep()">
                            <i class="fas fa-chevron-left"></i> Back
                        </button>
                        <button class="tour-btn tour-btn-next" id="tourNext" onclick="nextStep()">
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tour-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 100000;
            pointer-events: none;
        }

        .tour-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            pointer-events: auto;
        }

        .tour-spotlight {
            position: absolute;
            border-radius: 8px;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.75),
                0 0 30px rgba(16, 185, 129, 0.5),
                inset 0 0 20px rgba(16, 185, 129, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }

        .tour-tooltip {
            position: absolute;
            background: linear-gradient(135deg, rgba(20, 20, 30, 0.98), rgba(30, 30, 50, 0.98));
            border: 1px solid rgba(16, 185, 129, 0.5);
            border-radius: 16px;
            padding: 24px;
            max-width: 380px;
            min-width: 300px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5),
                0 0 40px rgba(16, 185, 129, 0.2);
            pointer-events: auto;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100001;
        }

        .tour-tooltip-arrow {
            position: absolute;
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, rgba(20, 20, 30, 0.98), rgba(30, 30, 50, 0.98));
            border: 1px solid rgba(16, 185, 129, 0.5);
            transform: rotate(45deg);
            border-right: none;
            border-top: none;
        }

        .tour-step-indicator {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 8px;
            font-family: 'Orbitron', monospace;
        }

        .tour-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0 0 12px 0;
            background: linear-gradient(135deg, #10b981, #06b6d4);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .tour-content {
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            margin: 0 0 20px 0;
            font-size: 0.95rem;
        }

        .tour-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .tour-nav {
            display: flex;
            gap: 8px;
        }

        .tour-btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            font-size: 0.9rem;
        }

        .tour-btn-skip {
            background: transparent;
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tour-btn-skip:hover {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.8);
        }

        .tour-btn-prev {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .tour-btn-prev:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .tour-btn-next {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .tour-btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }

        @media (max-width: 768px) {
            .tour-tooltip {
                max-width: 90vw;
                min-width: 280px;
                padding: 20px;
            }

            .tour-actions {
                flex-direction: column;
            }

            .tour-nav {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>

    <script>
        const tourSteps = <?php echo json_encode($tour_steps); ?>;
        let currentStep = 0;

        function showStep(stepIndex) {
            const step = tourSteps[stepIndex];
            const tooltip = document.getElementById('tourTooltip');
            const spotlight = document.getElementById('tourSpotlight');
            const title = document.getElementById('tourTitle');
            const content = document.getElementById('tourContent');
            const currentStepEl = document.getElementById('tourCurrentStep');
            const prevBtn = document.getElementById('tourPrev');
            const nextBtn = document.getElementById('tourNext');

            // Update content
            title.textContent = step.title;
            content.textContent = step.content;
            currentStepEl.textContent = stepIndex + 1;

            // Update buttons
            prevBtn.style.display = stepIndex === 0 ? 'none' : 'inline-flex';
            nextBtn.innerHTML = stepIndex === tourSteps.length - 1 ?
                'Finish <i class="fas fa-check"></i>' :
                'Next <i class="fas fa-chevron-right"></i>';

            // Find target element
            const target = document.querySelector(step.target);

            if (target && step.position !== 'center') {
                const rect = target.getBoundingClientRect();
                const padding = 10;

                // Position spotlight
                spotlight.style.display = 'block';
                spotlight.style.left = (rect.left - padding) + 'px';
                spotlight.style.top = (rect.top - padding) + 'px';
                spotlight.style.width = (rect.width + padding * 2) + 'px';
                spotlight.style.height = (rect.height + padding * 2) + 'px';

                // Position tooltip
                positionTooltip(tooltip, rect, step.position);
            } else {
                // Center position
                spotlight.style.display = 'none';
                tooltip.style.left = '50%';
                tooltip.style.top = '50%';
                tooltip.style.transform = 'translate(-50%, -50%)';
            }
        }

        function positionTooltip(tooltip, targetRect, position) {
            const tooltipRect = tooltip.getBoundingClientRect();
            const gap = 20;
            let left, top;

            switch (position) {
                case 'right':
                    left = targetRect.right + gap;
                    top = targetRect.top + (targetRect.height / 2) - (tooltipRect.height / 2);
                    break;
                case 'left':
                    left = targetRect.left - tooltipRect.width - gap;
                    top = targetRect.top + (targetRect.height / 2) - (tooltipRect.height / 2);
                    break;
                case 'bottom':
                    left = targetRect.left + (targetRect.width / 2) - (tooltipRect.width / 2);
                    top = targetRect.bottom + gap;
                    break;
                case 'top':
                    left = targetRect.left + (targetRect.width / 2) - (tooltipRect.width / 2);
                    top = targetRect.top - tooltipRect.height - gap;
                    break;
            }

            // Keep tooltip in viewport
            left = Math.max(20, Math.min(left, window.innerWidth - tooltipRect.width - 20));
            top = Math.max(20, Math.min(top, window.innerHeight - tooltipRect.height - 20));

            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
            tooltip.style.transform = 'none';
        }

        function nextStep() {
            if (currentStep < tourSteps.length - 1) {
                currentStep++;
                showStep(currentStep);
            } else {
                completeTour();
            }
        }

        function prevStep() {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        }

        function skipTour() {
            if (confirm('Skip the tour? You can restart it from Settings anytime.')) {
                completeTour();
            }
        }

        function completeTour() {
            const overlay = document.getElementById('onboardingTour');
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.3s';

            setTimeout(() => {
                overlay.remove();
            }, 300);

            // Save completion to server
            fetch('<?php echo rtrim(APP_URL ?? '/attendance', '/'); ?>/api/complete-onboarding.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo generate_csrf_token(); ?>'
                },
                body: JSON.stringify({
                    completed: true
                })
            }).catch(console.error);
        }

        // Start tour
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => showStep(0), 500);
        });

        // Handle keyboard
        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('onboardingTour')) return;

            if (e.key === 'ArrowRight' || e.key === 'Enter') nextStep();
            if (e.key === 'ArrowLeft') prevStep();
            if (e.key === 'Escape') skipTour();
        });
    </script>
<?php endif; ?>