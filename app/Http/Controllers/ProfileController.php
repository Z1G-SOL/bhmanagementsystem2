<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile-setup', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'boarding_house_name' => 'required|string|max:255',
            'house_rules' => 'required|string',
        ]);

        Auth::user()->update([
            'boarding_house_name' => $request->boarding_house_name,
            'house_rules' => $request->house_rules,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile entities synchronized.');
    }
}