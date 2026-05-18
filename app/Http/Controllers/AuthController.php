<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserCredential;
use App\Models\Employee;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validate 'username' instead of 'employee_id'
        $request->validate([
            'username' => 'required', 
            'password' => 'required',
        ]);

        // 2. FIRST, find the employee by the new username in the employee table
        $employee = Employee::where('username', $request->username)->first();

        if (!$employee) {
            return back()->withErrors(['login' => 'No account found with that username.'])->withInput();
        }

        // 3. THEN, get their associated credentials
        $credential = $employee->credential;

        if (!$credential) {
            return back()->withErrors(['login' => 'No login credentials set up for this employee.'])->withInput();
        }

        if (!$credential->is_active) {
            return back()->withErrors(['login' => 'This account has been deactivated. Please contact your administrator.'])->withInput();
        }

        if (!Hash::check($request->password, $credential->password_hash)) {
            return back()->withErrors(['login' => 'Incorrect password. Please try again.'])->withInput();
        }

        // 4. Log them in using the credential model
        Auth::login($credential);
        $request->session()->regenerate();

        // [FIX APPLIED HERE]: Normalize the user access string to lowercase
        $rawAccess = $employee->access->user_access ?? 'employee';
        $realAccess = strtolower($rawAccess);

        // 5. Store the stable user_id in the session
        $request->session()->put('user_id',       $employee->user_id);
        $request->session()->put('employee_id',   $employee->employee_id); // Keep this just in case old views need it
        $request->session()->put('user_access',   $realAccess);
        $request->session()->put('view_as',       $realAccess); 

        return redirect()->route('dashboard');
    }

    // ─────────────────────────────────────────
    //  SWITCH VIEW (Admin ↔ Employee)
    // ─────────────────────────────────────────
    public function switchView(Request $request)
    {
        // [FIX APPLIED HERE]: Normalize session data to lowercase for safe comparison
        $realAccess = strtolower(session('user_access', 'employee'));
        
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