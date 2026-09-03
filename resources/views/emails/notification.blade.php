<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Elite Academy Notification' }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #FAFAF9;
            margin: 0;
            padding: 24px;
            color: #0F172A;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 20px;
            border: 1px solid #E2E8F0;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #F1F5F9;
            padding-bottom: 24px;
            margin-bottom: 24px;
        }
        .logo {
            height: 48px;
            width: auto;
        }
        .title {
            font-size: 20px;
            font-weight: 800;
            color: #0F172A;
            margin: 16px 0 8px 0;
        }
        .body-text {
            font-size: 15px;
            color: #334155;
            margin-bottom: 24px;
        }
        .btn-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            display: inline-block;
            background-color: #0D9488;
            color: #FFFFFF !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 14px;
        }
        .footer {
            border-top: 1px solid #F1F5F9;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo_500.webp') }}" alt="Elite Academy" class="logo">
            <h1 class="title">{{ $title }}</h1>
        </div>

        <div class="body-text">
            <p>{{ $body }}</p>
        </div>

        @if(!empty($actionUrl))
            <div class="btn-container">
                <a href="{{ $actionUrl }}" class="btn">{{ $actionLabel ?? (app()->getLocale() === 'ar' ? 'الانتقال إلى البوابة ←' : 'Go to Portal →') }}</a>
            </div>
        @endif

        <div class="footer">
            <p>{{ app()->getLocale() === 'ar' ? 'أكاديمية إيليت — المنصة التعليمية الرائدة في مصر' : 'Elite Academy — Leading Educational Platform in Egypt' }}</p>
            <p>&copy; {{ date('Y') }} Elite Academy. {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
        </div>
    </div>
</body>
</html>
