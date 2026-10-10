<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\AiProvider;
use App\Models\AiUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiProviderController extends Controller
{
    public function index()
    {
        $providers = AiProvider::latest()->get();
        $usageThisMonth = AiUsageLog::where('created_at', '>=', now()->startOfMonth())
            ->selectRaw('feature, COUNT(*) as calls, SUM(cost_estimate) as cost, SUM(input_tokens + output_tokens) as tokens')
            ->groupBy('feature')
            ->get();
        $totalCost = AiUsageLog::where('created_at', '>=', now()->startOfMonth())->sum('cost_estimate');

        return view('ai-providers.index', compact('providers', 'usageThisMonth', 'totalCost'));
    }

    public function create()
    {
        return view('ai-providers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:120',
            'api_format'             => 'required|in:openai_compatible,anthropic,gemini,image_generic,ocr_generic',
            'base_url'               => 'required|url|max:500',
            'api_key'                => 'required|string|max:500',
            'default_model'          => 'nullable|string|max:100',
            'default_pricing_input'  => 'nullable|numeric|min:0',
            'default_pricing_output' => 'nullable|numeric|min:0',
            'extra_headers'          => 'nullable|json',
            'is_active'              => 'boolean',
        ]);

        $extraHeaders = ! empty($validated['extra_headers']) ? json_decode($validated['extra_headers'], true) : null;

        $provider = new AiProvider([
            'name'                   => $validated['name'],
            'api_format'             => $validated['api_format'],
            'base_url'               => $validated['base_url'],
            'default_model'          => $validated['default_model'] ?? null,
            'default_pricing_input'  => $validated['default_pricing_input'] ?? null,
            'default_pricing_output' => $validated['default_pricing_output'] ?? null,
            'extra_headers'          => $extraHeaders,
            'is_active'              => $request->boolean('is_active', true),
        ]);
        $provider->api_key = $validated['api_key'];
        $provider->save();

        return redirect()->route('ai-providers.index')->with('success', 'AI provider ditambahkan.');
    }

    public function edit(AiProvider $provider)
    {
        return view('ai-providers.edit', compact('provider'));
    }

    public function update(Request $request, AiProvider $provider)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:120',
            'api_format'             => 'required|in:openai_compatible,anthropic,gemini,image_generic,ocr_generic',
            'base_url'               => 'required|url|max:500',
            'api_key'                => 'nullable|string|max:500',
            'default_model'          => 'nullable|string|max:100',
            'default_pricing_input'  => 'nullable|numeric|min:0',
            'default_pricing_output' => 'nullable|numeric|min:0',
            'extra_headers'          => 'nullable|json',
            'is_active'              => 'boolean',
        ]);

        $update = [
            'name' => $validated['name'],
            'api_format' => $validated['api_format'],
            'base_url' => $validated['base_url'],
            'default_model' => $validated['default_model'],
            'default_pricing_input' => $validated['default_pricing_input'],
            'default_pricing_output' => $validated['default_pricing_output'],
            'extra_headers' => $validated['extra_headers'] ? json_decode($validated['extra_headers'], true) : null,
            'is_active' => $request->boolean('is_active'),
        ];

        // Only update API key if provided
        if (! empty($validated['api_key'])) {
            $update['api_key'] = $validated['api_key'];
        }

        $provider->update($update);

        return redirect()->route('ai-providers.index')->with('success', 'Provider diupdate.');
    }

    public function destroy(AiProvider $provider)
    {
        $provider->delete();
        return back()->with('success', 'Provider dihapus.');
    }

    public function test(AiProvider $provider)
    {
        try {
            $endpoint = match ($provider->api_format) {
                'openai_compatible' => rtrim($provider->base_url, '/') . '/models',
                'anthropic'         => rtrim($provider->base_url, '/') . '/v1/models',
                'gemini'            => rtrim($provider->base_url, '/') . '/v1beta/models',
                default             => $provider->base_url,
            };

            $response = Http::timeout(10)
                ->withHeaders(array_merge(
                    ['Authorization' => 'Bearer ' . $provider->api_key],
                    $provider->extra_headers ?? []
                ))
                ->get($endpoint);

            if ($response->successful()) {
                return back()->with('success', "✓ Koneksi sukses ke {$provider->name}. HTTP {$response->status()}");
            }
            return back()->with('error', "Gagal: HTTP {$response->status()} — " . substr($response->body(), 0, 200));
        } catch (\Throwable $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
