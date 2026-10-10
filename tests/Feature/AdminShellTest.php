<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminShellTest extends TestCase
{
    use RefreshDatabase;

    #[Test] public function superadmin_sees_platform_shell(): void
    {
        $admin = User::factory()->create(['is_superadmin' => true]);
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Platform', false);
    }

    #[Test] public function workspace_user_cannot_see_platform(): void
    {
        $user = User::factory()->create(['is_superadmin' => false]);
        $ws = \App\Models\Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }
}
