<?php

namespace App\Services;

use App\Models\ShortUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ShortUrlService
{
    public function isUrlSafe(string $url): bool
    {
        $payload = [
            'client' => [
                'clientId' => 'devtask',
                'clientVersion' => '1.5.2'
            ],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING'],
                'platformTypes' => ['ANY_PLATFORM'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [['url' => $url]]
            ]
        ];

        try {
            $apiKey = config('services.google_safe_browsing.api_key');
            $response = Http::post(
                "https://safebrowsing.googleapis.com/v4/threatMatches:find?key={$apiKey}",
                $payload
            );

            return empty($response->json('matches'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function generateUniqueShortCode(): string
    {
        do {
            $shortCode = Str::random(6);
            $existingUrl = ShortUrl::where('short_code', $shortCode)->first();
        } while ($existingUrl);

        return $shortCode;
    }

    public function getExistingShortUrl(string $url, int $userId): ?ShortUrl
    {
        return ShortUrl::where('original_url', $url)
            ->where('user_id', $userId) // depends on if we want to allow same url for multiple users or not
            ->first();
    }

    public function createShortUrl(string $url, string $shortCode, int $userId): ShortUrl
    {
        $shortUrl = new ShortUrl([
            'original_url' => $url,
            'short_code' => $shortCode,
            'user_id' => $userId,
        ]);

        $shortUrl->save();

        return $shortUrl;
    }
}
