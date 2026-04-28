<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Payment;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TotalPayments extends Component
{
    use WithPagination;

    public $selectedPlan = null;
    protected $paginationTheme = 'tailwind'; // ou 'bootstrap' selon ton projet

    public function selectPlan($planName)
    {
        $this->selectedPlan = $planName;
        $this->resetPage(); // réinitialise la pagination
    }

    public function closePlanDetails()
    {
        $this->selectedPlan = null;
        $this->resetPage();
    }

    // Propriété dynamique pour les vendeurs du plan sélectionné (paginated)
    public function getVendorsForSelectedPlanProperty()
    {
        if (!$this->selectedPlan) {
            return collect();
        }

        return Payment::where('status', 'accepte')
            ->where('plan_name', $this->selectedPlan)
            ->with('user')
            ->select('user_id', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('user_id')
            ->orderBy('total_amount', 'desc')
            ->paginate(5);
    }

    public function render()
    {
        $statsByPlan = Payment::where('status', 'accepte')
            ->select('plan_name', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('plan_name')
            ->get();

        $totalGlobal = $statsByPlan->sum('total_amount');

        $vendorsByPlan = Payment::where('status', 'accepte')
            ->select('plan_name', DB::raw('COUNT(DISTINCT user_id) as vendor_count'))
            ->groupBy('plan_name')
            ->get();

        $totalVendors  = $vendorsByPlan->sum('vendor_count');
        $totalTransactions = $statsByPlan->sum('count');

        $recentVendors = Payment::where('status', 'accepte')
            ->with('user')->latest()->take(5)->get();

        $months = Payment::where('status', 'accepte')
            ->selectRaw("DATE_FORMAT(created_at, '%b') as month, DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(amount) as total")
            ->where('created_at', '>=', now()->subMonths(5))
            ->groupBy('month_key', 'month')
            ->orderBy('month_key')
            ->get();

        return view('livewire.admin.total-payments', [
            'statsByPlan'      => $statsByPlan,
            'totalGlobal'      => $totalGlobal,
            'vendorsByPlan'    => $vendorsByPlan,
            'totalVendors'     => $totalVendors,
            'totalTransactions'=> $totalTransactions,
            'recentVendors'    => $recentVendors,
            'months'           => $months,
            'vendorsForPlan'   => $this->vendorsForSelectedPlan, // appel dynamique
        ]);
    }
}