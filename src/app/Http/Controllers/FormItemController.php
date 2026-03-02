<?php

namespace App\Http\Controllers;

use App\Models\FormItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class FormItemController extends Controller
{
    /**
     * Store a newly created form item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'form_name' => ['required', 'string', 'max:255'],
        ]);

        FormItem::create($validated);

        return Redirect::route('settings.index', ['forms_page' => 1])->with('status', 'form-created');
    }

    /**
     * Show the form for editing the specified form item.
     */
    public function edit(FormItem $formItem): View
    {
        return view('forms.edit', [
            'formItem' => $formItem,
        ]);
    }

    /**
     * Update the specified form item.
     */
    public function update(Request $request, FormItem $formItem): RedirectResponse
    {
        $validated = $request->validate([
            'form_name' => ['required', 'string', 'max:255'],
        ]);

        $formItem->update($validated);

        return Redirect::route('settings.index')->with('status', 'form-updated');
    }
}
