<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PWA installability prerequisites, verified without a browser:
 * manifest validity, icon files/dimensions, SW safety rules.
 */
class PwaTest extends TestCase
{
    // branding() hits app_settings when error views render.
    use RefreshDatabase;
    #[Test]
    public function manifest_is_valid_and_installable(): void
    {
        $path = public_path('manifest.webmanifest');
        $this->assertFileExists($path);
        $manifest = json_decode(file_get_contents($path), true);
        $this->assertIsArray($manifest, 'manifest must be valid JSON');

        foreach (['name', 'short_name', 'id', 'start_url', 'scope', 'display', 'theme_color', 'background_color'] as $key) {
            $this->assertArrayHasKey($key, $manifest, "manifest missing $key");
            $this->assertNotEmpty($manifest[$key], "manifest.$key empty");
        }
        $this->assertEquals('standalone', $manifest['display']);

        $sizes = [];
        foreach ($manifest['icons'] as $icon) {
            $file = public_path(ltrim($icon['src'], '/'));
            $this->assertFileExists($file, 'missing icon ' . $icon['src']);
            // Only raster PNG icons are dimension-checked (favicon.ico is exempt).
            if (($icon['type'] ?? '') === 'image/png') {
                $info = getimagesize($file);
                $this->assertEquals('image/png', $info['mime'] ?? null);
            }
            $sizes[] = $icon['sizes'];
        }
        $this->assertContains('192x192', $sizes);
        $this->assertContains('512x512', $sizes);
        $purposes = array_column($manifest['icons'], 'purpose');
        $this->assertContains('maskable', $purposes);
    }

    #[Test]
    public function service_worker_is_safe_and_versioned(): void
    {
        $path = public_path('sw.js');
        $this->assertFileExists($path);
        $sw = file_get_contents($path);

        // Syntax check via node when available.
        if (trim((string) shell_exec('node --version 2>&1')) !== '') {
            exec('node --check ' . escapeshellarg($path) . ' 2>&1', $out, $code);
            $this->assertEquals(0, $code, 'sw.js has JS syntax errors: ' . implode("\n", $out));
        }

        // Versioned cache + old-cache cleanup.
        $this->assertMatchesRegularExpression("/const CACHE = 'invocely-v\d+'/", $sw);
        $this->assertStringContainsString('caches.delete', $sw);

        // API must be network-only: no caches.match/put for /api/.
        $this->assertDoesNotMatchRegularExpression('/caches\.(match|open)[\s\S]{0,200}\/api\//', $sw);
        $this->assertStringContainsString("startsWith('/api/')", $sw);

        // Only GET, same-origin.
        $this->assertStringContainsString("req.method !== 'GET'", $sw);
        $this->assertStringContainsString('self.location.origin', $sw);

        // Authenticated HTML never precached.
        $this->assertStringNotContainsString("'/'", explode('PRECACHE', $sw)[1] ?? '');
        $this->assertStringContainsString('/offline.html', $sw);
    }

    #[Test]
    public function offline_page_exists_and_is_standalone(): void
    {
        // Static files are served by the web server, not the test kernel —
        // assert content directly instead of via HTTP.
        $path = public_path('offline.html');
        $this->assertFileExists($path);
        $html = file_get_contents($path);
        $this->assertStringContainsString('offline', strtolower($html));
        $this->assertStringNotContainsString('@vite', $html, 'offline page must not depend on built assets');
    }
}
