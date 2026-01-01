<?php
/**
 * Superadmin - Add New School
 * Register a new school on the platform
 */

require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/school-context.php';

// Ensure superadmin access
if (!defined('TEST_MODE') || !TEST_MODE) {
    SchoolContext::requireSuperadmin();
}

$db = Database::getInstance();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schoolName = trim($_POST['school_name'] ?? '');
    $schoolCode = strtoupper(trim($_POST['school_code'] ?? ''));
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $plan = $_POST['subscription_plan'] ?? 'standard';
    $maxStudents = (int)($_POST['max_students'] ?? 500);
    $maxStaff = (int)($_POST['max_staff'] ?? 50);

    // Validation
    if (empty($schoolName) || empty($schoolCode)) {
        $message = 'School name and code are required.';
        $messageType = 'error';
    } else {
        // Check if school code already exists
        $existing = $db->fetchOne("SELECT id FROM schools WHERE school_code = ?", [$schoolCode]);
        if ($existing) {
            $message = 'School code already exists. Please use a unique code.';
            $messageType = 'error';
        } else {
            // Insert new school
            $result = $db->insert('schools', [
                'school_code' => $schoolCode,
                'school_name' => $schoolName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'subscription_plan' => $plan,
                'subscription_status' => 'trial',
                'max_students' => $maxStudents,
                'max_staff' => $maxStaff,
                'registered_by' => $_SESSION['user_id'] ?? 1,
                'is_active' => 1
            ]);

            if ($result) {
                $message = "School '{$schoolName}' registered successfully! Now create an admin account.";
                $messageType = 'success';
                // Redirect to create admin with school ID
                header("Location: create-admin.php?school_id={$result}&new=1");
                exit;
            } else {
                $message = 'Failed to register school. Please try again.';
                $messageType = 'error';
            }
        }
    }
}

// Nigerian states
$nigerianStates = [
    'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue', 'Borno',
    'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT', 'Gombe',
    'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara',
    'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau',
    'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
];

$pageTitle = "Register New School";
?>
<!DOCTYPE html>
<html lang="en" data-theme="cyberpunk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/cyberpunk-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --cyber-cyan: #00FFFF;
            --cyber-green: #00FF7F;
            --cyber-purple: #8B00FF;
            --dark-bg: #0A0A0F;
            --card-bg: #12121A;
        }

        body {
            background: var(--dark-bg);
            color: #fff;
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .back-link {
            color: var(--cyber-cyan);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        h1 {
            background: linear-gradient(90deg, var(--cyber-cyan), var(--cyber-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        .form-card {
            background: var(--card-bg);
            border: 1px solid rgba(0, 255, 255, 0.2);
            border-radius: 12px;
            padding: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            color: var(--cyber-cyan);
            font-size: 0.9rem;
            font-weight: 500;
        }

        input, select, textarea {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--cyber-cyan);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }

        .btn-submit {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, var(--cyber-cyan), var(--cyber-purple));
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.4);
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .message.error {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid #ff4444;
            color: #ff4444;
        }

        .message.success {
            background: rgba(0, 255, 127, 0.2);
            border: 1px solid var(--cyber-green);
            color: var(--cyber-green);
        }

        .hint {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <h1><i class="fas fa-school"></i> Register New School</h1>

        <?php if ($message): ?>
            <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" class="form-grid">
                <div class="form-group full-width">
                    <label for="school_name">School Name *</label>
                    <input type="text" id="school_name" name="school_name" required
                           placeholder="e.g. Greenfield International Academy">
                </div>

                <div class="form-group">
                    <label for="school_code">School Code *</label>
                    <input type="text" id="school_code" name="school_code" required
                           placeholder="e.g. GIA-001" maxlength="20" style="text-transform: uppercase;">
                    <span class="hint">Unique identifier (auto-uppercase)</span>
                </div>

                <div class="form-group">
                    <label for="subscription_plan">Subscription Plan</label>
                    <select id="subscription_plan" name="subscription_plan">
                        <option value="basic">Basic (₦50,000/term)</option>
                        <option value="standard" selected>Standard (₦100,000/term)</option>
                        <option value="premium">Premium (₦200,000/term)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">School Email</label>
                    <input type="email" id="email" name="email" placeholder="school@example.com">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="+234...">
                </div>

                <div class="form-group full-width">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="2" placeholder="Full school address"></textarea>
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="e.g. Lagos">
                </div>

                <div class="form-group">
                    <label for="state">State</label>
                    <select id="state" name="state">
                        <option value="">-- Select State --</option>
                        <?php foreach ($nigerianStates as $state): ?>
                            <option value="<?= $state ?>"><?= $state ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="max_students">Max Students</label>
                    <input type="number" id="max_students" name="max_students" value="500" min="50">
                </div>

                <div class="form-group">
                    <label for="max_staff">Max Staff</label>
                    <input type="number" id="max_staff" name="max_staff" value="50" min="5">
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-plus-circle"></i> Register School
                </button>
            </form>
        </div>
    </div>

    <script>
        // Auto-uppercase school code
        document.getElementById('school_code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
