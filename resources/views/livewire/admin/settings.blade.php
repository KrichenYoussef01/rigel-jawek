<div>

    {{-- ══════════════════════════ FEEDBACK ══════════════════════════ --}}
    @if($successMessage)
        <div class="alert-success-plans" 
             x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 4000)"
             x-show="show"
             x-transition>
            {{ $successMessage }}
        </div>
    @endif

    {{-- ══════════════════════════ CARDS ═════════════════════════════ --}}
    <div class="plans-container">
        <div class="plans-header">
            <h1 class="plans-title">📋 Plans d'abonnement</h1>
            <p class="plans-subtitle">Gérez les offres et leurs fonctionnalités</p>
        </div>

        <div class="plans-grid">
            @foreach($plans as $plan)
            <div class="plan-card {{ $plan->plan_name == 'Business' ? 'popular' : '' }}">
                @if($plan->plan_name == 'Business')
                    <div class="popular-badge">🔥 Plus populaire</div>
                @endif

                <div class="plan-icon">
    @if($plan->plan_name == 'Starter') 
        <i class="fas fa-bolt"></i>
    @elseif($plan->plan_name == 'Business') 
        <i class="fas fa-crown"></i>
    @else 
        <i class="fas fa-rocket"></i>
    @endif
</div>

                <h2 class="plan-name">{{ $plan->plan_name }}</h2>
                <div class="plan-price">
    {{ $plan->prix }} <span>TND/mois</span>
</div>

                <ul class="plan-features">
    <li><i class="fas fa-check-circle text-green-500"></i> {{ $plan->max_lives_par_mois ?? '∞' }} lives / mois</li>
    <li><i class="fas fa-check-circle text-green-500"></i> {{ $plan->max_commandes_par_mois ?? '∞' }} commandes / mois</li>
    <li><i class="fas fa-check-circle text-green-500"></i> {{ $plan->max_comptes_tiktok ?? '∞' }} comptes TikTok</li>
    @if($plan->support_prioritaire)
        <li><i class="fas fa-check-circle text-green-500"></i> Support prioritaire</li>
    @else
        <li class="disabled"><i class="fas fa-times-circle text-gray-300"></i> Support prioritaire</li>
    @endif
    @if($plan->api_personnalisee)
        <li><i class="fas fa-check-circle text-green-500"></i> API personnalisée</li>
    @endif
    @if($plan->manager_de_compte)
        <li><i class="fas fa-check-circle text-green-500"></i> Manager de compte</li>
    @endif
</ul>
                <div class="plan-actions">
                    <button wire:click="openPlanDetailsModal({{ $plan->id }})" class="btn-outline">
    <i class="fas fa-list-ul"></i> Détails
</button>
<button wire:click="openEditModal({{ $plan->id }})" class="btn-primary">
    <i class="fas fa-sliders-h"></i> Modifier
</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════ MODAL DÉTAILS ══════════════════════ --}}
    {{-- ══════════════════════════ MODAL DÉTAILS (lecture seule) ══════════════════════ --}}
