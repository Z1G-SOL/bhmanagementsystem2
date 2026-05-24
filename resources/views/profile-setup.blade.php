@extends('layouts.app')
@section('content')

<div class="card mx-auto shadow-sm border-0" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5">
        <h3 class="fw-bold text-dark mb-4">Boarding House Administrative Configuration</h3>
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Boarding House Identity Name</label>
                <input type="text" name="boarding_house_name" value="{{ $user->boarding_house_name }}" required class="form-control">
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">Explicit Structural Rules & Operational Conduct Statements</label>
                <textarea name="house_rules" rows="6" required class="form-control font-monospace">{{ $user->house_rules }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Synchronize Administrative Records</button>
        </form>
    </div>
</div>

@endsection