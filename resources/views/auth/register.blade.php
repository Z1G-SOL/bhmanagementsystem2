@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0 rounded-3">
                <div class="card-body p-4">
                    <h3 class="fw-bold text-dark text-center mb-1">Create Account</h3>
                    <p class="text-muted text-center small mb-4">Join the Davao Student Housing Network</p>

                    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Full Legal Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Maria Santos" required value="{{ old('name') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" required value="{{ old('email') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Mobile Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="e.g. 09123456789" required value="{{ old('phone_number') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Identification Profile Photo</label>
                            <input type="file" name="profile_photo" class="form-control" accept="image/*" required>
                            <div class="form-text small text-muted" style="font-size: 11px;">Clear, front-facing formal profile headshot. Required for booking verification.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Account Role Specification</label>
                            <select name="role" class="form-select" required>
                                <option value="renter" {{ old('role') == 'renter' ? 'selected' : '' }}>Student / Renter (Looking for Spaces)</option>
                                <option value="landlord" {{ old('role') == 'landlord' ? 'selected' : '' }}>Property Manager / Landlord (Leasing Spaces)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Password Configuration</label>
                            <input type="password" name="password" class="form-control" placeholder="At least 8 characters" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Confirm Password Verification</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat your password entry" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Finalize Account Verification
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3 border-0">
                    <span class="small text-muted">Already registered with us? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Sign In Here</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection