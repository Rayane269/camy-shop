<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-800 leading-tight uppercase tracking-tighter">
                Nouveau <span class="text-blue-600">Client</span>
            </h2>
            <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <i data-lucide="x-circle" class="w-8 h-8"></i>
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="bg-white shadow-sm border border-gray-100 rounded-[2.5rem] overflow-hidden">
    
            <div class="p-5">
                <form method="POST" action="{{ route('clients.store') }}" class="space-y-8">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label for="nom" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nom du client *</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </span>
                                <input type="text" name="nom" id="nom"
                                    class="block w-full pl-11 rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('nom') border-red-500 @enderror"
                                    value="{{ old('nom') }}" placeholder="Ex: AHMED" required>
                            </div>
                            @error('nom') <p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="prenom" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Prénom *</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i data-lucide="signature" class="w-4 h-4"></i>
                                </span>
                                <input type="text" name="prenom" id="prenom"
                                    class="block w-full pl-11 rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('prenom') border-red-500 @enderror"
                                    value="{{ old('prenom') }}" placeholder="Ex: Salim" required>
                            </div>
                            @error('prenom') <p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="telephone" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Numéro de téléphone *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </span>
                            <input type="tel" name="telephone" id="telephone"
                                class="block w-full pl-11 rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('telephone') border-red-500 @enderror"
                                value="{{ old('telephone') }}" placeholder="+269 --- -- --" required>
                        </div>
                        @error('telephone') <p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="adresse" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Adresse de livraison / Résidence *</label>
                        <div class="relative">
                            <span class="absolute top-4 left-4 text-gray-400">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </span>
                            <textarea name="adresse" id="adresse" rows="3"
                                class="block w-full pl-11 rounded-2xl border-gray-100 bg-gray-50/50 py-4 font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 @error('adresse') border-red-500 @enderror"
                                placeholder="Moroni, quartier..." required>{{ old('adresse') }}</textarea>
                        </div>
                        @error('adresse') <p class="text-red-500 text-[10px] font-black mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-6 flex flex-col sm:flex-row gap-4">
                        <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Enregistrer le client
                        </button>
                        <a href="{{ route('clients.index') }}"
                           class="flex-1 bg-gray-100 text-gray-500 py-5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-gray-200 transition text-center flex items-center justify-center gap-2">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</x-app-layout>