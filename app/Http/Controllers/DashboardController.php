<?php

// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Paiement;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            // On ne compte que les commandes actives (total > 0) du mois en cours
            'commandes_total' => Commande::where('total', '>', 0)
                ->whereMonth('date_commande', now()->month)
                ->whereYear('date_commande', now()->year)
                ->count(),
            
            // On compte les commandes réellement en attente
            'commandes_en_attente' => Commande::where('statut', 'en_attente')->count(),
            
            // On compte les paiements qui ne sont pas encore complétés
            'paiements_en_attente' => Paiement::where('statut_paiement', 'en_attente')->count(),
            
            // On calcule les revenus réels du mois basés sur le total final des commandes payées
            // (ce qui déduit automatiquement les remboursements et retours !)
            'revenus_mois' => Commande::whereHas('paiement', function($query) {
                    $query->whereIn('statut_paiement', ['paye', 'complete']);
                })
                ->whereMonth('date_commande', now()->month)
                ->whereYear('date_commande', now()->year)
                ->sum('total'),
        ];

        // On charge les relations 'paiement' et 'items' pour éviter les requêtes N+1 avec l'attribut virtuel 'a_un_retour'
        $commandes_recentes = Commande::with(['paiement', 'items'])->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'commandes_recentes'));
    }
}