<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Commande;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /**
     * Affiche le formulaire de paiement pour une commande spécifique.
     */
    public function create(Commande $commande)
    {
        if ($commande->paiement) {
            return redirect()->route('commandes.show', $commande)
                           ->with('error', 'Cette commande a déjà un paiement associé');
        }

        return view('paiements.create', compact('commande'));
    }

    /**
     * Enregistre le paiement et met à jour les statistiques globales.
     */
    public function store(Request $request, Commande $commande)
    {
        // 1. Validation flexible pour accepter tous les modes (Mvula, Exim, etc.)
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0',
            'mode_paiement' => 'required|string', 
            'reference_transaction' => 'nullable|string'
        ]);

        // 2. Création du paiement avec le statut 'paye' correspondant à l'énumération
        $paiement = Paiement::create([
            'commande_id' => $commande->id,
            'montant' => $validated['montant'],
            'mode_paiement' => $validated['mode_paiement'],
            'statut_paiement' => 'paye', 
            'date_paiement' => now(),
            'reference_transaction' => $validated['reference_transaction']
        ]);

        // 3. MISE À JOUR CRUCIALE POUR LE TABLEAU DE BORD ADMIN
        // On utilise 'completed' (avec un 'd') pour basculer du "Manque à gagner" au "Chiffre d'affaires"
        if ($paiement->montant >= $commande->total) {
            $commande->update(['statut' => 'completed']);
        }

        // 4. REDIRECTION VERS LE RÉCAPITULATIF DE LA COMMANDE
        // Au lieu de 'commandes.ticket', on redirige vers 'commandes.show'
        return redirect()->route('commandes.show', $commande)
            ->with('success', 'Règlement enregistré avec succès ! Vous pouvez maintenant imprimer le ticket ci-dessous.');
    }

    /**
     * Met à jour manuellement le statut d'un paiement.
     */
    public function updateStatut(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'statut_paiement' => 'required|string'
        ]);

        $paiement->update($validated);

        return redirect()->back()
                        ->with('success', 'Statut de paiement mis à jour');
    }
}