<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BasketDetails extends Component
{
    public $sessionId;
    public $platform; 
    public $baskets = [];

    public function mount($sessionId, $platform)
    {
        $this->sessionId = $sessionId;
        $this->platform = $platform;

        if ($platform === 'TikTok') {
            $this->baskets = DB::table('live_baskets')
                ->where('live_session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->baskets = DB::table('live_baskets_fb')
                ->where('facebook_session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->get();
        }

    
        foreach ($this->baskets as $basket) {
            $basket->articles = json_decode($basket->articles, true) ?? [];
            $basket->phones   = json_decode($basket->phones, true) ?? [];
        }
    }

    public function render()
    {
        return view('livewire.basket-details', [
            'baskets' => $this->baskets,
        ]);
    }
    
}