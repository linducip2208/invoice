<?php

namespace App\Http\Controllers;

use App\Models\DashboardWidget;
use App\Services\Dashboard\WidgetRegistry;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    public function customize(Request $request)
    {
        $widgets = DashboardWidget::where('user_id', $request->user()->id)
            ->orderBy('position')
            ->get();

        if ($widgets->isEmpty()) {
            // Seed default
            foreach (WidgetRegistry::defaultLayout() as $w) {
                DashboardWidget::create([
                    'user_id'     => $request->user()->id,
                    'widget_type' => $w['widget_type'],
                    'size'        => $w['size'],
                    'position'    => $w['position'],
                ]);
            }
            $widgets = DashboardWidget::where('user_id', $request->user()->id)
                ->orderBy('position')->get();
        }

        $registry = WidgetRegistry::WIDGETS;

        return view('dashboard.customize', compact('widgets', 'registry'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'widget_type' => 'required|string|max:50',
            'size'        => 'required|in:sm,md,lg,xl',
        ]);

        $maxPos = (int) DashboardWidget::where('user_id', $request->user()->id)->max('position');

        DashboardWidget::create([
            'user_id'     => $request->user()->id,
            'widget_type' => $validated['widget_type'],
            'size'        => $validated['size'],
            'position'    => $maxPos + 1,
        ]);

        return back()->with('success', 'Widget ditambahkan.');
    }

    public function update(Request $request, DashboardWidget $widget)
    {
        if ($widget->user_id !== $request->user()->id) abort(403);
        $validated = $request->validate([
            'size' => 'nullable|in:sm,md,lg,xl',
            'is_visible' => 'boolean',
        ]);
        $widget->update($validated);
        return back()->with('success', 'Widget diupdate.');
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        foreach ($request->ids as $position => $id) {
            DashboardWidget::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->update(['position' => $position]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, DashboardWidget $widget)
    {
        if ($widget->user_id !== $request->user()->id) abort(403);
        $widget->delete();
        return back()->with('success', 'Widget dihapus.');
    }
}
