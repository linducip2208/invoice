<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Indonesia\Coretax\EfakturGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EfakturXmlTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function xml_escapes_special_chars_exactly_once(): void
    {
        $workspace = Workspace::factory()->create([
            'name' => 'PT Maju & Bersama <Test>',
            'tax_id' => '01.234.567.8-901.000',
        ]);
        $client = Client::factory()->create([
            'workspace_id' => $workspace->id,
            'company_name' => 'CV A&B "Mitra"',
        ]);
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $workspace->id,
            'client_id' => $client->id,
        ]);
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Jasa <konsultasi> & implementasi',
            'quantity' => 1,
            'unit_price' => 100000,
        ]);

        $xml = (new EfakturGenerator())->generateXml($invoice->fresh());

        // Well-formed: parses without errors.
        $parsed = simplexml_load_string($xml);
        $this->assertNotFalse($parsed);

        // Escaped exactly once — no double-escaping.
        $this->assertStringContainsString('PT Maju &amp; Bersama', $xml);
        $this->assertStringContainsString('&lt;Test&gt;', $xml);
        $this->assertStringNotContainsString('&amp;amp;', $xml);
        $this->assertStringNotContainsString('&amp;lt;', $xml);
        $this->assertStringContainsString('CV A&amp;B', $xml);
    }

    #[Test]
    public function save_xml_sanitizes_filename_and_keeps_approved_status(): void
    {
        $workspace = Workspace::factory()->create();
        $client = Client::factory()->create(['workspace_id' => $workspace->id]);
        $invoice = Invoice::factory()->create([
            'workspace_id' => $workspace->id,
            'client_id' => $client->id,
            'invoice_no' => '../../evil',
        ]);

        $gen = new EfakturGenerator();
        $path = $gen->saveXml($invoice);

        $this->assertStringNotContainsString('..', $path);
        $this->assertStringStartsWith('efaktur/' . $workspace->id . '/', $path);
        $this->assertDatabaseHas('invoice_tax_details', [
            'invoice_id' => $invoice->id,
            'efaktur_status' => 'draft',
        ]);

        // Re-export after approval must not regress status to draft.
        $invoice->taxDetail->update(['efaktur_status' => 'approved']);
        $gen->saveXml($invoice->fresh());

        $this->assertDatabaseHas('invoice_tax_details', [
            'invoice_id' => $invoice->id,
            'efaktur_status' => 'approved',
        ]);
    }
}
