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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return Auth::user()->role === 'landlord'
                ? redirect()->route('landlord.dashboard')
                : redirect()->route('renter.dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our system records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Enforce strict profile image assignment validation checks
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:renter,landlord',
            'phone_number' => 'required|string|max:20',
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        // 2. Commit the uploaded photo file stream onto your public local drive allocations
        $photoPath = $request->file('profile_photo')->store('avatars', 'public');

        // 3. Inject new User identity row directly inside your MySQL table mapping
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone_number' => $request->phone_number,
            'profile_photo' => $photoPath,
        ]);

        Auth::login($user);

        return $user->role === 'landlord' 
            ? redirect()->route('landlord.dashboard') 
            : redirect()->route('renter.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('catalog.index');
    }
}