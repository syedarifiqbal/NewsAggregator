<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryInterface;
use App\Contracts\UserPreferenceRepositoryInterface;
use App\Models\UserPreference;
use Illuminate\Pagination\LengthAwarePaginator;

class UserPreferenceService
{
    public function __construct(
        private UserPreferenceRepositoryInterface $preferenceRepo,
        private ArticleRepositoryInterface $articleRepo,
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
