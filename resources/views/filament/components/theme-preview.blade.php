<div id="theme-live-preview-container" style="
    background: linear-gradient(145deg, #090d16 0%, #0f172a 100%);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 24px;
    color: #f8fafc;
    box-shadow: 0 20px 40px -15px rgba(0,0,0,0.6);
    margin-top: 10px;
    font-family: inherit;
">
    {{-- Header & Presets Toolbar --}}
    <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px; padding-bottom:18px; border-bottom:1px solid rgba(255,255,255,0.08);">
        <div>
            <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(13,148,136,0.15); border:1px solid rgba(13,148,136,0.3); padding:4px 12px; border-radius:9999px; font-size:11px; font-weight:700; color:#2dd4bf; letter-spacing:0.05em; text-transform:uppercase;">
                <span>✨ Real-time Component Preview</span>
            </div>
            <h3 style="font-size:18px; font-weight:800; color:#ffffff; margin:6px 0 2px 0;">Interactive UI Design Sandbox</h3>
            <p style="font-size:12px; color:#94a3b8; margin:0;">Live interactive visualization of all system components. Changes above apply live here and across the entire platform.</p>
        </div>

        {{-- 1-Click Preset Badges --}}
        <div style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
            <span style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase;">Presets:</span>
            
            <button type="button" onclick="window.__applyThemePreset('cyber_teal')" style="cursor:pointer; background:rgba(13,148,136,0.2); border:1px solid #14b8a6; color:#2dd4bf; font-size:11px; font-weight:700; padding:6px 12px; border-radius:8px; transition:all 0.2s ease;">
                💎 Cyber Teal
            </button>
            <button type="button" onclick="window.__applyThemePreset('royal_indigo')" style="cursor:pointer; background:rgba(99,102,241,0.2); border:1px solid #6366f1; color:#a5b4fc; font-size:11px; font-weight:700; padding:6px 12px; border-radius:8px; transition:all 0.2s ease;">
                🔮 Royal Indigo
            </button>
            <button type="button" onclick="window.__applyThemePreset('sunset_amber')" style="cursor:pointer; background:rgba(245,158,11,0.2); border:1px solid #f59e0b; color:#fcd34d; font-size:11px; font-weight:700; padding:6px 12px; border-radius:8px; transition:all 0.2s ease;">
                🌅 Sunset Amber
            </button>
            <button type="button" onclick="window.__applyThemePreset('crimson_flame')" style="cursor:pointer; background:rgba(244,63,94,0.2); border:1px solid #f43f5e; color:#fda4af; font-size:11px; font-weight:700; padding:6px 12px; border-radius:8px; transition:all 0.2s ease;">
                🌹 Crimson Flame
            </button>
            <button type="button" onclick="window.__applyThemePreset('emerald_growth')" style="cursor:pointer; background:rgba(16,185,129,0.2); border:1px solid #10b981; color:#6ee7b7; font-size:11px; font-weight:700; padding:6px 12px; border-radius:8px; transition:all 0.2s ease;">
                🍃 Emerald Growth
            </button>
        </div>
    </div>

    {{-- CSS Variables Hook for Live Sandbox --}}
    <style id="theme-live-preview-dynamic-css">
        :root {
            --pv-primary: #0d9488;
            --pv-secondary: #6366f1;
            --pv-accent: #f59e0b;
            --pv-btn-radius: 9999px;
            --pv-card-radius: 16px;
            --pv-badge-radius: 9999px;
            --pv-font: "Cairo", sans-serif;
            --pv-overlay: 0.55;
        }
        .pv-btn-primary {
            background: linear-gradient(135deg, var(--pv-primary) 0%, #06b6d4 100%);
            color: #041017;
            font-weight: 700;
            border-radius: var(--pv-btn-radius);
            box-shadow: 0 4px 14px rgba(13,148,136,0.35);
            transition: all 0.25s cubic-bezier(0.16,1,0.3,1);
            border: none;
            cursor: pointer;
        }
        .pv-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(13,148,136,0.55);
        }
        .pv-btn-secondary {
            background: rgba(255,255,255,0.08);
            color: #ffffff;
            font-weight: 700;
            border-radius: var(--pv-btn-radius);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            transition: all 0.25s cubic-bezier(0.16,1,0.3,1);
            cursor: pointer;
        }
        .pv-btn-secondary:hover {
            background: rgba(255,255,255,0.18);
            transform: translateY(-2px);
        }
        .pv-card {
            background: rgba(15,23,42,0.85);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--pv-card-radius);
            backdrop-filter: blur(12px);
            transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
        }
        .pv-card:hover {
            transform: translateY(-4px);
            border-color: var(--pv-primary);
            box-shadow: 0 16px 36px -10px rgba(13,148,136,0.3);
        }
        .pv-badge {
            border-radius: var(--pv-badge-radius);
            background: rgba(13,148,136,0.15);
            border: 1px solid rgba(13,148,136,0.4);
            color: var(--pv-primary);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>

    {{-- Grid Layout for Previews --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:20px;">

        {{-- 1. Mini Hero Slider Simulation --}}
        <div style="grid-column: 1 / -1; position:relative; overflow:hidden; border-radius:var(--pv-card-radius); border:1px solid rgba(255,255,255,0.15); min-height:220px; display:flex; flex-direction:column; justify-content:space-between; padding:24px; background:#020617;">
            {{-- Background Image --}}
            <img src="{{ asset('images/hero_student.webp') }}" alt="Hero Background" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0.6; z-index:0;">
            {{-- Dark Overlay --}}
            <div id="pv-hero-overlay" style="position:absolute; inset:0; background:rgba(2,6,23,0.65); z-index:1;"></div>
            
            {{-- Slide Content --}}
            <div style="position:relative; z-index:2; max-width:580px;">
                <div id="pv-hero-badge" class="pv-badge" style="margin-bottom:10px;">
                    <span>🚀 EGYPT'S #1 ACADEMIC PLATFORM</span>
                </div>
                <h4 id="pv-hero-title" style="font-size:22px; font-weight:800; color:#ffffff; line-height:1.2; margin:0 0 8px 0; text-shadow:0 2px 8px rgba(0,0,0,0.8);">
                    Empowering Future Leaders with Practical Academic Excellence
                </h4>
                <p id="pv-hero-subtitle" style="font-size:12px; color:#cbd5e1; margin:0 0 16px 0; line-height:1.5;">
                    Join thousands of students learning Programming, Artificial Intelligence, Science, and Business.
                </p>
                <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                    <button type="button" id="pv-hero-btn-primary" class="pv-btn-primary" style="padding:8px 20px; font-size:12px;">
                        <span>Explore Now →</span>
                    </button>
                    <button type="button" id="pv-hero-btn-secondary" class="pv-btn-secondary" style="padding:8px 18px; font-size:12px;">
                        <span>Book Free Trial</span>
                    </button>
                </div>
            </div>

            {{-- Slider Bottom Indicator Bar --}}
            <div style="position:relative; z-index:2; display:flex; align-items:center; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.15); font-size:11px; color:#94a3b8;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span id="pv-hero-counter" style="color:var(--pv-primary); font-weight:800; font-family:monospace;">01 / 04</span>
                    <div style="width:60px; height:4px; background:rgba(255,255,255,0.2); border-radius:99px; overflow:hidden;">
                        <div id="pv-hero-prog" style="width:45%; height:100%; background:var(--pv-primary);"></div>
                    </div>
                </div>
                <div style="display:flex; gap:6px;">
                    <span style="display:inline-block; width:18px; height:6px; border-radius:99px; background:var(--pv-primary);"></span>
                    <span style="display:inline-block; width:6px; height:6px; border-radius:99px; background:rgba(255,255,255,0.3);"></span>
                    <span style="display:inline-block; width:6px; height:6px; border-radius:99px; background:rgba(255,255,255,0.3);"></span>
                </div>
            </div>
        </div>

        {{-- 2. Button Component Showcase (mtns / Buttons) --}}
        <div class="pv-card" style="padding:20px;">
            <div style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                <span>🔘 Button Elements &amp; States</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                    <button type="button" class="pv-btn-primary" style="padding:10px 22px; font-size:13px;">
                        Primary Action
                    </button>
                    <button type="button" class="pv-btn-secondary" style="padding:10px 20px; font-size:13px;">
                        Secondary Action
                    </button>
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                    <button type="button" id="pv-btn-glow" style="cursor:pointer; background:var(--pv-primary); color:#041017; font-weight:800; padding:8px 18px; border-radius:var(--pv-btn-radius); font-size:12px; border:none; box-shadow:0 0 16px var(--pv-primary);">
                        ✨ Glowing Neon
                    </button>
                    <button type="button" id="pv-btn-outline" style="cursor:pointer; background:transparent; color:var(--pv-primary); font-weight:700; padding:8px 18px; border-radius:var(--pv-btn-radius); font-size:12px; border:1.5px solid var(--pv-primary);">
                        🔲 Outline Variant
                    </button>
                </div>

                <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
                    <span style="font-size:11px; color:#64748b;">Active Radius:</span>
                    <code id="pv-btn-radius-label" style="font-size:11px; color:#2dd4bf; background:rgba(0,0,0,0.3); padding:2px 6px; border-radius:4px;">Full Pill (9999px)</code>
                </div>
            </div>
        </div>

        {{-- 3. Card & Container Showcase --}}
        <div class="pv-card" style="padding:20px;">
            <div style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
                <span>🎴 Feature / Course Card</span>
                <span id="pv-card-style-badge" style="font-size:10px; background:rgba(255,255,255,0.1); padding:2px 8px; border-radius:6px; color:#94a3b8;">Glassmorphic</span>
            </div>

            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:calc(var(--pv-card-radius) - 4px); padding:14px; display:flex; gap:12px; align-items:center;">
                <div style="width:50px; height:50px; border-radius:calc(var(--pv-card-radius) - 6px); background:linear-gradient(135deg, var(--pv-primary) 0%, var(--pv-secondary) 100%); display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
                    🤖
                </div>
                <div style="flex:1;">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <span style="font-size:11px; color:var(--pv-primary); font-weight:700;">Artificial Intelligence</span>
                        <span style="font-size:10px; color:#fbbf24; font-weight:700;">★ 4.9 (1.2k)</span>
                    </div>
                    <div style="font-size:14px; font-weight:800; color:#ffffff; margin:2px 0;">Machine Learning Track</div>
                    <div style="font-size:11px; color:#94a3b8;">Weekly live streams &amp; hands-on projects</div>
                </div>
            </div>

            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.06);">
                <div style="font-size:12px; font-weight:700; color:#2dd4bf; display:flex; align-items:center; gap:6px;">
                    <span style="width:7px; height:7px; border-radius:50%; background:#2dd4bf;"></span>
                    <span>12 Sessions Included</span>
                </div>
                <button type="button" class="pv-btn-primary" style="padding:6px 14px; font-size:11px;">
                    Enroll Now
                </button>
            </div>
        </div>

        {{-- 4. Badges, Tags & Metrics --}}
        <div class="pv-card" style="padding:20px;">
            <div style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:12px;">
                <span>🏷️ Badges, Tags &amp; Counters</span>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px;">
                <div class="pv-badge">
                    <span style="width:6px; height:6px; border-radius:50%; background:var(--pv-primary);"></span>
                    <span>Live Stream Active</span>
                </div>
                <div class="pv-badge" style="background:rgba(99,102,241,0.15); border-color:rgba(99,102,241,0.4); color:#a5b4fc;">
                    <span>🎯 Certified Mentor</span>
                </div>
                <div class="pv-badge" style="background:rgba(245,158,11,0.15); border-color:rgba(245,158,11,0.4); color:#fcd34d;">
                    <span>⭐ 98.5% Satisfaction</span>
                </div>
            </div>

            {{-- Stat counter metric box --}}
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                <div style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.06); border-radius:calc(var(--pv-card-radius) - 6px); padding:10px; text-align:center;">
                    <div style="font-size:18px; font-weight:800; color:var(--pv-primary); font-family:monospace;">25,000+</div>
                    <div style="font-size:10px; color:#94a3b8; margin-top:2px;">Active Students</div>
                </div>
                <div style="background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.06); border-radius:calc(var(--pv-card-radius) - 6px); padding:10px; text-align:center;">
                    <div style="font-size:18px; font-weight:800; color:#f59e0b; font-family:monospace;">100%</div>
                    <div style="font-size:10px; color:#94a3b8; margin-top:2px;">Accredited Track</div>
                </div>
            </div>
        </div>

        {{-- 5. Mini Navigation Header Mockup --}}
        <div class="pv-card" style="padding:20px;">
            <div style="font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:12px;">
                <span>🧭 Header &amp; Navigation Bar</span>
            </div>

            <div style="background:rgba(15,23,42,0.95); border:1px solid rgba(255,255,255,0.1); border-radius:var(--pv-card-radius); padding:10px 16px; display:flex; align-items:center; justify-content:space-between;">
                {{-- Brand --}}
                <div style="display:flex; align-items:center; gap:8px;">
                    <div style="width:24px; height:24px; border-radius:6px; background:var(--pv-primary); display:flex; align-items:center; justify-content:center; font-weight:900; font-size:12px; color:#041017;">
                        E
                    </div>
                    <span style="font-weight:800; font-size:13px; color:#ffffff;">ELITE <span style="color:var(--pv-primary);">ACADEMY</span></span>
                </div>

                {{-- Nav Links --}}
                <div style="display:flex; gap:12px; font-size:11px; color:#94a3b8; font-weight:600;">
                    <span style="color:var(--pv-primary);">Home</span>
                    <span>Subjects</span>
                    <span>Teachers</span>
                </div>

                {{-- CTA Button --}}
                <button type="button" class="pv-btn-primary" style="padding:4px 12px; font-size:11px;">
                    Login →
                </button>
            </div>
        </div>

    </div>
</div>

<script>
(function() {
    'use strict';

    // ── Live sync with Filament Form inputs ─────────────────────────
    function syncThemePreview() {
        const primaryInput   = document.querySelector('[name*="theme_primary_color"]');
        const secondaryInput = document.querySelector('[name*="theme_secondary_color"]');
        const btnRadiusInput = document.querySelector('[name*="theme_btn_radius"]');
        const cardRadiusInput= document.querySelector('[name*="theme_card_radius"]');
        const badgeRadiusInput=document.querySelector('[name*="theme_badge_radius"]');
        const overlayInput   = document.querySelector('[name*="theme_slider_overlay_opacity"]');

        const primary   = primaryInput ? primaryInput.value : '#0d9488';
        const secondary = secondaryInput ? secondaryInput.value : '#6366f1';
        
        let btnRadiusVal = '9999px';
        if (btnRadiusInput) {
            const val = btnRadiusInput.value;
            if (val === 'none') btnRadiusVal = '0px';
            else if (val === 'sm') btnRadiusVal = '4px';
            else if (val === 'md') btnRadiusVal = '8px';
            else if (val === 'lg') btnRadiusVal = '12px';
            else btnRadiusVal = '9999px';
        }

        let cardRadiusVal = '16px';
        if (cardRadiusInput) {
            const val = cardRadiusInput.value;
            if (val === 'lg') cardRadiusVal = '8px';
            else if (val === 'xl') cardRadiusVal = '12px';
            else if (val === '2xl') cardRadiusVal = '16px';
            else if (val === '3xl') cardRadiusVal = '24px';
        }

        let badgeRadiusVal = '9999px';
        if (badgeRadiusInput) {
            const val = badgeRadiusInput.value;
            if (val === 'sm') badgeRadiusVal = '4px';
            else if (val === 'md') badgeRadiusVal = '8px';
            else badgeRadiusVal = '9999px';
        }

        const overlayVal = (overlayInput && overlayInput.value ? parseInt(overlayInput.value, 10) : 55) / 100;

        // Apply to CSS variables
        const root = document.documentElement;
        document.querySelectorAll('#theme-live-preview-container').forEach(container => {
            container.style.setProperty('--pv-primary', primary);
            container.style.setProperty('--pv-secondary', secondary);
            container.style.setProperty('--pv-btn-radius', btnRadiusVal);
            container.style.setProperty('--pv-card-radius', cardRadiusVal);
            container.style.setProperty('--pv-badge-radius', badgeRadiusVal);
        });

        const overlayEl = document.getElementById('pv-hero-overlay');
        if (overlayEl) overlayEl.style.background = 'rgba(2,6,23,' + overlayVal + ')';

        const btnRadiusLabel = document.getElementById('pv-btn-radius-label');
        if (btnRadiusLabel) btnRadiusLabel.textContent = btnRadiusVal;
    }

    // ── Presets Handler ───────────────────────────────────────────
    const presets = {
        cyber_teal: {
            theme_primary_color: '#0d9488',
            theme_secondary_color: '#06b6d4',
            theme_btn_radius: 'full',
            theme_card_radius: '2xl',
            theme_badge_radius: 'full',
            theme_slider_overlay_opacity: '55',
            theme_btn_style: 'gradient'
        },
        royal_indigo: {
            theme_primary_color: '#6366f1',
            theme_secondary_color: '#a855f7',
            theme_btn_radius: 'lg',
            theme_card_radius: '2xl',
            theme_badge_radius: 'md',
            theme_slider_overlay_opacity: '60',
            theme_btn_style: 'glow'
        },
        sunset_amber: {
            theme_primary_color: '#f59e0b',
            theme_secondary_color: '#ea580c',
            theme_btn_radius: 'full',
            theme_card_radius: '3xl',
            theme_badge_radius: 'full',
            theme_slider_overlay_opacity: '50',
            theme_btn_style: 'gradient'
        },
        crimson_flame: {
            theme_primary_color: '#f43f5e',
            theme_secondary_color: '#be123c',
            theme_btn_radius: 'md',
            theme_card_radius: 'xl',
            theme_badge_radius: 'sm',
            theme_slider_overlay_opacity: '65',
            theme_btn_style: 'solid'
        },
        emerald_growth: {
            theme_primary_color: '#10b981',
            theme_secondary_color: '#059669',
            theme_btn_radius: 'full',
            theme_card_radius: '2xl',
            theme_badge_radius: 'full',
            theme_slider_overlay_opacity: '50',
            theme_btn_style: 'gradient'
        }
    };

    window.__applyThemePreset = function(presetKey) {
        const p = presets[presetKey];
        if (!p) return;

        for (const [key, val] of Object.entries(p)) {
            const el = document.querySelector('[name*="' + key + '"]');
            if (el) {
                el.value = val;
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        setTimeout(syncThemePreview, 50);
    };

    // Attach listeners
    document.addEventListener('input', syncThemePreview);
    document.addEventListener('change', syncThemePreview);
    setTimeout(syncThemePreview, 200);
})();
</script>
