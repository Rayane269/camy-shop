<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black tracking-widest uppercase mb-4">
            <i data-lucide="lock" class="w-3 h-3"></i> Réinitialisation
        </div>
        <h2 class="text-3xl font-black text-gray-900 tracking-tighter uppercase">Réinitialisez votre <span class="text-blue-600">mot de passe</span></h2>
        <p class="text-gray-400 text-xs font-bold mt-2 uppercase tracking-wide">Entrez votre email et votre nouveau mot de passe.</p>
    </div>

    <x-auth-session-status class="mb-4 font-bold text-sm text-green-600 bg-green-50 p-4 rounded-2xl border border-green-100" :status="session('status')" />

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-2">
            <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Adresse Email</label>
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </span>
                <x-text-input id="email" class="block w-full pl-12 pr-4 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 shadow-inner transition-all" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="votre@email.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-[10px] font-black uppercase text-red-500" />
        </div>

        <div class="space-y-2">
            <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Nouveau mot de passe</label>
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <i data-lucide="key-round" class="w-5 h-5"></i>
                </span>
                <x-text-input id="password" class="block w-full pl-12 pr-4 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 shadow-inner transition-all" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-[10px] font-black uppercase text-red-500" />
        </div>

        <div class="space-y-2">
            <label for="password_confirmation" class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-1">Confirmer le mot de passe</label>
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </span>
                <x-text-input id="password_confirmation" class="block w-full pl-12 pr-4 py-4 bg-gray-50 border-gray-100 rounded-[1.5rem] font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 shadow-inner transition-all" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[10px] font-black uppercase text-red-500" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[2rem] font-black text-sm uppercase tracking-[0.2em] hover:bg-blue-600 hover:scale-[1.02] transition-all shadow-xl shadow-gray-200 flex items-center justify-center gap-3 group">
                <span>Réinitialiser</span>
                <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
            </button>
        </div>

        <div class="flex justify-center mt-4 text-[10px] uppercase tracking-[0.25em] font-black text-gray-500">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Retour à la connexion</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</x-guest-layout>
