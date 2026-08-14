<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $commande->numero_commande }}</title>
    <style>
        /* Print A4 */
        @page { size: A4; margin: 18mm; }

        :root{
            --accent: #0b5cff;
            --muted: #6c757d;
            --dark: #222222;
            --bg: #ffffff;
            --border: #e6e9ef;
            --radius: 6px;
            --font-stack: 'Helvetica', 'Arial', sans-serif;
        }

        html,body{margin:0;padding:0;background:var(--bg);font-family:var(--font-stack);color:var(--dark);}
        body{font-size:13px;line-height:1.45;-webkit-print-color-adjust:exact}

        .container{width:100%;max-width:800px;margin:20px auto;padding:18px}

        /* Header */
        .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px}
        .brand{display:flex;gap:12px;align-items:center}
        .brand img{width:150px;height:auto}
        .company{font-size:13px;color:var(--muted)}

        .invoice-meta{ text-align:right }
        .invoice-title{ font-size:28px;font-weight:700;color:var(--dark);text-transform:uppercase;margin:0 }
        .meta-box{ background:linear-gradient(180deg, rgba(11,92,255,0.06), transparent);padding:10px;border-radius:6px;border:1px solid var(--border);display:inline-block;margin-top:8px }
        .meta-box strong{display:block;font-size:14px}

        /* Client + Infos */
        .top-info{display:flex;justify-content:space-between;gap:16px;margin-top:18px}
        .client{padding:12px;border-radius:6px;border:1px solid var(--border);width:60%}
        .client h4{margin:0 0 6px 0;font-size:14px}
        .client p{margin:0;color:var(--muted);font-size:13px}

        .payment{width:35%;padding:12px;border-radius:6px;border:1px solid var(--border)}
        .payment .label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
        .payment .value{font-weight:700;margin-top:6px}

        /* Table */
        table.invoice-table{width:100%;border-collapse:collapse;margin-top:18px;background:#fff}
        table.invoice-table thead th{background:#f7f9ff;color:var(--dark);font-weight:700;text-align:left;padding:12px;border-bottom:1px solid var(--border);font-size:12px}
        table.invoice-table tbody td{padding:12px;border-bottom:1px solid var(--border);vertical-align:middle}
        table.invoice-table tbody tr:nth-child(even){background:#fbfdff}
        .text-right{text-align:right}
        .qty{width:80px;text-align:right}
        .unit{width:120px;text-align:right}
        .total{width:140px;text-align:right}

        /* Totals */
        .totals{margin-top:18px;display:flex;justify-content:flex-end}
        .totals .box{width:320px;border-radius:6px;border:1px solid var(--border);padding:12px;background:#fafbff}
        .totals .row{display:flex;justify-content:space-between;padding:6px 4px}
        .totals .row.total{font-weight:800;font-size:16px;border-top:1px dashed var(--border);padding-top:10px;margin-top:10px}

        /* Footer */
        .footer{margin-top:28px;padding-top:12px;border-top:1px solid var(--border);font-size:12px;color:var(--muted);text-align:center}

        /* Print adjustments */
        @media print{
            .container{margin:0;padding:0}
            .no-print{display:none}
            .footer{position:fixed;bottom:12mm;left:0;right:0}
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <header class="header">
            <div class="brand">
                <div>
                    <img src="{{ asset('images/logo-fac.png') }}" alt="logo-fac">
                </div>
                <div class="company">
                    <strong>Librairie Camy</strong><br>
                    Moroni Iroungoudjani, Comores<br>
                    Tél : 773 01 69 / 333 01 69 / 334 57 53<br>
                    librairiecamy@yahoo.fr
                </div>
            </div>

            <div class="invoice-meta">
                <h1 class="invoice-title">Facture</h1>
                <div class="meta-box">
                    <div>N° <strong>{{ $commande->numero_commande }}</strong></div>
                    <div>Date : <strong>{{ $commande->date_commande->format('d/m/Y') }}</strong></div>
                </div>
            </div>
        </header>

        <section class="top-info">
            <div class="client">
                <h4>Facturé à</h4>
                <p style="font-weight:700; font-size:15px;">{{ strtoupper($commande->client->nom) }} {{ $commande->client->prenom }}</p>
                <p>Tél : {{ $commande->client->telephone }}</p>
            </div>

            <aside class="payment">
                <div class="label">Mode de paiement</div>
                <div class="value">{{ $commande->paiement->mode_paiement_label }}</div>
                @if($commande->paiement->reference_transaction)
                    <div style="margin-top:8px;color:var(--muted);font-size:13px">Réf : {{ $commande->paiement->reference_transaction }}</div>
                @endif
            </aside>
        </section>

        <table class="invoice-table" role="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="qty">Quantité</th>
                    <th class="unit">Prix Unitaire</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commande->items as $item)
                    <tr>
                        <td style="font-weight:700">{{ $item->produit->nom }}</td>
                        <td class="qty">{{ $item->quantite }}</td>
                        <td class="unit">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} KMF</td>
                        <td class="total">{{ number_format($item->total, 0, ',', ' ') }} KMF</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="box">
                <div class="row"><div>Sous-total</div><div>{{ number_format($commande->total, 0, ',', ' ') }} KMF</div></div>
                {{-- Si vous avez TVA, remises, etc., ajoutez ici des lignes supplémentaires --}}
                <div class="row total"><div>TOTAL NET</div><div>{{ number_format($commande->total, 0, ',', ' ') }} KMF</div></div>
            </div>
        </div>

        <div class="footer">
            Librairie Camy — RC : MORONI — Conditions de vente disponibles sur www.librairie-camy.com
        </div>

        <div class="no-print" style="position:fixed;top:18px;right:18px">
            <button onclick="window.history.back()" style="padding:8px 12px;border-radius:6px;border:1px solid var(--border);background:#fff;cursor:pointer">← Retour</button>
        </div>
    </div>

</body>
</html>