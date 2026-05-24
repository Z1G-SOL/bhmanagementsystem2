@extends('layouts.app')
@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Business Analytics Hub</h2>
            <p class="text-muted mb-0">Live data evaluations, occupancy yields, and generated statement summary projections.</p>
        </div>
        <div>
            <a href="{{ route('landlord.dashboard') }}" class="btn btn-outline-secondary fw-bold btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Return to Map
            </a>
            <a href="{{ route('landlord.analytics.pdf') }}" class="btn btn-danger fw-bold btn-sm shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Export Printed Document (PDF)
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-white p-3 border-start border-primary border-4">
                <h6 class="text-uppercase font-monospace text-muted small mb-1">Current Active Revenue Yield</h6>
                <h2 class="fw-bold text-primary mb-1">PHP {{ number_format($currentActiveRevenue, 2) }}</h2>
                <span class="text-muted small">Generated out of PHP {{ number_format($monthlyGrossPotential, 2) }} potential</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-white p-3 border-start border-warning border-4">
                <h6 class="text-uppercase font-monospace text-muted small mb-1">Unreleased Vacancy Loss</h6>
                <h2 class="fw-bold text-warning mb-1">PHP {{ number_format($vacancyLoss, 2) }}</h2>
                <span class="text-muted small">Lost revenue from vacant broadcasting spaces</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-white p-3 border-start border-success border-4">
                <h6 class="text-uppercase font-monospace text-muted small mb-1">Dormitory Occupancy Rate</h6>
                <h2 class="fw-bold text-success mb-1">{{ $occupancyRate }}%</h2>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $occupancyRate }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-people me-2"></i>Active Operational Tenant Ledger</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small font-monospace">
                            <tr>
                                <th>Room Unit</th>
                                <th>Occupant Identity Name</th>
                                <th>Monthly Rate</th>
                                <th class="text-end">Lease Activation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeTenants as $tenant)
                                <tr>
                                    <td class="fw-bold">#{{ $tenant['room_number'] }}</td>
                                    <td>{{ $tenant['tenant_name'] }}</td>
                                    <td class="text-primary fw-bold">PHP {{ number_format($tenant['rate'], 2) }}</td>
                                    <td class="text-muted small text-end">{{ $tenant['started_at'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No active lease logs records captured.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart me-2"></i>Physical Space Allocation</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-building me-2 text-secondary"></i>Total Tracked Room Inventory</span>
                        <span class="badge bg-dark rounded-pill">{{ $totalRooms }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-door-closed-fill me-2 text-danger"></i>Occupied Operational Slots</span>
                        <span class="badge bg-danger rounded-pill">{{ $occupiedRooms }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-door-open-fill me-2 text-success"></i>Vacant Marketplace Channels</span>
                        <span class="badge bg-success rounded-pill">{{ $vacantRooms }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection