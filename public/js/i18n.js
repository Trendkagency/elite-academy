/**
 * Elite Academy — Bilingual i18n Engine (EN / AR)
 * Modern Standard Arabic — Premium Educational Platform
 * Mirrors Laravel __('file.key') localization convention.
 */

const translations = {
  en: {
    /* ----- navbar ----- */
    'navbar.home':        'Home',
    'navbar.subjects':    'Subjects',
    'navbar.instructors': 'Instructors',
    'navbar.blog':        'Blog',
    'navbar.about':       'About',
    'navbar.contact':     'Contact',
    'navbar.login':       'Log in',
    'navbar.portal':      'Student Portal',
    'navbar.menu':        'Menu',
    'navbar.close':       'Close',
    'navbar.lang_switch': 'العربية',

    /* ----- home ----- */
    'home.hero_badge':         'Programming & Tech Track',
    'home.hero_title_1':       'Build Your Future',
    'home.hero_title_2':       'Through Technology.',
    'home.hero_subtitle':      'Master programming with industry experts through hands-on projects and interactive cohorts.',
    'home.cta_explore':        'Explore Programming',
    'home.cta_trial':          'Book Free Trial',
    'home.cta_register':       'Student Registration',
    'home.cta_programs':       'Explore Programs',
    'home.stat_accredited':    'ACCREDITED',
    'home.stat_global':        'GLOBAL',
    'home.stat_courses':       'Expert Courses',
    'home.stat_students':      'Active Students',
    'home.about_badge':        'WHO WE ARE',
    'home.about_title':        'Empowering the Next Generation of Innovators',
    'home.about_subtitle':     'Elite Academy blends academic rigour with real-world application to give students the edge they need.',
    'home.subjects_badge':     'PROGRAMS',
    'home.subjects_title':     'Explore Our Academic Tracks',
    'home.subjects_subtitle':  'Cutting-edge curriculum designed by industry experts and academic researchers.',
    'home.mentors_badge':      'FACULTY',
    'home.mentors_title':      'Learn from the Best Minds',
    'home.courses_badge':      'COURSES',
    'home.courses_title':      'Featured Courses',
    'home.events_badge':       'EVENTS',
    'home.events_title':       'Upcoming Academic Events',
    'home.testimonials_badge': 'TESTIMONIALS',
    'home.testimonials_title': 'What Our Students Say',
    'home.why_badge':          'WHY ELITE',
    'home.why_title':          'Why Students Choose Elite Academy',
    'home.why_subtitle':       'Discover how our accredited programs and faculty prepare you for career success.',
    'home.benefit_1':          '250+ Courses',
    'home.benefit_2':          'Expert Teachers',
    'home.benefit_3':          'Practical',
    'home.benefit_4':          'Certificates',
    'home.benefit_5':          'Flexible',
    'home.benefit_6':          'Career',
    'home.cta_section_badge':  'Start Your Journey Today',
    'home.cta_section_title':  "Ready to Shape Your Child's Future with Elite Mentorship?",
    'home.cta_section_sub':    'Join over 25,000 students discovering their passion in Programming, AI, Science, and Design.',

    /* ----- subjects ----- */
    'subjects.page_badge':    'PROGRAMS',
    'subjects.page_title':    'Academic Subjects',
    'subjects.page_subtitle': 'Explore our curated curriculum tracks designed for ambitious learners.',
    'subjects.search':        'Search subjects...',
    'subjects.filter_all':    'All',
    'subjects.cta_details':   'View Details',
    'subjects.cta_enroll':    'Enroll Now',

    /* ----- teachers ----- */
    'teachers.page_badge':    'FACULTY',
    'teachers.page_title':    'Expert Instructors',
    'teachers.page_subtitle': 'Learn from world-class educators and industry professionals.',
    'teachers.search':        'Search instructors...',
    'teachers.stat_courses':  'Courses',
    'teachers.stat_students': 'Students',
    'teachers.stat_rating':   'Rating',
    'teachers.cta_profile':   'View Profile',

    /* ----- blog ----- */
    'blog.page_badge':    'EDUCATIONAL BLOG',
    'blog.page_title':    'Learn Beyond the Classroom',
    'blog.page_subtitle': 'Discover study tips, exam strategies, learning resources, university guidance, and educational insights from Elite Academy instructors.',
    'blog.featured':      'Featured Article',
    'blog.latest':        'Latest Articles',
    'blog.min_read':      'min read',

    /* ----- events ----- */
    'events.page_badge':        'ACADEMIC EVENTS',
    'events.page_title':        'Upcoming Academic Events',
    'events.page_subtitle':     'Discover workshops, revision sessions, competitions and educational events.',
    'events.stat_upcoming':     'Upcoming Events',
    'events.stat_live':         'Live Sessions',
    'events.stat_competitions': 'Competitions',
    'events.stat_workshops':    'Workshops',
    'events.cta_register':      'Register',
    'events.cta_details':       'Details',

    /* ----- contact ----- */
    'contact.page_badge':    'CONTACT US',
    'contact.page_title':    'Get in Touch',
    'contact.page_subtitle': "We're here to help. Reach out and we'll respond as soon as possible.",
    'contact.label_name':    'Full Name',
    'contact.label_email':   'Email Address',
    'contact.label_subject': 'Subject',
    'contact.label_message': 'Your Message',
    'contact.cta_send':      'Send Message',

    /* ----- footer ----- */
    'footer.rights':          '© 2026 Elite Academy. All rights reserved.',
    'footer.tagline':         'Empowering the next generation of innovators.',
    'footer.nav_home':        'Home',
    'footer.nav_subjects':    'Subjects',
    'footer.nav_instructors': 'Instructors',
    'footer.nav_blog':        'Blog',
    'footer.nav_about':       'About',
    'footer.nav_contact':     'Contact',

    /* ----- common ----- */
    'common.read_more':  'Read More',
    'common.view_all':   'View All',
    'common.back':       'Back',
    'common.close':      'Close',
    'common.submit':     'Submit',
    'common.cancel':     'Cancel',
    'common.search':     'Search',
    'common.filter':     'Filter',
    'common.loading':    'Loading...',
    'common.no_results': 'No results found.',
    'common.learn_more': 'Learn More',
    'common.enroll':     'Enroll',
    'common.register':   'Register',
    'common.login':      'Log in',
    'common.logout':     'Log out',

    /* ----- validation ----- */
    'validation.required':         'This field is required.',
    'validation.email_invalid':    'Please enter a valid email address.',
    'validation.min_length':       'Must be at least :min characters.',
    'validation.max_length':       'Must not exceed :max characters.',
    'validation.password_confirm': 'Passwords do not match.',
  },

  ar: {
    /* ----- navbar ----- */
    'navbar.home':        'الرئيسية',
    'navbar.subjects':    'المواد الدراسية',
    'navbar.instructors': 'المدرّسون',
    'navbar.blog':        'المدوّنة',
    'navbar.about':       'من نحن',
    'navbar.contact':     'تواصل معنا',
    'navbar.login':       'تسجيل الدخول',
    'navbar.portal':      'بوابة الطالب',
    'navbar.menu':        'القائمة',
    'navbar.close':       'إغلاق',
    'navbar.lang_switch': 'English',

    /* ----- home ----- */
    'home.hero_badge':         'مسار البرمجة والتقنية',
    'home.hero_title_1':       'ابنِ مستقبلك',
    'home.hero_title_2':       'من خلال التكنولوجيا.',
    'home.hero_subtitle':      'أتقن البرمجة مع خبراء الصناعة من خلال مشاريع عملية وكوهورتات تفاعلية.',
    'home.cta_explore':        'استكشف البرمجة',
    'home.cta_trial':          'احجز درسًا مجانيًا',
    'home.cta_register':       'تسجيل الطالب',
    'home.cta_programs':       'استعرض البرامج',
    'home.stat_accredited':    'معتمد',
    'home.stat_global':        'عالمي',
    'home.stat_courses':       'دورة متخصصة',
    'home.stat_students':      'طالب نشط',
    'home.about_badge':        'من نحن',
    'home.about_title':        'نُمكِّن الجيل القادم من المبدعين',
    'home.about_subtitle':     'تمزج أكاديمية النخبة بين الصرامة الأكاديمية والتطبيق الواقعي لمنح الطلاب الميزة التنافسية التي يحتاجونها.',
    'home.subjects_badge':     'البرامج',
    'home.subjects_title':     'استكشف مساراتنا الأكاديمية',
    'home.subjects_subtitle':  'مناهج متطورة صمّمها خبراء الصناعة والباحثون الأكاديميون.',
    'home.mentors_badge':      'هيئة التدريس',
    'home.mentors_title':      'تعلّم من أفضل العقول',
    'home.courses_badge':      'الدورات',
    'home.courses_title':      'الدورات المميزة',
    'home.events_badge':       'الفعاليات',
    'home.events_title':       'الفعاليات الأكاديمية القادمة',
    'home.testimonials_badge': 'آراء الطلاب',
    'home.testimonials_title': 'ماذا يقول طلابنا',
    'home.why_badge':          'لماذا إيليت',
    'home.why_title':          'لماذا يختار الطلاب أكاديمية إيليت',
    'home.why_subtitle':       'اكتشف كيف تؤهلك برامجنا وكوادرنا المعتمدة للنجاح المهني والأكاديمي.',
    'home.benefit_1':          '+250 دورة',
    'home.benefit_2':          'معلمون خبراء',
    'home.benefit_3':          'تطبيق عملي',
    'home.benefit_4':          'شهادات دولية',
    'home.benefit_5':          'تعلم مرن',
    'home.benefit_6':          'دعم مهني',
    'home.cta_section_badge':  'ابدأ رحلتك اليوم',
    'home.cta_section_title':  'هل أنت مستعد لتشكيل مستقبل طفلك مع نخبة المرشدين؟',
    'home.cta_section_sub':    'انضم إلى أكثر من 25,000 طالب يكتشفون شغفهم في البرمجة والذكاء الاصطناعي والعلوم والتصميم.',

    /* ----- subjects ----- */
    'subjects.page_badge':    'البرامج',
    'subjects.page_title':    'المواد الدراسية',
    'subjects.page_subtitle': 'استكشف مساراتنا الدراسية المنتقاة بعناية والمصمّمة للمتعلمين الطموحين.',
    'subjects.search':        'ابحث عن المواد...',
    'subjects.filter_all':    'الكل',
    'subjects.cta_details':   'عرض التفاصيل',
    'subjects.cta_enroll':    'سجّل الآن',

    /* ----- teachers ----- */
    'teachers.page_badge':    'هيئة التدريس',
    'teachers.page_title':    'مدرّسون متخصصون',
    'teachers.page_subtitle': 'تعلّم من أفضل المربّين وخبراء الصناعة على مستوى العالم.',
    'teachers.search':        'ابحث عن المدرّسين...',
    'teachers.stat_courses':  'دورات',
    'teachers.stat_students': 'طلاب',
    'teachers.stat_rating':   'التقييم',
    'teachers.cta_profile':   'عرض الملف الشخصي',

    /* ----- blog ----- */
    'blog.page_badge':    'المدوّنة التعليمية',
    'blog.page_title':    'تعلّم ما وراء الفصل الدراسي',
    'blog.page_subtitle': 'اكتشف نصائح الدراسة واستراتيجيات الامتحانات والمصادر التعليمية والإرشاد الجامعي والرؤى التعليمية من أساتذة أكاديمية النخبة.',
    'blog.featured':      'المقال المميز',
    'blog.latest':        'أحدث المقالات',
    'blog.min_read':      'د قراءة',

    /* ----- events ----- */
    'events.page_badge':        'الفعاليات الأكاديمية',
    'events.page_title':        'الفعاليات الأكاديمية القادمة',
    'events.page_subtitle':     'اكتشف ورش العمل والجلسات المراجعية والمسابقات والفعاليات التعليمية.',
    'events.stat_upcoming':     'فعاليات قادمة',
    'events.stat_live':         'جلسات مباشرة',
    'events.stat_competitions': 'مسابقات',
    'events.stat_workshops':    'ورش عمل',
    'events.cta_register':      'سجّل',
    'events.cta_details':       'التفاصيل',

    /* ----- contact ----- */
    'contact.page_badge':    'تواصل معنا',
    'contact.page_title':    'تواصل معنا',
    'contact.page_subtitle': 'نحن هنا للمساعدة. تواصل معنا وسنردّ في أقرب وقت ممكن.',
    'contact.label_name':    'الاسم الكامل',
    'contact.label_email':   'البريد الإلكتروني',
    'contact.label_subject': 'الموضوع',
    'contact.label_message': 'رسالتك',
    'contact.cta_send':      'إرسال الرسالة',

    /* ----- footer ----- */
    'footer.rights':          '© 2026 أكاديمية النخبة. جميع الحقوق محفوظة.',
    'footer.tagline':         'نُمكِّن الجيل القادم من المبدعين.',
    'footer.nav_home':        'الرئيسية',
    'footer.nav_subjects':    'المواد الدراسية',
    'footer.nav_instructors': 'المدرّسون',
    'footer.nav_blog':        'المدوّنة',
    'footer.nav_about':       'من نحن',
    'footer.nav_contact':     'تواصل معنا',

    /* ----- common ----- */
    'common.read_more':  'اقرأ المزيد',
    'common.view_all':   'عرض الكل',
    'common.back':       'رجوع',
    'common.close':      'إغلاق',
    'common.submit':     'إرسال',
    'common.cancel':     'إلغاء',
    'common.search':     'بحث',
    'common.filter':     'تصفية',
    'common.loading':    'جارٍ التحميل...',
    'common.no_results': 'لا توجد نتائج.',
    'common.learn_more': 'اعرف المزيد',
    'common.enroll':     'سجّل',
    'common.register':   'إنشاء حساب',
    'common.login':      'تسجيل الدخول',
    'common.logout':     'تسجيل الخروج',

    /* ----- validation ----- */
    'validation.required':         'هذا الحقل مطلوب.',
    'validation.email_invalid':    'يرجى إدخال بريد إلكتروني صحيح.',
    'validation.min_length':       'يجب أن يحتوي على :min أحرف على الأقل.',
    'validation.max_length':       'يجب ألّا يتجاوز :max حرفًا.',
    'validation.password_confirm': 'كلمتا المرور غير متطابقتين.',
  }
};

