<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Payment;
use Livewire\WithPagination;

class Vendeurs extends Component
{
    use WithPagination;

    public $status = '';
    public $plan = '';
    public $period = 'all';
    public $date_from = '';
    public $date_to = '';

    public function render()
    {
        $query = Payment::with('user')->latest();

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->plan) {
            $query->where('plan_name', $this->plan);
        }

        if ($this->period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($this->period === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->period === 'month') {
            $query->whereMonth('created_at', now()->month);
        } elseif ($this->period === 'custom') {
            if ($this->date_from) $query->whereDate('created_at', '>=', $this->date_from);
            if ($this->date_to)   $query->whereDate('created_at', '<=', $this->date_to);
        }

        $payments = $query->paginate(15);

        return view('livewire.admin.vendeurs', [
            'payments' => $payments,
        ]);
    }

    public function resetFilters()
    {
        $this->status = '';
        $this->plan = '';
        $this->period = 'all';
        $this->date_from = '';
        $this->date_to = '';
        $this->resetPage();
    }
}