<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase5Test extends TestCase
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

    #[Test] public function coupon_manual_subscribe_and_approve_applies_discount(): void
    {
        $pro = Plan::where('slug', 'pro')->first();
        $coupon = Coupon::create([
            'code' => 'HEMAT20', 'discount_type' => 'percent', 'discount_value' => 20,
            'plan_id' => $pro->id, 'is_active' => true,
        ]);

        $r = $this->actingAs($this->user)->post(route('billing.subscribe', $pro), [
            'period' => 'monthly', 'currency' => 'IDR',
            'payment_method' => 'manual', 'coupon_code' => 'HEMAT20',
        ]);
        $r->assertRedirect(route('billing.manual', $this->ws->activeSubscription()->id));

        $sub = $this->ws->activeSubscription()->fresh();
        $this->assertEquals('pending', $sub->status);
        $this->assertEquals('HEMAT20', $sub->metadata['coupon']['coupon_code']);

        // Manual page shows discounted total.
        $page = $this->actingAs($this->user)->get(route('billing.manual', $sub));
        $page->assertOk();
        $page->assertSee('HEMAT20', false);

        // Admin approve → invoice diskon + redemption tercatat.
        $admin = User::factory()->create(['is_superadmin' => true]);
        $full = $pro->priceFor('IDR', 'monthly');
        $this->actingAs($admin)->post(route('admin.manual-payments.approve', $sub))->assertRedirect();

        $inv = \App\Models\SubscriptionInvoice::where('subscription_id', $sub->id)->first();
        $this->assertEquals(round($full * 0.8, 2), (float) $inv->amount);
        $this->assertEquals(1, $coupon->fresh()->uses_count);
        $this->assertEquals(1, \App\Models\CouponRedemption::where('coupon_id', $coupon->id)->count());
    }

    #[Test] public function coupon_rejected_for_gateway_and_bad_code(): void
    {
        $pro = Plan::where('slug', 'pro')->first();
        Coupon::create(['code' => 'HEMAT20', 'discount_type' => 'percent', 'discount_value' => 20, 'plan_id' => $pro->id, 'is_active' => true]);

        $this->actingAs($this->user)->post(route('billing.subscribe', $pro), [
            'period' => 'monthly', 'currency' => 'IDR',
            'payment_method' => 'gateway', 'coupon_code' => 'HEMAT20',
        ])->assertSessionHas('error');

        $this->actingAs($this->user)->post(route('billing.subscribe', $pro), [
            'period' => 'monthly', 'currency' => 'IDR',
            'payment_method' => 'manual', 'coupon_code' => 'SALAH',
        ])->assertSessionHas('error');
    }

    #[Test] public function past_due_banner_renders_with_pay_cta(): void
    {
        $pro = Plan::where('slug', 'pro')->first();
        $sub = $this->ws->activeSubscription();
        $sub->update(['plan_id' => $pro->id, 'status' => 'past_due', 'billing_period' => 'monthly', 'currency' => 'IDR']);

        $r = $this->actingAs($this->user)->get(route('billing.show'));
        $r->assertOk();
        $r->assertSee('Bayar Sekarang', false);
        $r->assertSee('menunggak', false);
    }
}
