<?php 
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Code;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
   
    public function showPinForm()
{
    
    if (Auth::check() && Auth::user()->role  === 'vendeur') {
        return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous n\'avez pas les droits admin.');
    }

    return view('auth.admin-pin');
}
    
  
    public function verifyPin(Request $request)
    {
        $pin = config('app.admin_pin'); 
        $attempts = session('pin_attempts', 0);

        if ($attempts >= 3) {
            return back()->withErrors(['pin' => 'Trop de tentatives. Réessayez dans 15 minutes.']);
        }

        if ($request->pin !== $pin) {
            session(['pin_attempts' => $attempts + 1]);
            return back()->withErrors(['pin' => 'Code incorrect.']);
        }

        session(['pin_attempts' => 0, 'admin_pin_verified' => true]);
        return redirect()->route('admin.login');
    }


    public function showLoginForm()
    {
        
        
    
        return view('auth.admin-login');
    }
    public function showRegister() {
        return view('auth.register');
    }
    public function logout(Request $request)
{
    Auth::logout();

   
    $request->session()->invalidate();

   
    $request->session()->regenerateToken();

   
    return redirect()->route('login');
}

    
    public function register(RegisterRequest $request) 
{
    $validated = $request->validated();

    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // ✅ Créer automatiquement un enregistrement UsageCounter
    \App\Models\UsageCounter::create([
        'user_id'                  => $user->id,
        'mois'                     => now()->format('Y-m'), // ex: 2026-04
        'nb_lives_utilises'        => 0,
        'nb_commandes_utilises'    => 0,
        'nb_commentaires_utilises' => 0,
        'nb_exports_utilises'      => 0,
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('pricing');
}


    public function showLogin() {
        return view('auth.login');
    }

  
        
         public function login(LoginRequest $request)
{
   
    $validator = Validator::make($request->all(), [
        'g-recaptcha-response' => 'required|captcha',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $activePayment = Payment::where('user_id', $user->id)
            ->where('status', 'accepte')
            ->where('created_at', '>=', now()->subMonth())
            ->first();

        
        $pendingPayment = Payment::where('user_id', $user->id)
            ->where('status', 'en_attente')
            ->first();

       
        if ($pendingPayment && !$activePayment) {
            return redirect()->route('payment.pending')
                ->with('info', 'Votre demande d\'abonnement est en cours de validation. Veuillez patienter.');
        }

      
        if ($activePayment) {
            $daysLeft = $this->calculateDaysLeft($activePayment);
            return redirect()->route('dashboard')
                ->with('success', 'Connexion réussie ! Demande acceptée.');
        }

   
        return redirect()->route('pricing')
            ->with('info', 'Veuillez choisir un plan d\'abonnement pour continuer.');
    }

    return back()->withErrors(['email' => 'Identifiants incorrects']);
}
public function adminLogin(AdminRequest $request)
{
  

    $admin = User::where('email', $request->email)
                 ->where('role', 'admin')
                 ->first();

    if (!$admin || !Hash::check($request->password, $admin->password)) {
        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }

    Auth::login($admin);
    $request->session()->regenerate();

    $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;

    Session::put('admin_id',            $admin->id);
    Session::put('admin_name',          $admin->name);
    Session::put('admin_sanctum_token', $token);

    return redirect()->route('admin.selection');
}
public function adminLogout(Request $request)
{
    Session::forget('admin_id');
    Session::forget('admin_name');
    Session::flush();

    return redirect()->route('admin.login')
        ->with('success', 'Vous avez été déconnecté.');
}
 

public function showDashboard()
{
    $daysLeft = $this->calculateDaysLeft();

    
    $codesArticles = Code::where('user_id', Auth::id())->pluck('code')->toArray(); 

    return view('commentaire.dashboard', compact('daysLeft', 'codesArticles'));
}
private function calculateDaysLeft()
{
   
    if (Auth::check()) {
        $lastPayment = Payment::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->latest()
            ->first();

        if ($lastPayment) {
           
            $expiryDate = $lastPayment->created_at->copy()->addDays(30);
            
            
            $diff = (int) now()->diffInDays($expiryDate, false);
            
           
            return $diff > 0 ? $diff : 0;
        }
    }
}
}