<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class RateLimitingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear rate limiter cache before each test
        Cache::flush();
    }

    /**
     * Test that routes are protected by rate limiting
     */
    public function test_routes_have_rate_limiting(): void
    {
        // Make multiple requests to test rate limiting
        $responses = [];

        for ($i = 0; $i < 125; $i++) {
            $responses[] = $this->get('/');
        }

        // First 120 requests should succeed (within limit)
        for ($i = 0; $i < 120; $i++) {
            $this->assertEquals(200, $responses[$i]->status(), "Request {$i} should succeed");
        }

        // Requests beyond limit should be throttled (429 Too Many Requests)
        for ($i = 120; $i < 125; $i++) {
            $this->assertEquals(429, $responses[$i]->status(), "Request {$i} should be rate limited");
        }
    }

    /**
     * Test rate limit headers are present
     */
    public function test_rate_limit_headers_are_present(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Laravel should include rate limit headers
        $this->assertNotNull($response->headers->get('X-RateLimit-Limit'));
        $this->assertNotNull($response->headers->get('X-RateLimit-Remaining'));
    }

    /**
     * Test docs endpoints are rate limited
     */
    public function test_docs_endpoints_are_rate_limited(): void
    {
        $response = $this->get('/merchant');

        $response->assertStatus(200);
        $this->assertNotNull($response->headers->get('X-RateLimit-Limit'));
    }

    /**
     * Test team page is rate limited
     */
    public function test_team_page_is_rate_limited(): void
    {
        $response = $this->get('/team');

        $response->assertStatus(200);
        $this->assertNotNull($response->headers->get('X-RateLimit-Limit'));
    }

    /**
     * Test API endpoint is rate limited
     */
    public function test_api_endpoint_is_rate_limited(): void
    {
        $response = $this->get('/merchant/index.json');

        $response->assertStatus(200);
        $this->assertNotNull($response->headers->get('X-RateLimit-Limit'));
        $this->assertEquals('120', $response->headers->get('X-RateLimit-Limit'));
    }
}
