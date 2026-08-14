<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $commande->numero_commande }}</title>
    <style>
        /* Configuration A4 */
        @page { size: A4; margin: 15mm; }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 13px; 
            color: #333;
            line-height: 1.5;
            margin: 0;
        }

        .header-table { width: 100%; margin-bottom: 30px; border: none; }
        .header-table td { border: none; vertical-align: top; }

        .invoice-title { 
            font-size: 28px; 
            font-weight: bold; 
            color: #1a1a1a; 
            text-transform: uppercase;
            text-align: right;
            margin: 0;
        }

        .info-box { margin-bottom: 20px; }
        .info-box strong { color: #000; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { 
            background-color: #f8f9fa; 
            color: #000; 
            text-align: left; 
            text-transform: uppercase; 
            font-size: 11px;
            letter-spacing: 1px;
        }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eee; }
        
        .text-right { text-align: right; }
        
        .total-section { 
            margin-top: 30px; 
            float: right; 
            width: 250px; 
        }
        .total-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 10px 0;
            font-size: 16px;
        }
        .total-final { 
            border-top: 2px solid #000; 
            font-weight: bold; 
            font-size: 18px;
            margin-top: 5px;
        }

        .footer { 
            position: fixed; 
            bottom: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 10px; 
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        /* Bouton retour visible seulement à l'écran */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td>
                <img src="{{ asset('images/logo-fac.png') }}" alt="logo" width="180">
                <p style="margin-top: 10px;">
                    <strong>Librairie Camy</strong><br>
                    Adresse : Moroni Iroungoudjani, Comores<br>
                    Tél :  773 01 69/ 333 01 69 / 334 57 53
                    Email :librairiecamy@yahoo.fr
                </p>
            </td>
            <td>
                <h1 class="invoice-title">Facture</h1>
                <p class="text-right">
                    N° : <strong>{{ $commande->numero_commande }}</strong><br>
                    Date : {{ $commande->date_commande->format('d/m/Y') }}
                </p>
            </td>
        </tr>
    </table>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

    <div class="info-box">
        <p><strong>FACTURÉ À :</strong></p>
        <p style="font-size: 16px; margin: 5px 0;">
            {{ strtoupper($commande->client->nom) }} {{ $commande->client->prenom }}
        </p>
        <p>Téléphone : {{ $commande->client->telephone }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation du produit</th>
                <th class="text-right">Quantité</th>
                <th class="text-right">Prix Unit.</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->items as $item)
                <tr>
                    <td style="font-weight: bold;">{{ $item->produit->nom }}</td>
                    <td class="text-right">{{ $item->quantite }}</td>
                    <td class="text-right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} KMF</td>
                    <td class="text-right">{{ number_format($item->total, 0, ',', ' ') }} KMF</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Total Partiel :</span>
            <span>{{ number_format($commande->total, 0, ',', ' ') }} KMF</span>
        </div>
        <div class="total-row total-final">
            <span>TOTAL NET :</span>
            <span>{{ number_format($commande->total, 0, ',', ' ') }} KMF</span>
        </div>
        
        <p style="margin-top: 20px; font-size: 11px;">
            Mode de paiement : <strong>{{ $commande->paiement->mode_paiement_label }}</strong>
            @if($commande->paiement->reference_transaction)
                <br>Réf : {{ $commande->paiement->reference_transaction }}
            @endif
        </p>
    </div>

    <div class="footer">
        Librairie Camy - RC : MORONI - Conditions de vente disponibles sur www.librairie-camy.com
    </div>

    <div class="no-print" style="position: fixed; top: 20px; right: 20px;">
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer;">← Retour</button>
    </div>

</body>
</html>