<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'equipement_id',
        'date_debut',
        'date_fin',
        'montant_total',
        'statut'
    ];

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
