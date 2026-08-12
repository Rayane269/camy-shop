<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4">
            
            @php
                $estÉligible = $commande->date_commande->isAfter(now()->subHours(48));
                $heuresEcoulees = $commande->date_commande->diffInHours(now());
            @endphp

            <div class="bg-white shadow-2xl rounded-[3rem] border border-gray-100 overflow-hidden p-8 sm:p-12">
                
                <div class="flex flex-col md:flex-row justify-between items-start mb-12">
                    <div>
                        <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest italic">Service Retour</span>
                        <h2 class="text-4xl font-black text-gray-900 mt-4 tracking-tighter uppercase">Ticket #{{ $commande->numero_commande }}</h2>
                        <p class="text-gray-400 font-bold text-xs uppercase mt-2">Client : {{ $commande->client->nom ?? 'Passage' }}</p>
                        <p class="text-[10px] font-black uppercase mt-1 {{ $estÉligible ? 'text-green-600' : 'text-red-600' }}">
                            Acheté le : {{ $commande->date_commande->format('d/m/Y à H:i') }} (Il y a {{ $heuresEcoulees }}h)
                        </p>
                    </div>
                    <div class="bg-gray-900 text-white p-8 rounded-[2.5rem] shadow-xl text-center min-w-[180px]">
                        <div class="text-[10px] font-bold uppercase opacity-50 tracking-widest">Total Payé</div>
                        <div class="text-2xl font-black">{{ number_format($commande->total, 0, '', ' ') }} KMF</div>
                    </div>
                </div>

                @if(!$estÉligible)
                    <div class="p-8 bg-red-50 border-2 border-red-200 rounded-[2.5rem] text-center my-6">
                        <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="clock-alert" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-xl font-black text-red-800 uppercase tracking-tight">Délai de retour dépassé !</h3>
                        <p class="text-sm text-red-700 font-medium mt-2 max-w-md mx-auto">
                            Ce ticket a été édité il y a <strong>{{ $heuresEcoulees }} heures</strong>. Conformément à la politique de l'établissement, aucun retour n'est accepté après le délai légal de 48 heures.
                        </p>
                        <div class="mt-8">
                            <a href="{{ route('commandes.index') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-gray-900 text-white font-black text-xs rounded-2xl uppercase tracking-wider hover:bg-black transition">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i> Retourner à la liste
                            </a>
                        </div>
                    </div>
                @else
                    {{-- FORMULAIRE --}}
                    <form action="{{ route('commandes.effectuer-retour', $commande) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            @foreach($commande->items as $index => $item)
                            {{-- Chaque ligne a l'attribut data-prix stocké proprement --}}
                            <div class="ligne-retour flex items-center justify-between p-6 bg-gray-50 rounded-[2.5rem] border border-gray-100 hover:border-red-200 transition-all group" 
                                 data-prix="{{ $item->prix_unitaire }}">
                                
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-inner font-black text-gray-900 text-lg group-hover:scale-110 transition-transform">
                                        {{ $item->quantite }}
                                    </div>
                                    <div>
                                        <h4 class="font-black text-gray-900 uppercase text-sm group-hover:text-red-600 transition-colors">{{ $item->produit->nom }}</h4>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PU: {{ number_format($item->prix_unitaire, 0, '', ' ') }} KMF</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <div class="text-right">
                                        <label class="block text-[9px] font-black text-red-500 uppercase mb-2">Qté à rendre</label>
                                        {{-- Input avec la classe "input-quantite" bien présente --}}
                                        <input type="number" name="items[{{ $index }}][quantite_retour]" 
                                            min="0" max="{{ $item->quantite }}" value="0"
                                            class="input-quantite w-24 border-none bg-white rounded-2xl font-black text-center text-gray-900 focus:ring-2 focus:ring-red-500 shadow-inner py-3 text-lg">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- ZONE D'AFFICHAGE DE LA MONNAIE DYNAMIQUE --}}
                        <div class="mt-8 p-6 bg-red-50 rounded-[2rem] border border-red-100 flex justify-between items-center shadow-inner">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-red-600 text-white rounded-xl shadow-md">
                                    <i data-lucide="coins" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-red-800 uppercase tracking-wider">Monnaie à rendre au client</h4>
                                    <p class="text-[10px] font-bold text-red-400 uppercase tracking-tight mt-0.5">Calculé automatiquement en direct</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span id="montantRemboursement" class="text-3xl font-black text-red-600 tracking-tight">0</span>
                                <span class="text-base font-black text-red-600 ml-1">KMF</span>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('commandes.index') }}" class="flex-1 py-5 bg-gray-100 text-gray-400 rounded-[2rem] font-black uppercase text-[10px] tracking-widest text-center">Annuler l'opération</a>
                            <button type="submit" class="flex-[2] py-5 bg-red-600 text-white rounded-[2rem] font-black uppercase text-[10px] tracking-widest shadow-2xl shadow-red-100 hover:bg-gray-900 transition-all flex items-center justify-center gap-3">
                                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                                Valider le remboursement
                            </button>
                        </div>
                    </form>
                @endif
                
            </div>
        </div>
    </div>

    {{-- LE SCRIPT DE CALCUL SÉCURISÉ --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            const affichageMontant = document.getElementById('montantRemboursement');
            const inputsQuantite = document.querySelectorAll('.input-quantite');

            function calculerTotalRemboursement() {
                let totalRemboursement = 0;

                document.querySelectorAll('.ligne-retour').forEach(ligne => {
                    const prixUnitaire = parseFloat(ligne.getAttribute('data-prix')) || 0;
                    const input = ligne.querySelector('.input-quantite');
                    let quantiteARendre = parseInt(input.value) || 0;

                    // Sécurité : évite les valeurs négatives ou supérieures au max acheté
                    const maxAutorise = parseInt(input.getAttribute('max')) || 0;
                    if (quantiteARendre > maxAutorise) {
                        quantiteARendre = maxAutorise;
                        input.value = maxAutorise;
                    }
                    if (quantiteARendre < 0) {
                        quantiteARendre = 0;
                        input.value = 0;
                    }

                    totalRemboursement += prixUnitaire * quantiteARendre;
                });

                // Met à jour l'affichage avec l'espace pour les milliers
                if (affichageMontant) {
                    affichageMontant.textContent = totalRemboursement.toLocaleString('fr-FR').replace(/\s/g, ' ');
                }
            }

            // Écoute les changements sur chaque champ (quand on tape ou clique sur les flèches)
            inputsQuantite.forEach(input => {
                input.addEventListener('input', calculerTotalRemboursement);
                input.addEventListener('change', calculerTotalRemboursement);
            });
        });
    </script>
</x-app-layout>