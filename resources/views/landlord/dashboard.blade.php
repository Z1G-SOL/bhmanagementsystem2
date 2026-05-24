@extends('layouts.app')
@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4 border-0 border-start border-success border-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Landlord Command Hub</h2>
            <p class="text-muted mb-0">Overview of your boarding houses, room allocations, and student applications.</p>
        </div>
        <div>
            <a href="{{ route('landlord.analytics') }}" class="btn btn-outline-dark fw-bold shadow-sm me-2">
                <i class="bi bi-graph-up-arrow me-1"></i> View Analytics Report
            </a>
            <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                <i class="bi bi-plus-lg me-1"></i> Add New Room Unit
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase font-monospace text-muted small mb-1">Total Assets</h6>
                        <h3 class="fw-bold text-dark mb-0">{{ $rooms->count() }} Units</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded p-3 fs-4"><i class="bi bi-building"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase font-monospace text-muted small mb-1">Occupied Rooms</h6>
                        <h3 class="fw-bold text-danger mb-0">{{ $rooms->where('is_available', false)->count() }}</h3>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded p-3 fs-4"><i class="bi bi-door-closed-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase font-monospace text-muted small mb-1">Vacant / Public</h6>
                        <h3 class="fw-bold text-success mb-0">{{ $rooms->where('is_available', true)->count() }}</h3>
                    </div>
                    <div class="bg-success-subtle text-success rounded p-3 fs-4"><i class="bi bi-door-open-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-white h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase font-monospace text-muted small mb-1">Pending Leads</h6>
                        <h3 class="fw-bold text-warning mb-0">
                            {{ $rooms->pluck('inquiries')->flatten()->where('status', 'Pending')->count() }}
                        </h3>
                    </div>
                    <div class="bg-warning-subtle text-warning rounded p-3 fs-4"><i class="bi bi-person-lines-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-3">Dormitory Layout Allocation Map</h4>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3 mb-5">
        @forelse($rooms as $room)
            @php
                $pendingCount = $room->inquiries->where('status', 'Pending')->count();
                $acceptedInquiry = $room->inquiries->where('status', 'Accepted')->first();
                $tenantUser = $acceptedInquiry ? $acceptedInquiry->renter : null;
            @endphp
            <div class="col">
                <div class="card h-100 border-2 shadow-sm text-center transition-hover 
                    {{ $room->is_available ? 'border-success bg-success-subtle style-cursor-pointer' : 'border-danger bg-danger-subtle' }}"
                    @if($room->is_available) onclick="if({{ $pendingCount }} > 0) { viewApplicants({{ json_encode($room->room_number) }}, {{ json_encode($room->inquiries) }}); }" @endif>
                    
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <span class="d-block text-uppercase font-monospace text-muted small mb-1">Unit Spec</span>
                            <h4 class="fw-bold text-dark mb-1">#{{ $room->room_number }}</h4>
                            <span class="badge bg-white text-dark border mb-3">PHP {{ number_format($room->monthly_rate, 2) }}</span>
                        </div>

                        <div>
                            @if($room->is_available)
                                @if($pendingCount > 0)
                                    <button type="button" class="btn btn-warning btn-sm w-100 fw-bold position-relative">
                                        Review App 
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $pendingCount }}
                                        </span>
                                    </button>
                                @else
                                    <span class="text-success small fw-bold d-block py-1"><i class="bi bi-broadcast"></i> Broadcasting</span>
                                @endif
                            @else
                                @php
                                    $nextDueDate = null;
                                    $daysUntilDue = 0;
                                    $isRentDue = false;

                                    if ($acceptedInquiry) {
                                        $startDate = $acceptedInquiry->rent_started_at ?? $acceptedInquiry->updated_at;
                                        $nextDueDate = $startDate->copy()->addDays(30);
                                        $daysUntilDue = ceil(now()->diffInDays($nextDueDate, false));
                                        $isRentDue = $daysUntilDue <= 0;
                                    }
                                @endphp

                                <div class="mb-2 p-1 rounded border {{ $isRentDue ? 'bg-danger text-white border-danger animate-pulse' : 'bg-white text-danger border-danger-subtle' }}">
                                    <span class="small fw-bold font-monospace" style="font-size: 11px;">
                                        @if($isRentDue)
                                            <i class="bi bi-exclamation-triangle-fill"></i> RENT PAST DUE!
                                        @else
                                            <i class="bi bi-calendar-check"></i> Rent Due: {{ $daysUntilDue }} Days
                                        @endif
                                    </span>
                                </div>

                                <div class="row g-1 mt-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold" style="font-size: 12px;" 
                                                onclick="viewTenantDetails({{ json_encode($room->room_number) }}, {{ json_encode($tenantUser) }}, {{ json_encode($acceptedInquiry) }})">
                                            Profile
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <form action="{{ route('landlord.room.evict', $room->id) }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to end this lease and evict the tenant? The room will immediately go back on the public market.');">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold" style="font-size: 12px;">
                                                Evict
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 w-100 text-center py-5 bg-white rounded shadow-sm">
                <i class="bi bi-building-add text-muted fs-1"></i>
                <p class="text-muted mt-2 mb-0">You haven't setup any rooms yet. Click "Add New Room Unit" to populate your map.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-house-add-fill me-2"></i>Provision New Room Asset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('landlord.room.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Room Number / ID</label>
                            <input type="text" name="room_number" class="form-control" placeholder="e.g. 101-A" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Monthly Rate (PHP)</label>
                            <input type="number" name="monthly_rate" min="0" step="0.01" class="form-control" placeholder="e.g. 3500" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Target Campus Hub Proximity</label>
                            <select name="nearest_school" class="form-select" required>
                                <option value="">-- Select Nearest Institution --</option>
                                <option value="Ateneo de Davao University">Ateneo de Davao University (AdDU)</option>
                                <option value="University of Mindanao">University of Mindanao (UM)</option>
                                <option value="University of Southeastern Philippines">University of Southeastern Philippines (USeP)</option>
                                <option value="Davao Medical School Foundation">Davao Medical School Foundation (DMSF)</option>
                                <option value="UP Mindanao">UP Mindanao (UPMin)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Location & Distance Narrative</label>
                            <textarea name="distance_indicator" rows="2" class="form-control" placeholder="e.g. 3-minute walking distance from the main gate." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary d-block">Utilities Standard Matrix</label>
                            <div class="row g-2 bg-light p-2 rounded border mx-0">
                                <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="amenities[]" value="Free Wi-Fi" id="am1"><label class="form-check-label small" for="am1">Free Wi-Fi</label></div></div>
                                <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="amenities[]" value="Air Conditioned" id="am2"><label class="form-check-label small" for="am2">Air Conditioned</label></div></div>
                                <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="amenities[]" value="Private CR" id="am3"><label class="form-check-label small" for="am3">Private CR</label></div></div>
                                <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" name="amenities[]" value="Cooking Allowed" id="am4"><label class="form-check-label small" for="am4">Cooking Allowed</label></div></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary mb-1">Required Photo Array (Upload 3 Images)</label>
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text font-monospace" style="font-size: 11px;">Photo 1</span>
                                <input type="file" name="room_photo_1" class="form-control" required>
                            </div>
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text font-monospace" style="font-size: 11px;">Photo 2</span>
                                <input type="file" name="room_photo_2" class="form-control" required>
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text font-monospace" style="font-size: 11px;">Photo 3</span>
                                <input type="file" name="room_photo_3" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn btn-success w-100 fw-bold">Commit & Launch Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="applicantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="applicantModalTitle">Inbound Applications</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light font-monospace small">
                            <tr>
                                <th class="ps-3">Applicant Identity Details</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Filing Date</th>
                                <th class="text-end pe-3">Pipeline Decision Actions</th>
                            </tr>
                        </thead>
                        <tbody id="applicantsTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tenantDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="tenantModalTitle">Active Occupancy File</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 rounded-circle border overflow-hidden shadow-sm bg-light" style="width: 140px; height: 140px;">
                    <img id="tenantDisplayPhoto" src="" class="w-100 h-100 object-fit-cover" alt="Verified Resident Headshot Image">
                </div>
                <h4 class="fw-bold text-dark mb-1" id="tenantDisplayName">---</h4>
                <span class="badge bg-danger-subtle text-danger mb-4 px-3 py-1 border border-danger">Verified Active Resident</span>
                
                <div class="bg-light rounded p-3 text-start border">
                    <div class="mb-2 small"><strong class="text-secondary">Email Address:</strong> <span id="tenantDisplayEmail" class="text-dark float-end">---</span></div>
                    <div class="mb-2 small"><strong class="text-secondary">Phone Number:</strong> <span id="tenantDisplayPhone" class="text-dark float-end">---</span></div>
                    <div class="mb-2 small"><strong class="text-secondary">Age Parameter:</strong> <span id="tenantDisplayAge" class="text-dark float-end">---</span></div>
                    <div class="mb-0 small"><strong class="text-secondary">Gender Group:</strong> <span id="tenantDisplayGender" class="text-dark float-end">---</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let applicantsModalInstance;
    let tenantDetailsModalInstance;

    document.addEventListener("DOMContentLoaded", function() {
        applicantsModalInstance = new bootstrap.Modal(document.getElementById('applicantsModal'));
        tenantDetailsModalInstance = new bootstrap.Modal(document.getElementById('tenantDetailsModal'));
    });

    function viewApplicants(roomNumber, inquiries) {
        document.getElementById('applicantModalTitle').innerText = "Inbound Pipeline: Room " + roomNumber;
        const tbody = document.getElementById('applicantsTableBody');
        tbody.innerHTML = '';

        const pendingInquiries = inquiries.filter(i => i.status === 'Pending');

        if (pendingInquiries.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No processing workflows found.</td></tr>`;
        } else {
            pendingInquiries.forEach(inquiry => {
                const dateFiled = inquiry.created_at ? new Date(inquiry.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
                const name = inquiry.renter ? inquiry.renter.name : 'Unknown';
                const email = inquiry.renter ? inquiry.renter.email : 'N/A';
                const phone = inquiry.renter ? inquiry.renter.phone_number : 'N/A';

                const acceptUrl = `/landlord/inquiry/${inquiry.id}/accept`;
                const rejectUrl = `/landlord/inquiry/${inquiry.id}/reject`;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="ps-3 py-3">
                        <div class="fw-bold text-dark">${name}</div>
                        <div class="text-muted small">${email} • ${phone}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">${inquiry.age} yrs</span></td>
                    <td><span class="text-secondary">${inquiry.gender}</span></td>
                    <td class="text-muted small">${dateFiled}</td>
                    <td class="text-end pe-3">
                        <div class="d-inline-block">
                            <form action="${acceptUrl}" method="POST" class="d-inline" onsubmit="return confirm('Accept this renter? This will lock the room and notify the student.');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-sm btn-outline-success fw-bold me-1">Accept</button>
                            </form>
                            <form action="${rejectUrl}" method="POST" class="d-inline" onsubmit="return confirm('Decline this application?');">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">Reject</button>
                            </form>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        applicantsModalInstance.show();
    }

    function viewTenantDetails(roomNumber, tenantUser, inquiry) {
        document.getElementById('tenantModalTitle').innerText = "Tenant Profile: Room #" + roomNumber;
        document.getElementById('tenantDisplayName').innerText = tenantUser ? tenantUser.name : 'Unknown';
        document.getElementById('tenantDisplayEmail').innerText = tenantUser ? tenantUser.email : '---';
        document.getElementById('tenantDisplayPhone').innerText = tenantUser ? tenantUser.phone_number : '---';
        document.getElementById('tenantDisplayAge').innerText = inquiry ? inquiry.age + " years old" : '---';
        document.getElementById('tenantDisplayGender').innerText = inquiry ? inquiry.gender : '---';
        
        document.getElementById('tenantDisplayPhoto').src = (tenantUser && tenantUser.profile_photo) ? "/storage/" + tenantUser.profile_photo : "https://via.placeholder.com/150";
        
        tenantDetailsModalInstance.show();
    }
</script>

<style>
    .style-cursor-pointer { cursor: pointer; }
    .transition-hover:hover { transform: translateY(-2px); box-shadow: 0 .4rem .8rem rgba(0,0,0,.08)!important; transition: all 0.2s ease-in-out; }
    .animate-pulse { animation: pulseAnimation 2s infinite; }
    @keyframes pulseAnimation { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }
</style>
@endsection