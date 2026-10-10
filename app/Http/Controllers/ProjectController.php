<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('client')->latest();
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('client_id')) $query->where('client_id', $request->client_id);

        $projects = $query->paginate(15)->withQueryString();
        $clients = Client::orderBy('name')->get();

        return view('projects.index', compact('projects', 'clients'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'name'          => 'required|string|max:200',
            'description'   => 'nullable|string|max:2000',
            'status'        => 'required|in:active,on_hold,completed,cancelled',
            'budget_amount' => 'nullable|numeric|min:0',
            'budget_hours'  => 'nullable|numeric|min:0',
            'hourly_rate'   => 'nullable|numeric|min:0',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'color'         => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $project = Project::create($validated);
        return redirect()->route('projects.show', $project)->with('success', 'Project dibuat.');
    }

    public function show(Project $project)
    {
        $project->load('client');

        $stats = [
            'total_invoiced' => (float) $project->invoices()->sum('total'),
            'total_paid'     => (float) $project->invoices()->sum('amount_paid'),
            'total_unpaid'   => (float) $project->invoices()->sum('balance_due'),
            'total_hours'    => (float) $project->timeEntries()->sum('hours'),
            'total_expense'  => (float) $project->expenses()->sum('amount'),
            'invoice_count'  => $project->invoices()->count(),
            'time_count'     => $project->timeEntries()->count(),
            'expense_count'  => $project->expenses()->count(),
        ];

        $stats['margin'] = $stats['total_invoiced'] - $stats['total_expense'];
        $stats['margin_pct'] = $stats['total_invoiced'] > 0
            ? round($stats['margin'] / $stats['total_invoiced'] * 100, 1)
            : null;

        $invoices = $project->invoices()->latest()->limit(10)->get();
        $timeEntries = $project->timeEntries()->latest('entry_date')->limit(10)->get();
        $expenses = $project->expenses()->latest('expense_date')->limit(10)->get();

        return view('projects.show', compact('project', 'stats', 'invoices', 'timeEntries', 'expenses'));
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'name'          => 'required|string|max:200',
            'description'   => 'nullable|string|max:2000',
            'status'        => 'required|in:active,on_hold,completed,cancelled',
            'budget_amount' => 'nullable|numeric|min:0',
            'budget_hours'  => 'nullable|numeric|min:0',
            'hourly_rate'   => 'nullable|numeric|min:0',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'color'         => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $project->update($validated);
        return redirect()->route('projects.show', $project)->with('success', 'Project diupdate.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project dihapus.');
    }
}
