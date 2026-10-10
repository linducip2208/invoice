<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Navigation integrity:
 * 1. Every route() referenced by any Blade view is registered.
 * 2. Guests are redirected away from private pages.
 * 3. Regular users get 403 on superadmin pages.
 */
class NavIntegrityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function every_blade_route_reference_is_registered(): void
    {
        $names = [];
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        foreach ($rii as $f) {
            if ($f->isDir() || ! str_ends_with($f->getFilename(), '.blade.php')) {
                continue;
            }
            $c = file_get_contents($f->getPathname());
            // route('name') helper only — exclude $request->route('...') method calls.
            preg_match_all("/(?<!->|::)route\(\s*'([^']+)'/", $c, $m);
            foreach ($m[1] as $name) {
                $names[$name] = $f->getPathname();
            }
        }

        $this->assertNotEmpty($names);
        $missing = [];
        foreach ($names as $name => $file) {
            if (! Route::has($name)) {
                $missing[] = "$name (in $file)";
            }
        }
        $this->assertEmpty($missing, 'Blade references to unregistered routes: ' . implode('; ', $missing));
    }

    #[Test]
    public function guest_cannot_see_private_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('invoices.index'))->assertRedirect(route('login'));
        $this->get(route('clients.index'))->assertRedirect(route('login'));
        $this->get(route('payments.index'))->assertRedirect(route('login'));
        $this->get(route('reports.index'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    #[Test]
    public function regular_user_cannot_reach_superadmin(): void
    {
        $user = User::factory()->create(['is_superadmin' => false]);
        $ws = \App\Models\Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);

        $a = $this->actingAs($user);
        $a->get(route('admin.dashboard'))->assertForbidden();
        $a->get(route('admin.users.index'))->assertForbidden();
        $a->get(route('admin.plans.index'))->assertForbidden();
        $a->get(route('admin.subscriptions.index'))->assertForbidden();
    }

    #[Test]
    public function no_dead_links_in_app_shell(): void
    {
        $shell = file_get_contents(resource_path('views/components/tabler/layout.blade.php'));
        // href="#" is legitimate ONLY on Bootstrap dropdown toggles
        // (data-bs-toggle="dropdown"); anywhere else it is a dead link.
        preg_match_all('/<a\b[^>]*>/', $shell, $tags);
        $dead = [];
        foreach ($tags[0] as $tag) {
            if (str_contains($tag, 'href="#"') && ! str_contains($tag, 'data-bs-toggle')) {
                $dead[] = $tag;
            }
        }
        $this->assertEmpty($dead, 'Dead links in app shell: ' . implode(',', $dead));
    }
}
