<x-app-layout>
    <x-slot name="header">
       <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight uppercase tracking-tighter">
                Créer une<span class="text-blue-600"> nouvelle commande</span>
            </h2>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200 animate-pulse">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Scanner Instantané Prêt
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">
        <form method="POST" action="{{ route('commandes.store') }}" id="commandeForm" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- CLIENT --}}
                    <div class="overflow-hidden rounded-[1.5rem] border border-gray-200 bg-white shadow-[0_12px_45px_-20px_rgba(15,23,42,0.25)]">
                        <div class="flex items-center justify-between border-b border-gray-100 bg-gradient-to-r from-white via-blue-50/40 to-white px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="user-plus" class="w-5 h-5 text-blue-600"></i>
                                <h3 class="font-bold text-gray-700">Client</h3>
                            </div>
                            <a href="{{ route('clients.create') }}" class="flex items-center gap-1 text-xs font-bold uppercase tracking-wider text-blue-600 hover:underline">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i> Nouveau client
                            </a>
                        </div>
                        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                            <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">

                            <div class="space-y-3">
                                <p class="text-sm font-black uppercase tracking-widest text-gray-400">Mode client</p>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="client-standard-btn" onclick="setClientMode('standard')" class="inline-flex items-center justify-center rounded-full border border-blue-200 bg-blue-600 px-4 py-2 text-sm font-black text-white transition hover:bg-blue-700">Client standard</button>
                                    <button type="button" id="client-fidele-btn" onclick="setClientMode('fidele')" class="inline-flex items-center justify-center rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-black text-gray-700 transition hover:border-blue-300">Client fidèle</button>
                                </div>
                                <p id="client-mode-help" class="text-xs text-gray-400">Commande sans coordonnées client. Utilisez le téléphone seulement pour les clients fidèles.</p>
                            </div>

                            <div class="space-y-3">
                                <label for="client_phone" class="block text-xs font-bold text-gray-400 uppercase mb-2">Téléphone client</label>
                                <input type="tel" name="client_phone" id="client_phone" value="{{ old('client_phone') }}" placeholder="Ex: 34xxxxxx" class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-bold text-gray-700 focus:border-blue-500 focus:ring-blue-500" oninput="lookupClientByPhone(this.value)">
                                <p id="client-lookup-status" class="text-xs font-medium text-gray-400">Aucun client lié.</p>
                            </div>
                        </div>

                        <div id="client-details" class="hidden border-t border-gray-100 bg-blue-50/50 px-6 py-4 text-sm text-blue-700">
                            <p class="font-black uppercase tracking-widest text-xs text-blue-600">Client trouvé</p>
                            <p id="client-details-name" class="mt-2 font-bold text-gray-800"></p>
                            <p id="client-details-phone" class="text-gray-600"></p>
                        </div>
                    </div>

                    {{-- PANIER --}}
                    <div class="overflow-hidden rounded-[1.5rem] border border-gray-200 bg-white shadow-[0_12px_45px_-20px_rgba(15,23,42,0.25)]">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <i data-lucide="shopping-cart" class="w-5 h-5 text-blue-600"></i>
                                <h3 class="font-bold text-gray-700">Panier</h3>
                            </div>
                            <button type="button" onclick="ajouterArticle()" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-black">
                                <i data-lucide="list-plus" class="w-4 h-4"></i> Ajout Manuel
                            </button>
                        </div>
                        
                        <div id="articles-container" class="space-y-4 p-6"></div>

                        <div id="empty-state" class="flex flex-col items-center justify-center p-12 text-center">
                            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-dashed border-gray-200 bg-gray-50 text-gray-300">
                                <i data-lucide="barcode" class="w-8 h-8"></i>
                            </div>
                            <p class="font-medium text-gray-400">Scannez un produit ou utilisez l’ajout manuel avec le code produit</p>
                        </div>
                    </div>

                </div>

                {{-- RÉSUMÉ CAISSE --}}
                <div class="space-y-6">
                    <div class="sticky top-6 rounded-[1.75rem] border border-gray-200 bg-white p-6 shadow-[0_12px_45px_-20px_rgba(15,23,42,0.25)]">
                        <h3 class="mb-6 border-b border-gray-100 pb-4 text-sm font-black uppercase tracking-widest text-gray-800">Résumé Caisse</h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 font-medium">Total articles</span>
                                <span id="total-articles" class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-black text-sm">0</span>
                            </div>
                            <div class="pt-4 border-t border-dashed">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-1 text-right">Montant total</p>
                                <p id="total-commande" class="text-right text-4xl font-black text-gray-900">0 KMF</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <button type="submit" class="flex w-full items-center justify-center gap-3 rounded-2xl bg-blue-600 px-4 py-4 text-lg font-black text-white shadow-lg shadow-blue-200 transition hover:bg-blue-700">
                                <i data-lucide="check-circle" class="w-6 h-6"></i> VALIDER
                            </button>
                            <a href="{{ route('commandes.index') }}" class="inline-block w-full py-2 text-center text-sm font-bold uppercase tracking-widest text-gray-400 transition hover:text-red-500">
                                Abandonner
                            </a>
                        </div>

                        <div id="dernier-article-widget" class="mt-8 pt-6 border-t border-gray-100 hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                            <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-3">Dernière détection</p>
                            <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <img id="dernier-article-image" src="" class="rounded-lg w-16 h-16 object-cover border bg-white">
                                <div class="overflow-hidden">
                                    <p id="dernier-article-nom" class="font-bold text-gray-800 truncate text-sm"></p>
                                    <span class="inline-block bg-yellow-400 text-[10px] font-black px-2 py-0.5 rounded mt-1 uppercase">Scan OK</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $clientsPayload = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'nom' => $client->nom,
                'prenom' => $client->prenom,
                'telephone' => $client->telephone,
            ];
        })->toArray();
    @endphp

    <script>
        let articleIndex = 0;
        const produits = @json($produitsFormates);
        const clientsData = @json($clientsPayload);
        
        // --- OPTIMISATION DU SCANNER ---
        const produitMap = new Map();
        produits.forEach(p => {
            if (p.code_barre) produitMap.set(String(p.code_barre).trim(), p);
        });

        let barcodeBuffer = "";
        let lastKeyTime = 0;
        let lastScannedCode = "";
        let lastScanTime = 0;
        let clientMode = 'standard';

        // Bip sonore de confirmation (Web Audio API)
        function playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1200, ctx.currentTime);
                gain.gain.setValueAtTime(0.1, ctx.currentTime);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.08);
            } catch (e) {}
        }

        // Ecouteur d'événement ultra-rapide optimisé pour Douchettes/Scanners
        window.addEventListener('keydown', (e) => {
            const currentTime = Date.now();
            
            // Un scanner saisit des caractères extrêmement vite (< 40ms entre chaque)
            if (currentTime - lastKeyTime > 100) {
                barcodeBuffer = "";
            }
            
            if (e.key === 'Enter') {
                if (barcodeBuffer.trim().length >= 2) {
                    e.preventDefault();
                    e.stopPropagation();
                    const codeScanne = barcodeBuffer.trim();
                    barcodeBuffer = "";
                    
                    // Anti-rebond (Empêche un double scan involontaire en moins de 300ms)
                    if (codeScanne === lastScannedCode && (currentTime - lastScanTime) < 300) {
                        return;
                    }
                    
                    lastScannedCode = codeScanne;
                    lastScanTime = currentTime;
                    handleScan(codeScanne);
                }
            } else if (e.key.length === 1) {
                barcodeBuffer += e.key;
            }
            
            lastKeyTime = currentTime;
        }, true);

        function handleScan(code) {
            // Recherche instantanée dans la Map (O(1))
            const produit = produitMap.get(code);
            if (produit) {
                playBeep();
                ajouterOuIncrementerProduit(produit);
            } else {
                console.warn('Produit non trouvé pour le code :', code);
            }
        }

        function ajouterOuIncrementerProduit(produit) {
            let ligneExistante = null;
            document.querySelectorAll('.produit-select').forEach(select => {
                if (select.value == produit.id) {
                    ligneExistante = select.closest('.article-item');
                }
            });

            if (ligneExistante) {
                const qtyInput = ligneExistante.querySelector('.quantite');
                const nouvelleQty = parseInt(qtyInput.value) + 1;
                if (nouvelleQty <= produit.stock) {
                    qtyInput.value = nouvelleQty;
                    updatePrix(ligneExistante.querySelector('.produit-select'));
                }
            } else {
                ajouterArticle(produit.id);
            }
        }

        function setClientMode(mode) {
            clientMode = mode;
            document.getElementById('client-standard-btn').classList.toggle('bg-blue-600', mode === 'standard');
            document.getElementById('client-standard-btn').classList.toggle('text-white', mode === 'standard');
            document.getElementById('client-standard-btn').classList.toggle('bg-white', mode !== 'standard');
            document.getElementById('client-standard-btn').classList.toggle('text-gray-700', mode !== 'standard');
            document.getElementById('client-standard-btn').classList.toggle('border-gray-200', mode !== 'standard');
            document.getElementById('client-fidele-btn').classList.toggle('bg-blue-600', mode === 'fidele');
            document.getElementById('client-fidele-btn').classList.toggle('text-white', mode === 'fidele');
            document.getElementById('client-fidele-btn').classList.toggle('bg-white', mode !== 'fidele');
            document.getElementById('client-fidele-btn').classList.toggle('text-gray-700', mode !== 'fidele');
            document.getElementById('client-fidele-btn').classList.toggle('border-gray-200', mode !== 'fidele');
            document.getElementById('client-mode-help').textContent = mode === 'fidele'
                ? 'Entrez le téléphone du client fidèle pour le retrouver ou le créer automatiquement.'
                : 'Mode standard : la commande peut être créée sans coordonnées client.';

            const phoneInput = document.getElementById('client_phone');
            if (mode === 'fidele') {
                phoneInput.removeAttribute('disabled');
                phoneInput.required = true;
            } else {
                phoneInput.removeAttribute('required');
                phoneInput.required = false;
                phoneInput.value = '';
                document.getElementById('client-lookup-status').textContent = 'Aucun client lié.';
                hideClientDetails();
                document.getElementById('client_id').value = '';
            }
        }

        function lookupClientByPhone(phone) {
            const cleanedPhone = phone.replace(/\D/g, '');
            const status = document.getElementById('client-lookup-status');
            const details = document.getElementById('client-details');
            const idField = document.getElementById('client_id');

            if (!cleanedPhone) {
                status.textContent = 'Aucun client lié.';
                hideClientDetails();
                idField.value = '';
                return;
            }

            const match = clientsData.find(client => client.telephone === cleanedPhone);
            if (match) {
                status.textContent = 'Client trouvé dans la base.';
                document.getElementById('client-details-name').textContent = `${match.nom} ${match.prenom}`;
                document.getElementById('client-details-phone').textContent = `Téléphone : ${match.telephone}`;
                details.classList.remove('hidden');
                idField.value = match.id;
            } else {
                status.textContent = 'Nouveau client : il sera créé automatiquement si vous poursuivez.';
                hideClientDetails();
                idField.value = '';
            }
        }

        function hideClientDetails() {
            document.getElementById('client-details').classList.add('hidden');
        }

        setClientMode('standard');

        function ajouterArticle(produitId = "") {
            document.getElementById('empty-state').classList.add('hidden');
            const container = document.getElementById('articles-container');
            const html = `
                <div class="article-item group rounded-[1.25rem] border border-gray-100 bg-white p-4 shadow-sm transition hover:border-blue-200" data-index="${articleIndex}">
                    <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
                        <div class="md:col-span-5">
                            <label class="mb-1 block text-[10px] font-black uppercase tracking-widest text-gray-400">Code produit</label>
                            <div class="flex gap-2">
                                <input type="text" class="code-produit block w-full rounded-xl border border-gray-100 bg-gray-50 px-3 py-2.5 text-sm font-bold text-gray-700 focus:border-blue-500 focus:outline-none" placeholder="Saisir le code" onkeydown="if(event.key === 'Enter'){event.preventDefault(); validerCodeProduit(this);}">
                                <button type="button" onclick="validerCodeProduit(this)" class="rounded-xl bg-blue-600 px-3 py-2 text-sm font-black text-white transition hover:bg-blue-700">OK</button>
                            </div>
                            <select name="items[${articleIndex}][produit_id]" class="produit-select hidden" required onchange="updatePrix(this)">
                                <option value="">Choisir...</option>
                                ${produits.map(p => `<option value="${p.id}" ${p.id == produitId ? 'selected' : ''} data-prix="${p.prix}" data-stock="${p.stock}" data-image="${p.image}" data-code="${p.code_barre}" data-nom="${p.nom}">${p.nom} (Stock: ${p.stock})</option>`).join('')}
                            </select>
                            <p class="mt-2 text-[11px] font-medium text-gray-400">Saisissez le code du produit pour l’ajouter manuellement.</p>
                        </div>
                        <div class="text-center md:col-span-2">
                            <label class="mb-1 block text-center text-[10px] font-black uppercase tracking-widest text-gray-400">Prix Unit.</label>
                            <input type="text" class="prix-unitaire block w-full border-none bg-transparent p-0 text-center font-bold text-gray-600" readonly value="0 KMF">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-center text-[10px] font-black uppercase tracking-widest text-gray-400">Quantité</label>
                            <div class="flex items-center">
                                <input type="number" name="items[${articleIndex}][quantite]" class="quantite block w-full rounded-lg border border-gray-100 bg-gray-50 p-2 text-center font-black" min="1" value="1" required onchange="calculerTotal()">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-right text-[10px] font-black uppercase tracking-widest text-gray-400">Total Ligne</label>
                            <input type="text" class="total-ligne block w-full border-none bg-transparent p-0 text-right font-black text-blue-600" readonly value="0 KMF">
                        </div>
                        <div class="flex justify-center md:col-span-1">
                            <button type="button" onclick="retirerArticle(this)" class="rounded-xl p-2 text-gray-300 transition hover:text-red-500">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            
            container.insertAdjacentHTML('beforeend', html);
            lucide.createIcons();
            if(produitId) updatePrix(container.querySelector(`[data-index="${articleIndex}"] .produit-select`));
            articleIndex++;
        }

        function retirerArticle(btn) {
            btn.closest('.article-item').remove();
            if(document.querySelectorAll('.article-item').length === 0) {
                document.getElementById('empty-state').classList.remove('hidden');
            }
            calculerTotal();
        }

        function validerCodeProduit(trigger) {
            const item = trigger.closest('.article-item');
            const input = item.querySelector('.code-produit');
            const select = item.querySelector('.produit-select');
            const code = input.value.trim();

            if (!code) return;

            const option = Array.from(select.options).find(opt => opt.dataset.code === code || opt.textContent.toLowerCase().includes(code.toLowerCase()));

            if (option) {
                select.value = option.value;
                updatePrix(select);
                input.value = code;
            } else {
                input.classList.add('border-red-300');
                input.placeholder = 'Code introuvable';
                setTimeout(() => input.classList.remove('border-red-300'), 1200);
            }
        }

        function updatePrix(select) {
            const option = select.selectedOptions[0];
            const item = select.closest('.article-item');
            if (option && option.value) {
                const prix = parseFloat(option.dataset.prix);
                item.querySelector('.prix-unitaire').value = prix.toLocaleString() + ' KMF';
                item.querySelector('.quantite').max = option.dataset.stock;
                
                const codeInput = item.querySelector('.code-produit');
                if (codeInput) {
                    codeInput.value = option.dataset.code || codeInput.value;
                }

                // Widget Update
                document.getElementById('dernier-article-nom').textContent = option.dataset.nom || option.textContent.split('(')[0];
                document.getElementById('dernier-article-image').src = option.dataset.image;
                document.getElementById('dernier-article-widget').classList.remove('hidden');
                
                envoyerArticleEnLive(option.value, item.querySelector('.quantite').value);
            }
            calculerTotal();
        }

        function calculerTotal() {
            let totalQty = 0; let totalCmd = 0;
            document.querySelectorAll('.article-item').forEach(item => {
                const select = item.querySelector('.produit-select');
                const qtyInput = item.querySelector('.quantite');
                if (select.value && qtyInput.value) {
                    const prix = parseFloat(select.selectedOptions[0].dataset.prix);
                    const qty = parseInt(qtyInput.value);
                    const subtotal = prix * qty;
                    item.querySelector('.total-ligne').value = subtotal.toLocaleString() + ' KMF';
                    totalQty += qty; totalCmd += subtotal;
                }
            });
            document.getElementById('total-articles').textContent = totalQty;
            document.getElementById('total-commande').textContent = totalCmd.toLocaleString() + ' KMF';
        }

        function envoyerArticleEnLive(produitId, quantite) {
            fetch(`/affichage-client-temp/${produitId}/article`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ produit_id: produitId, quantite: quantite })
            }).catch(err => console.log('Offline'));
        }

        document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });
    </script>
</x-app-layout>