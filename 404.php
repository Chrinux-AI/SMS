<?php
/**
 * Custom 404 Error Page
 * Beautiful error page with search, common links, and AI suggestions
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

$page_title = 'Page Not Found - 404';
$requested_url = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo APP_NAME; ?></title>
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
    <?php include 'includes/head-meta.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Orbitron:wght@500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="assets/css/cyberpunk-ui.css" rel="stylesheet">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-primary, #0a0e27);
            padding: 20px;
        }
        .error-content {
            max-width: 800px;
            width: 100%;
            text-align: center;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            font-family: 'Orbitron', sans-serif;
            background: linear-gradient(135deg, var(--cyber-cyan, #00BFFF), var(--cyber-purple, #8A2BE2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
        }
        .error-title {
            font-size: 2rem;
            color: var(--text-primary, #ffffff);
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
        }
        .error-message {
            font-size: 1.1rem;
            color: var(--text-muted, #9ca3af);
            margin-bottom: 40px;
        }
        .search-box {
            max-width: 500px;
            margin: 0 auto 40px;
            position: relative;
        }
        .search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            background: rgba(0, 191, 255, 0.1);
            border: 2px solid var(--cyber-cyan, #00BFFF);
            border-radius: 25px;
            color: white;
            font-size: 1rem;
        }
        .search-input:focus {
            outline: none;
            box-shadow: 0 0 20px rgba(0, 191, 255, 0.3);
        }
        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--cyber-cyan, #00BFFF), var(--cyber-purple, #8A2BE2));
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .common-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 40px;
        }
        .link-card {
            padding: 20px;
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid var(--cyber-cyan, #00BFFF);
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-primary, #ffffff);
            transition: all 0.3s;
        }
        .link-card:hover {
            background: rgba(0, 191, 255, 0.2);
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 191, 255, 0.3);
        }
        .link-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--cyber-cyan, #00BFFF);
        }
        .link-card h3 {
            margin: 0 0 5px;
            font-size: 1.1rem;
        }
        .link-card p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted, #9ca3af);
        }
        .ai-suggestion {
            background: rgba(139, 92, 246, 0.1);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .ai-suggestion h3 {
            color: var(--cyber-purple, #8A2BE2);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .ai-suggestion ul {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
        }
        .ai-suggestion li {
            padding: 8px 0;
            color: var(--text-body, #ffffff);
        }
        .ai-suggestion li:before {
            content: "💡 ";
            margin-right: 8px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--cyber-cyan, #00BFFF), var(--cyber-purple, #8A2BE2));
            border: none;
            border-radius: 25px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 191, 255, 0.4);
        }
        .btn-secondary {
            background: rgba(0, 191, 255, 0.1);
            border: 1px solid var(--cyber-cyan, #00BFFF);
        }
        .btn-secondary:hover {
            background: rgba(0, 191, 255, 0.2);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-message">
                The page you're looking for doesn't exist or has been moved.
            </p>

            <!-- Search Box -->
            <div class="search-box">
                <input type="text" class="search-input" id="searchInput" placeholder="Search for pages, features, or help..." onkeypress="handleSearchEnter(event)">
                <button class="search-btn" onclick="performSearch()">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <!-- AI Suggestions -->
            <div class="ai-suggestion">
                <h3><i class="fas fa-robot"></i> Did you mean...</h3>
                <ul id="aiSuggestions">
                    <?php
                    $suggestions = getAISuggestions($requested_url);
                    foreach ($suggestions as $suggestion): ?>
                        <li><a href="<?php echo htmlspecialchars($suggestion['url']); ?>" style="color: var(--cyber-cyan); text-decoration: none;"><?php echo htmlspecialchars($suggestion['text']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Common Links -->
            <div class="common-links">
                <a href="<?php echo APP_URL; ?>/index.php" class="link-card">
                    <i class="fas fa-home"></i>
                    <h3>Home</h3>
                    <p>Go to homepage</p>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo APP_URL; ?>/<?php echo $_SESSION['role']; ?>/dashboard.php" class="link-card">
                        <i class="fas fa-tachometer-alt"></i>
                        <h3>Dashboard</h3>
                        <p>Your main dashboard</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/messages.php" class="link-card">
                        <i class="fas fa-envelope"></i>
                        <h3>Messages</h3>
                        <p>View your messages</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/<?php echo $_SESSION['role']; ?>/settings.php" class="link-card">
                        <i class="fas fa-cog"></i>
                        <h3>Settings</h3>
                        <p>Account settings</p>
                    </a>
                <?php else: ?>
                    <a href="<?php echo APP_URL; ?>/login.php" class="link-card">
                        <i class="fas fa-sign-in-alt"></i>
                        <h3>Login</h3>
                        <p>Sign in to your account</p>
                    </a>
                    <a href="<?php echo APP_URL; ?>/register.php" class="link-card">
                        <i class="fas fa-user-plus"></i>
                        <h3>Register</h3>
                        <p>Create new account</p>
                    </a>
                <?php endif; ?>
                <a href="<?php echo APP_URL; ?>/visitor/faq.php" class="link-card">
                    <i class="fas fa-question-circle"></i>
                    <h3>FAQ</h3>
                    <p>Frequently asked questions</p>
                </a>
                <a href="<?php echo APP_URL; ?>/visitor/contact.php" class="link-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Contact</h3>
                    <p>Get help & support</p>
                </a>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?php echo APP_URL; ?>/index.php" class="btn">
                    <i class="fas fa-home"></i> Go Home
                </a>
                <button onclick="window.history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Go Back
                </button>
                <a href="javascript:void(0)" onclick="reportIssue()" class="btn btn-secondary">
                    <i class="fas fa-bug"></i> Report Issue
                </a>
            </div>
        </div>
    </div>

    <script>
        function handleSearchEnter(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        }

        function performSearch() {
            const query = document.getElementById('searchInput').value.trim();
            if (query) {
                // Redirect to search or use global search if available
                if (typeof openGlobalSearch === 'function') {
                    openGlobalSearch(query);
                } else {
                    // Fallback: try to find page
                    window.location.href = '<?php echo APP_URL; ?>/index.php?search=' + encodeURIComponent(query);
                }
            }
        }

        function reportIssue() {
            const url = window.location.href;
            const subject = encodeURIComponent('404 Error Report');
            const body = encodeURIComponent(`Page not found: ${url}\n\nRequested URL: <?php echo htmlspecialchars($requested_url); ?>\n\nPlease fix this broken link.`);
            window.location.href = '<?php echo APP_URL; ?>/visitor/contact.php?subject=' + subject + '&body=' + body;
        }

        // Auto-focus search box
        document.getElementById('searchInput').focus();
    </script>
</body>
</html>

<?php
/**
 * Get AI suggestions based on requested URL
 */
