<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $rooms = Room::where('user_id', $user->id)->with(['activeLeads', 'tenant'])->get();

        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        $tenants = Tenant::where('user_id', $user->id)->get();
        $totalCollected = $tenants->sum('total_paid');
        $totalPending = $tenants->sum('balance_owed');

        $dueTenantsNotifications = [];
        foreach ($tenants as $t) {
            if ($t->is_rent_overdue) {
                $dueTenantsNotifications[] = "Reminder: Rent is due for {$t->name} ({$t->room->room_number})";
            }
        }

        return view('dashboard', compact(
            'rooms', 'occupancyRate', 'occupiedRooms', 'totalRooms',
            'totalCollected', 'totalPending', 'dueTenantsNotifications'
        ));
    }

    public function occupyRoom(Request $request, $roomId)
    {
        $request->validate(['lead_id' => 'required|exists:leads,id']);

        $room = Room::where('user_id', Auth::id())->findOrFail($roomId);
        $lead = Lead::findOrFail($request->lead_id);

        Tenant::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'name' => $lead->renter_name,
            'contact_number' => $lead->renter_contact,
            'photo_path' => $lead->renter_photo,
            'move_in_date' => Carbon::now(),
        ]);

        $lead->update(['is_converted' => true]);
        $room->update(['status' => 'occupied']);

        return redirect()->route('dashboard')->with('success', 'Room state successfully shifted to Occupied.');
    }

    public function vacateRoom($roomId)
    {
        $room = Room::where('user_id', Auth::id())->findOrFail($roomId);
        if ($room->tenant) $room->tenant->delete();
        $room->update(['status' => 'available']);
        
        return redirect()->route('dashboard')->with('success', 'Room cleared and dynamically put back on market.');
    }
}