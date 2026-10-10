<?php

namespace Tests;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Workspace;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed plans (free/pro/business). Required because
     * Workspace::activeSubscription() aborts 500 when no free plan exists,
     * and plan-gated routes need a subscription to evaluate limits.
     */
    protected function seedPlans(): void
    {
        $this->seed(PlanSeeder::class);
    }

    /**
     * Attach an ACTIVE Pro subscription to the workspace so plan-gated
     * routes (invoice/client create, delete, ...) pass the
     * EnforcePlanLimits middleware during tests.
     */
    protected function giveProPlan(Workspace $workspace): Subscription
    {
        $pro = Plan::where('slug', 'pro')->firstOrFail();

        return Subscription::create([
            'workspace_id' => $workspace->id,
            'plan_id' => $pro->id,
            'status' => 'active',
            'billing_period' => 'monthly',
            'currency' => $workspace->currency ?? 'IDR',
        ]);
    }

    /**
     * Attach an ACTIVE Business subscription (all feature flags on).
     */
    protected function giveBusinessPlan(Workspace $workspace): Subscription
    {
        $business = Plan::where('slug', 'business')->firstOrFail();

        return Subscription::create([
            'workspace_id' => $workspace->id,
            'plan_id' => $business->id,
            'status' => 'active',
            'billing_period' => 'monthly',
            'currency' => $workspace->currency ?? 'IDR',
        ]);
    }
}
