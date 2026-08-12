<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clôture de Caisse</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <h2 style="color: #1e3a8a; font-size: 20px; font-weight: 800; text-transform: uppercase; margin-bottom: 20px;">
        Bonjour Administrateur,
    </h2>
    
    <p style="font-size: 15px; margin-bottom: 25px;">
        La caisse a été clôturée avec succès pour la journée du 
        <strong>{{ $donnees['date_cloture'] ?? date('d/m/Y à H:i') }}</strong> 
        par <strong>{{ $donnees['caissier'] ?? auth()->user()->name ?? 'Caissier' }}</strong>.
    </p>
    
    <div style="background-color: #f8fafc; border: 1px border-radius: 12px; padding: 20px; margin-bottom: 25px; border: 1px solid #e2e8f0;">
        <h3 style="color: #475569; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0; margin-bottom: 15px; border-b: 1px solid #e2e8f0; padding-bottom: 8px;">
            Résumé financier de la journée :
        </h3>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Nombre de commandes :</td>
                <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #1e293b;">
                    {{ $donnees['total_commandes'] ?? 0 }}
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Chiffre d'affaires Brut :</td>
                <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #1e293b;">
                    {{ number_format($donnees['ca_brut'] ?? 0, 0, '', ' ') }} KMF
                </td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #dc2626;">Total des retours / remboursements :</td>
                <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #dc2626;">
                    - {{ number_format($donnees['total_retours'] ?? 0, 0, '', ' ') }} KMF
                </td>
            </tr>
            <tr style="border-top: 1px solid #e2e8f0;">
                <td style="padding: 12px 0 0 0; font-weight: 800; color: #1e3a8a; font-size: 15px;">Chiffre d'affaires Net :</td>
                <td style="padding: 12px 0 0 0; text-align: right; font-weight: 900; color: #16a34a; font-size: 18px;">
                    {{ number_format($donnees['ca_net'] ?? 0, 0, '', ' ') }} KMF
                </td>
            </tr>
        </table>
    </div>

    <p style="font-size: 14px; color: #64748b;">
        <span style="font-weight: 700; color: #475569;">Note :</span> Vous trouverez en pièce jointe le rapport complet au format PDF contenant le détail de tous les mouvements (commandes et retours de stock de la journée).
    </p>
    
    <div style="margin-top: 35px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 13px; color: #94a3b8;">
        Cordialement,<br>
        <strong style="color: #475569; font-style: normal;">Système de Caisse Ray-Multitech</strong>
    </div>
</body>
</html>