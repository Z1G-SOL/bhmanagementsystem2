@extends('layouts.app')
@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">Student Renter Portal</h2>
        <p class="text-muted mb-0">Track your housing applications, active leases, and upcoming rent obligations in Davao City.</p>
    </div>

    @php
        // Isolate any accepted booking to see if this student is currently renting a unit
        $activeLease = $inquiries->where('status', 'Accepted')->first();
    @endphp

    @if($activeLease && $activeLease->room)
        @php
            $startDate = $activeLease->rent_started_at ?? $activeLease->updated_at;
            $nextDueDate = $startDate->copy()->addDays(30);
            $daysUntilDue = ceil(now()->diffInDays($nextDueDate, false));
            $isRentDue = $daysUntilDue <= 0;
            $landlordUser = $activeLease->room->user;
        @endphp

        <div class="card shadow border-0 mb-5 overflow-hidden">
            <div class="card-header {{ $isRentDue ? 'bg-danger' : 'bg-dark' }} text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold font-monospace"><i class="bi bi-house-heart-fill me-2"></i>Your Active Housing Lease</h5>
                    <span class="badge bg-white {{ $isRentDue ? 'text-danger' : 'text-dark' }} fw-bold text-uppercase">
                        {{ $isRentDue ? 'Overdue Action Needed' : 'Lease Active' }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4 bg-white">
                <div class="row align-items-center g-4">
                    <div class="col-md-4 border-end">
                        <span class="text-uppercase font-monospace text-muted small d-block mb-1">Occupying Unit</span>
                        <h3 class="fw-bold text-dark mb-2">Room #{{ $activeLease->room->room_number }}</h3>
                        <p class="text-secondary small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Near {{ $activeLease->room->nearest_school }}</p>
                        <h5 class="text-primary fw-bold mb-0">PHP {{ number_format($activeLease->room->monthly_rate, 2) }} <span class="text-muted fs-6 fw-normal">/month</span></h5>
                    </div>

                    <div class="col-md-4 text-center border-end">
                        <span class="text-uppercase font-monospace text-muted small d-block mb-2">Rent Payment Status Tracker</span>
                        
                        @if($isRentDue)
                            <div class="alert alert-danger d-inline-block border-2 mb-0 px-4 py-2 animate-pulse">
                                <h4 class="fw-bold mb-1 text-danger"><i class="bi bi-exclamation-octagon-fill me-2"></i>RENT PAST DUE</h4>
                                <span class="small fw-bold">Please remit your PHP {{ number_format($activeLease->room->monthly_rate, 2) }} payment immediately.</span>
                            </div>
                        @else
                            <div class="d-inline-block bg-light rounded-3 border px-4 py-2">
                                <h2 class="fw-bold text-dark mb-0 font-monospace">{{ $daysUntilDue }}</h2>
                                <span class="text-muted small fw-bold text-uppercase">Days Until Rent Due</span>
                            </div>
                            <div class="mt-2 text-muted small">
                                Next Due Date: <strong>{{ $nextDueDate->format('M d, Y') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <span class="text-uppercase font-monospace text-muted small d-block mb-2">Property Owner Contact</span>
                        @if($landlordUser)
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3 fs-4 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">{{ $landlordUser->name }}</h6>
                                    <div class="text-muted small"><i class="bi bi-telephone-fill me-1"></i> {{ $landlordUser->phone_number }}</div>
                                    <div class="text-muted small"><i class="bi bi-envelope-fill me-1"></i> {{ $landlordUser->email }}</div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted small mb-0">Landlord details unavailable.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <h4 class="fw-bold text-dark mb-3">Your Sent Applications Log</h4>
    <div class="card shadow-sm border-0 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light font-monospace small">
                    <tr>
                        <th class="ps-3">Target Space</th>
                        <th>Monthly Bill</th>
                        <th>Application Status</th>
                        <th>Filing Timestamp</th>
                        <th class="pe-3 text-end">Lease Reference Code</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="ps-3 py-3">
                                @if($inquiry->room)
                                    <span class="fw-bold text-dark d-block">Room #{{ $inquiry->room->room_number }}</span>
                                    <span class="text-muted small">Hub: {{ $inquiry->room->nearest_school }}</span>
                                @else
                                    <span class="text-muted small text-danger">Unavailable Room Unit Reference</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark">
                                    PHP {{ $inquiry->room ? number_format($inquiry->room->monthly_rate, 2) : '0.00' }}
                                </span>
                            </td>
                            <td>
                                @if($inquiry->status === 'Accepted')
                                    <span class="badge bg-success shadow-sm px-2.5 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Active Renter</span>
                                @elseif($inquiry->status === 'Pending')
                                    <span class="badge bg-warning text-dark border px-2.5 py-1.5"><i class="bi bi-hourglass-split me-1"></i> Under Review</span>
                                @elseif($inquiry->status === 'Terminated')
                                    <span class="badge bg-secondary px-2.5 py-1.5"><i class="bi bi-door-open me-1"></i> Lease Finished</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5">Declined</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $inquiry->created_at ? $inquiry->created_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="pe-3 text-end font-monospace text-muted small">
                                @if($inquiry->status === 'Accepted')
                                    <span class="text-primary fw-bold">DMC-L{{ $inquiry->id }}-R{{ $inquiry->room_id }}</span>
                                @else
                                    --
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                You haven't sent any boarding house inquiries yet. Visit the public marketplace feed to secure a room.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .animate-pulse {
        animation: pulseAnimation 2s infinite;
    }
    @keyframes pulseAnimation {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
</style>
@endsection