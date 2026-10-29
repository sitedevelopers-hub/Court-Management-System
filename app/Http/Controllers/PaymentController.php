<?php

namespace App\Http\Controllers;

use App\Models\CaseFee;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $payments = Payment::with(['fee.courtCase', 'receiver'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('receipt_number', 'like', "%{$search}%")
                        ->orWhere('transaction_reference', 'like', "%{$search}%")
                        ->orWhereHas('fee', function ($feeQuery) use ($search) {
                            $feeQuery->where('fee_reference', 'like', "%{$search}%")
                                ->orWhereHas('courtCase', function ($caseQuery) use ($search) {
                                    $caseQuery->where('case_number', 'like', "%{$search}%")
                                        ->orWhere('case_title', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest('paid_at')
            ->paginate(10)
            ->withQueryString();

        return view('payments.index', compact('payments', 'search'));
    }

    public function create(Request $request)
    {
        $fees = CaseFee::with('courtCase')
            ->whereIn('status', ['unpaid', 'partially_paid'])
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedFeeId = $request->query('case_fee_id');

        return view('payments.create', compact('fees', 'selectedFeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_fee_id' => ['required', 'exists:case_fees,id'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'payment_method' => [
                'required',
                Rule::in(['cash', 'mpesa', 'bank_transfer', 'card', 'cheque', 'other']),
            ],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['required', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment = DB::transaction(function () use ($validated) {
            $fee = CaseFee::whereKey($validated['case_fee_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $totalAmount = (float) $fee->amount;
            $alreadyPaid = (float) $fee->amount_paid;
            $paymentAmount = (float) $validated['amount'];
            $outstanding = round($totalAmount - $alreadyPaid, 2);

            if ($outstanding <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'case_fee_id' => 'This fee has already been paid in full.',
                ]);
            }

            if ($paymentAmount > $outstanding) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'The payment exceeds the outstanding balance of KES '
                        . number_format($outstanding, 2) . '.',
                ]);
            }

            $payment = Payment::create([
                'case_fee_id' => $fee->id,
                'received_by' => auth()->id(),
                'receipt_number' => $this->generateReceiptNumber(),
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'paid_at' => $validated['paid_at'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            $newPaidAmount = round($alreadyPaid + $paymentAmount, 2);

            $fee->amount_paid = $newPaidAmount;
            $fee->status = $this->calculateFeeStatus($totalAmount, $newPaidAmount);
            $fee->save();

            return $payment;
        });

        return redirect()
            ->route('payments.show', $payment)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['fee.courtCase', 'fee.participant', 'receiver']);

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        return redirect()
            ->route('payments.show', $payment)
            ->with('info', 'Completed payments cannot be edited directly. Record a financial adjustment if a correction is required.');
    }

    public function update(Request $request, Payment $payment)
    {
        return redirect()
            ->route('payments.show', $payment)
            ->with('error', 'Completed payments cannot be changed directly. Use the financial adjustment process to preserve the audit trail.');
    }

    public function destroy(Payment $payment)
    {
        return redirect()
            ->route('payments.index')
            ->with('error', 'Payment records cannot be deleted because they form part of the financial audit trail.');
    }

    private function generateReceiptNumber(): string
    {
        do {
            $receipt = 'RCT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Payment::where('receipt_number', $receipt)->exists());

        return $receipt;
    }

    private function calculateFeeStatus(float $total, float $paid): string
    {
        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $total) {
            return 'paid';
        }

        return 'partially_paid';
    }
}
