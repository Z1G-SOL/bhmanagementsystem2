<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LedgerController extends Controller
{
    public function index()
    {
        $tenants = Tenant::where('user_id', Auth::id())->with(['room', 'payments'])->get();
        return view('ledger', compact('tenants'));
    }

    public function addPayment(Request $request, $tenantId)
    {
        $request->validate(['amount_paid' => 'required|numeric|min:1']);
        $tenant = Tenant::where('user_id', Auth::id())->findOrFail($tenantId);

        Payment::create([
            'tenant_id' => $tenant->id,
            'amount_paid' => $request->amount_paid,
            'payment_date' => Carbon::now(),
        ]);

        return redirect()->route('ledger.index')->with('success', 'Payment executed and logged into ledger.');
    }

    public function downloadReceipt($paymentId)
    {
        $payment = Payment::with(['tenant.room', 'tenant.landlord'])->findOrFail($paymentId);
        if ($payment->tenant->user_id !== Auth::id()) abort(403);

        $html = "
        <div style='font-family: sans-serif; padding: 30px; border: 2px solid #333;'>
            <h2>RECEIPT OF PAYMENT</h2>
            <p><strong>Boarding House:</strong> {$payment->tenant->landlord->boarding_house_name}</p>
            <p><strong>Landlord:</strong> {$payment->tenant->landlord->name}</p>
            <hr/>
            <p><strong>Tenant Reference:</strong> {$payment->tenant->name}</p>
            <p><strong>Room Target:</strong> {$payment->tenant->room->room_number}</p>
            <p><strong>Amount Credited:</strong> PHP " . number_format($payment->amount_paid, 2) . "</p>
            <p><strong>Transaction Date:</strong> " . $payment->payment_date->format('M d, Y') . "</p>
        </div>";

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="receipt-payment-'.$payment->id.'.html"');
    }
}