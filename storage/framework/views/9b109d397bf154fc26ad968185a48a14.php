<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="rpt-wrapper">
        
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

        
        <div class="rpt-topbar">
            <div style="flex: 1; display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                <div style="position: relative; flex: 1; min-width: 240px;">
                    <span style="position: absolute; top: 50%; transform: translateY(-50%); left: 0.85rem; color: #94A3B8; pointer-events: none;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input 
                        wire:model.live.debounce.300ms="searchQuery"
                        type="text" 
                        placeholder="<?php echo e(__('Search teachers by name, specialization, email, or phone...')); ?>"
                        class="rpt-search-input"
                    />
                </div>
                <div>
                    <select 
                        wire:change="selectTeacher($event.target.value)"
                        class="rpt-select"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->teachersList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($tch->id); ?>" <?php echo e($this->selectedTeacherId === $tch->id ? 'selected' : ''); ?>>
                                <?php echo e($tch->user?->name ?? __('Teacher')); ?> (<?php echo e($tch->title ?: ($tch->specialization ?: __('Instructor'))); ?>)
                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <option disabled><?php echo e(__('No teachers found')); ?></option>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <button onclick="elitePrintReport()" type="button" class="rpt-btn">
                    <i class="fa-solid fa-print"></i>
                    <span><?php echo e(__('Print Report')); ?></span>
                </button>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->selectedTeacher): ?>
            <?php
                $teacher = $this->selectedTeacher;
                $user = $teacher->user;
                $metrics = $this->teacherMetrics;
            ?>

            
            <div class="rpt-hero-teacher">
                <div style="position: relative; z-index: 10; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                        <div class="rpt-avatar" style="width: 5rem; height: 5rem; min-width: 5rem; min-height: 5rem; max-width: 5rem; max-height: 5rem; border-radius: 1.25rem; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.25); border: 2px solid rgba(255,255,255,0.35);">
                            <img src="<?php echo e($teacher->photo_url); ?>" alt="<?php echo e($user?->name); ?>" style="width: 5rem; height: 5rem; max-width: 5rem; max-height: 5rem; object-fit: cover; display: block; border-radius: inherit;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?php echo e(urlencode($user?->name ?? 'Teacher')); ?>&background=4F46E5&color=fff&size=256&bold=true';" />
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                            <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0; color: #FFFFFF;">
                                    <?php echo e($user?->name ?? __('Teacher Name')); ?>

                                </h2>
                                <span style="background: rgba(255, 255, 255, 0.2); padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 800; border: 1px solid rgba(255, 255, 255, 0.3);">
                                    <i class="fa-solid fa-graduation-cap" style="margin-inline-end: 0.25rem;"></i>
                                    <?php echo e($teacher->title ?: __('Faculty Instructor')); ?>

                                </span>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem; font-size: 0.8rem; color: #E0E7FF;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user?->email): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-envelope" style="color: #A5B4FC;"></i>
                                        <?php echo e($user->email); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user?->phone): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-phone" style="color: #A5B4FC;"></i>
                                        <?php echo e($user->phone); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($teacher->specialization): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <i class="fa-solid fa-atom" style="color: #A5B4FC;"></i>
                                        <?php echo e($teacher->specialization); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                        <div style="background: rgba(251, 191, 36, 0.25); border: 1px solid rgba(251, 191, 36, 0.4); color: #FEF08A; padding: 0.4rem 0.85rem; border-radius: 0.75rem; font-size: 0.825rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-star" style="color: #FBBF24;"></i>
                            <span><?php echo e(number_format($metrics['average_rating'], 1)); ?> / 5.0</span>
                            <span style="color: #FEF9C3; font-size: 0.75rem; opacity: 0.85;">(<?php echo e($metrics['reviews_count']); ?> <?php echo e(__('reviews')); ?>)</span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF; padding: 0.4rem 0.85rem; border-radius: 0.75rem; font-size: 0.825rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-business-time" style="color: #A5B4FC;"></i>
                            <span><?php echo e($teacher->years_experience ?: 5); ?>+ <?php echo e(__('Years Teaching Experience')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="rpt-grid-4">
                
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;"><?php echo e(__('Courses Taught')); ?></span>
                        <div class="rpt-stat-icon" style="background: #EEF2FF; color: #4F46E5;">
                            <i class="fa-solid fa-book-bookmark"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.85rem; font-weight: 900; color: #1E1B4B; line-height: 1;">
                        <span style="color: inherit;" class="dark:text-white"><?php echo e($metrics['courses_count']); ?></span>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        <i class="fa-solid fa-layer-group" style="color: #6366F1;"></i>
                        <?php echo e(__('Active published courses')); ?>

                    </p>
                </div>

                
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;"><?php echo e(__('Enrolled Students')); ?></span>
                        <div class="rpt-stat-icon" style="background: #CCFBF1; color: #0D9488;">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                    <div style="font-size: 1.85rem; font-weight: 900; color: #0D9488; line-height: 1;">
                        <?php echo e($metrics['total_students_enrolled']); ?>

                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        <?php echo e(__('Unique active learners')); ?>

                    </p>
                </div>

                
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;"><?php echo e(__('Live Sessions')); ?></span>
                        <div class="rpt-stat-icon" style="background: #FFE4E6; color: #E11D48;">
                            <i class="fa-solid fa-video"></i>
                        </div>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 0.35rem;">
                        <span style="font-size: 1.85rem; font-weight: 900; color: #E11D48; line-height: 1;">
                            <?php echo e($metrics['completed_sessions_count']); ?>

                        </span>
                        <span style="font-size: 0.8rem; font-weight: 600; color: #94A3B8;">/ <?php echo e($metrics['live_sessions_count']); ?> <?php echo e(__('Total')); ?></span>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        <?php echo e(__('Taught')); ?>: <strong style="color: #0F172A;" class="dark:text-slate-200"><?php echo e($metrics['total_hours_taught']); ?></strong> <?php echo e(__('hours')); ?>

                    </p>
                </div>

                
                <div class="rpt-stat-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 800; color: #64748B; text-transform: uppercase;"><?php echo e(__('Assignments & Grading')); ?></span>
                        <div class="rpt-stat-icon" style="background: #F3E8FF; color: #9333EA;">
                            <i class="fa-solid fa-check-to-slot"></i>
                        </div>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 0.35rem;">
                        <span style="font-size: 1.85rem; font-weight: 900; color: #9333EA; line-height: 1;">
                            <?php echo e($metrics['graded_submissions_count']); ?>

                        </span>
                        <span style="font-size: 0.8rem; font-weight: 600; color: #94A3B8;"><?php echo e(__('Graded')); ?></span>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.5rem 0 0 0;">
                        <?php echo e(__('Created assignments')); ?>: <strong style="color: #0F172A;" class="dark:text-slate-200"><?php echo e($metrics['assignments_count']); ?></strong>
                    </p>
                </div>
            </div>

            
            <div class="rpt-main-box">
                
                <div class="rpt-tabs-header">
                    <?php
                        $tabs = [
                            'overview'    => ['icon' => 'fa-book-open',         'label' => __('Assigned Courses'),            'count' => $this->courses->count()],
                            'sessions'    => ['icon' => 'fa-video',             'label' => __('Conducted & Live Sessions'),    'count' => $this->liveSessions->count()],
                            'students'    => ['icon' => 'fa-user-group',        'label' => __('Enrolled Students Roster'),    'count' => $this->enrolledStudents->count()],
                            'assignments' => ['icon' => 'fa-file-signature',    'label' => __('Assignments & Grading'),       'count' => $this->assignments->count()],
                            'notes'       => ['icon' => 'fa-clipboard-user',    'label' => __('Authored Educational Notes'),  'count' => $this->notes->count()],
                            'exceptions'  => ['icon' => 'fa-envelope-open-text','label' => __('Student Exception Requests'),   'count' => $this->exceptions->count()],
                        ];
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button 
                            wire:click="setTab('<?php echo e($tabKey); ?>')"
                            type="button" 
                            class="rpt-tab-btn <?php echo e($activeTab === $tabKey ? 'active' : ''); ?>"
                        >
                            <i class="fa-solid <?php echo e($tab['icon']); ?>"></i>
                            <span><?php echo e($tab['label']); ?></span>
                            <span class="rpt-tab-count"><?php echo e($tab['count']); ?></span>
                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <div style="padding: 1.5rem;">

                    
                    <div class="rpt-print-header" style="display: none;">
                        <div>
                            <div class="rpt-print-logo-text">&#9670; Elite Academy</div>
                            <div style="font-size: 9pt; color: #64748B; margin-top: 2pt;"><?php echo e(__('Teacher Comprehensive Faculty Report')); ?></div>
                        </div>
                        <div class="rpt-print-meta">
                            <div><?php echo e(__('Teacher')); ?>: <strong><?php echo e($user?->name); ?></strong></div>
                            <div><?php echo e(__('Report Date')); ?>: <?php echo e(now()->format('Y-m-d H:i')); ?></div>
                            <div><?php echo e(__('Specialization')); ?>: <?php echo e($teacher->specialization ?? $teacher->title ?? '-'); ?></div>
                        </div>
                    </div>

                    
                    <div class="rpt-print-section" id="section-overview" style="<?php echo e($activeTab !== 'overview' ? 'display:none;' : ''); ?>">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-book-bookmark" style="color: #6366F1;"></i>
                                <?php echo e(__('Assigned Courses & Curriculum Delivery')); ?>

                            </h3>

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 1rem; padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;" class="dark:bg-slate-950/60 dark:border-slate-800">
                                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                                            <div>
                                                <h4 style="font-size: 0.95rem; font-weight: 800; color: #0F172A; margin: 0;" class="dark:text-white"><?php echo e($c->title); ?></h4>
                                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.35rem;">
                                                    <span class="rpt-badge rpt-badge-info"><?php echo e($c->subject?->name ?? __('General')); ?></span>
                                                    <span style="font-size: 0.75rem; color: #94A3B8;"><?php echo e($c->gradeLevel?->name ?? __('All Grades')); ?></span>
                                                </div>
                                            </div>
                                            <span class="rpt-badge <?php echo e($c->is_active ? 'rpt-badge-success' : 'rpt-badge-danger'); ?>">
                                                <?php echo e($c->is_active ? __('Active') : __('Inactive')); ?>

                                            </span>
                                        </div>

                                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center; font-size: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #E2E8F0;" class="dark:border-slate-800">
                                            <div style="background: #FFFFFF; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                                                <div style="color: #94A3B8; font-size: 0.7rem;"><?php echo e(__('Sessions')); ?></div>
                                                <div style="font-weight: 800; color: #0F172A; margin-top: 0.15rem;" class="dark:text-white"><?php echo e($c->sessions_count ?: $c->sessions->count()); ?></div>
                                            </div>
                                            <div style="background: #FFFFFF; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                                                <div style="color: #94A3B8; font-size: 0.7rem;"><?php echo e(__('Students')); ?></div>
                                                <div style="font-weight: 800; color: #0D9488; margin-top: 0.15rem;"><?php echo e($c->enrollments->count()); ?></div>
                                            </div>
                                            <div style="background: #FFFFFF; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                                                <div style="color: #94A3B8; font-size: 0.7rem;"><?php echo e(__('Price')); ?></div>
                                                <div style="font-weight: 800; color: #0F172A; margin-top: 0.15rem;" class="dark:text-white"><?php echo e($c->price ? $c->price . ' EGP' : __('Standard')); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <div style="grid-column: 1 / -1; padding: 2.5rem; text-align: center; color: #94A3B8;">
                                        <i class="fa-solid fa-book-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                        <p style="font-size: 0.875rem; font-weight: 600; margin: 0;"><?php echo e(__('No courses currently assigned to this teacher.')); ?></p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                    </div>

                    
                    <div class="rpt-print-section" id="section-sessions" style="<?php echo e($activeTab !== 'sessions' ? 'display:none;' : ''); ?>">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-video" style="color: #E11D48;"></i>
                                <?php echo e(__('Conducted & Scheduled Live Sessions')); ?>

                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('Session Title')); ?></th>
                                            <th><?php echo e(__('Course / Subject')); ?></th>
                                            <th><?php echo e(__('Target Student')); ?></th>
                                            <th><?php echo e(__('Scheduled Date & Time')); ?></th>
                                            <th><?php echo e(__('Duration & Platform')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->liveSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <tr>
                                                <td style="font-weight: 800; color: #0F172A;" class="dark:text-white">
                                                    <?php echo e($sess->title); ?>

                                                </td>
                                                <td><?php echo e($sess->course?->title ?? $sess->subject?->name ?? '-'); ?></td>
                                                <td>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sess->studentUser): ?>
                                                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                                                            <img src="<?php echo e($sess->studentUser->avatar_url); ?>" alt="<?php echo e($sess->studentUser->name); ?>" style="width: 2rem; height: 2rem; min-width: 2rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid #0D9488;" />
                                                            <div>
                                                                <div style="font-weight: 700; color: #0F172A; line-height: 1.2;" class="dark:text-white"><?php echo e($sess->studentUser->name); ?></div>
                                                                <div style="font-size: 0.7rem; color: #94A3B8;"><?php echo e($sess->studentUser->email); ?></div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="rpt-badge rpt-badge-info"><?php echo e(__('Cohort Session')); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </td>
                                                <td style="font-family: monospace;">
                                                    <?php echo e($sess->scheduled_at ? $sess->scheduled_at->format('Y-m-d H:i') : '-'); ?>

                                                </td>
                                                <td>
                                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                                        <i class="fa-solid fa-clock" style="color: #94A3B8;"></i>
                                                        <?php echo e($sess->duration_minutes); ?>m (<?php echo e(__(ucfirst($sess->meeting_platform ?: 'online'))); ?>)
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="rpt-badge <?php echo e($sess->status === 'completed' ? 'rpt-badge-success' : ($sess->status === 'scheduled' ? 'rpt-badge-info' : 'rpt-badge-danger')); ?>">
                                                        <?php echo e(__(ucfirst($sess->status))); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                                    <?php echo e(__('No live sessions recorded for this instructor.')); ?>

                                                </td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    
                    <div class="rpt-print-section" id="section-students" style="<?php echo e($activeTab !== 'students' ? 'display:none;' : ''); ?>">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-user-group" style="color: #0D9488;"></i>
                                <?php echo e(__('Enrolled Students Roster')); ?>

                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('Student')); ?></th>
                                            <th><?php echo e(__('Contact Info')); ?></th>
                                            <th><?php echo e(__('Grade Level')); ?></th>
                                            <th><?php echo e(__('Enrolled Course')); ?></th>
                                            <th><?php echo e(__('Enrollment Date')); ?></th>
                                            <th><?php echo e(__('Progress')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->enrolledStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <tr>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                        <img src="<?php echo e($enr->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=0D9488&color=fff'); ?>" alt="<?php echo e($enr->studentUser?->name); ?>" style="width: 2.35rem; height: 2.35rem; min-width: 2.35rem; border-radius: 9999px; object-fit: cover; border: 2px solid #0D9488; flex-shrink: 0;" />
                                                        <div>
                                                            <div style="font-weight: 800; color: #0F172A; line-height: 1.2;" class="dark:text-white">
                                                                <?php echo e($enr->studentUser?->name ?? __('Student')); ?>

                                                            </div>
                                                            <div style="font-size: 0.7rem; color: #64748B;" class="dark:text-slate-400">
                                                                <?php echo e($enr->studentUser?->studentProfile?->school_name ?: __('Elite Academy')); ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="font-size: 0.75rem;">
                                                    <div><?php echo e($enr->studentUser?->email); ?></div>
                                                    <div style="color: #94A3B8; font-family: monospace;"><?php echo e($enr->studentUser?->phone); ?></div>
                                                </td>
                                                <td>
                                                    <?php echo e($enr->studentUser?->studentProfile?->gradeLevel?->name ?? '-'); ?>

                                                </td>
                                                <td style="font-weight: 700; color: #6366F1;" class="dark:text-indigo-400">
                                                    <?php echo e($enr->course?->title ?? '-'); ?>

                                                </td>
                                                <td style="font-family: monospace;">
                                                    <?php echo e($enr->enrolled_at ? $enr->enrolled_at->format('Y-m-d') : '-'); ?>

                                                </td>
                                                <td>
                                                    <span style="font-weight: 800; color: #0D9488;"><?php echo e($enr->progress_percent); ?>%</span>
                                                </td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                                    <?php echo e(__('No students currently enrolled in this teacher\'s courses.')); ?>

                                                </td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    
                    <div class="rpt-print-section" id="section-assignments" style="<?php echo e($activeTab !== 'assignments' ? 'display:none;' : ''); ?>">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-file-pen" style="color: #9333EA;"></i>
                                <?php echo e(__('Assignments Created & Grading Log')); ?>

                            </h3>

                            <div class="rpt-table-container">
                                <table class="rpt-table">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(__('Assignment Title')); ?></th>
                                            <th><?php echo e(__('Course')); ?></th>
                                            <th><?php echo e(__('Passing Grade')); ?></th>
                                            <th><?php echo e(__('Submissions Received')); ?></th>
                                            <th><?php echo e(__('Graded Submissions')); ?></th>
                                            <th><?php echo e(__('Status')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php
                                                $gradedCount = $a->submissions->whereNotNull('grade')->count();
                                                $totalSubs = $a->submissions->count();
                                            ?>
                                            <tr>
                                                <td style="font-weight: 800; color: #0F172A;" class="dark:text-white">
                                                    <?php echo e($a->title); ?>

                                                </td>
                                                <td><?php echo e($a->course?->title ?? '-'); ?></td>
                                                <td style="font-family: monospace; font-weight: 800;">
                                                    <?php echo e($a->passing_grade ?: 60); ?>%
                                                </td>
                                                <td style="font-weight: 700; color: #9333EA;">
                                                    <?php echo e($totalSubs); ?> <?php echo e(__('Submissions')); ?>

                                                </td>
                                                <td>
                                                    <span class="rpt-badge <?php echo e($gradedCount === $totalSubs && $totalSubs > 0 ? 'rpt-badge-success' : 'rpt-badge-warning'); ?>">
                                                        <?php echo e($gradedCount); ?> / <?php echo e($totalSubs); ?> <?php echo e(__('Graded')); ?>

                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="rpt-badge <?php echo e($a->status === 'published' ? 'rpt-badge-success' : 'rpt-badge-info'); ?>">
                                                        <?php echo e(__(ucfirst($a->status))); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <tr>
                                                <td colspan="6" style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                                    <?php echo e(__('No assignments created by this teacher.')); ?>

                                                </td>
                                            </tr>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    
                    <div class="rpt-print-section" id="section-notes" style="<?php echo e($activeTab !== 'notes' ? 'display:none;' : ''); ?>">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-clipboard-user" style="color: #0284C7;"></i>
                                <?php echo e(__('Educational Notes Authored for Students')); ?>

                            </h3>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 1rem; padding: 1.15rem; display: flex; flex-direction: column; gap: 0.65rem;" class="dark:bg-slate-950/60 dark:border-slate-800">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <img src="<?php echo e($note->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=0284C7&color=fff'); ?>" alt="<?php echo e($note->studentUser?->name); ?>" style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid #0284C7; flex-shrink: 0;" />
                                            <div>
                                                <h4 style="font-size: 0.85rem; font-weight: 800; color: #0F172A; margin: 0;" class="dark:text-white">
                                                    <?php echo e(__('For Student')); ?>: <?php echo e($note->studentUser?->name ?? __('Student')); ?>

                                                </h4>
                                                <div style="font-size: 0.7rem; color: #94A3B8;"><?php echo e($note->studentUser?->email); ?></div>
                                            </div>
                                        </div>
                                        <span style="font-size: 0.75rem; font-family: monospace; color: #94A3B8;"><?php echo e($note->created_at->format('Y-m-d H:i')); ?></span>
                                    </div>
                                    <p style="font-size: 0.825rem; color: #475569; margin: 0; line-height: 1.6; padding-inline-start: 3rem;" class="dark:text-slate-300">
                                        <?php echo e($note->note); ?>

                                    </p>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                    <i class="fa-solid fa-clipboard-check" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                    <p style="font-size: 0.875rem; font-weight: 600; margin: 0;"><?php echo e(__('No educational notes authored by this teacher.')); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="rpt-print-section" id="section-exceptions" style="<?php echo e($activeTab !== 'exceptions' ? 'display:none;' : ''); ?>">
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 800; color: #0F172A; text-transform: uppercase; margin: 0; display: flex; align-items: center; gap: 0.5rem;" class="dark:text-white">
                                <i class="fa-solid fa-envelope-open-text" style="color: #9333EA;"></i>
                                <?php echo e(__('Student Absence & Exception Requests Handled')); ?>

                            </h3>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->exceptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 1rem; padding: 1.15rem; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;" class="dark:bg-slate-950/60 dark:border-slate-800">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="<?php echo e($exc->studentUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=Student&background=9333EA&color=fff'); ?>" alt="<?php echo e($exc->studentUser?->name); ?>" style="width: 2.25rem; height: 2.25rem; min-width: 2.25rem; border-radius: 9999px; object-fit: cover; border: 1.5px solid #9333EA; flex-shrink: 0;" />
                                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                                <h4 style="font-size: 0.85rem; font-weight: 800; color: #0F172A; margin: 0;" class="dark:text-white">
                                                    <?php echo e($exc->studentUser?->name ?? __('Student')); ?>

                                                </h4>
                                                <span class="rpt-badge rpt-badge-info"><?php echo e($exc->liveSession?->title ?? $exc->course?->title ?? __('Session Request')); ?></span>
                                                <span class="rpt-badge <?php echo e($exc->status === 'approved' ? 'rpt-badge-success' : ($exc->status === 'rejected' ? 'rpt-badge-danger' : 'rpt-badge-warning')); ?>">
                                                    <?php echo e(__(ucfirst($exc->status))); ?>

                                                </span>
                                            </div>
                                            <p style="font-size: 0.775rem; color: #64748B; margin: 0;" class="dark:text-slate-400">
                                                <?php echo e(__('Reason')); ?>: <?php echo e($exc->reason ?: '-'); ?>

                                            </p>
                                        </div>
                                    </div>
                                    <span style="font-size: 0.75rem; font-family: monospace; color: #94A3B8;"><?php echo e($exc->created_at->format('Y-m-d H:i')); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div style="padding: 2.5rem; text-align: center; color: #94A3B8;">
                                    <i class="fa-solid fa-envelope-circle-check" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                    <p style="font-size: 0.875rem; font-weight: 600; margin: 0;"><?php echo e(__('No exception requests for this teacher\'s classes.')); ?></p>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 3.5rem 1.5rem; text-align: center; background: #FFFFFF; border-radius: 1.5rem; border: 1.5px solid #E2E8F0;" class="dark:bg-slate-900 dark:border-slate-800">
                <i class="fa-solid fa-user-slash" style="font-size: 2.5rem; color: #94A3B8; margin-bottom: 0.75rem; display: block;"></i>
                <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0;" class="dark:text-white"><?php echo e(__('No teacher selected')); ?></h3>
                <p style="font-size: 0.825rem; color: #64748B; margin: 0.35rem 0 0 0;"><?php echo e(__('Please pick a teacher from the dropdown above to view their comprehensive faculty report.')); ?></p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\elite-academy\resources\views/filament/pages/teacher-reports-page.blade.php ENDPATH**/ ?>