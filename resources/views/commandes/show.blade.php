<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight uppercase tracking-tighter">
                Détail de la commande <span class="text-blue-600">#{{ $commande->numero_commande ?? $commande->id }}</span>
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('commandes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Retour à la liste
                </a>
                @if($commande->statut === 'en_attente')
                    <a href="{{ route('commandes.edit', $commande) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 transition">
                        <i data-lucide="edit" class="w-4 h-4"></i> Modifier la commande
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">
        
        {{-- Flash messages pour les notifications --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3 font-bold">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3 font-bold">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- ALERTE VISUELLE : COMMANDE INTEGRALEMENT REMBOURSÉE --}}
        @if($commande->a_un_retour === 'total')
            <div class="mb-6 p-6 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-4 shadow-sm">
                <div class="p-3 bg-amber-600 text-white rounded-xl">
                    <i data-lucide="undo-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-md font-black text-amber-800 uppercase tracking-tight">Commande Remboursée / Annulée</h3>
                    <p class="text-sm text-amber-700 font-medium mt-0.5">Tous les articles de ce ticket ont été restitués au stock. Le montant a été rendu au client.</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- COLONNE DE GAUCHE : CONTENU DE LA COMMANDE --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Informations Client --}}
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
                        <h3 class="font-bold text-gray-700">Informations Client</h3>
                    </div>
                    <div class="p-6 grid md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Coordonnées</p>
                            <p class="font-bold text-lg text-gray-900">{{ $commande->client->nom }} {{ $commande->client->prenom }}</p>
                            <p class="text-gray-600 flex items-center gap-2 mt-1"><i data-lucide="mail" class="w-4 h-4"></i> {{ $commande->client->email }}</p>
                            <p class="text-gray-600 flex items-center gap-2 mt-1"><i data-lucide="phone" class="w-4 h-4"></i> {{ $commande->client->telephone }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Adresse de livraison</p>
                            <div class="text-gray-700 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                <p class="font-medium">{{ $commande->client->adresse }}</p>
                                <p>{{ $commande->client->code_postal }} {{ $commande->client->ville }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Articles commandés --}}
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <i data-lucide="shopping-bag" class="w-5 h-5 text-blue-600"></i>
                            <h3 class="font-bold text-gray-700">Articles commandés</h3>
                        </div>
                        <span class="text-sm font-medium text-gray-500">{{ $commande->items->count() }} produit(s)</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">Produit</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Prix Unitaire</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">Quantité actuelle</th>
                                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-right">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($commande->items as $item)
                                <tr class="hover:bg-gray-50/50 transition {{ $item->quantite == 0 ? 'bg-red-50/40 opacity-60' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-gray-800">{{ $item->produit->nom }}</p>
                                            @if($item->quantite == 0)
                                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black uppercase rounded">Rendu</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400">Ref: #PROD-{{ $item->produit->id }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-gray-600">
                                        {{ number_format($item->prix_unitaire, 0, '', ' ') }} KMF
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-block px-3 py-1 {{ $item->quantite == 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }} rounded-full font-bold text-sm">
                                            × {{ $item->quantite }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900">
                                        {{ number_format($item->total, 0, '', ' ') }} KMF
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-blue-50/30">
                                <tr>
                                    <td colspan="3" class="px-6 py-5 text-right font-bold text-gray-600 uppercase tracking-widest text-sm">Total final encaissé</td>
                                    <td class="px-6 py-5 text-right text-2xl font-black text-blue-600">
                                        {{ number_format($commande->total, 0, '', ' ') }} KMF
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($commande->notes)
                <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl flex gap-3">
                    <i data-lucide="sticky-note" class="w-5 h-5 text-amber-500"></i>
                    <div>
                        <p class="text-sm font-bold text-amber-800 uppercase mb-1">Note du caissier</p>
                        <p class="text-amber-900">{{ $commande->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- COLONNE DE DROITE --}}
            <div class="space-y-6">
                
                @if(!$commande->paiement)
                    {{-- ÉTAT 1 : LA COMMANDE N'EST PAS ENCORE ENREGISTRÉE COMME PAYÉE --}}
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-gray-50">
                            <i data-lucide="shield-alert" class="w-5 h-5 text-amber-500"></i>
                            <h3 class="font-bold text-gray-700">Actions de Caisse</h3>
                        </div>

                        <a href="{{ route('paiements.create', $commande) }}" class="flex items-center justify-center gap-2 w-full bg-green-600 text-white py-3.5 rounded-lg font-black text-md hover:bg-green-700 shadow-lg shadow-green-100 transition">
                            <i data-lucide="banknote" class="w-5 h-5"></i> FINALISER LE PAIEMENT
                        </a>
                    </div>
                @else
                    {{-- ÉTAT 2 : LE PAIEMENT EST ENREGISTRÉ --}}
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                            <i data-lucide="credit-card" class="w-5 h-5 text-blue-600"></i>
                            <h3 class="font-bold text-gray-700">Suivi du Règlement</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="text-gray-500 text-sm">Montant à l'Achat</span>
                                    <span class="font-black text-gray-800">{{ number_format($commande->paiement->montant, 0, '', ' ') }} KMF</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-gray-50 pb-2">
                                    <span class="text-gray-500 text-sm">Méthode utilisée</span>
                                    <span class="font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded italic text-sm">{{ $commande->paiement->mode_paiement_label }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 text-sm">État Logistique</span>
                                    @if($commande->a_un_retour === 'total')
                                        <span class="flex items-center gap-1 font-bold text-amber-600">
                                            <i data-lucide="undo-2" class="w-4 h-4"></i> Intégralement Remboursé
                                        </span>
                                    @elseif($commande->a_un_retour === 'partiel')
                                        <span class="flex items-center gap-1 font-bold text-blue-600">
                                            <i data-lucide="git-branch" class="w-4 h-4"></i> Retour Partiel Archivé
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1 font-bold text-green-600">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i> Payé (Intact)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BLOC LOGISTIQUE DU RETOUR (Invisible si déjà totalement remboursé) --}}
                    @if($commande->a_un_retour !== 'total' && $commande->date_commande->isAfter(now()->subHours(48)))
                        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <i data-lucide="refresh-cw" class="w-5 h-5 text-red-600"></i>
                                <h3 class="font-bold text-gray-700 text-red-600">Service Après-Vente</h3>
                            </div>
                            <div class="p-6">
                                <p class="text-xs text-gray-400 font-medium mb-4 leading-relaxed uppercase">Le client souhaite restituer un ou plusieurs articles ? Cliquez ci-dessous pour ouvrir le module de calcul.</p>
                                <a href="{{ route('commandes.retour', $commande->numero_commande) }}" class="flex items-center justify-center gap-2 w-full bg-red-50 text-red-600 border border-red-200 py-3 rounded-lg font-black hover:bg-red-600 hover:text-white transition shadow-sm">
                                    <i data-lucide="receipt text" class="w-4 h-4"></i> EFFECTUER UN RETOUR / MONNAIE
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- BLOC IMPRESSION UNIQUE --}}
                    @if($commande->total > 0)
                        <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <i data-lucide="printer" class="w-5 h-5 text-blue-600"></i>
                                <h3 class="font-bold text-gray-700">Actions d'Impression</h3>
                            </div>
                            <div class="p-6 space-y-3">
                                <a href="{{ route('commandes.imprimerPhysique', $commande) }}" class="flex items-center justify-center gap-2 w-full bg-green-600 text-white py-3 rounded-lg font-black hover:bg-green-700 shadow-lg shadow-green-100 transition">
                                    <i data-lucide="printer" class="w-5 h-5"></i> RE-IMPRIMER SUR L'EPSON
                                </a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</x-app-layout>