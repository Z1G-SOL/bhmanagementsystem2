<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LandlordDashboardController extends Controller
{
    /**
     * Display the primary Landlord Hub overview.
     * Modified to explicitly eager-load tenant inquiry and account relationships.
     */
    public function index()
    {
        $rooms = Room::where('user_id', Auth::id())
                     ->with(['inquiries.renter'])
                     ->get();
                     
        return view('landlord.dashboard', compact('rooms'));
    }

    /**
     * Provision and store a brand new room asset into database storage.
     */
    public function storeRoom(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string',
            'monthly_rate' => 'required|numeric|min:0',
            'nearest_school' => 'required|string',
            'distance_indicator' => 'required|string',
        ]);

        $amenities = $request->input('amenities', []);

        Room::create([
            'user_id' => Auth::id(),
            'room_number' => $request->room_number,
            'monthly_rate' => $request->monthly_rate,
            'nearest_school' => $request->nearest_school,
            'distance_indicator' => $request->distance_indicator,
            'amenities' => $amenities,
            'is_available' => true,
            'room_photo_1' => $request->hasFile('room_photo_1') ? $request->file('room_photo_1')->store('rooms', 'public') : null,
            'room_photo_2' => $request->hasFile('room_photo_2') ? $request->file('room_photo_2')->store('rooms', 'public') : null,
            'room_photo_3' => $request->hasFile('room_photo_3') ? $request->file('room_photo_3')->store('rooms', 'public') : null,
        ]);

        return back()->with('success', 'Room unit launched successfully onto the marketplace!');
    }

    /**
     * Accept a tenant, lock the room, and start the 30-day payment countdown.
     */
    public function acceptInquiry($id)
    {
        $inquiry = Inquiry::with('room')->findOrFail($id);

        if ($inquiry->room->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $inquiry->update([
            'status' => 'Accepted',
            'rent_started_at' => now() 
        ]);
        
        $inquiry->room->update(['is_available' => false]);

        Inquiry::where('room_id', $inquiry->room_id)
            ->where('id', '!=', $id)
            ->where('status', 'Pending')
            ->update(['status' => 'Rejected']);

        return back()->with('success', 'Application accepted! The billing cycle countdown is now live.');
    }

    /**
     * Decline an inbound student room application.
     */
    public function rejectInquiry($id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->update(['status' => 'Rejected']);
        
        return back()->with('success', 'Application declined successfully.');
    }

    /**
     * Terminate an active lease, log history, and re-broadcast vacancy.
     */
    public function evictTenant($id)
    {
        // Finding via room ID based on your route schema configuration parameter
        $room = Room::findOrFail($id);
        
        if ($room->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $activeInquiry = Inquiry::where('room_id', $room->id)
                                ->where('status', 'Accepted')
                                ->first();

        if ($activeInquiry) {
            $activeInquiry->update(['status' => 'Terminated']);
        }

        $room->update(['is_available' => true]);

        return back()->with('success', 'Tenant lease ended! Room #' . $room->room_number . ' is now broadcasting as vacant.');
    }

    /**
     * Permanently delete a room asset from the platform database.
     */
    public function destroyRoom($id)
    {
        $room = Room::with('inquiries')->findOrFail($id);

        if ($room->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$room->is_available) {
            return back()->with('error', 'Action Blocked: You cannot delete a room that is currently occupied by an active tenant. Evict the tenant first.');
        }

        $room->inquiries()->delete();
        $room->delete();

        return back()->with('success', 'Room Unit #' . $room->room_number . ' has been permanently removed from your assets.');
    }

    /**
     * Display the visual HTML analytics dashboard.
     */
    public function viewAnalytics()
    {
        $analyticsData = $this->compileAnalyticsData();
        return view('landlord.analytics', $analyticsData);
    }

    /**
     * Compile current statistics and stream a downloadable PDF print file.
     */
    public function exportAnalyticsPDF()
    {
        $analyticsData = $this->compileAnalyticsData();
        
        $pdf = Pdf::loadView('landlord.analytics_pdf', $analyticsData)
                  ->setPaper('a4', 'portrait');
                  
        return $pdf->download('DavaoDormConnect_Financial_Report_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Core calculation helper method to prevent code duplication metrics.
     */
    private function compileAnalyticsData()
    {
        $landlordId = Auth::id();
        $rooms = Room::where('user_id', $landlordId)->with('inquiries.renter')->get();
        
        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->where('is_available', false)->count();
        $vacantRooms = $totalRooms - $occupiedRooms;
        
        $monthlyGrossPotential = $rooms->sum('monthly_rate');
        $currentActiveRevenue = $rooms->where('is_available', false)->sum('monthly_rate');
        $vacancyLoss = $monthlyGrossPotential - $currentActiveRevenue;
        
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        $activeTenants = [];
        foreach ($rooms as $room) {
            $activeLease = $room->inquiries->where('status', 'Accepted')->first();
            if ($activeLease && $activeLease->renter) {
                // Modified to use rent_started_at directly or fallback to updated_at timestamp safely
                $startDate = $activeLease->rent_started_at ?? $activeLease->updated_at;
                $activeTenants[] = [
                    'room_number' => $room->room_number,
                    'tenant_name' => $activeLease->renter->name,
                    'rate' => $room->monthly_rate,
                    'started_at' => $startDate->format('M d, Y'),
                ];
            }
        }

        return compact(
            'rooms', 'totalRooms', 'occupiedRooms', 'vacantRooms', 
            'monthlyGrossPotential', 'currentActiveRevenue', 'vacancyLoss', 
            'occupancyRate', 'activeTenants'
        );
    }
}