<?php
/**
 * Link Audit Script for Verdant SMS
 * Scans all PHP files and identifies broken navigation links
 *
 * Usage: php scripts/link-audit.php
 */

define('BASE_PATH', dirname(__DIR__));

// Colors for terminal output
class Colors {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const CYAN = "\033[36m";
    const RESET = "\033[0m";
    const BOLD = "\033[1m";
}

echo "\n" . Colors::CYAN . Colors::BOLD . "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           VERDANT SMS - LINK AUDIT TOOL v1.0                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝" . Colors::RESET . "\n\n";

$report = [
    'scan_date' => date('Y-m-d H:i:s'),
    'total_files_scanned' => 0,
    'total_links_found' => 0,
    'broken_links' => [],
    'missing_pages' => [],
    'navigation_issues' => [],
    'summary' => []
];

// Directories containing role-specific pages
$role_dirs = [
    'admin', 'student', 'teacher', 'parent', 'principal', 'vice-principal',
    'accountant', 'librarian', 'transport', 'hostel', 'canteen', 'nurse',
    'counselor', 'admin-officer', 'class-teacher', 'subject-coordinator',
    'alumni', 'general', 'visitor', 'superadmin', 'owner'
];

// Function to extract navigation links from cyber-nav.php
function extractNavigationLinks($content) {
    $links = [];

    // Match array key patterns like 'dashboard.php' =>
    preg_match_all("/['\"]([^'\"]+\.php)['\"]\\s*=>/", $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $link) {
            $links[] = $link;
        }
    }

    // Match href patterns
    preg_match_all('/href=["\']([^"\']+\.php)["\']/', $content, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $link) {
            // Skip dynamic PHP URLs
            if (strpos($link, '<?php') === false) {
                $links[] = $link;
            }
        }
    }

    return array_unique($links);
}

// Function to check if a page exists
function pageExists($link, $currentDir, $role = null) {
    global $role_dirs;

    // Clean the link
    $link = trim($link);

    // Handle relative paths
    if (strpos($link, '../') === 0) {
        $resolved = realpath($currentDir . '/' . $link);
        if ($resolved && file_exists($resolved)) {
            return true;
        }
        // Also check without realpath for links that may not exist yet
        $testPath = $currentDir . '/' . $link;
        $testPath = str_replace('../', '/../', $testPath);
        $normalized = realpath(dirname($testPath));
        if ($normalized) {
            return file_exists($normalized . '/' . basename($link));
        }
        return false;
    }

    // Handle absolute paths starting with /
    if (strpos($link, '/') === 0) {
        $cleanPath = preg_replace('/^\/attendance\//', '', $link);
        return file_exists(BASE_PATH . '/' . $cleanPath);
    }

    // Handle subdirectory paths like 'academics/exams.php'
    if (strpos($link, '/') !== false) {
        // Check in current role directory
        if ($role) {
            $testPath = BASE_PATH . '/' . $role . '/' . $link;
            if (file_exists($testPath)) {
                return true;
            }
        }
        // Check from base path
        return file_exists(BASE_PATH . '/' . $link);
    }

    // Simple filename - check in role directories
    if ($role) {
        $testPath = BASE_PATH . '/' . $role . '/' . $link;
        if (file_exists($testPath)) {
            return true;
        }
    }

    // Check in base path
    return file_exists(BASE_PATH . '/' . $link);
}

// Scan navigation files
echo Colors::YELLOW . "📋 Scanning navigation files..." . Colors::RESET . "\n";

$navFiles = [
    'includes/cyber-nav.php',
    'includes/nature-nav.php',
    'includes/student-nav.php',
    'includes/general-nav.php',
    'includes/visitor-nav.php',
    'includes/admin-nav.php',
    'includes/mobile-bottom-nav.php'
];

foreach ($navFiles as $navFile) {
    $fullPath = BASE_PATH . '/' . $navFile;
    if (file_exists($fullPath)) {
        $report['total_files_scanned']++;
        $content = file_get_contents($fullPath);
        $links = extractNavigationLinks($content);
        $report['total_links_found'] += count($links);

        echo "  📄 " . basename($navFile) . ": " . count($links) . " links found\n";

        foreach ($links as $link) {
            // Skip external links
            if (strpos($link, 'http') === 0 || strpos($link, '<?php') !== false) {
                continue;
            }

            // Check for each role directory
            $linkWorks = false;
            foreach ($role_dirs as $role) {
                if (pageExists($link, BASE_PATH . '/' . $role, $role)) {
                    $linkWorks = true;
                    break;
                }
            }

            // Also check from base
            if (!$linkWorks && pageExists($link, BASE_PATH)) {
                $linkWorks = true;
            }

            if (!$linkWorks) {
                $report['broken_links'][] = [
                    'source' => $navFile,
                    'link' => $link,
                    'type' => 'navigation'
                ];
            }
        }
    }
}

// Scan role directories for missing pages
echo Colors::YELLOW . "\n📁 Scanning role directories..." . Colors::RESET . "\n";

