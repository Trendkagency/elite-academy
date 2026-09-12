# syntax=docker/dockerfile:1
# =========================================================
# Dockerfile بديل لـ Nixpacks - Laravel + Filament v3
# nginx + php-fpm + queue worker + scheduler عبر supervisor
# =========================================================

FROM php:8.4-fpm-alpine

# حزم النظام الأساسية
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        curl \
        git \
        unzip \
        icu-data-full

# تثبيت PHP extensions بسرعة وبدون بطء أو استهلاك زائد للذاكرة
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
        pdo_mysql \
        zip \
        gd \
        intl \
        mbstring \
        bcmath \
        exif \
        opcache

# تثبيت Composer مباشرة من صورته الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_PROCESS_TIMEOUT=600

# 1. نسخ ملفات composer أولاً للاستفادة من الـ Docker layer caching
COPY composer.json composer.lock ./

RUN composer config process-timeout 600 \
    && composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --ignore-platform-reqs \
        --no-interaction

# 2. نسخ كود المشروع بالكامل
COPY --chown=www-data:www-data . .

# 3. إنشاء الـ autoloader المحسن
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# 4. تنظيف ملفات التطوير وغير المطلوبة في الـ Production
RUN rm -rf \
        tests \
        .git \
        .github \
        .env.example \
        storage/logs/*.log \
        node_modules \
    && composer clear-cache 2>/dev/null || true

# 5. صلاحيات المجلدات اللازمة لـ Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 6. ملفات إعداد nginx / php-fpm / supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-custom.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/worker-nginx.conf /etc/supervisor/conf.d/worker-nginx.conf
COPY docker/worker-phpfpm.conf /etc/supervisor/conf.d/worker-phpfpm.conf
COPY docker/worker-laravel.conf /etc/supervisor/conf.d/worker-laravel.conf
COPY docker/worker-scheduler.conf /etc/supervisor/conf.d/worker-scheduler.conf
COPY docker/start.sh /start.sh

RUN chmod +x /start.sh \
    && mkdir -p /var/log/supervisor /var/lib/nginx/tmp \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx

EXPOSE 80

CMD ["/start.sh"]
