#!/bin/bash

# Verdant SMS - Messaging System Installation Script
# Creates database tables and installs dependencies for WhatsApp/Telegram clone

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║     VERDANT SMS - MESSAGING SYSTEM INSTALLATION              ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Change to project directory
cd /opt/lampp/htdocs/attendance

# Step 1: Import database schema
echo "📊 Step 1/3: Creating database tables..."
mysql -u root --socket=/opt/lampp/var/mysql/mysql.sock attendance_system < database/messaging_system_schema.sql

if [ $? -eq 0 ]; then
    echo "✅ Database tables created successfully!"
else
    echo "❌ Error creating database tables. Check MySQL is running."
    exit 1
fi

# Step 2: Install Ratchet library
echo ""
echo "📦 Step 2/3: Installing Ratchet WebSocket library..."
composer require cboden/ratchet

if [ $? -eq 0 ]; then
    echo "✅ Ratchet installed successfully!"
else
    echo "❌ Error installing Ratchet. Check composer is installed."
    exit 1
fi

# Step 3: Create upload directories
echo ""
echo "📁 Step 3/3: Creating upload directories..."
mkdir -p uploads/voice_notes
mkdir -p uploads/chat_files
mkdir -p uploads/chat_thumbnails
chmod 755 uploads/voice_notes
chmod 755 uploads/chat_files
chmod 755 uploads/chat_thumbnails

echo "✅ Upload directories created!"

# Verify installation
echo ""
echo "🔍 Verifying installation..."
echo ""

# Check database tables
TABLE_COUNT=$(mysql -u root --socket=/opt/lampp/var/mysql/mysql.sock attendance_system -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'attendance_system' AND table_name LIKE 'chat_%' OR table_name LIKE 'conversation%' OR table_name LIKE 'video_call%'" -s -N)

echo "📊 Database Tables Created: $TABLE_COUNT"

# Check Ratchet
if [ -d "vendor/cboden/ratchet" ]; then
    echo "✅ Ratchet Library: Installed"
else
    echo "❌ Ratchet Library: Not found"
fi

# Check directories
if [ -d "uploads/voice_notes" ]; then
    echo "✅ Voice Notes Directory: Created"
else
    echo "❌ Voice Notes Directory: Missing"
fi

if [ -d "uploads/chat_files" ]; then
    echo "✅ Chat Files Directory: Created"
else
    echo "❌ Chat Files Directory: Missing"
fi

echo ""
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║     ✅ MESSAGING SYSTEM INSTALLATION COMPLETE!               ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""
echo "🚀 To start the WebSocket server:"
echo "   php server/websocket-chat-server.php"
echo ""
echo "Or run in background:"
echo "   nohup php server/websocket-chat-server.php > logs/chat-server.log 2>&1 &"
echo ""
