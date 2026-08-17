<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachersData = [
            [
                'name' => 'د. أحمد محمود',
                'email' => 'dr.ahmed.mahmoud@elite.edu',
                'phone' => '+201000000101',
                'slug' => 'dr-ahmed-mahmoud',
                'title' => 'أستاذ الفيزياء والفيزياء الحديثة',
                'specialization' => 'Physics',
                'bio' => 'دكتوراه في الفيزياء التطبيقية وحاصل على جوائز تميز أكاديمي. أكثر من 15 عاماً في إعداد طلاب الثانوية العامة للدرجات النهائية.',
                'years_experience' => 15,
                'rating_avg' => 4.9,
                'students_count' => 2450,
                'photo' => 'images/instructor_portrait.png',
                'is_featured' => true,
                'is_public' => true,
            ],
            [
                'name' => 'أ. سارة محمد',
                'email' => 'sarah.mohamed@elite.edu',
                'phone' => '+201000000102',
                'slug' => 'sarah-mohamed',
                'title' => 'كبير معلمي الكيمياء العضوية والتحليلية',
                'specialization' => 'Chemistry',
                'bio' => 'خبرة 12 عاماً في شرح مادة الكيمياء للمرحلة الثانوية بأساليب التفكير النقدي وتطبيق تجارب المعامل التفاعلية.',
                'years_experience' => 12,
                'rating_avg' => 4.8,
                'students_count' => 1890,
                'photo' => 'images/instructor_female.png',
                'is_featured' => true,
                'is_public' => true,
            ],
            [
                'name' => 'د. عمر خالد',
                'email' => 'dr.omar.khaled@elite.edu',
                'phone' => '+201000000103',
                'slug' => 'dr-omar-khaled',
                'title' => 'أستاذ الرياضيات البحتة والتطبيقية',
                'specialization' => 'Mathematics',
                'bio' => 'متخصص في التفاضل والتكامل والجبر والهندسة الفراغية. قام بتدريب أكثر من 10,000 طالب على مدار مشواره المهني.',
                'years_experience' => 18,
                'rating_avg' => 4.9,
                'students_count' => 3100,
                'photo' => 'images/instructor_male.png',
                'is_featured' => true,
                'is_public' => true,
            ],
            [
                'name' => 'د. فاطمة علي',
                'email' => 'fatma.ali@elite.edu',
                'phone' => '+201000000104',
                'slug' => 'dr-fatma-ali',
                'title' => 'أستاذة الأحياء وعلم الجينات',
                'specialization' => 'Biology',
                'bio' => 'ماجستير في العلوم البيولوجية. تشرح دروس البيولوجيا وجيولوجيا الأرض باستخدام الرسوم المتحركة ثلاثية الأبعاد.',
                'years_experience' => 10,
                'rating_avg' => 4.9,
                'students_count' => 1650,
                'photo' => 'images/instructor_female.png',
                'is_featured' => true,
                'is_public' => true,
            ],
            [
                'name' => 'م. كريم زكي',
                'email' => 'kareem.zaki@elite.edu',
                'phone' => '+201000000105',
                'slug' => 'eng-kareem-zaki',
                'title' => 'خبير البرمجيات والذكاء الاصطناعي',
                'specialization' => 'Programming',
                'bio' => 'مهندس برمجيات ومحاضر تقني في علوم الحاسب ولغات البرمجة (Python, Web Development, Algorithms).',
                'years_experience' => 8,
                'rating_avg' => 4.9,
                'students_count' => 2200,
                'photo' => 'images/instructor_male.png',
                'is_featured' => true,
                'is_public' => true,
            ],
            [
                'name' => 'أ. هدى محمود',
                'email' => 'hoda.mahmoud@elite.edu',
                'phone' => '+201000000106',
                'slug' => 'hoda-mahmoud',
                'title' => 'كبير معلمي اللغة الإنجليزية وآدابها',
                'specialization' => 'English',
                'bio' => 'خبير المناهج البريطانية واللغة الإنجليزية للثانوية العامة. تركيز مكثف على القواعد، البلاغة، والقراءة المستفيضة.',
                'years_experience' => 14,
                'rating_avg' => 4.8,
                'students_count' => 1780,
                'photo' => 'images/instructor_female.png',
                'is_featured' => false,
                'is_public' => true,
            ],
            [
                'name' => 'د. طارق فؤاد',
                'email' => 'tarek.fouad@elite.edu',
                'phone' => '+201000000107',
                'slug' => 'dr-tarek-fouad',
                'title' => 'استشاري اللغة العربية والنحو والصرف',
                'specialization' => 'Arabic',
                'bio' => 'دكتوراه في البلاغة والنقد الأدبي. أسلوب مبسط في تبسيط قواعد النحو والصرف وتدريب الطلاب على نماذج الامتحانات النهائية.',
                'years_experience' => 22,
                'rating_avg' => 5.0,
                'students_count' => 4200,
                'photo' => 'images/instructor_portrait.png',
                'is_featured' => true,
                'is_public' => true,
            ],
            [
                'name' => 'أ. نوران سامي',
                'email' => 'nouran.samy@elite.edu',
                'phone' => '+201000000108',
                'slug' => 'nouran-samy',
                'title' => 'محاضرة البرمجة والذكاء الاصطناعي للناشئين',
                'specialization' => 'AI & Tech',
                'bio' => 'مدربة معتمدة في الذكاء الاصطناعي وبناء التطبيقات الذكية للطلاب والتجهيز للمسابقات الأولمبية الدولية.',
                'years_experience' => 7,
                'rating_avg' => 4.9,
                'students_count' => 1350,
                'photo' => 'images/instructor_female.png',
                'is_featured' => false,
                'is_public' => true,
            ],
        ];

        foreach ($teachersData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => bcrypt('password'),
                    'status' => AccountStatus::APPROVED,
                    'email_verified_at' => now(),
                ]
            );

            $profile = TeacherProfile::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'specialization' => $data['specialization'],
                    'bio' => $data['bio'],
                    'years_experience' => $data['years_experience'],
                    'rating_avg' => $data['rating_avg'],
                    'students_count' => $data['students_count'],
                    'photo' => $data['photo'],
                    'is_featured' => $data['is_featured'],
                    'is_public' => $data['is_public'],
                    'show_contact_info' => true,
                ]
            );

            // Attach matching subject if exists
            $subject = Subject::where('name', 'LIKE', "%{$data['specialization']}%")
                ->orWhere('slug', strtolower($data['specialization']))
                ->first();

            if ($subject) {
                $profile->subjects()->syncWithoutDetaching([$subject->id]);
            }
        }
    }
}
