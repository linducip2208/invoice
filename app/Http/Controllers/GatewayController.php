<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class GatewayController extends Controller
{
    public function index(): View
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        $presets = $this->loadPresets();

        return view('gateways.index', compact('gateways', 'presets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'gateway'           => 'required|string|max:50',
            'base_url'          => 'nullable|url|max:500',
            'api_format'        => 'required|in:rest,redirect,qris,ewallets',
            'api_key'           => 'nullable|string|max:500',
            'api_secret'        => 'nullable|string|max:500',
            'merchant_id'       => 'nullable|string|max:500',
            'client_key'        => 'nullable|string|max:500',
            'server_key'        => 'nullable|string|max:500',
            'is_active'         => 'boolean',
            'is_sandbox'        => 'boolean',
            'supported_channels'=> 'nullable|array',
            'supported_channels.*' => 'string',
        ]);

        $gateway = new PaymentGateway();
        $gateway->workspace_id = workspace()->id;
        $gateway->name = $validated['name'];
        $gateway->gateway = $validated['gateway'];
        $gateway->base_url = $validated['base_url'] ?? '';
        $gateway->api_format = $validated['api_format'];
        $gateway->is_active = $request->boolean('is_active', true);
        $gateway->is_sandbox = $request->boolean('is_sandbox', true);
        $gateway->supported_channels = $validated['supported_channels'] ?? [];
        $gateway->sort_order = PaymentGateway::max('sort_order') + 1;

        if (!empty($validated['api_key'])) {
            $gateway->setNonEncryptedKey('api_key', $validated['api_key']);
        }
        if (!empty($validated['api_secret'])) {
            $gateway->setNonEncryptedKey('api_secret', $validated['api_secret']);
        }
        if (!empty($validated['merchant_id'])) {
            $gateway->setNonEncryptedKey('merchant_id', $validated['merchant_id']);
        }
        if (!empty($validated['client_key'])) {
            $gateway->setNonEncryptedKey('client_key', $validated['client_key']);
        }
        if (!empty($validated['server_key'])) {
            $gateway->setNonEncryptedKey('server_key', $validated['server_key']);
        }

        $gateway->save();

        return Redirect::route('gateways.index')->with('success', 'Gateway berhasil ditambahkan.');
    }

    public function update(Request $request, PaymentGateway $gateway): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'base_url'          => 'nullable|url|max:500',
            'api_format'        => 'required|in:rest,redirect,qris,ewallets',
            'api_key'           => 'nullable|string|max:500',
            'api_secret'        => 'nullable|string|max:500',
            'merchant_id'       => 'nullable|string|max:500',
            'client_key'        => 'nullable|string|max:500',
            'server_key'        => 'nullable|string|max:500',
            'is_active'         => 'boolean',
            'is_sandbox'        => 'boolean',
            'supported_channels'=> 'nullable|array',
            'supported_channels.*' => 'string',
        ]);

        $gateway->name = $validated['name'];
        $gateway->base_url = $validated['base_url'] ?? '';
        $gateway->api_format = $validated['api_format'];
        $gateway->is_active = $request->boolean('is_active', true);
        $gateway->is_sandbox = $request->boolean('is_sandbox', true);
        $gateway->supported_channels = $validated['supported_channels'] ?? [];

        // Only update keys if new values are provided (empty = keep existing)
        if (!empty($validated['api_key'])) {
            $gateway->setNonEncryptedKey('api_key', $validated['api_key']);
        }
        if (!empty($validated['api_secret'])) {
            $gateway->setNonEncryptedKey('api_secret', $validated['api_secret']);
        }
        if (!empty($validated['merchant_id'])) {
            $gateway->setNonEncryptedKey('merchant_id', $validated['merchant_id']);
        }
        if (!empty($validated['client_key'])) {
            $gateway->setNonEncryptedKey('client_key', $validated['client_key']);
        }
        if (!empty($validated['server_key'])) {
            $gateway->setNonEncryptedKey('server_key', $validated['server_key']);
        }

        $gateway->save();

        return Redirect::route('gateways.index')->with('success', 'Gateway berhasil diperbarui.');
    }

    public function destroy(PaymentGateway $gateway): RedirectResponse
    {
        $gateway->delete();
        return Redirect::route('gateways.index')->with('success', 'Gateway dihapus.');
    }

    public function toggle(PaymentGateway $gateway): RedirectResponse
    {
        $gateway->update(['is_active' => !$gateway->is_active]);
        return Redirect::route('gateways.index')->with('success', $gateway->is_active ? 'Gateway diaktifkan.' : 'Gateway dinonaktifkan.');
    }

    /**
     * Load preset templates from JSON
     */
    private function loadPresets(): array
    {
        $path = storage_path('app/payment-gateway-presets/indonesia.json');
        if (!file_exists($path)) return [];

        $data = json_decode(file_get_contents($path), true);
        return $data['presets'] ?? [];
    }
}
