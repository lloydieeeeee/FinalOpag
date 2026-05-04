<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserCredential;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'password'    => 'required',
        ]);

       $credential = UserCredential::where('employee_id', $request->employee_id)->first();

        if (!$credential) {
            return back()->withErrors(['login' => 'No account found with that username.'])->withInput();
        }
        if (!$credential->is_active) {
            return back()->withErrors(['login' => 'This account has been deactivated. Please contact your administrator.'])->withInput();
        }
        if (!Hash::check($request->password, $credential->password_hash)) {
            return back()->withErrors(['login' => 'Incorrect password. Please try again.'])->withInput();
        }

        Auth::login($credential);
        $request->session()->regenerate();

        $realAccess = $credential->userAccess->user_access ?? 'employee';

        // Store both real access and current view mode in session
        $request->session()->put('employee_id',    $credential->employee_id);
        $request->session()->put('user_access',     $realAccess);
        $request->session()->put('view_as',         $realAccess); // current effective role

        return redirect()->route('dashboard');
    }

    // ─────────────────────────────────────────
    //  SWITCH VIEW (Admin ↔ Employee)
    // ─────────────────────────────────────────
    public function switchView(Request $request)
    {
        // Only real admins can switch
        $realAccess = session('user_access');
        if ($realAccess !== 'admin') {
            return back()->withErrors(['error' => 'Unauthorized.']);
        }

        $currentView = session('view_as', 'admin');
        $newView     = $currentView === 'admin' ? 'employee' : 'admin';

        $request->session()->put('view_as', $newView);

        return redirect()->route('dashboard')
            ->with('success', 'Switched to ' . ucfirst($newView) . ' view.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}