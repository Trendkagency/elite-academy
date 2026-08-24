@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<style>
/* Elite Academy Signature Teal/Emerald Theme System */
.quiz-page-bg {
    background-color: #FAFAF9;
    min-height: 100vh;
}

.quiz-main-card {
    background: #FFFFFF;
    border-radius: 32px;
    border: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06), 0 0 1px rgba(15, 23, 42, 0.1);
}

.option-card-elite {
    border: 2px solid #E2E8F0;
    border-radius: 20px;
    background: #FFFFFF;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    user-select: none;
}

.option-card-elite:hover {
    border-color: #0D9488;
    background-color: #F0FDF4;
}

.option-card-elite.selected {
    border-color: #0D9488 !important;
    background-color: #F0FDF4 !important;
    box-shadow: 0 6px 16px rgba(13, 148, 136, 0.15) !important;
}

.btn-elite-primary {
    background-color: #0D9488;
    color: #FFFFFF;
    border-radius: 14px;
    transition: all 0.2s ease;
}

.btn-elite-primary:hover {
    background-color: #0F766E;
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(13, 148, 136, 0.25);
}

.btn-elite-nav {
    border: 2px solid #E2E8F0;
    background: #FFFFFF;
    color: #1E293B;
    border-radius: 14px;
    transition: all 0.2s ease;
}

.btn-elite-nav:hover:not(:disabled) {
    border-color: #0D9488;
    background: #F0FDF4;
}

.btn-elite-nav.active-step {
    background-color: #0D9488 !important;
    border-color: #0D9488 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
}

.unselectable {
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
}
</style>
@endpush

