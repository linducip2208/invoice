<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::where(function ($q) {
                $q->where('workspace_id', workspace()?->id)
                  ->orWhereNull('workspace_id'); // global admin coupons visible
            })
            ->latest()
            ->get();

        return view('coupons.index', compact('coupons'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'           => 'nullable|string|max:50|unique:coupons,code',
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'currency'       => 'nullable|string|size:3',
            'applies_to'     => 'required|in:invoice,subscription,both',
            'min_amount'     => 'nullable|numeric|min:0',
            'max_uses'       => 'nullable|integer|min:1',
            'expires_at'     => 'nullable|date|after:now',
            'allow_stack'    => 'boolean',
        ]);

        $validated['workspace_id'] = workspace()?->id;
        $validated['code'] = strtoupper($validated['code'] ?? Str::random(8));
        $validated['allow_stack'] = $request->boolean('allow_stack');
        $validated['is_active'] = true;

        Coupon::create($validated);
        return back()->with('success', 'Coupon dibuat.');
    }

    public function destroy(Coupon $coupon)
    {
        if ($coupon->workspace_id !== workspace()?->id) abort(403);
        $coupon->delete();
        return back()->with('success', 'Coupon dihapus.');
    }

    public function toggle(Coupon $coupon)
    {
        if ($coupon->workspace_id !== workspace()?->id) abort(403);
        $coupon->update(['is_active' => ! $coupon->is_active]);
        return back()->with('success', 'Status diubah.');
    }
}
