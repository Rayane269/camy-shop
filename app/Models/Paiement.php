<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'montant',
        'mode_paiement',
        'statut_paiement',
        'date_paiement',
        'reference_transaction'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'datetime'
    ];

    const MODES_PAIEMENT = [
        'especes' => 'Espèces',
        'carte' => 'Carte bancaire',
        'mvula' => 'Mvula',
        'virement' => 'Virement',
        'exim' => 'Exim',
    ];

    const STATUTS_PAIEMENT = [
        'en_attente' => 'En attente',
        'paye' => 'Payé',
        'echec' => 'Échec',
        'rembourse' => 'Remboursé'
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function getModePaiementLabelAttribute()
    {
        return self::MODES_PAIEMENT[$this->mode_paiement] ?? $this->mode_paiement;
    }

    public function getStatutPaiementLabelAttribute()
    {
        return self::STATUTS_PAIEMENT[$this->statut_paiement] ?? $this->statut_paiement;
    }
}
