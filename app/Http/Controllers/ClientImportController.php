<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientImportController extends Controller
{
    public function show()
    {
        return view('clients.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $headers = fgetcsv($handle, 0, ',', '"', '\\');

        $imported = 0;
        $skipped = 0;
        $errors = [];
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($row) < 1) { $skipped++; continue; }
            // Per-row isolation: one bad row must never abort the whole file
            // with a 500, nor leave a half-written record.
            try {
                $data = array_combine($headers, array_pad($row, count($headers), ''));

                $name = trim($data['name'] ?? '');
                if ($name === '') { $skipped++; continue; }

                $email = trim($data['email'] ?? '');
                if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    if (count($errors) < 5) $errors[] = __('Row skipped (bad email): :email', ['email' => $email]);
                    continue;
                }

                Client::create([
                    'name'         => $name,
                    'company_name' => $data['company_name'] ?? null,
                    'email'        => $email !== '' ? $email : null,
                    'phone'        => $data['phone'] ?? null,
                    'address'      => $data['address'] ?? null,
                    'city'         => $data['city'] ?? null,
                    'state'        => $data['state'] ?? null,
                    'country'      => $data['country'] ?? 'Indonesia',
                    'postal_code'  => $data['postal_code'] ?? null,
                    'tax_id'       => $data['tax_id'] ?? null,
                    'is_active'    => true,
                ]);
                $imported++;
            } catch (\Throwable $e) {
                $skipped++;
                if (count($errors) < 5) $errors[] = __('Row skipped (error): :msg', ['msg' => $e->getMessage()]);
                \Illuminate\Support\Facades\Log::warning('Client CSV row failed', ['error' => $e->getMessage()]);
            }
        }
        fclose($handle);

        $message = "Imported {$imported}, skipped {$skipped}.";
        if ($errors) $message .= ' ' . implode(' ', $errors);
        return redirect()->route('clients.index')->with('success', $message);
    }
}
