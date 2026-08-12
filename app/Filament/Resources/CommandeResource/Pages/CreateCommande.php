<?php

namespace App\Filament\Resources\CommandeResource\Pages;

use App\Filament\Resources\CommandeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCommande extends CreateRecord
{
    protected static string $resource = CommandeResource::class;

    protected function getRedirectUrl():string 
    {
        return $this ->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string 
    {
        return ' Commande crée ';
    }
}
