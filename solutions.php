<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solutions - Verdant SMS | Tailored for Every Institution & Role</title>
    <meta name="description" content="Discover how Verdant SMS adapts to K-12 schools, universities, training centers, and academies. Customized dashboards for administrators, teachers, students, and parents.">

    <link rel="icon" href="assets/images/favicon.ico">
    <link rel="manifest" href="manifest.json">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/homepage.css">

    <style>
        .solution-tabs {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 3rem auto;
            max-width: 1000px;
            padding: 0 2rem;
        }

        .tab-btn {
            padding: 14px 28px;
            background: rgba(30, 30, 30, 0.8);
            border: 2px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-secondary);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-color: var(--primary-color);
            color: white;
        }

        .solution-content {
            display: none;
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .solution-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .solution-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .solution-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2.5rem;
            transition: all 0.3s ease;
        }

        .solution-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.2);
        }

        .solution-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .solution-card h3 {
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .solution-card p {
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .feature-list-compact {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list-compact li {
            padding: 0.5rem 0;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-list-compact li i {
            color: var(--accent-color);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <?php include 'includes/navigation.php'; ?>

    <section class="page-header">
        <div class="container">
            <h1>Solutions for Every Institution</h1>
            <p>Verdant SMS adapts to your unique needs, whether you're a K-12 school, university, training center, or specialized academy.</p>
        </div>
    </section>

    <!-- Tab Navigation -->
    <div class="solution-tabs">
        <button class="tab-btn active" data-tab="k12">K-12 Schools</button>
        <button class="tab-btn" data-tab="university">Universities</button>
        <button class="tab-btn" data-tab="training">Training Centers</button>
        <button class="tab-btn" data-tab="academy">Academies</button>
    </div>

    <!-- K-12 Solutions -->
    <div class="solution-content active" id="k12">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 class="section-title">K-12 School Solutions</h2>
            <p class="section-subtitle">Comprehensive management for primary and secondary education</p>
        </div>

        <div class="solution-grid">
            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>Student Management</h3>
                <p>Complete student lifecycle from enrollment to graduation with digital records and automated workflows.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Online enrollment & registration</li>
                    <li><i class="fas fa-check"></i> Digital student IDs with photos</li>
                    <li><i class="fas fa-check"></i> Transcript & report card generation</li>
                    <li><i class="fas fa-check"></i> Parent portal with real-time updates</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-clipboard-check"></i></div>
                <h3>Attendance & Discipline</h3>
                <p>Biometric attendance tracking with automated parent notifications and comprehensive discipline management.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Fingerprint & QR code scanning</li>
                    <li><i class="fas fa-check"></i> Instant SMS/WhatsApp alerts</li>
                    <li><i class="fas fa-check"></i> Behavior tracking & incident reports</li>
                    <li><i class="fas fa-check"></i> Detention scheduling</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3>Academic Excellence</h3>
                <p>Curriculum planning, gradebook management, and learning analytics to ensure student success.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> AI-powered grade predictions</li>
                    <li><i class="fas fa-check"></i> Standards-based grading</li>
                    <li><i class="fas fa-check"></i> Assignment submissions & rubrics</li>
                    <li><i class="fas fa-check"></i> Progress reports & transcripts</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-users"></i></div>
                <h3>Parent Engagement</h3>
                <p>Keep parents informed and involved with mobile apps, messaging, and real-time updates.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Mobile parent app (PWA)</li>
                    <li><i class="fas fa-check"></i> Two-way messaging system</li>
                    <li><i class="fas fa-check"></i> Event calendar & announcements</li>
                    <li><i class="fas fa-check"></i> Fee payment tracking</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- University Solutions -->
    <div class="solution-content" id="university">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 class="section-title">University & College Solutions</h2>
            <p class="section-subtitle">Advanced features for higher education institutions</p>
        </div>

        <div class="solution-grid">
            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-university"></i></div>
                <h3>Course Management</h3>
                <p>Flexible course catalogs, section management, and prerequisite enforcement for complex academic structures.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Multi-department course catalogs</li>
                    <li><i class="fas fa-check"></i> Prerequisite tracking</li>
                    <li><i class="fas fa-check"></i> Credit hour management</li>
                    <li><i class="fas fa-check"></i> Academic calendar builder</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-user-graduate"></i></div>
                <h3>Student Services</h3>
                <p>Comprehensive student lifecycle management from application to alumni engagement.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Online admissions portal</li>
                    <li><i class="fas fa-check"></i> Degree audit & planning</li>
                    <li><i class="fas fa-check"></i> Scholarship management</li>
                    <li><i class="fas fa-check"></i> Alumni network platform</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-book-reader"></i></div>
                <h3>LMS Integration</h3>
                <p>Deep integration with Canvas, Moodle, Blackboard via LTI 1.3 for unified learning experiences.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> LTI 1.3 certified</li>
                    <li><i class="fas fa-check"></i> Auto grade passback</li>
                    <li><i class="fas fa-check"></i> Single sign-on (SSO)</li>
                    <li><i class="fas fa-check"></i> Course roster sync</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-chart-bar"></i></div>
                <h3>Research & Analytics</h3>
                <p>Advanced reporting for institutional research, accreditation, and strategic planning.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Retention analytics</li>
                    <li><i class="fas fa-check"></i> Graduation rate tracking</li>
                    <li><i class="fas fa-check"></i> Accreditation reports</li>
                    <li><i class="fas fa-check"></i> Custom data exports</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Training Center Solutions -->
    <div class="solution-content" id="training">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 class="section-title">Training Center Solutions</h2>
            <p class="section-subtitle">Flexible tools for vocational and professional training programs</p>
        </div>

        <div class="solution-grid">
            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-certificate"></i></div>
                <h3>Certification Management</h3>
                <p>Track certifications, licenses, and continuing education requirements with automated renewals.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Digital certificate issuance</li>
                    <li><i class="fas fa-check"></i> CEU tracking</li>
                    <li><i class="fas fa-check"></i> Renewal notifications</li>
                    <li><i class="fas fa-check"></i> Certificate verification portal</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-calendar-alt"></i></div>
                <h3>Session Scheduling</h3>
                <p>Manage multiple concurrent sessions, instructor assignments, and facility bookings.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Multi-session courses</li>
                    <li><i class="fas fa-check"></i> Instructor availability tracking</li>
                    <li><i class="fas fa-check"></i> Room & equipment booking</li>
                    <li><i class="fas fa-check"></i> Waitlist management</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-dollar-sign"></i></div>
                <h3>Revenue Management</h3>
                <p>Flexible pricing, early bird discounts, group rates, and integrated payment processing.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Tiered pricing models</li>
                    <li><i class="fas fa-check"></i> Corporate billing</li>
                    <li><i class="fas fa-check"></i> Payment plans</li>
                    <li><i class="fas fa-check"></i> Refund management</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-users-cog"></i></div>
                <h3>Corporate Training</h3>
                <p>Dedicated portals for corporate clients with custom reporting and bulk enrollment.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> B2B client portals</li>
                    <li><i class="fas fa-check"></i> Bulk enrollment tools</li>
                    <li><i class="fas fa-check"></i> Custom progress reports</li>
                    <li><i class="fas fa-check"></i> Contract management</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Academy Solutions -->
    <div class="solution-content" id="academy">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 class="section-title">Specialized Academy Solutions</h2>
            <p class="section-subtitle">Tailored for music, sports, arts, and specialized learning institutions</p>
        </div>

        <div class="solution-grid">
            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-music"></i></div>
                <h3>Lesson Scheduling</h3>
                <p>Manage private lessons, group classes, and recitals with flexible scheduling tools.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> One-on-one lesson booking</li>
                    <li><i class="fas fa-check"></i> Instructor availability calendar</li>
                    <li><i class="fas fa-check"></i> Makeup lesson tracking</li>
                    <li><i class="fas fa-check"></i> Recital planning tools</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-trophy"></i></div>
                <h3>Performance Tracking</h3>
                <p>Track student progress, competitions, and achievements with multimedia portfolios.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Skill level assessments</li>
                    <li><i class="fas fa-check"></i> Competition results tracking</li>
                    <li><i class="fas fa-check"></i> Video/audio portfolios</li>
                    <li><i class="fas fa-check"></i> Achievement badges</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-warehouse"></i></div>
                <h3>Equipment Management</h3>
                <p>Track instrument rentals, equipment loans, and inventory with maintenance schedules.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Rental agreements</li>
                    <li><i class="fas fa-check"></i> Damage tracking</li>
                    <li><i class="fas fa-check"></i> Maintenance logs</li>
                    <li><i class="fas fa-check"></i> Inventory management</li>
                </ul>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="fas fa-ticket-alt"></i></div>
                <h3>Event Management</h3>
                <p>Organize performances, exhibitions, and showcases with ticketing and participant management.</p>
                <ul class="feature-list-compact">
                    <li><i class="fas fa-check"></i> Event registration</li>
                    <li><i class="fas fa-check"></i> Ticket sales</li>
                    <li><i class="fas fa-check"></i> Participant scheduling</li>
                    <li><i class="fas fa-check"></i> Program generation</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Role-Based Solutions Section -->
    <section class="features-section" style="background: rgba(10, 10, 10, 0.8); margin-top: 6rem;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 class="section-title">Solutions by User Role</h2>
                <p class="section-subtitle">Customized dashboards and features for every stakeholder</p>
            </div>

            <div class="solution-grid">
                <div class="solution-card">
                    <div class="solution-icon"><i class="fas fa-user-shield"></i></div>
                    <h3>For Administrators</h3>
                    <p>Command center with real-time analytics, system settings, and comprehensive oversight tools.</p>
                    <a href="modules.php?role=admin" class="btn btn-outline">Explore Admin Features</a>
                </div>

                <div class="solution-card">
                    <div class="solution-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <h3>For Teachers</h3>
                    <p>Streamlined gradebook, attendance tools, and communication features to focus on teaching.</p>
                    <a href="modules.php?role=teacher" class="btn btn-outline">View Teacher Dashboard</a>
                </div>

                <div class="solution-card">
                    <div class="solution-icon"><i class="fas fa-user-graduate"></i></div>
                    <h3>For Students</h3>
                    <p>Self-service portal for grades, schedules, assignments, and extracurricular activities.</p>
                    <a href="modules.php?role=student" class="btn btn-outline">Student Portal Demo</a>
                </div>

                <div class="solution-card">
                    <div class="solution-icon"><i class="fas fa-users"></i></div>
                    <h3>For Parents</h3>
                    <p>Stay informed with real-time updates on attendance, grades, and school communications.</p>
                    <a href="modules.php?role=parent" class="btn btn-outline">Parent Features</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container cta-content">
            <h2>Find Your Perfect Solution</h2>
            <p>Let's discuss how Verdant SMS can be customized for your institution's unique needs.</p>
            <div class="cta-buttons">
                <a href="demo-request.php" class="btn btn-white btn-large">
                    <i class="fas fa-calendar-check"></i> Schedule Demo
                </a>
                <a href="contact.php" class="btn btn-outline-white btn-large">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/homepage.js"></script>
    <script>
        // Solution tabs functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.solution-content');

        // Check URL parameter on page load
        const urlParams = new URLSearchParams(window.location.search);
        const typeParam = urlParams.get('type');

        if (typeParam) {
            tabBtns.forEach(btn => {
                if (btn.dataset.tab === typeParam) {
                    switchTab(btn);
                }
            });
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                switchTab(btn);

                // Update URL without reload
                const newUrl = new URL(window.location);
                newUrl.searchParams.set('type', btn.dataset.tab);
                window.history.pushState({}, '', newUrl);
            });
        });

        function switchTab(activeBtn) {
            // Remove active from all
            tabBtns.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Add active to clicked
            activeBtn.classList.add('active');
            const tabId = activeBtn.dataset.tab;
            document.getElementById(tabId).classList.add('active');
        }
    </script>
</body>

</html>