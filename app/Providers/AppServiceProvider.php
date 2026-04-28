<?php

namespace App\Providers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Payment;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider

{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        View::composer('*', function ($view) {
        if (Auth::check()) {
            $lastPayment = Payment::where('user_id', Auth::id())
                ->where('status', 'completed')
                ->latest()
                ->first();

            $daysLeft = 0;
            if ($lastPayment) {
                $expiryDate = $lastPayment->created_at->addDays(30);
                $daysLeft = (int) now()->diffInDays($expiryDate, false);
            }
            
            $view->with('daysLeft', $daysLeft > 0 ? $daysLeft : 0);
        }
    });
    }
   
}
