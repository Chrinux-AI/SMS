#!/usr/bin/env php
<?php

/**
 * Link Checker Script
 * Scans all PHP files and validates internal links
 *
 * Usage: php scripts/check_links.php
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           VERDANT SMS - LINK INTEGRITY CHECKER              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$base_dir = dirname(__DIR__);
$broken_links = [];
$working_links = [];
$total_checked = 0;

// Scan all PHP files
$php_files = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($base_dir),
  RecursiveIteratorIterator::SELF_FIRST
);

$files_to_scan = [];
foreach ($php_files as $file) {
  if ($file->isFile() && $file->getExtension() === 'php') {
    $path = $file->getPathname();

    // Skip vendor and backups
    if (
      strpos($path, '/vendor/') !== false ||
      strpos($path, '/_backups/') !== false ||
      strpos($path, '/node_modules/') !== false
    ) {
      continue;
    }

    $files_to_scan[] = $path;
  }
}

echo "Found " . count($files_to_scan) . " PHP files to scan...\n\n";

foreach ($files_to_scan as $file_path) {
  $content = file_get_contents($file_path);
  $relative_path = str_replace($base_dir . '/', '', $file_path);

  // Extract href links
  preg_match_all('/href=["\']([^"\']+\.php[^"\']*)["\']/', $content, $matches);

  if (!empty($matches[1])) {
    foreach ($matches[1] as $link) {
      $total_checked++;

      // Skip external links
      if (strpos($link, 'http') === 0) {
        continue;
      }

      // Resolve relative path
      $file_dir = dirname($file_path);
      $target_path = realpath($file_dir . '/' . $link);

      // Remove query strings and anchors
      $link_file = preg_replace('/[?#].*$/', '', $link);
      $target_path = realpath($file_dir . '/' . $link_file);

      if (!$target_path || !file_exists($target_path)) {
        $broken_links[] = [
          'source' => $relative_path,
          'link' => $link,
          'resolved' => $target_path ?: 'NOT FOUND'
        ];
      } else {
        $working_links[] = $link;
      }
    }
  }
}

// Display results
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                       SCAN RESULTS                           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Total Links Checked: $total_checked\n";
echo "✅ Working Links: " . count($working_links) . "\n";
echo "❌ Broken Links: " . count($broken_links) . "\n\n";

if (!empty($broken_links)) {
  echo "╔══════════════════════════════════════════════════════════════╗\n";
  echo "║                     BROKEN LINKS                             ║\n";
  echo "╚══════════════════════════════════════════════════════════════╝\n\n";

  foreach ($broken_links as $broken) {
    echo "❌ Source: " . $broken['source'] . "\n";
    echo "   Link: " . $broken['link'] . "\n";
    echo "   Status: NOT FOUND\n\n";
  }

  // Generate report
  $report_file = $base_dir . '/logs/broken_links_' . date('Y-m-d_His') . '.txt';
  $report_dir = dirname($report_file);

  if (!is_dir($report_dir)) {
    mkdir($report_dir, 0755, true);
  }

  $report_content = "BROKEN LINKS REPORT\n";
  $report_content .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

  foreach ($broken_links as $broken) {
    $report_content .= "Source: {$broken['source']}\n";
    $report_content .= "Link: {$broken['link']}\n";
    $report_content .= "Status: NOT FOUND\n\n";
  }

  file_put_contents($report_file, $report_content);

  echo "📄 Full report saved to: $report_file\n\n";

  exit(1); // Exit with error code
} else {
  echo "🎉 All links are working! No broken links found.\n\n";
  exit(0);
}
