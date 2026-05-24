@extends('layouts.app')
@section('content')

<div class="card mx-auto shadow-sm border-0" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5">
        <h3 class="fw-bold text-dark mb-4">Initialize New Real Estate Unit</h3>
        @if($errors->any())
            <div class="alert alert-danger"><strong>Initialization Errors Encountered:</strong><ul class="mb-0 mt-2 small">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>
        @endif
        <form action="{{ route('rooms.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3"><label class="form-label fw-bold text-secondary">Room Identifier</label><input type="text" name="room_number" required class="form-control"></div>
            <div class="mb-3"><label class="form-label fw-bold text-secondary">Monthly Rate (PHP)</label><input type="number" name="monthly_rate" step="0.01" required class="form-control"></div>
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Proximity Anchor</label>
                <select name="nearest_school" required class="form-select bg-light">
                    @foreach($schools as $school) <option value="{{ $school }}">{{ $school }}</option> @endforeach
                </select>
            </div>
            <div class="mb-4"><label class="form-label fw-bold text-secondary">Distance Indicator</label><input type="text" name="distance_indicator" required class="form-control"></div>
            
            <div class="alert alert-primary mb-4 border border-primary">
                <strong class="d-block mb-3">📸 Mandatory Room Imagery Array (3 Photos Required)</strong>
                <div class="mb-2"><label class="form-label fw-bold text-dark small mb-1">Primary Room Capture <span class="text-danger">*</span></label><input type="file" name="room_photo_1" required class="form-control form-control-sm"></div>
                <div class="mb-2"><label class="form-label fw-bold text-dark small mb-1">Internal CR Capture <span class="text-danger">*</span></label><input type="file" name="room_photo_2" required class="form-control form-control-sm"></div>
                <div><label class="form-label fw-bold text-dark small mb-1">Secondary Space Capture <span class="text-danger">*</span></label><input type="file" name="room_photo_3" required class="form-control form-control-sm"></div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Publish Unit Instance</button>
        </form>
    </div>
</div>

@endsection