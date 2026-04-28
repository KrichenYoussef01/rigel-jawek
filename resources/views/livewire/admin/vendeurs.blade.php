<div>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/vendeurs.css') }}">
    @endpush

    <div class="max-w-7xl mx-auto px-4 py-10">

        {{-- ====== HEADER ====== --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold">💳 Gestion des Paiements</h1>
                <p class="text-gray-400 text-sm mt-1">Filtrez et gérez les demandes d'abonnement</p>
            </div>
        </div>

        {{-- ====== STATS CARDS (style onglets) ====== --}}
        <nav class="flex space-x-8" style="border-bottom: none !important; margin-bottom: 1.5rem;">
            {{-- Total --}}
            <button wire:click="setFilter('all')"
                class="tab-button {{ !$status && !$plan && $period === 'all' && !$date_from && !$date_to ? 'active' : '' }}">
                <span>Total paiements</span>
                <span>{{ $totalCount ?? $payments->total() }}</span>
            </button>

            {{-- Acceptés --}}
            <button wire:click="setStatusFilter('accepte')"
                class="tab-button {{ $status === 'accepte' ? 'active' : '' }}">
                <span>Acceptés</span>
                <span>{{ $acceptedCount ?? $payments->where('status','accepte')->count() }}</span>
            </button>

            {{-- En attente --}}
            <button wire:click="setStatusFilter('en_attente')"
                class="tab-button {{ $status === 'en_attente' ? 'active' : '' }}">
                <span>En attente</span>
                <span>{{ $pendingCount ?? $payments->where('status','en_attente')->count() }}</span>
            </button>

            {{-- Refusés --}}
            <button wire:click="setStatusFilter('refuse')"
                class="tab-button {{ $status === 'refuse' ? 'active' : '' }}">
                <span>Refusés</span>
                <span>{{ $refusedCount ?? $payments->where('status','refuse')->count() }}</span>
            </button>
        </nav>

        {{-- ====== FILTRES ====== --}}
        <div class="glass-card p-5 mb-6" style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 1rem;">
            <div class="flex flex-col md:flex-row gap-4 items-end flex-wrap">

                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Période rapide</label>
                    <div class="flex gap-2 flex-wrap">
                        <button wire:click="$set('period','today')"
                            class="filter-btn {{ $period === 'today' ? 'active' : '' }}">Aujourd'hui</button>
                        <button wire:click="$set('period','week')"
                            class="filter-btn {{ $period === 'week' ? 'active' : '' }}">Cette semaine</button>
                        <button wire:click="$set('period','month')"
                            class="filter-btn {{ $period === 'month' ? 'active' : '' }}">Ce mois</button>
                        <button wire:click="$set('period','all')"
                            class="filter-btn {{ $period === 'all' ? 'active' : '' }}">Tout</button>
                    </div>
                </div>

                <div class="flex gap-3 items-end">
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Du</label>
                        <input type="date" wire:model.live="date_from"
                            class="bg-white border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Au</label>
                        <input type="date" wire:model.live="date_to"
                            class="bg-white border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Statut</label>
                    <select wire:model.live="status"
                        class="bg-white border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:border-indigo-500">
                        <option value="">Tous les statuts</option>
                        <option value="accepte">✅ Acceptés</option>
                        <option value="en_attente">⏳ En attente</option>
                        <option value="refuse">❌ Refusés</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Plan</label>
                    <select wire:model.live="plan"
                        class="bg-white border border-gray-300 rounded-xl px-3 py-2 text-sm text-gray-800 outline-none focus:border-indigo-500">
                        <option value="">Tous les plans</option>
                        <option value="Starter">Starter</option>
                        <option value="Business">Business</option>
                        <option value="Premium">Premium</option>
                    </select>
                </div>

                @if($status || $plan || $period !== 'all' || $date_from || $date_to)
                <button wire:click="resetFilters"
                    class="text-xs text-gray-500 hover:text-red-500 transition underline py-2">
                    Réinitialiser
                </button>
                @endif

            </div>
        </div>

        {{-- ====== TABLEAU ====== --}}
        <div class="overflow-hidden" style="background: white; border: 1px solid #e5e7eb; border-radius: 1rem;">
            <div class="overflow-x-auto">
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Plan</th>
                            <th>Montant</th>
                            <th>Méthode</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                            <th>Payé</th>
                            <th style="text-align: right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        @php
                            $initials = strtoupper(substr($payment->user->name ?? 'U', 0, 1));
                            if(str_contains($payment->user->name ?? '', ' ')) {
                                $parts = explode(' ', $payment->user->name);
                                $initials = strtoupper(substr($parts[0],0,1).substr($parts[1] ?? '',0,1));
                            }
                        @endphp
                        <tr>
                            <td data-initial="{{ $initials }}">
                                {{ $payment->user->name ?? '—' }}
                                <div class="text-xs text-gray-500">{{ $payment->user->email ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="font-mono text-indigo-600 font-bold text-xs">{{ $payment->plan_name }}</span>
                            </td>
                            <td class="font-semibold text-indigo-600">
                                {{ number_format($payment->amount, 2) }} TND
                            </td>
                            <td class="text-gray-600 capitalize">
                                {{ $payment->payment_method ?? '—' }}
                            </td>
                            <td class="text-xs">
                                <div class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</div>
                                <div class="text-gray-500">{{ \Carbon\Carbon::parse($payment->created_at)->format('H:i') }}</div>
                            </td>
                            <td class="text-xs">
                                @if($payment->expires_at)
                                    @php
                                        $expires = \Carbon\Carbon::parse($payment->expires_at);
                                        $isExpired = $expires->isPast();
                                        $daysLeft = max(0, (int) now()->diffInDays($expires, false));
                                    @endphp
                                    <div class="{{ $isExpired ? 'text-red-600' : 'text-emerald-600' }} font-semibold">
                                        {{ $expires->format('d/m/Y') }}
                                    </div>
                                    @if($isExpired)
                                        <span class="inline-block mt-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Expiré</span>
                                    @elseif($daysLeft <= 5)
                                        <span class="inline-block mt-1 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ $daysLeft }}j restants</span>
                                    @else
                                        <span class="inline-block mt-1 text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">{{ $daysLeft }}j restants</span>
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge 
                                    @if($payment->status == 'accepte') status-accepted
                                    @elseif($payment->status == 'en_attente') status-pending
                                    @elseif($payment->status == 'refuse') status-refused
                                    @endif">
                                    {{ $payment->status == 'accepte' ? 'Accepté' : ($payment->status == 'en_attente' ? 'En attente' : 'Refusé') }}
                                </span>
                            </td>
                            <td>
                                @if(in_array($payment->payment_method, ['card','carte']))
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Payé</span>
                                @else
                                    <form action="{{ route('admin.payments.togglePaid', $payment->id) }}" method="POST">
                                        @csrf
                                        @if($payment->is_paid)
                                            <button type="submit" onclick="return confirm('Marquer comme NON payé ?')"
                                                class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-green-700 transition">
                                                Payé
                                            </button>
                                        @else
                                            <button type="submit" onclick="return confirm('Confirmer la réception des espèces ?')"
                                                class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-xs font-semibold hover:bg-indigo-700 transition">
                                                En attente
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </td>
                            <td style="text-align: right">
                                <form action="{{ route('admin.vendeurs.destroy', $payment->user_id) }}" method="POST"
                                    onsubmit="return confirm('Supprimer ce vendeur ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-50 text-red-600 border border-red-200 rounded-lg px-3 py-1.5 text-xs font-semibold hover:bg-red-600 hover:text-white transition">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-12 text-gray-500">
                                <div class="text-4xl mb-2">🔍</div>
                                Aucun paiement trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
            @endif
        </div>

    </div>
</div>