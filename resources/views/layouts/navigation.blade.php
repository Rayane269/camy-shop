<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <div class="shrink-0 flex items-center bg-white p-2.5 ">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-7 w-auto fill-current text-white" />
                    </a>
                </div>

                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex items-center">
                    
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="inline-flex items-center px-5 py-2.5 rounded-xl transition-all duration-300 gap-2 border-none 
                        {{ request()->routeIs('dashboard') 
                            ? 'bg-blue-100 text-blue-700 text-base font-black shadow-sm ring-1 ring-blue-200 scale-110 translate-y-[-1px]' 
                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-bold text-sm' }}">
                        <i data-lucide="layout-dashboard" class="{{ request()->routeIs('dashboard') ? 'w-5 h-5' : 'w-4 h-4' }}"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('commandes.index')" :active="request()->routeIs('commandes.*')"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl transition-all duration-300 gap-2 border-none 
                        {{ request()->routeIs('commandes.*') 
                            ? 'bg-blue-100 text-blue-700 text-base font-black shadow-sm ring-1 ring-blue-200 scale-110 translate-y-[-1px]' 
                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-bold text-sm' }}">
                        <i data-lucide="shopping-bag" class="{{ request()->routeIs('commandes.*') ? 'w-5 h-5' : 'w-4 h-4' }}"></i>
                        <span>{{ __('Commandes') }}</span>
                    </x-nav-link>

                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')"
                        class="inline-flex items-center px-5 py-2.5 rounded-xl transition-all duration-300 gap-2 border-none 
                        {{ request()->routeIs('clients.*') 
                            ? 'bg-blue-100 text-blue-700 text-base font-black shadow-sm ring-1 ring-blue-200 scale-110 translate-y-[-1px]' 
                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 font-bold text-sm' }}">
                        <i data-lucide="users" class="{{ request()->routeIs('clients.*') ? 'w-5 h-5' : 'w-4 h-4' }}"></i>
                        <span>{{ __('Clients') }}</span>
                    </x-nav-link>

                    <button onclick="openScanner()" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-red-600 text-white font-black text-sm uppercase tracking-widest gap-2 hover:bg-gray-900 transition-all shadow-lg shadow-red-100 group ml-4">
                        <i data-lucide="scan-barcode" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                        <span>{{ __('Scanner Retour') }}</span>
                    </button>

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div x-data="{ open: false }" x-on:close-user-dropdown.window="open = false" data-user-dropdown class="relative">
                    <button @click="open = !open" @keydown.escape="open = false" class="flex items-center gap-4 bg-gray-50 px-4 py-2 rounded-2xl border border-gray-100 shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <div class="flex flex-col items-end leading-none">
                            <span class="text-[11px] font-black text-gray-800 uppercase tracking-tighter">{{ Auth::user()->name }}</span>
                            <span class="text-[9px] text-green-500 font-bold uppercase tracking-widest mt-0.5 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> En ligne
                            </span>
                        </div>
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 shadow-sm text-gray-500 hover:text-blue-600 hover:border-blue-200 transition">
                            <i data-lucide="user-cog" class="w-5 h-5"></i>
                        </div>
                    </button>

                    <div x-show="open" x-transition x-cloak @click.outside="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg z-50">
                        <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                            <div class="px-4 py-2 border-b border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ma session</p>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-gray-700 font-bold hover:bg-blue-50">
                                <i data-lucide="settings" class="w-4 h-4 inline-block mr-2 text-gray-400"></i> {{ __('Paramètres') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-red-600 font-bold hover:bg-red-50">
                                    <i data-lucide="log-out" class="w-4 h-4 inline-block mr-2"></i> {{ __('Déconnexion') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition shadow-sm">
                    <i x-show="!open" data-lucide="menu" class="w-6 h-6"></i>
                    <i x-show="open" data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="sm:hidden border-t border-gray-50 bg-white shadow-2xl">
        <div class="pt-4 pb-4 space-y-2 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                class="rounded-2xl font-black flex items-center gap-4 py-4 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-500' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('commandes.index')" :active="request()->routeIs('commandes.*')" 
                class="rounded-2xl font-black flex items-center gap-4 py-4 {{ request()->routeIs('commandes.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-500' }}">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i> {{ __('Commandes') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')" 
                class="rounded-2xl font-black flex items-center gap-4 py-4 {{ request()->routeIs('clients.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-gray-500' }}">
                <i data-lucide="users" class="w-5 h-5"></i> {{ __('Clients') }}
            </x-responsive-nav-link>

            <button onclick="openScanner()" class="w-full mt-4 flex items-center justify-center gap-3 px-6 py-4 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm italic">
                <i data-lucide="scan-barcode" class="w-5 h-5"></i> {{ __('Scanner Ticket') }}
            </button>
        </div>

        <div class="pt-6 pb-6 border-t border-gray-100 bg-gray-50/80">
            <div class="px-6 flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-900 rounded-2xl flex items-center justify-center text-white font-black text-lg shadow-lg shadow-gray-200">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-black text-gray-900 text-base uppercase tracking-tighter">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-6 space-y-2 px-4">
                <a href="{{ route('profile.edit') }}" @click="open = false" class="flex items-center gap-3 rounded-xl border-none py-3 font-bold text-gray-700 transition hover:bg-blue-50">
                    <i data-lucide="settings" class="w-4 h-4 text-gray-400"></i> {{ __('Mon Profil') }}
                </a>

                <form method="POST" action="{{ route('logout') }}" @submit="open = false">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl border-none py-3 font-black text-red-600 transition hover:bg-red-50">
                        <i data-lucide="log-out" class="w-4 h-4"></i> {{ __('Déconnexion') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div id="scannerModal" class="fixed inset-0 z-[100] hidden bg-gray-900/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white rounded-[3.5rem] max-w-md w-full p-12 shadow-2xl text-center">
        <div class="w-24 h-24 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-8 animate-pulse">
            <i data-lucide="barcode" class="w-12 h-12"></i>
        </div>
        <h3 class="text-3xl font-black text-gray-900 uppercase tracking-tighter italic">Lecteur Actif</h3>
        <p class="text-gray-400 text-[10px] font-black uppercase mt-2 tracking-widest">Scanner le ticket pour le retour</p>
        
        <input type="text" id="barcodeInput" class="opacity-0 absolute" autocomplete="off">
        
        <div class="mt-10 py-8 px-6 bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
            <span class="text-[10px] font-black uppercase text-gray-400 tracking-[0.2em] animate-bounce block">Lecture en cours...</span>
        </div>

        <button onclick="closeScanner()" class="mt-10 text-gray-300 font-black uppercase text-[10px] tracking-widest hover:text-red-600 transition-colors">Abandonner</button>
    </div>
</div>

<script>
    function openScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        const input = document.getElementById('barcodeInput');
        input.focus();
        // Garder le focus forcé
        input.onblur = () => setTimeout(() => input.focus(), 10);
    }

    function closeScanner() {
        document.getElementById('scannerModal').classList.add('hidden');
        document.getElementById('barcodeInput').value = '';
    }

    // Gestion de la touche Entrée du scanner
    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const code = this.value.trim();
            if (code) {
                window.location.href = "/commandes/retour/" + code;
            }
        }
    });

    // Initialisation des icônes Lucide
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // Ferme le dropdown utilisateur si on clique en dehors (fallback si @click.outside ne fonctionne pas)
    document.addEventListener('click', function(e) {
        const dropdown = document.querySelector('[data-user-dropdown]');
        if (!dropdown) return;
        if (!dropdown.contains(e.target)) {
            document.dispatchEvent(new CustomEvent('close-user-dropdown'));
        }
    });
</script>