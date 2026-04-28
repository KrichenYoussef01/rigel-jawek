@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/payments.css') }}">
@endpush

<div>  {{-- ← div racine Livewire --}}

    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="{{ route('admin.selection') }}" class="text-gray-500 hover:text-gray-700 transition">
                    ← Retour
                </a>
            </div>
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-800">Gestion des Paiements</h1>
                <p class="text-gray-500">Approbation et suivi des abonnements</p>
            </div>
            <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg font-bold">
                Total : {{ $payments->total() }}
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl text-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- Onglets --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex space-x-8">
                @php
                    $statuses = [
                        'en_attente' => 'En attente',
                        'accepte'    => 'Accepté',
                        'refuse'     => 'Refusé',
                        'suspendu'   => 'Suspendu',
                    ];
                @endphp
                @foreach($statuses as $statusKey => $statusLabel)
                    <button wire:click="setStatus('{{ $statusKey }}')"
                            class="tab-button pb-3 px-1 text-sm font-medium {{ $selectedStatus === $statusKey ? 'active text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $statusLabel }}
                        <span class="ml-2 bg-gray-100 text-gray-600 rounded-full px-2 py-0.5 text-xs">
                            {{ $payments->where('status', $statusKey)->count() }}
                        </span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Montant</th>
                        <th>Plan</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments->where('status', $selectedStatus) as $payment)
                        <tr>
                            <td class="font-medium">{{ $payment->user->name }}</td>
                            <td>{{ $payment->amount }} TND</td>
                            <td>{{ $payment->plan_name }}</td>
                            <td>
                                @php
                                    $badgeClass = match($payment->status) {
                                        'en_attente' => 'status-pending',
                                        'accepte'    => 'status-accepted',
                                        'refuse'     => 'status-refused',
                                        'suspendu'   => 'status-suspended',
                                        default      => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="status-badge {{ $badgeClass }}">
                                    {{ $statuses[$payment->status] ?? $payment->status }}
                                </span>
                            </td>
                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="flex gap-2 flex-wrap">
                                    @if($payment->status === 'en_attente')
                                        <form method="POST" action="{{ route('admin.payments.accept', $payment->id) }}">
                                            @csrf
                                            <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold">
                                                Accepter
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.payments.refuse', $payment->id) }}">
                                            @csrf
                                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold">
                                                Refuser
                                            </button>
                                        </form>
                                    @elseif($payment->status === 'accepte')
                                        <button onclick="openUserDetails({{ $payment->user->id }})"
                                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs font-semibold">
                                            Détails
                                        </button>
                                        <form method="POST" action="{{ route('admin.payments.refuse', $payment->id) }}">
                                            @csrf
                                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold">
                                                Refuser
                                            </button>
                                        </form>
                                    @elseif($payment->status === 'suspendu')
                                        <form method="POST" action="{{ route('admin.payments.restore', $payment->id) }}">
                                            @csrf
                                            <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-xs font-semibold">
                                                Réactiver
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                Aucun paiement avec ce statut.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    </div>

    {{-- MODALE --}}
    <div id="userDetailsModal"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4"
         onclick="closeUserDetailsIfClickedOutside(event)">
        <div class="modal-white">
            <div class="modal-header">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Détails de l'utilisateur</h2>
                    <button onclick="closeUserDetails()" class="text-gray-400 hover:text-gray-600 text-2xl leading-5">&times;</button>
                </div>
                <p id="modalUserName" class="text-sm text-gray-500 mt-1"></p>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="stat-card-white">
                        <div class="text-2xl mb-1">🎥</div>
                        <div class="text-2xl font-bold text-indigo-600" id="modalLiveCount">0</div>
                        <div class="text-xs text-gray-500 mt-1">Lives utilisés</div>
                    </div>
                    <div class="stat-card-white">
                        <div class="text-2xl mb-1">📦</div>
                        <div class="text-2xl font-bold text-emerald-600" id="modalCommandCount">0</div>
                        <div class="text-xs text-gray-500 mt-1">Commandes</div>
                    </div>
                    <div class="stat-card-white">
                        <div class="text-2xl mb-1">💬</div>
                        <div class="text-2xl font-bold text-blue-600" id="modalCommentCount">0</div>
                        <div class="text-xs text-gray-500 mt-1">Commentaires</div>
                    </div>
                    <div class="stat-card-white">
                        <div class="text-2xl mb-1">📤</div>
                        <div class="text-2xl font-bold text-orange-600" id="modalExportCount">0</div>
                        <div class="text-xs text-gray-500 mt-1">Exports</div>
                    </div>
                </div>

                <div class="detail-row">
                    <h3 class="font-semibold text-gray-700 mb-3">📋 Informations de contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-500">Nom complet</div>
                            <div class="font-medium text-gray-800" id="modalFullName">-</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Email</div>
                            <div class="font-medium text-indigo-600" id="modalEmail">-</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Plan actuel</div>
                            <div class="font-medium text-green-600" id="modalPlan">-</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500">Inscription</div>
                            <div class="font-medium text-gray-800" id="modalCreatedAt">-</div>
                        </div>
                    </div>
                </div>

                <div class="detail-row">
                    <h3 class="font-semibold text-gray-700 mb-3">📊 Historique mensuel</h3>
                    <div id="modalMonthlyHistory" class="space-y-3"></div>
                </div>
            </div>
            <div class="bg-gray-50 p-4 rounded-b-lg flex justify-end gap-3">
                <button onclick="closeUserDetails()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold">
                    Fermer
                </button>
                <button onclick="sendMessageToUser()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
                    💬 Envoyer un message
                </button>
            </div>
        </div>
    </div>

</div>  {{-- ← fin div racine Livewire --}}

@push('scripts')
<script>
    window.usersData = {
        @foreach($acceptedUsers as $user)
            "{{ $user['id'] }}": @json($user)@if(!$loop->last),@endif
        @endforeach
    };
</script>
<script src="{{ asset('js/admin/payments.js') }}"></script>
@endpush