foreach ($role_dirs as $role) {
    $roleDir = BASE_PATH . '/' . $role;
    if (is_dir($roleDir)) {
        $files = glob($roleDir . '/*.php');
        $fileCount = count($files);
        $report['summary'][$role] = ['files' => $fileCount, 'subdirs' => []];

        // Check subdirectories
        $subdirs = glob($roleDir . '/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
            $subFiles = glob($subdir . '/*.php');
            $report['summary'][$role]['subdirs'][basename($subdir)] = count($subFiles);
            $fileCount += count($subFiles);
        }

        echo "  📂 " . $role . ": " . $fileCount . " PHP files\n";
        $report['total_files_scanned'] += $fileCount;
    } else {
        $report['missing_pages'][] = [
            'expected' => $role . '/',
            'type' => 'role_directory',
            'priority' => 'low'
        ];
    }
}

// Check for required pages in each role
echo Colors::YELLOW . "\n🔍 Checking required pages..." . Colors::RESET . "\n";

$requiredPages = ['dashboard.php', 'settings.php'];

foreach ($role_dirs as $role) {
    $roleDir = BASE_PATH . '/' . $role;
    if (is_dir($roleDir)) {
        foreach ($requiredPages as $page) {
            if (!file_exists($roleDir . '/' . $page)) {
                $report['missing_pages'][] = [
                    'expected' => $role . '/' . $page,
                    'type' => 'required_page',
                    'priority' => 'high'
                ];
            }
        }
    }
}

// Display results
echo "\n" . Colors::CYAN . Colors::BOLD . "═══════════════════════════════════════════════════════════════" . Colors::RESET . "\n";
echo Colors::BOLD . "                         AUDIT RESULTS\n" . Colors::RESET;
echo Colors::CYAN . "═══════════════════════════════════════════════════════════════" . Colors::RESET . "\n";

echo "\n📊 " . Colors::BOLD . "Summary:" . Colors::RESET . "\n";
echo "   Total Files Scanned: " . Colors::GREEN . $report['total_files_scanned'] . Colors::RESET . "\n";
echo "   Total Links Found: " . Colors::GREEN . $report['total_links_found'] . Colors::RESET . "\n";
echo "   Broken Links: " . (count($report['broken_links']) > 0 ? Colors::RED : Colors::GREEN) . count($report['broken_links']) . Colors::RESET . "\n";
echo "   Missing Required Pages: " . (count($report['missing_pages']) > 0 ? Colors::YELLOW : Colors::GREEN) . count($report['missing_pages']) . Colors::RESET . "\n";

if (!empty($report['broken_links'])) {
    echo "\n" . Colors::RED . "❌ Broken Navigation Links:" . Colors::RESET . "\n";
    foreach ($report['broken_links'] as $broken) {
        echo "   ├─ " . Colors::YELLOW . $broken['link'] . Colors::RESET . "\n";
        echo "   │  └─ Source: " . $broken['source'] . "\n";
    }
}

if (!empty($report['missing_pages'])) {
    echo "\n" . Colors::YELLOW . "⚠️ Missing Pages:" . Colors::RESET . "\n";
    $highPriority = array_filter($report['missing_pages'], fn($p) => $p['priority'] === 'high');
    $lowPriority = array_filter($report['missing_pages'], fn($p) => $p['priority'] !== 'high');

    if (!empty($highPriority)) {
        echo "   " . Colors::RED . "High Priority:" . Colors::RESET . "\n";
        foreach ($highPriority as $missing) {
            echo "   ├─ " . $missing['expected'] . "\n";
        }
    }

    if (!empty($lowPriority) && count($lowPriority) <= 10) {
        echo "   " . Colors::YELLOW . "Low Priority (missing role directories):" . Colors::RESET . "\n";
        foreach ($lowPriority as $missing) {
            echo "   ├─ " . $missing['expected'] . "\n";
        }
    } elseif (!empty($lowPriority)) {
        echo "   " . Colors::YELLOW . "Low Priority: " . count($lowPriority) . " items" . Colors::RESET . "\n";
    }
}

// Save JSON report
$reportPath = BASE_PATH . '/scripts/link-audit-report.json';
file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT));
echo "\n" . Colors::GREEN . "✅ Report saved to: scripts/link-audit-report.json" . Colors::RESET . "\n\n";

// Recommendations
echo Colors::CYAN . Colors::BOLD . "═══════════════════════════════════════════════════════════════" . Colors::RESET . "\n";
echo Colors::BOLD . "                       RECOMMENDATIONS\n" . Colors::RESET;
echo Colors::CYAN . "═══════════════════════════════════════════════════════════════" . Colors::RESET . "\n\n";

if (count($report['broken_links']) === 0 && count($report['missing_pages']) === 0) {
    echo Colors::GREEN . "🎉 All navigation links are working! No issues found." . Colors::RESET . "\n";
} else {
    echo "1. Fix broken navigation links by creating the missing pages\n";
    echo "2. Ensure all role directories have dashboard.php and settings.php\n";
    echo "3. Run this audit again after fixes\n";
}

echo "\n";
