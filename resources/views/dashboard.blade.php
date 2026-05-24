<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@extends('layouts.app')
@section('content')

@if(session('success')) <div class="alert alert-success shadow-sm mb-4">{{ session('success') }}</div> @endif
@if(!empty($dueTenantsNotifications))
    <div class="alert alert-warning shadow-sm mb-4 border-start border-warning border-4">
        <h5 class="alert-heading fw-bold">🔔 System Automation Tasks Alert Engine</h5>
        <ul class="mb-0 small">@foreach($dueTenantsNotifications as $notif) <li>{{ $notif }}</li> @endforeach</ul>
    </div>
@endif

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm border-top border-primary border-4">
            <div class="card-body">
                <p class="text-muted small text-uppercase fw-bold mb-1">Total Occupancy Status</p>
                <h2 class="fw-bold text-dark">{{ $occupancyRate }}%</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm border-top border-success border-4">
            <div class="card-body">
                <p class="text-muted small text-uppercase fw-bold mb-1">Gross Capital Recouped</p>
                <h2 class="fw-bold text-success">PHP {{ number_format($totalCollected, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm border-top border-danger border-4">
            <div class="card-body">
                <p class="text-muted small text-uppercase fw-bold mb-1">Unrealized Outstandings</p>
                <h2 class="fw-bold text-danger">PHP {{ number_format($totalPending, 2) }}</h2>
            </div>
        </div>
    </div>
</div>

<h4 class="fw-bold text-dark mb-3">Boarding House Real Estate Architecture Grid</h4>
<div class="row row-cols-2 row-cols-md-6 g-3">
    @foreach($rooms as $room)
        <div class="col">
            @if($room->status == 'available')
                <button onclick="openOccupyModal({{ $room->id }}, '{{ $room->room_number }}', {{ json_encode($room->activeLeads) }})" class="btn btn-success w-100 h-100 p-3 text-start shadow-sm d-flex flex-column justify-content-between rounded-3" style="min-height: 120px;">
                    <span class="fs-5 fw-bold d-block">{{ $room->room_number }}</span>
                    <div class="d-flex justify-content-between align-items-end w-100 mt-3">
                        <span class="badge bg-dark bg-opacity-25 text-white">VACANT</span>
                        <span class="badge bg-white text-success rounded-circle">{{ count($room->activeLeads) }}</span>
                    </div>
                </button>
            @else
                <button onclick="openTenantModal('{{ $room->room_number }}', '{{ $room->tenant->name }}', '{{ $room->tenant->contact_number }}', '{{ asset('storage/' . $room->tenant->photo_path) }}', '{{ $room->tenant->move_in_date->format('M d, Y') }}', 'PHP {{ number_format($room->tenant->balance_owed, 2) }}', '{{ route('rooms.vacate', $room->id) }}')" class="btn btn-danger w-100 h-100 p-3 text-start shadow-sm d-flex flex-column justify-content-between rounded-3" style="min-height: 120px;">
                    <span class="fs-5 fw-bold d-block">{{ $room->room_number }}</span>
                    <div class="d-flex justify-content-between align-items-end w-100 mt-3">
                        <span class="badge bg-dark bg-opacity-25 text-white">OCCUPIED</span><span>👤</span>
                    </div>
                </button>
            @endif
        </div>
    @endforeach
</div>

<div class="modal fade" id="occupyModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white"><h5 class="modal-title fw-bold" id="occupyModalTitle">Initialize Shift-To-Occupied Layout</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <form id="occupyForm" method="POST">
          @csrf
          <div class="modal-body">
              <label class="form-label fw-bold text-secondary">Select Converted Lead Signature</label>
              <select name="lead_id" id="leadSelect" required class="form-select mb-2"></select>
          </div>
          <div class="modal-footer"><button type="submit" class="btn btn-success w-100 fw-bold">Transition Room to State: RED</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="tenantModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content text-center">
      <div class="modal-header bg-danger text-white"><h5 class="modal-title fw-bold" id="tenantModalTitle">Tenant Identity Core</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
          <img id="tenantImg" src="" class="rounded-circle border border-danger border-3 mb-3 object-fit-cover" style="width: 100px; height: 100px;">
          <h4 id="tenantName" class="fw-bold mb-0"></h4>
          <p id="tenantContact" class="text-muted small mb-3"></p>
          <div class="bg-light p-2 rounded text-start small mb-3">
              <div class="mb-1"><strong>Move-In Sync:</strong> <span id="tenantInDate"></span></div>
              <div><strong>Calculated Outstandings:</strong> <span id="tenantBalance" class="text-danger fw-bold"></span></div>
          </div>
          <div class="d-flex gap-2">
              <a href="{{ route('ledger.index') }}" class="btn btn-primary btn-sm flex-grow-1 fw-bold">Ledger File</a>
              <form id="vacateForm" method="POST" class="flex-grow-1">@csrf <button type="submit" class="btn btn-dark btn-sm w-100 fw-bold">Evict / Vacate</button></form>
          </div>
      </div>
    </div>
  </div>
</div>

<script>
    function openOccupyModal(roomId, roomNumber, leads) {
        const select = document.getElementById('leadSelect');
        select.innerHTML = '';
        if (leads.length === 0) return alert('No structural entry vectors available inside uncommitted lead parameters.');
        leads.forEach(lead => {
            let option = document.createElement('option'); option.value = lead.id; option.text = lead.renter_name + ' (' + lead.renter_contact + ')'; select.appendChild(option);
        });
        document.getElementById('occupyForm').action = "/rooms/" + roomId + "/occupy";
        document.getElementById('occupyModalTitle').innerText = "Occupy Allocation " + roomNumber;
        new bootstrap.Modal(document.getElementById('occupyModal')).show();
    }
    function openTenantModal(roomNumber, name, contact, photo, inDate, balance, vacateUrl) {
        document.getElementById('tenantModalTitle').innerText = "Passport File: " + roomNumber;
        document.getElementById('tenantName').innerText = name; document.getElementById('tenantContact').innerText = contact;
        document.getElementById('tenantImg').src = photo; document.getElementById('tenantInDate').innerText = inDate;
        document.getElementById('tenantBalance').innerText = balance; document.getElementById('vacateForm').action = vacateUrl;
        new bootstrap.Modal(document.getElementById('tenantModal')).show();
    }
</script>
@endsection