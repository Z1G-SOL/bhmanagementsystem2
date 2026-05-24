@extends('layouts.app')
@section('content')

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <h2 class="card-title fw-bold text-dark mb-3">Find Available Rooms in Davao City</h2>
        <form action="{{ route('catalog.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label class="form-label fw-bold text-secondary small mb-1">Filter by Proximity to Campus</label>
                <select name="school" class="form-select bg-light">
                    <option value="">-- View All Campuses --</option>
                    @foreach($schools as $school)
                        <option value="{{ $school }}" {{ request('school') == $school ? 'selected' : '' }}>{{ $school }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Filter Selection</button>
            </div>
        </form>
    </div>
</div>

@if(session('lead_success'))
    <div class="alert alert-success shadow-sm mb-4">
        <h4 class="alert-heading fw-bold">Inquiry Pipeline Established Successfully!</h4>
        <p class="mb-2">Your lead configuration metrics have been dispatched to the landlord's visual command dashboard.</p>
        <a href="mailto:{{ session('lead_success')['landlord_email'] }}?subject=Inquiry for {{ session('lead_success')['room_number'] }}" class="btn btn-success btn-sm fw-bold">✉️ Trigger Direct Email Engagement</a>
    </div>
@endif

<div class="row row-cols-1 row-cols-md-3 g-4">
    @forelse($rooms as $room)
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div id="carouselRoom{{ $room->id }}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner" style="height: 220px;">
                        <div class="carousel-item active h-100"><img src="{{ asset('storage/' . $room->room_photo_1) }}" class="d-block w-100 h-100 object-fit-cover"></div>
                        <div class="carousel-item h-100"><img src="{{ asset('storage/' . $room->room_photo_2) }}" class="d-block w-100 h-100 object-fit-cover"></div>
                        <div class="carousel-item h-100"><img src="{{ asset('storage/' . $room->room_photo_3) }}" class="d-block w-100 h-100 object-fit-cover"></div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselRoom{{ $room->id }}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselRoom{{ $room->id }}" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title fw-bold mb-0">{{ $room->room_number }}</h5>
                        <span class="badge bg-success fs-6">PHP {{ number_format($room->monthly_rate, 2) }}</span>
                    </div>
                    <p class="text-primary fw-bold small mb-1">📍 Near {{ $room->nearest_school }}</p>
                    <p class="text-muted small fst-italic mb-3">⚡ {{ $room->distance_indicator }}</p>
                    <strong class="d-block text-secondary small mb-1">Amenities Included:</strong>
                    <div class="d-flex flex-wrap gap-1">
                        @if($room->amenities)
                            @foreach($room->amenities as $amenity) <span class="badge bg-light text-dark border">{{ $amenity }}</span> @endforeach
                        @else <span class="text-muted small">Basic Allocation Only</span> @endif
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pb-3">
                    <button type="button" class="btn btn-primary w-100 fw-bold" onclick="openLeadModal({{ $room->id }}, '{{ $room->room_number }}')">I'm Interested</button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">No rooms actively matching query parameters are exposed to marketplace.</div>
    @endforelse
</div>

<div class="modal fade" id="leadModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="modalRoomTitle">Express Intent</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="leadForm" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
              <div class="mb-3"><label class="form-label fw-bold text-secondary">Full Legal Name</label><input type="text" name="renter_name" required class="form-control"></div>
              <div class="mb-3"><label class="form-label fw-bold text-secondary">Active Contact Number</label><input type="text" name="renter_contact" required class="form-control"></div>
              <div class="mb-3"><label class="form-label fw-bold text-secondary">Verify Identity (Profile Photo)</label><input type="file" name="renter_photo" required class="form-control"></div>
          </div>
          <div class="modal-footer border-top-0">
              <button type="submit" class="btn btn-success w-100 fw-bold">Submit Identification Metadata</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    function openLeadModal(roomId, roomNumber) {
        document.getElementById('leadForm').action = "/room/" + roomId + "/lead";
        document.getElementById('modalRoomTitle').innerText = "Express Intent for " + roomNumber;
        new bootstrap.Modal(document.getElementById('leadModal')).show();
    }
</script>
@endsection