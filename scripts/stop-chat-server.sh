#!/bin/bash

# Verdant SMS - Chat Server Stop Script
# Stops the WebSocket server

echo "🛑 Stopping Verdant SMS Chat Server..."

# Find process ID
PID=$(pgrep -f "websocket-chat-server.php")

if [ -z "$PID" ]; then
    echo "⚠️  Chat server is not running."
    exit 0
fi

# Kill process
kill $PID

# Wait for process to stop
sleep 1

# Check if stopped
if pgrep -f "websocket-chat-server.php" > /dev/null; then
    echo "⚠️  Server did not stop gracefully, forcing..."
    kill -9 $PID
fi

echo "✅ Chat server stopped successfully!"
