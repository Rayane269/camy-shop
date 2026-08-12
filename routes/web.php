<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    CommandeController,
    ClientController,
    PaiementController,
    AffichageClientController,
    DashboardController,
    ProduitController,
    ClotureController
};

// 1. ACCUEIL & REDIRECTION
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin' ? redirect('/admin') : redirect('/dashboard');
    }
    return view('welcome');
});

// 2. ZONE AUTHENTIFIÉE (Admin & Caissier)
Route::middleware('auth')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROUTES D'IMPRESSION (Accessibles partout)
    Route::get('/commandes/{commande}/facture', [CommandeController::class, 'facture'])->name('commandes.facture');
    Route::get('/commandes/{commande}/ticket', [CommandeController::class, 'ticket'])->name('commandes.ticket');
    
    // Nouvelle route : Impression physique directe sur l'Epson TM-T88III
    Route::get('/commandes/{commande}/imprimer-physique', [CommandeController::class, 'imprimerTicketPhysique'])->name('commandes.imprimerPhysique');

    Route::get('/produit/{produit}/etiquette', function (\App\Models\Produit $produit) {
        return view('produits.etiquette', compact('produit'));
    })->name('produit.etiquette');

    // Route spécifique pour Filament (Impression en masse)
    Route::get('/produits/impression-masse', function () {
        $ids = session('print_ids', []);
        $produits = \App\Models\Produit::whereIn('id', $ids)->get();
        if ($produits->isEmpty()) return "Aucun produit sélectionné.";
        return view('produits.etiquettes-masse', compact('produits'));
    })->name('produit.etiquette.masse');
});

// 3. ZONE CAISSIER (Spécifique)
Route::middleware(['auth', 'verified', 'caissier'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('commandes', CommandeController::class);
    Route::patch('commandes/{commande}/statut', [CommandeController::class, 'updateStatut'])->name('commandes.update-statut');
    
    // Paiements
    Route::get('commandes/{commande}/paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('commandes/{commande}/paiements', [PaiementController::class, 'store'])->name('paiements.store');
    
    // Retours (CORRIGÉ : {id} au lieu de {commande} pour éviter les conflits logistiques de conversion)
    Route::get('/commandes/retour/{numero}', [CommandeController::class, 'retourScan'])->name('commandes.retour');
    Route::post('/commandes/{id}/effectuer-retour', [CommandeController::class, 'effectuerRetour'])->name('commandes.effectuer-retour');

    // Clients & Produits
    Route::resource('clients', ClientController::class);
    Route::resource('produits', ProduitController::class)->only(['index', 'create', 'store']);

    // API & Affichage
    Route::get('/api/produits/{produit}', function (\App\Models\Produit $produit) {
        return response()->json(['id' => $produit->id, 'nom' => $produit->nom, 'prix' => $produit->prix]);
    })->name('api.produits.show');
    
    Route::get('/affichage-client/{commande}', [AffichageClientController::class, 'show'])->name('affichage.client');
    
    // Clôture Journalière de Caisse
    Route::post('/cloture-journaliere', [ClotureController::class, 'cloturerJournee'])->name('cloture.journee');
});

// 4. AFFICHAGE PUBLIC (Temp)
Route::get('/affichage-client-temp/{tempId}', function ($tempId) { return view('affichage-client-temp', compact('tempId')); });
Route::get('/affichage-client-temp/{tempId}/article', [CommandeController::class, 'ajouterArticleTemp']);

require __DIR__.'/auth.php';