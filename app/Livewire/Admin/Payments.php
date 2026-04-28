<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class Payments extends Component
{
    use WithPagination;

    public $selectedStatus = 'en_attente';

    protected $queryString = ['selectedStatus'];

    public function setStatus($status)
    {
        $this->selectedStatus = $status;
        $this->resetPage();
    }

    public function render()
    {
       
        $payments = Payment::with(['user.usageCounters'])
            ->when($this->selectedStatus, function ($query) {
                return $query->where('status', $this->selectedStatus);
            })
            ->latest()
            ->paginate(10);

        
        $acceptedUsers = Payment::with(['user.usageCounters'])
            ->where('status', 'accepte')
            ->get()
            ->map(function ($payment) {
                $user = $payment->user;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'plan' => $payment->plan_name,
                    'createdAt' => $user->created_at->format('d/m/Y'),
                    'totalLives' => $user->usageCounters->sum('nb_lives_utilises'),
                    'totalCommands' => $user->usageCounters->sum('nb_commandes_utilises'),
                    'totalComments' => $user->usageCounters->sum('nb_commentaires_utilises'),
                    'totalExports' => $user->usageCounters->sum('nb_exports_utilises'),
                    'monthlyUsage' => $user->usageCounters->sortByDesc('mois')->map(function ($counter) {
                        return [
                            'month' => $counter->mois,
                            'lives' => $counter->nb_lives_utilises,
                            'commands' => $counter->nb_commandes_utilises,
                            'comments' => $counter->nb_commentaires_utilises,
                            'exports' => $counter->nb_exports_utilises,
                        ];
                    })->values()->toArray(),
                ];
            });

        return view('livewire.admin.payments', [
            'payments' => $payments,
            'acceptedUsers' => $acceptedUsers,
        ]);
    }
}