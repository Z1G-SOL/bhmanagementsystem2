@extends('layouts.app')
@section('content')

<div class="card mx-auto shadow-sm border-0 mt-5" style="max-width: 400px;">
    <div class="card-body p-4 p-md-5">
        <h4 class="fw-bold text-dark mb-4 text-center">Landlord Administrative Gate</h4>
        @if($errors->any()) <div class="alert alert-danger small">{{ $errors->first() }}</div> @endif
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3"><label class="form-label fw-bold text-secondary">Email</label><input type="email" name="email" required class="form-control"></div>
            <div class="mb-4"><label class="form-label fw-bold text-secondary">Password</label><input type="password" name="password" required class="form-control"></div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Authenticate</button>
        </form>
    </div>
</div>

@endsection