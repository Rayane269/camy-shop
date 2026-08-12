<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'numero_commande',
        'statut',
        'total',
        'date_commande',
        'notes'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'date_commande' => 'datetime'
    ];

    const STATUTS = [
        'en_attente' => 'En attente',
        'confirmee' => 'Confirmée',
        'en_preparation' => 'En préparation',
        'expediee' => 'Expédiée',
        'livree' => 'Livrée',
        'annulee' => 'Annulée'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(CommandeItem::class);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }

    public function getStatutLabelAttribute()
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    /**
     * Détermine si la commande a fait l'objet d'un retour partiel ou total
     */
    public function getAUnRetourAttribute()
    {
        // Si le statut est explicitement "annulee" ou si le montant est tombé à 0 KMF
        if ($this->statut === 'annulee' || (float)$this->total == 0) {
            return 'total';
        }

        // Si la commande n'est pas annulée mais qu'elle a des lignes d'articles vides (quantité passée à 0 suite au retour)
        // cela signifie qu'un ou plusieurs articles ont été rendus mais pas la totalité de la commande.
        $changementLignes = $this->items()->where('quantite', 0)->exists();
        if ($changementLignes) {
            return 'partiel';
        }

        return 'non';
    }

    /**
     * Génère un numéro de commande unique basé sur la dernière commande de l'année en cours.
     */
    public static function genererNumeroUnique()
    {
        $anneeEnCours = date('Y');

        // On cherche la dernière commande créée cette année
        $derniereCommande = static::whereYear('created_at', $anneeEnCours)
            ->orderBy('id', 'desc')
            ->first();

        if ($derniereCommande && $derniereCommande->numero_commande) {
            // On récupère les 5 derniers chiffres (ex: "CMD-2026-00014" -> "00014" -> 14)
            $dernierNumero = (int) substr($derniereCommande->numero_commande, -5);
            $prochainNombre = $dernierNumero + 1;
        } else {
            // S'il n'y a aucune commande pour cette année
            $prochainNombre = 1;
        }

        // On assemble le tout : CMD-2026-00015
        return 'CMD-' . $anneeEnCours . '-' . str_pad($prochainNombre, 5, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($commande) {
            // Si le numéro n'a pas déjà été défini manuellement dans le contrôleur, on le génère de façon robuste
            if (empty($commande->numero_commande)) {
                $commande->numero_commande = static::genererNumeroUnique();
            }
        });
    }
}