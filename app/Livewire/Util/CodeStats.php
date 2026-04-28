<?php

namespace App\Livewire\Util;

use Livewire\Component;

class CodeStats extends Component
{
    public array $labels = [];
    public array $data   = [];
    public bool  $hasLiveData = false;

   
    #[\Livewire\Attributes\On('updateLiveStats')]
    public function updateLiveStats(array $labels = [], array $data = []): void
    {
        if (empty($labels)) return;

        $this->labels      = array_values($labels);
        $this->data        = array_values($data);
        $this->hasLiveData = true;

        $this->dispatch('chartReady', labels: $this->labels, data: $this->data);
    }

    public function render()
    {
        return view('livewire.util.code-stats');
    }
}