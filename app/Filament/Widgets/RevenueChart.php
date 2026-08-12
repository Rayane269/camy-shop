<?php

namespace App\Filament\Widgets;

use App\Models\Paiement;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des Revenus (7 jours)';
    protected static string $color = 'success';
    protected static ?string $pollingInterval = '15s';

    protected function getData(): array
    {
        // On génère les 7 derniers jours
        $data = collect(range(6, 0))->map(function ($days) {
            $date = Carbon::today()->subDays($days);
            
            // CORRECTION ICI : statut 'complete', colonne 'montant' et date_paiement
            $sum = Paiement::where('statut_paiement', 'complete')
                ->whereDate('date_paiement', $date)
                ->sum('montant');
            
            return [
                'label' => $date->translatedFormat('d M'), // Format plus joli (ex: 25 fév)
                'aggregate' => $sum,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenus (KMF)',
                    'data' => $data->pluck('aggregate')->toArray(),
                    'fill' => 'start',
                    'tension' => 0.4, // Ajoute une jolie courbe lisse
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}