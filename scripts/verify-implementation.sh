#!/bin/bash

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║              VERDANT SMS - VERIFICATION TESTS                ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

BASE_DIR="/opt/lampp/htdocs/attendance"
PASSED=0
FAILED=0

# Test 1: Check favicon files
echo "🧪 Test 1: Favicon Files"
if [ -f "$BASE_DIR/assets/images/icons/favicon.ico" ] && \
   [ -f "$BASE_DIR/assets/images/icons/favicon-32x32.png" ] && \
   [ -f "$BASE_DIR/assets/images/icons/apple-touch-icon.png" ]; then
    echo "   ✅ PASSED - All favicon files exist"
    ((PASSED++))
else
    echo "   ❌ FAILED - Missing favicon files"
    ((FAILED++))
fi

# Test 2: Check demo-request.php navigation
echo "🧪 Test 2: Demo Request Navigation"
if grep -q 'href="../index.php"' "$BASE_DIR/visitor/demo-request.php"; then
    echo "   ✅ PASSED - Back to Home button links correctly"
    ((PASSED++))
else
    echo "   ❌ FAILED - Incorrect navigation link"
    ((FAILED++))
fi

# Test 3: Check email functionality
echo "🧪 Test 3: Email Configuration"
if grep -q 'christolabiyi35@gmail.com' "$BASE_DIR/visitor/demo-request.php" && \
   grep -q 'send_email' "$BASE_DIR/visitor/demo-request.php"; then
    echo "   ✅ PASSED - Email notification configured"
    ((PASSED++))
else
    echo "   ❌ FAILED - Email not configured"
    ((FAILED++))
fi

# Test 4: Check generated pages
echo "🧪 Test 4: Generated Pages"
HEALTH_COUNT=$(find "$BASE_DIR/admin/health" -name "*.php" 2>/dev/null | wc -l)
LIBRARY_COUNT=$(find "$BASE_DIR/admin/library" -name "*.php" 2>/dev/null | wc -l)
if [ "$HEALTH_COUNT" -ge 10 ] && [ "$LIBRARY_COUNT" -ge 10 ]; then
    echo "   ✅ PASSED - Missing pages created (Health: $HEALTH_COUNT, Library: $LIBRARY_COUNT)"
    ((PASSED++))
else
    echo "   ❌ FAILED - Missing pages not found"
    ((FAILED++))
fi

# Test 5: Check CSS overflow fixes
echo "🧪 Test 5: CSS Scrolling Fixes"
if grep -q "overflow-y: auto" "$BASE_DIR/assets/css/cyberpunk-ui.css"; then
    echo "   ✅ PASSED - CSS overflow fixed"
    ((PASSED++))
else
    echo "   ❌ FAILED - CSS not fixed"
    ((FAILED++))
fi

# Test 6: Check manifest.json
echo "🧪 Test 6: PWA Manifest"
if grep -q "android-chrome-192x192.png" "$BASE_DIR/manifest.json"; then
    echo "   ✅ PASSED - Manifest updated with correct icons"
    ((PASSED++))
else
    echo "   ❌ FAILED - Manifest not updated"
    ((FAILED++))
fi

# Test 7: Check favicon links in files
echo "🧪 Test 7: Favicon Links in PHP Files"
FAVICON_COUNT=$(grep -r "favicon.ico" "$BASE_DIR" --include="*.php" 2>/dev/null | wc -l)
if [ "$FAVICON_COUNT" -gt 100 ]; then
    echo "   ✅ PASSED - Favicon links added to $FAVICON_COUNT locations"
    ((PASSED++))
else
    echo "   ❌ FAILED - Not enough favicon links found"
    ((FAILED++))
fi

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                      TEST RESULTS                            ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "  ✅ Passed: $PASSED tests"
echo "  ❌ Failed: $FAILED tests"
echo ""

if [ "$FAILED" -eq 0 ]; then
    echo "  🎉 ALL TESTS PASSED! System is ready!"
else
    echo "  ⚠️  Some tests failed. Check the output above."
fi

echo ""
echo "🌐 QUICK ACCESS URLS:"
echo "   • Home: http://localhost/attendance/"
echo "   • Demo Request: http://localhost/attendance/visitor/demo-request.php"
echo "   • Login: http://localhost/attendance/login.php"
echo ""
