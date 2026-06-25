<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePreferenceRequest;
use App\Http\Resources\ArticleResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function __construct(private UserPreferenceService $preferenceService) {}

    public function show(Request $request)
    {
        $preferences = $this->preferenceService->getPreferences($request->user()->id);
        return $this->success($preferences);
    }

    public function update(UpdatePreferenceRequest $request)
    {
        $data = $this->preferenceService->updatePreferences(
            $request->user()->id,
            $request->validated()
        );

        return $this->success($data, 'Preferences updated successfully.');
    }

    public function feed(Request $request)
    {
        $feeds = $this->preferenceService->personalizedFeed($request->user()->id);
        return ArticleResource::collection($feeds);
    }
}
