<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class LoginController extends Controller
{

    public function index()
    {
        $email = request()->cookie('remember_email');
        $password = request()->cookie('remember_password');

        return view('login.index', [
            'title' => 'login',
            'setting' => Setting::first() ?? view()->shared('setting'),
            'email' => $email,
            'password' => $password,
            'remember' => $email ? true : false
        ]);
    }
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Save credentials to cookies if remember is checked
            if ($remember) {
                cookie()->queue('remember_email', $request->email, 60 * 24 * 30); // 30 days
                cookie()->queue('remember_password', $request->password, 60 * 24 * 30); // 30 days
            } else {
                // Clear cookies if remember is not checked
                cookie()->queue(cookie()->forget('remember_email'));
                cookie()->queue(cookie()->forget('remember_password'));
            }

            return to_route('dashboard.index')->withSuccess('Login berhasil');
        }

        // Record failed login event to audit logs
        \App\Models\AuditLog::create([
            'user_id' => null,
            'action' => 'LOGIN_FAILED',
            'description' => 'Gagal login menggunakan email: ' . $request->email,
            'payload_before' => null,
            'payload_after' => ['email' => $request->email],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withError('Login gagal email atau password tidak benar')->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->withSuccess('Logout berhasil');
    }

    public function switchUser(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $currentUser = Auth::user();
        
        $isOriginalAdmin = ($currentUser->role === 'Admin' || $currentUser->role === 'Superadmin');
        $hasSwitched = session()->has('original_user_id');

        if (!$isOriginalAdmin && !$hasSwitched) {
            abort(403, 'Anda tidak diizinkan mengganti user');
        }

        if (!$hasSwitched) {
            session(['original_user_id' => $currentUser->id]);
        }

        if (session('original_user_id') == $request->user_id) {
            session()->forget('original_user_id');
        }

        Auth::loginUsingId($request->user_id);

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SWITCH_USER',
            'description' => 'Bertukar peran menjadi user ID: ' . $request->user_id . ' (' . Auth::user()->name . ')',
            'payload_before' => null,
            'payload_after' => null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return to_route('dashboard.index')->withSuccess('User berhasil diganti');
    }
}
