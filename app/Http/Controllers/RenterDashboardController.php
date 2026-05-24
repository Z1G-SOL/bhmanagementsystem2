<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RenterDashboardController extends Controller
{
    /**
     * Display the Renter Dashboard with active tracking metrics.
     */
    public function index()
    {
        // Fetch all sent applications along with room details and landlords
        $inquiries = Auth::user()->inquiriesSent()
            ->with(['room.user']) // Cascade landlord profile details through room ownership mappings
            ->latest()
            ->get();

        return view('renter.dashboard', compact('inquiries'));
    }
}