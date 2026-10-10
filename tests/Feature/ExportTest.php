<?php

namespace Tests\Feature;

use App\Http\Controllers\ExportController;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->user->workspaces()->attach($this->workspace->id, ['role' => 'admin']);
        $this->giveProPlan($this->workspace);
        session(['current_workspace_id' => $this->workspace->id]);
    }

    #[Test]
    public function csv_escapes_spreadsheet_formulas(): void
    {
        $ctrl = new ExportController();
        $this->assertEquals("'=CMD(1)", $ctrl->safeCell('=CMD(1)'));
        $this->assertEquals("'+1+1", $ctrl->safeCell('+1+1'));
        $this->assertEquals("'-2+3", $ctrl->safeCell('-2+3'));
        $this->assertEquals("'@evil", $ctrl->safeCell('@evil'));
        $this->assertEquals('Normal Name', $ctrl->safeCell('Normal Name'));
        $this->assertEquals(123, $ctrl->safeCell(123));
        $this->assertNull($ctrl->safeCell(null));
    }

    #[Test]
    public function invoice_export_never_emits_raw_formula(): void
    {
        $client = Client::factory()->create([
            'workspace_id' => $this->workspace->id,
            'name' => '=HYPERLINK("http://evil")',
        ]);
        Invoice::factory()->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('exports.invoices'));
        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString("'=HYPERLINK", $content);
        $this->assertStringNotContainsString('"=HYPERLINK', $content);
    }

    #[Test]
    public function exports_reflect_recorded_payments(): void
    {
        $client = Client::factory()->create(['workspace_id' => $this->workspace->id]);
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->workspace->id, 'client_id' => $client->id,
        ]);
        Payment::factory()->create([
            'workspace_id' => $this->workspace->id, 'invoice_id' => $invoice->id,
            'amount' => 55000, 'reference_no' => 'PAY-EXPORT-1',
        ]);

        $content = $this->actingAs($this->user)->get(route('exports.payments'))->streamedContent();
        $this->assertStringContainsString('PAY-EXPORT-1', $content);
        $this->assertStringContainsString('55000', $content);
    }
}
