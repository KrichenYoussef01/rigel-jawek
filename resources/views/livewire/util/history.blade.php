@push('styles')
    <link rel="stylesheet" href="{{ asset('css/commentaire/history.css') }}">
    <link rel="stylesheet" href="{{ asset('css/commentaire/dashboard.css') }}">
@endpush

<div>
    @if($detailMode)

    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title">
                @if($platform === 'TikTok')
                    <i class="fab fa-tiktok" style="color:#010101;"></i>
                @else
                    <i class="fab fa-facebook" style="color:#1877f2;"></i>
                @endif
                Détails de la session
            </h3>
            <div class="flex gap-2">
                <button onclick="confirmDeleteSession({{ $session->id ?? 'null' }})"
                    class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </button>
                <button wire:click="$set('detailMode', false)" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </button>
            </div>
        </div>

        @if($session)
            <div class="p-4 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <strong><i class="fas fa-link text-indigo-500 mr-1"></i> Lien :</strong>
                    <a href="{{ $session->link ?? $session->tiktok_link ?? '#' }}"
                       target="_blank" class="text-blue-600 hover:underline">
                        {{ $session->link ?? $session->tiktok_link ?? 'Non renseigné' }}
                    </a>
                </div>
                <div><strong><i class="fas fa-play-circle text-green-500 mr-1"></i> Début :</strong> {{ \Carbon\Carbon::parse($session->started_at)->format('d/m/Y H:i') }}</div>
                <div><strong><i class="fas fa-stop-circle text-red-500 mr-1"></i> Fin :</strong> {{ $session->ended_at ? \Carbon\Carbon::parse($session->ended_at)->format('d/m/Y H:i') : 'En cours' }}</div>
                <div><strong><i class="fas fa-comments text-blue-500 mr-1"></i> Commentaires :</strong> {{ $session->total_comments }}</div>
                <div><strong><i class="fas fa-users text-purple-500 mr-1"></i> Clients :</strong> {{ $session->total_clients }}</div>
                <div><strong><i class="fas fa-box text-yellow-500 mr-1"></i> Articles :</strong> {{ $session->total_articles }}</div>
                <div><strong><i class="fas fa-phone text-emerald-500 mr-1"></i> Téléphones :</strong> {{ $session->total_phones }}</div>
            </div>
        @endif

        <div class="overflow-x-auto mt-2">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-user mr-1"></i> Client
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-phone mr-1"></i> Téléphones
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-box mr-1"></i> Articles
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-calendar mr-1"></i> Date
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($baskets as $basket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <i class="fas fa-user-circle text-gray-400 mr-1"></i>
                                {{ $basket->client_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @foreach($basket->phones as $phone)
                                    <span class="stat-pill">
                                        <i class="fas fa-phone-alt mr-1"></i>{{ $phone }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
    @if(!empty($basket->articles) && is_array($basket->articles))
        <div style="display:flex;flex-wrap:wrap;gap:4px;">
            @foreach($basket->articles as $article)
                @if(!empty($article))
                    <span class="inline-flex items-center gap-1"
                          style="background:#e0e7ff;color:#4338ca;padding:3px 10px;
                                 border-radius:999px;font-size:12px;font-weight:700;">
                        <i class="fas fa-tag" style="font-size:10px;"></i>
                        {{ $article }}
                    </span>
                @endif
            @endforeach
        </div>
    @else
        <span class="text-gray-400 text-xs"><i class="fas fa-minus"></i></span>
    @endif
</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                {{ \Carbon\Carbon::parse($basket->created_at)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-inbox fa-2x mb-2 block text-gray-300"></i>
                                Aucun panier trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @else
    {{-- ══════════════════════════ LIST VIEW ══════════════════════════ --}}
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title">
                    <i class="fas fa-history text-indigo-500 mr-2"></i> Historique des Lives
                </h3>
                <button id="btn-delete-selected"
                    onclick="confirmDeleteSelected()"
                    style="display:none;"
                    class="btn-delete-all">
                    <i class="fas fa-trash-alt mr-1"></i> Supprimer la sélection
                    (<span id="selected-count">0</span>)
                </button>
            </div>

            {{-- ════ BARRE DE FILTRES ════ --}}
            <div class="filter-bar">

                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;"></i>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Rechercher par lien..."
                           style="min-width:200px;padding-left:32px;">
                </div>

                <select wire:model.live="platform">
                    <option value=""><i class="fas fa-mobile-alt"></i> Toutes plateformes</option>
                    <option value="TikTok">TikTok</option>
                    <option value="Facebook">Facebook</option>
                </select>

                <input type="date" wire:model.live="dateFrom" title="Date début">
                <input type="date" wire:model.live="dateTo"   title="Date fin">

                <select wire:model.live="sortBy">
                    <option value="started_at">Trier par date</option>
                    <option value="total_comments">Commentaires</option>
                    <option value="total_clients">Clients</option>
                    <option value="total_articles">Articles</option>
                </select>

                <select wire:model.live="sortDir">
                    <option value="desc">↓ Décroissant</option>
                    <option value="asc">↑ Croissant</option>
                </select>

                <button wire:click="resetFilters" class="btn-reset">
                    <i class="fas fa-times mr-1"></i> Réinitialiser
                </button>

                {{-- AJOUT : Bouton Actualiser --}}
                <button wire:click="refreshSessions" class="btn-reset">
                    <i class="fas fa-sync-alt mr-1"></i> Actualiser
                </button>

                <span class="filter-count">
                    <i class="fas fa-filter mr-1"></i>{{ $sessions->total() }} résultat(s)
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="history-table">
                <thead>
                    <tr>
                        <th class="px-4 py-3 select-col">
                            <input type="checkbox" class="checkbox-row" id="check-all" onchange="toggleAll(this)">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-mobile-alt mr-1"></i> Plateforme
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-link mr-1"></i> Lien
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-play-circle mr-1"></i> Début
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-stop-circle mr-1"></i> Fin
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-comments mr-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-users mr-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-box mr-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-phone mr-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <i class="fas fa-cog mr-1"></i> Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sessions as $session)
                    <tr>
                        <td class="px-4 py-4 select-col">
                            <input type="checkbox" class="checkbox-row row-check"
                                   value="{{ $session->id }}"
                                   onchange="updateSelectedCount()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($session->platform === 'TikTok')
                                <span class="badge-tiktok">
                                    <i class="fab fa-tiktok mr-1"></i>TikTok
                                </span>
                            @else
                                <span class="badge-fb">
                                    <i class="fab fa-facebook mr-1"></i>Facebook
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="max-width:180px;">
                            @if($session->link ?? $session->tiktok_link ?? null)
                                <a href="{{ $session->link ?? $session->tiktok_link }}"
                                   target="_blank"
                                   title="{{ $session->link ?? $session->tiktok_link }}"
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-medium"
                                   style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-block;">
                                    <i class="fas fa-external-link-alt" style="font-size:11px;flex-shrink:0;"></i>
                                    {{ Str::limit($session->link ?? $session->tiktok_link, 25) }}
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">
                                    <i class="fas fa-minus"></i>
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                            {{ \Carbon\Carbon::parse($session->started_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($session->ended_at)
                                <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                                {{ \Carbon\Carbon::parse($session->ended_at)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-green-500 font-semibold">
                                    <i class="fas fa-circle fa-xs mr-1" style="font-size:8px;"></i>En cours
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="stat-pill">{{ $session->total_comments ?? $session->comments_count }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="stat-pill">{{ $session->total_clients ?? $session->clients_count }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="stat-pill">{{ $session->total_articles ?? $session->articles_count }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="stat-pill">{{ $session->total_phones ?? $session->phones_count }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                            <button wire:click="loadDetail({{ $session->id }}, '{{ $session->platform }}')"
                                    class="inline-flex items-center gap-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                                <i class="fas fa-eye"></i> Détails
                            </button>
                            
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-400">
                                <i class="fas fa-inbox fa-3x mb-3 block text-gray-300"></i>
                                Aucun live trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-4">
            {{ $sessions->links() }}
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/commentaire/history.js') }}"></script>

</div>