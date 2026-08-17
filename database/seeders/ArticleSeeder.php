<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::first();
        $authorId = $adminUser ? $adminUser->id : null;

        $articles = [
            [
                'title' => 'How to Prepare for Final Exams Without Stress',
                'category' => 'Study Tips',
                'excerpt' => 'Final exams don\'t have to trigger burnout or anxiety. Learn how to structure Pomodoro blocks, prioritize high-yield topics, and maintain peak focus.',
                'content' => "Final exams don't have to trigger burnout or anxiety. By breaking revision sessions into structured Pomodoro blocks, prioritizing high-yield topics, and reviewing past exam papers, you can build steady confidence and achieve top scores while maintaining a healthy sleep schedule.\n\nKey Strategies:\n1. Spaced Repetition over Cramming\n2. Active Recall & Practice Problems\n3. Consistent Sleep Hygiene",
                'image' => 'images/hero_student.png',
                'read_time_minutes' => 6,
                'is_published' => true,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Top 10 Python Projects Every Student Should Build in 2026',
                'category' => 'Programming',
                'excerpt' => 'Building real-world projects is the fastest way to master software engineering concepts. From web scrapers to interactive AI bots, explore these beginner-friendly projects.',
                'content' => "Building real-world projects is the fastest way to master software engineering concepts. From automated web scrapers and interactive quiz bots to data analysis dashboards, these 10 beginner-friendly Python applications will sharpen your computational thinking and boost your academic portfolio.\n\nFeatured Projects:\n1. Automated Task Queue & CLI Tools\n2. RESTful API Server with FastAPI\n3. Computer Vision Emotion Detector",
                'image' => 'images/course_ai.png',
                'read_time_minutes' => 8,
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Understanding Transformer Architectures & LLM Tuning',
                'category' => 'AI & Tech',
                'excerpt' => 'Artificial intelligence is revolutionizing education and software architecture. Discover how transformer models and self-attention mechanisms work.',
                'content' => "Artificial intelligence is revolutionizing education and software architecture. Discover how transformer models, self-attention mechanisms, and fine-tuning pipelines empower modern generative applications.\n\nTopics Covered:\n- Encoder-Decoder Mechanisms\n- Tokenization Strategies\n- Efficient Fine-Tuning with LoRA",
                'image' => 'images/academy_campus.png',
                'read_time_minutes' => 10,
                'is_published' => true,
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Elite Academy Term 1 Registration & Exam Schedule Announcement',
                'category' => 'Announcements',
                'excerpt' => 'Official announcement regarding Term 1 registration deadlines, live session Q&A schedules, and accreditation certification details.',
                'content' => "We are excited to announce that registration for Term 1 accredited courses is now officially open across all grade levels. Students enrolled will receive full access to live interactive sessions, video lectures, and graded homework feedback.",
                'image' => 'images/hero_student.png',
                'read_time_minutes' => 4,
                'is_published' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Mastering Calculus & Coordinate Geometry for Secondary Exams',
                'category' => 'Mathematics',
                'excerpt' => 'Step-by-step guidance on solving complex integration, derivatives, and matrix operations prepared for Ministry of Education curriculum standards.',
                'content' => "Calculus and analytical geometry form the backbone of modern engineering and science disciplines. In this guide, our senior faculty breaks down step-by-step methods to tackle derivative applications, matrix determinants, and vector geometry.",
                'image' => 'images/academy_campus.png',
                'read_time_minutes' => 7,
                'is_published' => true,
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Electromagnetism Lab Experiments: Virtual vs Practical Insights',
                'category' => 'Science',
                'excerpt' => 'An in-depth look at magnetic flux, induction laws, and circuit analysis through practical laboratory demonstrations.',
                'content' => "Understanding electromagnetism requires both theoretical comprehension and physical intuition. Learn how virtual simulations combined with hands-on lab exercises help students grasp Faraday's Law and circuit impedance.",
                'image' => 'images/course_ai.png',
                'read_time_minutes' => 6,
                'is_published' => true,
                'published_at' => now()->subDays(15),
            ],
        ];

        foreach ($articles as $a) {
            Article::firstOrCreate(
                ['slug' => Str::slug($a['title'])],
                array_merge($a, [
                    'author_user_id' => $authorId,
                    'slug' => Str::slug($a['title']),
                ])
            );
        }
    }
}
