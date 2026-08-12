<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    public function imprimer(Commande $commande)
    {
        // On charge la vue que nous avons créée
        $pdf = Pdf::loadView('factures.ticket', compact('ticket'))
            ->setPaper([0, 0, 204, 600], 'portrait') // Format ticket de caisse
            ->setOptions([
                'isRemoteEnabled' => true, // Pour autoriser le code-barres
                'defaultFont' => 'sans-serif'
            ]);

        // Afficher le PDF dans le navigateur
        return $pdf->stream('ticket-'.$commande->numero_commande.'.pdf');
    }
}