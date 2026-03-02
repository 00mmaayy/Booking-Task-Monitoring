<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Store a newly created client.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'tin' => ['required', 'string', 'max:255'],
            'tel_phone_number' => ['required', 'string', 'max:255'],
        ]);

        Client::create($validated);

        return Redirect::route('settings.index', ['clients_page' => 1])->with('status', 'client-created');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): View
    {
        return view('clients.edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'tin' => ['required', 'string', 'max:255'],
            'tel_phone_number' => ['required', 'string', 'max:255'],
        ]);

        $client->update($validated);

        return Redirect::route('settings.index')->with('status', 'client-updated');
    }
}
