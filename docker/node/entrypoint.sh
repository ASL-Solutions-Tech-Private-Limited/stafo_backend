#!/bin/sh
set -e

cd /var/www/chat-server

if [ ! -d "node_modules" ]; then
    echo "Installing chat-server node dependencies..."
    npm install --silent
fi

exec "$@"
