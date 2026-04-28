<?php

namespace App\Livewire\Util;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
class History extends Component
{
    use WithPagination;

    public $search = '';
    public $platform = ''; 
    public $showDetails = false;
    public $currentDetails = [];
    public $currentPlatform = '';
    protected $queryString = ['search', 'platform'];
    public $detailMode = false;
    public $currentSessionId = null;
    public $currentSession = null;
    public $currentBaskets = [];
    
public $dateFrom = '';
public $dateTo   = '';
public $sortBy   = 'started_at';
public $sortDir  = 'desc';


public function resetFilters()
{
    $this->search   = '';
    $this->platform = '';
    $this->dateFrom = '';
    $this->dateTo   = '';
    $this->sortBy   = 'started_at';
    $this->sortDir  = 'desc';
}
    public function render()
{
    if ($this->detailMode) {
        return view('livewire.util.history', [
            'sessions' => collect(),
            'session'  => $this->currentSession,
            'baskets'  => $this->currentBaskets,
            'platform' => $this->currentPlatform,
        ]);
    }

    $userId = Auth::id();

    $tiktok = DB::table('live_sessions')
        ->where('user_id', $userId)
        ->select(
            'id',
            'tiktok_link as link',
            'started_at', 'ended_at',
            'total_comments as comments_count',
            'total_clients as clients_count',
            'total_articles as articles_count',
            'total_phones as phones_count',
            DB::raw("'TikTok' as platform")
        );

    $facebook = DB::table('facebook_sessions')
        ->where('user_id', $userId)
        ->select(
            'id',
            'live_link as link',
            'started_at', 'ended_at',
            'total_comments as comments_count',
            'total_clients as clients_count',
            'total_articles as articles_count',
            'total_phones as phones_count',
            DB::raw("'Facebook' as platform")
        );

    if ($this->search) {
        $tiktok->where('tiktok_link', 'like', '%' . $this->search . '%');
        $facebook->where('live_link', 'like', '%' . $this->search . '%');
    }


if ($this->platform === 'TikTok') {
    $query = $tiktok;
} elseif ($this->platform === 'Facebook') {
    $query = $facebook;
} else {
    $query = $tiktok->union($facebook);
}


$all = $query->get();

if ($this->dateFrom) {
    $all = $all->filter(fn($s) => $s->started_at >= $this->dateFrom);
}
if ($this->dateTo) {
    $all = $all->filter(fn($s) => $s->started_at <= $this->dateTo . ' 23:59:59');
}


$all = $this->sortDir === 'asc'
    ? $all->sortBy($this->sortBy)
    : $all->sortByDesc($this->sortBy);


$page     = request()->get('page', 1);
$perPage  = 10;
$sessions = new \Illuminate\Pagination\LengthAwarePaginator(
    $all->slice(($page - 1) * $perPage, $perPage)->values(),
    $all->count(),
    $perPage,
    $page,
    ['path' => request()->url()]
);

return view('livewire.util.history', [
    'sessions' => $sessions,
]);
}

    public function viewDetails($id, $platform)
    {
        session(['history_detail_platform' => $platform, 'history_detail_id' => $id]);
        return redirect()->route('history.detail', ['platform' => $platform, 'id' => $id]);
    }
    public function deleteSession($id = null)
{
    $targetId = $id ?? $this->currentSessionId;
    if (!$targetId) {
        return;
    }

    
    $isTikTok = DB::table('live_sessions')->where('id', $targetId)->exists();
    $isFacebook = DB::table('facebook_sessions')->where('id', $targetId)->exists();

    if ($isTikTok) {
        DB::table('live_baskets')->where('live_session_id', $targetId)->delete();
        DB::table('live_sessions')->where('id', $targetId)->delete();
    } elseif ($isFacebook) {
        DB::table('live_baskets_fb')->where('facebook_session_id', $targetId)->delete();
        DB::table('facebook_sessions')->where('id', $targetId)->delete();
    } else {
        session()->flash('error', 'Session introuvable.');
        return;
    }


    $this->detailMode = false;
    $this->currentSession = null;
    $this->currentBaskets = [];
    $this->currentSessionId = null;
    $this->currentPlatform = '';
}
public function refreshSessions()
{
    // Rafraîchit la liste des sessions sans modifier les filtres
    $this->resetPage(); // Remet à la première page
    // Forcer le rechargement des données via la méthode render()
    // Le composant se rafraîchira automatiquement
}    
public function showDetails($id, $platform)
    {
        $this->currentPlatform = $platform;
        $this->showDetails = true;

        if ($platform === 'TikTok') {
            $this->currentDetails = DB::table('live_baskets')
                ->where('live_session_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        } else {
            $this->currentDetails = DB::table('live_baskets_fb')
                ->where('facebook_session_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        }
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->currentDetails = [];
        $this->currentPlatform = '';
    }
    public function loadDetail($id, $platform)
{
    $this->detailMode = true;
    $this->currentSessionId = $id;
    $this->currentPlatform = $platform;

    if ($platform === 'TikTok') {
        $this->currentSession = DB::table('live_sessions')->where('id', $id)->first();
        $baskets = DB::table('live_baskets')->where('live_session_id', $id)->get();
    } else {
        $this->currentSession = DB::table('facebook_sessions')->where('id', $id)->first();
        $baskets = DB::table('live_baskets_fb')->where('facebook_session_id', $id)->get();
    }

    // ✅ Correction : transformer chaque basket proprement
    $this->currentBaskets = $baskets->map(function ($basket) {
        $basket->articles = is_string($basket->articles)
            ? (json_decode($basket->articles, true) ?? [])
            : ($basket->articles ?? []);

        $basket->phones = is_string($basket->phones)
            ? (json_decode($basket->phones, true) ?? [])
            : ($basket->phones ?? []);

        return $basket;
    });
}
}