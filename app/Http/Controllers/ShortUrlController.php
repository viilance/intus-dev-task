<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShortenUrlRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\ShortUrlService;

class ShortUrlController extends Controller
{
    protected ShortUrlService $shortUrlService;

    public function __construct(ShortUrlService $shortUrlService)
    {
        $this->shortUrlService = $shortUrlService;
    }

    public function shortenUrl(ShortenUrlRequest $request): JsonResponse
    {
        $url = $request->get('url');
        $user = Auth::user();

        if (!$this->shortUrlService->isUrlSafe($url)) {
            return response()->json(['error' => 'URL is not safe'], 422);
        }

        $userId = $user->getAuthIdentifier();
        $existingUrl = $this->shortUrlService->getExistingShortUrl($url, $userId);
        if ($existingUrl) {
            return response()->json(['shortUrl' => url("/short/{$existingUrl->short_code}")]);
        }

        $shortCode = $this->shortUrlService->generateUniqueShortCode();
        $this->shortUrlService->createShortUrl($url, $shortCode, $userId);

        return response()->json(['shortUrl' => url("/short/{$shortCode}")]);
    }
}
