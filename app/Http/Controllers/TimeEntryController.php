<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = TimeEntry::with('client')->latest('entry_date');
        if ($request->filled('client_id')) $query->where('client_id', $request->client_id);
        if ($request->filled('billable'))  $query->where('billable', $request->billable === 'yes');

        $entries = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('name')->get();
        $totalHours = (clone $query)->sum('hours');
        $totalAmount = TimeEntry::query()->selectRaw('SUM(hours * hourly_rate) as t')->value('t') ?? 0;

        return view('time-entries.index', compact('entries', 'clients', 'totalHours', 'totalAmount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'   => 'nullable|exists:clients,id',
            'description' => 'required|string|max:500',
            'hours'       => 'required|numeric|min:0.01|max:24',
            'hourly_rate' => 'required|numeric|min:0',
            'entry_date'  => 'required|date',
            'billable'    => 'boolean',
        ]);
        $validated['user_id'] = auth()->id();
        $validated['billable'] = ! empty($validated['billable']);

        TimeEntry::create($validated);
        return back()->with('success', 'Time entry created.');
    }

    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();
        return back()->with('success', 'Entry deleted.');
    }
}
