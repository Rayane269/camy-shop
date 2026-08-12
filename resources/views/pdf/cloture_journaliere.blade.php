<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport de Clôture de Caisse</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1e3a8a; padding-bottom: 8px; }
        .title { font-size: 22px; font-weight: bold; color: #1e3a8a; letter-spacing: 1px; }
        .subtitle { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-top: 2px; }
        
        .meta-table { width: 100%; margin-bottom: 20px; font-size: 11px; }
        .meta-table td { padding: 3px 0; }
        
        .blocks-table { width: 100%; margin-bottom: 25px; border-spacing: 15px 0; margin-left: -15px; margin-right: -15px; }
        .block-card { width: 50%; border: 1px solid #cbd5e1; background-color: #f8fafc; padding: 12px; vertical-align: top; border-radius: 8px; }
        .block-card h3 { font-size: 12px; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-top: 0; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        
        .table-summary { width: 100%; border-collapse: collapse; }
        .table-summary td { padding: 4px 0; font-size: 11px; }
        
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #e2e8f0; padding: 7px 8px; text-align: left; }
        .table th { background-color: #f1f5f9; font-weight: bold; color: #334155; text-transform: uppercase; font-size: 10px; }
        
        /* Classes d'états pour le SAV */
        .row-refunded { background-color: #fef3c7; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 9px; font-weight: bold; border-radius: 3px; text-transform: uppercase; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-warning { background-color: #ffedd5; color: #9a3412; }
        .badge-success { background-color: #dcfce7; color: #166534; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">LIBRAIRIE-CAMY/div>
        <div class="subtitle">Rapport de Clôture de Caisse Journalier</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;"><strong>Date Opérationnelle :</strong> {{ $date }}</td>
            <td style="width: 50%; text-align: right;"><strong>Généré le :</strong> {{ $date_cloture }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Responsable de Caisse :</strong> {{ $caissier }}</td>
        </tr>
    </table>

    {{-- Utilisation d'un tableau pour aligner les deux blocs proprement sans bug DomPDF --}}
    <table class="blocks-table">
        <tr>
            <td class="block-card">
                <h3>Résumé Financier</h3>
                <table class="table-summary">
                    <tr>
                        <td>Total Commandes Actives:</td>
                        <td class="text-right font-bold">{{ $total_commandes }}</td>
                    </tr>
                    <tr>
                        <td>Chiffre d'Affaires Brut :</td>
                        <td class="text-right font-bold">{{ number_format($ca_brut, 0, '', ' ') }} KMF</td>
                    </tr>
                    <tr>
                        <td style="color: #b91c1c;">Total Retours / Remboursements :</td>
                        <td class="text-right font-bold" style="color: #b91c1c;">- {{ number_format($total_retours, 0, '', ' ') }} KMF</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-top: 1px dashed #cbd5e1; padding-top: 5px; margin-top: 5px;"></td>
                    </tr>
                    <tr>
                        <td class="font-bold" style="font-size: 12px; color: #1e3a8a;">CHIFFRE D'AFFAIRES NET :</td>
                        <td class="text-right font-bold" style="font-size: 13px; color: #16a34a;">{{ number_format($ca_net, 0, '', ' ') }} KMF</td>
                    </tr>
                </table>
            </td>

            <td class="block-card">
                <h3>Répartition des Règlements (Nets)</h3>
                <table class="table-summary">
                    @foreach($modes as $mode => $montant)
                    <tr>
                        <td>Mode {{ $mode }} :</td>
                        <td class="text-right font-bold">{{ number_format($montant, 0, '', ' ') }} KMF</td>
                    </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <h3 style="font-size: 13px; color: #1e293b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-top: 20px;">Détail des Transactions du Jour</h3>
    
    <table class="table">
        <thead>
            <tr>
                <th style="width: 15%;">N° Commande</th>
                <th style="width: 35%;">Client</th>
                <th style="width: 12%;" class="text-center">Heure</th>
                <th style="width: 18%;" class="text-center">Mode</th>
                <th style="width: 20%;" class="text-right">Montant Final</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commandes as $commande)
            {{-- On applique un fond jaune clair discret si la commande a subi des mouvements de retour --}}
            <tr class="{{ $commande->a_un_retour ? 'row-refunded' : '' }}">
                <td class="font-bold">#{{ $commande->numero_commande }}</td>
                <td>
                    {{ $commande->client->nom }} {{ $commande->client->prenom }}
                    
                    {{-- Ajout de badges informatifs pour l'administrateur --}}
                    @if($commande->a_un_retour === 'total')
                        <span class="badge badge-danger">Remboursé</span>
                    @elseif($commande->a_un_retour === 'partiel')
                        <span class="badge badge-warning">Retour Partiel</span>
                    @endif
                </td>
                <td class="text-center">{{ $commande->date_commande->format('H:i') }}</td>
                <td class="text-center">
                    <small style="font-style: italic;">{{ $commande->paiement->mode_paiement_label ?? 'ESPECES' }}</small>
                </td>
                <td class="text-right font-bold">
                    <span style="{{ $commande->total == 0 ? 'text-decoration: line-through; color: #94a3b8;' : '' }}">
                        {{ number_format($commande->total, 0, '', ' ') }} KMF
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>