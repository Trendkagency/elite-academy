<?php

namespace App\DTOs;

readonly class UserRegistrationDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $phone = null,
        public string $userType = 'student',
        public ?int $gradeLevelId = null,
        public ?string $schoolName = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            phone: $data['phone'] ?? null,
            userType: $data['user_type'] ?? 'student',
            gradeLevelId: isset($data['grade_level_id']) ? (int) $data['grade_level_id'] : null,
            schoolName: $data['school_name'] ?? null,
        );
    }
}
