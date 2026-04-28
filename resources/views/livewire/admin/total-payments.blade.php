<div>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        .dk-wrap {
            font-family: 'Space Grotesk', sans-serif;
            color: #1e293b;
            background: #f8fafc;
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        .dk-wrap * { box-sizing: border-box; margin: 0; padding: 0; }

        .dk-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 2rem; }
        .dk-title { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.02em; color: #0f172a; }
        .dk-subtitle { font-size: 0.8rem; color: #475569; margin-top: 0.25rem; }
        .dk-badge {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 0.72rem; font-weight: 600;
            padding: 0.3rem 0.75rem; border-radius: 99px;
            background: #dcfce7; color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .dk-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #15803d; }

        .dk-hero {
            position: relative; overflow: hidden;
            border-radius: 1.25rem; padding: 2.5rem 2.5rem 2rem; margin-bottom: 1.75rem;
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        .dk-hero-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
            color: #2563eb; margin-bottom: 1rem;
        }
        .dk-hero-label::before { content: ''; width: 18px; height: 2px; background: #2563eb; border-radius: 2px; }
        .dk-hero-amount {
            font-family: 'JetBrains Mono', monospace;
            font-size: clamp(2rem, 5vw, 3.4rem); font-weight: 700;
            color: #0f172a; letter-spacing: -0.02em; line-height: 1; margin-bottom: 0.75rem;
        }
        .dk-hero-amount span { color: #2563eb; }
        .dk-hero-meta { font-size: 0.8rem; color: #475569; }

        .dk-stats-row {
            display: grid; grid-template-columns: repeat(3,1fr);
            gap: 1rem; margin-bottom: 1.75rem;
        }
        .dk-stat-mini {
            background: #ffffff; border: 1px solid #e2e8f0;
            border-radius: 1rem; padding: 1.1rem 1.25rem;
            display: flex; align-items: center; gap: 1rem;
            transition: all .2s;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .dk-stat-mini:hover { border-color: #cbd5e1; transform: translateY(-2px); }
        .dk-stat-icon {
            width: 42px; height: 42px; border-radius: 0.6rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .i-pur { background: #e0e7ff; color: #4f46e5; }
        .i-cya { background: #cffafe; color: #0891b2; }
        .i-grn { background: #dcfce7; color: #15803d; }
        .dk-stat-val { font-size: 1.5rem; font-weight: 700; line-height: 1; color: #0f172a; }
        .dk-stat-lbl { font-size: 0.68rem; color: #475569; margin-top: 0.2rem; text-transform: uppercase; letter-spacing: 0.06em; }

        .dk-plans {
            display: grid; grid-template-columns: repeat(3,1fr);
            gap: 1.25rem; margin-bottom: 1.75rem;
        }
        .dk-plan-card {
            background: #ffffff; border: 1px solid #e2e8f0;
            border-radius: 1.25rem; padding: 1.5rem;
            cursor: pointer; transition: all .25s; position: relative; overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .dk-plan-card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -12px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        .dk-plan-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .dk-plan-name { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #475569; }
        .dk-plan-icon {
            width: 36px; height: 36px; border-radius: 0.6rem;
            display: flex; align-items: center; justify-content: center; font-size: 0.95rem;
        }
        .dk-plan-amount { font-family: 'JetBrains Mono', monospace; font-size: 1.4rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem; }
        .dk-plan-txn { font-size: 0.75rem; color: #475569; display: flex; align-items: center; gap: 5px; margin-bottom: 1rem; }
        .dk-progress-lbl { display: flex; justify-content: space-between; font-size: 0.68rem; color: #475569; margin-bottom: 0.4rem; }
        .dk-progress-bar { height: 4px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
        .dk-progress-fill { height: 100%; border-radius: 99px; }

        .dk-bottom { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .dk-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1.25rem; padding: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03); }
        .dk-section-title {
            font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            color: #475569; margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 8px;
        }
        .dk-section-title::before { content: ''; width: 3px; height: 14px; border-radius: 2px; background: #3b82f6; }

        .dk-chart-area { display: flex; align-items: flex-end; gap: 0.6rem; height: 120px; margin-bottom: 0.5rem; }
        .dk-bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; }
        .dk-bar { width: 100%; border-radius: 6px 6px 0 0; min-height: 4px; position: relative; transition: filter .2s; background: #94a3b8; }
        .dk-bar:hover { filter: brightness(1.1); }
        .dk-bar-val {
            font-size: 0.58rem; color: #1e293b;
            position: absolute; top: -18px; left: 50%; transform: translateX(-50%); white-space: nowrap;
        }
        .dk-chart-lbls { display: flex; gap: 0.6rem; }
        .dk-chart-lbls span { flex: 1; text-align: center; font-size: 0.62rem; color: #475569; }

        .dk-vp-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
        .dk-vp-name { font-size: 0.8rem; font-weight: 600; color: #0f172a; min-width: 90px; }
        .dk-vp-bar { flex: 1; height: 6px; background: #e2e8f0; border-radius: 99px; overflow: hidden; }
        .dk-vp-fill { height: 100%; border-radius: 99px; }
        .dk-vp-count { font-size: 0.75rem; font-weight: 700; min-width: 28px; text-align: right; }

        .dk-vendor-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.65rem 0; border-bottom: 1px solid #e2e8f0; }
        .dk-vendor-item:last-child { border-bottom: none; }
        .dk-avatar {
            width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
            background: #e0e7ff; color: #2563eb;
        }
        .dk-vendor-name { font-size: 0.82rem; font-weight: 600; color: #0f172a; }
        .dk-vendor-email { font-size: 0.7rem; color: #475569; }
        .dk-vendor-plan {
            margin-left: auto; font-size: 0.65rem; font-weight: 700;
            padding: 0.2rem 0.6rem; border-radius: 99px;
            background: #e0e7ff; color: #1e40af; white-space: nowrap;
        }

        .dk-plan-details {
            margin-top: 1.5rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .dk-plan-details-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;
        }
        .dk-plan-details-title { font-size: 1rem; font-weight: 700; color: #0f172a; }
        .dk-close-btn {
            background: none; border: 1px solid #cbd5e1; border-radius: 99px;
            padding: 0.25rem 0.8rem; font-size: 0.7rem; font-weight: 500;
            color: #475569; cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .dk-close-btn:hover { background: #f1f5f9; border-color: #94a3b8; }
        .dk-table { width: 100%; border-collapse: collapse; }
        .dk-table th {
            text-align: left; padding: 0.75rem 0.5rem;
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
            color: #475569; border-bottom: 1px solid #e2e8f0;
        }
        .dk-table td {
            padding: 0.75rem 0.5rem; font-size: 0.85rem;
            border-bottom: 1px solid #f1f5f9; color: #1e293b;
        }
        .dk-table tr:last-child td { border-bottom: none; }
        .dk-pagination { margin-top: 1.25rem; }
        .dk-pagination nav { display: flex; justify-content: center; }
    </style>

    <div class="dk-wrap">

        {{-- Header --}}
        <div class="dk-header">
            <div>
                <h1 class="dk-title">Total des Paiements</h1>
                <p class="dk-subtitle">Vue d'ensemble des revenus par plan</p>
            </div>
            <div class="dk-badge">+141% ce mois</div>
        </div>

        {{-- Hero --}}
        <div class="dk-hero">
            <div class="dk-hero-label">Montant Total Collecté</div>
            <div class="dk-hero-amount">
                {{ number_format($totalGlobal, 3) }} <span>TND</span>
            </div>
            <div class="dk-hero-meta">
                {{ $totalTransactions }} transactions au total · {{ now()->translatedFormat('F Y') }}
            </div>
        </div>

        {{-- Mini stats --}}
        <div class="dk-stats-row">
            <div class="dk-stat-mini">
                <div class="dk-stat-icon i-pur">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="dk-stat-val">{{ $totalVendors }}</div>
                    <div class="dk-stat-lbl">Vendeurs actifs</div>
                </div>
            </div>
            <div class="dk-stat-mini">
                <div class="dk-stat-icon i-cya">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </div>
                <div>
                    <div class="dk-stat-val">{{ $totalTransactions }}</div>
                    <div class="dk-stat-lbl">Transactions totales</div>
                </div>
            </div>
            <div class="dk-stat-mini">
                <div class="dk-stat-icon i-grn">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <div>
                    <div class="dk-stat-val">{{ $statsByPlan->count() }}</div>
                    <div class="dk-stat-lbl">Plans actifs</div>
                </div>
            </div>
        </div>

        {{-- Plan Cards --}}
        <div class="dk-plans">
            @foreach($statsByPlan as $plan)
            @php
                $planMeta = [
                    'Enterprise' => [
                        'accent' => '#3b82f6',
                        'bg'     => '#e0e7ff',
                        'icon'   => '<i class="fa-solid fa-server" style="color:#3b82f6"></i>',
                        'dot'    => '#3b82f6',
                    ],
                    'Business' => [
                        'accent' => '#0ea5e9',
                        'bg'     => '#e0f2fe',
                        'icon'   => '<i class="fa-solid fa-briefcase" style="color:#0ea5e9"></i>',
                        'dot'    => '#0ea5e9',
                    ],
                    'Starter' => [
                        'accent' => '#f97316',
                        'bg'     => '#ffedd5',
                        'icon'   => '<i class="fa-solid fa-rocket" style="color:#f97316"></i>',
                        'dot'    => '#f97316',
                    ],
                ];
                $m   = $planMeta[$plan->plan_name] ?? [
                    'accent' => '#8b5cf6',
                    'bg'     => '#ede9fe',
                    'icon'   => '<i class="fa-solid fa-credit-card" style="color:#8b5cf6"></i>',
                    'dot'    => '#8b5cf6',
                ];
                $pct = $totalGlobal > 0 ? round(($plan->total_amount / $totalGlobal) * 100) : 0;
            @endphp
            <div class="dk-plan-card"
                 style="border-top: 2px solid {{ $m['accent'] }};"
                 wire:click="selectPlan('{{ $plan->plan_name }}')">
                <div class="dk-plan-top">
                    <span class="dk-plan-name">{{ $plan->plan_name }}</span>
                    <div class="dk-plan-icon" style="background:{{ $m['bg'] }}">
                        {!! $m['icon'] !!}
                    </div>
                </div>
                <div class="dk-plan-amount">{{ number_format($plan->total_amount, 3) }} TND</div>
                <div class="dk-plan-txn">
                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $m['accent'] }};display:inline-block;"></span>
                    {{ $plan->count }} transactions
                </div>
                <div class="dk-progress-lbl">
                    <span>Part des revenus</span>
                    <span style="color:{{ $m['accent'] }};font-weight:700;">{{ $pct }}%</span>
                </div>
                <div class="dk-progress-bar">
                    <div class="dk-progress-fill" style="width:{{ $pct }}%;background:{{ $m['accent'] }}"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Tableau des vendeurs pour le plan sélectionné --}}
        @if($selectedPlan)
        <div class="dk-plan-details">
            <div class="dk-plan-details-header">
                <div class="dk-plan-details-title">
                    <i class="fa-solid fa-table-list" style="color:#2563eb; margin-right:6px;"></i>
                    Vendeurs · <span style="color:#2563eb;">{{ $selectedPlan }}</span>
                </div>
                <button class="dk-close-btn" wire:click="closePlanDetails">
                    <i class="fa-solid fa-xmark"></i> Fermer
                </button>
            </div>

            @if($vendorsForPlan->count() > 0)
                <table class="dk-table">
                    <thead>
                        <tr>
                            <th><i class="fa-solid fa-user" style="margin-right:5px;"></i>Vendeur</th>
                            <th><i class="fa-solid fa-envelope" style="margin-right:5px;"></i>Email</th>
                            <th><i class="fa-solid fa-coins" style="margin-right:5px;"></i>Total payé (TND)</th>
                            <th><i class="fa-solid fa-receipt" style="margin-right:5px;"></i>Nb transactions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendorsForPlan as $vendor)
                        <tr>
                            <td>{{ $vendor->user->name ?? 'N/A' }}</td>
                            <td>{{ $vendor->user->email ?? '' }}</td>
                            <td>{{ number_format($vendor->total_amount, 3) }}</td>
                            <td>{{ $vendor->transaction_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="dk-pagination">
                    {{ $vendorsForPlan->links() }}
                </div>
            @else
                <p style="color:#475569; text-align:center; padding:1rem;">
                    <i class="fa-solid fa-circle-info" style="margin-right:6px;"></i>
                    Aucun vendeur trouvé pour ce plan.
                </p>
            @endif
        </div>
        @endif

        {{-- Bottom grid --}}
        <div class="dk-bottom">

            {{-- Bar chart --}}
            <div class="dk-card">
                <div class="dk-section-title">
                    <i class="fa-solid fa-chart-column" style="margin-right:4px;"></i>
                    Évolution des revenus
                </div>
                <div class="dk-chart-area">
                    @foreach($months as $mo)
                    @php
                        $maxMonth = $months->max('total') ?: 1;
                        $h = $maxMonth > 0 ? max(6, round(($mo->total / $maxMonth) * 100)) : 6;
                    @endphp
                    <div class="dk-bar-wrap">
                        <div class="dk-bar" style="height:{{ $h }}%; background:{{ $loop->last ? '#3b82f6' : '#94a3b8' }}">
                            <span class="dk-bar-val">{{ $mo->total >= 1000 ? round($mo->total/1000,1).'k' : round($mo->total) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="dk-chart-lbls">
                    @foreach($months as $mo)<span>{{ $mo->month }}</span>@endforeach
                </div>
            </div>

            {{-- Vendeurs par plan + récents --}}
            <div class="dk-card">
                <div class="dk-section-title">
                    <i class="fa-solid fa-layer-group" style="margin-right:4px;"></i>
                    Vendeurs par plan
                </div>
                @foreach($vendorsByPlan as $vp)
                @php
                    $planColors = ['Enterprise'=>'#3b82f6','Business'=>'#0ea5e9','Starter'=>'#f97316'];
                    $color      = $planColors[$vp->plan_name] ?? '#8b5cf6';
                    $maxVendors = $vendorsByPlan->max('vendor_count') ?: 1;
                    $vpct       = round(($vp->vendor_count / $maxVendors) * 100);
                @endphp
                <div class="dk-vp-row">
                    <span class="dk-vp-name">{{ $vp->plan_name }}</span>
                    <div class="dk-vp-bar">
                        <div class="dk-vp-fill" style="width:{{ $vpct }}%; background:{{ $color }}"></div>
                    </div>
                    <span class="dk-vp-count" style="color:{{ $color }}">{{ $vp->vendor_count }}</span>
                </div>
                @endforeach

                <div class="dk-section-title" style="margin-top:1.5rem;">
                    <i class="fa-solid fa-clock-rotate-left" style="margin-right:4px;"></i>
                    Récents vendeurs
                </div>
                @foreach($recentVendors as $p)
                <div class="dk-vendor-item">
                    <div class="dk-avatar">
                        {{ Str::upper(Str::substr($p->user->name ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <div class="dk-vendor-name">{{ $p->user->name ?? 'N/A' }}</div>
                        <div class="dk-vendor-email">{{ $p->user->email ?? '' }}</div>
                    </div>
                    <div class="dk-vendor-plan">{{ $p->plan_name }}</div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>