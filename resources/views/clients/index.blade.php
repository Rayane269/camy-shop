<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight uppercase tracking-tighter">
                Répertoire <span class="text-blue-600">Clients</span>
            </h2>
            <a href="{{ route('clients.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                <i data-lucide="user-plus" class="w-5 h-5"></i> NOUVEAU CLIENT
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto space-y-6" x-data="{ openDelete: false, deleteUrl: '', clientName: '' }">
        
        @if (session('success'))
            <div class="mb-2 rounded-2xl border border-green-200/70 bg-green-50/80 px-4 py-3 text-sm font-bold text-green-700 shadow-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-2 rounded-2xl border border-red-200/70 bg-red-50/80 px-4 py-3 text-sm font-bold text-red-700 shadow-sm flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-[1.75rem] border border-gray-100 bg-gradient-to-r from-white via-blue-50/30 to-white p-4 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total inscrits :</span>
                <span class="rounded-full bg-blue-100/80 px-3 py-1 text-sm font-black text-blue-700">{{ $clients->total() }}</span>
            </div>

            <form action="{{ route('clients.index') }}" method="GET" class="relative w-full md:w-80">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un nom ou téléphone..." 
                    class="w-full rounded-2xl border border-gray-200 bg-white/90 py-2.5 pl-10 pr-4 text-sm font-bold text-gray-700 shadow-sm transition focus:border-transparent focus:ring-2 focus:ring-blue-500/40">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            </form>
        </div>

        <div class="overflow-hidden rounded-[1.75rem] border border-gray-200 bg-white shadow-[0_12px_45px_-20px_rgba(15,23,42,0.25)]">
            @if ($clients->count() > 0)
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">ID / Client</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Contact</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Localisation</th>
                            <th class="px-8 py-5 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($clients as $client)
                            <tr class="group transition-all duration-200 hover:bg-blue-50/50">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-black text-lg shadow-md group-hover:scale-110 transition-transform">
                                            {{ strtoupper(substr($client->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-gray-800 uppercase tracking-tight">
                                                {{ $client->nom }} {{ $client->prenom }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Client #{{ $client->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-2 text-gray-600 font-bold text-sm">
                                        <div class="p-1.5 bg-green-50 text-green-600 rounded-lg">
                                            <i data-lucide="phone" class="w-4 h-4"></i>
                                        </div>
                                        {{ $client->telephone ?: 'Non renseigné' }}
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-2 text-gray-500 font-medium text-sm">
                                        <div class="p-1.5 bg-gray-100 text-gray-500 rounded-lg">
                                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        </div>
                                        <span class="truncate max-w-xs">{{ $client->adresse ?: 'Pas d\'adresse' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                        <a href="{{ route('clients.edit', $client) }}" class="rounded-xl border border-gray-100 bg-white p-2.5 text-gray-400 shadow-sm transition hover:border-blue-200 hover:text-blue-600">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <button @click="openDelete = true; deleteUrl = '{{ route('clients.destroy', $client) }}'; clientName = '{{ $client->nom }} {{ $client->prenom }}'" 
                                            class="rounded-xl border border-gray-100 bg-white p-2.5 text-gray-400 shadow-sm transition hover:border-red-200 hover:text-red-600">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    {{ $clients->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-20 text-center bg-gradient-to-br from-gray-50 via-white to-blue-50/40">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border-4 border-dashed border-gray-200 bg-white text-gray-300 shadow-sm">
                        <i data-lucide="users" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-400">Aucun client trouvé</h3>
                    @if(request('search'))
                        <a href="{{ route('clients.index') }}" class="mt-4 inline-block text-xs font-bold uppercase tracking-widest text-blue-600">Voir tous les clients</a>
                    @endif
                </div>
            @endif
        </div>

        <template x-teleport="body">
            <div x-show="openDelete" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" x-cloak>
                
                <div @click.away="openDelete = false" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full shadow-2xl border border-gray-100 text-center">
                    
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="alert-triangle" class="w-10 h-10"></i>
                    </div>
                    
                    <h3 class="font-black text-gray-800 text-xl uppercase tracking-tighter mb-2">Supprimer le client ?</h3>
                    <p class="text-gray-500 font-medium text-sm mb-8">Voulez-vous vraiment retirer <span x-text="clientName" class="font-black text-gray-800"></span> ? Cette action est définitive.</p>
                    
                    <form :action="deleteUrl" method="POST" class="flex flex-col gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white font-black py-4 rounded-2xl hover:bg-red-700 shadow-lg shadow-red-200 transition uppercase tracking-widest text-xs">
                            Confirmer la suppression
                        </button>
                        <button type="button" @click="openDelete = false" class="w-full bg-gray-100 text-gray-500 font-black py-4 rounded-2xl hover:bg-gray-200 transition uppercase tracking-widest text-xs">
                            Annuler
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</x-app-layout>