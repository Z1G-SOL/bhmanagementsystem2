@extends('layouts.app')
@section('content')

<div class="bg-primary text-white py-5 mb-4 shadow-sm">
    <div class="container">
        <h1 class="fw-bold display-5 mb-2">📍 Davao-Dorm Marketplace</h1>
        <p class="lead mb-0 opacity-75">Discover student-friendly boarding houses and dorms optimized near major Davao institutions.</p>
    </div>
</div>

<div class="container py-2">
    
    <div class="card shadow-sm border-0 mb-4 p-3 bg-white">
        <form action="{{ route('catalog.index') }}" method="GET" id="filterForm">
            <div class="row align-items-center g-3">
                <div class="col-md-3">
                    <label class="form-label font-monospace small text-uppercase text-secondary fw-bold mb-1">
                        <i class="bi bi-funnel-fill text-primary"></i> Filter by Campus Hub
                    </label>
                </div>
                <div class="col-md-9">
                    <select name="school" class="form-select border-2 border-primary-subtle" onchange="document.getElementById('filterForm').submit();">
                        <option value="">🎒 View All Available Rooms across Davao City</option>
                        <option value="Ateneo de Davao University" {{ $selectedSchool == 'Ateneo de Davao University' ? 'selected' : '' }}>
                            🔹 Ateneo de Davao University (AdDU)
                        </option>
                        <option value="University of Mindanao" {{ $selectedSchool == 'University of Mindanao' ? 'selected' : '' }}>
                            🔹 University of Mindanao (UM)
                        </option>
                        <option value="University of Southeastern Philippines" {{ $selectedSchool == 'University of Southeastern Philippines' ? 'selected' : '' }}>
                            🔹 University of Southeastern Philippines (USeP)
                        </option>
                        <option value="Davao Medical School Foundation" {{ $selectedSchool == 'Davao Medical School Foundation' ? 'selected' : '' }}>
                            🔹 Davao Medical School Foundation (DMSF)
                        </option>
                        <option value="UP Mindanao" {{ $selectedSchool == 'UP Mindanao' ? 'selected' : '' }}>
                            🔹 UP Mindanao (UPMin)
                        </option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    @if($selectedSchool)
        <div class="d-flex align-items-center mb-4">
            <span class="text-muted small">Showing units scoped near:</span>
            <span class="badge bg-primary-subtle text-primary border border-primary ms-2 px-3 py-2 fw-bold">
                {{ $selectedSchool }}
                <a href="{{ route('catalog.index') }}" class="text-primary ms-2 text-decoration-none fw-normal">✕</a>
            </span>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-5">
        @forelse($rooms as $room)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden position-relative hover-shadow transition">
                    
                    <span class="position-absolute top-0 end-0 bg-dark text-white fw-bold px-3 py-1 m-3 rounded-pill small shadow-sm z-3" style="opacity: 0.9;">
                        PHP {{ number_format($room->monthly_rate, 2) }}/mo
                    </span>

                    <div style="height: 180px; width: 100%;" class="bg-light">
                        @if($room->room_photo_1)
                            <img src="{{ asset('storage/' . $room->room_photo_1) }}" class="w-100 h-100 object-fit-cover" alt="Room Unit Preview">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-image fs-2"></i>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Room #{{ $room->room_number }}</h5>
                            
                            <div class="small text-danger fw-semibold mb-2">
                                <i class="bi bi-geo-alt-fill"></i> Near {{ $room->nearest_school }}
                            </div>
                            
                            <p class="text-muted small mb-3 text-truncate-2" style="font-size: 13px; line-height: 1.4;">
                                {{ $room->distance_indicator }}
                            </p>
                        </div>

                        <div>
                            <div class="mb-3 d-flex flex-wrap gap-1">
                                @if(is_array($room->amenities) || is_object($room->amenities))
                                    @foreach(array_slice((array)$room->amenities, 0, 3) as $amenity)
                                        <span class="badge bg-light text-dark border font-monospace" style="font-size: 10px;">
                                            {{ $amenity }}
                                        </span>
                                    @endforeach
                                    @if(count((array)$room->amenities) > 3)
                                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 10px;">
                                            +{{ count((array)$room->amenities) - 3 }} more
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-light text-muted border font-monospace" style="font-size: 10px;">Standard Utilities</span>
                                @endif
                            </div>

                            @auth
                                @if(Auth::user()->role === 'renter')
                                    <button class="btn btn-outline-primary btn-sm w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#inquireModal{{ $room->id }}">
                                        View Room Units
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-sm w-100 fw-bold disabled" disabled>
                                        Landlord Account View
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                                    Sign In to Book Unit
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            @auth
                @if(Auth::user()->role === 'renter')
                    <div class="modal fade" id="inquireModal{{ $room->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-send-fill me-2"></i>File Application for Room {{ $room->room_number }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('inquiry.store', $room->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-secondary">Your Current Age</label>
                                                <input type="number" name="age" class="form-control" placeholder="e.g. 21" min="15" max="100" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-bold text-secondary">Gender Specification</label>
                                                <select name="gender" class="form-select" required>
                                                    <option value="">-- Select --</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-secondary">Type of Valid ID Submitted</label>
                                            <select name="id_type" class="form-select" required>
                                                <option value="">-- Choose ID Document Category --</option>
                                                <option value="NATIONAL ID">NATIONAL ID</option>
                                                <option value="PAG-IBIG">PAG-IBIG</option>
                                                <option value="SSS">SSS</option>
                                                <option value="VOTER'S ID">VOTER'S ID</option>
                                                <option value="PASSPORT">PASSPORT</option>
                                                <option value="CURRENT/UPTODATE SCHOOL ID">CURRENT/UPTODATE SCHOOL ID</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-0 p-3 bg-light border rounded border-primary-subtle">
                                            <label for="valid_id_{{ $room->id }}" class="form-label small fw-bold text-danger">
                                                <i class="bi bi-card-image me-1"></i> Upload Valid ID Image (Required)
                                            </label>
                                            <input type="file" name="valid_id" id="valid_id_{{ $room->id }}" class="form-control border-danger-subtle" accept="image/jpeg, image/png, image/jpg" required>
                                            <small class="text-muted d-block mt-1 style-italic" style="font-size: 11px;">
                                                Provide a clear photo image of your student or government ID. Accepted extensions: JPG, JPEG, PNG (Max file capacity limit: 2MB).
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer pt-0 border-top-0">
                                        <button type="submit" class="btn btn-success w-100 fw-bold">Submit Application Request</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

        @empty
            <div class="col-12 text-center py-5 bg-white rounded shadow-sm border">
                <i class="bi bi-search text-muted fs-1 d-block mb-3"></i>
                <h4 class="fw-bold text-dark">No Available Dormitories Found</h4>
                <p class="text-muted small max-width-500 mx-auto">
                    There are currently no active room units broadcasting vacant vacancies near this chosen campus hub location. Check back later or try selecting another Davao campus network area!
                </p>
                <a href="{{ route('catalog.index') }}" class="btn btn-sm btn-primary fw-bold mt-2">Clear Selection Filters</a>
            </div>
        @endforelse
    </div>
</div>

<style>
    .hover-shadow:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12)!important; }
    .transition { transition: all 0.25s ease-in-out; }
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

@endsection