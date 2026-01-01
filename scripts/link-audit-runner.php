<?php
/**
 * Link Audit Script - Scans all PHP files for broken links
 * Run: php scripts/link-audit-runner.php
 */

define('BASE_PATH', dirname(__DIR__));

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       VERDANT SMS - LINK AUDIT TOOL v1.0                     ║\n";
echo "║       Scanning for broken links and missing pages...         ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$brokenLinks = [];
$checkedLinks = [];
$totalFiles = 0;
$totalLinks = 0;

// Directories to scan
$scanDirs = [
    BASE_PATH,
    BASE_PATH . '/admin',
    BASE_PATH . '/student',
    BASE_PATH . '/teacher',
    BASE_PATH . '/parent',
    BASE_PATH . '/visitor',
    BASE_PATH . '/includes',
];

// Extensions to check
$phpFiles = [];

foreach ($scanDirs as $dir) {
    if (!is_dir($dir)) continue;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            // Skip vendor, _backups, and hidden directories
            $path = $file->getPathname();
            if (strpos($path, '/vendor/') !== false ||
                strpos($path, '/_backups/') !== false ||
                strpos($path, '/_deprecated/') !== false ||
                strpos($path, '/.') !== false) {
                continue;
            }
            $phpFiles[] = $path;
        }
    }
}

echo "📁 Found " . count($phpFiles) . " PHP files to scan\n\n";

foreach ($phpFiles as $file) {
    $totalFiles++;
    $content = file_get_contents($file);
    $relativePath = str_replace(BASE_PATH, '', $file);

    // Find all href attributes
    preg_match_all('/href=["\']([^"\'#]+)["\']/', $content, $hrefMatches);

    // Find all src attributes (for includes)
    preg_match_all('/include[_once]*\s*[("\']+([^"\']+)["\')]+/', $content, $includeMatches);

    $links = array_merge($hrefMatches[1] ?? [], $includeMatches[1] ?? []);

    foreach ($links as $link) {
        // Skip external links, javascript, mailto, tel, anchors
        if (preg_match('/^(https?:|javascript:|mailto:|tel:|#|\$|<\?|{)/', $link)) {
            continue;
        }

        // Skip already checked
        if (isset($checkedLinks[$link])) {
            continue;
        }

        $totalLinks++;
        $checkedLinks[$link] = true;

        // Resolve relative path
        $fileDir = dirname($file);

        if (strpos($link, '/') === 0) {
            // Absolute path from web root
            $resolvedPath = BASE_PATH . $link;
        } else {
            // Relative path
            $resolvedPath = realpath($fileDir . '/' . $link);
            if (!$resolvedPath) {
                $resolvedPath = $fileDir . '/' . $link;
            }
        }

        // Normalize path
        $resolvedPath = preg_replace('/\.php\?.*$/', '.php', $resolvedPath);

        // Check if file exists
        if (!file_exists($resolvedPath)) {
            $brokenLinks[] = [
                'file' => $relativePath,
                'link' => $link,
                'resolved' => str_replace(BASE_PATH, '', $resolvedPath)
            ];
        }
    }
}

echo "✅ Scanned $totalFiles files\n";
echo "🔗 Checked $totalLinks unique links\n\n";

if (count($brokenLinks) > 0) {
    echo "❌ Found " . count($brokenLinks) . " potentially broken links:\n\n";

    foreach ($brokenLinks as $broken) {
        echo "📄 File: {$broken['file']}\n";
        echo "   ❌ Link: {$broken['link']}\n";
        echo "   📍 Resolved to: {$broken['resolved']}\n\n";
    }

    // Save report
    $reportPath = BASE_PATH . '/logs/link-audit-report.txt';
    $report = "Link Audit Report - " . date('Y-m-d H:i:s') . "\n";
    $report .= str_repeat('=', 60) . "\n\n";
    $report .= "Broken Links Found: " . count($brokenLinks) . "\n\n";

    foreach ($brokenLinks as $broken) {
        $report .= "File: {$broken['file']}\n";
        $report .= "Link: {$broken['link']}\n";
        $report .= "Resolved: {$broken['resolved']}\n\n";
    }

    if (!is_dir(dirname($reportPath))) {
        mkdir(dirname($reportPath), 0755, true);
    }
    file_put_contents($reportPath, $report);
    echo "📝 Report saved to: logs/link-audit-report.txt\n";

} else {
    echo "✅ No broken links found! All links are valid.\n";
}

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    AUDIT COMPLETE                            ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";
