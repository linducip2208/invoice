<?php

namespace App\Http\Controllers;

use App\Models\ReportSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = ReportSubscription::where('user_id', $request->user()->id)
            ->latest()
            ->get();
        return view('report-subscriptions.index', compact('subscriptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type'   => 'required|in:weekly_summary,monthly_pl,quarterly_tax,aging_report',
            'frequency'     => 'required|in:weekly,monthly,quarterly',
            'day_of_week'   => 'nullable|integer|min:1|max:7',
            'day_of_month'  => 'nullable|integer|min:1|max:31',
            'hour'          => 'required|integer|min:0|max:23',
        ]);

        $sub = ReportSubscription::create([
            ...$validated,
            'user_id'      => $request->user()->id,
            'is_active'    => true,
            'next_send_at' => $this->computeFirstSend($validated),
        ]);

        return back()->with('success', 'Subscription dibuat. Email dikirim mulai ' . $sub->next_send_at->format('d M Y'));
    }

    private function computeFirstSend(array $data): Carbon
    {
        $now = now()->setTime($data['hour'], 0);
        return match ($data['frequency']) {
            'weekly'    => $now->copy()->addDays(((int) ($data['day_of_week'] ?? 1) - $now->dayOfWeekIso + 7) % 7 ?: 7),
            'monthly'   => $now->copy()->addMonth()->day(min($data['day_of_month'] ?? 1, 28)),
            'quarterly' => $now->copy()->addMonths(3),
            default     => $now->copy()->addWeek(),
        };
    }

    public function destroy(ReportSubscription $sub)
    {
        if ($sub->user_id !== auth()->id()) abort(403);
        $sub->delete();
        return back()->with('success', 'Subscription dihapus.');
    }

    public function toggle(ReportSubscription $sub)
    {
        if ($sub->user_id !== auth()->id()) abort(403);
        $sub->update(['is_active' => ! $sub->is_active]);
        return back()->with('success', 'Status diubah.');
    }
}
