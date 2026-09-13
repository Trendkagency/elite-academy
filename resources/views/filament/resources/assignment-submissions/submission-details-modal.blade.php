@php
    /** @var \App\Models\AssignmentSubmission $submission */
    $submission = $record ?? $submission ?? null;
    if (! $submission) {
        return;
    }

    $assignment = $submission->assignment;
    $student = $submission->studentUser;
    $answers = $submission->answers->keyBy('question_id');
    $questions = $assignment ? $assignment->questions()->with('options')->orderBy('sort_order', 'asc')->get() : collect();

    $passingScore = (float) ($submission->passing_score ?? $assignment?->passing_score ?? $assignment?->passing_grade ?? 70.0);
    $effectiveScore = $submission->percentage ?? $submission->grade ?? $submission->score ?? 0.0;
    $isPassed = (float) $effectiveScore >= $passingScore;

    $statusVal = is_object($submission->status) ? ($submission->status->value ?? (string)$submission->status) : (string)$submission->status;

    // Calculate time spent duration if available
    $timeSpentText = null;
    if ($submission->started_at && $submission->submitted_at) {
        $diffMinutes = $submission->started_at->diffInMinutes($submission->submitted_at);
        $diffSeconds = $submission->started_at->diffInSeconds($submission->submitted_at) % 60;
        $timeSpentText = $diffMinutes > 0 ? "{$diffMinutes}m {$diffSeconds}s" : "{$diffSeconds}s";
    }

    $totalQuestions = $questions->count();
    $correctAnswersCount = 0;
    foreach ($questions as $q) {
        $ans = $answers->get($q->id);
        if ($ans && $ans->is_correct) {
            $correctAnswersCount++;
        }
    }
@endphp

<style>
    .submission-modal-container {
        font-family: inherit;
        color: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 4px 0;
    }
    .submission-card {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25);
    }
    .submission-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border: 1px solid #334155;
        border-radius: 20px;
        padding: 24px;
        position: relative;
        overflow: hidden;
    }
    .hero-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 20px;
        align-items: center;
    }
    @media (max-width: 768px) {
        .hero-grid {
            grid-template-columns: 1fr;
        }
    }
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
    }
    .stat-pill {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }
    .info-group {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 14px;
        padding: 14px 16px;
    }
    .info-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 13px;
        font-weight: 700;
        color: #f1f5f9;
    }
    .question-card {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.2s ease;
    }
    .question-card.correct-border {
        border-right: 4px solid #10b981;
    }
    .question-card.incorrect-border {
        border-right: 4px solid #ef4444;
    }
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid #1e293b;
        gap: 10px;
    }
    .option-row {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 13px;
    }
    .option-row.is-correct-key {
        border-color: #059669;
        background: rgba(16, 185, 129, 0.08);
    }
    .option-row.student-picked-correct {
        border-color: #10b981;
        background: rgba(16, 185, 129, 0.18);
        box-shadow: 0 0 0 1px #10b981;
    }
    .option-row.student-picked-incorrect {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.15);
        box-shadow: 0 0 0 1px #ef4444;
    }
    .badge-custom {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 800;
    }
</style>

