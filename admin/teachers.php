<?php

/**
 * Teachers Management - Nature UI
 */

session_start();

require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

// Require admin access
require_role('admin');

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_teacher'])) {
        $user_data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password_hash' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'role' => 'teacher',
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'status' => 'active'
        ];

        $user_id = db()->insert('users', $user_data);
        if ($user_id) {
            log_activity($_SESSION['user_id'], 'create', 'users', $user_id);
            $message = 'Teacher added successfully!';
            $message_type = 'success';
        } else {
            $message = 'Error adding teacher. Username or email may already exist.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['delete_teacher'])) {
        $id = (int)$_POST['teacher_id'];
        db()->delete('users', 'id = ?', [$id]);
        log_activity($_SESSION['user_id'], 'delete', 'users', $id);
        $message = 'Teacher deleted successfully!';
        $message_type = 'success';
    } elseif (isset($_POST['assign_class'])) {
        $teacher_id = (int)$_POST['teacher_id'];
        $class_id = (int)$_POST['class_id'];
        db()->update('classes', ['teacher_id' => $teacher_id], 'id = ?', [$class_id]);
        $message = 'Class assigned successfully!';
        $message_type = 'success';
    }
}

// Get all teachers with stats
$teachers = db()->fetchAll("
    SELECT u.*,
           (SELECT COUNT(*) FROM classes WHERE teacher_id = u.id) as class_count
    FROM users u
    WHERE u.role = 'teacher'
    ORDER BY u.last_name, u.first_name
");

// Get all classes for assignment
$all_classes = db()->fetchAll("SELECT * FROM classes ORDER BY name");

$page_title = 'Teachers Management';
$page_icon = 'chalkboard-teacher';
$full_name = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/attendance/manifest.json">
    <meta name="theme-color" content="#00BFFF">
    <link rel="apple-touch-icon" href="/attendance/assets/images/icons/icon-192x192.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&family=Inter:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../assets/css/cyberpunk-ui.css" rel="stylesheet">

</head>

<body class="cyber-bg">
    <div class="starfield"></div>
    <div class="cyber-grid"></div>
    <div class="cyber-layout">
        <?php include '../includes/cyber-nav.php'; ?>
        <main class="cyber-main">
            <header class="cyber-header">
                <div class="page-title-section">
                    <div class="page-icon-orb"><i class="fas fa-<?php echo $page_icon; ?>"></i></div>
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                </div>
                <div class="header-actions">
                    <div class="biometric-orb" title="Quick Scan"><i class="fas fa-fingerprint"></i></div>
                    <div class="user-card" style="padding: 8px 15px; margin: 0;">
                        <div class="user-avatar" style="width: 35px; height: 35px; font-size: 0.9rem;">
                            <?php echo strtoupper(substr($full_name, 0, 2)); ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name" style="font-size: 0.85rem;"><?php echo htmlspecialchars($full_name); ?></div>
                            <div class="user-role">Administrator</div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="cyber-content slide-in">
                <?php if ($message): ?>
                    <div class="cyber-alert <?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                        <span><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>

                <!-- Statistics -->
                <section class="orb-grid">
                    <div class="stat-orb">
                        <div class="stat-icon cyan">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo count($teachers); ?></div>
                            <div class="stat-label">Total Teachers</div>
                            <div class="stat-trend up">
                                <i class="fas fa-users"></i>
                                <span>Active Staff</span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-orb">
                        <div class="stat-icon green">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo count($all_classes); ?></div>
                            <div class="stat-label">Total Classes</div>
                            <div class="stat-trend up">
                                <i class="fas fa-check-circle"></i>
                                <span>All Levels</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Add Teacher Form -->
                <div class="holo-card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-user-plus"></i> <span>Add New Teacher</span></div>
                    </div>
                    <div class="card-body">
                        <form method="POST" style="display:grid;grid-template-columns:repeat(2,1fr);gap:20px;">
                            <div>
                                <label style="color:var(--cyber-cyan);font-size:0.85rem;font-weight:600;margin-bottom:8px;display:block;"><i class="fas fa-user"></i> FIRST NAME</label>
                                <input type="text" name="first_name" required style="width:100%;padding:12px;background:rgba(0,191,255,0.05);border:1px solid var(--cyber-cyan);border-radius:8px;color:var(--cyber-cyan);font-family:Roboto;">
                            </div>
                            <div>
                                <label style="color:var(--cyber-cyan);font-size:0.85rem;font-weight:600;margin-bottom:8px;display:block;"><i class="fas fa-user"></i> LAST NAME</label>
                                <input type="text" name="last_name" required style="width:100%;padding:12px;background:rgba(0,191,255,0.05);border:1px solid var(--cyber-cyan);border-radius:8px;color:var(--cyber-cyan);font-family:Roboto;">
                            </div>
                            <div>
                                <label style="color:var(--cyber-cyan);font-size:0.85rem;font-weight:600;margin-bottom:8px;display:block;"><i class="fas fa-envelope"></i> EMAIL</label>
                                <input type="email" name="email" required style="width:100%;padding:12px;background:rgba(0,191,255,0.05);border:1px solid var(--cyber-cyan);border-radius:8px;color:var(--cyber-cyan);font-family:Roboto;">
                            </div>
                            <div>
                                <label style="color:var(--cyber-cyan);font-size:0.85rem;font-weight:600;margin-bottom:8px;display:block;"><i class="fas fa-at"></i> USERNAME</label>
                                <input type="text" name="username" required style="width:100%;padding:12px;background:rgba(0,191,255,0.05);border:1px solid var(--cyber-cyan);border-radius:8px;color:var(--cyber-cyan);font-family:Roboto;">
                            </div>
                            <div style="grid-column:span 2;">
                                <label style="color:var(--cyber-cyan);font-size:0.85rem;font-weight:600;margin-bottom:8px;display:block;"><i class="fas fa-lock"></i> PASSWORD</label>
                                <input type="password" name="password" required style="width:100%;padding:12px;background:rgba(0,191,255,0.05);border:1px solid var(--cyber-cyan);border-radius:8px;color:var(--cyber-cyan);font-family:Roboto;">
                            </div>
                            <div style="grid-column:span 2;display:flex;justify-content:flex-end;">
                                <button type="submit" name="add_teacher" class="cyber-btn primary"><i class="fas fa-save"></i> Add Teacher</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Teachers List -->
                <div class="holo-card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-users"></i> <span>All Teachers</span></div>
                        <div class="card-badge cyan"><?php echo count($teachers); ?> Teachers</div>
                    </div>
                    <div class="card-body">
                        <div class="holo-table-wrapper">
                            <table class="holo-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Classes</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($teachers as $teacher): ?>
                                        <tr>
                                            <td>
                                                <div style="display:flex;align-items:center;gap:12px;">
                                                    <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--cyber-cyan),var(--hologram-purple));display:flex;align-items:center;justify-content:center;font-weight:700;color:white;">
                                                        <?php echo strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div style="font-weight:600;color:var(--cyber-cyan);">
                                                            <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                            <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                                            <td><span class="cyber-badge cyan"><?php echo $teacher['class_count']; ?> Classes</span></td>
                                            <td><span class="cyber-badge success">Active</span></td>
                                            <td>
                                                <div style="display:flex;gap:8px;">
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this teacher?');">
                                                        <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">
                                                        <button type="submit" name="delete_teacher" class="cyber-btn danger" style="padding:8px 12px;font-size:0.85rem;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/pwa-manager.js"></script>
    <script src="../assets/js/pwa-analytics.js"></script>
</body>

</html>
