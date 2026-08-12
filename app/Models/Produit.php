<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code_barre',
        'description',
        'prix',
        'stock',
        'image'
    ];

    protected $casts = [
        'prix' => 'decimal:2'
    ];

    public function commandeItems()
    {
        return $this->hasMany(CommandeItem::class);
    }
}
