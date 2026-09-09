<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => AccountStatus::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::updated(function (User $user) {
            if ($user->wasChanged('status')) {
                $statusValue = $user->status instanceof AccountStatus ? $user->status->value : (string) $user->status;
                if ($statusValue === AccountStatus::APPROVED->value || $statusValue === 'approved') {
                    app(\App\Services\Notification\FcmNotificationService::class)->notifyAccountApproved($user);
                }
            }
        });
    }

    /**
     * Strict Security Authorization for Filament Admin Panel.
     * Only approved users with Admin privileges (AdminProfile) can access /admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        $statusValue = $this->status instanceof AccountStatus ? $this->status->value : (string) $this->status;
        if ($statusValue !== AccountStatus::APPROVED->value && $statusValue !== 'approved') {
            return false;
        }

        return $this->isAdmin();
    }

    public function createToken(string $name): object
    {
        $token = Str::random(60);
        $this->update(['remember_token' => $token]);

        return new class ($token) {
            public function __construct(public string $plainTextToken)
            {}
        };
    }

    public function tokens(): object
    {
        return new class ($this) {
            public function __construct(protected User $user)
            {}
            public function delete(): bool
            {
                return $this->user->update(['remember_token' => null]);
            }
        };
    }

    public function adminProfile(): HasOne
    {
        return $this->hasOne(AdminProfile::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_user_id', 'student_user_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function parents(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_user_id', 'parent_user_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function studentEducationalNotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentEducationalNote::class, 'student_user_id');
    }

    public function scopeRoleStudent($query)
    {
        return $query->whereDoesntHave('teacherProfile')
            ->whereDoesntHave('parentProfile')
            ->whereDoesntHave('adminProfile')
            ->whereNotIn('email', ['admin@elite-academy.com', 'admin@elite.edu']);
    }

    public function scopeRoleTeacher($query)
    {
        return $query->whereHas('teacherProfile');
    }

    public function scopeRoleParent($query)
    {
        return $query->whereHas('parentProfile');
    }

    public function scopeRoleAdmin($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('adminProfile')
                ->orWhereIn('email', ['admin@elite-academy.com', 'admin@elite.edu']);
        });
    }

    public function isAdmin(): bool
    {
        if (in_array($this->email, ['admin@elite-academy.com', 'admin@elite.edu'], true)) {
            return true;
        }

        return $this->adminProfile()->exists();
    }

    public function isTeacher(): bool
    {
        return $this->teacherProfile()->exists();
    }

    public function isStudent(): bool
    {
        if ($this->isAdmin() || $this->isTeacher() || $this->isParent()) {
            return false;
        }

        return true;
    }

    public function isParent(): bool
    {
        return $this->parentProfile()->exists();
    }

    public function getRoleName(): string
    {
        if ($this->isAdmin()) {
            return \App\Enums\Role::ADMIN->value;
        }
        if ($this->isTeacher()) {
            return \App\Enums\Role::TEACHER->value;
        }
        if ($this->isParent()) {
            return \App\Enums\Role::PARENT->value;
        }

        return \App\Enums\Role::STUDENT->value;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->relationLoaded('teacherProfile') && $this->teacherProfile) {
            return $this->teacherProfile->photo_url;
        }

        if ($this->relationLoaded('studentProfile') && $this->studentProfile) {
            return $this->studentProfile->avatar_url;
        }

        if ($this->teacherProfile) {
            return $this->teacherProfile->photo_url;
        }

        if ($this->studentProfile) {
            return $this->studentProfile->avatar_url;
        }

        $name = urlencode($this->name ?? 'User');

        return "https://ui-avatars.com/api/?name={$name}&background=6366F1&color=ffffff&size=200&bold=true&font-size=0.38";
    }

    public function syncAssignedRole(string $role, array $profileData = []): void
    {
        switch ($role) {
            case 'student':
                TeacherProfile::where('user_id', $this->id)->forceDelete();
                ParentProfile::where('user_id', $this->id)->forceDelete();
                if (!in_array($this->email, ['admin@elite-academy.com', 'admin@elite.edu'], true)) {
                    AdminProfile::where('user_id', $this->id)->delete();
                }

                $profile = StudentProfile::withTrashed()->firstOrNew(['user_id' => $this->id]);
                if ($profile->trashed()) {
                    $profile->restore();
                }
                $profile->fill([
                    'grade_level_id' => $profileData['grade_level_id'] ?? $profile->grade_level_id,
                    'school_name' => $profileData['school_name'] ?? $profile->school_name,
                ]);
                $profile->save();

                if (isset($profileData['student_subjects'])) {
                    $profile->subjects()->sync($profileData['student_subjects']);
                }
                break;

            case 'teacher':
                StudentProfile::where('user_id', $this->id)->forceDelete();
                ParentProfile::where('user_id', $this->id)->forceDelete();
                if (!in_array($this->email, ['admin@elite-academy.com', 'admin@elite.edu'], true)) {
                    AdminProfile::where('user_id', $this->id)->delete();
                }

                $profile = TeacherProfile::withTrashed()->firstOrNew(['user_id' => $this->id]);
                if ($profile->trashed()) {
                    $profile->restore();
                }
                $slug = $profile->slug ?: (\Illuminate\Support\Str::slug($this->name ?: 'teacher') . '-' . $this->id);
                $profile->fill([
                    'slug' => $slug,
                    'title' => $profileData['teacher_title'] ?? $profile->title,
                    'specialization' => $profileData['teacher_specialization'] ?? $profile->specialization,
                    'years_experience' => $profileData['teacher_experience'] ?? ($profile->years_experience ?: 5),
                ]);
                $profile->save();
                break;

            case 'parent':
                StudentProfile::where('user_id', $this->id)->forceDelete();
                TeacherProfile::where('user_id', $this->id)->forceDelete();
                if (!in_array($this->email, ['admin@elite-academy.com', 'admin@elite.edu'], true)) {
                    AdminProfile::where('user_id', $this->id)->delete();
                }

                $profile = ParentProfile::withTrashed()->firstOrNew(['user_id' => $this->id]);
                if ($profile->trashed()) {
                    $profile->restore();
                }
                $profile->save();

                if (isset($profileData['parent_students'])) {
                    $this->children()->sync($profileData['parent_students']);
                }
                break;

            case 'admin':
                StudentProfile::where('user_id', $this->id)->forceDelete();
                TeacherProfile::where('user_id', $this->id)->forceDelete();
                ParentProfile::where('user_id', $this->id)->forceDelete();

                AdminProfile::firstOrCreate(['user_id' => $this->id]);
                break;
        }
    }

    public function getPermissionsList(): array
    {
        return \App\Permissions\PermissionsRegistry::defaultPermissionsForRole($this->getRoleName());
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $permissions = $this->getPermissionsList();

        return in_array($permission, $permissions, true);
    }
}
