<?php

namespace App\Repositories;

use App\Contracts\UserPreferenceRepositoryContract;
use App\Models\UserPreference;

class UserPreferenceRepository implements UserPreferenceRepositoryContract
{
    public function __construct(private UserPreference $model) {}

    public function findByUserId(int $userId): ?UserPreference
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function updateOrCreate(int $userId, array $data): UserPreference
    {
        return $this->model->updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }
}
