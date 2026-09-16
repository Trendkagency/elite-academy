{{-- SECTION 7: EXCUSES & EXCEPTIONS HISTORY (Full Table & Submission Triggers) --}}
<section id="sectionPane_exceptions" class="student-section-pane space-y-6 {{ $activeTab === 'exceptions' ? '' : 'hidden' }}">
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-7 border border-slate-200/90 dark:border-slate-800 shadow-sm space-y-6">
        
        {{-- Header & Submission Trigger --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
            <div>
                <h2 class="font-heading font-black text-lg sm:text-xl md:text-2xl text-slate-900 dark:text-white flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-lg shadow-2xs">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </span>
                    <span>{{ $isAr ? 'سجل الأعذار والطلبات الاستثنائية' : 'Excuses & Exception Requests History' }}</span>
                </h2>
                <p class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-1">
                    {{ $isAr ? 'متابعة حالة أعذار الغياب وطلبات استثناء الواجبات والردود الإدارية عليها.' : 'Track the review status of your attendance excuses and homework exception submissions.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="window.openModal ? window.openModal('excuseModal') : document.getElementById('excuseModal').classList.remove('hidden')"
                    class="btn-lift px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-slate-950 rounded-2xl text-xs font-extrabold shadow-sm flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-file-signature"></i>
                    <span>{{ __('app.portal.submit_excuse') }}</span>
                </button>
                <button type="button" onclick="window.openModal ? window.openModal('homeworkExceptionModal') : document.getElementById('homeworkExceptionModal').classList.remove('hidden')"
                    class="btn-lift px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl text-xs font-bold shadow-sm flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-clipboard-question"></i>
                    <span>{{ __('app.portal.submit_exception') }}</span>
                </button>
            </div>
        </div>

        @if(count($exceptions) > 0)
            {{-- Full Information Exceptions Table --}}
            <div class="table-responsive rounded-2xl border border-slate-200/90 dark:border-slate-800">
                <table class="w-full text-start text-xs sm:text-sm elite-sortable-table" data-page-size="6">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 font-mono font-bold uppercase text-[11px] border-b border-slate-200 dark:border-slate-700 select-none">
                        <tr>
                            <th class="col-title py-3.5 px-4 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[240px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'رقم الطلب والنوع' : 'Request Reference & Scope' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-course py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[180px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'الحصة / الكورس المرتبط' : 'Target Session / Course' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-title py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[240px]" data-sort-type="text">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'سبب العذر أو الاستثناء' : 'Reason Description' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-date py-3.5 px-3 sortable-header cursor-pointer hover:text-teal-600 dark:hover:text-teal-400 transition-colors min-w-[140px]" data-sort-type="date">
                                <div class="flex items-center gap-1.5">
                                    <span>{{ $isAr ? 'تاريخ التقديم' : 'Submitted At' }}</span>
                                    <span class="sort-icon opacity-40 text-[10px]"><i class="fa-solid fa-sort"></i></span>
                                </div>
                            </th>
                            <th class="col-action py-3.5 px-4 text-end min-w-[140px]" data-no-sort="true">{{ $isAr ? 'حالة الاعتماد' : 'Approval Status' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($exceptions as $exc)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="col-title py-3.5 px-4 font-bold text-slate-900 dark:text-white min-w-[240px]">
                                    <div class="space-y-0.5">
                                        <span class="break-words font-semibold text-slate-900 dark:text-slate-100 block leading-snug">{{ $exc->is_global || $exc->scope === 'global' ? ($isAr ? 'استثناء شامل (كافة الكورسات)' : 'Global System Exemption') : ($isAr ? 'عذر كورس محدد' : 'Course Exception') }}</span>
                                        <p class="text-[10px] font-mono text-slate-400 font-normal">#EXC-{{ $exc->id }}</p>
                                    </div>
                                </td>

                                <td class="col-course py-3.5 px-3 whitespace-nowrap text-slate-700 dark:text-slate-300 min-w-[180px]">
                                    <span class="line-clamp-2 leading-snug">{{ $exc->liveSession?->title ?: ($exc->course?->title ?: ($isAr ? 'طلب عام' : 'General Request')) }}</span>
                                </td>

                                <td class="col-title py-3.5 px-3 text-slate-600 dark:text-slate-400 text-xs min-w-[240px] max-w-[360px] leading-relaxed">
                                    <span class="break-words line-clamp-3">{{ $exc->reason }}</span>
                                </td>

                                <td class="col-date py-3.5 px-3 whitespace-nowrap text-slate-500 font-mono text-xs min-w-[140px]">
                                    {{ $exc->created_at ? $exc->created_at->format('Y-m-d H:i') : '' }}
                                </td>

                                <td class="col-action py-3.5 px-4 text-end whitespace-nowrap min-w-[140px]">
                                    @if($exc->status === 'approved')
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> <span>{{ $isAr ? 'تم القبول والاعتماد' : 'Approved' }}</span>
                                        </span>
                                    @elseif($exc->status === 'rejected')
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-900 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i> <span>{{ $isAr ? 'مرفوض' : 'Rejected' }}</span>
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-clock"></i> <span>{{ $isAr ? 'قيد المراجعة' : 'Pending Review' }}</span>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-xs font-mono text-slate-500 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-700">
                {{ $isAr ? 'لا توجد طلبات استثناء أو أعذار سابقة مسجلة.' : 'No exception requests recorded yet.' }}
            </div>
        @endif
    </div>
</section>
