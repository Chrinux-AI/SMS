<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore all 42 comprehensive modules of Verdant SMS - Complete school management solution covering academics, finance, HR, operations and more.">
    <title>All 42 Modules - Verdant SMS</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/homepage.css">

    <style>
        .modules-hero {
            background: linear-gradient(135deg, rgba(0, 191, 255, 0.1), rgba(138, 43, 226, 0.1));
            padding: 150px 0 100px;
            text-align: center;
        }

        .modules-filter {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 3rem 0;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid rgba(0, 191, 255, 0.3);
            border-radius: 50px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: var(--gradient-primary);
            color: white;
            border-color: transparent;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .module-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 2rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .module-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0 40px rgba(0, 191, 255, 0.3);
            transform: translateY(-5px);
        }

        .module-header {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .module-icon {
            width: 60px;
            height: 60px;
            min-width: 60px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
        }

        .module-info h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }

        .module-category {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: rgba(0, 191, 255, 0.2);
            border-radius: 50px;
            color: var(--primary-color);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .module-description {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .module-features {
            list-style: none;
            margin-bottom: 1.5rem;
        }

        .module-features li {
            padding: 0.5rem 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .module-features li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--accent-color);
            position: absolute;
            left: 0;
        }

        .module-roles {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            background: rgba(138, 43, 226, 0.2);
            border-radius: 50px;
            color: var(--secondary-color);
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <?php include 'includes/public-header.php'; ?>

    <section class="modules-hero">
        <div class="container">
            <span class="section-badge">Complete Platform</span>
            <h1 class="section-title">All 42 Modules</h1>
            <p class="section-subtitle">Comprehensive school management solution covering every aspect of educational institution operations</p>

            <div class="modules-filter">
                <button class="filter-btn active" data-category="all">All Modules</button>
                <button class="filter-btn" data-category="core">Core Academic</button>
                <button class="filter-btn" data-category="finance">Finance</button>
                <button class="filter-btn" data-category="hr">Human Resources</button>
                <button class="filter-btn" data-category="operations">Operations</button>
                <button class="filter-btn" data-category="communication">Communication</button>
                <button class="filter-btn" data-category="facilities">Facilities</button>
            </div>
        </div>
    </section>

    <section class="modules-section">
        <div class="container">
            <div class="modules-grid" id="modulesGrid">
                <!-- Core Academic Modules -->
                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-user-graduate"></i></div>
                        <div class="module-info">
                            <h3>Student Management</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Complete student lifecycle management from admission to alumni tracking with detailed profiles and academic records.</p>
                    <ul class="module-features">
                        <li>Student registration & admission</li>
                        <li>Academic records & transcripts</li>
                        <li>Enrollment & class assignment</li>
                        <li>Student ID card generation</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Registrar</span>
                        <span class="role-badge">Student</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="module-info">
                            <h3>Teacher Portal</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Comprehensive teacher management with class scheduling, lesson planning, and performance tracking.</p>
                    <ul class="module-features">
                        <li>Teacher profiles & credentials</li>
                        <li>Class schedule management</li>
                        <li>Lesson plan creation</li>
                        <li>Performance evaluation</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Teacher</span>
                        <span class="role-badge">Principal</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-fingerprint"></i></div>
                        <div class="module-info">
                            <h3>Attendance System</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Advanced biometric and manual attendance tracking with real-time notifications and comprehensive reports.</p>
                    <ul class="module-features">
                        <li>Biometric fingerprint scanning</li>
                        <li>Manual attendance marking</li>
                        <li>Real-time parent notifications</li>
                        <li>Attendance analytics & reports</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Teacher</span>
                        <span class="role-badge">Student</span>
                        <span class="role-badge">Parent</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-book"></i></div>
                        <div class="module-info">
                            <h3>Academic Management</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Complete academic operations including curriculum, subjects, classes, and academic calendar management.</p>
                    <ul class="module-features">
                        <li>Curriculum & syllabus management</li>
                        <li>Subject & class creation</li>
                        <li>Academic calendar</li>
                        <li>Grading system setup</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Principal</span>
                        <span class="role-badge">Academic Head</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-clipboard-list"></i></div>
                        <div class="module-info">
                            <h3>Examination & Grading</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Comprehensive exam management from creation to result publication with automated grade calculations.</p>
                    <ul class="module-features">
                        <li>Exam schedule creation</li>
                        <li>Online & offline exams</li>
                        <li>Automated grade calculation</li>
                        <li>Report card generation</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Teacher</span>
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Student</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-users"></i></div>
                        <div class="module-info">
                            <h3>Parent Portal</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Dedicated parent interface for monitoring child's academic progress, attendance, and school communications.</p>
                    <ul class="module-features">
                        <li>Student progress tracking</li>
                        <li>Attendance monitoring</li>
                        <li>Fee payment portal</li>
                        <li>Teacher communication</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Parent</span>
                        <span class="role-badge">Guardian</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-tasks"></i></div>
                        <div class="module-info">
                            <h3>Homework & Assignments</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Digital homework management with online submission, grading, and deadline tracking.</p>
                    <ul class="module-features">
                        <li>Assignment creation & distribution</li>
                        <li>Online submission portal</li>
                        <li>Automatic plagiarism detection</li>
                        <li>Grading & feedback system</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Teacher</span>
                        <span class="role-badge">Student</span>
                        <span class="role-badge">Parent</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-book-reader"></i></div>
                        <div class="module-info">
                            <h3>LMS Integration</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Seamless Learning Management System integration via LTI 1.3 for unified educational experience.</p>
                    <ul class="module-features">
                        <li>Canvas, Moodle, Blackboard support</li>
                        <li>Single sign-on (SSO)</li>
                        <li>Grade synchronization</li>
                        <li>Content deep linking</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Teacher</span>
                        <span class="role-badge">Student</span>
                    </div>
                </div>

                <div class="module-card" data-category="core">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="module-info">
                            <h3>Timetable Management</h3>
                            <span class="module-category">Core Academic</span>
                        </div>
                    </div>
                    <p class="module-description">Intelligent timetable generation with conflict detection and automatic scheduling optimization.</p>
                    <ul class="module-features">
                        <li>Automated timetable generation</li>
                        <li>Conflict detection</li>
                        <li>Teacher & room allocation</li>
                        <li>Substitution management</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Timetable Coordinator</span>
                    </div>
                </div>

                <!-- Finance Modules -->
                <div class="module-card" data-category="finance">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-dollar-sign"></i></div>
                        <div class="module-info">
                            <h3>Fee Management</h3>
                            <span class="module-category">Finance</span>
                        </div>
                    </div>
                    <p class="module-description">Complete fee collection, tracking, and reporting with multiple payment methods and automated reminders.</p>
                    <ul class="module-features">
                        <li>Fee structure setup</li>
                        <li>Online payment gateway</li>
                        <li>Payment reminders</li>
                        <li>Receipt generation</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Accountant</span>
                        <span class="role-badge">Parent</span>
                        <span class="role-badge">Student</span>
                    </div>
                </div>

                <div class="module-card" data-category="finance">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-receipt"></i></div>
                        <div class="module-info">
                            <h3>Accounting & Expenses</h3>
                            <span class="module-category">Finance</span>
                        </div>
                    </div>
                    <p class="module-description">Full accounting module with expense tracking, budgeting, and financial reporting.</p>
                    <ul class="module-features">
                        <li>Income & expense tracking</li>
                        <li>Budget management</li>
                        <li>Financial reports</li>
                        <li>Tax calculation</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Accountant</span>
                        <span class="role-badge">Principal</span>
                        <span class="role-badge">Admin</span>
                    </div>
                </div>

                <div class="module-card" data-category="finance">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="module-info">
                            <h3>Payroll Management</h3>
                            <span class="module-category">Finance</span>
                        </div>
                    </div>
                    <p class="module-description">Automated payroll processing with tax calculation, allowances, deductions, and payslip generation.</p>
                    <ul class="module-features">
                        <li>Salary structure setup</li>
                        <li>Automatic salary calculation</li>
                        <li>Tax & deduction management</li>
                        <li>Digital payslip delivery</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">HR Manager</span>
                        <span class="role-badge">Accountant</span>
                        <span class="role-badge">Staff</span>
                    </div>
                </div>

                <!-- HR Modules -->
                <div class="module-card" data-category="hr">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-id-badge"></i></div>
                        <div class="module-info">
                            <h3>HR Management</h3>
                            <span class="module-category">Human Resources</span>
                        </div>
                    </div>
                    <p class="module-description">Complete HR suite for staff management, recruitment, performance evaluation, and leave tracking.</p>
                    <ul class="module-features">
                        <li>Employee database</li>
                        <li>Recruitment & onboarding</li>
                        <li>Performance appraisal</li>
                        <li>Document management</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">HR Manager</span>
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Staff</span>
                    </div>
                </div>

                <div class="module-card" data-category="hr">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-calendar-times"></i></div>
                        <div class="module-info">
                            <h3>Leave Management</h3>
                            <span class="module-category">Human Resources</span>
                        </div>
                    </div>
                    <p class="module-description">Digital leave application, approval workflow, and leave balance tracking for all staff members.</p>
                    <ul class="module-features">
                        <li>Leave application portal</li>
                        <li>Approval workflow</li>
                        <li>Leave balance tracking</li>
                        <li>Leave calendar</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">All Staff</span>
                        <span class="role-badge">HR Manager</span>
                        <span class="role-badge">Department Head</span>
                    </div>
                </div>

                <!-- Communication Modules -->
                <div class="module-card" data-category="communication">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-comments"></i></div>
                        <div class="module-info">
                            <h3>Messaging System</h3>
                            <span class="module-category">Communication</span>
                        </div>
                    </div>
                    <p class="module-description">Internal messaging platform for secure communication between students, teachers, parents, and staff.</p>
                    <ul class="module-features">
                        <li>Direct messaging</li>
                        <li>Group chat</li>
                        <li>File sharing</li>
                        <li>Message notifications</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">All Users</span>
                    </div>
                </div>

                <div class="module-card" data-category="communication">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-bullhorn"></i></div>
                        <div class="module-info">
                            <h3>Announcements & Notices</h3>
                            <span class="module-category">Communication</span>
                        </div>
                    </div>
                    <p class="module-description">School-wide announcement system with targeted delivery and read receipt tracking.</p>
                    <ul class="module-features">
                        <li>Targeted announcements</li>
                        <li>Priority notifications</li>
                        <li>Read receipts</li>
                        <li>Announcement archive</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Principal</span>
                        <span class="role-badge">All Users</span>
                    </div>
                </div>

                <div class="module-card" data-category="communication">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-envelope"></i></div>
                        <div class="module-info">
                            <h3>Email & SMS Integration</h3>
                            <span class="module-category">Communication</span>
                        </div>
                    </div>
                    <p class="module-description">Automated email and SMS notifications for important updates, reminders, and alerts.</p>
                    <ul class="module-features">
                        <li>Email templates</li>
                        <li>SMS notifications</li>
                        <li>WhatsApp integration</li>
                        <li>Bulk messaging</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Admin</span>
                        <span class="role-badge">Accountant</span>
                        <span class="role-badge">Teacher</span>
                    </div>
                </div>

                <!-- Continue with remaining modules... (Libraries, Transport, Hostel, Canteen, etc.) -->
                <div class="module-card" data-category="facilities">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-book-open"></i></div>
                        <div class="module-info">
                            <h3>Library Management</h3>
                            <span class="module-category">Facilities</span>
                        </div>
                    </div>
                    <p class="module-description">Digital library with book cataloging, issue/return tracking, and online catalog search.</p>
                    <ul class="module-features">
                        <li>Book cataloging & barcoding</li>
                        <li>Issue & return tracking</li>
                        <li>Online book search</li>
                        <li>Fine calculation</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Librarian</span>
                        <span class="role-badge">Student</span>
                        <span class="role-badge">Teacher</span>
                    </div>
                </div>

                <div class="module-card" data-category="facilities">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-bus"></i></div>
                        <div class="module-info">
                            <h3>Transport Management</h3>
                            <span class="module-category">Facilities</span>
                        </div>
                    </div>
                    <p class="module-description">Complete transport module with route management, GPS tracking, and driver allocation.</p>
                    <ul class="module-features">
                        <li>Route & vehicle management</li>
                        <li>GPS tracking</li>
                        <li>Driver allocation</li>
                        <li>Transport fee management</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Transport Manager</span>
                        <span class="role-badge">Driver</span>
                        <span class="role-badge">Parent</span>
                    </div>
                </div>

                <div class="module-card" data-category="facilities">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-building"></i></div>
                        <div class="module-info">
                            <h3>Hostel Management</h3>
                            <span class="module-category">Facilities</span>
                        </div>
                    </div>
                    <p class="module-description">Hostel operations management including room allocation, mess management, and visitor tracking.</p>
                    <ul class="module-features">
                        <li>Room allocation</li>
                        <li>Mess management</li>
                        <li>Visitor log</li>
                        <li>Hostel fee tracking</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Hostel Warden</span>
                        <span class="role-badge">Student</span>
                        <span class="role-badge">Parent</span>
                    </div>
                </div>

                <div class="module-card" data-category="facilities">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-utensils"></i></div>
                        <div class="module-info">
                            <h3>Canteen Management</h3>
                            <span class="module-category">Facilities</span>
                        </div>
                    </div>
                    <p class="module-description">Canteen operations with menu management, online ordering, and cashless payment system.</p>
                    <ul class="module-features">
                        <li>Menu management</li>
                        <li>Online ordering</li>
                        <li>Cashless payment</li>
                        <li>Inventory tracking</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Canteen Manager</span>
                        <span class="role-badge">Student</span>
                        <span class="role-badge">Staff</span>
                    </div>
                </div>

                <!-- Plus 22 more modules covering Health, Events, Alumni, Analytics, etc. -->
                <div class="module-card" data-category="operations">
                    <div class="module-header">
                        <div class="module-icon"><i class="fas fa-heartbeat"></i></div>
                        <div class="module-info">
                            <h3>Health Management</h3>
                            <span class="module-category">Operations</span>
                        </div>
                    </div>
                    <p class="module-description">Student health records, medical history, vaccination tracking, and nurse visit logs.</p>
                    <ul class="module-features">
                        <li>Medical records</li>
                        <li>Vaccination tracking</li>
                        <li>Nurse visit logs</li>
                        <li>Emergency contacts</li>
                    </ul>
                    <div class="module-roles">
                        <span class="role-badge">Nurse</span>
                        <span class="role-badge">Parent</span>
                        <span class="role-badge">Student</span>
                    </div>
                </div>

                <!-- Add 20 more module cards here with similar structure -->

            </div>

            <div style="text-align: center; margin-top: 4rem;">
                <a href="demo-request.php" class="btn btn-primary btn-large">
                    <i class="fas fa-play-circle"></i> Request Live Demo
                </a>
                <a href="pricing.php" class="btn btn-secondary btn-large">
                    <i class="fas fa-tag"></i> View Pricing
                </a>
            </div>
        </div>
    </section>

    <?php include 'includes/public-footer.php'; ?>

    <script src="assets/js/homepage.js"></script>
    <script>
        // Module filtering
        const filterBtns = document.querySelectorAll('.filter-btn');
        const moduleCards = document.querySelectorAll('.module-card');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const category = btn.dataset.category;

                // Update active button
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Filter modules
                moduleCards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>