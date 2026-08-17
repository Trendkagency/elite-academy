@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-teal-950 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        @include('components.breadcrumb', [
            'items' => [
                ['label' => __('navbar.home'), 'route' => 'home'],
                ['label' => __('app.parent_portal')],
            ]
        ])

        <div class="space-y-2">
            <span class="inline-block text-xs font-mono uppercase tracking-widest text-teal-400 font-extrabold bg-teal-950 px-3.5 py-1.5 rounded-full border border-teal-800">
                PARENT DASHBOARD
            </span>
            <h1 class="font-heading text-3xl sm:text-4xl font-black text-white tracking-tight">
                Parent Portal: Student Academic Monitoring
            </h1>
            <p class="text-slate-300 text-sm font-mono">
                Track your linked children's course enrollment, homework grades, and live session attendance.
            </p>
        </div>
    </div>
</section>

<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xl space-y-6">
            <h2 class="font-heading font-black text-2xl text-slate-900">Linked Students</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($linkedStudents) && count($linkedStudents) > 0)
                    @foreach($linkedStudents as $st)
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-3">
                            <div class="flex justify-between items-center">
                                <h3 class="font-bold text-lg text-slate-900">{{ $st->user?->name }}</h3>
                                <span class="text-xs font-mono font-bold bg-teal-100 text-teal-700 px-3 py-1 rounded-full">{{ $st->gradeLevel?->name ?: 'Grade Level' }}</span>
                            </div>
                            <p class="text-xs font-mono text-slate-500">School: {{ $st->school_name ?: 'Elite Academy' }}</p>
                            <button onclick="loadStudentProgress({{ $st->user_id }})" class="btn-lift py-2 px-4 text-xs font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-xl cursor-pointer">
                                View Academic Performance &rarr;
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-2 text-center py-8 bg-slate-50 rounded-2xl border border-slate-200 text-slate-500 text-sm">
                        No linked students found for this parent account.
                    </div>
                @endif
            </div>

            <div id="progressResult" class="hidden p-6 bg-teal-50/50 rounded-2xl border border-teal-200/80 space-y-3">
                <h3 class="font-bold text-base text-teal-900">Student Progress Overview</h3>
                <div id="progressContent" class="text-xs font-mono text-slate-700 space-y-2"></div>
            </div>
        </div>
    </div>
</section>

<script>
async function loadStudentProgress(studentId) {
    const resBox = document.getElementById('progressResult');
    const content = document.getElementById('progressContent');
    resBox.classList.remove('hidden');
    content.textContent = 'Loading student progress details...';

    try {
        const res = await fetch(`/ajax/parent/student/${studentId}/progress`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
            content.innerHTML = `
                <p><strong>Enrolled Courses Count:</strong> ${data.enrollments_count}</p>
                <p><strong>Total Homework Submissions:</strong> ${data.submissions.length}</p>
            `;
        } else {
            content.textContent = data.message || 'Error loading progress.';
        }
    } catch (e) {
        content.textContent = 'Network error while loading student progress.';
    }
}
</script>
@endsection
