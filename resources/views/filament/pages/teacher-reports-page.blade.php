<x-filament-panels::page>
    <div class="rpt-wrapper">
        {{-- Comprehensive Embedded Styles for Reporting Views --}}
        <style>
            .rpt-wrapper {
                font-family: 'Cairo', sans-serif !important;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                animation: rptFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            @keyframes rptFadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* --- Control Topbar --- */
            .rpt-topbar {
                background: #FFFFFF;
                border: 1.5px solid #E2E8F0;
                border-radius: 1.25rem;
                padding: 1rem 1.25rem;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                transition: all 0.2s ease;
            }
            html.dark .rpt-topbar {
                background: #0F172A;
                border-color: rgba(51, 65, 85, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            }

            .rpt-search-input {
                width: 100%;
                background: #F8FAFC;
                border: 1.5px solid #CBD5E1;
                border-radius: 0.875rem;
                padding: 0.65rem 1rem 0.65rem 2.5rem;
                font-size: 0.85rem;
                font-weight: 600;
                color: #0F172A;
                outline: none;
                transition: all 0.2s ease;
            }
            html[dir="rtl"] .rpt-search-input {
                padding: 0.65rem 2.5rem 0.65rem 1rem;
            }
            html.dark .rpt-search-input {
                background: #1E293B;
                border-color: #334155;
                color: #F8FAFC;
            }
            .rpt-search-input:focus {
                border-color: #6366F1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
            }

            .rpt-select {
                background: #F8FAFC;
                border: 1.5px solid #CBD5E1;
                border-radius: 0.875rem;
                padding: 0.65rem 1rem;
                font-size: 0.85rem;
                font-weight: 700;
                color: #0F172A;
                cursor: pointer;
                outline: none;
                transition: all 0.2s ease;
                min-width: 260px;
            }
            html.dark .rpt-select {
                background: #1E293B;
                border-color: #334155;
                color: #F8FAFC;
            }
            .rpt-select:focus {
                border-color: #6366F1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
            }

            .rpt-btn {
                background: #F1F5F9;
                color: #334155;
                border: 1px solid #CBD5E1;
                border-radius: 0.875rem;
                padding: 0.65rem 1.25rem;
                font-size: 0.825rem;
                font-weight: 800;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            html.dark .rpt-btn {
                background: #1E293B;
                color: #E2E8F0;
                border-color: #334155;
            }
            .rpt-btn:hover {
                background: #E2E8F0;
                transform: translateY(-1px);
            }
            html.dark .rpt-btn:hover {
                background: #334155;
            }

            /* --- Hero Teacher Banner Card --- */
            .rpt-hero-teacher {
                background: linear-gradient(135deg, #312E81 0%, #4338CA 50%, #1E1B4B 100%);
                color: #FFFFFF;
                border-radius: 1.5rem;
                padding: 2rem;
                position: relative;
                overflow: hidden;
                box-shadow: 0 10px 25px -5px rgba(49, 46, 129, 0.4);
                border: 1px solid rgba(129, 140, 248, 0.3);
            }
            .rpt-hero-teacher::before {
                content: '';
                position: absolute;
                top: -40px;
                right: -40px;
                width: 240px;
                height: 240px;
                background: radial-gradient(circle, rgba(165, 180, 252, 0.25) 0%, rgba(255,255,255,0) 70%);
                border-radius: 50%;
                pointer-events: none;
            }

            .rpt-avatar {
                width: 5rem !important;
                height: 5rem !important;
                min-width: 5rem !important;
                min-height: 5rem !important;
                max-width: 5rem !important;
                max-height: 5rem !important;
                border-radius: 1.25rem !important;
                background: rgba(255, 255, 255, 0.15) !important;
                backdrop-filter: blur(10px) !important;
                border: 2px solid rgba(255, 255, 255, 0.3) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 2.25rem !important;
                font-weight: 800 !important;
                color: #E0E7FF !important;
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.1) !important;
                overflow: hidden !important;
                flex-shrink: 0 !important;
            }
            .rpt-avatar img {
                width: 100% !important;
                height: 100% !important;
                max-width: 5rem !important;
                max-height: 5rem !important;
                object-fit: cover !important;
                display: block !important;
                border-radius: inherit !important;
            }

            /* --- KPI Cards Grid --- */
            .rpt-grid-4 {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
                gap: 1.25rem;
            }

            .rpt-stat-card {
                background: #FFFFFF;
                border: 1.5px solid #E2E8F0;
                border-radius: 1.25rem;
                padding: 1.25rem 1.5rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            html.dark .rpt-stat-card {
                background: #0F172A;
                border-color: rgba(51, 65, 85, 0.8);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            }
            .rpt-stat-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px -3px rgba(0, 0, 0, 0.08);
            }
            html.dark .rpt-stat-card:hover {
                box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.4);
            }

            .rpt-stat-icon {
                width: 2.75rem;
                height: 2.75rem;
                border-radius: 0.875rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
            }

            /* --- Content Box & Tabs --- */
            .rpt-main-box {
                background: #FFFFFF;
                border: 1.5px solid #E2E8F0;
                border-radius: 1.5rem;
                overflow: hidden;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            }
            html.dark .rpt-main-box {
                background: #0F172A;
                border-color: rgba(51, 65, 85, 0.8);
                box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3);
            }

            .rpt-tabs-header {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.25rem 0;
                border-bottom: 1.5px solid #E2E8F0;
                overflow-x: auto;
                scrollbar-width: none;
            }
            html.dark .rpt-tabs-header {
                border-color: #1E293B;
            }
            .rpt-tabs-header::-webkit-scrollbar {
                display: none;
            }

            .rpt-tab-btn {
                padding: 0.875rem 1.15rem;
                font-size: 0.875rem;
                font-weight: 800;
                border: none;
                background: transparent;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                white-space: nowrap;
                border-bottom: 3px solid transparent;
                color: #64748B;
                transition: all 0.2s ease;
            }
            html.dark .rpt-tab-btn {
                color: #94A3B8;
            }
            .rpt-tab-btn:hover {
                color: #6366F1;
            }
            .rpt-tab-btn.active {
                color: #6366F1;
                border-bottom-color: #6366F1;
            }
            html.dark .rpt-tab-btn.active {
                color: #818CF8;
                border-bottom-color: #818CF8;
            }

            .rpt-tab-count {
                font-size: 0.75rem;
                font-weight: 800;
                padding: 0.15rem 0.6rem;
                border-radius: 9999px;
                background: #F1F5F9;
                color: #475569;
            }
            html.dark .rpt-tab-count {
                background: #1E293B;
                color: #94A3B8;
            }
            .rpt-tab-btn.active .rpt-tab-count {
                background: #EEF2FF;
                color: #4338CA;
            }
            html.dark .rpt-tab-btn.active .rpt-tab-count {
                background: #312E81;
                color: #C7D2FE;
            }

            /* --- Table Styles --- */
            .rpt-table-container {
                overflow-x: auto;
                border-radius: 1rem;
                border: 1px solid #E2E8F0;
            }
            html.dark .rpt-table-container {
                border-color: #1E293B;
            }

            .rpt-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.825rem;
                text-align: left;
            }
            html[dir="rtl"] .rpt-table {
                text-align: right;
            }
            .rpt-table th {
                background: #F8FAFC;
                padding: 0.85rem 1rem;
                font-weight: 800;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #475569;
                border-bottom: 1.5px solid #E2E8F0;
            }
            html.dark .rpt-table th {
                background: #1E293B;
                color: #94A3B8;
                border-bottom-color: #334155;
            }
            .rpt-table td {
                padding: 0.85rem 1rem;
                border-bottom: 1px solid #F1F5F9;
                color: #334155;
            }
            html.dark .rpt-table td {
                border-bottom-color: #1E293B;
                color: #CBD5E1;
            }
            .rpt-table tr:hover td {
                background: rgba(248, 250, 252, 0.6);
            }
            html.dark .rpt-table tr:hover td {
                background: rgba(30, 41, 59, 0.4);
            }

            /* --- Badges --- */
            .rpt-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.65rem;
                border-radius: 9999px;
                font-size: 0.725rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.025em;
            }
            .rpt-badge-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
            html.dark .rpt-badge-success { background: #052e16; color: #86efac; border-color: #14532d; }
            .rpt-badge-info { background: #E0F2FE; color: #075985; border: 1px solid #BAE6FD; }
            html.dark .rpt-badge-info { background: #082f49; color: #7dd3fc; border-color: #0369a1; }
            .rpt-badge-warning { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
            html.dark .rpt-badge-warning { background: #451a03; color: #fcd34d; border-color: #78350f; }
            .rpt-badge-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
            html.dark .rpt-badge-danger { background: #450a0a; color: #fca5a5; border-color: #7f1d1d; }

            /* ================================================================
               PROFESSIONAL PRINT STYLES — ELITE ACADEMY TEACHER REPORT
            ================================================================ */
            @media print {
                /* --- Hide ALL admin UI chrome --- */
                .no-print,
                header, aside, nav,
                .fi-sidebar, .fi-topbar, .fi-breadcrumbs,
                .fi-header, .fi-pagination,
                .rpt-topbar,
                .rpt-tabs-header {
                    display: none !important;
                }

                @page {
                    size: A4 portrait;
                    margin: 18mm 16mm 20mm 16mm;
                }

                html, body {
                    font-family: 'Cairo', sans-serif !important;
                    font-size: 11pt !important;
                    color: #0F172A !important;
                    background: #FFFFFF !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }

                .rpt-wrapper {
                    gap: 0 !important;
                    animation: none !important;
                    background: #FFFFFF !important;
                }

                /* --- Show ALL tab sections when printing --- */
                .rpt-print-section {
                    display: block !important;
                    page-break-inside: avoid;
                }

                /* --- Print Header --- */
                .rpt-print-header {
                    display: flex !important;
                    align-items: center;
                    justify-content: space-between;
                    border-bottom: 3px solid #4338CA;
                    padding-bottom: 12pt;
                    margin-bottom: 14pt;
                }
                .rpt-print-logo-text {
                    font-size: 20pt;
                    font-weight: 900;
                    color: #4338CA;
                    letter-spacing: -0.02em;
                }
                .rpt-print-meta {
                    font-size: 8.5pt;
                    color: #64748B;
                    text-align: right;
                }

                /* --- Hero Banner → print strip --- */
                .rpt-hero-teacher {
                    background: #312E81 !important;
                    color: #FFFFFF !important;
                    border-radius: 8pt !important;
                    padding: 12pt 14pt !important;
                    margin-bottom: 12pt !important;
                    box-shadow: none !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                    color-adjust: exact;
                }

                /* --- KPI Grid --- */
                .rpt-grid-4 {
                    display: grid !important;
                    grid-template-columns: repeat(4, 1fr) !important;
                    gap: 8pt !important;
                    margin-bottom: 12pt !important;
                }
                .rpt-stat-card {
                    border: 1px solid #E2E8F0 !important;
                    border-radius: 6pt !important;
                    padding: 8pt 10pt !important;
                    box-shadow: none !important;
                    background: #F8FAFC !important;
                    page-break-inside: avoid;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* --- Main box --- */
                .rpt-main-box {
                    border: none !important;
                    box-shadow: none !important;
                    border-radius: 0 !important;
                }

                /* --- Section headings in print --- */
                .rpt-section-print-title {
                    display: flex !important;
                    align-items: center;
                    gap: 6pt;
                    font-size: 12pt;
                    font-weight: 900;
                    color: #0F172A;
                    border-left: 4pt solid #4338CA;
                    padding-left: 8pt;
                    margin: 14pt 0 8pt 0;
                    page-break-after: avoid;
                }

                /* --- Tables --- */
                .rpt-table {
                    font-size: 8.5pt !important;
                    border-collapse: collapse !important;
                    width: 100% !important;
                }
                .rpt-table th {
                    background: #F1F5F9 !important;
                    color: #0F172A !important;
                    padding: 5pt 7pt !important;
                    font-size: 8pt !important;
                    border-bottom: 2px solid #E2E8F0 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .rpt-table td {
                    padding: 5pt 7pt !important;
                    border-bottom: 1px solid #F1F5F9 !important;
                    color: #1E293B !important;
                }
                .rpt-table-container {
                    border: 1px solid #E2E8F0 !important;
                    border-radius: 4pt !important;
                    overflow: visible !important;
                }

                /* --- Course cards --- */
                .rpt-course-card {
                    border: 1px solid #E2E8F0 !important;
                    background: #F8FAFC !important;
                    border-radius: 5pt !important;
                    padding: 8pt 10pt !important;
                    margin-bottom: 6pt !important;
                    box-shadow: none !important;
                    page-break-inside: avoid;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* --- Note & exception cards --- */
                .rpt-note-card {
                    border: 1px solid #E2E8F0 !important;
                    background: #F8FAFC !important;
                    border-radius: 5pt !important;
                    padding: 8pt 10pt !important;
                    margin-bottom: 5pt !important;
                    box-shadow: none !important;
                    page-break-inside: avoid;
                }

                /* --- Badges --- */
                .rpt-badge, .rpt-badge-success, .rpt-badge-info, .rpt-badge-warning, .rpt-badge-danger {
                    border: 1px solid #E2E8F0 !important;
                    padding: 1pt 5pt !important;
                    border-radius: 4pt !important;
                    font-size: 7.5pt !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .rpt-badge-success { background: #DCFCE7 !important; color: #15803D !important; }
                .rpt-badge-info    { background: #E0F2FE !important; color: #075985 !important; }
                .rpt-badge-warning { background: #FEF3C7 !important; color: #92400E !important; }
                .rpt-badge-danger  { background: #FEE2E2 !important; color: #991B1B !important; }

                /* --- Avatar --- */
                .rpt-avatar {
                    background: rgba(255,255,255,0.25) !important;
                    color: #FFFFFF !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* --- Print footer --- */
                .rpt-print-footer {
                    display: block !important;
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    text-align: center;
                    font-size: 7.5pt;
                    color: #94A3B8;
                    border-top: 1px solid #E2E8F0;
                    padding-top: 5pt;
                }
            }
        </style>

        {{-- Top Control Bar: Search & Quick Picker --}}
        <div class="rpt-topbar">
            <div style="flex: 1; display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                <div style="position: relative; flex: 1; min-width: 240px;">
                    <span style="position: absolute; top: 50%; transform: translateY(-50%); left: 0.85rem; color: #94A3B8; pointer-events: none;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input 
                        wire:model.live.debounce.300ms="searchQuery"
                        type="text" 
                        placeholder="{{ __('Search teachers by name, specialization, email, or phone...') }}"
                        class="rpt-search-input"
                    />
                </div>
                <div>
                    <select 
                        wire:change="selectTeacher($event.target.value)"
                        class="rpt-select"
                    >
                        @forelse($this->teachersList as $tch)
                            <option value="{{ $tch->id }}" {{ $this->selectedTeacherId === $tch->id ? 'selected' : '' }}>
                                {{ $tch->user?->name ?? __('Teacher') }} ({{ $tch->title ?: ($tch->specialization ?: __('Instructor')) }})
                            </option>
                        @empty
                            <option disabled>{{ __('No teachers found') }}</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <button onclick="elitePrintReport()" type="button" class="rpt-btn">
                    <i class="fa-solid fa-print"></i>
                    <span>{{ __('Print Report') }}</span>
                </button>
            </div>
        </div>

        @if($this->selectedTeacher)
            @php
                $teacher = $this->selectedTeacher;
                $user = $teacher->user;
                $metrics = $this->teacherMetrics;
            @endphp

            {{-- Teacher Profile Banner Card --}}
            <div class="rpt-hero-teacher">
                <div style="position: relative; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                        <div class="rpt-avatar" style="width: 5rem; height: 5rem; min-width: 5rem; min-height: 5rem; max-width: 5rem; max-height: 5rem; border-radius: 1.25rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.25); border: 2px solid rgba(255,255,255,0.35);">
                            <img src="{{ $teacher->photo_url }}" alt="{{ $user?->name }}" style="width: 5rem; height: 5rem; max-width: 5rem; max-height: 5rem; object-fit: cover; display: block; border-radius: inherit;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user?->name ?? 'Teacher') }}&background=4F46E5&color=fff&size=256&bold=true';" />
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                            <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: #FFFFFF;">
                                    {{ $user?->name ?? __('Teacher Name') }}
                                </h2>
                                <span style="background: rgba(255, 255, 255, 0.2); padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; border: 1px solid rgba(255, 255, 255, 0.3);">
                                    <i class="fa-solid fa-graduation-cap" style="margin-inline-end: 0.25rem;"></i>
                                    {{ $teacher->title ?: __('Faculty Instructor') }}
                                </span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; font-size: 0.8rem; color: #E0E7FF;">
                                @if($user?->email)
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-envelope" style="color: #A5B4FC;"></i>
                                        {{ $user->email }}
                                    </span>
                                @endif
                                @if($user?->phone)
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-phone" style="color: #A5B4FC;"></i>
                                        {{ $user->phone }}
                                    </span>
                                @endif
                                @if($teacher->specialization)
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-atom" style="color: #A5B4FC;"></i>
                                        {{ $teacher->specialization }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Rating & Experience Badges --}}
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                        <div style="background: rgba(251, 191, 36, 0.25); border: 1px solid rgba(251, 191, 36, 0.4); color: #FEF08A; padding: 0.4rem 0.85rem; border-radius: 0.75rem; font-size: 0.825rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-star" style="color: #FBBF24;"></i>
                            <span>{{ number_format($metrics['average_rating'], 1) }} / 5.0</span>
                            <span style="color: #FEF9C3; font-size: 0.75rem; opacity: 0.85;">({{ $metrics['reviews_count'] }} {{ __('reviews') }})</span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF; padding: 0.4rem 0.85rem; border-radius: 0.75rem; font-size: 0.825rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-business-time" style="color: #A5B4FC;"></i>
                            <span>{{ $teacher->years_experience ?: 5 }}+ {{ __('Years Teaching Experience') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Metric KPI Overview Cards --}}
            <div class="rpt-grid-4">
                {{-- Courses Taught --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;">{{ __('Courses Taught') }}</span>
                        <div class="rpt-stat-icon" style="background: #EEF2FF; color: #4F46E5;">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.85rem; font-weight: 900; color: #1E1B4B; line-height: 1;">
                        <span style="color: inherit;" class="dark:text-white">{{ $metrics['courses_count'] }}</span>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        <i class="fa-solid fa-layer-group" style="color: #6366F1;"></i>
                        {{ __('Active published courses') }}
                    </p>
                </div>

                {{-- Enrolled Students --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;">{{ __('Enrolled Students') }}</span>
                        <div class="rpt-stat-icon" style="background: #CCFBF1; color: #0D9488;">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.85rem; font-weight: 900; color: #0D9488; line-height: 1;">
                        {{ $metrics['total_students_enrolled'] }}
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        {{ __('Unique active learners') }}
                    </p>
                </div>

                {{-- Live Sessions --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;">{{ __('Live Sessions') }}</span>
                        <div class="rpt-stat-icon" style="background: #FFE4E6; color: #E11D48;">
                            <i class="fa-solid fa-video"></i>
                        </div>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 0.35rem;">
                        <span style="font-size: 1.85rem; font-weight: 900; color: #E11D48; line-height: 1;">
                            {{ $metrics['completed_sessions_count'] }}
                        </span>
                        <span style="font-size: 0.8rem; font-weight: 600; color: #94A3B8;">/ {{ $metrics['live_sessions_count'] }} {{ __('Total') }}</span>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        {{ __('Taught') }}: <strong style="color: #0F172A;" class="dark:text-slate-200">{{ $metrics['total_hours_taught'] }}</strong> {{ __('hours') }}
                    </p>
                </div>

                {{-- Assignments & Grading --}}
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;">{{ __('Assignments & Grading') }}</span>
                        <div class="rpt-stat-icon" style="background: #F3E8FF; color: #9333EA;">
                            <i class="fa-solid fa-check-to-slot"></i>
                        </div>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 0.35rem;">
                        <span style="font-size: 1.85rem; font-weight: 900; color: #9333EA; line-height: 1;">
                            {{ $metrics['graded_submissions_count'] }}
                        </span>
                        <span style="font-size: 0.8rem; font-weight: 600; color: #94A3B8;">{{ __('Graded') }}</span>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        {{ __('Created assignments') }}: <strong style="color: #0F172A;" class="dark:text-slate-200">{{ $metrics['assignments_count'] }}</strong>
                    </p>
                </div>
            </div>

            {{-- Main Tabbed Detail Box --}}
            <div class="rpt-main-box">
                {{-- Tabs Navigation --}}
                <div class="rpt-tabs-header">
                    @php
                        $tabs = [
                            'overview'    => ['icon' => 'fa-book-open',         'label' => __('Assigned Courses'),            'count' => $this->courses->count()],
                            'sessions'    => ['icon' => 'fa-video',             'label' => __('Conducted & Live Sessions'),    'count' => $this->liveSessions->count()],
                            'students'    => ['icon' => 'fa-user-group',        'label' => __('Enrolled Students Roster'),    'count' => $this->enrolledStudents->count()],
                            'assignments' => ['icon' => 'fa-file-signature',    'label' => __('Assignments & Grading'),       'count' => $this->assignments->count()],
                            'notes'       => ['icon' => 'fa-clipboard-user',    'label' => __('Authored Educational Notes'),  'count' => $this->notes->count()],
                            'exceptions'  => ['icon' => 'fa-envelope-open-text','label' => __('Student Exception Requests'),   'count' => $this->exceptions->count()],
                        ];
                    @endphp

                    @foreach($tabs as $tabKey => $tab)
                        <button 
                            wire:click="setTab('{{ $tabKey }}')"
                            type="button" 
                            class="rpt-tab-btn {{ $activeTab === $tabKey ? 'active' : '' }}"
                        >
                            <i class="fa-solid {{ $tab['icon'] }}"></i>
                            <span>{{ $tab['label'] }}</span>
                            <span class="rpt-tab-count">{{ $tab['count'] }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Tab Content Display --}}
                <div style="padding: 1.5rem;">

                    {{-- Print-only report header --}}
                    <div class="rpt-print-header" style="display: none;">
                        <div>
                            <div class="rpt-print-logo-text">&#9670; Elite Academy</div>
                            <div style="font-size: 9pt; color: #64748B; margin-top: 2pt;">{{ __('Teacher Comprehensive Faculty Report') }}</div>
                        </div>
                        <div class="rpt-print-meta">
                            <div>{{ __('Teacher') }}: <strong>{{ $user?->name }}</strong></div>
                            <div>{{ __('Report Date') }}: {{ now()->format('Y-m-d H:i') }}</div>
                            <div>{{ __('Specialization') }}: {{ $teacher->specialization ?? $teacher->title ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- 1. Assigned Courses Tab --}}
                    <div class="rpt-print-section" id="section-overview" style="{{ $activeTab !== 'overview' ? 'display:none;' : '' }}">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; color:#6366F1; text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-book-bookmark" style="color: #6366F1;"></i>
                                {{ __('Assigned Courses & Curriculum Delivery') }}
                            </h3>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                                @forelse($this->courses as $c)
                                    <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 1rem; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;" class="dark:bg-slate-950/60 dark:border-slate-800">
                                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                                            <div>
                                                <h4 style="font-size: 0.95rem; font-weight: 800; dark:color:white color:#0F172A margin: 0;" class="dark:text-white">{{ $c->title }}</h4>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.35rem;">
                                                    <span class="rpt-badge rpt-badge-info">{{ $c->subject?->name ?? __('General') }}</span>
                                                    <span style="font-size: 0.75rem; color: #94A3B8;">{{ $c->gradeLevel?->name ?? __('All Grades') }}</span>
                                                </div>
                                            </div>
                                            <span class="rpt-badge {{ $c->is_active ? 'rpt-badge-success' : 'rpt-badge-danger' }}">
                                                {{ $c->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </div>

                                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center; font-size: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #E2E8F0;" class="dark:border-slate-800">
                                            <div style="background: #FFFFFF; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                                                <div style="color: #94A3B8; font-size: 0.7rem;">{{ __('Sessions') }}</div>
                                                <div style="font-weight: 800; color: #0D9488; margin-top: 0.15rem;">{{ $c->sessions_count ?: $c->sessions->count() }}</div>
                                            </div>
                                            <div style="background: #FFFFFF; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                                                <div style="color: #94A3B8; font-size: 0.7rem;">{{ __('Students') }}</div>
                                                <div style="font-weight: 800; font-weight: 800; dark:color:white color:#0F172A margin-top: 0.15rem;">{{ $c->enrollments->count() }}</div>
                                            </div>
                                            <div style="background: #FFFFFF; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                                                <div style="color: #94A3B8; font-size: 0.7rem;">{{ __('Price') }}</div>
                                                <div style="font-weight: 800; dark:color:white color:#0F172A margin-top: 0.15rem;" class="dark:text-white">{{ $c->price ? $c->price . ' EGP' : __('Standard') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div style="grid-column: 1 / -1; padding: 2.5rem; text-align: center; color: #94A3B8;">
                                        <i class="fa-solid fa-book-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                        <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ __('No courses currently assigned to this teacher.') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>{{-- end section-overview --}}

                    {{-- 2. Live Sessions Tab --}}
                    <div class="rpt-print-section" id="section-sessions" style="{{ $activeTab !== 'sessions' ? 'display:none;' : '' }}">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; dark:color:white color:#0F172A text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-video" style="color: #E11D48;"></i>
                                {{ __('Conducted & Scheduled Live Sessions') }}
                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Session Title') }}</th>
                                            <th>{{ __('Course / Subject') }}</th>
                                            <th>{{ __('Target Student') }}</th>
                                            <th>{{ __('Scheduled Date & Time') }}</th>
                                            <th>{{ __('Duration & Platform') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($this->liveSessions as $sess)
                                            <tr>
                                                <td style="font-weight: 800; dark:color:white color:#0F172A" class="dark:text-white">
                                                    {{ $sess->title }}
                                                </td>
                                                <td>{{ $sess->course?->title ?? $sess->subject?->name ?? '-' }}</td>
                                                <td>
                                                    @if($sess->studentUser)
                                                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                                                            <img src="{{ $sess->studentUser->avatar_url }}" alt="{{ $sess->studentUser->name }}" style="width: 2rem; height: 2rem; min-width: 2rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid #0D9488;" />
                                                            <div>
                                                                <div style="font-weight: 700; color: #0F172A; line-height: 1.2;" class="dark:text-white">{{ $sess->studentUser->name }}</div>
                                                                <div style="font-size: 0.7rem; color: #94A3B8;">{{ $sess->studentUser->email }}</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="rpt-badge rpt-badge-info">{{ __('Cohort Session') }}</span>
                                                    @endif
                                                </td>
                                                <td style="font-family: monospace;">
                                                    {{ $sess->scheduled_at ? $sess->scheduled_at->format('Y-m-d H:i') : '-' }}
                                                </td>
                                                <td>
                                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                                        <i class="fa-solid fa-clock" style="color: #94A3B8;"></i>
                                                        {{ $sess->duration_minutes }}m ({{ __(ucfirst($sess->meeting_platform ?: 'online')) }})
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="rpt-badge {{ $sess->status === 'completed' ? 'rpt-badge-success' : ($sess->status === 'scheduled' ? 'rpt-badge-info' : 'rpt-badge-danger') }}">
                                                        {{ __(ucfirst($sess->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                                    {{ __('No live sessions recorded for this instructor.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end section-sessions --}}

                    {{-- 3. Enrolled Students Roster Tab --}}
                    <div class="rpt-print-section" id="section-students" style="{{ $activeTab !== 'students' ? 'display:none;' : '' }}">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; dark:color:white color:#0F172A text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-user-group" style="color: #0D9488;"></i>
                                {{ __('Enrolled Students Roster') }}
                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Student') }}</th>
                                            <th>{{ __('Contact Info') }}</th>
                                            <th>{{ __('Grade Level') }}</th>
                                            <th>{{ __('Enrolled Course') }}</th>
                                            <th>{{ __('Enrollment Date') }}</th>
                                            <th>{{ __('Progress') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($this->enrolledStudents as $enr)
                                            <tr>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <img src="{{ $enr->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=0D9488&color=fff' }}" alt="{{ $enr->studentUser?->name }}" style="width: 2.35rem; height: 2.35rem; min-width: 2.35rem; border-radius: 9999px; object-fit: cover; border: 2px solid #0D9488; flex-shrink: 0;" />
                                                        <div>
                                                            <div style="font-weight: 800; dark:color:white color:#0F172A line-height: 1.2;" class="dark:text-white">
                                                                {{ $enr->studentUser?->name ?? __('Student') }}
                                                            </div>
                                                            <div style="font-size: 0.7rem; color: #64748B;" class="dark:text-slate-400">
                                                                {{ $enr->studentUser?->studentProfile?->school_name ?: __('Elite Academy') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="font-size: 0.75rem;">
                                                    <div>{{ $enr->studentUser?->email }}</div>
                                                    <div style="color: #94A3B8; font-family: monospace;">{{ $enr->studentUser?->phone }}</div>
                                                </td>
                                                <td>
                                                    {{ $enr->studentUser?->studentProfile?->gradeLevel?->name ?? '-' }}
                                                </td>
                                                <td style="font-weight: 700; color: #6366F1;" class="dark:text-indigo-400">
                                                    {{ $enr->course?->title ?? '-' }}
                                                </td>
                                                <td style="font-family: monospace;">
                                                    {{ $enr->enrolled_at ? $enr->enrolled_at->format('Y-m-d') : '-' }}
                                                </td>
                                                <td>
                                                    <span style="font-weight: 800; color: #0D9488;">{{ $enr->progress_percent }}%</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                                    {{ __('No students currently enrolled in this teacher\'s courses.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end section-students --}}

                    {{-- 4. Assignments & Grading Tab --}}
                    <div class="rpt-print-section" id="section-assignments" style="{{ $activeTab !== 'assignments' ? 'display:none;' : '' }}">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; dark:color:white color:#0F172A text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-file-pen" style="color: #9333EA;"></i>
                                {{ __('Assignments Created & Grading Log') }}
                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Assignment Title') }}</th>
                                            <th>{{ __('Course') }}</th>
                                            <th>{{ __('Passing Grade') }}</th>
                                            <th>{{ __('Submissions Received') }}</th>
                                            <th>{{ __('Graded Submissions') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($this->assignments as $a)
                                            @php
                                                $gradedCount = $a->submissions->whereNotNull('grade')->count();
                                                $totalSubs = $a->submissions->count();
                                            @endphp
                                            <tr>
                                                <td style="font-weight: 800; dark:color:white color:#0F172A" class="dark:text-white">
                                                    {{ $a->title }}
                                                </td>
                                                <td>{{ $a->course?->title ?? '-' }}</td>
                                                <td style="font-family: monospace; font-weight: 800;">
                                                    {{ $a->passing_grade ?: 60 }}%
                                                </td>
                                                <td style="font-weight: 700; color: #9333EA;">
                                                    {{ $totalSubs }} {{ __('Submissions') }}
                                                </td>
                                                <td>
                                                    <span class="rpt-badge {{ $gradedCount === $totalSubs && $totalSubs > 0 ? 'rpt-badge-success' : 'rpt-badge-warning' }}">
                                                        {{ $gradedCount }} / {{ $totalSubs }} {{ __('Graded') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="rpt-badge {{ $a->status === 'published' ? 'rpt-badge-success' : 'rpt-badge-info' }}">
                                                        {{ __(ucfirst($a->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                                    {{ __('No assignments created by this teacher.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>{{-- end section-assignments --}}

                    {{-- 5. Educational Notes Authored Tab --}}
                    <div class="rpt-print-section" id="section-notes" style="{{ $activeTab !== 'notes' ? 'display:none;' : '' }}">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; dark:color:white color:#0F172A text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-clipboard-user" style="color: #0284C7;"></i>
                                {{ __('Educational Notes Authored for Students') }}
                            </h3>

                            @forelse($this->notes as $note)
                                <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 1rem; padding: 1.15rem; display: flex; flex-direction: column; gap: 0.65rem;" class="dark:bg-slate-950/60 dark:border-slate-800">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <img src="{{ $note->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=0284C7&color=fff' }}" alt="{{ $note->studentUser?->name }}" style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid #0284C7; flex-shrink: 0;" />
                                            <div>
                                                <h4 style="font-size: 0.85rem; font-weight: 800; dark:color:white color:#0F172A margin: 0;" class="dark:text-white">
                                                    {{ __('For Student') }}: {{ $note->studentUser?->name ?? __('Student') }}
                                                </h4>
                                                <div style="font-size: 0.7rem; color: #94A3B8;">{{ $note->studentUser?->email }}</div>
                                            </div>
                                        </div>
                                        <span style="font-size: 0.75rem; font-family: monospace; color: #94A3B8;">{{ $note->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <p style="font-size: 0.825rem; color: #475569; margin: 0; line-height: 1.6; padding-inline-start: 3rem;" class="dark:text-slate-300">
                                        {{ $note->note }}
                                    </p>
                                </div>
                            @empty
                                <div style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                    <i class="fa-solid fa-clipboard-check" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                    <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ __('No educational notes authored by this teacher.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>{{-- end section-notes --}}

                    {{-- 6. Exception Requests Handled Tab --}}
                    <div class="rpt-print-section" id="section-exceptions" style="{{ $activeTab !== 'exceptions' ? 'display:none;' : '' }}">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; dark:color:white color:#0F172A text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-envelope-open-text" style="color: #9333EA;"></i>
                                {{ __('Student Absence & Exception Requests Handled') }}
                            </h3>

                            @forelse($this->exceptions as $exc)
                                <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 1rem; padding: 1.15rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;" class="dark:bg-slate-950/60 dark:border-slate-800">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="{{ $exc->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=9333EA&color=fff' }}" alt="{{ $exc->studentUser?->name }}" style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid #9333EA; flex-shrink: 0;" />
                                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                <h4 style="font-size: 0.85rem; font-weight: 800; dark:color:white color:#0F172A margin: 0;" class="dark:text-white">
                                                    {{ $exc->studentUser?->name ?? __('Student') }}
                                                </h4>
                                                <span class="rpt-badge rpt-badge-info">{{ $exc->liveSession?->title ?? $exc->course?->title ?? __('Session Request') }}</span>
                                                <span class="rpt-badge {{ $exc->status === 'approved' ? 'rpt-badge-success' : ($exc->status === 'rejected' ? 'rpt-badge-danger' : 'rpt-badge-warning') }}">
                                                    {{ __(ucfirst($exc->status)) }}
                                                </span>
                                            </div>
                                            <p style="font-size: 0.775rem; color: #64748B; margin: 0;" class="dark:text-slate-400">
                                                {{ __('Reason') }}: {{ $exc->reason ?: '-' }}
                                            </p>
                                        </div>
                                    </div>
                                    <span style="font-size: 0.75rem; font-family: monospace; color: #94A3B8;">{{ $exc->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            @empty
                                <div style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                    <i class="fa-solid fa-envelope-circle-check" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                    <p style="font-size: 0.875rem; font-weight: 600; margin: 0;">{{ __('No exception requests for this teacher\'s classes.') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>{{-- end section-exceptions --}}
                </div>
            </div>
        @else
            <div style="padding: 3.5rem 1.5rem; text-align: center; background: #FFFFFF; border-radius: 1.5rem; border: 1.5px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; color: #94A3B8; margin-bottom: 0.75rem; display: block;"></i>
                <h3 style="font-size: 1rem; font-weight: 800; dark:color:white color:#0F172A margin: 0;" class="dark:text-white">{{ __('No teacher selected') }}</h3>
                <p style="font-size: 0.825rem; color: #64748B; margin: 0.35rem 0 0 0;">{{ __('Please pick a teacher from the dropdown above to view their comprehensive faculty report.') }}</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
