<?php

namespace App\Services;

use App\Models\LandingPage;
use App\Models\LandingPageCounter;
use App\Models\LandingPageSection;
use App\Models\LandingPageVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class LandingPageEngineService
{
    public const CACHE_KEY = 'published_landing_page_data_v2';

    /**
     * Get published sections with performance caching
     */
    public function getPublishedSections(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $page = LandingPage::firstOrCreate(
                ['slug' => 'main'],
                ['title' => 'Main Landing Page', 'status' => 'published']
            );

            $sections = LandingPageSection::where('landing_page_id', $page->id)
                ->where('is_enabled', true)
                ->orderBy('sort_order', 'asc')
                ->with(['counters' => function ($q) {
                    $q->where('is_enabled', true)->orderBy('sort_order', 'asc');
                }])
                ->get();

            if ($sections->isEmpty()) {
                $this->seedDefaultSections($page);
                $sections = LandingPageSection::where('landing_page_id', $page->id)
                    ->where('is_enabled', true)
                    ->orderBy('sort_order', 'asc')
                    ->with('counters')
                    ->get();
            }

            return $sections->toArray();
        });
    }

    /**
     * Publish current draft sections & create a version snapshot
     */
    public function publishSections(array $sections): LandingPageVersion
    {
        $page = LandingPage::firstOrCreate(
            ['slug' => 'main'],
            ['title' => 'Main Landing Page', 'status' => 'published']
        );

        // Delete existing draft sections
        LandingPageSection::where('landing_page_id', $page->id)->delete();

        foreach ($sections as $index => $secData) {
            $section = LandingPageSection::create([
                'landing_page_id' => $page->id,
                'section_key' => $secData['key'] ?? $secData['section_key'] ?? ('sec_' . $index),
                'type' => $secData['type'] ?? 'custom',
                'title_en' => $secData['title_en'] ?? null,
                'title_ar' => $secData['title_ar'] ?? null,
                'subtitle_en' => $secData['subtitle_en'] ?? null,
                'subtitle_ar' => $secData['subtitle_ar'] ?? null,
                'badge_en' => $secData['badge_en'] ?? null,
                'badge_ar' => $secData['badge_ar'] ?? null,
                'image_url' => $secData['image_url'] ?? null,
                'settings_json' => $secData['settings_json'] ?? ($secData['settings'] ?? []),
                'is_enabled' => $secData['is_enabled'] ?? true,
                'sort_order' => $index,
            ]);

            // Save counters if present
            if (! empty($secData['counters']) && is_array($secData['counters'])) {
                foreach ($secData['counters'] as $cIndex => $cData) {
                    LandingPageCounter::create([
                        'section_id' => $section->id,
                        'type' => $cData['type'] ?? 'manual',
                        'data_source' => $cData['data_source'] ?? null,
                        'target_value' => $cData['target_value'] ?? ($cData['count'] ?? '100'),
                        'prefix' => $cData['prefix'] ?? null,
                        'suffix' => $cData['suffix'] ?? null,
                        'label_ar' => $cData['label_ar'] ?? null,
                        'label_en' => $cData['label_en'] ?? null,
                        'description_ar' => $cData['description_ar'] ?? null,
                        'description_en' => $cData['description_en'] ?? null,
                        'color' => $cData['color'] ?? 'teal',
                        'is_enabled' => $cData['is_enabled'] ?? true,
                        'sort_order' => $cIndex,
                    ]);
                }
            }
        }

        // Get latest version number
        $latestVer = LandingPageVersion::where('landing_page_id', $page->id)->max('version_number') ?: 0;
        $newVersionNumber = $latestVer + 1;

        // Create Snapshot
        $snapshotData = LandingPageSection::where('landing_page_id', $page->id)
            ->with('counters')
            ->get()
            ->toArray();

        $version = LandingPageVersion::create([
            'landing_page_id' => $page->id,
            'version_number' => $newVersionNumber,
            'snapshot_json' => $snapshotData,
            'created_by' => Auth::user()?->name ?? 'Admin Studio',
            'status' => 'published',
        ]);

        $page->update([
            'published_version_id' => $version->id,
            'status' => 'published',
        ]);

        // Clear cache
        Cache::forget(self::CACHE_KEY);

        return $version;
    }

    /**
     * Restore a historical version snapshot
     */
    public function restoreVersion(int $versionId): bool
    {
        $version = LandingPageVersion::find($versionId);
        if (! $version || empty($version->snapshot_json)) {
            return false;
        }

        $snapshot = $version->snapshot_json;
        $this->publishSections($snapshot);

        return true;
    }

    /**
     * Seed default initial sections into database if empty
     */
    private function seedDefaultSections(LandingPage $page): void
    {
        $defaults = [
            [
                'key' => 'hero-slider',
                'type' => 'hero',
                'title_ar' => 'نُمكّن قادة المستقبل بالتميز الأكاديمي والتطبيقي',
                'title_en' => 'Empowering Future Leaders with Practical Academic Excellence',
                'subtitle_ar' => 'انضم إلى آلاف الطلاب الذين يتعلمون البرمجة، والذكاء الاصطناعي، والعلوم، وإدارة الأعمال من أفضل معلمي مصر.',
                'subtitle_en' => 'Join thousands of students learning Programming, Artificial Intelligence, Science, and Business from Egypt’s top educators.',
                'badge_ar' => '🚀 المنصة الأكاديمية الأولى في مصر',
                'badge_en' => '🚀 EGYPT’S #1 ACADEMIC PLATFORM',
                'image_url' => 'images/course_ai.png',
                'is_enabled' => true,
                'sort_order' => 0,
            ],
            [
                'key' => 'stats-overlay',
                'type' => 'counters',
                'title_ar' => 'أرقام وإنجازات إيليت أوبن أباديمي',
                'title_en' => 'Elite Academy Numbers & Impact',
                'is_enabled' => true,
                'sort_order' => 1,
                'counters' => [
                    ['type' => 'dynamic', 'data_source' => 'students_count', 'target_value' => '25000', 'suffix' => '+', 'label_ar' => 'الطلاب النشطين', 'label_en' => 'Active Students', 'color' => 'teal'],
                    ['type' => 'dynamic', 'data_source' => 'courses_count', 'target_value' => '120', 'suffix' => '+', 'label_ar' => 'الكورسات والمقررات المعتمدة', 'label_en' => 'Expert Courses', 'color' => 'teal'],
                    ['type' => 'dynamic', 'data_source' => 'teachers_count', 'target_value' => '45', 'suffix' => '+', 'label_ar' => 'المعلمين والمحاضرين', 'label_en' => 'Instructors & Mentors', 'color' => 'teal'],
                    ['type' => 'dynamic', 'data_source' => 'parents_satisfaction', 'target_value' => '98.5', 'suffix' => '%', 'label_ar' => 'رضا أولياء الأمور', 'label_en' => 'Parent Satisfaction', 'color' => 'orange'],
                    ['type' => 'dynamic', 'data_source' => 'certificates_count', 'target_value' => '100', 'suffix' => '%', 'label_ar' => 'شهادات دولية معتمدة', 'label_en' => 'Global Certifications', 'color' => 'teal'],
                ],
            ],
            [
                'key' => 'why-choose',
                'type' => 'features',
                'title_ar' => 'لماذا تختار أكاديمية إيليت؟',
                'title_en' => 'Why Choose Elite Academy?',
                'subtitle_ar' => 'نقدم تجربة تعليمية فريدة تجمع بين التفوق الأكاديمي والتطبيق العملي الحديث.',
                'subtitle_en' => 'We offer a unique educational experience combining academic excellence and modern practical application.',
                'is_enabled' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'about-preview',
                'type' => 'about',
                'title_ar' => 'عن أكاديمية إيليت',
                'title_en' => 'About Elite Academy',
                'subtitle_ar' => 'منصة تعليمية رائدة تهدف إلى بناء وتأهيل جيل جديد من المبتكرين.',
                'subtitle_en' => 'A leading educational platform aimed at building and preparing a new generation of innovators.',
                'badge_ar' => 'REDEFINING EDUCATION',
                'badge_en' => 'REDEFINING EDUCATION',
                'image_url' => 'images/logo.png',
                'is_enabled' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'subjects-grid',
                'type' => 'courses',
                'title_ar' => 'استكشف المواد الدراسية المتخصصة',
                'title_en' => 'Explore Specialized Subjects & Programs',
                'subtitle_ar' => 'مناهج مصممة خصيصاً لمواكبة سوق العمل والتميز الأكاديمي.',
                'subtitle_en' => 'Curricula designed to meet job market demands and academic distinction.',
                'is_enabled' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'teachers-marquee',
                'type' => 'teachers',
                'title_ar' => 'تعرف على نخبة المعلمين والمرشدين',
                'title_en' => 'Meet Our Elite Mentors & Teachers',
                'subtitle_ar' => 'أفضل الخبراء والمحاضرين الأكاديميين في مصر والوطن العربي.',
                'subtitle_en' => 'Top academic experts and instructors in Egypt and the Arab region.',
                'is_enabled' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'testimonials',
                'type' => 'testimonials',
                'title_ar' => 'ماذا يقول طلابنا وأولياء أمورنا؟',
                'title_en' => 'What Students & Parents Say About Us',
                'subtitle_ar' => 'آراء حقيقية وتجارب نجاح ملهمة من مجتمع إيليت أكاديمي.',
                'subtitle_en' => 'Real reviews and inspiring success stories from the Elite Academy community.',
                'is_enabled' => true,
                'sort_order' => 6,
            ],
            [
                'key' => 'cta_section',
                'type' => 'cta',
                'title_ar' => 'هل أنت مستعد للتفوق في رحلتك الأكاديمية؟',
                'title_en' => 'Ready to Excel in Your Academic Journey?',
                'subtitle_ar' => 'انضم إلى أكاديمية إيليت اليوم واحصل على وصول غير محدود للمناهج والبث المباشر.',
                'subtitle_en' => 'Join Elite Academy today and gain unlimited access to courses and live sessions.',
                'is_enabled' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($defaults as $secData) {
            $counters = $secData['counters'] ?? [];
            unset($secData['counters']);

            $secData['section_key'] = $secData['key'] ?? $secData['section_key'] ?? ('sec_' . uniqid());
            unset($secData['key']);

            $secData['landing_page_id'] = $page->id;
            $secData['settings_json'] = [
                'bg_color' => 'transparent',
                'padding' => 'py-12',
                'tilt_angle' => 10,
                'depth_px' => 30,
                'glass_blur' => 'blur-md',
                'responsive' => ['desktop' => true, 'tablet' => true, 'mobile' => true],
            ];

            $section = LandingPageSection::create($secData);

            foreach ($counters as $cIndex => $cData) {
                LandingPageCounter::create([
                    'section_id' => $section->id,
                    'type' => $cData['type'] ?? 'manual',
                    'data_source' => $cData['data_source'] ?? null,
                    'target_value' => $cData['target_value'] ?? '100',
                    'suffix' => $cData['suffix'] ?? null,
                    'label_ar' => $cData['label_ar'] ?? null,
                    'label_en' => $cData['label_en'] ?? null,
                    'color' => $cData['color'] ?? 'teal',
                    'is_enabled' => true,
                    'sort_order' => $cIndex,
                ]);
            }
        }
    }
}
