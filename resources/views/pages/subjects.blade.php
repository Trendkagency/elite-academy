@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Subjects Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/80 pb-6">
            <div class="space-y-2">
                <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                    School <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Subjects</span>
                </h1>
                <p class="text-slate-600 text-base font-medium">
                    Browse every subject and discover available teachers and courses.
                </p>
            </div>

            {{-- Optional Filter Trigger --}}
            <label for="filter-drawer-toggle" class="btn-lift inline-flex items-center gap-2 self-start sm:self-auto bg-white border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-xs font-mono font-bold px-4 py-2.5 rounded-xl shadow-xs cursor-pointer">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filters</span>
            </label>
        </div>

        {{-- Subjects Grid --}}
        @php
            $subjectsList = [
                ['name' => 'Mathematics', 'grade' => 'Secondary 1 • Grade 10', 'badgeColor' => 'bg-teal-600', 'img' => 'images/course_ai.png', 'desc' => 'Algebra, Trigonometry & Coordinate Geometry tailored for Ministry exams.', 'teachers' => '12 Teachers', 'lessons' => '48 Lessons'],
                ['name' => 'Physics', 'grade' => 'Secondary 2 • Grade 11', 'badgeColor' => 'bg-blue-600', 'img' => 'images/hero_student.png', 'desc' => 'Classical Mechanics, Thermodynamics & Wave Optics.', 'teachers' => '10 Teachers', 'lessons' => '42 Lessons'],
                ['name' => 'Chemistry', 'grade' => 'Secondary 3 • Grade 12', 'badgeColor' => 'bg-purple-600', 'img' => 'images/instructor_portrait.png', 'desc' => 'Organic Reactions, Electrochemistry & Kinetics.', 'teachers' => '14 Teachers', 'lessons' => '56 Lessons'],
                ['name' => 'Biology', 'grade' => 'Secondary 2 • Grade 11', 'badgeColor' => 'bg-emerald-600', 'img' => 'images/instructor_female.png', 'desc' => 'Human Physiology, Genetics & Ecology.', 'teachers' => '8 Teachers', 'lessons' => '36 Lessons'],
                ['name' => 'English Literature', 'grade' => 'Secondary 1 • Grade 10', 'badgeColor' => 'bg-rose-600', 'img' => 'images/instructor_male.png', 'desc' => 'Poetry Analysis, Advanced Grammar & Essay Writing.', 'teachers' => '11 Teachers', 'lessons' => '40 Lessons'],
                ['name' => 'Arabic Rhetoric', 'grade' => 'Secondary 3 • Grade 12', 'badgeColor' => 'bg-orange-600', 'img' => 'images/academy_campus.png', 'desc' => 'Grammar Syntax, Classical Rhetoric & Literature.', 'teachers' => '15 Teachers', 'lessons' => '64 Lessons'],
                ['name' => 'Robotics & Python', 'grade' => 'Preparatory 3 • Grade 9', 'badgeColor' => 'bg-cyan-600', 'img' => 'images/instructor_portrait.png', 'desc' => 'STEM Coding, Sensors & Hardware Logic.', 'teachers' => '7 Teachers', 'lessons' => '28 Lessons'],
                ['name' => 'French Language', 'grade' => 'Preparatory 2 • Grade 8', 'badgeColor' => 'bg-amber-600', 'img' => 'images/instructor_female.png', 'desc' => 'Conversational Grammar, Vocabulary & Comprehension.', 'teachers' => '6 Teachers', 'lessons' => '24 Lessons'],
            ];
        @endphp

        <div id="subjects-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 md:gap-8 pt-2">
            @foreach ($subjectsList as $s)
                @include('components.subject-card', [
                    'image' => $s['img'],
                    'grade' => $s['grade'],
                    'badgeColor' => $s['badgeColor'],
                    'name' => $s['name'],
                    'description' => $s['desc'],
                    'teachers' => $s['teachers'],
                    'lessons' => $s['lessons'],
                    'route' => route('subject-details'),
                ])
            @endforeach
        </div>

        {{-- Loading Skeleton --}}
        <div id="skeleton-loader-subjects" class="hidden grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pt-6">
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[460px] border border-slate-300/50"></div>
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[460px] border border-slate-300/50"></div>
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[460px] border border-slate-300/50"></div>
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[460px] border border-slate-300/50"></div>
        </div>

        {{-- Infinite Scroll Sentinel --}}
        <div id="subjects-scroll-sentinel" class="py-6 flex flex-col items-center justify-center space-y-3">
            <div id="subjects-spinner" class="hidden items-center gap-3 text-teal-600 font-mono text-xs font-bold">
                <svg class="animate-spin h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading additional school subjects...</span>
            </div>

            <p id="subjects-end-message" class="hidden text-xs font-mono font-extrabold text-slate-500 bg-slate-100 px-5 py-2.5 rounded-full border border-slate-200">
                🎉 You've reached the end of our subjects catalog.
            </p>
        </div>

    </div>
</section>

