<?php

namespace App\Livewire;

use Livewire\Component;

class AdminDashboard extends Component
{
    public $platform = 'facebook';
    public $liveUrl = '';
    public $errorMessage = null;
    public $section = 'payments'; // ← ADD THIS (default section)

    protected $rules = [
        'platform' => 'required|in:facebook,tiktok',
        'liveUrl'  => 'required|url',
    ];

    public function setSection(string $section): void // ← ADD THIS
    {
        $this->section = $section;
    }

    public function startLive()
    {
        $this->validate();

        $this->dispatchBrowserEvent('triggerExtraction', [
            'platform' => $this->platform,
            'url'      => $this->liveUrl,
        ]);
    }

    public function stopLive()
    {
        $this->dispatchBrowserEvent('stopExtraction');
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}