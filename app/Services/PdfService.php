<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    public function generate(Invoice $invoice, ?string $template = null): string
    {
        $invoice->loadMissing(['items', 'client', 'workspace', 'layout']);

        $workspace = $invoice->workspace;
        $template = $template ?: ($invoice->template ?: $workspace->default_template ?? 'classic');

        $primaryColor = $invoice->primary_color ?: $workspace->primary_color ?? '#2563EB';
        $accentColor  = $invoice->accent_color ?: $workspace->accent_color ?? '#1E40AF';

        $logoBase64 = null;
        if ($workspace->logo_path) {
            $logoPath = Storage::disk('public')->path($workspace->logo_path);
            if (file_exists($logoPath)) {
                $mime = mime_content_type($logoPath);
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }

        $data = [
            'invoice'      => $invoice,
            'workspace'    => $workspace,
            'client'       => $invoice->client,
            'items'        => $invoice->items,
            'primaryColor' => $primaryColor,
            'accentColor'  => $accentColor,
            'logoBase64'   => $logoBase64,
            'colors'       => [
                'primary' => $primaryColor,
                'accent'  => $accentColor,
            ],
        ];

        // If invoice has a custom drag-drop layout, render via the block renderer.
        if ($invoice->layout) {
            $html = view('invoices.pdf-custom', [
                'invoice'   => $invoice,
                'layout'    => $invoice->layout,
                'workspace' => $workspace,
            ])->render();
        } else {
            $html = view('invoices.templates.' . $template, $data)->render();
        }

        $paper = $invoice->layout?->settings['paper'] ?? 'a4';
        $pdf = Pdf::loadHTML($html)->setPaper(strtolower($paper));

        $directory = 'invoices/' . $invoice->workspace_id;

        $filename = str_replace(['/', '\\', ' '], '-', $invoice->invoice_no) . '.pdf';

        $content = $pdf->output();

        Storage::disk('public')->put($directory . '/' . $filename, $content);

        return $directory . '/' . $filename;
    }
}
