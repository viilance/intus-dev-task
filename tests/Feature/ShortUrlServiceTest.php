<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use App\Models\User;
use App\Services\ShortUrlService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShortUrlServiceTest extends TestCase
{
    public function test_it_checks_if_a_url_is_safe(): void
    {
        Http::fake([
            'https://safebrowsing.googleapis.com/v4/threatMatches:find*' => Http::response(['matches' => []], 200),
        ]);

        $service = new ShortUrlService();
        $isSafe = $service->isUrlSafe('https://example.com');

        $this->assertTrue($isSafe);
    }

    public function test_it_creates_a_new_short_url_record(): void
    {
        $user = User::factory()->create();
        $url = 'https://example.com';
        $shortCode = 'abc123';

        $service = new ShortUrlService();
        $shortUrl = $service->createShortUrl($url, $shortCode, $user->id);

        $this->assertDatabaseHas('short_urls', [
            'original_url' => $url,
            'short_code' => $shortCode,
            'user_id' => $user->id,
        ]);

        $this->assertEquals($url, $shortUrl->original_url);
        $this->assertEquals($shortCode, $shortUrl->short_code);
        $this->assertEquals($user->id, $shortUrl->user_id);
    }

    public function test_it_returns_existing_short_url_for_given_url_and_user()
    {
        $user = User::factory()->create();
        $existingShortUrl = ShortUrl::factory()->create([
            'original_url' => 'https://example.com',
            'user_id' => $user->id,
        ]);

        $service = new ShortUrlService();
        $foundShortUrl = $service->getExistingShortUrl('https://example.com', $user->id);

        $this->assertNotNull($foundShortUrl);
        $this->assertEquals($existingShortUrl->short_code, $foundShortUrl->short_code);
    }

    public function test_it_returns_null_when_no_short_url_exists_for_given_url_and_user()
    {
        $user = User::factory()->create();

        $service = new ShortUrlService();
        $foundShortUrl = $service->getExistingShortUrl('https://example.com', $user->id);

        $this->assertNull($foundShortUrl);
    }

    public function test_it_shortens_a_new_url_successfully()
    {
        $user = User::factory()->create();
        $url = 'https://example.com';

        $mockedService = \Mockery::mock(ShortUrlService::class);
        $mockedService->shouldReceive('isUrlSafe')->andReturn(true);
        $mockedService->shouldReceive('getExistingShortUrl')->andReturn(null);
        $mockedService->shouldReceive('generateUniqueShortCode')->andReturn('abc123');
        $mockedService->shouldReceive('createShortUrl')
            ->andReturn(new ShortUrl([
                'original_url' => $url,
                'short_code' => 'abc123',
            ]));
        $this->app->instance(ShortUrlService::class, $mockedService);

        $response = $this->actingAs($user)->postJson('/shorten-url', ['url' => $url]);

        $response->assertStatus(200)
            ->assertJson(['shortUrl' => url('/short/abc123')]);
    }

    public function test_it_returns_an_error_for_an_unsafe_url()
    {
        $user = User::factory()->create();
        $url = 'http://unsafe-url.com';

        $mockedService = \Mockery::mock(ShortUrlService::class);
        $mockedService->shouldReceive('isUrlSafe')->andReturn(false);
        $this->app->instance(ShortUrlService::class, $mockedService);

        $response = $this->actingAs($user)->postJson('/shorten-url', ['url' => $url]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'URL is not safe']);
    }
}
