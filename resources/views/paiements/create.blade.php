<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                Finaliser le <span class="text-green-600">Règlement</span>
            </h2>
            <div class="flex items-center gap-2 text-sm text-gray-500 font-medium">
                <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                Paiement Securisé
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 px-4">
        
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl flex items-center gap-3 font-bold">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Message d'erreur dynamique géré en JS --}}
            <div id="js_error_api" class="hidden mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3 font-bold shadow-sm">
                <i data-lucide="alert-octagon" class="w-5 h-5 text-red-600"></i>
                <span id="js_error_message"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                
                {{-- Résumé du montant (Focus visuel) --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-blue-600 rounded-3xl p-8 text-white shadow-xl shadow-blue-100 relative overflow-hidden">
                        <i data-lucide="wallet" class="absolute -right-4 -bottom-4 w-32 h-32 text-blue-500 opacity-50"></i>
                        <p class="text-blue-100 font-bold uppercase tracking-widest text-xs mb-2">Total à encaisser</p>
                        <h3 class="text-4xl font-black mb-6">{{ number_format($commande->total, 0, '', ' ') }} <span class="text-lg">KMF</span></h3>
                        
                        <div class="space-y-3 relative z-10">
                            <div class="flex items-center gap-3 text-sm">
                                <i data-lucide="user" class="w-4 h-4 text-blue-200"></i>
                                <span class="font-medium">{{ $commande->client->nom }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <i data-lucide="hash" class="w-4 h-4 text-blue-200"></i>
                                <span class="font-medium">Commande #{{ $commande->id }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 flex gap-3 italic text-sm text-amber-800">
                        <i data-lucide="info" class="w-5 h-5 text-amber-500 shrink-0"></i>
                        Vérifiez bien le montant avant de valider la transaction.
                    </div>
                </div>

                {{-- Formulaire de paiement --}}
                <div class="md:col-span-3">
                    <div class="bg-white shadow-sm border border-gray-100 rounded-3xl p-8">
                        <form method="POST" id="formulaire_paiement" action="{{ route('paiements.store', $commande) }}" class="space-y-6">
                            @csrf
                            
                            <div>
                                <label for="mode_paiement" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
                                    Méthode de paiement <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 gap-3">
                                    <select name="mode_paiement" id="mode_paiement"
                                        class="block w-full rounded-xl border-gray-200 py-4 font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('mode_paiement') border-red-500 @enderror"
                                        required>
                                        <option value="">Sélectionner le mode...</option>
                                        @foreach (\App\Models\Paiement::MODES_PAIEMENT as $key => $label)
                                            <option value="{{ $key }}" {{ old('mode_paiement') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('mode_paiement')
                                    <p class="text-xs text-red-600 mt-2 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- BLOC CALCULATEUR AVEC ETAT DE VALIDATION COULEUR --}}
                            <div id="bloc_monnaie" class="hidden p-5 bg-gray-50 border border-gray-200 rounded-2xl space-y-4 transition-all duration-200">
                                <div class="flex items-center gap-2 text-xs font-black text-blue-600 uppercase tracking-widest">
                                    <i data-lucide="calculator" class="w-4 h-4"></i>
                                    Calcul de la monnaie (Espèces)
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-2">Montant reçu du client</label>
                                        <input type="number" id="montant_recu" placeholder="Ex: 5000" 
                                            class="block w-full rounded-xl border-gray-200 py-3 font-bold text-gray-700 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label id="label_rendu_reste" class="block text-xs font-bold text-gray-500 mb-2">Monnaie à rendre</label>
                                        <div id="container_rendu" class="w-full bg-white border border-gray-200 rounded-xl py-3 px-4 font-black text-xl text-green-600 flex items-center justify-between">
                                            <span id="monnaie_rendu">0</span>
                                            <span class="text-xs font-bold text-gray-400">KMF</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="reference_transaction" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3">
                                    Référence de transaction
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                        <i data-lucide="file-key" class="w-5 h-5"></i>
                                    </span>
                                    <input type="text" name="reference_transaction" id="reference_transaction"
                                        placeholder="Ex: Ref mobile money, chèque..."
                                        class="block w-full pl-12 rounded-xl border-gray-200 py-4 font-medium focus:ring-blue-500 @error('reference_transaction') border-red-500 @enderror"
                                        value="{{ old('reference_transaction') }}">
                                </div>
                                @error('reference_transaction')
                                    <p class="text-xs text-red-600 mt-2 font-bold">{{ $message }}</p>
                                @enderror
                            </div>

                            <input type="hidden" name="montant" id="total_commande" value="{{ $commande->total }}">

                            <div class="pt-6 space-y-3">
                                <button type="submit" id="btn_encaisser"
                                        class="w-full bg-green-600 text-white py-5 rounded-2xl font-black text-xl hover:bg-green-700 transition shadow-lg shadow-green-100 flex items-center justify-center gap-3">
                                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                                    ENCAISSER MAINTENANT
                                </button>
                                
                                <a href="{{ route('commandes.show', $commande) }}"
                                   class="block text-center w-full py-2 text-gray-400 font-bold text-sm hover:text-gray-600 transition uppercase tracking-widest">
                                    Annuler et revenir
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
      
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            const form = document.getElementById('formulaire_paiement');
            const modePaiement = document.getElementById('mode_paiement');
            const blocMonnaie = document.getElementById('bloc_monnaie');
            const montantRecu = document.getElementById('montant_recu');
            const monnaieRendu = document.getElementById('monnaie_rendu');
            const labelRenduReste = document.getElementById('label_rendu_reste');
            const containerRendu = document.getElementById('container_rendu');
            const btnEncaisser = document.getElementById('btn_encaisser');
            const errorApi = document.getElementById('js_error_api');
            const errorMessage = document.getElementById('js_error_message');
            
            // Robust parsing of the total (strip spaces/thousands separators)
            const totalRaw = document.getElementById('total_commande').value;
            const totalCommande = Number(String(totalRaw).replace(/[^0-9.-]+/g, '')) || 0;

            function initialiserCalculateur() {
                blocMonnaie.classList.add('hidden');
                errorApi.classList.add('hidden');
                montantRecu.value = '';
                monnaieRendu.innerText = '0';
                containerRendu.className = "w-full bg-white border border-gray-200 rounded-xl py-3 px-4 font-black text-xl text-green-600 flex items-center justify-between";
                labelRenduReste.innerText = "Monnaie à rendre";
                btnEncaisser.disabled = false;
                btnEncaisser.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                btnEncaisser.classList.add('bg-green-600', 'hover:bg-green-700');
            }

            modePaiement.addEventListener('change', () => {
                const mode = modePaiement.value.toUpperCase();
                if (mode === 'ESPECES' || mode === 'ESPECE') {
                    initialiserCalculateur();
                    blocMonnaie.classList.remove('hidden');
                    montantRecu.focus();
                } else {
                    initialiserCalculateur();
                }
            });

            montantRecu.addEventListener('input', () => {
                const recuRaw = montantRecu.value.trim();
                
                if (recuRaw === '') {
                    monnaieRendu.innerText = '0';
                    labelRenduReste.innerText = "Monnaie à rendre";
                    containerRendu.className = "w-full bg-white border border-gray-200 rounded-xl py-3 px-4 font-black text-xl text-green-600 flex items-center justify-between";
                    errorApi.classList.add('hidden');
                    return;
                }

                const recu = parseFloat(recuRaw) || 0;

                if (recu < totalCommande) {
                    // CAS INSUFFISANT : Le client doit rajouter de l'argent
                    const manque = totalCommande - recu;
                    monnaieRendu.innerText = manque.toLocaleString('fr-FR');
                    labelRenduReste.innerText = "Reste à payer !";
                    
                    // Design d'alerte rouge
                    containerRendu.className = "w-full bg-red-50 border border-red-300 rounded-xl py-3 px-4 font-black text-xl text-red-600 flex items-center justify-between";
                    
                    // Affichage du bandeau d'erreur en haut
                    errorMessage.innerText = `Le montant saisi est insuffisant. Il manque ${manque.toLocaleString('fr-FR')} KMF pour valider cet achat.`;
                    errorApi.classList.remove('hidden');
                } else {
                    // CAS CORRECT : Monnaie à rendre normale
                    const rendu = recu - totalCommande;
                    monnaieRendu.innerText = rendu.toLocaleString('fr-FR');
                    labelRenduReste.innerText = "Monnaie à rendre";
                    
                    // Design de succès vert
                    containerRendu.className = "w-full bg-green-50 border border-green-300 rounded-xl py-3 px-4 font-black text-xl text-green-600 flex items-center justify-between";
                    errorApi.classList.add('hidden');
                }
            });

            // BLOQUE LA VALIDATION DU FORMULAIRE SI LA SOMME Saisie EST PAS ASSEZ HAUTE
            form.addEventListener('submit', (e) => {
                const mode = modePaiement.value.toUpperCase();
                if (mode === 'ESPECES' || mode === 'ESPECE') {
                    const recu = parseFloat(montantRecu.value) || 0;
                    
                    if (recu < totalCommande) {
                        e.preventDefault(); // Annule l'envoi vers le serveur
                        
                        const manque = totalCommande - recu;
                        errorMessage.innerText = `Blocage sécurité : Versement insuffisant ! Demandez au client de rajouter ${manque.toLocaleString('fr-FR')} KMF.`;
                        errorApi.classList.remove('hidden');
                        montantRecu.focus();
                    }
                }
            });
        });
    </script>
</x-app-layout>