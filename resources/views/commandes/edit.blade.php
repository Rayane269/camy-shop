<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-gray-800">
            Modifier la Commande #{{ $commande->id }}
        </h2>
    </x-slot>

    <div class="py-6 px-4 max-w-7xl mx-auto space-y-6">
        <form method="POST" action="{{ route('commandes.update', $commande) }}" id="commandeForm" class="space-y-6 mt-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Informations de la commande</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700">Client *</label>
                                <select name="client_id" id="client_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $commande->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom }} {{ $client->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('notes', $commande->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white shadow-md rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Articles</h3>
                            <div class="flex gap-2">
                                <button type="button" onclick="ajouterArticle()" class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
                                    <i data-lucide="plus" class="w-4 h-4"></i> Manuel
                                </button>
                            </div>
                        </div>
                        <div id="articles-container" class="space-y-4">
                            </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white shadow-md rounded-lg p-6 sticky top-6">
                        <h3 class="text-lg font-semibold mb-4">Résumé</h3>
                        <div class="flex justify-between mb-2">
                            <span>Total articles:</span>
                            <span id="total-articles" class="font-bold text-lg">0</span>
                        </div>
                        <div class="flex justify-between mb-6">
                            <span>Total commande:</span>
                            <span id="total-commande" class="font-bold text-2xl text-blue-600">0,00 KMF</span>
                        </div>
                        <div class="space-y-2">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-bold hover:bg-blue-700 flex justify-center items-center gap-2">
                                <i data-lucide="refresh-cw" class="w-5 h-5"></i> Mettre à jour
                            </button>
                            <a href="{{ route('commandes.index') }}" class="w-full inline-block text-center bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let articleIndex = 0;
        const produits = @json($produitsFormates);
        // Charger les items existants de la commande
        const itemsExistants = @json($commande->items);

        // 1. GESTION DU SCANNER (Même logique que Create)
        let barcodeBuffer = "";
        let lastKeyTime = Date.now();
        window.addEventListener('keydown', (e) => {
            const currentTime = Date.now();
            if (currentTime - lastKeyTime > 50) barcodeBuffer = "";
            if (e.key === 'Enter') {
                if (barcodeBuffer.length > 2) {
                    e.preventDefault();
                    handleScan(barcodeBuffer);
                    barcodeBuffer = "";
                }
            } else if (e.key.length === 1) {
                barcodeBuffer += e.key;
            }
            lastKeyTime = currentTime;
        });

        function handleScan(code) {
            const produit = produits.find(p => p.code_barre === code);
            if (produit) ajouterOuIncrementerProduit(produit);
        }

        // 2. LOGIQUE D'AJOUT / RENDU
        function ajouterArticle(produitId = "", quantite = 1) {
            const container = document.getElementById('articles-container');
            const html = `
                <div class="article-item border rounded-lg p-4 bg-gray-50 shadow-sm" data-index="${articleIndex}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-5">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Produit</label>
                            <select name="items[${articleIndex}][produit_id]" class="produit-select block w-full border-gray-300 rounded-md mt-1" required onchange="updatePrix(this)">
                                <option value="">Choisir...</option>
                                ${produits.map(p => `<option value="${p.id}" ${p.id == produitId ? 'selected' : ''} data-prix="${p.prix}" data-stock="${p.stock}">${p.nom}</option>`).join('')}
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Prix</label>
                            <input type="text" class="prix-unitaire block w-full border-gray-300 rounded-md mt-1 bg-gray-100" readonly>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Quantité</label>
                            <input type="number" name="items[${articleIndex}][quantite]" class="quantite block w-full border-gray-300 rounded-md mt-1" min="1" value="${quantite}" required onchange="calculerTotal()">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Total</label>
                            <input type="text" class="total-ligne block w-full border-gray-300 rounded-md mt-1 bg-gray-100 font-bold" readonly>
                        </div>
                        <div class="md:col-span-1 flex items-end justify-center pb-2">
                            <button type="button" onclick="this.closest('.article-item').remove(); calculerTotal();" class="text-red-500 hover:text-red-700">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            
            container.insertAdjacentHTML('beforeend', html);
            lucide.createIcons();
            
            const currentSelect = container.querySelector(`[data-index="${articleIndex}"] .produit-select`);
            if(produitId) updatePrix(currentSelect);
            
            articleIndex++;
        }

        function updatePrix(select) {
            const option = select.selectedOptions[0];
            const item = select.closest('.article-item');
            if (option.value) {
                const prix = parseFloat(option.dataset.prix);
                item.querySelector('.prix-unitaire').value = prix.toLocaleString() + ' KMF';
            }
            calculerTotal();
        }

        function calculerTotal() {
            let totalQty = 0;
            let totalCmd = 0;
            document.querySelectorAll('.article-item').forEach(item => {
                const select = item.querySelector('.produit-select');
                const qtyInput = item.querySelector('.quantite');
                if (select.value && qtyInput.value) {
                    const prix = parseFloat(select.selectedOptions[0].dataset.prix);
                    const qty = parseInt(qtyInput.value);
                    const subtotal = prix * qty;
                    item.querySelector('.total-ligne').value = subtotal.toLocaleString() + ' KMF';
                    totalQty += qty;
                    totalCmd += subtotal;
                }
            });
            document.getElementById('total-articles').textContent = totalQty;
            document.getElementById('total-commande').textContent = totalCmd.toLocaleString() + ',00 KMF';
        }

        // INITIALISATION : Charger les items de la commande
        document.addEventListener('DOMContentLoaded', () => {
            if (itemsExistants.length > 0) {
                itemsExistants.forEach(item => {
                    ajouterArticle(item.produit_id, item.quantite);
                });
            }
            lucide.createIcons();
            calculerTotal();
        });
    </script>
</x-app-layout>