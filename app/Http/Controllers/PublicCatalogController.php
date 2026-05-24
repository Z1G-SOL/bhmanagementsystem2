<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Lead;
use Illuminate\Http\Request;

class PublicCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('user')->where('status', 'available');

        if ($request->has('school') && !empty($request->school)) {
            $query->where('nearest_school', $request->school);
        }

        $rooms = $query->latest()->get();
        $schools = [
            'ATENEO DE DAVAO', 'UM', 'UIC BONIFACIO', 'UIC BAJADA', 
            'UIC BANGKEROHAN', 'JOJI ILAGAN', 'USEP', 'ATENEO HIGH SCHOOL', 'ATENEO ELEMENTARY'
        ];

        return view('catalog', compact('rooms', 'schools'));
    }

    public function storeLead(Request $request, $roomId)
    {
        $request->validate([
            'renter_name' => 'required|string|max:255',
            'renter_contact' => 'required|string|max:255',
            'renter_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $room = Room::findOrFail($roomId);
        $photoPath = $request->hasFile('renter_photo') ? $request->file('renter_photo')->store('renter_photos', 'public') : null;

        $lead = Lead::create([
            'room_id' => $room->id,
            'renter_name' => $request->renter_name,
            'renter_contact' => $request->renter_contact,
            'renter_photo' => $photoPath,
        ]);

        return back()->with('lead_success', [
            'landlord_email' => $room->user->email,
            'room_number' => $room->room_number,
            'school' => $room->nearest_school,
            'renter_name' => $lead->renter_name
        ]);
    }
}