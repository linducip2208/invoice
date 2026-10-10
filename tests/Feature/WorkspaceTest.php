<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_without_workspace_is_redirected_to_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('workspace.create'));
    }

    #[Test]
    public function user_can_create_first_workspace(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workspace.store'), [
            'name' => 'PT Maju Bersama',
            'currency' => 'IDR',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('workspaces', ['name' => 'PT Maju Bersama']);
        $workspace = Workspace::where('name', 'PT Maju Bersama')->firstOrFail();
        $this->assertDatabaseHas('workspace_user', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);
    }

    #[Test]
    public function workspace_creation_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('workspace.store'), [
            'currency' => 'IDR',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
