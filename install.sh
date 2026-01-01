#!/bin/bash

echo "🌿 Verdant SMS - One-Click Installer"
echo "====================================="

# Check for Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker Desktop first."
    exit 1
fi

echo "🚀 Building containers..."
docker-compose build

echo "🔥 Starting services..."
docker-compose up -d

echo "⏳ Waiting for database to initialize..."
sleep 10

echo "✅ Installation Complete!"
echo "-------------------------------------"
echo "🌍 App URL:      http://localhost:8080"
echo "📧 Mailhog:      http://localhost:8025"
echo "🗄️ Database:     Port 3306"
echo "-------------------------------------"
echo "Login with: admin@verdant.edu / Verdant2025!"
