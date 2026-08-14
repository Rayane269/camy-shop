<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight uppercase tracking-tighter">
                Gestion des <span class="text-blue-600">Commandes</span>
            </h2>
            <div class="flex items-center gap-3">
                {{-- BOUTON CLÔTURER LA JOURNÉE --}}
                <button type="button" onclick="openClotureModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white text-sm font-black rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 uppercase tracking-wider">
                    <i data-lucide="lock" class="w-4 h-4"></i> Clôturer la Journée
                </button>

                {{-- BOUTON NOUVELLE COMMANDE --}}
                <a href="{{ route('commandes.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-black rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i> NOUVELLE COMMANDE
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto space-y-6">
        
        {{-- Flash messages de statut pour la clôture --}}
        @if (session('success'))
            <div class="mb-2 flex items-center gap-3 rounded-2xl border border-green-200/70 bg-green-50/80 px-4 py-3 text-sm font-bold text-green-700 shadow-sm">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-2 flex items-center gap-3 rounded-2xl border border-red-200/70 bg-red-50/80 px-4 py-3 text-sm font-bold text-red-700 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-4 rounded-[1.5rem] border border-gray-100 bg-gradient-to-r from-white via-blue-50/30 to-white p-4 shadow-sm">
                <div class="rounded-xl bg-blue-50 p-3 text-blue-600"><i data-lucide="shopping-cart" class="w-6 h-6"></i></div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Commandes</p>
                    <p class="text-xl font-black text-gray-800">{{ $commandes->total() }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-[1.75rem] border border-gray-200 bg-white shadow-[0_12px_45px_-20px_rgba(15,23,42,0.25)]">
            @if ($commandes->count() > 0)
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Réf / Date</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Client</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Montant</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Paiement</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @foreach ($commandes as $commande)
                            <tr class="transition hover:bg-blue-50/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-black text-gray-800">{{ $commande->numero_commande }}</div>
                                        
                                        {{-- IDENTIFICATION VISUELLE DU RETOUR SUR LA RÉFÉRENCE --}}
                                        @if($commande->a_un_retour === 'total')
                                            <span class="inline-flex items-center p-1 bg-amber-50 text-amber-600 rounded-md border border-amber-100" title="Retour total effectué">
                                                <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
                                            </span>
                                        @elseif($commande->a_un_retour === 'partiel')
                                            <span class="inline-flex items-center p-1 bg-blue-50 text-blue-600 rounded-md border border-blue-100" title="Retour partiel effectué">
                                                <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">{{ $commande->date_commande ? $commande->date_commande->format('d M Y à H:i') : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 border">
                                            {{ substr($commande->client->nom ?? 'C', 0, 1) }}
                                        </div>
                                        <div class="text-sm font-bold text-gray-700">{{ $commande->client->nom ?? 'Client' }} {{ $commande->client->prenom ?? 'Occasionnel' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-black text-gray-900">{{ number_format($commande->total, 0, '', ' ') }} KMF</span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    {{-- GESTION DYNAMIQUE DU BADGE DE PAIEMENT ET DE RETOUR --}}
                                    @if($commande->a_un_retour === 'total')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase px-2.5 py-1 rounded-md bg-amber-100 text-amber-700 border border-amber-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Remboursé
                                        </span>
                                    @elseif($commande->a_un_retour === 'partiel')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase px-2.5 py-1 rounded-md bg-blue-100 text-blue-700 border border-blue-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Retour Partiel
                                        </span>
                                    @elseif ($commande->paiement)
                                        @php
                                            $isPaye = in_array($commande->paiement->statut_paiement, ['paye', 'complete']);
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase px-2.5 py-1 rounded-md {{ $isPaye ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ $commande->paiement->statut_paiement_label }}
                                        </span>
                                    @else
                                        <a href="{{ route('paiements.create', $commande) }}"
                                            class="inline-flex items-center gap-1 text-[10px] font-black uppercase px-2.5 py-1 rounded-md bg-red-50 text-red-600 border border-red-100 hover:bg-red-100 transition">
                                            <i data-lucide="credit-card" class="w-3 h-3"></i> Imprimer & Payer
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('commandes.show', $commande) }}"
                                            class="rounded-xl border border-gray-100 bg-white p-2.5 text-blue-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50" title="Voir le détail">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        @if ($commande->statut == 'en_attente' && !$commande->paiement)
                                            <a href="{{ route('commandes.edit', $commande) }}"
                                                class="rounded-xl border border-gray-100 bg-white p-2.5 text-gray-600 shadow-sm transition hover:border-gray-200 hover:bg-gray-100" title="Modifier">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <form method="POST" action="{{ route('commandes.destroy', $commande) }}"
                                                onsubmit="return confirm('Supprimer définitivement cette commande ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-xl border border-gray-100 bg-white p-2.5 text-red-600 shadow-sm transition hover:border-red-200 hover:bg-red-50" title="Supprimer">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    {{ $commandes->links() }}
                </div>
            @else
                <div class="bg-gradient-to-br from-gray-50 via-white to-blue-50/40 p-20 text-center">
                    <div class="mx-auto mb-4 inline-flex h-20 w-20 items-center justify-center rounded-full border-4 border-dashed border-gray-200 bg-white text-gray-300 shadow-sm">
                        <i data-lucide="inbox" class="w-8 h-8"></i>
                    </div>
                    <p class="text-lg font-bold text-gray-500">Aucune commande enregistrée pour le moment.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL INTERACTIF DE CLÔTURE DE CAISSE --}}
    <div id="clotureModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-60 backdrop-blur-sm transition-opacity" onclick="closeClotureModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                <div class="bg-white px-6 pt-6 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-50 text-red-600 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-lucide="shield-alert" class="w-6 h-6"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-black text-gray-900 uppercase tracking-tight" id="modal-title">
                                Clôturer la journée ?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 font-medium leading-relaxed">
                                    Êtes-vous sûr de vouloir fermer la caisse ? Cette action va compiler toutes les transactions, <span class="font-bold text-red-600">générer le rapport PDF</span> et l'envoyer directement dans votre boîte e-mail.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <form method="POST" action="{{ route('cloture.journee') }}" id="clotureForm">
                        @csrf
                        <button type="submit" onclick="showLoadingState()" id="confirmBtn"
                            class="w-full inline-flex justify-center items-center gap-2 rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-red-600 text-sm font-black text-white hover:bg-red-700 focus:outline-none transition uppercase tracking-wider">
                            <i data-lucide="check" class="w-4 h-4"></i> Oui, Clôturer
                        </button>
                    </form>
                    <button type="button" onclick="closeClotureModal()" id="cancelBtn"
                        class="mt-3 w-full sm:mt-0 inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none transition uppercase tracking-wider">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS DE GESTION DU MODAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function openClotureModal() {
            const modal = document.getElementById('clotureModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function closeClotureModal() {
            const modal = document.getElementById('clotureModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function showLoadingState() {
            const confirmBtn = document.getElementById('confirmBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            
            confirmBtn.disabled = true;
            cancelBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Envoi en cours...';
            confirmBtn.classList.replace('bg-red-600', 'bg-red-400');
            
            document.getElementById('clotureForm').submit();
        }
    </script>
</x-app-layout>