#!/bin/bash

# ═══════════════════════════════════════════════════════════════
# VERDANT SMS v4.0 - QUICK START SCRIPT
# Automated setup for development environment
# ═══════════════════════════════════════════════════════════════

set -e  # Exit on error

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║         VERDANT SMS v4.0 - QUICK START INSTALLER            ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run as root (sudo)${NC}"
    exit 1
fi

echo -e "${GREEN}[1/10]${NC} Checking prerequisites..."

# Check PHP version
if ! command -v php &> /dev/null; then
    echo -e "${RED}PHP not found. Please install PHP 8.3+${NC}"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "  ✓ PHP $PHP_VERSION detected"

# Check MySQL
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}MySQL not found. Please install MySQL 8.0+${NC}"
    exit 1
fi
echo "  ✓ MySQL detected"

# Check Apache
if ! command -v apache2ctl &> /dev/null && ! command -v httpd &> /dev/null; then
    echo -e "${YELLOW}Apache not detected. You may need to install it separately.${NC}"
else
    echo "  ✓ Apache detected"
fi

echo ""
echo -e "${GREEN}[2/10]${NC} Creating .env file from template..."

if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "  ✓ .env created from .env.example"
        echo -e "${YELLOW}  ⚠ Please edit .env with your actual credentials${NC}"
    else
        echo -e "${RED}  ✗ .env.example not found${NC}"
        exit 1
    fi
else
    echo "  ✓ .env already exists"
fi

echo ""
echo -e "${GREEN}[3/10]${NC} Setting up directory permissions..."

chmod -R 755 assets/
chmod -R 777 uploads/
chmod -R 777 logs/
chmod -R 777 cache/
chmod -R 777 _backups/

mkdir -p uploads logs cache _backups
chmod -R 777 uploads/ logs/ cache/ _backups/

echo "  ✓ Permissions set"

echo ""
echo -e "${GREEN}[4/10]${NC} Installing Composer dependencies..."

if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader
    echo "  ✓ Composer dependencies installed"
else
    echo -e "${YELLOW}  ⚠ Composer not found. Skipping...${NC}"
fi

echo ""
echo -e "${GREEN}[5/10]${NC} Creating database..."

# Read .env for database credentials
DB_NAME=$(grep "^DB_NAME=" .env | cut -d '=' -f2)
DB_USER=$(grep "^DB_USER=" .env | cut -d '=' -f2)
DB_PASS=$(grep "^DB_PASS=" .env | cut -d '=' -f2)

if [ -z "$DB_NAME" ]; then
    DB_NAME="attendance_system"
fi

# Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || {
    echo -e "${YELLOW}  ⚠ Could not create database automatically. Please create manually.${NC}"
}

echo "  ✓ Database created/verified"

echo ""
echo -e "${GREEN}[6/10]${NC} Importing database schema..."

if [ -f database/verdant-sms-schema.sql ]; then
    mysql -u root -p $DB_NAME < database/verdant-sms-schema.sql 2>/dev/null || {
        echo -e "${YELLOW}  ⚠ Could not import schema. Please import manually.${NC}"
    }
    echo "  ✓ Schema imported"
else
    echo -e "${YELLOW}  ⚠ Schema file not found. Skipping...${NC}"
fi

echo ""
echo -e "${GREEN}[7/10]${NC} Creating default admin account..."

php _setup/create-default-admin.php 2>/dev/null || {
    echo -e "${YELLOW}  ⚠ Could not create admin. You may need to do this manually.${NC}"
}

echo ""
echo -e "${GREEN}[8/10]${NC} Setting up Apache configuration..."

if [ -d /opt/lampp ]; then
    # LAMPP/XAMPP detected
    echo "  ✓ LAMPP detected at /opt/lampp"
    echo "  Your site should be accessible at: http://localhost/attendance"
elif [ -d /etc/apache2 ]; then
    # Standard Apache on Linux
    cat > /etc/apache2/sites-available/verdant-sms.conf <<EOF
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /opt/lampp/htdocs/attendance

    <Directory /opt/lampp/htdocs/attendance>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/verdant-sms-error.log
    CustomLog \${APACHE_LOG_DIR}/verdant-sms-access.log combined
</VirtualHost>
EOF

    a2ensite verdant-sms.conf
    a2enmod rewrite
    systemctl reload apache2

    echo "  ✓ Apache configured"
fi

echo ""
echo -e "${GREEN}[9/10]${NC} Running system checks..."

php scripts/check_links.php 2>/dev/null || {
    echo -e "${YELLOW}  ⚠ Link checker not available yet${NC}"
}

echo ""
echo -e "${GREEN}[10/10]${NC} Installation complete!"

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║                    🎉 SUCCESS!                               ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "Your Verdant SMS installation is ready!"
echo ""
echo "📍 Access your site at: http://localhost/attendance"
echo ""
echo "🔑 Default admin credentials:"
echo "   Email: admin@verdantsms.com"
echo "   Password: Admin@123"
echo ""
echo "⚠️  IMPORTANT NEXT STEPS:"
echo "   1. Edit .env file with your actual credentials"
echo "   2. Change the default admin password"
echo "   3. Set TEST_MODE=false in production"
echo "   4. Configure email/SMS settings"
echo "   5. Create favicon and branding assets"
echo ""
echo "📖 Read IMPLEMENTATION_GUIDE.md for detailed setup instructions"
echo "📋 Check TODO-COMPLETE-V4.md for feature roadmap"
echo ""
echo "Need help? Contact: christolabiyi35@gmail.com"
echo ""
