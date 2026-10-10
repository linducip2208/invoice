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
        while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if (count($row) < 1) { $skipped++; continue; }
            $data = array_combine($headers, array_pad($row, count($headers), ''));

            if (empty($data['name'] ?? null)) { $skipped++; continue; }

            Client::create([
                'name'         => $data['name'],
                'company_name' => $data['company_name'] ?? null,
                'email'        => $data['email'] ?? null,
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
        }
        fclose($handle);

        return redirect()->route('clients.index')->with('success', "Imported {$imported}, skipped {$skipped}.");
    }
}
