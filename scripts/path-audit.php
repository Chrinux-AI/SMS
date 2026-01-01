<?php
/**
 * Path Audit Script for Verdant SMS
 * Scans for common path issues including:
 * - Absolute paths missing /attendance/ prefix
 * - Broken relative paths
 * - Inconsistent path patterns
 */

define('BASE_PATH', dirname(__DIR__));

class Colors {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const CYAN = "\033[36m";
    const RESET = "\033[0m";
    const BOLD = "\033[1m";
}

echo Colors::CYAN . Colors::BOLD . "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║           VERDANT SMS - PATH AUDIT TOOL v1.0                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝" . Colors::RESET . "\n\n";

$issues = [];

// Define problematic patterns
$patterns = [
    // Absolute paths that should be relative or use APP_URL
    '/href="\/[a-z]+\/[^"]+"/' => 'Absolute path without /attendance/',
    '/src="\/[a-z]+\/[^"]+"/' => 'Absolute path in src without /attendance/',
    '/action="\/[a-z]+\/[^"]+"/' => 'Absolute path in form action',
];

// Scan all PHP files
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(BASE_PATH, RecursiveDirectoryIterator::SKIP_DOTS)
);

$scannedFiles = 0;
$issuesFound = 0;

foreach ($files as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        // Skip certain directories
        $path = $file->getPathname();
        if (strpos($path, '/_deprecated') !== false ||
            strpos($path, '/vendor') !== false ||
            strpos($path, '/scripts') !== false) {
            continue;
        }

        $content = file_get_contents($path);
        $relativePath = str_replace(BASE_PATH . '/', '', $path);
        $scannedFiles++;

        // Check for common issues
        foreach ($patterns as $pattern => $description) {
            if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    // Skip if it's already using APP_URL or has /attendance/
                    if (strpos($match[0], '/attendance/') !== false) {
                        continue;
                    }
                    // Skip JavaScript URLs
                    if (strpos($match[0], 'javascript:') !== false) {
                        continue;
                    }
                    // Skip external URLs
                    if (strpos($match[0], 'http') !== false) {
                        continue;
                    }

                    $lineNumber = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                    $issues[] = [
                        'file' => $relativePath,
                        'line' => $lineNumber,
                        'issue' => $description,
                        'code' => trim($match[0])
                    ];
                    $issuesFound++;
                }
            }
        }
    }
}

// Display results
echo Colors::YELLOW . "📋 Scanned $scannedFiles PHP files\n" . Colors::RESET;
echo "\n";

if (empty($issues)) {
    echo Colors::GREEN . "🎉 No path issues found! All paths are properly formatted.\n" . Colors::RESET;
} else {
    echo Colors::RED . "Found $issuesFound potential path issues:\n\n" . Colors::RESET;

    $byFile = [];
    foreach ($issues as $issue) {
        $byFile[$issue['file']][] = $issue;
    }

    foreach ($byFile as $file => $fileIssues) {
        echo Colors::CYAN . "📄 $file" . Colors::RESET . "\n";
        foreach ($fileIssues as $issue) {
            echo "   Line {$issue['line']}: {$issue['code']}\n";
        }
        echo "\n";
    }
}

echo "\n" . Colors::CYAN . "═══════════════════════════════════════════════════════════════" . Colors::RESET . "\n";
echo Colors::BOLD . "RECOMMENDATIONS:\n" . Colors::RESET;
echo "1. Use relative paths (e.g., 'contact.php' or '../dashboard.php')\n";
echo "2. Use APP_URL constant for absolute paths:\n";
echo "   Example: <?php echo APP_URL; ?>/student/dashboard.php\n";
echo "3. Run this script again after fixes\n";
echo "\n";
