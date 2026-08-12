<?php
// app/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    
    public function index(Request $request)
{
    $query = Client::query();

    if ($request->has('search')) {
        $search = $request->get('search');
        $query->where('nom', 'like', "%{$search}%")
              ->orWhere('prenom', 'like', "%{$search}%")
              ->orWhere('telephone', 'like', "%{$search}%");
    }

    $clients = $query->latest()->paginate(10);
    return view('clients.index', compact('clients'));
}

    public function show(Client $client)
    {
        $client->load(['commandes.paiement']);
        return view('clients.show', compact('client'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|min:3',
            'prenom' => 'required|string|max:255|min:3',
            'telephone' => ['required', 'regex:/^[3-4][0-9]{6}$/'],
            'adresse' => 'required|string|max:255|min:3',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
                        ->with('success', 'Client créé avec succès');
    }
    // Modifie ou ajoute ces méthodes dans ClientController
public function edit(Client $client)
{
    return view('clients.edit', compact('client'));
}

public function update(Request $request, Client $client)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'nullable|string|max:255',
        'telephone' => 'required|string|max:20',
        'adresse' => 'nullable|string',
    ]);

    $client->update($validated);

    return redirect()->route('clients.index')
                     ->with('success', 'Client mis à jour avec succès');
}

public function destroy(Client $client)
{
    // Optionnel : vérifier si le client a des commandes avant de supprimer
    if ($client->commandes()->count() > 0) {
        return redirect()->back()->with('error', 'Impossible de supprimer un client ayant des commandes.');
    }

    $client->delete();
    return redirect()->route('clients.index')
                     ->with('success', 'Client supprimé du répertoire');
}
}