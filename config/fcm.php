<?php

/*
|--------------------------------------------------------------------------
| Firebase Cloud Messaging (FCM) Configuration
|--------------------------------------------------------------------------
|
| This file contains all configuration options for Firebase Cloud Messaging
| push notifications used across the Elite Academy LMS platform.
|
| Supported API modes:
|   - "legacy"  : Uses the FCM Legacy HTTP API (server key based)
|   - "v1"      : Uses the FCM HTTP v1 API (OAuth2 service account based)
|
| To activate FCM, set the keys in your .env file.
| If FCM_SERVER_KEY (legacy) or FCM_SERVICE_ACCOUNT_JSON (v1) is missing,
| notifications are saved in the DB only and push is silently skipped.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | FCM API Mode
    |--------------------------------------------------------------------------
    |
    | Choose which FCM API to use:
    |   "legacy" — Simple server key, easiest to set up. Being deprecated by Google.
    |   "v1"     — Modern OAuth2-based API, recommended for production.
    |
    */
    'mode' => env('FCM_MODE', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Legacy API (HTTP v0)
    |--------------------------------------------------------------------------
    |
    | Used when FCM_MODE=legacy.
    |
    | Get your Server Key from:
    |   Firebase Console → Project Settings → Cloud Messaging → Server Key
    |
    */
    'legacy' => [
        'key'      => env('FCM_SERVER_KEY'),
        'endpoint' => 'https://fcm.googleapis.com/fcm/send',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP v1 API (Recommended)
    |--------------------------------------------------------------------------
    |
    | Used when FCM_MODE=v1.
    |
    | Steps:
    |   1. Firebase Console → Project Settings → Service Accounts
    |   2. Click "Generate new private key" → download JSON file
    |   3. Set FCM_PROJECT_ID to your Firebase project ID
    |   4. Set FCM_SERVICE_ACCOUNT_JSON to the path of that JSON file
    |      OR paste the JSON content directly as FCM_SERVICE_ACCOUNT_JSON_CONTENT
    |
    */
    'v1' => [
        'project_id'           => env('FCM_PROJECT_ID', 'elite-academy-67a15'),
        'service_account_path' => env('FCM_SERVICE_ACCOUNT_JSON', base_path('config/elite-academy-67a15-firebase-adminsdk-fbsvc-cc97e50339.json')),
        'endpoint'             => 'https://fcm.googleapis.com/v1/projects/%s/messages:send',
    ],

    'web_config' => [
        'api_key'             => env('FCM_WEB_API_KEY', 'AIzaSyCmAS3q2VNvCbKhrfKtC8hn163GX7116Ns'),
        'auth_domain'         => env('FCM_WEB_AUTH_DOMAIN', 'elite-academy-67a15.firebaseapp.com'),
        'project_id'          => env('FCM_PROJECT_ID', 'elite-academy-67a15'),
        'storage_bucket'      => env('FCM_WEB_STORAGE_BUCKET', 'elite-academy-67a15.firebasestorage.app'),
        'messaging_sender_id' => env('FCM_WEB_MESSAGING_SENDER_ID', '53377882422'),
        'app_id'               => env('FCM_WEB_APP_ID', '1:53377882422:web:dddcb2f63b4fcc089f7b97'),
        'vapid_key'           => env('VAPID_PUBLIC_KEY', 'BM-m60kE0EKBkhRfnv-Lq4GH2X2NRZ15ir9QcSPJO_xKOHavHtTrTTbIvDShwxNcrgaMKLYU02fz0jgc7KfwZPA'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Defaults
    |--------------------------------------------------------------------------
    |
    | Default icon, sound, and click action for all push notifications.
    |
    */
    'defaults' => [
        'icon'         => env('FCM_DEFAULT_ICON', '/images/logo.png'),
        'sound'        => env('FCM_DEFAULT_SOUND', 'default'),
        'click_action' => env('FCM_DEFAULT_CLICK_ACTION', env('APP_URL') . '/student-portal'),
        'color'        => env('FCM_DEFAULT_COLOR', '#0F172A'),
        'badge'        => env('FCM_DEFAULT_BADGE', '1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Android / iOS Channel Settings
    |--------------------------------------------------------------------------
    |
    | Android channel ID used for notification categorization.
    | iOS sound and badge settings.
    |
    */
    'android' => [
        'channel_id'   => env('FCM_ANDROID_CHANNEL_ID', 'elite_academy_alerts'),
        'priority'     => env('FCM_ANDROID_PRIORITY', 'high'),         // "normal" or "high"
        'ttl'          => env('FCM_ANDROID_TTL', 86400),               // seconds (24h)
    ],

    'apns' => [
        'sound'        => env('FCM_APNS_SOUND', 'default'),
        'badge'        => env('FCM_APNS_BADGE', 1),
        'content_available' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Web Push (VAPID) Settings
    |--------------------------------------------------------------------------
    |
    | Used for browser-based Web Push Notifications.
    |
    | Generate VAPID keys:
    |   node -e "const webpush = require('web-push'); const keys = webpush.generateVAPIDKeys();
    |   console.log('Public:', keys.publicKey); console.log('Private:', keys.privateKey);"
    |
    */
    'vapid' => [
        'public_key'  => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'subject'     => env('VAPID_SUBJECT', 'mailto:admin@elite-academy.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging & Fallback
    |--------------------------------------------------------------------------
    |
    | When FCM is not configured or dispatch fails, notifications are logged
    | to the Laravel log channel defined below.
    |
    */
    'logging' => [
        'enabled'  => env('FCM_LOG_ENABLED', true),
        'channel'  => env('FCM_LOG_CHANNEL', 'stack'),
        'on_error' => env('FCM_LOG_ON_ERROR', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting & Batch Size
    |--------------------------------------------------------------------------
    |
    | Max tokens per FCM batch request (legacy API max: 1000).
    | Used by broadcastNotification() to chunk large recipient lists.
    |
    */
    'batch_size' => env('FCM_BATCH_SIZE', 500),

    /*
    |--------------------------------------------------------------------------
    | Token Cleanup
    |--------------------------------------------------------------------------
    |
    | How many days before an unused FCM token is considered stale
    | and eligible for cleanup (via artisan fcm:cleanup-tokens).
    |
    */
    'token_expiry_days' => env('FCM_TOKEN_EXPIRY_DAYS', 90),

];
