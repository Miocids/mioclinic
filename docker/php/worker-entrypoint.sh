#!/bin/bash
set -e

echo "Queue worker: waiting for app container to finish setup..."
sleep 15

echo "Queue worker: starting..."
exec php artisan queue:work \
    --sleep=3 \
    --tries=3 \
    --timeout=300 \
    --max-time=3600 \
    --queue=default
