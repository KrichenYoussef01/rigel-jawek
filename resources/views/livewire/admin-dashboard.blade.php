<div class="fixed inset-0 flex overflow-hidden bg-gray-950">

    
    <aside class="w-64 bg-white text-gray-800 flex flex-col flex-shrink-0 border-r border-gray-200">

        <div class="p-6 text-xl font-bold border-b border-gray-800 tracking-wide">
            ⚙️ Admin Panel
        </div>

       <nav class="flex-1 p-4 space-y-1">
            <button wire:click="setSection('payments')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                    {{ $section === 'payments' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-900 hover:text-white' }}">
                <i class="fas fa-shield-alt mr-2"></i> Demandes de Paiements
            </button>

            <button wire:click="setSection('total-payments')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                    {{ $section === 'total-payments' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-900 hover:text-white' }}">
                <i class="fas fa-money-bill-wave mr-2"></i> Total des Paiements
            </button>

            <button wire:click="setSection('vendeurs')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                    {{ $section === 'vendeurs' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-900 hover:text-white' }}">
                <i class="fas fa-user-tie mr-2"></i> Les Vendeurs
            </button>

            <button wire:click="setSection('settings')"
                class="w-full text-left px-4 py-3 rounded-xl transition font-medium
                    {{ $section === 'settings' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:bg-gray-900 hover:text-white' }}">
                <i class="fas fa-cog mr-2"></i> Limites des abonnements
            </button>
        </nav>
    </aside>

    {{-- ===== CONTENU BLANC ===== --}}
    <main class="flex-1 overflow-y-auto bg-white text-gray-900">

        @if ($section === 'payments')
            @livewire('admin.payments')

        @elseif ($section === 'total-payments')
            @livewire('admin.total-payments')

        @elseif ($section === 'vendeurs')
            @livewire('admin.vendeurs')

        @elseif ($section === 'settings')
            @livewire('admin.settings')

        @else
            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <p class="text-6xl mb-4">👈</p>
                <p class="text-xl font-medium">Sélectionnez une section</p>
            </div>
        @endif

    </main>
</div>