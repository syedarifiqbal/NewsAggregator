<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryContract;
use App\Contracts\UserPreferenceRepositoryContract;
use Illuminate\Pagination\LengthAwarePaginator;

class UserPreferenceService
{
    public function __construct(
        private UserPreferenceRepositoryContract $preferenceRepo,
        private ArticleRepositoryContract $articleRepo,
    ) {}

    public function getPreferences(int $userId): array
    {
        $preference = $this->preferenceRepo->findByUserId($userId);

        return [
            'preferred_sources' => $preference->preferred_sources ?? [],
            'preferred_categories' => $preference->preferred_categories ?? [],
            'preferred_authors' => $preference->preferred_authors ?? [],
        ];
    }

    public function updatePreferences(int $userId, array $data): array
    {
        $preference = $this->preferenceRepo->updateOrCreate($userId, $data);

        return [
            'preferred_sources' => $preference->preferred_sources ?? [],
            'preferred_categories' => $preference->preferred_categories ?? [],
            'preferred_authors' => $preference->preferred_authors ?? [],
        ];
    }

    public function personalizedFeed(int $userId): LengthAwarePaginator
    {
        $preference = $this->preferenceRepo->findByUserId($userId);

        if (!$preference) {
            return $this->articleRepo->index();
        }

        return $this->articleRepo->personalizedFeed($preference->toArray());
    }
}