function getAISuggestions($url) {
    $suggestions = [];
    $url_lower = strtolower($url);
    
    // Extract keywords from URL
    $keywords = explode('/', trim($url, '/'));
    $last_keyword = end($keywords);
    
    // Common page mappings
    $page_mappings = [
        'dashboard' => ['url' => '/dashboard.php', 'text' => 'Dashboard'],
        'settings' => ['url' => '/settings.php', 'text' => 'Settings'],
        'profile' => ['url' => '/profile.php', 'text' => 'Profile'],
        'messages' => ['url' => '/messages.php', 'text' => 'Messages'],
        'attendance' => ['url' => '/attendance.php', 'text' => 'Attendance'],
        'grades' => ['url' => '/grades.php', 'text' => 'Grades'],
        'students' => ['url' => '/admin/students.php', 'text' => 'Students'],
        'teachers' => ['url' => '/admin/teachers.php', 'text' => 'Teachers'],
        'classes' => ['url' => '/admin/classes.php', 'text' => 'Classes'],
        'reports' => ['url' => '/reports.php', 'text' => 'Reports'],
    ];
    
    // Check if keyword matches any page
    foreach ($page_mappings as $keyword => $page) {
        if (strpos($url_lower, $keyword) !== false || strpos($last_keyword, $keyword) !== false) {
            $role = $_SESSION['role'] ?? 'student';
            $suggestions[] = [
                'url' => APP_URL . '/' . $role . $page['url'],
                'text' => $page['text']
            ];
        }
    }
    
    // Default suggestions if none found
    if (empty($suggestions)) {
        $role = $_SESSION['role'] ?? 'student';
        $suggestions = [
            ['url' => APP_URL . '/' . $role . '/dashboard.php', 'text' => 'Dashboard'],
            ['url' => APP_URL . '/messages.php', 'text' => 'Messages'],
            ['url' => APP_URL . '/' . $role . '/settings.php', 'text' => 'Settings'],
        ];
    }
    
    return array_slice($suggestions, 0, 3);
}
?>
