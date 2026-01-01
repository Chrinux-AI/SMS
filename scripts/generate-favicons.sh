#!/bin/bash

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║           VERDANT SMS - FAVICON GENERATOR                   ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

BASE_DIR="/opt/lampp/htdocs/attendance"
ICONS_DIR="$BASE_DIR/assets/images/icons"
SOURCE_SVG="$BASE_DIR/assets/images/logo.svg"

# Create icons directory
mkdir -p "$ICONS_DIR"

# Check if ImageMagick is installed
if ! command -v convert &> /dev/null && ! command -v magick &> /dev/null; then
    echo "❌ ImageMagick not found. Installing..."
    echo "Run: sudo apt-get install imagemagick (Ubuntu/Debian)"
    echo "or: brew install imagemagick (Mac)"
    exit 1
fi

# Determine convert command
if command -v magick &> /dev/null; then
    CONVERT="magick convert"
else
    CONVERT="convert"
fi

echo "📦 Source: $SOURCE_SVG"
echo "📁 Output: $ICONS_DIR"
echo ""

# Generate all favicon sizes
echo "🎨 Generating favicon-16x16.png..."
$CONVERT "$SOURCE_SVG" -resize 16x16 "$ICONS_DIR/favicon-16x16.png"

echo "🎨 Generating favicon-32x32.png..."
$CONVERT "$SOURCE_SVG" -resize 32x32 "$ICONS_DIR/favicon-32x32.png"

echo "🎨 Generating favicon-96x96.png..."
$CONVERT "$SOURCE_SVG" -resize 96x96 "$ICONS_DIR/favicon-96x96.png"

echo "🎨 Generating android-chrome-192x192.png..."
$CONVERT "$SOURCE_SVG" -resize 192x192 "$ICONS_DIR/android-chrome-192x192.png"

echo "🎨 Generating android-chrome-512x512.png..."
$CONVERT "$SOURCE_SVG" -resize 512x512 "$ICONS_DIR/android-chrome-512x512.png"

echo "🎨 Generating apple-touch-icon.png (180x180)..."
$CONVERT "$SOURCE_SVG" -resize 180x180 "$ICONS_DIR/apple-touch-icon.png"

echo "🎨 Generating apple-touch-icon-precomposed.png..."
cp "$ICONS_DIR/apple-touch-icon.png" "$ICONS_DIR/apple-touch-icon-precomposed.png"

echo "🎨 Generating mstile-150x150.png (Windows)..."
$CONVERT "$SOURCE_SVG" -resize 150x150 "$ICONS_DIR/mstile-150x150.png"

echo "🎨 Generating favicon.ico (multi-size)..."
$CONVERT "$SOURCE_SVG" -resize 16x16 "$ICONS_DIR/favicon-16.png"
$CONVERT "$SOURCE_SVG" -resize 32x32 "$ICONS_DIR/favicon-32.png"
$CONVERT "$SOURCE_SVG" -resize 48x48 "$ICONS_DIR/favicon-48.png"
$CONVERT "$ICONS_DIR/favicon-16.png" "$ICONS_DIR/favicon-32.png" "$ICONS_DIR/favicon-48.png" "$ICONS_DIR/favicon.ico"
rm "$ICONS_DIR/favicon-16.png" "$ICONS_DIR/favicon-32.png" "$ICONS_DIR/favicon-48.png"

# Copy to root for easy access
cp "$ICONS_DIR/favicon.ico" "$BASE_DIR/favicon.ico"

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                  GENERATION COMPLETE                         ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "✅ Generated all favicon sizes:"
ls -lh "$ICONS_DIR"
echo ""
echo "🎯 Next: Update HTML files with favicon links"
echo "   Run: php scripts/add-favicon-links.php"
echo ""
