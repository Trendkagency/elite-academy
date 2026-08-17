<?php

namespace App\Models;

use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
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

    public function isAdmin(): bool
    {
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
}
