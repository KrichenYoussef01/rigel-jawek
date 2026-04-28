<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PlanLimit;

class PlanDetails extends Component
{
    public $planId;
    public $planName;
   

    protected $rules = [
        'max_lives_par_mois' => 'nullable|integer',
        'max_commandes_par_mois' => 'nullable|integer',
       
    ];

    public function mount($planId)
    {
        $this->planId = $planId;
        $this->loadPlan();
    }

    public function loadPlan()
    {
        $plan = PlanLimit::findOrFail($this->planId);
        $this->planName = $plan->plan_name;
        
    }

    public function updatePlan()
    {
        $this->validate();
        PlanLimit::where('id', $this->planId)->update([
            'max_lives_par_mois' => $this->max_lives_par_mois,
           
        ]);
        session()->flash('message', 'Plan mis à jour avec succès');
        $this->dispatch('closeModal'); 
    }

    public function closeModal()
    {
        $this->dispatch('closeModal');
    }

    public function render()
    {
        return view('livewire.admin.plan-details');
    }
}