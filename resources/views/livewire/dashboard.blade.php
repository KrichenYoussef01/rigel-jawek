<div class="fixed inset-0 flex overflow-hidden bg-gray-950">

   
    <aside class="w-64 bg-white text-gray-800 flex flex-col flex-shrink-0 border-r border-gray-200">

        <div class="p-6 text-xl font-bold border-b border-gray-200 tracking-wide">
            ⚙️ Mon Panel
        </div>

        <nav class="flex-1 p-4 space-y-1">
         
            <button wire:click="setSection('accueil')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                       {{ $section === 'accueil' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                🏠 Accueil
            </button>

            <button wire:click="setSection('dashboard')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                       {{ $section === 'dashboard' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                📊 Tableau de bord
            </button>

            
            <button wire:click="setSection('livestream')"
    class="w-full text-left px-4 py-3 rounded-xl transition font-medium
           {{ $section === 'livestream' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
    📡 Live Stream
</button>

            
            <button wire:click="setSection('settings')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                       {{ $section === 'settings' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                ⚙️ Paramètres
            </button>

           
            <button wire:click="setSection('stats')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                       {{ $section === 'stats' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                📈 Statistiques
            </button>

           
            <button wire:click="setSection('support')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                       {{ $section === 'support' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
                🆘 Support
            </button>
        </nav>
    </aside>

   
    <main class="flex-1 overflow-y-auto bg-white text-gray-900 p-6">

        @if ($section === 'accueil')
            @livewire('util.dashboardutil')

        @elseif ($section === 'dashboard')
            @livewire('util.code-article')

        @elseif ($section === 'livestream')
           @include('livewire.util.live-stream')

        @elseif ($section === 'settings')
            <h1 class="text-2xl font-bold">Paramètres</h1>
            <p class="mt-2">Configuration de l'application...</p>

        @elseif ($section === 'stats')
            <h1 class="text-2xl font-bold">Statistiques</h1>
            <p class="mt-2">Graphiques et analyses...</p>

        @elseif ($section === 'support')
            <h1 class="text-2xl font-bold">Support</h1>
            <p class="mt-2">Tickets, FAQ, assistance...</p>

        @else
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <p class="text-6xl mb-4">👈</p>
                <p class="text-xl font-medium">Sélectionnez une section</p>
            </div>
        @endif

    </main>
</div>