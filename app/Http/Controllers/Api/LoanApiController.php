<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanApiController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $loans = Loan::query()
            ->with('borrower')
            ->when(in_array($status, ['pending', 'active', 'paid', 'overdue', 'closed', 'cancelled'], true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return response()->json($loans);
    }

    public function show(Loan $loan)
    {
        $loan->load(['borrower', 'payments']);

        return response()->json($loan);
    }

    public function payments(Loan $loan)
    {
        $payments = $loan->payments()->latest('paid_at')->get();

        return response()->json([
            'loan_id' => $loan->id,
            'reference_no' => $loan->reference_no,
            'payments' => $payments,
        ]);
    }
}

