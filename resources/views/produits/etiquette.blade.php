<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
    /* 1. Définition de la taille physique pour l'imprimante */
    @page { 
        size: 50mm 30mm; 
        margin: 0 !important; 
    } 

    /* 2. Reset du corps pour éviter les décalages */
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        width: 50mm;
        height: 30mm;
        overflow: hidden;
        background-color: white;
        -webkit-print-color-adjust: exact; /* Force l'impression du fond gris */
    }

    /* 3. Conteneur principal qui fait exactement la taille de l'étiquette */
    .label-container {
        width: 50mm;
        height: 30mm;
        position: relative;
        box-sizing: border-box;
    }

    .header { 
        background: #e5e5e5 !important; /* Force le gris sur Safari/Chrome */
        padding: 2mm 3mm; 
        height: 12mm; 
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .product-name { 
        font-size: 8pt; 
        font-weight: bold; 
        margin: 0; 
        width: 70%; 
        line-height: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .price { 
        position: absolute; 
        right: 2mm; 
        top: 50%;
        transform: translateY(-50%);
        font-size: 14pt; 
        font-weight: 900; 
    }

    .barcode-container { 
        text-align: center; 
        width: 100%;
        padding-top: 2mm;
    }
    
    /* On force le code-barre à rester dans les clous */
    .barcode-container div {
        margin: 0 auto !important;
        transform: scale(1.1); /* On l'agrandit un peu pour qu'il soit bien lisible */
    }

    .barcode-text { 
        font-size: 7pt; 
        margin-top: 1mm; 
        letter-spacing: 1.5mm; 
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class="header">
        <p class="product-name">{{ \Illuminate\Support\Str::limit($produit->nom, 25) }}</p>
        <p class="product-sku">REF: {{ $produit->id }}-{{ date('y') }}</p>
        <div class="price">
            {{ number_format($produit->prix, 0, ',', ' ') }}<span class="currency">KMF</span>
        </div>
    </div>

    <div class="barcode-container">
        <div style="display: inline-block;">
            {!! DNS1D::getBarcodeHTML($produit->code_barre, 'C128', 1.4, 35) !!}
        </div>
        <div class="barcode-text">{{ $produit->code_barre }}</div>
    </div>

    <script>
        // Lance l'impression et ferme l'onglet après (optionnel)
        window.onload = function() { 
            window.print(); 
            // window.onafterprint = function() { window.close(); }; 
        }
    </script>
</body>
</html>