<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\RenterDashboardController;
use App\Http\Controllers\LandlordDashboardController;

// ---------------------------------------------------------
// PUBLIC ROUTES
// ---------------------------------------------------------
Route::get('/', [MarketplaceController::class, 'index'])->name('catalog.index');

// ---------------------------------------------------------
// AUTHENTICATION ROUTES (Guests Only)
// ---------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ---------------------------------------------------------
// AUTHENTICATED ROUTES
// ---------------------------------------------------------
Route::middleware('auth')->group(function () {
    
    // Renters inquiring about a room unit
    Route::post('/room/{id}/inquire', [MarketplaceController::class, 'storeInquiry'])->name('inquiry.store');

    // =====================================================
    // RENTER PORTAL GROUP
    // =====================================================
    Route::prefix('renter')->group(function () {
        Route::get('/dashboard', [RenterDashboardController::class, 'index'])->name('renter.dashboard');
    });

    // =====================================================
    // LANDLORD COMMAND HUB GROUP
    // =====================================================
    Route::prefix('landlord')->group(function () {
        // Base View Map Overview Layout
        Route::get('/dashboard', [LandlordDashboardController::class, 'index'])->name('landlord.dashboard');
        
        // Property Provisions Pipeline
        Route::post('/room/store', [LandlordDashboardController::class, 'storeRoom'])->name('landlord.room.store');
        
        // Inbound Pipeline Action Endpoints
        Route::post('/inquiry/{id}/accept', [LandlordDashboardController::class, 'acceptInquiry'])->name('landlord.inquiry.accept');
        Route::post('/inquiry/{id}/reject', [LandlordDashboardController::class, 'rejectInquiry'])->name('landlord.inquiry.reject');
        
        // Fixed URL Redundancy for Tenant Lease Evictions / Marketplace Republishing Engine
        Route::post('/room/{id}/evict', [LandlordDashboardController::class, 'evictTenant'])->name('landlord.room.evict');

        Route::get('/analytics', [LandlordDashboardController::class, 'viewAnalytics'])->name('landlord.analytics');
        Route::get('/analytics/pdf', [LandlordDashboardController::class, 'exportAnalyticsPDF'])->name('landlord.analytics.pdf');
    });
});