<div class="submission-modal-container">
    {{-- Top Hero Section --}}
    <div class="submission-hero">
        <div class="hero-grid">
            <div>
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; flex-wrap: wrap;">
                    <span class="badge-custom" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <i class="fa-solid fa-book-bookmark"></i>
                        {{ $assignment?->course?->title ?? 'Course Assignment' }}
                    </span>

                    @if($statusVal === 'completed')
                        <span class="badge-custom" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i> Completed / مكتمل
                        </span>
                    @elseif($statusVal === 'reviewed')
                        <span class="badge-custom" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                            <i class="fa-solid fa-user-check"></i> Reviewed / تم التقييم
                        </span>
                    @elseif($statusVal === 'late')
                        <span class="badge-custom" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);">
                            <i class="fa-solid fa-triangle-exclamation"></i> Late Submission / تسليم متأخر
                        </span>
                    @elseif($statusVal === 'submitted')
                        <span class="badge-custom" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3);">
                            <i class="fa-solid fa-paper-plane"></i> Submitted / تم التسليم
                        </span>
                    @else
                        <span class="badge-custom" style="background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3);">
                            <i class="fa-solid fa-clock"></i> {{ ucfirst($statusVal) }}
                        </span>
                    @endif

                    @if($isPassed)
                        <span class="badge-custom" style="background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981;">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i> PASSED (ناجح)
                        </span>
                    @else
                        <span class="badge-custom" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid #ef4444;">
                            <i class="fa-solid fa-circle-xmark text-rose-500"></i> FAILED (راسب)
                        </span>
                    @endif
                </div>

                <h2 style="font-size: 20px; font-weight: 800; color: #ffffff; margin: 0 0 6px 0; line-height: 1.4;">
                    {{ $assignment?->title ?? 'Assignment Submission' }}
                </h2>

                <p style="font-size: 13px; color: #94a3b8; margin: 0;">
                    {{ $assignment?->description ?? 'No assignment description provided.' }}
                </p>
            </div>

            {{-- Big Grade Banner --}}
            <div style="background: #0f172a; border: 2px solid {{ $isPassed ? '#10b981' : '#ef4444' }}; border-radius: 18px; padding: 16px 24px; text-align: center; min-width: 150px;">
                <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px;">
                    Final Grade
                </div>
                <div style="font-size: 32px; font-weight: 900; color: {{ $isPassed ? '#34d399' : '#f87171' }}; line-height: 1;">
                    {{ round($effectiveScore, 1) }}%
                </div>
                <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-top: 4px;">
                    Pass Threshold: {{ round($passingScore, 1) }}%
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-strip">
        {{-- Student Info --}}
        <div class="stat-pill">
            <div class="stat-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <div class="info-label">Student Name / الطالب</div>
                <div class="info-value">{{ $student?->name ?? 'Student #' . $submission->student_user_id }}</div>
                <div style="font-size: 11px; color: #64748b;">{{ $student?->email ?? 'No email' }}</div>
            </div>
        </div>

        {{-- Points & Questions Breakdown --}}
        <div class="stat-pill">
            <div class="stat-icon" style="background: rgba(20, 184, 166, 0.15); color: #2dd4bf;">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <div class="info-label">Correct Answers / الأسئلة الصحيحة</div>
                <div class="info-value" style="color: #2dd4bf;">
                    {{ $correctAnswersCount }} / {{ $totalQuestions }} Questions
                </div>
                <div style="font-size: 11px; color: #64748b;">
                    Score: {{ $submission->score ?? $submission->grade ?? 0 }} / {{ $submission->total_points ?? 100 }} pts
                </div>
            </div>
        </div>

        {{-- Timing & Duration --}}
        <div class="stat-pill">
            <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                <i class="fa-solid fa-stopwatch"></i>
            </div>
            <div>
                <div class="info-label">Submission Date & Time</div>
                <div class="info-value">
                    {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, h:i A') : 'Pending' }}
                </div>
                <div style="font-size: 11px; color: #64748b;">
                    {{ $timeSpentText ? "Time Taken: {$timeSpentText}" : ($submission->started_at ? 'Started: ' . $submission->started_at->format('h:i A') : 'Attempt #1') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Meta Details Grid --}}
    <div class="meta-grid">
        <div class="info-group">
            <div class="info-label"><i class="fa-solid fa-graduation-cap"></i> Course & Subject</div>
            <div class="info-value">{{ $assignment?->course?->title ?? 'N/A' }}</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                Subject: {{ $assignment?->course?->subject?->name ?? 'General' }} | Session: {{ $assignment?->liveSession?->title ?? $assignment?->session?->title ?? 'Main Session' }}
            </div>
        </div>

        <div class="info-group">
            <div class="info-label"><i class="fa-solid fa-chalkboard-user"></i> Assigned Teacher</div>
            <div class="info-value">
                {{ $assignment?->teacherProfile?->user?->name ?? $assignment?->course?->teacher?->name ?? 'Academy Instructor' }}
            </div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                Enrollment ID: #{{ $submission->course_enrollment_id ?? 'N/A' }}
            </div>
        </div>

        <div class="info-group">
            <div class="info-label"><i class="fa-solid fa-calendar-xmark"></i> Assignment Deadline & Rules</div>
            <div class="info-value">
                {{ $assignment?->effective_due_at ? $assignment->effective_due_at->format('d M Y, h:i A') : 'No strict deadline' }}
            </div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">
                Duration: {{ $assignment?->duration_minutes ? $assignment->duration_minutes . ' mins' : 'Untimed' }} | Mandatory: {{ $assignment?->is_mandatory ? 'Yes (إلزامي)' : 'Optional' }}
            </div>
        </div>
    </div>

    {{-- Teacher & Evaluation Notes --}}
    @if($submission->teacher_notes || $submission->evaluation_notes || $submission->reviewed_by)
        <div class="submission-card" style="background: #0f172a; border-left: 4px solid #38bdf8;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 13px; font-weight: 800; color: #38bdf8; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-comment-dots"></i> Teacher Feedback & Evaluation Notes / ملاحظات وتوجيهات التقييم
                </span>
                @if($submission->reviewer)
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 700;">
                        Reviewed by: {{ $submission->reviewer->name }} {{ $submission->reviewed_at ? '(' . $submission->reviewed_at->format('d M Y, h:i A') . ')' : '' }}
                    </span>
                @endif
            </div>
            @if($submission->teacher_notes)
                <div style="font-size: 13px; color: #e2e8f0; line-height: 1.6; margin-bottom: 6px;">
                    <strong>ملاحظات المعلم:</strong> {{ $submission->teacher_notes }}
                </div>
            @endif
            @if($submission->evaluation_notes && $submission->evaluation_notes !== $submission->teacher_notes)
                <div style="font-size: 12px; color: #94a3b8; line-height: 1.5;">
                    <strong>ملاحظات النظام:</strong> {{ $submission->evaluation_notes }}
                </div>
            @endif
        </div>
    @endif

    {{-- Question-by-Question Breakdown --}}
    <div class="submission-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #334155;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-list-check" style="color: #2dd4bf;"></i>
                    Detailed Questions & Answers Breakdown / تفاصيل الأسئلة والإجابات
                </h3>
                <p style="font-size: 12px; color: #94a3b8; margin: 0;">
                    Review student answers compared against the official answer keys.
                </p>
            </div>
            <span class="badge-custom" style="background: rgba(20, 184, 166, 0.15); color: #2dd4bf; border: 1px solid rgba(20, 184, 166, 0.3);">
                {{ $questions->count() }} Total Questions
            </span>
        </div>

        @if($questions->isNotEmpty())
            @foreach($questions as $index => $question)
                @php
                    $answer = $answers->get($question->id);
                    $selectedIds = $answer ? (array) $answer->selected_option_ids : [];
                    $isCorrect = $answer ? (bool) $answer->is_correct : false;
                    $pointsEarned = $answer ? (float) $answer->points_earned : 0.0;
                    $questionPoints = (float) ($question->points ?? 1.0);
                    $hasAnswered = $answer !== null;
                @endphp

                <div class="question-card {{ $isCorrect ? 'correct-border' : ($hasAnswered ? 'incorrect-border' : '') }}">
                    <div class="question-header">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="background: #1e293b; color: #38bdf8; font-weight: 800; font-size: 12px; padding: 3px 10px; border-radius: 8px; border: 1px solid #334155;">
                                Q{{ $index + 1 }}
                            </span>
                            <span style="font-size: 12px; color: #94a3b8; font-weight: 700;">
                                {{ $question->is_multiple_choice ? 'Multiple Choice (MSQ)' : 'Single Choice' }}
                            </span>
                        </div>

                        <div>
                            @if($isCorrect)
                                <span class="badge-custom" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid #10b981;">
                                    <i class="fa-solid fa-check-circle"></i> Correct (+{{ $pointsEarned }} / {{ $questionPoints }} pts)
                                </span>
                            @elseif($hasAnswered)
                                <span class="badge-custom" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444;">
                                    <i class="fa-solid fa-times-circle"></i> Incorrect (0 / {{ $questionPoints }} pts)
                                </span>
                            @else
                                <span class="badge-custom" style="background: rgba(148, 163, 184, 0.2); color: #94a3b8; border: 1px solid #64748b;">
                                    <i class="fa-solid fa-minus-circle"></i> Not Answered (0 / {{ $questionPoints }} pts)
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Question Prompt --}}
                    <div style="font-size: 14px; font-weight: 700; color: #f8fafc; line-height: 1.6; margin-bottom: 14px;">
                        {{ $question->question_text }}
                    </div>

                    @if($question->image_path)
                        <div style="margin-bottom: 14px; text-align: center;">
                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Attachment" style="max-height: 240px; border-radius: 12px; border: 1px solid #334155; margin: 0 auto;">
                        </div>
                    @endif

                    {{-- Options List --}}
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        @foreach($question->options as $option)
                            @php
                                $isOfficialCorrect = (bool) $option->is_correct;
                                $isPickedByStudent = in_array((int)$option->id, array_map('intval', $selectedIds), true);
                                
                                $optionClass = '';
                                if ($isPickedByStudent && $isOfficialCorrect) {
                                    $optionClass = 'student-picked-correct';
                                } elseif ($isPickedByStudent && !$isOfficialCorrect) {
                                    $optionClass = 'student-picked-incorrect';
                                } elseif (!$isPickedByStudent && $isOfficialCorrect) {
                                    $optionClass = 'is-correct-key';
                                }
                            @endphp

                            <div class="option-row {{ $optionClass }}">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    @if($isPickedByStudent && $isOfficialCorrect)
                                        <i class="fa-solid fa-square-check text-emerald-400 text-lg"></i>
                                    @elseif($isPickedByStudent && !$isOfficialCorrect)
                                        <i class="fa-solid fa-square-xmark text-rose-400 text-lg"></i>
                                    @elseif($isOfficialCorrect)
                                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                                    @else
                                        <i class="fa-regular fa-square text-slate-500 text-lg"></i>
                                    @endif

                                    <span style="font-weight: {{ $isOfficialCorrect || $isPickedByStudent ? '700' : '500' }}; color: {{ $isPickedByStudent && !$isOfficialCorrect ? '#fca5a5' : ($isOfficialCorrect ? '#86efac' : '#e2e8f0') }};">
                                        {{ $option->option_text }}
                                    </span>
                                </div>

                                <div style="display: flex; align-items: center; gap: 6px;">
                                    @if($isPickedByStudent)
                                        <span class="badge-custom" style="background: {{ $isOfficialCorrect ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)' }}; color: {{ $isOfficialCorrect ? '#34d399' : '#f87171' }}; border: 1px solid {{ $isOfficialCorrect ? '#10b981' : '#ef4444' }};">
                                            <i class="fa-solid fa-user"></i> Student Selection / اختيار الطالب
                                        </span>
                                    @endif

                                    @if($isOfficialCorrect)
                                        <span class="badge-custom" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4);">
                                            <i class="fa-solid fa-circle-check text-emerald-500"></i> Correct Answer / الإجابة النموذجية
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 30px; background: #0f172a; border-radius: 14px; border: 1px dashed #334155; color: #94a3b8;">
                <i class="fa-solid fa-file-lines" style="font-size: 32px; color: #64748b; margin-bottom: 10px;"></i>
                <div style="font-size: 14px; font-weight: 700; color: #cbd5e1;">Manual Assignment / Homework Task</div>
                <div style="font-size: 12px; margin-top: 4px;">This assignment was evaluated directly or as a file upload task without automated MSQ question breakdown.</div>
            </div>
        @endif
    </div>
</div>
