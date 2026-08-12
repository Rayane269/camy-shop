<?php

// app/Http/Controllers/AffichageClientController.php

namespace App\Http\Controllers;

use App\Models\Commande;

class AffichageClientController extends Controller
{
    public function show(Commande $commande)
    {
        return view('affichage-client', compact('commande'));
    }
}

