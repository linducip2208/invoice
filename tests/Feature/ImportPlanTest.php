<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportPlanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $ws;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->user = User::factory()->create();
        $this->ws = Workspace::factory()->create();
        $this->user->workspaces()->attach($this->ws->id, ['role' => 'admin']);
        session(['current_workspace_id' => $this->ws->id]);
    }

    protected function csvFile(string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'csv') . '.csv';
        file_put_contents($path, $content);
        return new UploadedFile($path, 'clients.csv', 'text/csv', null, true);
    }

    #[Test] public function malformed_rows_skipped_valid_imported(): void
    {
        $this->giveProPlan($this->ws); // csv.import is a Pro-gated feature
        $file = $this->csvFile(
            "name,company_name,email\n" .
            "Good Client,Good Corp,good@example.test\n" .
            ",No Name Co,noname@example.test\n" .
            "Bad Email,Bad Corp,not-an-email\n" .
            "Second Good,,second@example.test\n"
        );

        $response = $this->actingAs($this->user)->post(route('clients.import'), ['file' => $file]);
        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', ['workspace_id' => $this->ws->id, 'name' => 'Good Client']);
        $this->assertDatabaseHas('clients', ['workspace_id' => $this->ws->id, 'name' => 'Second Good']);
        $this->assertDatabaseMissing('clients', ['email' => 'noname@example.test']);
        $this->assertDatabaseMissing('clients', ['email' => 'not-an-email']);
        $this->assertEquals(2, Client::where('workspace_id', $this->ws->id)->count());
    }

    #[Test] public function import_rejects_non_csv(): void
    {
        $this->giveProPlan($this->ws);
        $path = tempnam(sys_get_temp_dir(), 'bin') . '.exe';
        // Real binary content so finfo does not sniff it as text/plain.
        file_put_contents($path, "\x4d\x5a\x90\x00\x03\x00\x00\x00\xff\xd8\xff\xe0\x00\x10JFIF");
        $file = new UploadedFile($path, 'evil.exe', 'application/octet-stream', null, true);

        $this->actingAs($this->user)->post(route('clients.import'), ['file' => $file])
            ->assertSessionHasErrors('file');
        $this->assertEquals(0, Client::where('workspace_id', $this->ws->id)->count());
    }

    #[Test] public function free_plan_blocks_second_invoice(): void
    {
        // No subscription => auto free plan (max 1 lifetime invoice).
        $client = Client::factory()->create(['workspace_id' => $this->ws->id]);
        $payload = [
            'client_id' => $client->id, 'currency' => 'IDR',
            'issue_date' => now()->format('Y-m-d'), 'due_date' => now()->addDays(7)->format('Y-m-d'),
            'template' => 'classic', 'discount_type' => 'none', 'discount_value' => 0,
            'items' => [['description' => 'x', 'quantity' => 1, 'unit_price' => 1000, 'tax_rate' => 0]],
        ];
        $this->actingAs($this->user)->post(route('invoices.store'), $payload)->assertRedirect();
        $this->actingAs($this->user)->post(route('invoices.store'), $payload)
            ->assertRedirect(route('billing.show'));
    }

    #[Test] public function free_plan_blocks_sixth_client(): void
    {
        Client::factory()->count(5)->create(['workspace_id' => $this->ws->id]);
        $this->actingAs($this->user)->post(route('clients.store'), ['name' => 'Sixth', 'is_active' => '1'])
            ->assertRedirect(route('billing.show'));
        $this->assertEquals(5, Client::where('workspace_id', $this->ws->id)->count());
    }

    #[Test] public function pro_plan_allows_over_free_limits(): void
    {
        $this->giveProPlan($this->ws);
        Client::factory()->count(5)->create(['workspace_id' => $this->ws->id]);
        $this->actingAs($this->user)->post(route('clients.store'), ['name' => 'Sixth', 'is_active' => '1'])
            ->assertRedirect();
        $this->assertEquals(6, Client::where('workspace_id', $this->ws->id)->count());
    }
}
