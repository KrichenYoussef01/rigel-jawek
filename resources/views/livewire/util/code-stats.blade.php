@push('styles')
    <link rel="stylesheet" href="{{ asset('css/commentaire/code-stats.css') }}">
@endpush

<div>
<div class="cs-page">

    
    <div class="cs-topbar">
        <div class="cs-topbar__left">
            <div class="cs-topbar__icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <h1 class="cs-topbar__title">Statistiques par code article</h1>
                <p class="cs-topbar__sub">Analyse des performances de vos articles en live</p>
            </div>
        </div>
        <div class="cs-topbar__right">
            @if($hasLiveData)
                <span class="cs-live-badge">
                    <span class="cs-live-dot"></span> Live actif
                </span>
            @endif
            <button onclick="window.dispatchEvent(new CustomEvent('force-stats-refresh'))"
                    class="cs-refresh-btn">
                <i class="fas fa-sync-alt"></i> Actualiser
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{--              CARTES STATISTIQUES               --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div class="cs-stats-list">

        <div class="cs-stat-item">
            <div class="cs-stat-icon cs-stat-icon--indigo">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <div class="cs-stat-number">{{ count($labels) }}</div>
                <div class="cs-stat-name">Codes actifs</div>
            </div>
        </div>

        <div class="cs-stat-item">
            <div class="cs-stat-icon cs-stat-icon--blue">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
                <div class="cs-stat-number">{{ array_sum($data ?? []) }}</div>
                <div class="cs-stat-name">Total commandes</div>
            </div>
        </div>

        <div class="cs-stat-item">
            <div class="cs-stat-icon cs-stat-icon--emerald">
                <i class="fas fa-trophy"></i>
            </div>
            <div>
                <div class="cs-stat-number">{{ $labels[0] ?? '—' }}</div>
                <div class="cs-stat-name">Article #1</div>
            </div>
        </div>

        <div class="cs-stat-item">
            <div class="cs-stat-icon cs-stat-icon--orange">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="cs-stat-number">{{ count($labels) > 0 ? max($data ?? [0]) : 0 }}</div>
                <div class="cs-stat-name">Max commandes</div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{--                  GRAPHIQUES                    --}}
    {{-- ══════════════════════════════════════════════ --}}
    <div class="cs-charts-grid">

        <div class="cs-chart-card">
            <div class="cs-chart-card__head">
                <div class="cs-chart-card__icon" style="background:#ede9fe;">
                    <i class="fas fa-chart-pie" style="color:#6d28d9;"></i>
                </div>
                <div>
                    <h3 class="cs-chart-card__title">Répartition des commandes</h3>
                    <p class="cs-chart-card__sub">Part de chaque code article</p>
                </div>
            </div>
            <div class="cs-chart-wrap">
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <div class="cs-chart-card">
            <div class="cs-chart-card__head">
                <div class="cs-chart-card__icon" style="background:#dbeafe;">
                    <i class="fas fa-chart-bar" style="color:#1d4ed8;"></i>
                </div>
                <div>
                    <h3 class="cs-chart-card__title">Commandes par code</h3>
                    <p class="cs-chart-card__sub">Comparaison des volumes</p>
                </div>
            </div>
            <div class="cs-chart-wrap">
                <canvas id="barChart"></canvas>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════ --}}
    {{--                   TABLEAU                      --}}
    {{-- ══════════════════════════════════════════════ --}}
    @if(count($labels) > 0)
    <div class="cs-table-card">
        <div class="cs-table-card__head">
            <div class="cs-chart-card__icon" style="background:#dcfce7;">
                <i class="fas fa-table" style="color:#15803d;"></i>
            </div>
            <div>
                <h3 class="cs-chart-card__title">Détail par code article</h3>
                <p class="cs-chart-card__sub">{{ count($labels) }} code(s) enregistré(s)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="cs-table" id="code-stats-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag mr-1"></i> Rang</th>
                        <th><i class="fas fa-barcode mr-1"></i> Code article</th>
                        <th><i class="fas fa-shopping-cart mr-1"></i> Commandes</th>
                        <th><i class="fas fa-chart-bar mr-1"></i> Part</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = array_sum($data ?? []); @endphp
                    @foreach($labels as $index => $label)
                    @php
                        $pct = $total > 0 ? round(($data[$index] / $total) * 100) : 0;
                        $colors = ['#f59e0b','#6366f1','#10b981','#3b82f6','#ec4899','#8b5cf6'];
                        $color  = $colors[$index % count($colors)];
                    @endphp
                    <tr class="cs-table__row">
                        <td>
                            @if($index === 0) <span class="cs-medal">🥇</span>
                            @elseif($index === 1) <span class="cs-medal">🥈</span>
                            @elseif($index === 2) <span class="cs-medal">🥉</span>
                            @else
                                <span class="cs-rank-num">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="cs-code-chip">
                                <i class="fas fa-tag"></i> {{ $label }}
                            </span>
                        </td>
                        <td>
                            <span class="cs-cmd-num">{{ $data[$index] }}</span>
                        </td>
                        <td>
                            <div class="cs-bar-wrap">
                                <div class="cs-bar">
                                    <div class="cs-bar__fill" style="width:{{ $pct }}%;background:{{ $color }};"></div>
                                </div>
                                <span class="cs-bar__pct">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @else
    {{-- État vide --}}
    <div class="cs-empty">
        <div class="cs-empty__icon">
            <i class="fas fa-inbox"></i>
        </div>
        <h3 class="cs-empty__title">Aucune donnée disponible</h3>
        <p class="cs-empty__sub">Les statistiques apparaîtront dès qu'un code article est commandé durant un live.</p>
    </div>
    @endif

</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/commentaire/codeStats.js') }}"></script>
@endpush