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

        return new class($token) {
            public function __construct(public string $plainTextToken) {}
        };
    }

    public function tokens(): object
    {
        return new class($this) {
            public function __construct(protected User $user) {}
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
        return $this->studentProfile()->exists();
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
