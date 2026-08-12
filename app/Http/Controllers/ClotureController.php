<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeItem; 
use App\Mail\RapportClotureMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // Important pour gérer l'heure locale correctement
use PDF; 

class ClotureController extends Controller
{
    public function cloturerJournee()
    {
        // Fixation sécurisée du fuseau horaire des Comores pour ne pas dépendre de l'heure du serveur
        $aujourdhui = Carbon::now('Indian/Comoro')->startOfDay();
        $finAujourdhui = Carbon::now('Indian/Comoro')->endOfDay();

        // 1. Récupérer les commandes PAYÉES d'aujourd'hui
        $commandes = Commande::with(['client', 'paiement', 'items.produit'])
            ->whereBetween('date_commande', [$aujourdhui, $finAujourdhui])
            ->where('statut', 'completed')
            ->get();

        if ($commandes->isEmpty()) {
            return redirect()->back()->with('error', 'Aucune commande validée à clôturer pour aujourd\'hui.');
        }

        // 2. Calculs financiers
        $caBrut = $commandes->sum('total');
        
        // Calcul des modes de paiement
        $modesPaiement = [
            'ESPECES' => 0,
            'MVULA' => 0,
            'EXIM' => 0,
            'AUTRES' => 0
        ];

        foreach ($commandes as $cmd) {
            if ($cmd->paiement) {
                $mode = strtoupper($cmd->paiement->mode_paiement);
                if (array_key_exists($mode, $modesPaiement)) {
                    $modesPaiement[$mode] += $cmd->paiement->montant;
                } else {
                    $modesPaiement['AUTRES'] += $cmd->paiement->montant;
                }
            }
        }

        $totalRetours = 0; 

        $caNet = $caBrut - $totalRetours;

        // 3. Préparation des données pour le PDF et l'Email
        $donnees = [
            'date' => Carbon::now('Indian/Comoro')->format('d/m/Y'),
            
            // CORRECTION ICI : Ajout de la variable attendue par le fichier PDF et l'email
            'date_cloture' => Carbon::now('Indian/Comoro')->format('d/m/Y à H:i'),
            
            'caissier' => auth()->user()->name ?? 'Caissier',
            'total_commandes' => $commandes->count(),
            'ca_brut' => $caBrut,
            'total_retours' => $totalRetours,
            'ca_net' => $caNet,
            'modes' => $modesPaiement,
            'commandes' => $commandes
        ];

        try {
            // 4. Génération du fichier PDF en arrière-plan
            $pdf = PDF::loadView('pdf.cloture_journaliere', $donnees);
            
            $fileName = 'cloture_' . Carbon::now('Indian/Comoro')->format('Y_m_d_His') . '.pdf';
            $filePath = storage_path('app/public/clotures/' . $fileName);
            
            // Créer le dossier s'il n'existe pas
            if (!file_exists(storage_path('app/public/clotures'))) {
                mkdir(storage_path('app/public/clotures'), 0755, true);
            }

            $pdf->save($filePath);

            // 5. Envoi immédiat par Email à l'administrateur
            $adminEmail = env('ADMIN_EMAIL', 'proprietaire@raymultitech.com');
            Mail::to($adminEmail)->send(new RapportClotureMail($donnees, $filePath));

            // 6. Marquer les commandes comme 'clotures' pour verrouiller la journée
            Commande::whereBetween('date_commande', [$aujourdhui, $finAujourdhui])
                ->where('statut', 'completed')
                ->update(['statut' => 'cloture']);

            return redirect()->back()->with('success', 'La journée a été clôturée ! Le rapport PDF complet a été envoyé par email à l\'administration.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la clôture : ' . $e->getMessage());
        }
    }
}