<section class="relative">
    <header class="mb-8">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                <i data-lucide="user-cog" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tighter">
                    {{ __('Mon Profil') }}
                </h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    {{ __("Gérez vos informations personnelles et votre email.") }}
                </p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <x-input-label for="name" class="ml-2 text-[10px] font-black uppercase tracking-widest text-gray-400" :value="__('Nom complet')" />
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input id="name" name="name" type="text" 
                        class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 transition-all shadow-inner" 
                        value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-2">
                <x-input-label for="email" class="ml-2 text-[10px] font-black uppercase tracking-widest text-gray-400" :value="__('Adresse Email')" />
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input id="email" name="email" type="email" 
                        class="block w-full pl-11 pr-4 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 transition-all shadow-inner" 
                        value="{{ old('email', $user->email) }}" required autocomplete="username" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 p-4 bg-yellow-50 rounded-2xl border border-yellow-100">
                        <p class="text-xs font-bold text-yellow-700 flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ __('Votre email n\'est pas vérifié.') }}
                        </p>
                        <button form="send-verification" class="mt-2 text-[10px] font-black uppercase tracking-widest text-yellow-800 underline hover:text-yellow-900">
                            {{ __('Renvoyer le lien de vérification') }}
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-[10px] font-black uppercase text-green-600">
                                {{ __('Un nouveau lien a été envoyé.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-8 py-4 bg-gray-900 text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-200 transition-all flex items-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i>
                {{ __('Enregistrer les modifications') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 rounded-xl">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ __('Enregistré !') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>

<script>
    // Initialisation des icônes si nécessaire
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>