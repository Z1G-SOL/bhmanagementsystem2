@extends('layouts.app')
@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Davao-Dorm Marketplace</h2>
            <p class="text-muted mb-0">Browse student-friendly boarding houses near major Davao campuses.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-4 border-0 border-start border-success border-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @forelse($rooms as $room)
            <div class="col">
                <div class="card h-100 shadow-sm border-0 overflow-hidden property-card position-relative">
                    <span class="badge bg-primary position-absolute top-0 end-0 m-3 z-3 fs-6 shadow-sm">
                        PHP {{ number_format($room->monthly_rate, 2) }}/mo
                    </span>
                    
                    <img src="{{ asset('storage/' . $room->room_photo_1) }}" class="card-img-top object-fit-cover" style="height: 200px;" alt="Room Photo">
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-dark mb-1">Room {{ $room->room_number }}</h5>
                        <p class="text-secondary small mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Near {{ $room->nearest_school }}
                        </p>
                        <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($room->distance_indicator, 90) }}</p>
                        
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @if($room->amenities)
                                @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                    <span class="badge bg-light text-dark border small">{{ $amenity }}</span>
                                @endforeach
                                @if(count($room->amenities) > 3)
                                    <span class="badge bg-light text-secondary border small">+{{ count($room->amenities) - 3 }} more</span>
                                @endif
                            @endif
                        </div>

                        <button type="button" class="btn btn-outline-primary w-100 fw-bold mt-auto" 
                                onclick="openDetailsModal({{ json_encode($room) }}, '{{ $room->landlord->name }}')">
                            View Room Units
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">No spaces are currently broadcasted to the live marketplace catalog.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div id="modalCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner rounded shadow-sm" style="height: 280px;">
                                <div class="carousel-item active h-100"><img id="img1" src="" class="d-block w-100 h-100 object-fit-cover"></div>
                                <div class="carousel-item h-100"><img id="img2" src="" class="d-block w-100 h-100 object-fit-cover"></div>
                                <div class="carousel-item h-100"><img id="img3" src="" class="d-block w-100 h-100 object-fit-cover"></div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                            <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div>
                            <span class="badge bg-success mb-2 px-2 py-1 fs-6" id="modalPrice"></span>
                            <h3 class="fw-bold text-dark mb-1" id="modalTitle"></h3>
                            <p class="text-primary fw-semibold small mb-3" id="modalSchool"></p>
                            
                            <div class="bg-light p-3 rounded mb-3">
                                <h6 class="fw-bold text-secondary small text-uppercase mb-1">Location Details</h6>
                                <p class="text-muted small mb-0" id="modalDistance"></p>
                            </div>

                            <h6 class="fw-bold text-secondary small text-uppercase mb-2">Amenities Standard Matrix</h6>
                            <div id="modalAmenities" class="d-flex flex-wrap gap-1 mb-3"></div>
                        </div>

                        <div>
                            <p class="small text-muted mb-2">Managed by: <strong class="text-dark" id="modalLandlord"></strong></p>
                            
                            @auth
                                @if(Auth::user()->isRenter())
                                    <button class="btn btn-primary w-100 fw-bold py-2 shadow-sm" onclick="switchToInquiryForm()">
                                        I'm Interested
                                    </button>
                                @else
                                    <button class="btn btn-secondary w-100 fw-bold py-2" disabled>Landlords Cannot Inquire</button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-warning w-100 fw-bold py-2">Sign In to File Inquiry</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="inquiryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Inquiry Routing Form</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="inquiryForm" method="POST">
                @csrf
                <div class="modal-body">
                    @auth
                        <div class="mb-2 bg-light p-2 rounded border">
                            <span class="d-block text-uppercase text-secondary fw-bold font-monospace" style="font-size: 10px;">Renter Profile Core Info</span>
                            <span class="d-block text-dark fw-bold small">{{ Auth::user()->name }}</span>
                            <span class="text-muted d-block" style="font-size: 11px;">{{ Auth::user()->email }} • {{ Auth::user()->phone_number }}</span>
                        </div>
                    @endauth

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold mb-1">Age Matrix</label>
                        <input type="number" name="age" min="16" max="100" required class="form-control" placeholder="e.g. 21">
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-secondary small fw-bold mb-1">Gender Identification</label>
                        <select name="gender" required class="form-select">
                            <option value="">-- Choose Option --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn btn-success w-100 fw-bold">Dispatch Intent Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let detailsModalInstance;
    let inquiryModalInstance;
    let activeRoomId = null;

    document.addEventListener("DOMContentLoaded", function() {
        detailsModalInstance = new bootstrap.Modal(document.getElementById('detailsModal'));
        inquiryModalInstance = new bootstrap.Modal(document.getElementById('inquiryModal'));
    });

    function openDetailsModal(room, landlordName) {
        activeRoomId = room.id;
        
        // Populating the UI strings
        document.getElementById('modalTitle').innerText = "Room " + room.room_number;
        document.getElementById('modalPrice').innerText = "PHP " + parseFloat(room.monthly_rate).toLocaleString('en-US', {minimumFractionDigits: 2}) + " / mo";
        document.getElementById('modalSchool').innerText = "📍 Proximity Vector: Near " + room.nearest_school;
        document.getElementById('modalDistance').innerText = room.distance_indicator;
        document.getElementById('modalLandlord').innerText = landlordName;

        // Base image mutations
        document.getElementById('img1').src = "/storage/" + room.room_photo_1;
        document.getElementById('img2').src = "/storage/" + room.room_photo_2;
        document.getElementById('img3').src = "/storage/" + room.room_photo_3;

        // Render Dynamic Amenities Node Loop
        const amenitiesContainer = document.getElementById('modalAmenities');
        amenitiesContainer.innerHTML = '';
        if (room.amenities && room.amenities.length > 0) {
            room.amenities.forEach(amenity => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-light text-dark border p-2';
                badge.innerText = amenity;
                amenitiesContainer.appendChild(badge);
            });
        } else {
            amenitiesContainer.innerHTML = '<span class="text-muted small">Standard Basic Utilities Only</span>';
        }

        detailsModalInstance.show();
    }

    function switchToInquiryForm() {
        // Toggle active display viewports cleanly
        detailsModalInstance.hide();
        
        // Update form submission destination pipeline
        document.getElementById('inquiryForm').action = "/room/" + activeRoomId + "/inquire";
        
        // Render step two modal viewport sequence
        setTimeout(() => {
            inquiryModalInstance.show();
        }, 400);
    }
</script>
@endsection