{{-- Slide-over Filter Drawer --}}
<input type="checkbox" id="filter-drawer-toggle" class="peer hidden">
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs opacity-0 pointer-events-none peer-checked:opacity-100 peer-checked:pointer-events-auto transition-opacity">
    <div class="fixed right-0 top-0 bottom-0 w-full max-w-md bg-white p-6 shadow-2xl flex flex-col justify-between translate-x-full peer-checked:translate-x-0 transition-transform duration-300 space-y-6 overflow-y-auto">
        <div class="space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="font-heading font-black text-xl text-slate-900">Filter Subjects</h3>
                <label for="filter-drawer-toggle" class="p-2 text-slate-400 hover:text-slate-900 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </label>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider mb-2">Search</label>
                    <input type="text" placeholder="Search subject name..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium">
                </div>

                <div>
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider mb-2">Sort By</label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold">
                        <option>Most Popular</option>
                        <option>Alphabetical</option>
                        <option>Newest</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex gap-3">
            <label for="filter-drawer-toggle" class="w-1/2 text-center py-2.5 font-bold text-slate-600 bg-slate-100 rounded-xl cursor-pointer">Close</label>
            <label for="filter-drawer-toggle" class="w-1/2 text-center py-2.5 font-extrabold text-white bg-teal-600 hover:bg-teal-700 rounded-xl cursor-pointer">Apply Filters</label>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
      const grid = document.getElementById('subjects-grid');
      const sentinel = document.getElementById('subjects-scroll-sentinel');
      const spinner = document.getElementById('subjects-spinner');
      const skeleton = document.getElementById('skeleton-loader-subjects');
      const endMessage = document.getElementById('subjects-end-message');

      let currentBatch = 1;
      const maxBatches = 2;
      let isFetching = false;

      const extraSubjects = [
        { name: "Environmental Science", grade: "Secondary 2 • Grade 11", desc: "Eco-systems, Climate Cycles & Biodiversity.", teachers: 5, lessons: 22, badge: "bg-emerald-600", img: "images/hero_student.png" },
        { name: "German Language", grade: "Preparatory 3 • Grade 9", desc: "German Grammar, Conversation & Reading.", teachers: 6, lessons: 26, badge: "bg-amber-600", img: "images/instructor_female.png" },
        { name: "Computer Studies", grade: "Preparatory 1 • Grade 7", desc: "Algorithms, Data Representation & Office Skills.", teachers: 8, lessons: 30, badge: "bg-cyan-600", img: "images/course_ai.png" },
        { name: "Global History", grade: "Secondary 1 • Grade 10", desc: "Ancient Civilizations & Modern World Events.", teachers: 7, lessons: 32, badge: "bg-purple-600", img: "images/academy_campus.png" }
      ];

      function createSubjectCardHTML(s) {
        return `
          <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-lg hover:shadow-2xl card-lift group transition-all duration-300 flex flex-col justify-between h-[460px]">
            <div class="relative h-56 overflow-hidden bg-slate-950">
              <img src="${s.img}" loading="lazy" alt="${s.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
              <span class="absolute top-4 left-4 text-[10px] font-mono font-extrabold text-white ${s.badge} px-3 py-1 rounded-full shadow-md">
                ${s.grade}
              </span>
            </div>

            <div class="p-6 flex flex-col justify-between flex-1 space-y-3">
              <div class="space-y-1">
                <h3 class="font-heading font-extrabold text-xl text-slate-900 group-hover:text-teal-600 transition-colors">
                  ${s.name}
                </h3>
                <p class="text-xs text-slate-500 line-clamp-2">${s.desc}</p>
              </div>

              <div class="pt-3 border-t border-slate-100 text-xs font-semibold text-slate-600 space-y-3">
                <div class="flex items-center justify-between font-mono text-[11px]">
                  <span>👨‍🏫 ${s.teachers} Teachers</span>
                  <span>📚 ${s.lessons} Lessons</span>
                </div>
                <a href="{{ route('subject-details') }}" class="btn-lift block w-full text-center py-2 bg-teal-50 hover:bg-teal-600 text-teal-700 hover:text-white font-extrabold rounded-xl transition-all">
                  Explore Subject &rarr;
                </a>
              </div>
            </div>
          </div>
        `;
      }

      function fetchMoreSubjects() {
        if (isFetching || currentBatch >= maxBatches) return;
        isFetching = true;

        spinner.classList.remove('hidden');
        spinner.classList.add('flex');
        skeleton.classList.remove('hidden');
        skeleton.classList.add('grid');

        setTimeout(() => {
          currentBatch++;

          extraSubjects.forEach(s => {
            grid.insertAdjacentHTML('beforeend', createSubjectCardHTML(s));
          });

          spinner.classList.remove('flex');
          spinner.classList.add('hidden');
          skeleton.classList.remove('grid');
          skeleton.classList.add('hidden');

          isFetching = false;

          if (currentBatch >= maxBatches) {
            endMessage.classList.remove('hidden');
            observer.disconnect();
          }
        }, 1200);
      }

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting && !isFetching && currentBatch < maxBatches) {
            fetchMoreSubjects();
          }
        });
      }, { rootMargin: '200px' });

      if (sentinel) observer.observe(sentinel);
    });
  </script>
@endpush