@section('content')
<section class="quiz-page-bg py-6 sm:py-10 px-4 sm:px-6 lg:px-8 min-h-screen unselectable" oncontextmenu="return false;" oncopy="return false;" oncut="return false;" ondragstart="return false;">
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Top Elite Academy Brand & User Profile Header --}}
        <div class="flex items-center justify-between px-2">
            <div class="flex items-center gap-3">
                <a href="{{ route('student-portal') }}" class="font-heading font-black text-2xl sm:text-3xl text-slate-900 tracking-tight hover:opacity-80 transition-opacity flex items-center gap-2">
                    <span class="text-teal-600">Elite</span> Academy<span class="text-teal-500">.</span>
                </a>
            </div>

            {{-- User Profile Pill --}}
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-xs">
                <div class="w-10 h-10 rounded-full bg-teal-600 text-white font-bold flex items-center justify-center text-sm shadow-sm overflow-hidden border-2 border-teal-300">
                    {{ mb_substr(auth()->user()->name ?? 'S', 0, 1) }}
                </div>
                <div class="text-left text-xs leading-tight">
                    <h4 class="font-extrabold text-slate-900">{{ auth()->user()->name ?? 'Learner' }}</h4>
                    <span class="font-mono text-slate-500 text-[11px] block">ID: {{ auth()->user()->id ?? '1001' }}</span>
                </div>
            </div>
        </div>

        {{-- Main Quiz Card Container --}}
        <form id="eliteQuizForm" action="{{ route('ajax.assignment.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">

            <div class="quiz-main-card p-6 sm:p-10 md:p-12 relative overflow-hidden space-y-8">
                
                {{-- Quiz Top Bar: Timer & Submit Button --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-teal-50 border border-teal-200/80 flex items-center justify-center text-teal-700 text-xl shadow-xs">
                            🕒
                        </div>
                        <div>
                            <span class="text-[11px] font-mono font-bold text-slate-400 uppercase tracking-wider block">Time remaining</span>
                            <span id="quizTimer" class="font-mono font-black text-slate-900 text-base sm:text-lg">00 : 30 : 00</span>
                        </div>
                    </div>

                    <button type="submit" id="submitQuizBtn" class="btn-elite-primary px-8 py-3 font-bold text-sm shadow-md cursor-pointer flex items-center gap-2">
                        Submit
                    </button>
                </div>

                {{-- Main Quiz Body (Questions + Circular Gauge) --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center min-h-[320px]">
                    
                    {{-- Left 8 Columns: Question & Options Grid --}}
                    <div class="lg:col-span-8 space-y-6">
                        @forelse($assignment->questions as $index => $q)
                            <div id="questionStep{{ $index }}" class="question-step space-y-6 {{ $index === 0 ? '' : 'hidden' }}" data-step="{{ $index }}">
                                
                                {{-- Question Number Tag --}}
                                <div class="space-y-1">
                                    <span class="text-xs font-mono font-extrabold text-slate-500 block">
                                        Question <span class="text-teal-700">{{ $index + 1 }}</span> of {{ count($assignment->questions) }}
                                    </span>
                                    <h3 class="font-heading font-black text-lg sm:text-xl text-slate-900 leading-snug math-render">
                                        {{ $q->question_text }}
                                    </h3>

                                    @if($q->image_path)
                                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 p-3 max-w-lg my-3 shadow-xs">
                                            <img src="{{ asset('storage/' . $q->image_path) }}" class="max-h-56 rounded-xl object-contain pointer-events-none">
                                        </div>
                                    @endif
                                </div>

                                {{-- Answer Option Cards (2x2 Grid on Desktop) --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                    @foreach($q->options as $optIndex => $opt)
                                        @php
                                            $inputType = $q->is_multiple_choice ? 'checkbox' : 'radio';
                                            $letter = chr(65 + $optIndex); // A, B, C, D
                                            $savedOptIds = $savedAnswers[(string) $q->id] ?? $savedAnswers[(int) $q->id] ?? [];
                                            if (is_string($savedOptIds)) {
                                                $savedOptIds = json_decode($savedOptIds, true) ?: [$savedOptIds];
                                            }
                                            $savedOptIdsInt = array_map('intval', (array) $savedOptIds);
                                            $isChecked = in_array((int) $opt->id, $savedOptIdsInt, true);
                                        @endphp
                                        <label class="option-label option-card-elite p-4 sm:p-5 flex items-center justify-between cursor-pointer text-sm font-semibold text-slate-800 shadow-xs {{ $isChecked ? 'selected' : '' }}">
                                            <div class="flex items-center gap-3">
                                                <input type="{{ $inputType }}" name="answers[{{ $q->id }}][]" value="{{ $opt->id }}" {{ $isChecked ? 'checked' : '' }} class="option-input accent-teal-600 w-4 h-4 cursor-pointer">
                                                
                                                <span class="option-letter-badge font-mono font-bold text-slate-400 w-6">
                                                    {{ $letter }}.
                                                </span>

                                                <span class="math-render leading-relaxed text-sm sm:text-base font-bold text-slate-800">{{ $opt->option_text }}</span>
                                            </div>

                                            @if($opt->image_path)
                                                <img src="{{ asset('storage/' . $opt->image_path) }}" class="h-8 rounded border border-slate-200 pointer-events-none">
                                            @endif
                                        </label>
                                    @endforeach
                                </div>

                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-500 font-mono text-xs">
                                No questions configured for this assignment yet.
                            </div>
                        @endforelse
                    </div>

                    {{-- Right 4 Columns: Circular Gauge Progress Ring --}}
                    <div class="lg:col-span-4 flex flex-col items-center justify-center p-4">
                        <div class="relative w-44 h-44 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="48" stroke="#E2E8F0" stroke-width="12" fill="transparent" />
                                <circle id="gaugeRingFill" cx="60" cy="60" r="48" stroke="#0D9488" stroke-width="12" fill="transparent"
                                        stroke-dasharray="301.59" stroke-dashoffset="271.43" stroke-linecap="round" class="transition-all duration-500 ease-out" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                <span id="gaugeText" class="font-heading font-black text-3xl sm:text-4xl text-slate-900 tracking-tight">1/{{ count($assignment->questions) }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Bottom Navigation Toolbar --}}
                <div class="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-slate-100">
                    <button type="button" id="prevBtn" disabled class="btn-elite-nav px-6 py-3 text-xs font-bold font-mono disabled:opacity-40 disabled:cursor-not-allowed">
                        Prev
                    </button>

                    {{-- Question Numbers Grid Map (1, 2, 3, 4...) --}}
                    <div class="flex flex-wrap items-center justify-center gap-2 overflow-x-auto py-1" id="dotsContainer">
                        @foreach($assignment->questions as $i => $q)
                            <button type="button" data-step-index="{{ $i }}" class="dot-item btn-elite-nav w-10 h-10 text-xs font-bold font-mono flex items-center justify-center {{ $i === 0 ? 'active-step' : '' }}">
                                {{ $i + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <button type="button" id="nextBtn" class="btn-elite-nav px-8 py-3 text-xs font-bold font-mono">
                        Next
                    </button>
                </div>

            </div>
        </form>

    </div>
</section>

{{-- Result Breakdown Modal (Displays Full Scores & Evaluation Breakdown on Screen) --}}
<div id="resultModal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-6 border border-slate-200 text-center">
        
        {{-- Passed/Failed Icon Badge --}}
        <div id="resultIconBadge" class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4">
            🎉
        </div>

        <div class="space-y-2">
            <h3 id="resultTitle" class="font-heading font-black text-2xl text-slate-900">Assignment Evaluated</h3>
            <p id="resultMessage" class="text-xs font-mono text-slate-600 leading-relaxed"></p>
        </div>

        {{-- Score Numbers Breakdown Grid --}}
        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 font-mono text-xs">
            <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100">
                <span class="text-slate-400 uppercase text-[10px] font-bold block">Final Percentage</span>
                <span id="resultPercentage" class="font-black text-2xl text-teal-600">100%</span>
            </div>
            <div class="space-y-1 p-2 bg-white rounded-xl border border-slate-100">
                <span class="text-slate-400 uppercase text-[10px] font-bold block">Points Earned</span>
                <span id="resultScore" class="font-black text-2xl text-slate-900">10 / 10</span>
            </div>
        </div>

        {{-- Modal Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('student-portal') }}" class="w-full sm:w-auto btn-elite-primary px-8 py-3 font-bold text-xs shadow-md">
                Go to Student Portal &rarr;
            </a>
            <button type="button" onclick="closeResultModal()" class="w-full sm:w-auto btn-elite-nav px-6 py-3 font-bold text-xs">
                Review Questions
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/contrib/auto-render.min.js"></script>

<script>
window.currentStep = {{ $currentStepIndex ?? 0 }};
window.totalSteps = {{ count($assignment->questions) }};
window.durationMinutes = {{ $assignment->duration_minutes ?? 30 }};
window.timerSeconds = {{ $remainingSeconds ?? 1800 }};
window.assignmentId = {{ $assignment->id }};
window.savedAnswers = @json($savedAnswers ?? []);

window.triggerKaTeXRender = function() {
    if (window.renderMathInElement) {
        window.renderMathInElement(document.body, {
            delimiters: [
                {left: '$$', right: '$$', display: true},
                {left: '$', right: '$', display: false},
                {left: '\\(', right: '\\)', display: false},
                {left: '\\[', right: '\\]', display: true}
            ],
            throwOnError: false
        });
    }
};

window.persistStepIndexToServer = async function(stepIdx) {
    try {
        await fetch("{{ route('ajax.assignment.update-step') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                assignment_id: window.assignmentId,
                current_step_index: parseInt(stepIdx, 10)
            })
        });
    } catch (e) {}
};

window.restoreSavedAnswers = function() {
    if (!window.savedAnswers || typeof window.savedAnswers !== 'object') return;

    for (const [questionId, selectedOpts] of Object.entries(window.savedAnswers)) {
        if (!selectedOpts) continue;
        let optsArray = selectedOpts;
        if (typeof optsArray === 'string') {
            try { optsArray = JSON.parse(optsArray); } catch(e) { optsArray = [optsArray]; }
        }
        if (!Array.isArray(optsArray)) {
            optsArray = [optsArray];
        }
        if (optsArray.length === 0) continue;

        const inputs = document.querySelectorAll(`input[name="answers[${questionId}][]"]`);
        inputs.forEach(input => {
            const valInt = parseInt(input.value, 10);
            const valStr = String(input.value);
            const isMatch = optsArray.some(opt => opt == valInt || opt == valStr);

            if (isMatch) {
                input.checked = true;
                const label = input.closest('.option-card-elite');
                if (label) label.classList.add('selected');
            }
        });
    }

    // Set step to server-restored currentStepIndex
    window.currentStep = {{ $currentStepIndex ?? 0 }};
    window.updateStepUI();
};

window.saveDraftTimers = window.saveDraftTimers || {};

window.saveDraftAnswerToServer = function(questionId, selectedOptionIds) {
    const qIdStr = String(questionId);
    if (window.saveDraftTimers[qIdStr]) {
        clearTimeout(window.saveDraftTimers[qIdStr]);
    }

    window.saveDraftTimers[qIdStr] = setTimeout(async function() {
        try {
            const res = await fetch("{{ route('ajax.assignment.save-answer') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    assignment_id: window.assignmentId,
                    question_id: parseInt(questionId, 10),
                    selected_option_ids: selectedOptionIds
                })
            });

            const data = await res.json();
            if (!res.ok || !data.success) {
                window.queueOfflineDraft(questionId, selectedOptionIds);
            }
        } catch (err) {
            window.queueOfflineDraft(questionId, selectedOptionIds);
        }
    }, 500);
};

window.queueOfflineDraft = function(questionId, selectedOptionIds) {
    try {
        const key = `pending_drafts_${window.assignmentId}`;
        const queue = JSON.parse(localStorage.getItem(key) || '{}');
        queue[questionId] = selectedOptionIds;
        localStorage.setItem(key, JSON.stringify(queue));
    } catch (e) {}
};

window.flushOfflineDrafts = async function() {
    try {
        const key = `pending_drafts_${window.assignmentId}`;
        const queueStr = localStorage.getItem(key);
        if (!queueStr) return;
        const queue = JSON.parse(queueStr);

        for (const [qId, optIds] of Object.entries(queue)) {
            await window.saveDraftAnswerToServer(qId, optIds);
        }
        localStorage.removeItem(key);
    } catch (e) {}
};

window.addEventListener('online', window.flushOfflineDrafts);

window.syncOptionUI = function(input) {
    const labelEl = input.closest('.option-card-elite');
    if (!labelEl) return;

    const parentContainer = labelEl.closest('.question-step');
    const isMulti = input.type === 'checkbox';
    const questionIdMatch = input.name.match(/answers\[(\d+)\]/);
    const questionId = questionIdMatch ? questionIdMatch[1] : null;

    if (!isMulti) {
        // Single choice: update selected class on all option cards for this question
        parentContainer.querySelectorAll('.option-card-elite').forEach(card => {
            const cardInput = card.querySelector('input');
            if (cardInput && cardInput.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    } else {
        if (input.checked) {
            labelEl.classList.add('selected');
        } else {
            labelEl.classList.remove('selected');
        }
    }

    // Collect all checked option IDs for this question
    if (questionId) {
        const selectedOptionIds = [];
        parentContainer.querySelectorAll(`input[name="answers[${questionId}][]"]:checked`).forEach(checkedInput => {
            selectedOptionIds.push(parseInt(checkedInput.value, 10));
        });

        // Trigger real-time server auto-save
        window.saveDraftAnswerToServer(questionId, selectedOptionIds);
    }

    // Update bottom map indicators
    window.updateStepUI();

    // Auto-advance to Next Question on single choice selection after 350ms
    if (!isMulti && window.currentStep < window.totalSteps - 1) {
        setTimeout(() => {
            window.navigateStep(1);
        }, 350);
    }
};

window.updateStepUI = function() {
    document.querySelectorAll('.question-step').forEach((step, idx) => {
        if (idx === window.currentStep) {
            step.classList.remove('hidden');
        } else {
            step.classList.add('hidden');
        }
    });

    // Update Circular Gauge Ring
    const gaugeText = document.getElementById('gaugeText');
    const ringFill = document.getElementById('gaugeRingFill');
    if (gaugeText) gaugeText.textContent = `${window.currentStep + 1}/${window.totalSteps}`;

    if (ringFill) {
        const circumference = 301.59;
        const progress = (window.currentStep + 1) / window.totalSteps;
        const offset = circumference * (1 - progress);
        ringFill.style.strokeDashoffset = offset;
    }

    // Buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (prevBtn) prevBtn.disabled = window.currentStep === 0;
    if (nextBtn) nextBtn.disabled = window.currentStep === window.totalSteps - 1;

    // Dots & Map Items
    document.querySelectorAll('.dot-item').forEach((dot) => {
        const idx = parseInt(dot.getAttribute('data-step-index') || '0', 10);
        if (idx === window.currentStep) {
            dot.classList.add('active-step');
        } else {
            dot.classList.remove('active-step');
        }

        if (window.isStepAnswered(idx)) {
            dot.classList.add('border-teal-600', 'bg-teal-50', 'text-teal-700');
        } else {
            dot.classList.remove('border-teal-600', 'bg-teal-50', 'text-teal-700');
        }
    });

    window.triggerKaTeXRender();
};

window.isStepAnswered = function(stepIndex) {
    const stepEl = document.querySelector(`.question-step[data-step="${stepIndex}"]`);
    if (!stepEl) return true;
    return stepEl.querySelector('input:checked') !== null;
};

window.navigateStep = function(direction) {
    // If going forward, enforce that current question MUST be answered first
    if (direction > 0) {
        if (!window.isStepAnswered(window.currentStep)) {
            if (window.Toast) {
                window.Toast.warning(
                    "{{ app()->getLocale() === 'ar' ? '⚠️ يرجى اختيار إجابة للسؤال الحالي أولاً قبل الانتقال للسؤال التالي.' : '⚠️ Please select an answer for the current question before advancing.' }}",
                    "{{ app()->getLocale() === 'ar' ? 'إجابة السؤال مطلوبة' : 'Answer Required' }}"
                );
            }
            return false;
        }
    }

    const newStep = window.currentStep + direction;
    if (newStep >= 0 && newStep < window.totalSteps) {
        window.currentStep = newStep;
        window.updateStepUI();
        window.persistStepIndexToServer(newStep);
        return true;
    }
    return false;
};

window.jumpToStep = function(targetStepIdx) {
    if (targetStepIdx === window.currentStep) return;

    // If jumping forward, verify all previous questions up to targetStepIdx are answered
    if (targetStepIdx > window.currentStep) {
        for (let i = 0; i < targetStepIdx; i++) {
            if (!window.isStepAnswered(i)) {
                if (window.Toast) {
                    window.Toast.warning(
                        `{{ app()->getLocale() === 'ar' ? '⚠️ يرجى إجابة السؤال رقم (' : '⚠️ Please answer question #' }}${i + 1}{{ app()->getLocale() === 'ar' ? ') أولاً قبل الانتقال لأسئلة لاحقة.' : ' first before skipping ahead.' }}`,
                        "{{ app()->getLocale() === 'ar' ? 'إجابة السؤال مطلوبة' : 'Answer Required' }}"
                    );
                }
                window.currentStep = i;
                window.updateStepUI();
                window.persistStepIndexToServer(i);
                return;
            }
        }
    }

    if (targetStepIdx >= 0 && targetStepIdx < window.totalSteps) {
        window.currentStep = targetStepIdx;
        window.updateStepUI();
        window.persistStepIndexToServer(targetStepIdx);
    }
};

window.showResultModal = function(data) {
    const modal = document.getElementById('resultModal');
    const badge = document.getElementById('resultIconBadge');
    const title = document.getElementById('resultTitle');
    const msg = document.getElementById('resultMessage');
    const perc = document.getElementById('resultPercentage');
    const score = document.getElementById('resultScore');

    if (!modal) return;

    if (data.is_passed) {
        badge.textContent = '🎉';
        badge.className = 'w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4 bg-emerald-50 text-emerald-600 border-emerald-300';
        title.textContent = 'Passed Successfully! ✓';
        title.className = 'font-heading font-black text-2xl text-emerald-700';
    } else {
        badge.textContent = '⚠️';
        badge.className = 'w-20 h-20 rounded-full mx-auto flex items-center justify-center text-4xl shadow-md border-4 bg-rose-50 text-rose-600 border-rose-300';
        title.textContent = 'Did Not Pass ✕';
        title.className = 'font-heading font-black text-2xl text-rose-700';
    }

    if (msg) msg.textContent = data.message || 'Evaluation completed.';
    if (perc) perc.textContent = `${Math.round(data.percentage || 0)}%`;
    if (score) score.textContent = `${data.score || 0} / ${data.total_points || 10}`;

    modal.classList.remove('hidden');
};

window.closeResultModal = function() {
    const modal = document.getElementById('resultModal');
    if (modal) modal.classList.add('hidden');
};

document.addEventListener('DOMContentLoaded', function () {
    // Next Button listener
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.navigateStep(1);
        });
    }

    // Prev Button listener
    const prevBtn = document.getElementById('prevBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.navigateStep(-1);
        });
    }

    // Option Input change listeners (Native reliable checking)
    document.querySelectorAll('.option-input').forEach(input => {
        input.addEventListener('change', function() {
            window.syncOptionUI(this);
        });
    });

    // Dot navigation map listeners
    document.querySelectorAll('.dot-item').forEach(dot => {
        dot.addEventListener('click', function(e) {
            e.preventDefault();
            const stepIdx = parseInt(this.getAttribute('data-step-index') || '0', 10);
            window.jumpToStep(stepIdx);
        });
    });

    // Timer (00:30:00 Format)
    setInterval(() => {
        if (window.timerSeconds <= 0) return;
        window.timerSeconds--;
        const hrs = Math.floor(window.timerSeconds / 3600);
        const mins = Math.floor((window.timerSeconds % 3600) / 60);
        const secs = Math.floor(window.timerSeconds % 60);
        const timerEl = document.getElementById('quizTimer');
        if (timerEl) {
            timerEl.textContent = `${String(hrs).padStart(2, '0')} : ${String(mins).padStart(2, '0')} : ${String(secs).padStart(2, '0')}`;
        }
    }, 1000);

    // Restore pre-saved draft answers from server
    window.restoreSavedAnswers();
    window.flushOfflineDrafts();

    // Form Submission AJAX
    const quizForm = document.getElementById('eliteQuizForm');
    if (quizForm) {
        quizForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('submitQuizBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Evaluating...';
            }

            try {
                const formData = new FormData(quizForm);
                const res = await fetch("{{ route('ajax.assignment.submit') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    if (window.Toast) window.Toast.error(data.message || 'Evaluation failed.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit';
                    }
                    return;
                }

                if (window.Toast) {
                    if (data.is_passed) {
                        window.Toast.success(`Score: ${data.percentage}% (PASSED ✓)`, 'Assignment Complete!');
                    } else {
                        window.Toast.error(`Score: ${data.percentage}% (FAILED ✕)`, 'Assignment Result');
                    }
                }

                // Show On-Screen Results Modal Directly
                window.showResultModal(data);

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Results Evaluated ✓';
                }
            } catch (err) {
                if (window.Toast) window.Toast.error('Network error during evaluation submission.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit';
                }
            }
        });
    }

    window.updateStepUI();
});
</script>
@endpush
@endsection
