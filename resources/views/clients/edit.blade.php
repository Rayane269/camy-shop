<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-gray-800 uppercase tracking-tighter">
            Modifier le client : <span class="text-blue-600">{{ $client->nom }}</span>
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
            <form action="{{ route('clients.update', $client) }}" method="POST" class="p-10 space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', $client->nom) }}" 
                               class="w-full rounded-xl border-gray-100 bg-gray-50 focus:bg-white focus:ring-blue-500 font-bold">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', $client->prenom) }}"
                               class="w-full rounded-xl border-gray-100 bg-gray-50 focus:bg-white focus:ring-blue-500 font-bold">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $client->telephone) }}"
                           class="w-full rounded-xl border-gray-100 bg-gray-50 focus:bg-white focus:ring-blue-500 font-bold">
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Adresse</label>
                    <textarea name="adresse" rows="3" 
                              class="w-full rounded-xl border-gray-100 bg-gray-50 focus:bg-white focus:ring-blue-500 font-bold">{{ old('adresse', $client->adresse) }}</textarea>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition uppercase tracking-widest text-xs">
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('clients.index') }}" class="px-8 bg-gray-100 text-gray-500 font-black py-4 rounded-2xl hover:bg-gray-200 transition uppercase tracking-widest text-xs text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>