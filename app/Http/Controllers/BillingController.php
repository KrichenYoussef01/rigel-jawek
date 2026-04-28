<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PlanLimit;
use Illuminate\Support\Facades\Auth;
class BillingController extends Controller
{
    public function pricing()
{
    $plans = PlanLimit::orderBy('id')->get();
    return view('billing.pricing', compact('plans'));
}
    public function suspended()
{
   
    $suspendedPayment = Payment::where('user_id', Auth::id())
        ->where('status', 'suspendu')
        ->first();

    if (!$suspendedPayment) {
        return redirect()->route('dashboard');
    }

    return view('billing.suspended', [
        'payment' => $suspendedPayment
    ]);
}
   public function checkout($plan)
{
    $planLimit = PlanLimit::where('plan_name', ucfirst($plan))->firstOrFail();

    return view('billing.checkout', [
        'planName'  => $planLimit->plan_name,
        'planPrice' => $planLimit->prix . ' TND',
    ]);
}
    public function index()
{
    $plans = PlanLimit::orderBy('id')->get();
    return view('billing.pricing', compact('plans'));
}

    public function pending()
{
    $payment = Payment::where('user_id', Auth::id())
        ->where('status', 'en_attente')
        ->latest()
        ->first();

    return view('billing.pending', ['payment' => $payment]);
}
}
