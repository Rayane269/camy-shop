<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $commande->numero_commande }}</title>
    <style>
        /* CSS de base pour l'écran */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #000;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px 0;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        .ticket-container {
            width: 80mm;
            background: #fff;
            padding: 10px 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }

        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        .items-table th, .items-table td {
            font-size: 12px;
            padding: 3px 0;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            margin-top: 5px;
        }

        .totals-table td {
            font-size: 13px;
            padding: 2px 0;
        }

        .barcode-section {
            margin: 12px 0;
            text-align: center;
        }

        .actions-bar {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }

        /* --- STYLES EXCLUSIFS À L'IMPRESSION SUR LA CAISSE --- */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .ticket-container {
                width: 100%;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }

            @page {
                size: 80mm auto; /* Format rouleau thermique POSIKEX */
                margin: 0mm;
            }
        }
    </style>
</head>
<body>

    <!-- Boutons masqués à l'impression -->
    <div class="actions-bar no-print">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimer le ticket</button>
        <a href="{{ route('commandes.show', $commande) }}" class="btn btn-secondary">← Retour à la commande</a>
    </div>

    <div class="ticket-container">
        <!-- EN-TÊTE -->
        <div class="header text-center">
            <h2>RAY-MULTITECH</h2>
            <p>M'DE BAMABAO</p>
            <p>Tel : +269 448 04 33</p>
            <div class="divider"></div>
            <p>
                Caisse : {{ auth()->user()->name ?? 'Caissier' }}<br>
                Date : {{ $commande->date_commande ? $commande->date_commande->format('d/m/y H:i') : date('d/m/y H:i') }}
            </p>
        </div>

        <!-- CODE-BARRES TICKET -->
        <div class="barcode-section text-center">
            <div class="divider"></div>
            <span class="bold">{{ $commande->numero_commande }}</span>
            <div class="divider"></div>
        </div>

        <!-- LISTE DES ARTICLES -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-left" style="width: 50%;">ARTICLE</th>
                    <th class="text-right" style="width: 50%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commande->items as $item)
                    <tr>
                        <td class="text-left">
                            {{ mb_strtoupper($item->produit->nom ?? 'Article') }}
                            @if($item->quantite > 1)
                                <br><small>   {{ $item->quantite }}x {{ number_format($item->prix_unitaire, 0, '', ' ') }} KMF</small>
                            @endif
                        </td>
                        <td class="text-right bold">
                            {{ number_format($item->total, 0, '', ' ') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        <!-- TOTAUX ET PAIEMENT -->
        <table class="totals-table">
            <tr>
                <td class="bold">TOTAL ({{ $commande->items->count() }} art.)</td>
                <td class="text-right bold" style="font-size: 15px;">
                    {{ number_format($commande->total, 0, '', ' ') }} KMF
                </td>
            </tr>
            <tr>
                <td>PAIEMENT ({{ strtoupper($commande->paiement->mode_paiement_label ?? 'ESPECES') }})</td>
                <td class="text-right">
                    {{ number_format($commande->total, 0, '', ' ') }} KMF
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- PIED DE PAGE -->
        <div class="text-center" style="font-size: 11px; margin-top: 10px;">
            <p class="bold" style="margin-bottom: 5px;">* Ce ticket fait office de garantie *</p>
            <p style="margin: 2px 0;">GARANTIE 1 MOIS SUR L'ELECTRONIQUE</p>
            <p style="margin: 2px 0;">RETOUR POSSIBLE SOUS 48H</p>
            <p style="margin: 2px 0;">DANS L'EMBALLAGE D'ORIGINE</p>
            <p style="margin: 2px 0;">TICKET DE CAISSE OBLIGATOIRE.</p>
            <p class="bold" style="margin-top: 5px;">MERCI DE VOTRE VISITE !</p>
            <p style="margin-top: 5px;">www.librairie-camy.com</p>
        </div>
    </div>

    <!-- DECLENCHEMENT AUTOMATIQUE À L'OUVERTURE DE LA PAGE -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Déclenche automatiquement l'impression dès que la vue est chargée
            window.print();
        });
    </script>
</body>
</html>