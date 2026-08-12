<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ray-shop | Votre Caisse Intelligente</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,600,800" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] antialiased font-[Instrument\ Sans]">
        
        <header class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    
                    <span class="text-xl font-black tracking-tighter uppercase text-gray-900">Kamy-<span class="text-blue-600">Shop</span></span>
                </div>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black text-blue-600 shadow-inner hover:bg-blue-50 transition">
                                DASHBOARD
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-black text-gray-600 shadow-inner hover:bg-blue-50 hover:text-blue-600 transition tracking-widest uppercase">
                                Connexion
                            </a>
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <main class="pt-32 pb-20 px-6">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center">
                
                <div class="space-y-8 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-full text-xs font-black tracking-widest uppercase">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        </span>
                        Solution de Gestion v2.0
                    </div>

                    <h1 class="text-5xl lg:text-7xl font-black text-gray-900 leading-[1.1] tracking-tighter">
                        Gérez votre librairie <br>
                        <span class="text-blue-600">sans effort.</span>
                    </h1>

                    <p class="text-lg text-gray-500 font-medium leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Gagnez du temps, encaissez vos clients en toute simplicité. Suivez les commandes, gérez les paiements et boostez votre activité.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-5 bg-gray-900 text-white rounded-[2rem] font-black text-sm uppercase tracking-widest hover:scale-105 transition shadow-2xl shadow-gray-200 flex items-center justify-center gap-3">
                            <i data-lucide="play-circle" class="w-5 h-5"></i> Commencer maintenant
                        </a>
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full border-2 border-white bg-blue-100 flex items-center justify-center text-[10px] font-black italic">KMF</div>
                            <div class="w-10 h-10 rounded-full border-2 border-white bg-green-100 flex items-center justify-center text-green-600"><i data-lucide="check" class="w-4 h-4"></i></div>
                        </div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-tight">Adapté au marché local</span>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-blue-100/50 rounded-[3rem] blur-3xl opacity-30"></div>
                    <div class="relative bg-white border border-gray-100 p-8 rounded-[3rem] shadow-2xl space-y-6">
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Commandes</p>
                                <p class="text-lg font-black text-gray-800 tracking-tighter">Gestion simplifiée</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner translate-x-4">
                            <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-green-200">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Paiements</p>
                                <p class="text-lg font-black text-gray-800 tracking-tighter">Zéro erreur de caisse</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner -translate-x-4">
                            <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-200">
                                <i data-lucide="users" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Clients</p>
                                <p class="text-lg font-black text-gray-800 tracking-tighter">Fidélisation active</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <footer class="py-10 text-center border-t border-gray-50">
            <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.4em]">© 2026 RAY-MULTITECH POS SYSTEM</p>
        </footer>

    </body>
</html>