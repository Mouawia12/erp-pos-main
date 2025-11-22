<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceTermTemplate;
use Illuminate\Http\Request;

class InvoiceTermTemplateController extends Controller
{
    public function index()
    {
        $templates = InvoiceTermTemplate::latest()->get();
        return view('admin.invoice_terms.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        InvoiceTermTemplate::create($data);

        return back()->with('success', __('main.created'));
    }

    public function update(Request $request, InvoiceTermTemplate $invoiceTerm)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $invoiceTerm->update($data);

        return back()->with('success', __('main.updated'));
    }

    public function destroy(InvoiceTermTemplate $invoiceTerm)
    {
        $invoiceTerm->delete();
        return back()->with('success', __('main.deleted'));
    }
}
