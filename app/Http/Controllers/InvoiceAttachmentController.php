<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceAttachmentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $request->validate(['file' => 'required|file|max:10240']); // 10MB

        $file = $request->file('file');
        $path = $file->store('invoice-attachments/' . $invoice->id, 'public');

        InvoiceAttachment::create([
            'invoice_id' => $invoice->id,
            'file_name'  => $file->getClientOriginalName(),
            'file_path'  => $path,
            'file_size'  => $file->getSize(),
            'mime_type'  => $file->getMimeType(),
        ]);

        return back()->with('success', 'Attachment uploaded.');
    }

    public function destroy(InvoiceAttachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        return back()->with('success', 'Attachment removed.');
    }
}
