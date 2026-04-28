<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $section = 'accueil'; // Onglet par défaut

    public function setSection($section)
    {
        $this->section = $section;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}