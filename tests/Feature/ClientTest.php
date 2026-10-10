<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->user->workspaces()->attach($this->workspace->id, ['role' => 'admin']);
        session(['current_workspace_id' => $this->workspace->id]);
    }

    #[Test]
    public function authenticated_user_can_view_clients(): void
    {
        Client::factory()->count(5)->create(['workspace_id' => $this->workspace->id]);

        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertSee('Clients');
    }

    #[Test]
    public function user_can_create_client(): void
    {
        $data = [
            'name' => 'Test Client',
            'company_name' => 'Test Corp',
            'email' => 'client@test.com',
            'phone' => '081234567890',
            'address' => 'Jl. Test 123',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->user)->post(route('clients.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'workspace_id' => $this->workspace->id,
            'name' => 'Test Client',
            'email' => 'client@test.com',
        ]);
    }

    #[Test]
    public function user_can_update_client(): void
    {
        $client = Client::factory()->create(['workspace_id' => $this->workspace->id]);

        $response = $this->actingAs($this->user)
            ->put(route('clients.update', $client), [
                'name' => 'Updated Name',
                'is_active' => '1',
            ]);

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function user_can_delete_client(): void
    {
        $client = Client::factory()->create(['workspace_id' => $this->workspace->id]);

        $response = $this->actingAs($this->user)->delete(route('clients.destroy', $client));

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    #[Test]
    public function client_is_scoped_to_workspace(): void
    {
        $otherWorkspace = Workspace::factory()->create();
        $otherClient = Client::factory()->create(['workspace_id' => $otherWorkspace->id]);

        $response = $this->actingAs($this->user)->get(route('clients.show', $otherClient));

        $response->assertStatus(403);
    }

    #[Test]
    public function client_requires_name(): void
    {
        $response = $this->actingAs($this->user)->post(route('clients.store'), [
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
