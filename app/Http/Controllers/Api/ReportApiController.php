<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function collections(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $query = Payment::query();

        if ($from) {
            $query->whereDate('paid_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('paid_at', '<=', $to);
        }

        $total = (float) $query->sum('amount');
        $count = (int) $query->count();

        return response()->json([
            'from' => $from,
            'to' => $to,
            'count' => $count,
            'total_collected' => round($total, 2),
        ]);
    }
}

