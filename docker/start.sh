#!/bin/bash
set -e

cd /app

# storage link (لو مش موجود بالفعل)
php artisan storage:link --force || true

# كاش الإعدادات والراوتس (اختياري - شيله لو بتستخدم config ديناميكي وقت التشغيل)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec supervisord -c /etc/supervisord.conf -n
