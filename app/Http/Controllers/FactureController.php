<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function verifier($numero)
    {
        $commande = Commande::where('numero_commande', $numero)->first();

        if (!$commande) {
            return view('factures.verification', ['erreur' => 'Commande introuvable.']);
        }

        return view('factures.verification', compact('commande'));
    }
}
