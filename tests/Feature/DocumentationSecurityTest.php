<?php

namespace Tests\Feature;

use App\Documentation;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Filesystem\Filesystem;

class DocumentationSecurityTest extends TestCase
{
    protected Documentation $docs;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->docs = app(Documentation::class);
    }

    /**
     * Test that legitimate paths work correctly
     */
    public function test_legitimate_paths_work(): void
    {
        // Test valid category/page path
        $result = $this->docs->get('start-selling/tutorial-creating-your-first-products');

        // Should return an array with content and frontMatter keys
        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('frontMatter', $result);
    }

    /**
     * Test that basic path traversal is blocked
     */
    public function test_blocks_basic_path_traversal(): void
    {
        $maliciousPaths = [
            '../../../etc/passwd',
            '../../.env',
            '../config/database',
            'start-selling/../../.env',
        ];

        foreach ($maliciousPaths as $path) {
            $result = $this->docs->get($path);

            $this->assertNull(
                $result,
                "Path traversal attack should be blocked: {$path}"
            );
        }
    }

    /**
     * Test that backslash directory traversal is blocked
     */
    public function test_blocks_backslash_traversal(): void
    {
        $maliciousPaths = [
            '..\\..\\..\\etc\\passwd',
            '..\\config\\database',
            'start-selling\\..\\..\\config\\app',
        ];

        foreach ($maliciousPaths as $path) {
            $result = $this->docs->get($path);

            $this->assertNull(
                $result,
                "Backslash traversal attack should be blocked: {$path}"
            );
        }
    }

    /**
     * Test that null byte injection is blocked
     */
    public function test_blocks_null_byte_injection(): void
    {
        $maliciousPaths = [
            "legitimate-path\0../../etc/passwd",
            "start-selling/product\0.php",
        ];

        foreach ($maliciousPaths as $path) {
            $result = $this->docs->get($path);

            $this->assertNull(
                $result,
                "Null byte injection should be blocked: " . addslashes($path)
            );
        }
    }

    /**
     * Test that protocol injection is blocked
     */
    public function test_blocks_protocol_injection(): void
    {
        $maliciousPaths = [
            'file:///etc/passwd',
            'http://evil.com/malicious',
            'ftp://attacker.com/data',
        ];

        foreach ($maliciousPaths as $path) {
            $result = $this->docs->get($path);

            $this->assertNull(
                $result,
                "Protocol injection should be blocked: {$path}"
            );
        }
    }

    /**
     * Test that invalid characters are blocked
     */
    public function test_blocks_invalid_characters(): void
    {
        $maliciousPaths = [
            'test<script>alert(1)</script>',
            'test; rm -rf /',
            'test|cat /etc/passwd',
            'test`whoami`',
            'test$(whoami)',
        ];

        foreach ($maliciousPaths as $path) {
            $result = $this->docs->get($path);

            $this->assertNull(
                $result,
                "Invalid characters should be blocked: {$path}"
            );
        }
    }

    /**
     * Test sectionExists method security
     */
    public function test_section_exists_blocks_path_traversal(): void
    {
        $exists = $this->docs->sectionExists('../../../etc/passwd');

        $this->assertFalse(
            $exists,
            "sectionExists should return false for path traversal attempts"
        );
    }

    /**
     * Test that route-level path traversal is blocked
     */
    public function test_route_blocks_path_traversal(): void
    {
        $response = $this->get('/docs/category/../../.env');

        // Should return 404, not expose files
        $response->assertStatus(404);
    }

    /**
     * Test that multiple encoding attempts are blocked
     */
    public function test_blocks_encoded_traversal(): void
    {
        // URL encoded dots
        $paths = [
            '%2e%2e%2f%2e%2e%2f',
            'start-selling%2f..%2f..%2f.env',
        ];

        foreach ($paths as $path) {
            // Test via URL
            $response = $this->get('/docs/category/' . $path);
            $response->assertStatus(404);
        }
    }

    /**
     * Test that absolute paths are blocked
     */
    public function test_blocks_absolute_paths(): void
    {
        $maliciousPaths = [
            '/etc/passwd',
            '/var/www/.env',
            'C:\\Windows\\System32\\config',
        ];

        foreach ($maliciousPaths as $path) {
            $result = $this->docs->get($path);

            $this->assertNull(
                $result,
                "Absolute paths should be blocked: {$path}"
            );
        }
    }
}
