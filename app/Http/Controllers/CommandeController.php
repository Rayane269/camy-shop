<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Client;
use App\Models\Produit;
use App\Models\CommandeItem;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\ProductScanned;

// Importations pour l'impression thermique ESC/POS sous Linux Debian
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class CommandeController extends Controller
{
    public function index()
    {
        $commandes = Commande::with(['client', 'paiement'])
                    ->orderBy('date_commande', 'desc')
                    ->paginate(5);

        return view('commandes.index', compact('commandes'));
    }

    /**
     * ACCÈS AU RETOUR AVEC VÉRIFICATION DES 48 HEURES
     */
    public function retourScan($numero)
    {
        $commande = Commande::with(['items.produit', 'client'])
            ->where('numero_commande', $numero)
            ->first();

        if (!$commande) {
            return redirect()->back()->with('error', 'Ticket introuvable ou invalide.');
        }

        // VÉRIFICATION COMPLÉMENTAIRE : Si déjà clôturée
        if ($commande->statut === 'cloture') {
            return redirect()->route('commandes.show', $commande)
                ->with('error', 'Action interdite : La caisse de cette journée a déjà été clôturée.');
        }

        // VERIFICATION AVANT D'AFFICHER LA VUE RETOUR
        if ($commande->date_commande->isBefore(now()->subHours(48))) {
            return redirect()->route('commandes.show', $commande)
                ->with('error', 'Action interdite : Le délai de retour de 48 heures est dépassé pour ce ticket.');
        }

        return view('commandes.retour', compact('commande'));
    }

    /**
     * OPÉRATION DE RETOUR SÉCURISÉE (48H + VERROU CLÔTURE + TRANSACTION SQL)
     */
    public function effectuerRetour(Request $request, $id)
    {
        // Récupération de la commande avec ses relations logistiques
        $commande = Commande::with(['items.produit', 'paiement'])->findOrFail($id);

        // SÉCURITÉ DE CLÔTURE : Interdiction de modifier une commande clôturée
        if ($commande->statut === 'cloture') {
            return redirect()->route('commandes.show', $commande)
                ->with('error', 'Action interdite : Impossible de modifier cette vente, la caisse journalière a été clôturée.');
        }

        // DOUBLE SÉCURITÉ CÔTÉ SERVEUR POUR LES 48 HEURES
        if ($commande->date_commande->isBefore(now()->subHours(48))) {
            return redirect()->route('commandes.show', $commande)
                ->with('error', 'Action interdite : Le délai de retour de 48 heures est dépassé pour ce ticket.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:commande_items,id',
            'items.*.quantite_retour' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            $totalRemboursement = 0;

            foreach ($request->items as $itemData) {
                $qty = (int)$itemData['quantite_retour'];
                if ($qty > 0) {
                    $item = $commande->items()->find($itemData['id']);
                    if ($qty <= $item->quantite) {
                        $item->produit->increment('stock', $qty);
                        
                        // Calcul de la valeur financière retournée
                        $totalRemboursement += ($qty * $item->prix_unitaire);

                        // Ajustement de la ligne de commande d'origine
                        $item->decrement('quantite', $qty);
                        $item->update(['total' => $item->quantite * $item->prix_unitaire]);
                    } else {
                        throw new \Exception("La quantité retournée dépasse la quantité achetée.");
                    }
                }
            }

            // Recalcul du montant total de la commande après les retours
            $nouveauTotal = $commande->items->sum('total');
            $commande->update(['total' => $nouveauTotal]);

            // Si la commande ne contient plus rien, on l'annule logistiquement et on met à jour le paiement
            if ($nouveauTotal == 0) {
                $commande->update(['statut' => 'annulee']);
                if ($commande->paiement) {
                    $commande->paiement->update(['statut_paiement' => 'rembourse']);
                }
            }

            DB::commit();
            return redirect()->route('commandes.show', $commande)->with('success', 'Retour validé, stock réintégré et tiroir-caisse mis à jour !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors du retour : ' . $e->getMessage());
        }
    }

    public function show(Commande $commande)
    {
        $commande->load(['client', 'items.produit', 'paiement']);
        return view('commandes.show', compact('commande'));
    }

    public function ajouterArticleLive(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'total_actuel' => 'nullable|numeric'
        ]);

        $produit = Produit::findOrFail($validated['produit_id']);
        $totalCommande = $validated['total_actuel'] ?? ($produit->prix * $validated['quantite']);

        broadcast(new ProductScanned($produit, $totalCommande));

        return response()->json([
            'success' => true,
            'nom' => $produit->nom,
            'prix' => $produit->prix
        ]);
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $produits = Produit::where('stock', '>', 0)->get();

        $produitsFormates = $produits->map(function ($p) {
            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'prix' => $p->prix,
                'stock' => $p->stock,
                'code_barre' => $p->code_barre,
                'image' => $p->image ? asset('storage/' . $p->image) : asset('images/logo-fac.png'),
            ];
        })->toArray();

        return view('commandes.create', compact('clients', 'produits', 'produitsFormates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'client_phone' => ['nullable', 'regex:/^[34][0-9]{6}$/'],
            'items' => 'required|array|min:1',
            'items.*.produit_id' => 'required|exists:produits,id',
            'items.*.quantite' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        try {
            $commande = DB::transaction(function () use ($validated) {
                $clientId = $validated['client_id'] ?? null;

                if (!$clientId) {
                    if (!empty($validated['client_phone'])) {
                        $client = Client::firstWhere('telephone', $validated['client_phone']);
                        if (!$client) {
                            $client = Client::create([
                                'nom' => 'Client',
                                'prenom' => 'Fidèle',
                                'telephone' => $validated['client_phone'],
                                'adresse' => 'Adresse à compléter'
                            ]);
                        }
                        $clientId = $client->id;
                    } else {
                        $client = Client::firstOrCreate(
                            ['nom' => 'Client standard', 'prenom' => 'Invité', 'telephone' => null],
                            ['adresse' => 'Commande standard']
                        );
                        $clientId = $client->id;
                    }
                }

                $count = Commande::whereYear('created_at', date('Y'))->count() + 1;
                do {
                    $numero = 'CMD-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
                    $count++;
                } while (Commande::where('numero_commande', $numero)->exists());

                $commande = Commande::create([
                    'client_id' => $clientId,
                    'numero_commande' => $numero,
                    'date_commande' => now(),
                    'statut' => 'en_attente',
                    'notes' => $validated['notes'] ?? null,
                    'total' => 0
                ]);

                $total = 0;
                foreach ($validated['items'] as $item) {
                    $produit = Produit::find($item['produit_id']);
                    $sousTotal = $produit->prix * $item['quantite'];

                    CommandeItem::create([
                        'commande_id' => $commande->id,
                        'produit_id' => $item['produit_id'],
                        'quantite' => $item['quantite'],
                        'prix_unitaire' => $produit->prix,
                        'total' => $sousTotal
                    ]);

                    $produit->decrement('stock', $item['quantite']);
                    $total += $sousTotal;
                }

                $commande->update(['total' => $total]);
                return $commande;
            });

            return redirect()->route('paiements.create', $commande)
                             ->with('success', 'Commande #' . $commande->numero_commande . ' créée. Veuillez procéder au règlement.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la création de la commande : ' . $e->getMessage());
        }
    }

    public function edit(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return redirect()->route('commandes.show', $commande)
                             ->with('error', 'Seules les commandes en attente peuvent être modifiées');
        }

        $commande->load(['items.produit', 'client']);
        $clients = Client::orderBy('nom')->get();
        $produits = Produit::all();

        $produitsFormates = $produits->map(function ($p) {
            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'prix' => $p->prix,
                'stock' => $p->stock,
                'code_barre' => $p->code_barre,
                'image' => $p->image ? asset('storage/' . $p->image) : asset('images/logo-fac.png'),
            ];
        })->toArray();

        return view('commandes.edit', compact('commande', 'clients', 'produitsFormates'));
    }

    public function update(Request $request, Commande $commande)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.produit_id' => 'required|exists:produits,id',
            'items.*.quantite' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $commande) {
            foreach ($commande->items as $item) {
                $item->produit->increment('stock', $item->quantite);
            }

            $commande->items()->delete();

            $total = 0;
            foreach ($validated['items'] as $itemData) {
                $produit = Produit::find($itemData['produit_id']);
                $sousTotal = $produit->prix * $itemData['quantite'];

                $commande->items()->create([
                    'produit_id' => $produit->id,
                    'quantite' => $itemData['quantite'],
                    'prix_unitaire' => $produit->prix,
                    'total' => $sousTotal
                ]);

                $produit->decrement('stock', $itemData['quantite']);
                $total += $sousTotal;
            }

            $commande->update([
                'client_id' => $validated['client_id'],
                'total' => $total,
                'notes' => $validated['notes'] ?? null
            ]);
        });

        return redirect()->route('commandes.show', $commande)
                         ->with('success', 'Commande mise à jour');
    }

    public function ticket(Commande $commande)
    {
        $commande->load(['items.produit', 'paiement', 'client']);
        return view('factures.ticket', compact('commande'));
    }

    /**
     * IMPRESSION PHYSIQUE DIRECTE SUR LA MINI-IMPRIMANTE THERMIQUE DEBIAN POSIKEX (/dev/lp0)
     */
    public function imprimerTicketPhysique(Commande $commande)
    {
        $commande->load(['items.produit', 'client', 'paiement']);

        try {
            // Utilisation du port d'impression parallèle/USB direct sous Debian (/dev/lp0)
            $connector = new FilePrintConnector("/dev/lp0");
            $printer = new Printer($connector);

            // --- EN-TÊTE ---
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_DOUBLE_HEIGHT);
            $printer->text("RAY-MULTITECH\n");
            $printer->selectPrintMode(); 
            
            $printer->text("M'DE BAMABAO\n");
            $printer->text("Tel : +269 448 04 33\n");
            
            $nomCaissier = auth()->user() ? auth()->user()->name : 'Caissier';
            $dateFormatee = $commande->date_commande ? $commande->date_commande->format('d/m/y H:i') : date('d/m/y H:i');
            $printer->text("Caisse : " . $nomCaissier . " | " . $dateFormatee . "\n");

            // --- CODE-BARRES TICKET ---
            $printer->feed(1);
            $printer->setBarcodeHeight(45);
            $printer->setBarcodeWidth(2);
            $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
            
            $contenuBarcode = "{B" . $commande->numero_commande;
            $printer->barcode($contenuBarcode, Printer::BARCODE_CODE128);
            $printer->feed(1);

            $printer->text("--------------------------------\n");

            // --- LISTE DES ARTICLES ---
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            
            foreach ($commande->items as $index => $item) {
                $numero = $index + 1;
                $nomArticle = mb_strtoupper($item->produit->nom ?? 'Article');
                $totalLigne = number_format($item->total, 0, '', ' ');

                if (mb_strlen($nomArticle) > 18) {
                    $nomArticle = mb_substr($nomArticle, 0, 15) . "...";
                }

                $printer->text(sprintf("%-18s %9s %3s\n", $nomArticle, $totalLigne, $numero));

                if ($item->quantite > 1) {
                    $qty = $item->quantite;
                    $prixUni = number_format($item->prix_unitaire, 0, '', ' ');
                    $printer->text(sprintf("   %dx %s KMF\n", $qty, $prixUni));
                }
            }

            $printer->text("--------------------------------\n");

            // --- TOTAUX ET PAIEMENT ---
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $totalArticles = $commande->items->count();
            $totalMontant = number_format($commande->total, 0, '', ' ') . " KMF";
            
            $labelTotal = "TOTAL " . $totalArticles . " ARTICLE(S)";
            $printer->text(sprintf("%-20s %12s\n", $labelTotal, $totalMontant));
            
            $modePaiement = $commande->paiement->mode_paiement_label ?? 'ESPECES';
            $printer->text(sprintf("%-20s %12s\n", strtoupper($modePaiement), $totalMontant));

            // --- PIED DE PAGE ---
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("********************************\n");
            $printer->selectPrintMode(Printer::MODE_EMPHASIZED);
            $printer->text("Ce ticket fait office de garantie\n");
            $printer->selectPrintMode();
            
            $printer->text("\nGARANTIE 1 MOIS SUR L'ELECTRONIQUE\n");
            $printer->text("RETOUR POSSIBLE SOUS 48H\n");
            $printer->text("DANS L'EMBALLAGE D'ORIGINE\n");
            $printer->text("TICKET DE CAISSE OBLIGATOIRE.\n");
            $printer->text("MERCI DE VOTRE VISITE !\n");
            
            $printer->text("********************************\n");
            $printer->text("www.librairie-camy.com\n");

            // --- IMPULSION TIROIR-CAISSE & DÉCOUPE ---
            $printer->pulse();
            $printer->feed(4);
            $printer->cut();
            $printer->close();

            return redirect()->back()->with('success', 'Ticket imprimé avec succès sur la mini-imprimante !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Erreur d'impression : " . $e->getMessage());
        }
    }

    public function facture(Commande $commande)
    {
        if (!$commande->paiement || $commande->paiement->statut_paiement !== 'paye') {
            return redirect()->route('commandes.show', $commande)
                            ->with('error', 'La facture nécessite un paiement validé.');
        }

        $commande->load(['items.produit', 'client', 'paiement']);
        return view('factures.commande', compact('commande'));
    }

    public function destroy(Commande $commande)
    {
        if ($commande->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Suppression impossible pour cette commande.');
        }

        foreach ($commande->items as $item) {
            $item->produit->increment('stock', $item->quantite);
        }

        $commande->delete();
        return redirect()->route('commandes.index')->with('success', 'Commande supprimée.');
    }

    public function updateStatut(Request $request, Commande $commande)
    {
        $validated = $request->validate([
            'statut' => 'required|string|in:en_attente,completed,cloture,annulee'
        ]);

        $commande->update(['statut' => $validated['statut']]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès.');
    }
}