<?php
// app/Jobs/StartLiveStreamJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StartLiveStreamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300; // Le job peut durer 5 min

    public function __construct(
        public string $sessionKey,
        public string $platform,
        public string $liveUrl,
        public array  $platforms,
    ) {}

    public function handle(): void
    {
        $route = $this->platform === 'tiktok'
            ? url('/tiktok/start')
            : url('/facebook/start');

        // Statut initial : en cours
        Cache::put($this->sessionKey, [
            'status'  => 'running',
            'message' => '',
        ], now()->addMinutes(10));

        try {
            $response = Http::timeout(240)->post($route, [
                'link' => $this->liveUrl,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['error'])) {
                    Cache::put($this->sessionKey, [
                        'status'  => 'error',
                        'message' => $data['message'] ?? 'Erreur inconnue',
                    ], now()->addMinutes(5));
                } else {
                    Cache::put($this->sessionKey, [
                        'status'  => 'success',
                        'message' => 'Live démarré sur ' . $this->platforms[$this->platform]['label'] . ' !',
                    ], now()->addMinutes(10));
                }
            } else {
                Cache::put($this->sessionKey, [
                    'status'  => 'error',
                    'message' => 'Erreur serveur (' . $response->status() . ')',
                ], now()->addMinutes(5));
            }
        } catch (\Exception $e) {
            Cache::put($this->sessionKey, [
                'status'  => 'error',
                'message' => 'Erreur réseau : ' . $e->getMessage(),
            ], now()->addMinutes(5));
        }
    }
}