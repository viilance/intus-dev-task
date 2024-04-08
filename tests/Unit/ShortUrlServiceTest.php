<?php

namespace Tests\Unit;

use App\Services\ShortUrlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_unique_6_character_short_code(): void
    {
        $service = new ShortUrlService();

        $shortCode = $service->generateUniqueShortCode();

        $this->assertIsString($shortCode);
        $this->assertEquals(6, strlen($shortCode));
        $this->assertMatchesRegularExpression('/^[0-9A-Za-z]{6}$/', $shortCode);
    }
}
