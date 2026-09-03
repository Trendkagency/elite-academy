<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() === 'ar' ? 'مرحباً بك في أكاديمية إيليت' : 'Welcome to Elite Academy' }}</title>
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
            font-size: 22px;
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
            <h1 class="title">{{ app()->getLocale() === 'ar' ? "مرحباً بك يا {$user->name} في أكاديمية إيليت! 🎓" : "Welcome, {$user->name}, to Elite Academy! 🎓" }}</h1>
        </div>

        <div class="body-text">
            <p>{{ app()->getLocale() === 'ar' ? 'يسعدنا انضمامك إلى منصة إيليت التعليمية. يمكنك الآن متابعة حصصك المباشرة، وحل الواجبات، ومتابعة درجاتك وتقييمك الأكاديمي.' : 'We are thrilled to welcome you to Elite Academy. You can now access your interactive live classrooms, submit homework, and monitor your academic progress.' }}</p>
        </div>

        <div class="btn-container">
            <a href="{{ route('login') }}" class="btn">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول للمنصة ←' : 'Log In to Portal →' }}</a>
        </div>

        <div class="footer">
            <p>{{ app()->getLocale() === 'ar' ? 'أكاديمية إيليت — المنصة التعليمية الرائدة في مصر' : 'Elite Academy — Leading Educational Platform in Egypt' }}</p>
            <p>&copy; {{ date('Y') }} Elite Academy. {{ app()->getLocale() === 'ar' ? 'جميع الحقوق محفوظة.' : 'All rights reserved.' }}</p>
        </div>
    </div>
</body>
</html>
