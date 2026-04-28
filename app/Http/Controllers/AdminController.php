<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PlanLimit;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    public function destroyUser($id)
{
    try {
        DB::transaction(function () use ($id) {
            
            $payment = DB::table('payments')->where('user_id', $id)->first();
            if ($payment) {
                DB::table('company_wallets')->decrement('balance', $payment->amount);
                DB::table('payments')->where('user_id', $id)->delete();
            }
            DB::table('live_sessions')->where('user_id', $id)->delete();
            DB::table('users')->where('id', $id)->delete();
        });

        return back()->with('success', 'Utilisateur et données supprimés, portefeuille mis à jour.');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
}
    



 public function selection()
    {
        return view('admin.selectionpaie');
    }



    

    public function planUsers($plan)
    {
        $users = Payment::where('plan_name', $plan)
            ->where('status', 'accepte')
            ->with('user')
            ->get()
            ->map(function ($payment) {
                return [
                    'id'     => $payment->user->id,
                    'name'   => $payment->user->name,
                    'email'  => $payment->user->email,
                    'plan'   => $payment->plan_name,
                    'amount' => $payment->amount,
                    'date'   => $payment->created_at->format('d/m/Y'),
                ];
            });

        return response()->json($users);
    }

    public function planDetailsjson($id)
    {
        $plan = PlanLimit::findOrFail($id);
        return response()->json($plan);
    }
}
