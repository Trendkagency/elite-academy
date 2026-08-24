<?php

namespace App\Console\Commands;

use App\Enums\AccountStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedTwentyFiveThousandUsers extends Command
{
    protected $signature = 'app:seed-25k-users {--count=25000 : Total number of users to seed}';
    protected $description = 'High-performance bulk seeder for 25,000 multi-role users and associated profiles';

    public function handle(): int
    {
        $totalTarget = (int) $this->option('count');
        $this->info("Starting high-performance bulk database seeder for {$totalTarget} users...");

        $startTime = microtime(true);

        // Calculate role breakdown
        $adminCount = 50;
        $teacherCount = 450;
        $parentCount = 4500;
        $studentCount = $totalTarget - ($adminCount + $teacherCount + $parentCount); // 20,000

        $passwordHash = Hash::make('password');
        $now = now()->toDateTimeString();

        $this->output->progressStart($totalTarget);

        // 1. Seed Admins (50)
        $this->seedRole('admin', $adminCount, $passwordHash, $now);

        // 2. Seed Teachers (450)
        $teacherIds = $this->seedRole('teacher', $teacherCount, $passwordHash, $now);

        // 3. Seed Students (20,000)
        $studentData = $this->seedRoleWithPhones('student', $studentCount, $passwordHash, $now);

        // 4. Seed Parents (4,500)
        $this->seedParents('parent', $parentCount, $passwordHash, $now, $studentData['phones']);

        $this->output->progressFinish();

        $duration = round(microtime(true) - $startTime, 2);
        $totalInDb = DB::table('users')->count();

        $this->info("Successfully seeded {$totalTarget} users in {$duration} seconds!");
        $this->info("Total users in database: {$totalInDb}");

        return Command::SUCCESS;
    }

    protected function seedRole(string $role, int $count, string $passwordHash, string $now): array
    {
        $chunkSize = 1000;
        $createdIds = [];

        for ($i = 0; $i < $count; $i += $chunkSize) {
            $currentChunkSize = min($chunkSize, $count - $i);
            $userRows = [];
            $profileRows = [];

            for ($j = 0; $j < $currentChunkSize; $j++) {
                $idx = $i + $j + 1;
                $email = "{$role}_{$idx}_" . Str::random(5) . "@elite-test.com";
                $phone = "+201" . str_pad((string) rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);

                $userRows[] = [
                    'name' => ucfirst($role) . " User {$idx}",
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $passwordHash,
                    'status' => AccountStatus::APPROVED->value,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::transaction(function () use ($userRows, $role, $now, &$createdIds) {
                DB::table('users')->insert($userRows);

                // Fetch newly inserted IDs
                $emails = array_column($userRows, 'email');
                $insertedUsers = DB::table('users')->whereIn('email', $emails)->select('id')->get();

                $profileRows = [];
                $tableName = match ($role) {
                    'admin' => 'admin_profiles',
                    'teacher' => 'teacher_profiles',
                    'student' => 'student_profiles',
                    default => null
                };

                foreach ($insertedUsers as $user) {
                    $createdIds[] = $user->id;
                    if ($tableName) {
                        $profileData = ['user_id' => $user->id, 'created_at' => $now, 'updated_at' => $now];
                        if ($role === 'teacher') {
                            $profileData['slug'] = "teacher-{$user->id}-" . Str::random(6);
                            $profileData['title'] = 'Senior Instructor';
                            $profileData['specialization'] = 'Physics & Computer Science';
                            $profileData['bio'] = 'Expert educator with 10+ years experience';
                        } elseif ($role === 'student') {
                            $profileData['grade_level_id'] = 1;
                        }
                        $profileRows[] = $profileData;
                    }
                }

                if ($tableName && !empty($profileRows)) {
                    DB::table($tableName)->insert($profileRows);
                }
            });

            $this->output->progressAdvance($currentChunkSize);
        }

        return $createdIds;
    }

    protected function seedRoleWithPhones(string $role, int $count, string $passwordHash, string $now): array
    {
        $chunkSize = 2000;
        $createdPhones = [];

        for ($i = 0; $i < $count; $i += $chunkSize) {
            $currentChunkSize = min($chunkSize, $count - $i);
            $userRows = [];

            for ($j = 0; $j < $currentChunkSize; $j++) {
                $idx = $i + $j + 1;
                $email = "student_{$idx}_" . Str::random(6) . "@elite-student.com";
                $phone = "+201" . str_pad((string) (10000000 + $i + $j), 8, '0', STR_PAD_LEFT);
                $createdPhones[] = $phone;

                $userRows[] = [
                    'name' => "Student Candidate {$idx}",
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $passwordHash,
                    'status' => AccountStatus::APPROVED->value,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::transaction(function () use ($userRows, $now) {
                DB::table('users')->insert($userRows);

                $emails = array_column($userRows, 'email');
                $insertedUsers = DB::table('users')->whereIn('email', $emails)->select('id')->get();

                $profileRows = [];
                foreach ($insertedUsers as $u) {
                    $profileRows[] = [
                        'user_id' => $u->id,
                        'grade_level_id' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('student_profiles')->insert($profileRows);
            });

            $this->output->progressAdvance($currentChunkSize);
        }

        return ['phones' => $createdPhones];
    }

    protected function seedParents(string $role, int $count, string $passwordHash, string $now, array $studentPhones): void
    {
        $chunkSize = 1000;

        for ($i = 0; $i < $count; $i += $chunkSize) {
            $currentChunkSize = min($chunkSize, $count - $i);
            $userRows = [];

            for ($j = 0; $j < $currentChunkSize; $j++) {
                $idx = $i + $j + 1;
                $email = "parent_{$idx}_" . Str::random(5) . "@elite-parent.com";
                $phone = "+201" . str_pad((string) (50000000 + $i + $j), 8, '0', STR_PAD_LEFT);

                $userRows[] = [
                    'name' => "Parent Guardian {$idx}",
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $passwordHash,
                    'status' => AccountStatus::APPROVED->value,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::transaction(function () use ($userRows, $now) {
                DB::table('users')->insert($userRows);

                $emails = array_column($userRows, 'email');
                $insertedUsers = DB::table('users')->whereIn('email', $emails)->select('id')->get();

                $profileRows = [];
                foreach ($insertedUsers as $u) {
                    $profileRows[] = [
                        'user_id' => $u->id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('parent_profiles')->insert($profileRows);
            });

            $this->output->progressAdvance($currentChunkSize);
        }
    }
}
