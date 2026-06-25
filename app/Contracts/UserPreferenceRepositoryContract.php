<?php

namespace App\Contracts;

use App\Models\UserPreference;

interface UserPreferenceRepositoryContract
{
    public function findByUserId(int $userId): ?UserPreference;
    public function updateOrCreate(int $userId, array $data): UserPreference;
}
