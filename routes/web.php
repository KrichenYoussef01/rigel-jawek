<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\IncrementController;
use App\Http\Controllers\LiveSessionController;
use App\Http\Controllers\PayementController;
use App\Http\Controllers\SelectionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TikTokController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

/*─── PUBLIQUES ─────────────────────────────────────────*/
Route::get('/',              [SelectionController::class, 'index'])->name('selection');


Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');  // ✅ manquait
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);

/*─── ADMIN AUTH ─────────────────────────────────────────*/
//Route::get('/x9k2-admin-2026',         [AuthController::class, 'showPinForm'])->name('admin.secret');
Route::post('/x9k2-admin-2026/verify', [AuthController::class, 'verifyPin'])->name('admin.pin.verify');
Route::get('/x9k2-admin-2026',             [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login',            [AuthController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/admin/logout',           [AuthController::class, 'adminLogout'])->name('admin.logout');

/*─── VENDEUR AUTHENTIFIÉ ────────────────────────────────*/
Route::middleware('auth')->group(function () {
    Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'showDashboard'])->name('dashboard');
    Route::get('/pricing',          [BillingController::class, 'pricing'])->name('pricing');
    Route::get('/checkout/{plan}',  [BillingController::class, 'checkout'])->name('checkout');
    Route::get('/payment/pending',  [BillingController::class, 'pending'])->name('payment.pending');
    Route::post('/payment/process', [PayementController::class, 'store'])->name('payment.process');
    Route::get('/payment/suspended',[BillingController::class, 'suspended'])->name('payment.suspended');
    Route::get('/api/plan-limits',         [PayementController::class, 'getLimits']);
    Route::post('/api/increment-live',     [IncrementController::class, 'incrementLive']);
    Route::post('/api/increment-export',   [IncrementController::class, 'export']);
    Route::post('/api/increment-commande', [IncrementController::class, 'incrementCommande']);
    Route::get('/user/codes', function () {
        return response()->json(App\Models\Code::where('user_id', Auth::id())->pluck('code'));
    });
    Route::delete('/session/{id}', [LiveSessionController::class, 'destroy']);
});

/*─── TIKTOK ─────────────────────────────────────────────*/
Route::post('/tiktok/start',        [TikTokController::class, 'start'])->middleware('auth');
Route::get('/tiktok/comments',      [TikTokController::class, 'comments']);
Route::post('/tiktok/end',          [TikTokController::class, 'endLive'])->name('tiktok.end');
Route::post('/end-live',            [LiveSessionController::class, 'endLive'])->name('live.end'); // ✅ un seul
Route::get('/session-details/{id}', [TikTokController::class, 'showDetails'])->name('session_details');
Route::get('/tiktok/live',          [LiveSessionController::class, 'index'])->name('tiktok.live')->middleware('auth');

/*─── FACEBOOK ───────────────────────────────────────────*/

Route::post('/facebook/start',          [FacebookController::class, 'start'])->name('fb.start');
Route::get('/facebook/comments',        [FacebookController::class, 'comments']);
Route::post('/facebook/stop',           [FacebookController::class, 'stop']);

/*─── AI ─────────────────────────────────────────────────*/
Route::post('/ai/analyze', [AIController::class, 'analyze']);

/*─── ADMIN ──────────────────────────────────────────────*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/selection',                       [AdminController::class, 'selection'])->name('selection');
    
    
    Route::post('/payments/{id}/accept',           [PayementController::class, 'accept'])->name('payments.accept');
    Route::post('/payments/{id}/refuse',           [PayementController::class, 'refuse'])->name('payments.refuse');
    Route::post('/payments/{payment}/toggle-paid', [PayementController::class, 'togglePaid'])->name('payments.togglePaid');
    Route::post('/payments/{payment}/restore',     [PayementController::class, 'restore'])->name('payments.restore');
    Route::delete('/vendeurs/{id}',                [AdminController::class, 'destroyUser'])->name('vendeurs.destroy');
    Route::get('/plan-users/{plan}',               [AdminController::class, 'planUsers'])->name('plan.users.json');
    Route::get('/plan-details/{id}',               [AdminController::class, 'planDetails'])->name('plan.details.json');
    Route::get('/history',                         [TikTokController::class, 'history'])->name('history');
    Route::delete('/sessions/{id}',                [TikTokController::class, 'destroy'])->name('session.delete');
});
Route::get('/admin/plans/{id}', [AdminController::class, 'planDetails'])->name('admin.plans.details');
// Limites abonnement
Route::middleware('auth')->group(function () {
    Route::post('/increment/live',     [IncrementController::class, 'incrementLive']);
    Route::post('/increment/commande', [IncrementController::class, 'incrementCommande']);
    Route::post('/increment/export',   [IncrementController::class, 'export']);
});
Route::get('/mot-de-passe-oublie',  [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/mot-de-passe-oublie', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

// Réinitialisation
Route::get('/reinitialiser/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reinitialiser',        [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('/debug-expiry', function () {
    $user =Auth::user();
    if (!$user) return 'Non connecté';
    $payment = \App\Models\Payment::where('user_id', $user->id)
                ->where('status', 'accepte')
                ->latest()->first();
    if (!$payment) return 'Pas de paiement';
    
    $expires = \Carbon\Carbon::parse($payment->expires_at);
    $now = \Carbon\Carbon::now();
    return [
        'expires_at_raw' => $payment->expires_at,
        'expires_tz' => $expires->timezoneName,
        'now_tz' => $now->timezoneName,
        'diff_days' => $now->startOfDay()->diffInDays($expires->startOfDay()),
        'is_future' => $expires->isFuture(),
    ];
});