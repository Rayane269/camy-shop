<section class="relative">
    <header class="mb-8">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-red-50 text-red-600 rounded-2xl">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">
                    {{ __('Sécurité') }}
                </h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    {{ __('Utilisez un mot de passe complexe pour protéger votre accès.') }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="space-y-2">
            <x-input-label for="update_password_current_password" class="ml-2 text-[10px] font-black uppercase tracking-widest text-gray-400" :value="__('Mot de passe actuel')" />
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-red-500 transition-colors">
                    <i data-lucide="lock" class="w-4 h-4"></i>
                </span>
                <input id="update_password_current_password" name="current_password" type="password" 
                    class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-gray-700 focus:ring-2 focus:ring-red-500 transition-all shadow-inner" 
                    autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <x-input-label for="update_password_password" class="ml-2 text-[10px] font-black uppercase tracking-widest text-gray-400" :value="__('Nouveau mot de passe')" />
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-green-500 transition-colors">
                        <i data-lucide="key-round" class="w-4 h-4"></i>
                    </span>
                    <input id="update_password_password" name="password" type="password" 
                        class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all shadow-inner" 
                        autocomplete="new-password" placeholder="Min. 8 caractères" />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div class="space-y-2">
                <x-input-label for="update_password_password_confirmation" class="ml-2 text-[10px] font-black uppercase tracking-widest text-gray-400" :value="__('Confirmer le mot de passe')" />
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-green-500 transition-colors">
                        <i data-lucide="check-check" class="w-4 h-4"></i>
                    </span>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                        class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-gray-700 focus:ring-2 focus:ring-green-500 transition-all shadow-inner" 
                        autocomplete="new-password" placeholder="Répéter le mot de passe" />
                </div>
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-8 py-4 bg-gray-900 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-red-600 hover:shadow-lg hover:shadow-red-200 transition-all flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                {{ __('Mettre à jour la sécurité') }}
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 rounded-xl">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ __('Mot de passe modifié !') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>