
            const openModal = (id) => window.openModal(id);
            const closeModal = (id) => window.closeModal(id);

            function escapeHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
            window.escapeHtml = escapeHtml;

            function escapeJs(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/\\/g, '\\\\')
                    .replace(/'/g, "\\'")
                    .replace(/"/g, '\\"')
                    .replace(/\n/g, '\\n')
                    .replace(/\r/g, '\\r');
            }
            window.escapeJs = escapeJs;

            const isArLocale = false;
            const appBaseUrl = (() => {
                // 1. If currently at /teacher-portal, anything preceding /teacher-portal is the application base path
                const tpIdx = window.location.pathname.indexOf('/teacher-portal');
                if (tpIdx !== -1) {
                    return window.location.origin + window.location.pathname.substring(0, tpIdx);
                }
                // 2. If pathname contains /public
                const match = window.location.pathname.match(/^(.*?\/public)/);
                if (match) {
                    return window.location.origin + match[1];
                }
                // 3. Fallback to Laravel rendered base path
                const serverUrl = "http:\/\/localhost\/elite-academy\/public";
                if (serverUrl && serverUrl.startsWith('http')) {
                    try {
                        const p = new URL(serverUrl);
                        return window.location.origin + p.pathname.replace(/\/+$/, '');
                    } catch (e) {}
                }
                return window.location.origin;
            })();

            // Global i18n Dictionary for Dynamic JS Elements
            const i18n = {
                studentProfile: "Student Profile",
                loadingReview: "Loading review questions and choices breakdown...",
                correct: "Correct",
                incorrect: "Incorrect",
                studentCorrectPick: "Student Selected (Correct)",
                studentWrongPick: "Student Selected (Incorrect)",
                correctKey: "Correct Key",
                explanation: "Explanation:",
                question: "Question",
                noQuestionBreakdown: "No interactive questions logged for this assignment.",
                unableToLoadBreakdown: "Unable to load submission breakdown.",
                noCourses: "No courses enrolled with this instructor.",
                noSessions: "No teaching sessions recorded for this student.",
                noAttendance: "No attendance entries found.",
                noAssignments: "No assignments submitted yet.",
                noNotes: "No educational notes recorded yet for this student.",
                present: "Present",
                late: "Late",
                absent: "Absent",
                excused: "Excused",
                completed: "Completed",
                scheduled: "Scheduled",
                graded: "Graded",
                pendingReview: "Pending Review",
                inProgress: "In Progress",
                passed: "Passed \u003Ci class=\u0022fa-solid fa-check\u0022\u003E\u003C\/i\u003E",
                failed: "Needs Improvement",
            };

            let currentViewingStudentId = null;

            // ── Tab Switcher for Main Teacher Portal ──────────────────────────────────────
            function switchTeacherTab(tabKey) {
                if (!tabKey) return;
                const cleanKey = String(tabKey).replace('#', '').trim();
                const validTabs = ['overview', 'students', 'sessions', 'assignments', 'attendance', 'notifications'];
                const targetKey = validTabs.includes(cleanKey) ? cleanKey : 'overview';

                document.querySelectorAll('.teacher-tab-content').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.teacher-tab-btn').forEach(btn => {
                    btn.classList.remove('bg-teal-600', 'text-white', 'shadow-md', 'active');
                    btn.classList.add('text-slate-700', 'hover:bg-slate-100');
                });

                const activeContent = document.getElementById('teacher-tab-' + targetKey);
                const activeBtn = document.getElementById('tab-btn-' + targetKey);
                if (activeContent) activeContent.classList.remove('hidden');
                if (activeBtn) {
                    activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-100');
                    activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-md', 'active');
                }

                document.querySelectorAll('#portalSidebar .teacher-tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.getAttribute('data-tab') === targetKey) {
                        btn.classList.add('active');
                    }
                });

                // Keep URL hash updated without full page reload or jump
                if (window.history && window.history.replaceState) {
                    try {
                        const cur = new URL(window.location.href);
                        cur.hash = targetKey;
                        window.history.replaceState(null, '', cur.toString());
                    } catch (e) {}
                }

                // Mobile drawer auto-close
                if (window.innerWidth < 1024 && typeof togglePortalSidebar === 'function') {
                    togglePortalSidebar(false);
                }
            }
            window.switchTeacherTab = switchTeacherTab;

            // ── Sub-Tab Switcher for Student Profile Modal ────────────────────────────────
            function switchSpTab(tabKey) {
                document.querySelectorAll('.sp-tab-pane').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.sp-subtab-btn').forEach(btn => {
                    btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
                    btn.classList.add('text-slate-700', 'hover:bg-slate-200/60');
                });

                const activePane = document.getElementById('sp-pane-' + tabKey);
                const activeBtn = document.getElementById('sp-tab-btn-' + tabKey);
                if (activePane) activePane.classList.remove('hidden');
                if (activeBtn) {
                    activeBtn.classList.remove('text-slate-700', 'hover:bg-slate-200/60');
                    activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
                }
            }

            // ── Open Student Profile & Progressive Educational Data Fetch ─────────────────
            async function openStudentDetailsModal(studentUserId) {
                currentViewingStudentId = studentUserId;
                window.openModal('studentProfileModal');

                // Reset skeleton
                document.getElementById('spLoadingSkeleton').classList.remove('hidden');
                document.querySelectorAll('.sp-tab-pane').forEach(el => el.classList.add('hidden'));
                switchSpTab('overview');

                try {
                    const res = await fetch(`${appBaseUrl}/ajax/teacher/students/${studentUserId}/details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    let data = {};
                    try {
                        data = await res.json();
                    } catch (e) {
                        data = {
                            success: false,
                            message: 'Server error'
                        };
                    }

                    if (!res.ok || !data.success) {
                        const errorMsg = data.message || (res.status === 403 ?
                            'Unauthorized: You do not have permission to access this student educational profile.' :
                            'Failed to load student details.');
                        showTeacherToast(errorMsg, false);
                        closeModal('studentProfileModal');
                        return;
                    }

                    document.getElementById('spLoadingSkeleton').classList.add('hidden');
                    document.getElementById('sp-pane-overview').classList.remove('hidden');

                    // 1. Populate Header
                    const st = data.student;
                    const metrics = data.metrics || {};
                    document.getElementById('spModalAvatar').textContent = (st.name || 'S').substring(0, 1).toUpperCase();
                    document.getElementById('spModalName').textContent = st.name || i18n.studentProfile;
                    document.getElementById('spModalCode').textContent = '#' + (st.student_code || 'STU-' + studentUserId);
                    document.getElementById('spModalMeta').textContent =
                        `${st.school || 'Elite Academy'} • ${st.grade || 'Secondary'} • <i class="fa-solid fa-envelope"></i> ${st.email || ''}`;

                    // 2. Populate Overview KPIs
                    document.getElementById('spOverviewAttRate').textContent = `${metrics.attendance_rate || 100}%`;
                    document.getElementById('spOverviewAvgGrade').textContent = metrics.avg_score !== null ?
                        `${metrics.avg_score}%` : 'N/A';
                    document.getElementById('spOverviewTotalSessions').textContent = metrics.total_sessions || 0;
                    document.getElementById('spOverviewSubmissions').textContent = metrics.total_submissions || 0;

                    // Populate Overview Mini Lists
                    const recentSesContainer = document.getElementById('spOverviewRecentSessions');
                    if (data.sessions && data.sessions.length > 0) {
                        let sHtml = '';
                        data.sessions.slice(0, 3).forEach(s => {
                            const attColor = s.attendance_status === 'present' ? 'text-emerald-600' : (s
                                .attendance_status === 'late' ? 'text-amber-600' : 'text-rose-600');
                            sHtml += `<div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">${s.title}</p>
                        <p class="text-[10px] text-slate-400">${s.date}</p>
                    </div>
                    <span class="font-bold uppercase text-[10px] ${attColor}">${s.attendance_status || 'scheduled'}</span>
                </div>`;
                        });
                        recentSesContainer.innerHTML = sHtml;
                    } else {
                        recentSesContainer.innerHTML = `<p class="text-slate-400 italic py-2">${i18n.noSessions}</p>`;
                    }

                    const recentSubContainer = document.getElementById('spOverviewRecentSubmissions');
                    if (data.submissions && data.submissions.length > 0) {
                        let subHtml = '';
                        data.submissions.slice(0, 3).forEach(sub => {
                            const scoreText = sub.score !== null ?
                                `<span class="font-bold text-emerald-600">${sub.score}%</span>` :
                                `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                            subHtml += `<div class="p-2.5 rounded-xl bg-white border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">${sub.assignment_title}</p>
                        <p class="text-[10px] text-slate-400">${sub.submitted_at || 'Draft'}</p>
                    </div>
                    <div>${scoreText}</div>
                </div>`;
                        });
                        recentSubContainer.innerHTML = subHtml;
                    } else {
                        recentSubContainer.innerHTML = `<p class="text-slate-400 italic py-2">${i18n.noAssignments}</p>`;
                    }

                    // 3. Populate Courses Tab
                    const coursesList = document.getElementById('spCoursesList');
                    if (data.courses && data.courses.length > 0) {
                        let cHtml = '';
                        data.courses.forEach(c => {
                            cHtml += `<div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="font-heading font-bold text-sm text-slate-900">${c.title}</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-100 text-teal-800">${c.status}</span>
                    </div>
                    <p class="text-xs font-mono text-slate-500">${c.subject} • ${c.grade} • Enrolled: ${c.enrolled_at}</p>
                    <div class="space-y-1 pt-1">
                        <div class="flex justify-between text-[11px] font-mono text-slate-600">
                            <span>Syllabus Progress</span>
                            <span class="font-bold">${c.completed_sessions} / ${c.sessions_count} sessions (${c.progress_pct}%)</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-teal-600 h-full rounded-full" style="width: ${c.progress_pct}%"></div>
                        </div>
                    </div>
                </div>`;
                        });
                        coursesList.innerHTML = cHtml;
                    } else {
                        coursesList.innerHTML =
                            `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noCourses}</p>`;
                    }

                    // 4. Populate Sessions Tab
                    const sesTableBody = document.getElementById('spSessionsTableBody');
                    if (data.sessions && data.sessions.length > 0) {
                        let sesHtml = '';
                        data.sessions.forEach(s => {
                            const attBadge = s.attendance_status === 'present' ?
                                '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> Present</span>' :
                                (s.attendance_status === 'late' ?
                                    '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full font-bold"><i class="fa-solid fa-circle text-amber-500 text-[10px]"></i> Late</span>' :
                                    '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-full font-bold"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> Absent</span>'
                                    );
                            sesHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${s.title}</td>
                    <td class="py-2.5 px-3 text-slate-600">${s.course_title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${s.date}</td>
                    <td class="py-2.5 px-3 font-mono">${attBadge}</td>
                </tr>`;
                        });
                        sesTableBody.innerHTML = sesHtml;
                    } else {
                        sesTableBody.innerHTML =
                            `<tr><td colspan="4" class="py-4 text-center text-slate-400 italic">${i18n.noSessions}</td></tr>`;
                    }

                    // 5. Populate Attendance Tab
                    const attTableBody = document.getElementById('spAttendanceTableBody');
                    if (data.attendance && data.attendance.length > 0) {
                        let attHtml = '';
                        data.attendance.forEach(a => {
                            const statusStr = a.attendance_status || 'scheduled';
                            attHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${a.title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${a.date}</td>
                    <td class="py-2.5 px-3 font-mono text-right rtl:text-left">
                        <span class="font-bold uppercase text-[10px] px-2 py-0.5 rounded-full ${statusStr === 'present' ? 'bg-emerald-100 text-emerald-800' : (statusStr === 'late' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')}">${statusStr}</span>
                    </td>
                </tr>`;
                        });
                        attTableBody.innerHTML = attHtml;
                    } else {
                        attTableBody.innerHTML =
                            `<tr><td colspan="3" class="py-4 text-center text-slate-400 italic">${i18n.noAttendance}</td></tr>`;
                    }

                    // 6. Populate Assignments Tab
                    const assignTableBody = document.getElementById('spAssignmentsTableBody');
                    if (data.submissions && data.submissions.length > 0) {
                        let assHtml = '';
                        data.submissions.forEach(sub => {
                            const scoreDisplay = sub.score !== null ?
                                `<span class="font-bold text-emerald-600">${sub.score}%</span>` :
                                `<span class="text-orange-500 italic">${i18n.pendingReview}</span>`;
                            assHtml += `<tr class="hover:bg-slate-50">
                    <td class="py-2.5 px-3 font-bold text-slate-900">${sub.assignment_title}</td>
                    <td class="py-2.5 px-3 font-mono text-slate-500">${sub.submitted_at || 'Draft'}</td>
                    <td class="py-2.5 px-3 font-mono">${scoreDisplay}</td>
                    <td class="py-2.5 px-3 text-right rtl:text-left">
                        <button type="button" onclick="openGradeModal(${sub.id}, '${st.name ? st.name.replace(/'/g, "\\'") : ''}', '${sub.assignment_title.replace(/'/g, "\\'")}', '${sub.score}', '${sub.evaluation_notes ? sub.evaluation_notes.replace(/'/g, "\\'") : ''}')" class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-[11px] rounded-lg border border-teal-200">
                            <i class="fa-solid fa-magnifying-glass"></i> Review
                        </button>
                    </td>
                </tr>`;
                        });
                        assignTableBody.innerHTML = assHtml;
                    } else {
                        assignTableBody.innerHTML =
                            `<tr><td colspan="4" class="py-4 text-center text-slate-400 italic">${i18n.noAssignments}</td></tr>`;
                    }

                    // 7. Populate Assessments & Quizzes Tab
                    const assessContainer = document.getElementById('spAssessmentsContainer');
                    if (data.assessments && data.assessments.length > 0) {
                        let assessHtml = '';
                        data.assessments.forEach(ass => {
                            const passBadge = ass.is_passed ?
                                '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-check"></i> Passed</span>' :
                                (ass.score !== null ?
                                    '<span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-full"><i class="fa-solid fa-xmark me-1"></i> Retake</span>' :
                                    '<span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-full">Pending</span>'
                                    );
                            assessHtml += `<div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <p class="font-bold text-slate-900">${ass.assignment_title}</p>
                        <p class="text-[10px] font-mono text-slate-500">${ass.course_title} • ${ass.submitted_at}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-extrabold text-sm ${ass.score >= 70 ? 'text-emerald-600' : 'text-slate-800'}">${ass.score !== null ? ass.score + '%' : 'N/A'}</span>
                        ${passBadge}
                    </div>
                </div>`;
                        });
                        assessContainer.innerHTML = assessHtml;
                    } else {
                        assessContainer.innerHTML =
                            `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noAssignments}</p>`;
                    }

                    // 8. Populate Progress & Analytics Tab
                    const progContainer = document.getElementById('spProgressContainer');
                    progContainer.innerHTML = `
            <div class="p-5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-3">
                <h4 class="font-heading font-black text-sm text-slate-900">Comprehensive Academic Health</h4>
                <div class="grid grid-cols-2 gap-3 text-center text-xs font-mono">
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 block text-[10px] uppercase">Attendance Consistency</span>
                        <span class="font-extrabold text-base text-emerald-600">${metrics.attendance_rate || 100}%</span>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                        <span class="text-slate-400 block text-[10px] uppercase">Assessment Pass Rate</span>
                        <span class="font-extrabold text-base text-teal-600">${metrics.pass_rate || 100}%</span>
                    </div>
                </div>
            </div>
        `;

                    // 9. Populate Notes Tab
                    renderEducationalNotes(data.notes || []);

                } catch (err) {
                    document.getElementById('spLoadingSkeleton').innerHTML =
                        `<p class="text-xs text-rose-600 italic py-6">Failed to load student details.</p>`;
                }
            }

            function renderEducationalNotes(notes) {
                const container = document.getElementById('spNotesContainer');
                if (!container) return;

                if (notes && notes.length > 0) {
                    let nHtml = '';
                    notes.forEach(n => {
                        const catBadge = {
                                academic: '<span class="px-2 py-0.5 bg-teal-100 text-teal-800 rounded-full text-[10px] font-bold">Academic</span>',
                                homework: '<span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[10px] font-bold">Homework</span>',
                                participation: '<span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full text-[10px] font-bold">Participation</span>',
                                behavior: '<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">Behavior</span>',
                                general: '<span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-full text-[10px] font-bold">General</span>',
                            } [n.category] ||
                            '<span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded-full text-[10px] font-bold">Note</span>';

                        nHtml += `<div class="p-3.5 rounded-2xl bg-[#FAFAF9] border border-slate-200/90 space-y-1.5 text-xs">
                <div class="flex items-center justify-between">
                    ${catBadge}
                    <span class="text-[10px] font-mono text-slate-400">${n.created_at_human || n.created_at}</span>
                </div>
                <p class="text-slate-800 font-medium leading-relaxed">${n.note}</p>
            </div>`;
                    });
                    container.innerHTML = nHtml;
                } else {
                    container.innerHTML = `<p class="text-xs text-slate-400 italic text-center py-6">${i18n.noNotes}</p>`;
                }
            }

            // ── Open Add Note Modal for Current Student ──────────────────────────────────
            function openAddNoteModal() {
                if (!currentViewingStudentId) return;
                document.getElementById('noteStudentUserId').value = currentViewingStudentId;
                document.getElementById('addNoteForm').reset();
                window.openModal('addNoteModal');
            }

            // ── Client-Side Real-Time Filter for My Students Roster ───────────────────────
            function applyStudentFilters() {
                const searchVal = (document.getElementById('studentSearchInput')?.value || '').trim().toLowerCase();
                const courseVal = document.getElementById('studentCourseFilter')?.value || '';
                const gradeVal = document.getElementById('studentGradeFilter')?.value || '';
                const attVal = document.getElementById('studentAttendanceFilter')?.value || '';

                const cards = document.querySelectorAll('.student-roster-card');
                let visibleCount = 0;

                cards.forEach(card => {
                    const name = card.getAttribute('data-name') || '';
                    const code = card.getAttribute('data-code') || '';
                    const school = card.getAttribute('data-school') || '';
                    const courses = (card.getAttribute('data-courses') || '').split(',');
                    const grade = card.getAttribute('data-grade') || '';
                    const attendance = parseInt(card.getAttribute('data-attendance') || '100', 10);

                    let matchSearch = !searchVal || name.includes(searchVal) || code.includes(searchVal) || school
                        .includes(searchVal);
                    let matchCourse = !courseVal || courses.includes(courseVal);
                    let matchGrade = !gradeVal || grade === gradeVal;
                    let matchAtt = !attVal || (attVal === 'good' && attendance >= 80) || (attVal === 'risk' &&
                        attendance < 80);

                    if (matchSearch && matchCourse && matchGrade && matchAtt) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                const emptyState = document.getElementById('studentEmptySearchState');
                if (emptyState) {
                    if (visibleCount === 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }

                const countText = document.getElementById('studentFilterCountText');
                if (countText) {
                    countText.textContent = `Showing ${visibleCount} of ${cards.length} students`;
                }
            }

            function resetStudentFilters() {
                if (document.getElementById('studentSearchInput')) document.getElementById('studentSearchInput').value = '';
                if (document.getElementById('studentCourseFilter')) document.getElementById('studentCourseFilter').value = '';
                if (document.getElementById('studentGradeFilter')) document.getElementById('studentGradeFilter').value = '';
                if (document.getElementById('studentAttendanceFilter')) document.getElementById('studentAttendanceFilter')
                    .value = '';
                applyStudentFilters();
            }

            // ── Open Modals & Action Helpers ─────────────────────────────────────────────
            function openCreateSessionModal() {
                window.openModal('createSessionModal');
            }

            function openCreateAssignmentModal() {
                window.openModal('createAssignmentModal');
            }

            async function openAssignmentDetailsModal(assignmentId) {
                window.openModal('assignmentDetailsModal');

                document.getElementById('adLoadingSkeleton').classList.remove('hidden');
                document.getElementById('adModalContent').classList.add('hidden');
                switchAdSubTab('questions');

                try {
                    const res = await fetch(`${appBaseUrl}/ajax/teacher/assignments/${assignmentId}/details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    let data = {};
                    try {
                        data = await res.json();
                    } catch (e) {
                        data = {
                            success: false
                        };
                    }

                    if (!res.ok || !data.success) {
                        showTeacherToast(data.message || 'Failed to load assignment details.', false);
                        closeModal('assignmentDetailsModal');
                        return;
                    }

                    const a = data.assignment;
                    const stats = data.stats || {};

                    document.getElementById('adModalCourse').textContent = a.course_title;
                    document.getElementById('adModalSubject').textContent = a.subject_name || '';
                    document.getElementById('adModalStatus').textContent = (a.status || 'published').toUpperCase();
                    document.getElementById('adModalTitle').textContent = a.title;
                    document.getElementById('adModalDescription').textContent = a.description ||
                        'No instructions provided.';

                    document.getElementById('adDueAt').textContent = a.due_at || 'No deadline';
                    document.getElementById('adDueHuman').textContent = a.due_at_human || '';
                    document.getElementById('adPassScore').textContent = a.passing_score + '%';
                    document.getElementById('adQuestionsCount').textContent = a.total_questions || 0;
                    document.getElementById('adDuration').textContent = a.duration_minutes ? a.duration_minutes +
                        ' min' : '';
                    document.getElementById('adSubmissionsCount').textContent = stats.total_submissions || 0;
                    document.getElementById('adAvgScore').textContent = stats.avg_score !== null ?
                        'Avg: ' + stats.avg_score + '%' : '';

                    document.getElementById('adQuestionsBadge').textContent = (data.questions || []).length;
                    document.getElementById('adSubmissionsBadge').textContent = (data.submissions || []).length;

                    // Render Questions
                    const qContainer = document.getElementById('adQuestionsList');
                    const noQNotice = document.getElementById('adNoQuestionsNotice');
                    if (data.questions && data.questions.length > 0) {
                        noQNotice.classList.add('hidden');
                        let qHtml = '';
                        data.questions.forEach((q, idx) => {
                            let optsHtml = '';
                            (q.options || []).forEach(opt => {
                                const isCorrect = opt.is_correct;
                                optsHtml += `
                        <div class="p-2.5 rounded-xl border ${isCorrect ? 'bg-emerald-50/90 border-emerald-300 text-emerald-950 font-bold shadow-2xs' : 'bg-white border-slate-200 text-slate-700'} flex items-start justify-between gap-2 text-xs">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-5 h-5 rounded-lg ${isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500'} flex items-center justify-center shrink-0 text-[10px]">
                                    ${isCorrect ? '<i class="fa-solid fa-check"></i>' : '•'}
                                </span>
                                <span class="break-words">${escapeHtml(opt.option_text)}</span>
                            </div>
                            ${isCorrect ? '<span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded-md bg-emerald-200 text-emerald-900 shrink-0 font-extrabold"><i class="fa-solid fa-circle-check me-1"></i>Correct Answer</span>' : ''}
                        </div>
                    `;
                            });

                            qHtml += `
                    <div class="p-4 rounded-2xl bg-[#FAFAF9] border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-heading font-black text-xs text-teal-800 bg-teal-100/70 px-2.5 py-1 rounded-lg">#${idx + 1}</span>
                            <span class="text-[11px] font-mono font-bold text-slate-500">${q.points} pts</span>
                        </div>
                        <p class="text-sm font-bold text-slate-900 leading-snug">${escapeHtml(q.question_text)}</p>
                        <div class="space-y-1.5 pt-1">
                            ${optsHtml}
                        </div>
                    </div>
                `;
                        });
                        qContainer.innerHTML = qHtml;
                    } else {
                        qContainer.innerHTML = '';
                        noQNotice.classList.remove('hidden');
                    }

                    // Render Submissions
                    const subTBody = document.getElementById('adSubmissionsTableBody');
                    const noSubNotice = document.getElementById('adNoSubmissionsNotice');
                    const subTableContainer = document.getElementById('adSubmissionsTableContainer');

                    if (data.submissions && data.submissions.length > 0) {
                        noSubNotice.classList.add('hidden');
                        subTableContainer.classList.remove('hidden');
                        let subHtml = '';

                        data.submissions.forEach(s => {
                            const isPassed = s.is_passed;
                            const scoreText = s.score !== null ? `${s.score}%` : 'Pending Grade';
                            const scoreClass = s.score !== null ? (isPassed ? 'text-emerald-600 font-extrabold' :
                                'text-rose-600 font-extrabold') : 'text-slate-400 italic';
                            const statusBadgeClass = s.status === 'reviewed' ? 'bg-emerald-100 text-emerald-800' :
                                'bg-amber-100 text-amber-800';

                            subHtml += `
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 px-3">
                            <p class="font-bold text-slate-900">${escapeHtml(s.student_name)}</p>
                            <p class="text-[10px] text-slate-500 font-mono">${escapeHtml(s.student_email)}</p>
                        </td>
                        <td class="py-3 px-3 font-mono text-slate-600">${s.submitted_at || 'Draft'}</td>
                        <td class="py-3 px-3 font-mono ${scoreClass}">${scoreText}</td>
                        <td class="py-3 px-3 font-mono">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${statusBadgeClass}">
                                ${escapeHtml(s.status)}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right rtl:text-left">
                            <button type="button" onclick="closeModal('assignmentDetailsModal'); openGradeModal(${s.id}, '${escapeJs(s.student_name)}', '${escapeJs(a.title)}', '${s.score !== null ? s.score : ''}', '${escapeJs(s.evaluation_notes || '')}')" class="btn-lift px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                <i class="fa-solid fa-magnifying-glass"></i> Review &amp; Grade
                            </button>
                        </td>
                    </tr>
                `;
                        });
                        subTBody.innerHTML = subHtml;
                    } else {
                        subTBody.innerHTML = '';
                        subTableContainer.classList.add('hidden');
                        noSubNotice.classList.remove('hidden');
                    }

                    document.getElementById('adLoadingSkeleton').classList.add('hidden');
                    document.getElementById('adModalContent').classList.remove('hidden');

                } catch (err) {
                    showTeacherToast('Failed to load assignment details.', false);
                    closeModal('assignmentDetailsModal');
                }
            }

            function switchAdSubTab(tab) {
                document.querySelectorAll('.ad-pane').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.ad-subtab-btn').forEach(btn => {
                    btn.classList.remove('bg-teal-600', 'text-white', 'shadow-xs');
                    btn.classList.add('text-slate-600');
                });

                const pane = document.getElementById('ad-pane-' + tab);
                const btn = document.getElementById('ad-tab-btn-' + tab);
                if (pane) pane.classList.remove('hidden');
                if (btn) {
                    btn.classList.remove('text-slate-600');
                    btn.classList.add('bg-teal-600', 'text-white', 'shadow-xs');
                }
            }

            function openMeetingLinkModal(sessionId, currentLinkOrEl) {
                let currentLink = '';
                if (currentLinkOrEl && typeof currentLinkOrEl === 'object' && currentLinkOrEl.nodeType) {
                    currentLink = currentLinkOrEl.getAttribute('data-meeting-link') || '';
                } else if (typeof currentLinkOrEl === 'string') {
                    currentLink = currentLinkOrEl;
                }
                document.getElementById('linkSessionId').value = sessionId;
                document.getElementById('meetingUrlInput').value = currentLink || '';
                document.getElementById('meetingLinkForm').action = `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/link`;
                window.openModal('meetingLinkModal');
            }

            function openRescheduleModal(sessionId, currentDateTimeOrEl) {
                let currentDateTime = '';
                if (currentDateTimeOrEl && typeof currentDateTimeOrEl === 'object' && currentDateTimeOrEl.nodeType) {
                    currentDateTime = currentDateTimeOrEl.getAttribute('data-scheduled-at') || '';
                } else if (typeof currentDateTimeOrEl === 'string') {
                    currentDateTime = currentDateTimeOrEl;
                }
                document.getElementById('rescheduleSessionId').value = sessionId;
                document.getElementById('rescheduleDateTime').value = currentDateTime || '';
                document.getElementById('rescheduleForm').action =
                `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/reschedule`;
                window.openModal('rescheduleModal');
            }

            async function openAttendanceModal(sessionId, sessionTitleOrEl) {
                if (!sessionId) return;

                let sessionTitle = '';
                if (sessionTitleOrEl && typeof sessionTitleOrEl === 'object' && sessionTitleOrEl.nodeType) {
                    sessionTitle = sessionTitleOrEl.getAttribute('data-session-title') || sessionTitleOrEl.getAttribute(
                        'data-title') || '';
                } else if (typeof sessionTitleOrEl === 'string') {
                    sessionTitle = sessionTitleOrEl;
                }

                if (!sessionTitle) {
                    const triggerBtn = document.querySelector(`button[data-session-id="${sessionId}"]`);
                    if (triggerBtn) {
                        sessionTitle = triggerBtn.getAttribute('data-session-title') || triggerBtn.getAttribute(
                            'data-title') || '';
                    }
                }

                const sessionInput = document.getElementById('attendanceSessionId');
                if (sessionInput) sessionInput.value = sessionId;

                const titleEl = document.getElementById('attendanceSessionTitle');
                if (titleEl) titleEl.textContent = sessionTitle || (isArLocale ? 'جاري التحميل...' : 'Loading...');

                const formEl = document.getElementById('attendanceForm');
                if (formEl) formEl.action = `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance`;

                const searchInput = document.getElementById('attModalSearchInput');
                if (searchInput) searchInput.value = '';

                const container = document.getElementById('attendanceStudentsContainer');
                if (container) {
                    container.innerHTML = `
            <div class="py-8 space-y-3">
                <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-2xl animate-pulse">
                    <div class="w-11 h-11 rounded-xl bg-slate-200 shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 bg-slate-200 rounded-md w-1/3"></div>
                        <div class="h-2.5 bg-slate-200 rounded-md w-1/4"></div>
                    </div>
                    <div class="w-48 h-8 bg-slate-200 rounded-xl shrink-0"></div>
                </div>
                <div class="flex items-center gap-3 p-3.5 bg-slate-50 rounded-2xl animate-pulse">
                    <div class="w-11 h-11 rounded-xl bg-slate-200 shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 bg-slate-200 rounded-md w-1/2"></div>
                        <div class="h-2.5 bg-slate-200 rounded-md w-1/5"></div>
                    </div>
                    <div class="w-48 h-8 bg-slate-200 rounded-xl shrink-0"></div>
                </div>
            </div>
        `;
                }

                const countBadge = document.getElementById('attendanceCohortCount');
                if (countBadge) countBadge.textContent = '...';

                window.openModal('attendanceModal');

                try {
                    const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance-roster`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    let data = {};
                    try {
                        data = await res.json();
                    } catch (e) {
                        data = {
                            success: false,
                            message: 'Server error'
                        };
                    }

                    if (!res.ok || !data.success) {
                        if (container) {
                            container.innerHTML = `
                    <div class="py-10 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl shadow-xs border border-amber-200">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-slate-800">${escapeHtml(data.message || (isArLocale ? 'تعذر جلب كشف الحضور للجلسة.' : 'Could not load session attendance roster.'))}</p>
                            <p class="text-[11px] font-mono text-slate-400">${isArLocale ? 'يرجى التحقق من الصلاحيات والمحاولة مجدداً.' : 'Please verify permissions and retry.'}</p>
                        </div>
                        <button type="button" onclick="openAttendanceModal(${sessionId}, '${escapeJs(sessionTitle)}')" class="btn-lift px-3.5 py-1.5 bg-teal-600 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                            <i class="fa-solid fa-rotate-right"></i> ${isArLocale ? 'إعادة المحاولة' : 'Retry'}
                        </button>
                    </div>
                `;
                        }
                        return;
                    }

                    if (data.session && titleEl) {
                        titleEl.textContent =
                            `${data.session.title} ${data.session.course_title ? '— ' + data.session.course_title : ''}`;
                    }

                    const students = data.students || [];
                    if (countBadge) countBadge.textContent = students.length;

                    if (students.length === 0) {
                        if (container) {
                            container.innerHTML = `
                    <div class="py-12 text-center space-y-3">
                        <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto text-2xl shadow-2xs border border-teal-100">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-800">${isArLocale ? 'لا يوجد طلاب مسجلين في هذا الكورس حالياً.' : 'No students enrolled in this course yet.'}</p>
                            <p class="text-xs font-mono text-slate-400">${isArLocale ? 'سيظهر الطلاب المسجلون تلقائياً بمجرد انضمامهم إلى الكورس.' : 'Enrolled students will appear here automatically.'}</p>
                        </div>
                    </div>
                `;
                        }
                        return;
                    }

                    let html = '';
                    students.forEach((st, idx) => {
                        const curStatus = st.status || 'present';
                        const isPresent = curStatus === 'present';
                        const isLate = curStatus === 'late';
                        const isExcused = curStatus === 'excused';
                        const isAbsent = curStatus === 'absent';

                        html += `
                <div class="att-student-item p-3.5 sm:p-4 rounded-2xl bg-white hover:bg-slate-50/80 transition-all border border-slate-200/80 hover:border-teal-300 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs"
                     data-student-name="${escapeHtml(st.name || '')}"
                     data-student-code="${escapeHtml(st.code || st.student_code || '')}"
                     data-student-school="${escapeHtml(st.school || '')}">
                    
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-teal-600 to-emerald-500 text-white font-heading font-black text-sm flex items-center justify-center shrink-0 shadow-xs">
                            ${(st.name || 'S').substring(0, 1).toUpperCase()}
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <p class="text-xs sm:text-sm font-bold text-slate-900 truncate">${escapeHtml(st.name)}</p>
                            <p class="text-[11px] font-mono text-slate-500 truncate flex items-center gap-1.5 flex-wrap">
                                <span>${escapeHtml(st.school || 'Elite Academy')}</span>
                                ${st.grade ? `<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span><span>${escapeHtml(st.grade)}</span>` : ''}
                                ${(st.code || st.student_code) ? `<span class="inline-block w-1 h-1 rounded-full bg-slate-300"></span><span class="text-slate-400">#${escapeHtml(st.code || st.student_code)}</span>` : ''}
                            </p>
                        </div>
                    </div>

                    <input type="hidden" name="attendance[${idx}][student_user_id]" value="${st.id}">
                    
                    <div class="w-full sm:w-auto shrink-0 flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200/90">
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="present" ${isPresent ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'present', this)" class="peer sr-only att-status-radio att-radio-present">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-emerald-800 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                <span>${isArLocale ? 'حاضر' : 'Present'}</span>
                            </span>
                        </label>
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="late" ${isLate ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'late', this)" class="peer sr-only att-status-radio att-radio-late">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-amber-800 peer-checked:bg-amber-500 peer-checked:text-slate-950 peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-clock text-[10px]"></i>
                                <span>${isArLocale ? 'متأخر' : 'Late'}</span>
                            </span>
                        </label>
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="excused" ${isExcused ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'excused', this)" class="peer sr-only att-status-radio att-radio-excused">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-slate-800 peer-checked:bg-slate-600 peer-checked:text-white peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-user-shield text-[10px]"></i>
                                <span>${isArLocale ? 'معذور' : 'Excused'}</span>
                            </span>
                        </label>
                        <label class="flex-1 sm:flex-none cursor-pointer">
                            <input type="radio" name="attendance[${idx}][status]" value="absent" ${isAbsent ? 'checked' : ''} onchange="onAttendanceStatusRadioChange('${escapeJs(st.name)}', 'absent', this)" class="peer sr-only att-status-radio att-radio-absent">
                            <span class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold font-mono transition-all flex items-center justify-center gap-1 text-slate-600 hover:text-rose-800 peer-checked:bg-rose-600 peer-checked:text-white peer-checked:shadow-xs select-none">
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                <span>${isArLocale ? 'غائب' : 'Absent'}</span>
                            </span>
                        </label>
                    </div>
                </div>
            `;
                    });

                    if (container) container.innerHTML = html;

                } catch (err) {
                    if (container) {
                        container.innerHTML = `
                <div class="py-10 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto text-xl shadow-xs border border-rose-100">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-rose-700">${isArLocale ? 'تعذر تحميل كشف حضور الطلاب في الوقت الفعلي.' : 'Failed to load real-time attendance roster.'}</p>
                        <p class="text-[11px] font-mono text-slate-400">${isArLocale ? 'يرجى التحقق من الاتصال بالإنترنت والمحاولة مجدداً.' : 'Please check your connection and try again.'}</p>
                    </div>
                    <button type="button" onclick="openAttendanceModal(${sessionId}, '${escapeJs(sessionTitle)}')" class="btn-lift px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-rotate-right"></i> ${isArLocale ? 'إعادة المحاولة' : 'Retry'}
                    </button>
                </div>
            `;
                    }
                }
            }

            function onAttendanceStatusRadioChange(studentName, newStatus, radioEl) {
                const statusMap = {
                    present: {
                        title: isArLocale ? 'تسجيل حضور' : 'Attendance Check',
                        msg: isArLocale ? `تم تحديد (${studentName}) كـ حاضر` : `Marked (${studentName}) as Present`,
                        type: 'success'
                    },
                    late: {
                        title: isArLocale ? 'تسجيل تأخير' : 'Attendance Check',
                        msg: isArLocale ? `تم تحديد (${studentName}) كـ متأخر` : `Marked (${studentName}) as Late`,
                        type: 'warning'
                    },
                    excused: {
                        title: isArLocale ? 'تسجيل عذر' : 'Attendance Check',
                        msg: isArLocale ? `تم تحديد (${studentName}) كـ معذور` : `Marked (${studentName}) as Excused`,
                        type: 'info'
                    },
                    absent: {
                        title: isArLocale ? 'تسجيل غياب' : 'Attendance Check',
                        msg: isArLocale ? `تم تحديد (${studentName}) كـ غائب` : `Marked (${studentName}) as Absent`,
                        type: 'danger'
                    },
                };

                const cfg = statusMap[newStatus] || {
                    title: 'Attendance',
                    msg: 'Status updated',
                    type: 'info'
                };
                if (window.Toast) {
                    window.Toast.show({
                        type: cfg.type,
                        title: cfg.title,
                        message: cfg.msg,
                        duration: 2000
                    });
                }
            }

            function filterAttendanceModalStudents(query) {
                const q = (query || '').trim().toLowerCase();
                const studentRows = document.querySelectorAll('.att-student-item');
                studentRows.forEach(row => {
                    const name = (row.getAttribute('data-student-name') || '').toLowerCase();
                    const code = (row.getAttribute('data-student-code') || '').toLowerCase();
                    const school = (row.getAttribute('data-student-school') || '').toLowerCase();
                    if (!q || name.includes(q) || code.includes(q) || school.includes(q)) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            }

            function bulkSetAttendance(status) {
                const targetRadios = document.querySelectorAll(`.att-radio-${status}`);
                targetRadios.forEach(radio => {
                    radio.checked = true;
                });

                if (window.Toast) {
                    if (status === 'present') {
                        window.Toast.success(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ حضور' :
                            'All students marked as Present', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 2500);
                    } else if (status === 'late') {
                        window.Toast.warning(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ متأخرين' :
                            'All students marked as Late', isArLocale ? 'تحديث جماعي' : 'Bulk Update', 2500);
                    } else if (status === 'absent') {
                        window.Toast.danger(isArLocale ? 'تم تحديد جميع طلاب الجلسة كـ غياب' : 'All students marked as Absent',
                            isArLocale ? 'تحديث جماعي' : 'Bulk Update', 2500);
                    }
                }
            }

            window.openAttendanceModal = openAttendanceModal;
            window.filterAttendanceModalStudents = filterAttendanceModalStudents;
            window.bulkSetAttendance = bulkSetAttendance;
            window.onAttendanceStatusRadioChange = onAttendanceStatusRadioChange;
            window.openCreateSessionModal = openCreateSessionModal;
            window.openCreateAssignmentModal = openCreateAssignmentModal;
            window.openAssignmentDetailsModal = openAssignmentDetailsModal;
            window.openMeetingLinkModal = openMeetingLinkModal;
            window.openRescheduleModal = openRescheduleModal;
            window.openGradeModal = openGradeModal;
            window.confirmCancelSession = confirmCancelSession;
            window.openStudentDetailsModal = openStudentDetailsModal;
            window.switchTeacherTab = switchTeacherTab;

            // ── View Switcher & Toolbar Filter Helpers for Attendance Tab ───────────────
            window.switchAttView = function(viewType) {
                document.querySelectorAll('.att-view-pane').forEach(el => el.classList.add('hidden'));
                const pane = document.getElementById(viewType === 'cards' ? 'attViewCards' : (viewType === 'table' ?
                    'attViewTable' : 'attViewMatrix'));
                if (pane) pane.classList.remove('hidden');

                const btnCards = document.getElementById('attViewBtnCards');
                const btnTable = document.getElementById('attViewBtnTable');
                const btnMatrix = document.getElementById('attViewBtnMatrix');

                [btnCards, btnTable, btnMatrix].forEach(b => {
                    if (b) {
                        b.classList.remove('bg-teal-600', 'text-white', 'shadow-2xs');
                        b.classList.add('text-slate-600');
                    }
                });

                const activeBtn = viewType === 'cards' ? btnCards : (viewType === 'table' ? btnTable : btnMatrix);
                if (activeBtn) {
                    activeBtn.classList.remove('text-slate-600');
                    activeBtn.classList.add('bg-teal-600', 'text-white', 'shadow-2xs');
                }
            };

            window.applyAttendanceFilters = function() {
                const searchVal = (document.getElementById('attSearchInput')?.value || '').trim().toLowerCase();
                const courseVal = document.getElementById('attCourseFilter')?.value || '';
                const statusVal = document.getElementById('attStatusFilter')?.value || '';

                const cards = document.querySelectorAll('.att-session-card');
                const rows = document.querySelectorAll('.att-table-row');
                let visibleCount = 0;

                cards.forEach(card => {
                    const titleStr = card.getAttribute('data-title') || '';
                    const courseId = card.getAttribute('data-course') || '';
                    const isRecorded = card.getAttribute('data-recorded') === '1';
                    const isToday = card.getAttribute('data-today') === '1';

                    let matchSearch = !searchVal || titleStr.includes(searchVal);
                    let matchCourse = !courseVal || courseId === courseVal;
                    let matchStatus = true;
                    if (statusVal === 'recorded') matchStatus = isRecorded;
                    else if (statusVal === 'pending') matchStatus = !isRecorded;
                    else if (statusVal === 'today') matchStatus = isToday;

                    if (matchSearch && matchCourse && matchStatus) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                rows.forEach(row => {
                    const titleStr = row.getAttribute('data-title') || '';
                    const courseId = row.getAttribute('data-course') || '';
                    const isRecorded = row.getAttribute('data-recorded') === '1';
                    const isToday = row.getAttribute('data-today') === '1';

                    let matchSearch = !searchVal || titleStr.includes(searchVal);
                    let matchCourse = !courseVal || courseId === courseVal;
                    let matchStatus = true;
                    if (statusVal === 'recorded') matchStatus = isRecorded;
                    else if (statusVal === 'pending') matchStatus = !isRecorded;
                    else if (statusVal === 'today') matchStatus = isToday;

                    if (matchSearch && matchCourse && matchStatus) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });

                const emptyState = document.getElementById('attEmptySearchState');
                if (emptyState) {
                    if (visibleCount === 0 && cards.length > 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }

                const countBadge = document.getElementById('attCountBadge');
                if (countBadge) countBadge.textContent = visibleCount;
            };

            window.setAttendanceFilter = function(type) {
                const sel = document.getElementById('attStatusFilter');
                if (sel) {
                    sel.value = type;
                    applyAttendanceFilters();
                }
            };

            window.resetAttendanceFilters = function() {
                if (document.getElementById('attSearchInput')) document.getElementById('attSearchInput').value = '';
                if (document.getElementById('attCourseFilter')) document.getElementById('attCourseFilter').value = '';
                if (document.getElementById('attStatusFilter')) document.getElementById('attStatusFilter').value = '';
                applyAttendanceFilters();
            };

            async function confirmCancelSession(sessionId) {
                if (!confirm("Are you sure you want to cancel this live session? Affected students will be notified immediately.")) {
                    return;
                }

                try {
                    const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sessionId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await res.json();
                    showTeacherToast(data.message, data.success);
                    if (data.success) {
                        setTimeout(() => location.reload(), 900);
                    }
                } catch (err) {
                    showTeacherToast('Failed to cancel session', false);
                }
            }

            async function openGradeModal(submissionId, triggerElOrStudentName, assignmentTitle, currentScore,
            evaluationNotes) {
                if (!submissionId) return;

                let studentName = '';
                if (triggerElOrStudentName && typeof triggerElOrStudentName === 'object' && triggerElOrStudentName
                    .nodeType) {
                    studentName = triggerElOrStudentName.getAttribute('data-student-name') || '';
                    assignmentTitle = triggerElOrStudentName.getAttribute('data-assignment-title') || '';
                    currentScore = triggerElOrStudentName.getAttribute('data-score') || '';
                    evaluationNotes = triggerElOrStudentName.getAttribute('data-evaluation-notes') || '';
                } else if (typeof triggerElOrStudentName === 'string') {
                    studentName = triggerElOrStudentName;
                } else {
                    const btn = document.querySelector(`button[data-submission-id="${submissionId}"]`);
                    if (btn) {
                        studentName = btn.getAttribute('data-student-name') || '';
                        assignmentTitle = btn.getAttribute('data-assignment-title') || assignmentTitle || '';
                        currentScore = btn.getAttribute('data-score') || currentScore || '';
                        evaluationNotes = btn.getAttribute('data-evaluation-notes') || evaluationNotes || '';
                    }
                }

                const subIdInput = document.getElementById('gradeSubmissionId');
                if (subIdInput) subIdInput.value = submissionId;

                const nameEl = document.getElementById('gradeStudentName');
                if (nameEl) nameEl.textContent = studentName ? `${studentName} — ${assignmentTitle || ''}` : (isArLocale ?
                    'جاري التحميل...' : 'Loading...');

                const scoreInput = document.getElementById('gradeScoreInput');
                if (scoreInput) scoreInput.value = (currentScore && currentScore !== 'null' && currentScore !==
                    'undefined') ? currentScore : '';

                const notesEl = document.getElementById('gradeEvaluationNotes');
                if (notesEl) notesEl.value = (evaluationNotes && evaluationNotes !== 'null' && evaluationNotes !==
                    'undefined') ? evaluationNotes : '';

                const formEl = document.getElementById('gradeForm');
                if (formEl) formEl.action = `${appBaseUrl}/ajax/teacher/submissions/${submissionId}/review`;

                const questionsContainer = document.getElementById('submissionQuestionsContainer');
                if (questionsContainer) {
                    questionsContainer.innerHTML =
                        `<p class="text-xs text-slate-400 italic text-center py-4">${i18n.loadingReview}</p>`;
                }

                window.openModal('gradeModal');

                try {
                    const res = await fetch(`${appBaseUrl}/ajax/teacher/submissions/${submissionId}/review-details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.success && data.submission) {
                        if (nameEl && data.submission.student_name) {
                            nameEl.textContent =
                                `${data.submission.student_name} — ${data.submission.assignment_title || ''}`;
                        }
                        if (scoreInput && data.submission.score !== null && data.submission.score !== undefined) {
                            scoreInput.value = data.submission.score;
                        }
                        if (notesEl && data.submission.evaluation_notes) {
                            notesEl.value = data.submission.evaluation_notes;
                        }
                    }
                    if (data.success && data.questions && data.questions.length > 0) {
                        let html = '';
                        data.questions.forEach((q, idx) => {
                            const statusBadge = q.is_correct ?
                                `<span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-emerald-500 text-[10px]"></i> ${i18n.correct} (+${q.points_earned}/${q.points} pts)</span>` :
                                `<span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-mono font-bold rounded-full"><i class="fa-solid fa-circle text-rose-500 text-[10px]"></i> ${i18n.incorrect} (0/${q.points} pts)</span>`;

                            let optsHtml = '';
                            q.options.forEach(opt => {
                                let optStyle = 'bg-white border-slate-200 text-slate-700';
                                let badge = '';

                                if (opt.is_correct && opt.is_selected) {
                                    optStyle =
                                    'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold';
                                    badge =
                                        `<span class="text-emerald-600 font-mono text-[10px]">${i18n.studentCorrectPick}</span>`;
                                } else if (opt.is_correct) {
                                    optStyle = 'bg-teal-50 border-teal-300 text-teal-900 font-bold';
                                    badge =
                                        `<span class="text-teal-600 font-mono text-[10px]">${i18n.correctKey}</span>`;
                                } else if (opt.is_selected) {
                                    optStyle = 'bg-red-50 border-red-300 text-red-900 font-bold';
                                    badge =
                                        `<span class="text-red-600 font-mono text-[10px]">${i18n.studentWrongPick}</span>`;
                                }

                                optsHtml += `<div class="p-2.5 rounded-xl border ${optStyle} text-xs flex items-center justify-between gap-2">
                        <span>${escapeHtml(opt.option_text)}</span>
                        ${badge}
                    </div>`;

                                if (opt.explanation && opt.is_correct) {
                                    optsHtml +=
                                        `<p class="text-[11px] text-slate-500 font-mono italic pl-2">${i18n.explanation} ${escapeHtml(opt.explanation)}</p>`;
                                }
                            });

                            html += `<div class="p-3.5 bg-[#FAFAF9] rounded-2xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900">Q${idx + 1}: ${escapeHtml(q.question_text || i18n.question)}</span>
                        ${statusBadge}
                    </div>
                    <div class="space-y-1.5 pt-1">
                        ${optsHtml}
                    </div>
                </div>`;
                        });
                        if (questionsContainer) questionsContainer.innerHTML = html;
                    } else {
                        if (questionsContainer) questionsContainer.innerHTML =
                            `<p class="text-xs text-slate-500 italic text-center py-4">${i18n.noQuestionBreakdown}</p>`;
                    }
                } catch (err) {
                    if (questionsContainer) questionsContainer.innerHTML =
                        `<p class="text-xs text-rose-500 italic text-center py-4">${i18n.unableToLoadBreakdown}</p>`;
                }
            }

            // ── Interactive Question Builder for Assignment Creator ───────────────────────
            let teacherQuestionCount = 0;

            function addTeacherQuestion() {
                const container = document.getElementById('teacherQuestionsContainer');
                if (!container) return;

                const qIdx = teacherQuestionCount++;
                const isAr = false;

                const html = `
    <div class="teacher-q-card p-4 sm:p-5 bg-slate-50 border border-slate-200 rounded-2xl space-y-3 relative" id="teacherQCard_${qIdx}">
        <div class="flex items-center justify-between">
            <span class="text-xs font-mono font-extrabold text-teal-900 bg-teal-100 px-3 py-1 rounded-full border border-teal-200">
                ${isAr ? 'السؤال رقم ' : 'Question #'}${qIdx + 1}
            </span>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 text-xs font-mono">
                    <span class="text-slate-500">${isAr ? 'الدرجة:' : 'Pts:'}</span>
                    <input type="number" step="0.5" name="questions[${qIdx}][points]" value="1" min="0.5" class="w-14 bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-center">
                </div>
                <button type="button" onclick="removeTeacherQuestion(${qIdx})" class="text-rose-500 hover:text-rose-700 text-xs font-bold font-mono px-2 py-1 rounded-lg hover:bg-rose-50 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i> ${isAr ? 'حذف' : 'Remove'}
                </button>
            </div>
        </div>

        <div>
            <textarea name="questions[${qIdx}][question_text]" rows="2" required placeholder="${isAr ? 'اكتب نص السؤال هنا...' : 'Type question text here...'}" class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-xs font-mono focus:outline-none focus:border-teal-600"></textarea>
        </div>

        <div class="space-y-2 pt-1 border-t border-slate-200/60">
            <p class="text-[10px] font-mono text-slate-500 font-bold">${isAr ? 'الخيارات (حدد الدائرة بجانب الإجابة الصحيحة):' : 'Answer Choices (Select radio button for the correct option):'}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                ${[0, 1, 2, 3].map(optIdx => `
                            <div class="flex items-center gap-2 bg-white p-2 rounded-xl border border-slate-200">
                                <input type="radio" name="questions[${qIdx}][correct_index]" value="${optIdx}" ${optIdx === 0 ? 'checked' : ''} class="text-teal-600 focus:ring-teal-500 cursor-pointer">
                                <input type="text" name="questions[${qIdx}][options][${optIdx}]" required placeholder="${isAr ? 'الخيار ' + String.fromCharCode(65 + optIdx) : 'Option ' + String.fromCharCode(65 + optIdx)}" class="w-full text-xs font-mono border-0 focus:ring-0 p-0 text-slate-800">
                            </div>
                        `).join('')}
            </div>
        </div>
    </div>`;

                container.insertAdjacentHTML('beforeend', html);
            }

            function removeTeacherQuestion(qIdx) {
                const el = document.getElementById('teacherQCard_' + qIdx);
                if (el) el.remove();
            }

            function showTeacherToast(message, isSuccess) {
                if (window.Toast) {
                    if (isSuccess) {
                        window.Toast.success(message);
                    } else {
                        window.Toast.danger(message);
                    }
                } else {
                    const toast = document.getElementById('teacherToastAlert');
                    if (!toast) return;
                    toast.className =
                        `p-4 rounded-2xl text-sm font-semibold transition-all duration-300 shadow-md ${isSuccess ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
                    toast.textContent = message;
                    toast.classList.remove('hidden');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            }

            function bindAjaxForm(formId, onSuccess) {
                const form = document.getElementById(formId);
                if (!form) return;

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const formData = new FormData(form);

                    try {
                        const res = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            onSuccess(data);
                        } else {
                            showTeacherToast(data.message || 'Validation error', false);
                        }
                    } catch (err) {
                        showTeacherToast('Network connection error', false);
                    }
                });
            }

            // ── DOM Initializations ───────────────────────────────────────────────────────
            function initActiveTabFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                const queryTab = urlParams.get('tab');
                const hashTab = window.location.hash ? window.location.hash.replace('#', '') : '';
                const requestedTab = queryTab || hashTab;
                if (requestedTab) {
                    switchTeacherTab(requestedTab);
                }
            }

            window.addEventListener('hashchange', initActiveTabFromUrl);

            document.addEventListener('DOMContentLoaded', function() {
                // 1. Auto-switch tab from URL query param or hash (e.g. #attendance or ?tab=attendance)
                initActiveTabFromUrl();

                // 2. Check for initial student modal open (e.g. ?student=123)
                const rootEl = document.getElementById('teacher-portal-root');
                const initStudent = rootEl ? rootEl.getAttribute('data-initial-student') : null;
                if (initStudent) {
                    switchTeacherTab('students');
                    openStudentDetailsModal(initStudent);
                }

                // 3. Counter Animations
                const counters = document.querySelectorAll('.js-counter');
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                    if (target === 0) return;
                    let count = 0;
                    const step = Math.max(1, Math.ceil(target / 25));
                    const timer = setInterval(() => {
                        count += step;
                        if (count >= target) {
                            counter.textContent = target.toLocaleString();
                            clearInterval(timer);
                        } else {
                            counter.textContent = count.toLocaleString();
                        }
                    }, 30);
                });

                // 4. Attach Student Search & Filter Listeners
                const sInput = document.getElementById('studentSearchInput');
                if (sInput) sInput.addEventListener('input', applyStudentFilters);
                const cFilter = document.getElementById('studentCourseFilter');
                if (cFilter) cFilter.addEventListener('change', applyStudentFilters);
                const gFilter = document.getElementById('studentGradeFilter');
                if (gFilter) gFilter.addEventListener('change', applyStudentFilters);
                const aFilter = document.getElementById('studentAttendanceFilter');
                if (aFilter) aFilter.addEventListener('change', applyStudentFilters);

                // 4b. Attach Attendance Search & Filter Listeners
                const attSearch = document.getElementById('attSearchInput');
                if (attSearch) attSearch.addEventListener('input', applyAttendanceFilters);
                const attCourse = document.getElementById('attCourseFilter');
                if (attCourse) attCourse.addEventListener('change', applyAttendanceFilters);
                const attStatus = document.getElementById('attStatusFilter');
                if (attStatus) attStatus.addEventListener('change', applyAttendanceFilters);

                // 4c. Delegated Attendance Modal Trigger (Bulletproof click capture fallback)
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('button[data-session-id]');
                    if (btn && !btn.hasAttribute('onclick')) {
                        const sId = btn.getAttribute('data-session-id');
                        const sTitle = btn.getAttribute('data-session-title') || '';
                        if (sId) {
                            e.preventDefault();
                            e.stopPropagation();
                            openAttendanceModal(sId, sTitle);
                        }
                    }
                });

                // Helper function to detect phone numbers in client JS
                function clientHasPhoneNumber(text) {
                    if (!text) return false;
                    const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                    let norm = text;
                    for (let i = 0; i < 10; i++) {
                        norm = norm.replaceAll(eastern[i], i.toString());
                    }
                    if (/(?:\+|00)[0-9]{1,4}[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,14}/i.test(norm)) return true;
                    if (/(?:(?:\b|[^0-9])(?:01[0125]|05[0-9]|02|03|04)[\s\-\.\(\)]*([0-9][\s\-\.\(\)]*){6,10})/i.test(
                            norm)) return true;
                    if (/(?:[0-9][\s\-\.\,\/\(\)\#\*\_]{0,3}){7,15}[0-9]/.test(norm)) return true;
                    if (/\b[0-9]{8,16}\b/.test(norm)) return true;
                    return false;
                }

                const noteTextarea = document.getElementById('noteContentTextarea');
                const phoneWarning = document.getElementById('notePhoneWarning');
                if (noteTextarea && phoneWarning) {
                    noteTextarea.addEventListener('input', function() {
                        const hasPhone = clientHasPhoneNumber(noteTextarea.value);
                        if (hasPhone) {
                            phoneWarning.classList.remove('hidden');
                            noteTextarea.classList.add('border-rose-500', 'bg-rose-50/20');
                            noteTextarea.classList.remove('border-slate-200');
                        } else {
                            phoneWarning.classList.add('hidden');
                            noteTextarea.classList.remove('border-rose-500', 'bg-rose-50/20');
                            noteTextarea.classList.add('border-slate-200');
                        }
                    });
                }

                // 5. Educational Note AJAX Form Handler with Security Policy
                const noteForm = document.getElementById('addNoteForm');
                if (noteForm) {
                    noteForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const sId = document.getElementById('noteStudentUserId').value;
                        if (!sId) return;

                        const noteVal = noteTextarea ? noteTextarea.value : '';
                        if (clientHasPhoneNumber(noteVal)) {
                            if (window.Toast) {
                                window.Toast.danger(
                                    isArLocale ?
                                    'لا يمكنك إرسال أرقام الهواتف أو وسائل التواصل في الملاحظات التعليمية حرصاً على الأمان والخصوصية' :
                                    'Security Alert: Sharing phone numbers or contact details in notes is prohibited.',
                                    isArLocale ?
                                    'تنبيه أمان وخصوصية <i class="fa-solid fa-shield-halved"></i>' :
                                    'Security Violation'
                                );
                            }
                            return;
                        }

                        const formData = new FormData(noteForm);
                        const submitBtn = document.getElementById('saveNoteBtn');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
                        }

                        try {
                            const res = await fetch(`${appBaseUrl}/ajax/teacher/students/${sId}/notes`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                showTeacherToast(data.message, true);
                                closeModal('addNoteModal');
                                noteForm.reset();
                                if (phoneWarning) phoneWarning.classList.add('hidden');
                                if (noteTextarea) noteTextarea.classList.remove('border-rose-500',
                                    'bg-rose-50/20');
                                if (currentViewingStudentId == sId) {
                                    openStudentDetailsModal(sId);
                                }
                            } else {
                                const msg = data.message || (data.errors && data.errors.note ? data.errors
                                    .note[0] : 'Failed to save note');
                                if (window.Toast) {
                                    window.Toast.danger(msg, isArLocale ?
                                        'تنبيه أمان <i class="fa-solid fa-shield-halved"></i>' :
                                        'Security Alert');
                                } else {
                                    showTeacherToast(msg, false);
                                }
                            }
                        } catch (err) {
                            showTeacherToast('Network connection error', false);
                        } finally {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = `${isArLocale ? 'حفظ الملاحظة' : 'Save Note'} &rarr;`;
                            }
                        }
                    });
                }

                // ── Recurring Schedule Helpers ──────────────────────────────────────────
                window.openCreateRecurringModal = function() {
                    window.openModal('recurringScheduleModal');
                };

                window.toggleRecurrenceFields = function(type) {
                    const daysContainer = document.getElementById('recDaysContainer');
                    if (daysContainer) {
                        if (type === 'monthly' || type === 'single') {
                            daysContainer.classList.add('hidden');
                        } else {
                            daysContainer.classList.remove('hidden');
                        }
                    }
                };

                window.previewRecurringDates = async function() {
                    const form = document.getElementById('recurringScheduleForm');
                    if (!form) return;

                    const formData = new FormData(form);
                    const previewContainer = document.getElementById('recPreviewContainer');
                    const previewSummary = document.getElementById('recPreviewSummary');
                    const conflictBadge = document.getElementById('recConflictStatusBadge');
                    const conflictWarning = document.getElementById('recConflictWarning');
                    const tableBody = document.getElementById('recPreviewTableBody');

                    if (previewContainer) previewContainer.classList.remove('hidden');
                    if (previewSummary) previewSummary.innerHTML =
                        `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري فحص المواعيد والتعارضات...' : 'Validating dates and conflicts...'}`;

                    try {
                        const res = await fetch('http://localhost/elite-academy/public/ajax/teacher/recurring-schedules/preview', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
                            }
                        });
                        const data = await res.json();

                        if (res.status === 401) {
                            const sessionExpiredMsg = isArLocale ? 'انتهت الجلسة، يرجى إعادة تسجيل الدخول.' :
                                'Your session has expired. Please log in again.';
                            if (previewSummary) previewSummary.textContent = sessionExpiredMsg;
                            if (conflictBadge) {
                                conflictBadge.className =
                                    'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                                conflictBadge.innerHTML =
                                    `<i class="fa-solid fa-circle-xmark text-rose-500"></i> ${isArLocale ? 'جلسة منتهية' : 'Session Expired'}`;
                            }
                            showTeacherToast(sessionExpiredMsg, false);
                            setTimeout(() => window.location.href = 'http://localhost/elite-academy/public/login', 1500);
                            return;
                        }

                        if (!res.ok || !data.success) {
                            const errMsg = data.message || 'Validation failed';
                            if (previewSummary) previewSummary.textContent = errMsg;
                            if (conflictBadge) {
                                conflictBadge.className =
                                    'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
                                conflictBadge.innerHTML =
                                    `<i class="fa-solid fa-circle-xmark text-rose-500"></i> ${isArLocale ? 'خطأ' : 'Error'}`;
                            }
                            return;
                        }

                        if (previewSummary) {
                            previewSummary.textContent = isArLocale ?
                                `إجمالي الحصص المتولدة: ${data.total_sessions} حصة` :
                                `Total Generated Sessions: ${data.total_sessions}`;
                        }

                        if (data.has_conflicts) {
                            conflictBadge.className =
                                'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 animate-pulse';
                            conflictBadge.innerHTML = isArLocale ?
                                '<i class="fa-solid fa-triangle-exclamation"></i> يوجد تعارض في المواعيد' :
                                '<i class="fa-solid fa-triangle-exclamation"></i> Schedule Conflicts Detected';
                            conflictWarning.classList.remove('hidden');
                            conflictWarning.innerHTML =
                                `<span><i class="fa-solid fa-triangle-exclamation"></i> ${isArLocale ? 'تنبيه: بعض الحصص المقترحة تتعارض مع حصص سابقة لنفس المعلم أو الطالب.' : 'Warning: Some proposed sessions conflict with existing schedules.'}</span>`;
                        } else {
                            conflictBadge.className =
                                'text-[10px] font-mono font-extrabold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                            conflictBadge.innerHTML = isArLocale ?
                                '<i class="fa-solid fa-check"></i> المواعيد متاحة بدون تعارض' :
                                '<i class="fa-solid fa-check"></i> All Slots Available';
                            conflictWarning.classList.add('hidden');
                        }

                        if (tableBody) {
                            tableBody.innerHTML = (data.dates || []).map(d => `
                    <tr class="hover:bg-white/80 ${d.has_conflict ? 'bg-rose-50/60 text-rose-900' : ''}">
                        <td class="py-1.5 px-2 font-bold">${d.date}</td>
                        <td class="py-1.5 px-2 text-slate-500">${d.day_name}</td>
                        <td class="py-1.5 px-2">${d.start_time} - ${d.end_time}</td>
                        <td class="py-1.5 px-2 font-bold ${d.has_conflict ? 'text-rose-600' : 'text-emerald-600'}">
                            ${d.has_conflict ? '<i class="fa-solid fa-triangle-exclamation"></i> ' + (isArLocale ? 'تعارض' : 'Conflict') : '<i class="fa-solid fa-check"></i> ' + (isArLocale ? 'متاح' : 'Available')}
                        </td>
                    </tr>
                `).join('');
                        }

                    } catch (err) {
                        if (previewSummary) previewSummary.textContent = 'Connection error';
                    }
                };

                window.openEditSessionOverrideModal = function(sessionId, titleOrEl, scheduledAt, duration, meetingLink,
                    notes) {
                    let title = '';
                    if (titleOrEl && typeof titleOrEl === 'object' && titleOrEl.nodeType) {
                        title = titleOrEl.getAttribute('data-title') || '';
                        scheduledAt = titleOrEl.getAttribute('data-scheduled-at') || '';
                        duration = titleOrEl.getAttribute('data-duration') || 60;
                        meetingLink = titleOrEl.getAttribute('data-meeting-link') || '';
                        notes = titleOrEl.getAttribute('data-notes') || '';
                    } else if (typeof titleOrEl === 'string') {
                        title = titleOrEl;
                    }
                    document.getElementById('overrideSessionId').value = sessionId;
                    document.getElementById('overrideTitle').value = title || '';
                    document.getElementById('overrideDateTime').value = scheduledAt || '';
                    document.getElementById('overrideDuration').value = duration || 60;
                    document.getElementById('overrideMeetingLink').value = meetingLink || '';
                    document.getElementById('overrideReason').value = '';
                    window.openModal('editSessionOverrideModal');
                };

                window.confirmCancelSession = function(sessionId) {
                    document.getElementById('cancelSessionId').value = sessionId;
                    document.getElementById('cancelReasonInput').value = '';
                    openModal('cancelSessionModal');
                };

                // ── Form Handlers ────────────────────────────────────────────────────────
                bindAjaxForm('createSessionForm', function(data) {
                    showTeacherToast(data.message, true);
                    closeModal('createSessionModal');
                    setTimeout(() => location.reload(), 900);
                });

                const recForm = document.getElementById('recurringScheduleForm');
                if (recForm) {
                    recForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const submitBtn = document.getElementById('saveRecurringBtn');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري إنشاء الجدول والحصص...' : 'Generating sessions...'}`;
                        }

                        try {
                            const formData = new FormData(recForm);
                            const res = await fetch('http://localhost/elite-academy/public/ajax/teacher/recurring-schedules/create', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
                                }
                            });
                            if (res.status === 401) {
                                showTeacherToast(isArLocale ? 'انتهت الجلسة، يرجى إعادة تسجيل الدخول.' :
                                    'Your session has expired. Please log in again.', false);
                                setTimeout(() => window.location.href = 'http://localhost/elite-academy/public/login', 1500);
                                return;
                            }
                            const data = await res.json();
                            if (data.success) {
                                showTeacherToast(data.message, true);
                                closeModal('recurringScheduleModal');
                                setTimeout(() => location.reload(), 900);
                            } else {
                                showTeacherToast(data.message || 'Failed to create schedule', false);
                            }
                        } catch (err) {
                            showTeacherToast('Connection error', false);
                        } finally {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML =
                                    `${isArLocale ? 'إنشاء جدول الحصص المتكرر' : 'Create Recurring Schedule'} &rarr;`;
                            }
                        }
                    });
                }

                const overrideForm = document.getElementById('editSessionOverrideForm');
                if (overrideForm) {
                    overrideForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const sId = document.getElementById('overrideSessionId').value;
                        const submitBtn = document.getElementById('saveOverrideBtn');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري الحفظ...' : 'Saving...'}`;
                        }

                        try {
                            const formData = new FormData(overrideForm);
                            const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sId}/override`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                showTeacherToast(data.message, true);
                                closeModal('editSessionOverrideModal');
                                setTimeout(() => location.reload(), 900);
                            } else {
                                showTeacherToast(data.message || 'Failed to update session', false);
                            }
                        } catch (err) {
                            showTeacherToast('Connection error', false);
                        } finally {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML =
                                    `${isArLocale ? 'حفظ التغييرات' : 'Save Changes'} &rarr;`;
                            }
                        }
                    });
                }

                const cancelForm = document.getElementById('cancelSessionForm');
                if (cancelForm) {
                    cancelForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const sId = document.getElementById('cancelSessionId').value;
                        const formData = new FormData(cancelForm);

                        try {
                            const res = await fetch(`${appBaseUrl}/ajax/teacher/sessions/${sId}/cancel`, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                showTeacherToast(data.message, true);
                                closeModal('cancelSessionModal');
                                setTimeout(() => location.reload(), 900);
                            } else {
                                showTeacherToast(data.message || 'Failed to cancel session', false);
                            }
                        } catch (err) {
                            showTeacherToast('Connection error', false);
                        }
                    });
                }

                bindAjaxForm('createAssignmentForm', function(data) {
                    showTeacherToast(data.message, true);
                    closeModal('createAssignmentModal');
                    setTimeout(() => location.reload(), 900);
                });

                bindAjaxForm('meetingLinkForm', function(data) {
                    showTeacherToast(data.message, true);
                    closeModal('meetingLinkModal');
                    setTimeout(() => location.reload(), 900);
                });

                bindAjaxForm('rescheduleForm', function(data) {
                    showTeacherToast(data.message, true);
                    closeModal('rescheduleModal');
                    setTimeout(() => location.reload(), 900);
                });

                bindAjaxForm('gradeForm', function(data) {
                    showTeacherToast(data.message, true);
                    closeModal('gradeModal');
                    setTimeout(() => location.reload(), 900);
                });

                const attForm = document.getElementById('attendanceForm');
                if (attForm) {
                    attForm.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const sessionId = document.getElementById('attendanceSessionId').value;
                        const submitBtn = document.getElementById('saveAttendanceBtn');
                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML =
                                `<i class="fa-solid fa-hourglass-half"></i> ${isArLocale ? 'جاري حفظ كشف الحضور...' : 'Saving sheet...'}`;
                        }

                        try {
                            const formData = new FormData(attForm);
                            const res = await fetch(
                                `${appBaseUrl}/ajax/teacher/sessions/${sessionId}/attendance`, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': 'Oojk39UCu71xeJtulu7R2tJ35GJGQVwswtwOk59y'
                                    }
                                });
                            const data = await res.json();

                            if (data.success) {
                                showTeacherToast(data.message, true);
                                closeModal('attendanceModal');

                                // Dynamically update session card on the page
                                const card = document.getElementById(`attSessionCard_${sessionId}`);
                                if (card && data.stats) {
                                    card.setAttribute('data-recorded', '1');
                                    card.setAttribute('data-status', 'recorded');

                                    const badgeEl = document.getElementById(`attCardBadge_${sessionId}`);
                                    if (badgeEl) {
                                        badgeEl.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                    <span>${isArLocale ? 'تم الرصد' : 'Recorded'}</span>
                                    <span class="font-extrabold">(${data.stats.present}/${data.stats.total})</span>
                                </span>
                            `;
                                    }

                                    const statsEl = document.getElementById(`attCardStats_${sessionId}`);
                                    if (statsEl) {
                                        const presentEl = statsEl.querySelector('.att-stat-present');
                                        if (presentEl) presentEl.textContent = data.stats.present;
                                        const absentEl = statsEl.querySelector('.att-stat-absent');
                                        if (absentEl) absentEl.textContent = data.stats.absent;
                                        const rateEl = statsEl.querySelector('.att-stat-rate');
                                        if (rateEl) {
                                            rateEl.textContent = `${data.stats.rate}%`;
                                            rateEl.className =
                                                `font-black text-sm att-stat-rate ${data.stats.rate >= 80 ? 'text-emerald-600' : 'text-amber-600'}`;
                                        }
                                    }

                                    const btnLabel = document.getElementById(
                                    `attCardBtnLabel_${sessionId}`);
                                    if (btnLabel) {
                                        btnLabel.textContent = isArLocale ? 'مراجعة وتعديل الكشف' :
                                            'Review & Edit';
                                    }
                                }

                                // Update Table View if present
                                const tableBadge = document.getElementById(`attTableBadge_${sessionId}`);
                                if (tableBadge && data.stats) {
                                    tableBadge.innerHTML = `
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-100 text-emerald-800">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i>
                                <span>${isArLocale ? 'تم الرصد' : 'Recorded'}</span>
                            </span>
                        `;
                                }
                                const tableRate = document.getElementById(`attTableRate_${sessionId}`);
                                if (tableRate && data.stats) {
                                    tableRate.textContent = `${data.stats.rate}%`;
                                }

                                // Re-apply filters so status filter reflects instantly
                                applyAttendanceFilters();
                            } else {
                                showTeacherToast(data.message || (isArLocale ? 'فشل حفظ كشف الحضور' :
                                    'Failed to save attendance'), false);
                            }
                        } catch (err) {
                            showTeacherToast('Connection error', false);
                        } finally {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML =
                                    `<i class="fa-solid fa-check"></i> <span>${isArLocale ? 'حفظ كشف الحضور' : 'Save Attendance Sheet'}</span>`;
                            }
                        }
                    });
                }
            });
        