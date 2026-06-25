<?php

namespace App\Contracts;

use App\Models\UserPreference;

interface UserPreferenceRepositoryInterface
{
    public function findByUserId(int $userId): ?UserPreference;
    public function updateOrCreate(int $userId, array $data): UserPreference;
}
