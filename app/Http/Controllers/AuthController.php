<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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

        // Call the API for authentication
        $apiUrl = config('services.pelabuhan_api.url').'/nle-oauth/v1/user/login';

        $response = Http::post($apiUrl, [
            'username' => $data['username'],
            'password' => $data['password'],
        ]);

        if ($response->failed() || $response->json('status') !== 'success') {
            return back()->withErrors(['username' => 'Username atau password salah'])->withInput();
        }

        $apiData = $response->json('item');

        // Update .env file with new access token
        $envFile = base_path('.env');
        if (File::exists($envFile)) {
            $envContent = File::get($envFile);
            $newToken = $apiData['access_token'];

            // Replace the PELABUHAN_API_TOKEN line
            $envContent = preg_replace(
                '/^PELABUHAN_API_TOKEN=.*/m',
                'PELABUHAN_API_TOKEN='.$newToken,
                $envContent
            );

            File::put($envFile, $envContent);

            // Clear config cache to reload the new token
            \Artisan::call('config:clear');
        }

        // Find or create local user
        // Ensure email is not null (database requires NOT NULL). If API doesn't provide email,
        // create a safe fallback using username so DB insert won't fail.
        $fallbackEmail = ! empty($apiData['email']) ? $apiData['email'] : ($data['username'].'@no-email.local');

        $user = User::firstOrCreate(
            ['username' => $data['username']],
            [
                'name' => $apiData['name'] ?? $data['username'],
                'email' => $fallbackEmail,
                'password' => Hash::make($data['password']), // Store password for potential local fallback
            ]
        );

        // Store API token and user info in session
        $request->session()->put('access_token', $apiData['access_token']);
        $request->session()->put('token_expires_at', now()->addSeconds($apiData['expires_in'])->timestamp);
        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);
        $request->session()->put('api_user_data', $apiData);

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

        // Clear API related session data
        $request->session()->forget(['access_token', 'api_user_data', 'token_expires_at']);

        return redirect()->route('login.form');
    }
}
