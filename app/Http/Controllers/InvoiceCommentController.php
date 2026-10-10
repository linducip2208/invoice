<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceComment;
use Illuminate\Http\Request;

class InvoiceCommentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'body'        => 'required|string|max:5000',
            'is_internal' => 'boolean',
            'parent_id'   => 'nullable|exists:invoice_comments,id',
        ]);

        $user = $request->user();

        InvoiceComment::create([
            'workspace_id'    => $invoice->workspace_id,
            'invoice_id'      => $invoice->id,
            'author_user_id'  => $user->id,
            'author_name'     => $user->name,
            'author_email'    => $user->email,
            'body'            => $validated['body'],
            'is_internal'     => $request->boolean('is_internal'),
            'parent_id'       => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Komentar ditambahkan.');
    }

    public function destroy(InvoiceComment $comment)
    {
        $user = auth()->user();
        if ($comment->author_user_id !== $user->id && ! $user->isSuperadmin()) {
            abort(403);
        }
        $comment->delete();
        return back()->with('success', 'Komentar dihapus.');
    }
}
