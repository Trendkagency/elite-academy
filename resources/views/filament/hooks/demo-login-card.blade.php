@if(demo_enabled())
    <div class="demo-admin-login-hook">
        <style>
            .demo-admin-login-hook {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 0.85rem !important;
                margin-bottom: 1.25rem !important;
                box-sizing: border-box !important;
                font-family: 'Cairo', system-ui, sans-serif !important;
            }
            .demo-admin-login-hook svg {
                box-sizing: content-box !important;
                vertical-align: middle !important;
            }
            .demo-admin-banner {
                display: flex !important;
                align-items: center !important;
                gap: 0.65rem !important;
                padding: 0.65rem 0.85rem !important;
                border-radius: 0.85rem !important;
                font-size: 0.75rem !important;
                font-weight: 700 !important;
                box-sizing: border-box !important;
                background-color: #FEF3C7 !important;
                border: 1px solid #FCD34D !important;
                color: #92400E !important;
            }
            html.dark .demo-admin-banner {
                background-color: rgba(245, 158, 11, 0.15) !important;
                border-color: rgba(245, 158, 11, 0.35) !important;
                color: #FCD34D !important;
            }
            .demo-admin-banner svg {
                width: 1.15rem !important;
                height: 1.15rem !important;
                min-width: 1.15rem !important;
                min-height: 1.15rem !important;
                max-width: 1.15rem !important;
                max-height: 1.15rem !important;
                flex-shrink: 0 !important;
                color: #D97706 !important;
            }
            html.dark .demo-admin-banner svg {
                color: #FBBF24 !important;
            }
            .demo-admin-credentials-box {
                padding: 0.85rem 0.95rem !important;
                border-radius: 1rem !important;
                border: 1px solid #E2E8F0 !important;
                background-color: #F8FAFC !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
                box-sizing: border-box !important;
            }
            html.dark .demo-admin-credentials-box {
                background-color: rgba(15, 23, 42, 0.6) !important;
                border-color: rgba(51, 65, 85, 0.6) !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2) !important;
            }
            .demo-admin-divider {
                display: flex !important;
                align-items: center !important;
                text-align: center !important;
                margin: 0.5rem 0 !important;
                width: 100% !important;
            }
            .demo-admin-divider::before,
            .demo-admin-divider::after {
                content: '' !important;
                flex: 1 !important;
                border-bottom: 1px solid #E2E8F0 !important;
            }
            html.dark .demo-admin-divider::before,
            html.dark .demo-admin-divider::after {
                border-bottom-color: rgba(51, 65, 85, 0.6) !important;
            }
            .demo-admin-divider span {
                padding: 0 0.75rem !important;
                font-size: 0.7rem !important;
                font-weight: 700 !important;
                color: #94A3B8 !important;
                letter-spacing: 0.02em !important;
            }
            html.dark .demo-admin-divider span {
                color: #64748B !important;
            }
        </style>

        @if(config('demo.guards.show_banner', true))
            <div class="demo-admin-banner">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ config('demo.guards.banner_text', 'Demo mode — data resets automatically.') }}</span>
            </div>
        @endif

        <div class="demo-admin-credentials-box">
            <x-demo.credentials />
        </div>

        <div class="demo-admin-divider">
            <span>{{ app()->getLocale() === 'ar' ? 'أو تسجيل الدخول اليدوي' : 'Or Manual Admin Credentials' }}</span>
        </div>
    </div>
@endif
