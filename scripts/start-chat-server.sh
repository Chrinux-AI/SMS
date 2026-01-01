#!/bin/bash

# Verdant SMS - Chat Server Startup Script
# Starts the WebSocket server for real-time messaging

echo "🚀 Starting Verdant SMS Chat Server..."

# Change to project directory
cd /opt/lampp/htdocs/attendance

# Check if server is already running
if pgrep -f "websocket-chat-server.php" > /dev/null; then
    echo "⚠️  Chat server is already running!"
    echo "   Process ID: $(pgrep -f 'websocket-chat-server.php')"
    exit 1
fi

# Create logs directory if it doesn't exist
mkdir -p logs

# Start server in background
nohup php server/websocket-chat-server.php > logs/chat-server.log 2>&1 &

# Get process ID
SERVER_PID=$!

# Wait a moment for server to start
sleep 2

# Check if server is running
if ps -p $SERVER_PID > /dev/null; then
    echo "✅ Chat server started successfully!"
    echo "   Process ID: $SERVER_PID"
    echo "   WebSocket: ws://localhost:8080"
    echo "   Log file: logs/chat-server.log"
    echo ""
    echo "To stop the server:"
    echo "   ./scripts/stop-chat-server.sh"
else
    echo "❌ Failed to start chat server!"
    echo "   Check logs/chat-server.log for errors"
    exit 1
fi
