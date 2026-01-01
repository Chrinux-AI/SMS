#!/usr/bin/env php
<?php
/**
 * ADD FAVICON LINKS TO ALL PHP FILES
 * Adds proper favicon link tags to <head> section of all PHP files
 */

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║         VERDANT SMS - FAVICON LINK INJECTOR                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$base_dir = '/opt/lampp/htdocs/attendance';
$updated = 0;
$skipped = 0;

// Favicon HTML to inject
$favicon_html = <<<'HTML'
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/favicon-96x96.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/apple-touch-icon.png">
    <link rel="manifest" href="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>manifest.json">
    <meta name="msapplication-TileColor" content="#00BFFF">
    <meta name="msapplication-TileImage" content="<?php echo isset($favicon_path) ? $favicon_path : '../'; ?>assets/images/icons/mstile-150x150.png">
    <meta name="theme-color" content="#0a0a0f">
HTML;

// Simple version for root files
$favicon_html_root = <<<'HTML'
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
HTML;

// Find all PHP files
$iterator = new RecursiveIteratorIterator(
  new RecursiveDirectoryIterator($base_dir, RecursiveDirectoryIterator::SKIP_DOTS)
);

$php_files = [];
foreach ($iterator as $file) {
  if ($file->isFile() && $file->getExtension() === 'php') {
    $path = $file->getPathname();

    // Skip vendor, backups, and setup files
    if (
      strpos($path, '/vendor/') !== false ||
      strpos($path, '/_backups/') !== false ||
      strpos($path, '/_setup/') !== false ||
      strpos($path, '/scripts/') !== false
    ) {
      continue;
    }

    $php_files[] = $path;
  }
}

echo "Found " . count($php_files) . " PHP files to process...\n\n";

foreach ($php_files as $file_path) {
  $content = file_get_contents($file_path);

  // Skip if already has favicon links
  if (strpos($content, 'favicon.ico') !== false || strpos($content, 'apple-touch-icon') !== false) {
    echo "⏭️  Skipped (has favicons): " . basename($file_path) . "\n";
    $skipped++;
    continue;
  }

  // Skip if no <head> tag
  if (strpos($content, '<head>') === false) {
    $skipped++;
    continue;
  }

  // Determine if root file or subfolder
  $relative_path = str_replace($base_dir . '/', '', $file_path);
  $is_root = (substr_count($relative_path, '/') === 0);

  $html_to_inject = $is_root ? $favicon_html_root : $favicon_html;

  // Inject after <head> or after <title>
  if (strpos($content, '</title>') !== false) {
    $content = preg_replace(
      '/(<\/title>)/i',
      "$1\n" . $html_to_inject,
      $content,
      1
    );
  } else if (strpos($content, '<head>') !== false) {
    $content = preg_replace(
      '/(<head>)/i',
      "$1\n" . $html_to_inject,
      $content,
      1
    );
  } else {
    $skipped++;
    continue;
  }

  file_put_contents($file_path, $content);
  echo "✅ Updated: $relative_path\n";
  $updated++;
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    UPDATE COMPLETE                           ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Updated: $updated files\n";
echo "⏭️  Skipped: $skipped files\n";
echo "\n";
echo "🎯 All pages now have favicon links!\n";
echo "   Clear browser cache and refresh to see the icon.\n";
echo "\n";
