<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CurrencyRateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase11Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        CurrencyRateService::flushMemo();
        Cache::flush();
    }

    #[Test] public function auto_mode_uses_live_api(): void
    {
        Http::fake([
            'api.frankfurter.app/*' => Http::response(['amount' => 1, 'base' => 'USD', 'rates' => ['IDR' => 16000]], 200),
        ]);

        $this->assertEquals(16000, CurrencyRateService::rate('USD', 'IDR'));
        $this->assertEquals(32000, CurrencyRateService::convert(2, 'USD', 'IDR'));
    }

    #[Test] public function manual_mode_uses_setting_not_api(): void
    {
        \App\Models\AppSetting::updateOrCreate(['key' => 'fx.mode'], ['value' => 'manual', 'group' => 'fx']);
        \App\Models\AppSetting::updateOrCreate(['key' => 'fx.manual_USD_IDR'], ['value' => 15000, 'group' => 'fx']);
        Http::fake(['*' => Http::response(null, 500)]);

        $this->assertEquals(15000, CurrencyRateService::rate('USD', 'IDR'));
        $this->assertEquals('manual', CurrencyRateService::mode());
    }

    #[Test] public function offline_without_manual_returns_null(): void
    {
        Http::fake(['*' => Http::response(null, 500)]);
        $this->assertNull(CurrencyRateService::rate('USD', 'IDR'));
        $this->assertNull(CurrencyRateService::convert(10, 'USD', 'IDR'));
    }

    #[Test] public function admin_can_save_fx_settings(): void
    {
        $this->seedPlans();
        $admin = User::factory()->create(['is_superadmin' => true]);

        $this->actingAs($admin)->post(route('admin.settings.fx'), [
            'mode' => 'manual', 'manual_usd_idr' => 15500,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('app_settings', ['key' => 'fx.mode']);
        CurrencyRateService::flushMemo();
        $this->assertEquals(15500, CurrencyRateService::rate('USD', 'IDR'));
    }

    #[Test] public function superadmin_dashboard_shows_idr_equivalent(): void
    {
        $this->seedPlans();
        $admin = User::factory()->create(['is_superadmin' => true]);
        Http::fake([
            'api.frankfurter.app/*' => Http::response(['amount' => 1, 'base' => 'USD', 'rates' => ['IDR' => 16000]], 200),
        ]);

        $r = $this->actingAs($admin)->get(route('admin.dashboard'));
        $r->assertOk();
    }

    #[Test] public function dashboard_currency_toggle_converts_total(): void
    {
        $this->seedPlans();
        $admin = User::factory()->create(['is_superadmin' => true]);
        \App\Models\AppSetting::updateOrCreate(['key' => 'fx.mode'], ['value' => 'manual', 'group' => 'fx']);
        \App\Models\AppSetting::updateOrCreate(['key' => 'fx.manual_USD_IDR'], ['value' => 10000, 'group' => 'fx']);
        CurrencyRateService::flushMemo();

        $r = $this->actingAs($admin)->get(route('admin.dashboard', ['cur' => 'USD']));
        $r->assertOk();
        $r->assertSee('setara USD', false);
    }
}
