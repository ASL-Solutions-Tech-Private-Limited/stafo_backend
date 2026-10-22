#!/bin/sh
set -e

cd /var/www

export PYTHONPATH="/var/www/services/face_service/venv/lib/python3.10/site-packages:$PYTHONPATH"

echo "[Face Service] Starting Python Face Verification Engine on port 5005..."

exec "$@"
