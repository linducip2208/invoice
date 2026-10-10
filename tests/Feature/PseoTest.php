<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PseoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function landing_page_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Invocely');
    }

    #[Test]
    public function docs_page_loads(): void
    {
        $response = $this->get('/docs');
        $response->assertStatus(200);
    }

    #[Test]
    public function pricing_page_loads(): void
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);
    }

    #[Test]
    public function sitemap_xml_returns_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
    }

    #[Test]
    public function robots_txt_is_accessible(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertStatus(200);
    }

    #[Test]
    public function best_tools_pseo_page_loads(): void
    {
        $response = $this->get('/best-invoicing-tools-' . now()->year);
        $response->assertStatus(200);
    }

    #[Test]
    public function alternatives_pseo_page_loads(): void
    {
        $response = $this->get('/alternatives-to-freshbooks');
        $response->assertStatus(200);
    }

    #[Test]
    public function compare_pseo_page_loads(): void
    {
        $response = $this->get('/compare/invocely-vs-freshbooks');
        $response->assertStatus(200);
    }

    #[Test]
    public function invoice_templates_page_loads(): void
    {
        $response = $this->get('/invoice-templates');
        $response->assertStatus(200);
    }

    #[Test]
    public function indexnow_key_file_exists(): void
    {
        $this->assertFileExists(public_path('indexnow-key.txt'));
        $this->assertNotEmpty(trim(file_get_contents(public_path('indexnow-key.txt'))));
    }
}
