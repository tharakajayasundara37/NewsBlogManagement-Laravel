<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\DatabaseBootstrap;
use App\Support\PasswordHash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);
        DatabaseBootstrap::run();
        $user = User::where('email', $credentials['email'])->first();
        if (! $user || ! PasswordHash::check($credentials['password'], $user->password)) return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate(); return redirect()->intended(route('dashboard'));
    }
    public function logout(Request $request)
    {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect('/');
    }
}
