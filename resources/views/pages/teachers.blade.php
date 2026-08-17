@extends('layouts.app')

@section('content')
<section class="py-12 md:py-16 bg-[#FAFAF9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Teacher Directory Header --}}
        <div class="space-y-2 border-b border-slate-200/80 pb-6">
            <h1 class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight">
                Meet Our <span class="text-teal-600 underline decoration-orange-500 underline-offset-8">Expert Teachers</span>
            </h1>
            <p class="text-slate-600 text-base font-medium">
                Browse experienced teachers by subject and grade level.
            </p>
        </div>

        {{-- Teacher Filters Toolbar --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-200/90 shadow-lg space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2 space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider">Teacher</label>
                    <select class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 focus:outline-teal-600 cursor-pointer">
                        <option value="">Select a teacher...</option>
                        <option value="ahmed-hassan">Dr. Ahmed Hassan</option>
                        <option value="sarah-mohamed">Sarah Mohamed</option>
                        <option value="omar-khaled">Omar Khaled</option>
                        <option value="fatma-ali">Fatma Ali</option>
                        <option value="mohamed-adel">Mohamed Adel</option>
                        <option value="nour-ibrahim">Dr. Nour Ibrahim</option>
                        <option value="kareem-zaki">Eng. Kareem Zaki</option>
                        <option value="mona-sayed">Dr. Mona El-Sayed</option>
                        <option value="tarek-fouad">Dr. Tarek Fouad</option>
                        <option value="hoda-mahmoud">Hoda Mahmoud</option>
                        <option value="sherif-youssef">Dr. Sherif Youssef</option>
                        <option value="nouran-samy">Nouran Samy</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider">Grade</label>
                    <select class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-3.5 text-sm font-semibold text-slate-800 focus:outline-teal-600 cursor-pointer">
                        <option value="">All Grades</option>
                        <option value="10">Grade 10</option>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-mono font-extrabold text-slate-500 uppercase tracking-wider">Sort By</label>
                    <select class="w-full h-11 bg-[#FAFAF9] border border-slate-200 rounded-xl px-3.5 text-sm font-semibold text-slate-800 focus:outline-teal-600 cursor-pointer">
                        <option value="popular">Most Popular</option>
                        <option value="rating">Highest Rated</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>

            {{-- Subject Filter Chips --}}
            <div class="flex flex-wrap items-center gap-2 text-xs font-mono font-bold pt-4 border-t border-slate-100">
                <span class="text-slate-400 mr-2 uppercase">Subject Filters:</span>
                @include('components.filter-chip', ['label' => 'All', 'active' => true])
                @foreach (['Mathematics', 'Physics', 'Chemistry', 'Biology', 'Arabic', 'English', 'Programming'] as $subjectChip)
                    @include('components.filter-chip', ['label' => $subjectChip])
                @endforeach
            </div>
        </div>

        {{-- Counter Info Row --}}
        <div class="flex items-center justify-between text-xs font-mono font-bold text-slate-500 px-2 py-1">
            <span id="faculty-counter">Showing 12 Faculty Members</span>
            <span>150 Teachers • 18 Subjects • 12,000 Students</span>
        </div>

        {{-- Teachers Grid --}}
        @php
            $teachersList = [
                ['name' => 'Dr. Ahmed Hassan', 'title' => 'Algebra & Calculus', 'subject' => 'Mathematics', 'color' => 'bg-teal-600', 'rating' => '4.9 ★', 'students' => '1.4k Students', 'photo' => 'images/instructor_portrait.png'],
                ['name' => 'Sarah Mohamed', 'title' => 'Optics & Electromagnetism', 'subject' => 'Physics', 'color' => 'bg-purple-600', 'rating' => '4.8 ★', 'students' => '1.1k Students', 'photo' => 'images/instructor_female.png'],
                ['name' => 'Omar Khaled', 'title' => 'Organic Chemistry', 'subject' => 'Chemistry', 'color' => 'bg-blue-600', 'rating' => '4.9 ★', 'students' => '950 Students', 'photo' => 'images/instructor_male.png'],
                ['name' => 'Fatma Ali', 'title' => 'Genetics & Physiology', 'subject' => 'Biology', 'color' => 'bg-emerald-600', 'rating' => '4.9 ★', 'students' => '1.25k Students', 'photo' => 'images/hero_student.png'],
                ['name' => 'Mohamed Adel', 'title' => 'Classical Literature', 'subject' => 'Arabic', 'color' => 'bg-orange-600', 'rating' => '4.7 ★', 'students' => '880 Students', 'photo' => 'images/instructor_portrait.png'],
                ['name' => 'Dr. Nour Ibrahim', 'title' => 'Advanced Applied Math', 'subject' => 'Mathematics', 'color' => 'bg-teal-600', 'rating' => '5.0 ★', 'students' => '1.6k Students', 'photo' => 'images/academy_campus.png'],
                ['name' => 'Eng. Kareem Zaki', 'title' => 'Full-Stack Architecture', 'subject' => 'Programming', 'color' => 'bg-cyan-600', 'rating' => '4.9 ★', 'students' => '1.3k Students', 'photo' => 'images/course_ai.png'],
                ['name' => 'Dr. Mona El-Sayed', 'title' => 'Poetry & Rhetoric', 'subject' => 'English', 'color' => 'bg-rose-600', 'rating' => '4.8 ★', 'students' => '920 Students', 'photo' => 'images/instructor_female.png'],
                ['name' => 'Dr. Tarek Fouad', 'title' => 'Nuclear & Modern Physics', 'subject' => 'Physics', 'color' => 'bg-purple-600', 'rating' => '4.9 ★', 'students' => '1.05k Students', 'photo' => 'images/instructor_male.png'],
                ['name' => 'Hoda Mahmoud', 'title' => 'Analytical Chemistry', 'subject' => 'Chemistry', 'color' => 'bg-blue-600', 'rating' => '4.8 ★', 'students' => '780 Students', 'photo' => 'images/hero_student.png'],
                ['name' => 'Dr. Sherif Youssef', 'title' => 'AI & Neural Networks', 'subject' => 'Programming', 'color' => 'bg-cyan-600', 'rating' => '5.0 ★', 'students' => '1.5k Students', 'photo' => 'images/instructor_portrait.png'],
                ['name' => 'Nouran Samy', 'title' => 'Biochemistry & Ecosystems', 'subject' => 'Biology', 'color' => 'bg-emerald-600', 'rating' => '4.9 ★', 'students' => '1.1k Students', 'photo' => 'images/instructor_female.png'],
            ];
        @endphp

        <div id="teachers-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            @foreach ($teachersList as $t)
                @include('components.teacher-card', [
                    'photo' => $t['photo'],
                    'name' => $t['name'],
                    'title' => $t['title'],
                    'subject' => $t['subject'],
                    'subjectColor' => $t['color'],
                    'rating' => $t['rating'],
                    'students' => $t['students'],
                    'route' => route('teacher-profile'),
                ])
            @endforeach
        </div>

        {{-- Loading Skeleton --}}
        <div id="skeleton-loader" class="hidden grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pt-8">
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[440px] border border-slate-300/50"></div>
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[440px] border border-slate-300/50"></div>
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[440px] border border-slate-300/50"></div>
            <div class="animate-pulse bg-slate-200/80 rounded-3xl h-[440px] border border-slate-300/50"></div>
        </div>

        {{-- Infinite Scroll Sentinel --}}
        <div id="infinite-scroll-sentinel" class="py-8 flex flex-col items-center justify-center space-y-3">
            <div id="spinner" class="hidden items-center gap-3 text-teal-600 font-mono text-xs font-bold">
                <svg class="animate-spin h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading more faculty members...</span>
            </div>

            <p id="end-message" class="hidden text-xs font-mono font-extrabold text-slate-500 bg-slate-100 px-5 py-2.5 rounded-full border border-slate-200">
                🎉 You've reached the end of our faculty directory.
            </p>

            <button id="manual-load-more-btn" class="hidden btn-lift px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer">
                Load More Teachers
            </button>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
      const grid = document.getElementById('teachers-grid');
      const sentinel = document.getElementById('infinite-scroll-sentinel');
      const spinner = document.getElementById('spinner');
      const skeleton = document.getElementById('skeleton-loader');
      const endMessage = document.getElementById('end-message');
      const counter = document.getElementById('faculty-counter');

      let currentBatch = 1;
      const maxBatches = 3;
      let isFetching = false;

      const extraTeachersBatch2 = [
        { name: "Dr. Khaled Al-Mansoor", dept: "Mathematics", topic: "Higher Algebra", exp: "18+ Yrs Exp", rating: "5.0", badge: "bg-teal-600", img: "images/instructor_portrait.png" },
        { name: "Mariam Fathy", dept: "Physics", topic: "Thermodynamics & Waves", exp: "10+ Yrs Exp", rating: "4.8", badge: "bg-purple-600", img: "images/instructor_female.png" },
        { name: "Youssef Nabil", dept: "Chemistry", topic: "Physical Chemistry", exp: "12+ Yrs Exp", rating: "4.9", badge: "bg-blue-600", img: "images/instructor_male.png" },
        { name: "Amina Reda", dept: "Biology", topic: "Molecular Biology", exp: "15+ Yrs Exp", rating: "4.9", badge: "bg-emerald-600", img: "images/hero_student.png" }
      ];

      function createTeacherCardHTML(t) {
        return `
          <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/90 shadow-lg hover:shadow-2xl card-lift group transition-all duration-300 flex flex-col justify-between h-[440px]">
            <div class="relative h-56 overflow-hidden bg-slate-950">
              <img src="${t.img}" loading="lazy" alt="${t.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
              <span class="absolute top-4 left-4 text-[10px] font-mono font-extrabold text-white ${t.badge} px-3 py-1 rounded-full shadow-md">
                ${t.dept}
              </span>
              <span class="absolute top-4 right-4 text-[10px] font-mono font-extrabold text-white bg-slate-900/80 backdrop-blur-xs px-2.5 py-1 rounded-full border border-white/20">
                ${t.rating} ★
              </span>
            </div>

            <div class="p-5 flex flex-col justify-between flex-1 space-y-3">
              <div class="space-y-1">
                <h3 class="font-heading font-extrabold text-lg text-slate-900 group-hover:text-teal-600 transition-colors">
                  ${t.name}
                </h3>
                <p class="text-xs font-mono text-slate-500 line-clamp-1">${t.topic}</p>
              </div>

              <div class="pt-3 border-t border-slate-100 text-xs font-semibold text-slate-600 space-y-3">
                <div class="flex items-center justify-between font-mono text-[11px]">
                  <span>🎓 Verified Mentor</span>
                  <span>👥 1.2k Students</span>
                </div>
                <a href="{{ route('teacher-profile') }}" class="btn-lift block w-full text-center py-2 bg-teal-50 hover:bg-teal-600 text-teal-700 hover:text-white font-extrabold rounded-xl transition-all">
                  View Profile &rarr;
                </a>
              </div>
            </div>
          </div>
        `;
      }

      function fetchMoreTeachers() {
        if (isFetching || currentBatch >= maxBatches) return;
        isFetching = true;

        spinner.classList.remove('hidden');
        spinner.classList.add('flex');
        skeleton.classList.remove('hidden');
        skeleton.classList.add('grid');

        setTimeout(() => {
          currentBatch++;

          extraTeachersBatch2.forEach(t => {
            grid.insertAdjacentHTML('beforeend', createTeacherCardHTML(t));
          });

          spinner.classList.remove('flex');
          spinner.classList.add('hidden');
          skeleton.classList.remove('grid');
          skeleton.classList.add('hidden');

          const loadedCount = grid.children.length;
          if (counter) counter.textContent = `Showing ${loadedCount} Faculty Members`;

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
            fetchMoreTeachers();
          }
        });
      }, { rootMargin: '200px' });

      if (sentinel) observer.observe(sentinel);
    });
  </script>
@endpush