/* ------------------------------------------------------------------ */
/* Apply translations to all [data-i18n] elements                      */
/* Falls back to English if a key is missing from AR                   */
/* ------------------------------------------------------------------ */
function i18nApply(lang) {
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    const val = translations[lang]?.[key] ?? translations.en?.[key] ?? '';
    if (val) el.textContent = val;
  });

  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    const val = translations[lang]?.[key] ?? translations.en?.[key] ?? '';
    if (val) el.placeholder = val;
  });

  document.querySelectorAll('[data-i18n-aria]').forEach(el => {
    const key = el.getAttribute('data-i18n-aria');
    const val = translations[lang]?.[key] ?? translations.en?.[key] ?? '';
    if (val) el.setAttribute('aria-label', val);
  });
}

/* ------------------------------------------------------------------ */
/* Switch locale: dir/lang/font + translate all keyed strings          */
/* ------------------------------------------------------------------ */
function switchLanguage(lang) {
  const html = document.documentElement;
  const isAr = lang === 'ar';

  html.setAttribute('lang', lang);
  html.setAttribute('dir',  isAr ? 'rtl' : 'ltr');

  /* Font */
  document.body.style.fontFamily = isAr
    ? "'Cairo', system-ui, sans-serif"
    : "'Inter', system-ui, -apple-system, sans-serif";

  /* Switcher label */
  ['lang-label', 'mobile-lang-label'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.textContent = isAr ? 'English' : 'العربية';
  });

  /* Flip directional arrows */
  document.querySelectorAll('.rtl-arrow').forEach(el => {
    el.textContent = isAr ? '\u2190' : '\u2192';
  });

  i18nApply(lang);
  localStorage.setItem('elite_lang', lang);
  document.dispatchEvent(new CustomEvent('eliteLanguageChange', { detail: { lang } }));
}

/* Boot */
document.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('elite_lang') || 'en';
  switchLanguage(saved);

  ['lang-toggle-btn', 'mobile-lang-toggle-btn'].forEach(id => {
    const btn = document.getElementById(id);
    if (btn) {
      btn.addEventListener('click', () => {
        const cur = document.documentElement.getAttribute('lang') || 'en';
        switchLanguage(cur === 'en' ? 'ar' : 'en');
      });
    }
  });
});
