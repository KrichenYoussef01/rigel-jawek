<div class="bg-white rounded-2xl max-w-3xl w-full max-h-[85vh] overflow-hidden shadow-2xl">
    <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
        <h3 class="text-xl font-bold text-gray-800">
            Détails du plan <span class="text-indigo-600">{{ $planName }}</span>
        </h3>
        <button wire:click="$dispatch('closeModal')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
    </div>
    <div class="p-6 overflow-y-auto max-h-[70vh] bg-white">
        @if(session()->has('message'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePlan" class="space-y-6">
            {{-- Limites numériques --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">📊 Limites mensuelles</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max lives / mois</label>
                        <input type="number" wire:model="max_lives_par_mois" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max commandes / mois</label>
                        <input type="number" wire:model="max_commandes_par_mois" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max commentaires / live</label>
                        <input type="number" wire:model="max_commentaires_par_live" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max produits</label>
                        <input type="number" wire:model="max_produits" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max utilisateurs</label>
                        <input type="number" wire:model="max_utilisateurs" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max comptes TikTok</label>
                        <input type="number" wire:model="max_comptes_tiktok" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Max exports / jour</label>
                        <input type="number" wire:model="max_exports_par_jour" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    </div>
                </div>
            </div>

            {{-- Fonctionnalités booléennes --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">✨ Fonctionnalités</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center justify-between p-2 border border-gray-200 rounded-lg">
                        <span>Support prioritaire</span>
                        <input type="checkbox" wire:model="support_prioritaire" class="rounded">
                    </label>
                    <label class="flex items-center justify-between p-2 border border-gray-200 rounded-lg">
                        <span>API personnalisée</span>
                        <input type="checkbox" wire:model="api_personnalisee" class="rounded">
                    </label>
                    <label class="flex items-center justify-between p-2 border border-gray-200 rounded-lg">
                        <span>Multi-comptes TikTok</span>
                        <input type="checkbox" wire:model="multi_comptes_tiktok" class="rounded">
                    </label>
                    <label class="flex items-center justify-between p-2 border border-gray-200 rounded-lg">
                        <span>Manager de compte</span>
                        <input type="checkbox" wire:model="manager_de_compte" class="rounded">
                    </label>
                    <label class="flex items-center justify-between p-2 border border-gray-200 rounded-lg">
                        <span>Extraction temps réel</span>
                        <input type="checkbox" wire:model="extraction_temps_reel" class="rounded">
                    </label>
                    <label class="flex items-center justify-between p-2 border border-gray-200 rounded-lg">
                        <span>Paniers automatiques</span>
                        <input type="checkbox" wire:model="paniers_automatiques" class="rounded">
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>