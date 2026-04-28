<?php

namespace App\Livewire;

use Livewire\Component;

class ArticleSalesStats extends Component
{
    public $salesData = []; 

    protected $listeners = ['updateSalesStats' => 'updateData'];

    public function updateData($data)
    {
        $this->salesData = $data;
    }

    public function render()
    {
        return view('livewire.article-sales-stats');
    }
}