<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Equipement;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with(['client', 'equipement'])->latest()->paginate(10);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        $clients = Client::all();
        $equipements = Equipement::where('stock', '>', 0)->get();
        return view('locations.create', compact('clients', 'equipements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'equipement_id' => 'required|exists:equipements,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut'
        ]);

        $equipement = Equipement::findOrFail($request->equipement_id);
        $nbJours = Carbon::parse($request->date_debut)->diffInDays(Carbon::parse($request->date_fin)) + 1;
        $total = $equipement->tarif_journalier * $nbJours;

        Location::create([
            'client_id' => $request->client_id,
            'equipement_id' => $equipement->id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'montant_total' => $total,
            'statut' => 'en_attente'
        ]);

        return redirect()->route('locations.index')->with('success', 'Location enregistrée avec succès.');
    }

    public function edit(Location $location)
    {
        $clients = Client::all();
        $equipements = Equipement::all();
        return view('locations.edit', compact('location', 'clients', 'equipements'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'equipement_id' => 'required|exists:equipements,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut'
        ]);

        $equipement = Equipement::findOrFail($request->equipement_id);
        $nbJours = Carbon::parse($request->date_debut)->diffInDays(Carbon::parse($request->date_fin)) + 1;
        $total = $equipement->tarif_journalier * $nbJours;

        $location->update([
            'client_id' => $request->client_id,
            'equipement_id' => $request->equipement_id,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'montant_total' => $total,
        ]);

        return redirect()->route('locations.index')->with('success', 'Location mise à jour avec succès.');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Location supprimée.');
    }

    public function terminer(Location $location)
    {
        $location->update(['statut' => 'terminee']);
        return redirect()->route('locations.index')->with('success', 'Location terminée.');
    }

    public function facture(Location $location)
    {
        $pdf = Pdf::loadView('locations.facture', compact('location'));
        return $pdf->download('facture_location_' . $location->id . '.pdf');
    }
}
