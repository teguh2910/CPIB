<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = User::where('username', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah'])->withInput();
        }

        // Store some session values used by app
        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);

        // Also log in the user into Laravel's authentication system so Auth::check()/Auth::id() work
        Auth::login($user);

        // Prevent session fixation
        $request->session()->regenerate();

        // reset draft wizard saat login (opsional)
        $request->session()->forget('wizard_import');

        return redirect()->route('import.index');
    }

    public function logout(Request $request)
    {
        // Logout from Laravel auth and clear session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}
