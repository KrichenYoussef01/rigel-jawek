<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">

        @if (session()->has('message'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-layer-group text-indigo-500 mr-2"></i>
                Gestion des codes article
            </h2>

            <div class="flex flex-wrap gap-2 justify-end">
                <button wire:click="$set('showAddModal', true)"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <i class="fas fa-plus"></i> Ajouter
                </button>

                <button wire:click="export"
                    class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <i class="fas fa-file-csv"></i> Exporter CSV
                </button>

                <input type="file" wire:model="importFile" id="importFile" class="hidden" accept=".csv,.txt" />
                <label for="importFile"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition cursor-pointer">
                    <i class="fas fa-file-import"></i> Importer CSV
                    <span wire:loading wire:target="importFile" class="inline-block ml-1">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </label>

                <button onclick="confirmDeleteAll()"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                    <i class="fas fa-trash-alt"></i> Supprimer tout
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-3 px-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-hashtag mr-1"></i> #
                        </th>
                        <th class="py-3 px-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-barcode mr-1"></i> Code Article
                        </th>
                        <th class="py-3 px-5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-cog mr-1"></i> Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($codes as $index => $code)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-5 text-sm text-gray-400 w-12">{{ $index + 1 }}</td>

                            <td class="py-3 px-5">
                                @if(($editingId ?? null) === $code->id)
                                    <div class="flex flex-col">
                                        <input type="text"
                                               wire:model.defer="editCode"
                                               class="border border-indigo-300 rounded-lg px-3 py-1 text-sm focus:ring-2 focus:ring-indigo-400 outline-none w-full"
                                               placeholder="Nouveau code...">
                                        @error('editCode')
                                            <span class="text-red-500 text-[10px] mt-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                @else
                                    <span class="font-mono font-semibold text-sm bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full">
                                        <i class="fas fa-tag mr-1 text-indigo-400"></i>{{ $code->code }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(($editingId ?? null) === $code->id)
                                        <button onclick="saveEditConfirm()"
                                            class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition shadow-sm">
                                            <i class="fas fa-save"></i> Sauvegarder
                                        </button>
                                        <button wire:click="$set('editingId', null)"
                                            class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    @else
                                        <button wire:click="startEdit({{ $code->id }})"
                                            class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                            <i class="fas fa-pen"></i> Modifier
                                        </button>
                                        <button onclick="confirmDelete({{ $code->id }})"
                                            class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                            <i class="fas fa-trash-alt"></i> Supprimer
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-12 text-gray-400">
                                <i class="fas fa-inbox fa-3x mb-3 block text-gray-300"></i>
                                Aucun code enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($codes->count() > 0)
            <div class="mt-3 text-right text-xs text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                {{ $codes->count() }} code(s) enregistré(s)
            </div>
        @endif

        {{-- Modal Ajout --}}
        @if($showAddModal ?? false)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-2xl">
                    <h3 class="text-lg font-bold mb-1 text-gray-800">
                        <i class="fas fa-plus-circle text-indigo-500 mr-2"></i> Ajouter un code
                    </h3>
                    <p class="text-sm text-gray-500 mb-5">
                        <i class="fas fa-info-circle mr-1"></i>
                        Saisissez le code article à enregistrer.
                    </p>
                    <input type="text"
                           wire:model="newCode"
                           placeholder="Ex: C48, REF-001..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 mb-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    @error('newCode')
                        <span class="text-red-500 text-xs block mb-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                        </span>
                    @enderror
                    <div class="flex justify-end gap-2 mt-4">
                        <button wire:click="$set('showAddModal', false)"
                            class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-xl text-sm font-semibold transition">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button onclick="addCodeConfirm()" wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-xl text-sm font-semibold transition">
                            <span wire:loading.remove wire:target="addCode">
                                <i class="fas fa-plus mr-1"></i> Ajouter
                            </span>
                            <span wire:loading wire:target="addCode">
                                <i class="fas fa-spinner fa-spin mr-1"></i> En cours...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

@script
<script>
    window.livewireComponent = $wire;
</script>
@endscript

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/commentaire/codeArticle.js') }}"></script>
@endpush