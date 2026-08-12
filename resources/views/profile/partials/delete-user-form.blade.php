<section class="relative">
    <header class="mb-8">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-red-100 text-red-600 rounded-2xl animate-pulse">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">
                    {{ __('Zone de Danger') }}
                </h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    {{ __('Action irréversible : suppression définitive de votre accès.') }}
                </p>
            </div>
        </div>
    </header>

    <div class="p-6 bg-red-50 rounded-[2rem] border-2 border-dashed border-red-100">
        <p class="text-sm text-red-700 font-medium leading-relaxed">
            {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Veuillez télécharger les données que vous souhaitez conserver avant de procéder.') }}
        </p>

        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="mt-6 px-8 py-4 bg-red-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-gray-900 hover:shadow-xl transition-all flex items-center gap-2"
        >
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            {{ __('Supprimer définitivement le compte') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-white">
            @csrf
            @method('delete')

            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="shield-alert" class="w-10 h-10"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">
                    {{ __('Confirmation Requise') }}
                </h2>
                <p class="mt-2 text-sm font-bold text-gray-400 uppercase">
                    {{ __('Veuillez saisir votre mot de passe pour confirmer.') }}
                </p>
            </div>

            <div class="space-y-2">
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-red-600 transition-colors">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-gray-700 focus:ring-2 focus:ring-red-600 shadow-inner"
                        placeholder="{{ __('Mot de passe de sécurité') }}"
                    />
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <button type="button" x-on:click="$dispatch('close')" 
                    class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-gray-200 transition-all">
                    {{ __('Annuler') }}
                </button>

                <button type="submit" 
                    class="flex-1 py-4 bg-red-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-red-100 hover:bg-red-700 transition-all">
                    {{ __('Confirmer la suppression') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>