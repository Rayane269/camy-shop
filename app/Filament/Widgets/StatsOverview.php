<?php

namespace App\Filament\Widgets;

use App\Models\Commande;
use App\Models\Client;
use App\Models\Paiement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s'; 

    protected function getStats(): array
    {
        // 1. Calcul du CA du jour (On utilise le statut 'paye' enregistré par la caisse)
        $ca_jour = Paiement::where('statut_paiement', 'paye')
            ->whereDate('date_paiement', Carbon::today())
            ->sum('montant');

        // 2. Calcul du manque à gagner
        // On considère comme manque à gagner tout ce qui est en attente (non encore livré/payé)
        $manque_a_gagner = Commande::where('statut', 'en_attente')
            ->sum('total');

        // 3. Nombre de clients
        $total_clients = Client::count();

        // 4. Stock critique
        $stock_critique = \App\Models\Produit::where('stock', '<=', 5)->count();

        return [
            Stat::make('Chiffre d\'Affaires Jour', number_format($ca_jour, 0, '.', ' ') . ' KMF')
                ->description('Recettes réelles du ' . Carbon::now()->format('d/m/Y'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Manque à Gagner', number_format($manque_a_gagner, 0, '.', ' ') . ' KMF')
                ->description('Total des impayés ou commandes en attente')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Total Clients', $total_clients)
                ->description('Base de données active')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Alertes Stock', $stock_critique)
                ->description($stock_critique > 0 ? 'Articles à commander d\'urgence' : 'Stock optimal')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stock_critique > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'class' => $stock_critique > 0 ? 'animate-pulse' : '',
                ]),
        ];
    }
}