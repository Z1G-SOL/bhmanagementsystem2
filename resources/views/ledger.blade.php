@extends('layouts.app')
@section('content')

@if(session('success')) <div class="alert alert-success shadow-sm mb-4">{{ session('success') }}</div> @endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <h4 class="card-title fw-bold text-dark p-4 border-bottom mb-0">Financial Balance Ledger Engine</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-uppercase small text-muted">
                        <th class="ps-4 py-3">Identity Signature</th><th class="py-3">Cell</th><th class="py-3">Base Premium</th>
                        <th class="py-3">Total Accounted</th><th class="py-3">Dynamic Balance Owed</th><th class="py-3 text-center pe-4">Execution Interface</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($tenants as $tenant)
                        <tr class="{{ $tenant->is_rent_overdue ? 'table-danger' : '' }}">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $tenant->photo_path) }}" class="rounded-circle object-fit-cover" style="width: 40px; height: 40px;">
                                    <div><span class="fw-bold d-block text-dark">{{ $tenant->name }}</span> @if($tenant->is_rent_overdue) <span class="badge bg-danger">⚠️ OVERDUE</span> @endif</div>
                                </div>
                            </td>
                            <td class="py-3 fw-bold font-monospace">{{ $tenant->room->room_number }}</td>
                            <td class="py-3">PHP {{ number_format($tenant->room->monthly_rate, 2) }}</td>
                            <td class="py-3 text-success fw-bold">PHP {{ number_format($tenant->total_paid, 2) }}</td>
                            <td class="py-3 fw-bold {{ $tenant->balance_owed > 0 ? 'text-danger' : 'text-muted' }}">PHP {{ number_format($tenant->balance_owed, 2) }}</td>
                            <td class="py-3 pe-4 text-center">
                                <button onclick="openPaymentModal({{ $tenant->id }}, '{{ $tenant->name }}')" class="btn btn-success btn-sm fw-bold mb-2">Add Payment</button>
                                <div class="d-flex flex-wrap justify-content-center gap-1" style="max-width: 150px; margin: 0 auto;">
                                    @forelse($tenant->payments as $payment) <a href="{{ route('payments.receipt', $payment->id) }}" class="badge border border-secondary text-dark text-decoration-none bg-light">🧾 PHP {{ number_format($payment->amount_paid, 0) }}</a>
                                    @empty <span class="small text-muted">No logs</span> @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-5 text-center text-muted">Zero active data points matching query parameter arrays.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header bg-success text-white"><h6 class="modal-title fw-bold">Log Legal Currency Record</h6><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <form id="paymentForm" method="POST">
          @csrf
          <div class="modal-body">
              <p class="small text-muted mb-3">Target Payload Reference: <br><strong id="paymentModalTenantName" class="text-dark"></strong></p>
              <label class="form-label fw-bold text-secondary">Amount Credited (PHP)</label>
              <input type="number" name="amount_paid" step="0.01" required min="1" class="form-control">
          </div>
          <div class="modal-footer border-top-0"><button type="submit" class="btn btn-success w-100 fw-bold">Commit Payment to Ledger</button></div>
      </form>
    </div>
  </div>
</div>

<script>
    function openPaymentModal(tenantId, tenantName) {
        document.getElementById('paymentForm').action = "/tenants/" + tenantId + "/payments";
        document.getElementById('paymentModalTenantName').innerText = tenantName;
        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    }
</script>
@endsection