<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $commande->numero_commande }}</title>
    <style>
        /* Print A4 */
        @page { size: A4; margin: 12mm; }

        :root{
            --accent:#1d4ed8; /* deep blue */
            --accent-2:#7c3aed; /* purple */
            --muted:#6b7280;
            --dark:#0f172a;
            --bg:#ffffff;
            --card:#f8fafc;
            --border:#e6eef8;
            --radius:8px;
            --font:'Helvetica', 'Arial', sans-serif;
        }

        html,body{margin:0;padding:0;background:var(--bg);font-family:var(--font);color:var(--dark)}
        body{font-size:13px;line-height:1.45;-webkit-print-color-adjust:exact}

        .sheet{max-width:820px;margin:12px auto;padding:16px}

        /* Accent header */
        .accent{background:linear-gradient(90deg,var(--accent),var(--accent-2));padding:14px;border-radius:10px;color:#fff;display:flex;justify-content:space-between;align-items:center}
        .accent .left{display:flex;gap:14px;align-items:center}
        .accent img{width:72px;height:auto;border-radius:6px;background:rgba(255,255,255,0.06);padding:6px}
        .accent .name{font-size:18px;font-weight:800}
        .accent .tag{font-size:12px;opacity:0.95}

        .accent .right{text-align:right}
        .badge{background:rgba(255,255,255,0.12);padding:8px 12px;border-radius:10px;display:inline-block}
        .badge strong{display:block;font-size:16px}

        /* Main card */
        .card{background:var(--card);padding:16px;border-radius:8px;margin-top:12px;border:1px solid var(--border)}
        .row{display:flex;justify-content:space-between;gap:12px}

        .client{flex:1;padding:10px}
        .client h4{margin:0 0 6px 0;font-size:13px}

        .infos{width:260px;padding:10px;text-align:right}
        .infos .label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
        .infos .val{font-weight:800;font-size:15px;margin-top:6px}

        /* Table */
        table{width:100%;border-collapse:collapse;margin-top:14px;background:#fff;border-radius:6px;overflow:hidden}
        thead th{background:#f1f5f9;padding:12px 14px;text-align:left;font-size:12px;color:var(--dark);font-weight:700}
        tbody td{padding:12px 14px;border-bottom:1px solid var(--border);font-size:13px}
        tbody tr:last-child td{border-bottom:0}
        .muted{color:var(--muted)}
        .text-right{text-align:right}

        /* Totals */
        .summary{display:flex;justify-content:flex-end;margin-top:14px}
        .summary .inner{width:340px;background:#fff;padding:12px;border-radius:8px;border:1px solid var(--border)}
        .summary .line{display:flex;justify-content:space-between;padding:6px 0}
        .summary .grand{font-size:18px;font-weight:900;color:var(--accent)}

        /* Notes & footer */
        .notes{margin-top:16px;font-size:12px;color:var(--muted)}
        .footer{margin-top:18px;font-size:12px;color:var(--muted);text-align:center}

        @media print{.no-print{display:none}.sheet{margin:0;padding:0}}
    </style>
</head>
<body onload="window.print()">

    <div class="sheet">
        <div class="accent">
            <div class="left">
                <img src="{{ asset('images/logo-fac.png') }}" alt="logo">
                <div>
                    <div class="name">Librairie Camy</div>
                    <div class="tag">Moroni Iroungoudjani · Comores · librairiecamy@yahoo.fr</div>
                </div>
            </div>
            <div class="right">
                <div class="badge">
                    <div class="muted">Facture</div>
                    <strong>#{{ $commande->numero_commande }}</strong>
                </div>
                <div style="margin-top:8px;color:rgba(255,255,255,0.92)">Date : <strong>{{ $commande->date_commande->format('d/m/Y') }}</strong></div>
            </div>
        </div>

        <div class="card">
            <div class="row">
                <div class="client">
                    <h4>Facturé à</h4>
                    <div style="font-weight:800;font-size:15px">{{ strtoupper($commande->client->nom) }} {{ $commande->client->prenom }}</div>
                    <div class="muted" style="margin-top:6px">Tél : {{ $commande->client->telephone }}</div>
                </div>

                <div class="infos">
                    <div class="label">Mode de paiement</div>
                    <div class="val">{{ $commande->paiement->mode_paiement_label }}</div>
                    @if($commande->paiement->reference_transaction)
                        <div class="muted" style="margin-top:8px">Réf : {{ $commande->paiement->reference_transaction }}</div>
                    @endif
                </div>
            </div>

            <table role="table" aria-label="Détails de la commande">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th style="width:80px;text-align:right">Qté</th>
                        <th style="width:140px;text-align:right">Prix Un.</th>
                        <th style="width:160px;text-align:right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commande->items as $item)
                        <tr>
                            <td style="font-weight:700">{{ $item->produit->nom }}</td>
                            <td class="text-right">{{ $item->quantite }}</td>
                            <td class="text-right">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} KMF</td>
                            <td class="text-right">{{ number_format($item->total, 0, ',', ' ') }} KMF</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <div class="inner">
                    <div class="line"><div class="muted">Sous-total</div><div>{{ number_format($commande->total, 0, ',', ' ') }} KMF</div></div>
                    {{-- Exemple: TVA ou remises peuvent être insérées ici --}}
                    <div class="line grand"><div>TOTAL À PAYER</div><div>{{ number_format($commande->total, 0, ',', ' ') }} KMF</div></div>
                </div>
            </div>

            <div class="notes">
                <strong>Note :</strong> Merci pour votre commande. Conservez cette facture pour toute réclamation.
            </div>
        </div>

        <div class="footer">Librairie Camy — RC : MORONI — Conditions de vente disponibles sur www.librairie-camy.com</div>
        <div class="no-print" style="position:fixed;top:12px;right:12px"><button onclick="window.history.back()" style="padding:7px 10px;border-radius:6px;border:0;background:#fff;cursor:pointer">← Retour</button></div>
    </div>

</body>
</html>