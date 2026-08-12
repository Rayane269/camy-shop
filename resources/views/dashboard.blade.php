<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight uppercase tracking-tighter">
                Tableau de <span class="text-blue-600">Bord</span>
            </h2>
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs font-black text-gray-500 uppercase tracking-widest">Live System</span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto space-y-8">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- CARTE 1 : COMMANDES DU MOIS UNIQUEMENT --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute -right-2 -top-2 bg-blue-50 text-blue-100 rounded-full p-8 transition-transform group-hover:scale-110">
                    <i data-lucide="shopping-cart" class="w-12 h-12"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Commandes du mois</p>
                    <h2 class="text-4xl font-black text-gray-800 leading-none">{{ $stats['commandes_total'] }}</h2>
                    <div class="mt-4 flex items-center text-[10px] font-bold text-emerald-600 bg-emerald-50 w-max px-2 py-1 rounded-lg">
                        <i data-lucide="calendar" class="w-3 h-3 mr-1"></i> Mois en cours
                    </div>
                </div>
            </div>

            {{-- CARTE 2 : EN ATTENTE --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute -right-2 -top-2 bg-orange-50 text-orange-100 rounded-full p-8 transition-transform group-hover:scale-110">
                    <i data-lucide="clock" class="w-12 h-12"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">En Attente</p>
                    <h2 class="text-4xl font-black text-orange-600 leading-none">{{ $stats['commandes_en_attente'] }}</h2>
                    <div class="mt-4 flex items-center text-[10px] font-bold text-orange-600 bg-orange-50 w-max px-2 py-1 rounded-lg">
                        <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i> À traiter
                    </div>
                </div>
            </div>

            {{-- CARTE 3 : COMPTEUR DE RETOURS SAV DIRECT --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-amber-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute -right-2 -top-2 bg-amber-50 text-amber-200 rounded-full p-8 transition-transform group-hover:scale-110">
                    <i data-lucide="undo-2" class="w-12 h-12"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">Suivi Retours SAV</p>
                    <h2 class="text-4xl font-black text-amber-600 leading-none">
                        {{ \App\Models\Commande::where('total', 0)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count() }}
                    </h2>
                    <div class="mt-4 flex items-center text-[10px] font-bold text-amber-700 bg-amber-50 w-max px-2 py-1 rounded-lg">
                        <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i> Remboursements du mois
                    </div>
                </div>
            </div>

            {{-- CARTE 4 : REVENUS DU MOIS RÉELS (NETS D'ANNULATIONS) --}}
            <div class="bg-gray-900 p-6 rounded-3xl shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all border border-gray-800">
                <div class="absolute -right-2 -top-2 bg-gray-800 text-gray-700 rounded-full p-8">
                    <i data-lucide="trending-up" class="w-12 h-12"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Revenus nets du mois</p>
                    <h2 class="text-3xl font-black text-white leading-none">
                        {{ number_format($stats['revenus_mois'], 0, '', ' ') }} <span class="text-sm text-gray-400">KMF</span>
                    </h2>
                    <div class="mt-4 flex items-center text-[10px] font-bold text-green-400 bg-gray-800 w-max px-2 py-1 rounded-lg border border-gray-700">
                        <i data-lucide="shield-check" class="w-3 h-3 mr-1"></i> Chiffre d'Affaire Réel
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLEAU DES COMMANDES RÉCENTES HARMONISÉ --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-[2rem] overflow-hidden">
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-600 rounded-lg text-white">
                        <i data-lucide="list" class="w-5 h-5"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-800 uppercase tracking-tighter">Commandes récentes</h3>
                </div>
                <a href="{{ route('commandes.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-black transition shadow-lg shadow-gray-200">
                    <i data-lucide="plus" class="w-4 h-4"></i> Créer
                </a>
            </div>
            
            <div class="overflow-x-auto">
                @if ($commandes_recentes->count() > 0)
                    <table class="min-w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b">Numéro</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b">Client</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b text-center">Total</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b text-center">Paiement</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($commandes_recentes as $commande)
                                <tr class="group hover:bg-blue-50/30 transition {{ $commande->a_un_retour === 'total' ? 'bg-amber-50/20' : '' }}">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-gray-800">#{{ $commande->numero_commande }}</span>
                                            
                                            {{-- Badges icônes dynamiques sur le tableau de bord --}}
                                            @if($commande->a_un_retour === 'total')
                                                <span class="p-0.5 bg-amber-100 text-amber-700 rounded border border-amber-200" title="Remboursé intégralement">
                                                    <i data-lucide="undo-2" class="w-3 h-3"></i>
                                                </span>
                                            @elseif($commande->a_un_retour === 'partiel')
                                                <span class="p-0.5 bg-blue-100 text-blue-700 rounded border border-blue-200" title="Retour partiel d'articles">
                                                    <i data-lucide="undo-2" class="w-3 h-3"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-400 font-bold mt-0.5">{{ $commande->date_commande->format('d/m/Y H:i') }}</p>
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black text-xs">
                                                {{ substr($commande->client->nom, 0, 1) }}
                                            </div>
                                            <span class="font-bold text-gray-700">{{ $commande->client->nom }} {{ $commande->client->prenom }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-center">
                                        <span class="font-black {{ $commande->total == 0 ? 'text-amber-600 line-through' : 'text-gray-900' }}">
                                            {{ number_format($commande->total, 0, '', ' ') }} KMF
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 text-center">
                                        {{-- Harmonisation complète des badges de règlements avec le SAV --}}
                                        @if($commande->a_un_retour === 'total')
                                            <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-black uppercase rounded border border-amber-200">
                                                Remboursé
                                            </span>
                                        @elseif($commande->a_un_retour === 'partiel')
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] font-black uppercase text-blue-600">Retour Partiel</span>
                                                <span class="text-[9px] text-gray-400 font-bold italic">{{ $commande->paiement->mode_paiement_label ?? '' }}</span>
                                            </div>
                                        @elseif ($commande->paiement)
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] font-black uppercase text-{{ $commande->paiement->statut_paiement == 'paye' ? 'green' : 'amber' }}-600">
                                                    {{ $commande->paiement->statut_paiement_label }}
                                                </span>
                                                <span class="text-[9px] text-gray-400 font-bold italic">{{ $commande->paiement->mode_paiement_label }}</span>
                                            </div>
                                        @else
                                            <span class="px-2 py-1 bg-red-50 text-red-500 text-[10px] font-black uppercase rounded border border-red-100">
                                                Non payé
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <a href="{{ route('commandes.show', $commande) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-white border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-200 rounded-lg shadow-sm transition">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-8 py-12 text-center text-gray-400 italic">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                        <p>Aucune transaction récente à afficher.</p>
                    </div>
                @endif
            </div>
            <div class="bg-gray-50 px-8 py-4 text-center border-t border-gray-100">
                <a href="{{ route('commandes.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-blue-600 transition">Voir tous les rapports</a>
            </div>
        </div>
    </div>
</x-app-layout>