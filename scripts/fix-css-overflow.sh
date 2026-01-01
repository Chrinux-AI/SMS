#!/bin/bash

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║         VERDANT SMS - CSS OVERFLOW FIXER                    ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

BASE_DIR="/opt/lampp/htdocs/attendance"
FIXED=0

echo "🔍 Scanning CSS files for overflow: hidden issues..."
echo ""

# Find all CSS files and fix overflow issues
CSS_FILES=$(find "$BASE_DIR/assets/css" -name "*.css" 2>/dev/null)

for file in $CSS_FILES; do
    if grep -q "overflow.*hidden" "$file"; then
        echo "📝 Fixing: $(basename $file)"

        # Backup original
        cp "$file" "$file.backup"

        # Replace overflow: hidden with overflow-y: auto (preserve x-axis hidden)
        sed -i 's/overflow: hidden;/overflow-y: auto; overflow-x: hidden;/g' "$file"
        sed -i 's/overflow:hidden;/overflow-y: auto; overflow-x: hidden;/g' "$file"

        # Keep body and html as scroll/auto
        sed -i '/^body\|^html/,/}/ s/overflow-y: auto; overflow-x: hidden;/overflow-y: scroll; overflow-x: hidden;/' "$file"

        echo "   ✅ Fixed overflow issues in $(basename $file)"
        ((FIXED++))
    fi
done

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                    FIX COMPLETE                              ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "✅ Fixed $FIXED CSS files"
echo "💾 Backups created: *.css.backup"
echo ""
echo "🎯 Pages should now scroll properly!"
echo ""
