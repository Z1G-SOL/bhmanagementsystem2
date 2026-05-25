<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    /**
     * Display the Marketplace Catalog with optional campus sorting filters.
     */
    public function index(Request $request)
    {
        // Start an Eloquent query string looking only for public, vacant rooms
        $query = Room::where('is_available', true);

        // PIPELINE FILTER: If the user selected a specific institution, narrow down the dataset
        if ($request->has('school') && $request->school != '') {
            $query->where('nearest_school', $request->school);
        }

        // Fetch the remaining filtered listings from the database
        $rooms = $query->latest()->get();

        // Send the filtered results along with the currently active filter value back to the blade layout
        return view('catalog.index', [
            'rooms' => $rooms,
            'selectedSchool' => $request->query('school', '')
        ]);
    }

    /**
     * Store inbound inquiries sent by students with a mandatory verification ID upload.
     */
    public function storeInquiry(Request $request, $id)
    {
        // 1. Enforce strict validation boundary constraints, requiring a valid image file
        $request->validate([
            'age' => 'required|integer|min:15|max:100',
            'gender' => 'required|string|in:Male,Female,Other',
            'valid_id' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 👈 Enforced: max 2MB image file requirement
        ]);

        $room = Room::findOrFail($id);

        // 2. Stream the multi-part form image binary safely into local filesystem storage
        $idPath = null;
        if ($request->hasFile('valid_id')) {
            // Saves to: storage/app/public/ids/
            $idPath = $request->file('valid_id')->store('ids', 'public');
        }

        // 3. Persist record matching your structural attributes exactly
        Inquiry::create([
            'room_id' => $room->id,
            'landlord_id' => $room->user_id, // Automatically routes request tokens to the asset owner
            'renter_id' => Auth::id(),
            'age' => $request->age,
            'gender' => $request->gender,
            'valid_id_path' => $idPath,      // 👈 Stores the text file pointer path string reference
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Your inquiry alongside your Valid ID verification has been successfully dispatched to the property manager! Monitor your Renter Dashboard for real-time state changes.');
    }
}