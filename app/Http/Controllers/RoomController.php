<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function create()
    {
        $schools = [
            'ATENEO DE DAVAO', 'UM', 'UIC BONIFACIO', 'UIC BAJADA', 
            'UIC BANGKEROHAN', 'JOJI ILAGAN', 'USEP', 'ATENEO HIGH SCHOOL', 'ATENEO ELEMENTARY'
        ];
        return view('rooms-create', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string|max:255',
            'monthly_rate' => 'required|numeric|min:0',
            'nearest_school' => 'required|in:ATENEO DE DAVAO,UM,UIC BONIFACIO,UIC BAJADA,UIC BANGKEROHAN,JOJI ILAGAN,USEP,ATENEO HIGH SCHOOL,ATENEO ELEMENTARY',
            'distance_indicator' => 'required|string|max:255',
            'amenities' => 'nullable|array',
            'room_photo_1' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'room_photo_2' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'room_photo_3' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $paths = [];
        foreach (['room_photo_1', 'room_photo_2', 'room_photo_3'] as $photoKey) {
            $paths[$photoKey] = $request->file($photoKey)->store('room_photos', 'public');
        }

        Room::create([
            'user_id' => Auth::id(),
            'room_number' => $request->room_number,
            'monthly_rate' => $request->monthly_rate,
            'nearest_school' => $request->nearest_school,
            'distance_indicator' => $request->distance_indicator,
            'amenities' => $request->amenities ?? [],
            'room_photo_1' => $paths['room_photo_1'],
            'room_photo_2' => $paths['room_photo_2'],
            'room_photo_3' => $paths['room_photo_3'],
            'status' => 'available'
        ]);

        return redirect()->route('dashboard')->with('success', 'Room initialized successfully.');
    }
}