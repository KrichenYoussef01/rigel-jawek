@push('styles')
<link rel="stylesheet" href="{{ asset('css/commentaire/profile.css') }}">
@endpush
<div>
<div class="settings-wrapper">

    <div class="settings-header">
        <h1 class="settings-title">Paramètres</h1>
        <p class="settings-sub">Gérez votre profil et vos informations d'abonnement</p>
    </div>

    <div class="tabs-bar">
        <button wire:click="setTab('profil')"
                class="tab-btn {{ $activeTab === 'profil' ? 'tab-active' : '' }}">
            <i class="fas fa-user-circle tab-icon"></i> Profil
        </button>
        <button wire:click="setTab('abonnement')"
                class="tab-btn {{ $activeTab === 'abonnement' ? 'tab-active' : '' }}">
            <i class="fas fa-box-open tab-icon"></i> Limites abonnement
        </button>
    </div>

    {{-- FEEDBACK --}}
    @if($successMessage)
        <div class="alert-success">
            <i class="fas fa-check-circle mr-2"></i>{{ $successMessage }}
        </div>
    @endif
    @if($errorMessage)
        <div class="alert-error">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ $errorMessage }}
        </div>
    @endif

    @if($activeTab === 'profil')
    <div class="settings-grid">

        <div class="settings-card">
            <div class="card-head">
                <div class="card-icon-wrap" style="background:#fff3e0">
                    <i class="fas fa-address-card" style="color:#f97316;"></i>
                </div>
                <div>
                    <h2 class="card-title">Coordonnées</h2>
                    <p class="card-desc">Mettez à jour votre nom et adresse email</p>
                </div>
            </div>

            <form wire:submit.prevent="updateProfil" class="settings-form">
                <div class="field-group">
                    <label class="field-label">
                        <i class="fas fa-user mr-1 text-gray-400"></i> Nom complet
                    </label>
                    <input type="text" wire:model="name"
                           class="field-input @error('name') input-error @enderror"
                           placeholder="Votre nom">
                    @error('name')
                        <span class="field-error"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">
                        <i class="fas fa-envelope mr-1 text-gray-400"></i> Adresse email
                    </label>
                    <input type="email" wire:model="email"
                           class="field-input @error('email') input-error @enderror"
                           placeholder="votre@email.com">
                    @error('email')
                        <span class="field-error"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-save">
                    <span wire:loading.remove wire:target="updateProfil">
                        <i class="fas fa-save mr-1"></i> Enregistrer les modifications
                    </span>
                    <span wire:loading wire:target="updateProfil">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement...
                    </span>
                </button>
            </form>
        </div>

        <div class="settings-card">
            <div class="card-head">
                <div class="card-icon-wrap" style="background:#e8f5e9">
                    <i class="fas fa-lock" style="color:#16a34a;"></i>
                </div>
                <div>
                    <h2 class="card-title">Mot de passe</h2>
                    <p class="card-desc">Changez votre mot de passe de connexion</p>
                </div>
            </div>

            <form wire:submit.prevent="updatePassword" class="settings-form">
                <div class="field-group">
                    <label class="field-label">
                        <i class="fas fa-key mr-1 text-gray-400"></i> Mot de passe actuel
                    </label>
                    <input type="password" wire:model="current_password"
                           class="field-input @error('current_password') input-error @enderror"
                           placeholder="••••••••" autocomplete="current-password">
                    @error('current_password')
                        <span class="field-error"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">
                        <i class="fas fa-lock mr-1 text-gray-400"></i> Nouveau mot de passe
                    </label>
                    <input type="password" wire:model="new_password"
                           class="field-input @error('new_password') input-error @enderror"
                           placeholder="••••••••" autocomplete="new-password">
                    @error('new_password')
                        <span class="field-error"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">
                        <i class="fas fa-shield-alt mr-1 text-gray-400"></i> Confirmer le mot de passe
                    </label>
                    <input type="password" wire:model="new_password_confirmation"
                           class="field-input @error('new_password_confirmation') input-error @enderror"
                           placeholder="••••••••" autocomplete="new-password">
                    @error('new_password_confirmation')
                        <span class="field-error"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-save btn-green">
                    <span wire:loading.remove wire:target="updatePassword">
                        <i class="fas fa-key mr-1"></i> Changer le mot de passe
                    </span>
                    <span wire:loading wire:target="updatePassword">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Mise à jour...
                    </span>
                </button>
            </form>
        </div>

    </div>
    @endif

    @if($activeTab === 'abonnement')
    <div class="settings-grid">

        {{-- Carte : Plan actuel --}}
        <div class="settings-card">
            <div class="card-head">
                <div class="card-icon-wrap" style="background:#e3f2fd">
                    <i class="fas fa-crown" style="color:#3b82f6;"></i>
                </div>
                <div>
                    <h2 class="card-title">Plan actuel</h2>
                    <p class="card-desc">Détails de votre abonnement en cours</p>
                </div>
            </div>

            @if($planName)
                <div class="plan-badge">
                    <span class="badge-dot"></span>
                    Abonnement actif — {{ $planName }}
                </div>

                <div class="plan-infos">
                    <div class="plan-row">
                        <span class="plan-label">
                            <i class="fas fa-calendar-plus text-indigo-400 mr-1"></i> Date de début
                        </span>
                        <span class="plan-value">{{ $planStart }}</span>
                    </div>
                    <div class="plan-row">
                        <span class="plan-label">
                            <i class="fas fa-calendar-times text-red-400 mr-1"></i> Date d'expiration
                        </span>
                        <span class="plan-value">{{ $planEnd }}</span>
                    </div>
                    <div class="plan-row">
                        <span class="plan-label">
                            <i class="fas fa-hourglass-half text-yellow-400 mr-1"></i> Jours restants
                        </span>
                        <span class="plan-value {{ $this->daysLeft <= 5 ? 'text-danger' : 'text-success' }}">
                            {{ $this->daysLeft }} jour{{ $this->daysLeft > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                @php
                    $dl = $this->daysLeft;
                    $du = $this->daysUsed;
                    $pctAvancement = $dl > 0 ? min(100, ((30 - $dl) / 30) * 100) : 100;
                @endphp
                <div class="progress-wrap">
                    <div class="progress-label">
                        <span><i class="fas fa-chart-line mr-1"></i> Avancement</span>
                        <span>{{ $du }}/30 jours</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $pctAvancement }}%"></div>
                    </div>
                </div>

            @else
                <div class="no-plan">
                    <i class="fas fa-exclamation-triangle no-plan-icon" style="font-size:32px;color:#f59e0b;"></i>
                    <p>Aucun abonnement actif.</p>
                    <a href="{{ route('pricing') }}" class="btn-save"
                       style="display:inline-block;text-decoration:none;text-align:center;margin-top:12px;">
                        <i class="fas fa-rocket mr-1"></i> Choisir un plan
                    </a>
                </div>
            @endif
        </div>

        {{-- Carte : Utilisation du mois --}}
        <div class="settings-card">
            <div class="card-head">
                <div class="card-icon-wrap" style="background:#fce4ec">
                    <i class="fas fa-chart-bar" style="color:#e11d48;"></i>
                </div>
                <div>
                    <h2 class="card-title">Utilisation du mois</h2>
                    <p class="card-desc">{{ now()->format('F Y') }}</p>
                </div>
            </div>

            {{-- Lives --}}
            @php
                $pctLives = $limitLives
                    ? min(100, ($totalLives / max(1, $limitLives)) * 100)
                    : ($totalLives > 0 ? min(100, ($totalLives / 100) * 100) : 0);
            @endphp
            <div class="limit-row">
                <div class="limit-info">
                    <span class="limit-name">
                        <i class="fas fa-video text-red-400 mr-1"></i> Lives analysés
                    </span>
                    <span class="limit-count">{{ $totalLives }} / {{ $limitLives ?? '∞' }}</span>
                </div>
                <div class="limit-bar">
                    <div class="limit-fill {{ $pctLives >= 90 ? 'fill-danger' : ($pctLives >= 70 ? 'fill-warning' : 'fill-ok') }}"
                         style="width: {{ $pctLives }}%"></div>
                </div>
            </div>

            {{-- Commandes --}}
            @php
                $pctCmd = $limitCommandes
                    ? min(100, ($totalCommandes / max(1, $limitCommandes)) * 100)
                    : ($totalCommandes > 0 ? min(100, ($totalCommandes / 5000) * 100) : 0);
            @endphp
            <div class="limit-row">
                <div class="limit-info">
                    <span class="limit-name">
                        <i class="fas fa-shopping-cart text-blue-400 mr-1"></i> Commandes
                    </span>
                    <span class="limit-count">{{ $totalCommandes }} / {{ $limitCommandes ?? '∞' }}</span>
                </div>
                <div class="limit-bar">
                    <div class="limit-fill {{ $pctCmd >= 90 ? 'fill-danger' : ($pctCmd >= 70 ? 'fill-warning' : 'fill-ok') }}"
                         style="width: {{ $pctCmd }}%"></div>
                </div>
            </div>

            {{-- Commentaires --}}
            @php $pctArticles = $totalArticles > 0 ? min(100, ($totalArticles / 1000) * 100) : 0; @endphp
            <div class="limit-row">
                <div class="limit-info">
                    <span class="limit-name">
                        <i class="fas fa-comments text-purple-400 mr-1"></i> Commentaires
                    </span>
                    <span class="limit-count">{{ $totalArticles }}</span>
                </div>
                <div class="limit-bar">
                    <div class="limit-fill fill-ok" style="width: {{ $pctArticles }}%"></div>
                </div>
            </div>

            {{-- Exports --}}
            @php $pctExports = $totalExports > 0 ? min(100, ($totalExports / 100) * 100) : 0; @endphp
            <div class="limit-row">
                <div class="limit-info">
                    <span class="limit-name">
                        <i class="fas fa-file-export text-emerald-400 mr-1"></i> Exports
                    </span>
                    <span class="limit-count">{{ $totalExports }}</span>
                </div>
                <div class="limit-bar">
                    <div class="limit-fill fill-ok" style="width: {{ $pctExports }}%"></div>
                </div>
            </div>

            {{-- ✅ Alertes : basées sur $this->daysLeft (valeur calculée) --}}
            @php $dl = $this->daysLeft; @endphp

            @if($dl > 0 && $dl <= 5)
                <div class="expiry-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Abonnement expire dans {{ $dl }} jour{{ $dl > 1 ? 's' : '' }}.
                    <a href="{{ route('pricing') }}" class="renew-link">
                        <i class="fas fa-redo mr-1"></i> Renouveler
                    </a>
                </div>
            @elseif($dl <= 0 && !$planName)
                {{-- Expiré seulement si pas de plan actif --}}
                <div class="expiry-danger">
                    <i class="fas fa-times-circle mr-1"></i> Abonnement expiré.
                    <a href="{{ route('pricing') }}" class="renew-link">
                        <i class="fas fa-redo mr-1"></i> Renouveler
                    </a>
                </div>
            @endif
        </div>

    </div>
    @endif

</div>
</div>