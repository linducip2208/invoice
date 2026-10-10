<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Plan;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(20);
        $plans = Plan::orderBy('name')->get();
        return view('admin.coupons.index', compact('coupons', 'plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'           => 'required|string|max:50|unique:coupons,code',
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'plan_id'        => 'nullable|exists:plans,id',
            'max_uses'       => 'nullable|integer|min:1',
            'expires_at'     => 'nullable|date',
            'is_active'      => 'boolean',
        ]);
        $validated['is_active'] = ! empty($validated['is_active']);
        Coupon::create($validated);
        return back()->with('success', 'Coupon created.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);
        return back();
    }
}