@if($showPlanDetailsModal && $selectedPlanId)
    @php
        $plan = $plans->find($selectedPlanId);
    @endphp
    <div class="modal-overlay" wire:click.self="closePlanDetailsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">📋 Détails du plan — {{ $plan->plan_name }}</h2>
                <button wire:click="closePlanDetailsModal" class="modal-close">✕</button>
            </div>

            <div class="modal-body">
                <div class="details-grid-readonly">
                  <div class="detail-readonly">
                        <span class="detail-label"><i class="fas fa-tag"></i> Nom du plan</span>
                        <span class="detail-value">{{ $plan->plan_name }}</span>
                    </div>
                    <div class="detail-readonly">
                        <span class="detail-label"><i class="fas fa-coins"></i> Prix (TND/mois)</span>
                        <span class="detail-value">{{ number_format($plan->prix, 2) }} TND</span>
                    </div>
                    <div class="detail-readonly">
                        <span class="detail-label"><i class="fas fa-video"></i> Lives / mois</span>
                        <span class="detail-value">{{ $plan->max_lives_par_mois ?? '∞ illimité' }}</span>
                    </div>
                    <div class="detail-readonly">
                        <span class="detail-label">Commandes / mois</span>
                        <span class="detail-value">{{ $plan->max_commandes_par_mois ?? '∞ illimité' }}</span>
                    </div>
                    <div class="detail-readonly">
                        <span class="detail-label">Comptes TikTok</span>
                        <span class="detail-value">{{ $plan->max_comptes_tiktok ?? '∞ illimité' }}</span>
                    </div>
                    <div class="detail-readonly">
                        <span class="detail-label">Support prioritaire</span>
                        <span class="badge {{ $plan->support_prioritaire ? 'badge-yes' : 'badge-no' }}">
                            <i class="fas {{ $plan->support_prioritaire ? 'fa-check' : 'fa-times' }}"></i>
                            {{ $plan->support_prioritaire ? 'Activé' : 'Désactivé' }}
                        </span>
                    </div>
                    <div class="detail-readonly">
                        <span class="detail-label">API personnalisée</span>
                        <span class="badge {{ $plan->api_personnalisee ? 'badge-yes' : 'badge-no' }}">
                            {{ $plan->api_personnalisee ? '✅ Activé' : '❌ Désactivé' }}
                        </span>
                    </div>
                    <div class="detail-readonly">
                        <span class="detail-label">Manager de compte</span>
                        <span class="badge {{ $plan->manager_de_compte ? 'badge-yes' : 'badge-no' }}">
                            {{ $plan->manager_de_compte ? '✅ Activé' : '❌ Désactivé' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button wire:click="closePlanDetailsModal" class="btn-secondary">Fermer</button>
            </div>
        </div>
    </div>
@endif

    {{-- ══════════════════════════ MODAL ÉDITION ═══════════════════════ --}}
    @if($showEditModal)
        <div class="modal-overlay" wire:click.self="closeEditModal">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 class="modal-title">⚙️ Modifier le plan</h2>
                    <button wire:click="closeEditModal" class="modal-close">✕</button>
                </div>

                <form wire:submit.prevent="savePlan" class="edit-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom du plan</label>
                            <input type="text" wire:model="editPlanName" class="form-control @error('editPlanName') is-invalid @enderror">
                            @error('editPlanName') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Prix (TND/mois)</label>
                        <input type="number" wire:model="editPrix" step="0.01" 
       class="form-control @error('editPrix') is-invalid @enderror">
@error('editPrix') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Lives / mois</label>
                            <input type="number" wire:model="editMaxLives" class="form-control" placeholder="∞ illimité">
                        </div>
                        <div class="form-group">
                            <label>Commandes / mois</label>
                            <input type="number" wire:model="editMaxCommandes" class="form-control" placeholder="∞ illimité">
                        </div>
                        <div class="form-group">
                            <label>Comptes TikTok</label>
                            <input type="number" wire:model="editMaxComptesTiktok" class="form-control" placeholder="∞ illimité">
                        </div>
                    </div>

                    <div class="options-group">
                        <label class="option-checkbox">
                            <input type="checkbox" wire:model="editSupportPrioritaire">
                            <span>✅ Support prioritaire</span>
                        </label>
                        <label class="option-checkbox">
                            <input type="checkbox" wire:model="editApiPersonnalisee">
                            <span>✅ API personnalisée</span>
                        </label>
                        <label class="option-checkbox">
                            <input type="checkbox" wire:model="editManagerDeCompte">
                            <span>✅ Manager de compte</span>
                        </label>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn-save">
                            <span wire:loading.remove wire:target="savePlan">💾 Enregistrer</span>
                            <span wire:loading wire:target="savePlan">⏳ Enregistrement...</span>
                        </button>
                        <button type="button" wire:click="closeEditModal" class="btn-secondary">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════ STYLES PROFESSIONNELS ══════════════════════════ --}}
    <style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        /* -------------------- Global -------------------- */
        .plans-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        .plans-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .plans-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }

        .plans-subtitle {
            font-size: 0.9rem;
            color: #64748b;
        }

        /* -------------------- Grille des cartes -------------------- */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        /* Carte individuelle */
        .plan-card {
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 1.8rem 1.5rem 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e9eef3;
            transition: all 0.25s ease;
            position: relative;
        }

        .plan-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .plan-card.popular {
            border: 1px solid #6366f1;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.08);
        }

        .popular-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.25rem 1rem;
            border-radius: 30px;
            box-shadow: 0 2px 6px rgba(99, 102, 241, 0.3);
            white-space: nowrap;
        }

        .plan-icon {
            font-size: 3rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .plan-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .plan-price {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 1.5rem;
        }

        .plan-price span {
            font-size: 0.85rem;
            font-weight: 500;
            color: #64748b;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0 0 2rem 0;
            space-y: 0.6rem;
        }

        .plan-features li {
            font-size: 0.85rem;
            padding: 0.4rem 0;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .plan-features li.disabled {
            color: #cbd5e1;
        }

        .plan-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .btn-outline, .btn-primary {
            padding: 0.5rem 1.2rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: transparent;
        }

        .btn-outline {
            border: 1px solid #cbd5e1;
            color: #475569;
            background: white;
        }

        .btn-outline:hover {
            border-color: #6366f1;
            color: #6366f1;
            background: #f8fafc;
        }

        .btn-primary {
            background: #6366f1;
            color: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-primary:hover {
            background: #4f46e5;
            transform: scale(0.98);
        }

        /* -------------------- Alert success -------------------- */
        .alert-success-plans {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        /* -------------------- Modal professionnelle -------------------- */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
        }

        .modal-container {
            background: #ffffff;
            border-radius: 1.5rem;
            width: 100%;
            max-width: 680px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalFadeIn 0.2s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #eef2f6;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .modal-close {
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 40px;
            font-size: 1rem;
            cursor: pointer;
            color: #64748b;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem 1.5rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            border-top: 1px solid #eef2f6;
        }

        /* Grille des détails */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .detail-field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .detail-field label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.03em;
        }

        .field-input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.6rem 0.8rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: #0f172a;
            background: white;
            transition: all 0.2s;
        }

        .field-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Toggle switch */
        .toggle-field {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.3s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #6366f1;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }

        /* Formulaire édition */
        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            padding: 0 0.25rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
        }

        .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            font-size: 0.7rem;
            color: #ef4444;
        }

        .options-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .option-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            cursor: pointer;
            background: #f8fafc;
            padding: 0.4rem 0.9rem;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
        }

        .option-checkbox input {
            accent-color: #6366f1;
            width: 16px;
            height: 16px;
            margin: 0;
        }

        .btn-save {
            background: #6366f1;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-save:hover {
            background: #4f46e5;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .plans-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .details-grid {
                grid-template-columns: 1fr;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
            .modal-container {
                max-width: 95%;
            }
        }
        /* --- Détails lecture seule --- */
.details-grid-readonly {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.2rem;
}

.detail-readonly {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 0.8rem 1rem;
    border: 1px solid #eef2f6;
}

.detail-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.03em;
    margin-bottom: 0.35rem;
}

.detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.badge {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.7rem;
    border-radius: 30px;
}

.badge-yes {
    background: #d1fae5;
    color: #065f46;
}

.badge-no {
    background: #fee2e2;
    color: #991b1b;
}

@media (max-width: 640px) {
    .details-grid-readonly {
        grid-template-columns: 1fr;
    }
}
    </style>
</div>