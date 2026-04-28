<?php

namespace App\Http\Controllers;

use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncrementController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new PlanLimitService();
    }

    
    public function incrementLive(Request $request)
    {
        $userId = Auth::id();

       
        if (!$this->service->peutLancerLive($userId)) {
            return response()->json([
                'error' => 'Limite lives atteinte',
                'message' => 'Vous avez atteint votre limite de sessions Live.',
            ], 403);
        }

        $this->service->incrementerLive($userId);
        return response()->json(['success' => true]);
    }

    
    public function incrementCommande()
{
    if ($this->service->consommerCommande(Auth::id())) {
        return response()->json(['success' => true]);
    }

    return response()->json([
        'error' => 'Limite atteinte',
        'message' => 'Votre quota de commandes est épuisé.'
    ], 403);
}

    
    public function export(Request $request)
    {
        $userId = Auth::id();

        if (!$this->service->peutExporter($userId)) {
            return response()->json([
                'error'   => 'Limite exports atteinte',
                'message' => 'Vous avez atteint votre limite d\'exports.',
            ], 403);
        }

        $this->service->incrementerExport($userId);
        return response()->json(['success' => true]);
    }
}

