<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'image', 'description', 'tarif_journalier', 'stock'];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
