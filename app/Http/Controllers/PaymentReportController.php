<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentChannel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = array_merge([
            'date_from' => null,
            'date_to' => null,
            'status' => null,
            'channel' => null,
        ], $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:paid,pending,waiting_verification,expired,failed'],
            'channel' => ['nullable', 'string'],
        ]));

        $query = Payment::with(['sale.items.product', 'sale.user', 'channel'])
            ->latest('created_at');

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['channel'])) {
            $query->where('payment_channel', $filters['channel']);
        }

        $payments = $query->paginate(20)->withQueryString();

        // Aggregates for report header
        $statsQuery = clone $query;
        $totalPaid = (clone $query)->where('status', 'paid')->sum('total_amount');
        $totalPending = (clone $query)->whereIn('status', ['pending', 'waiting_verification'])->sum('total_amount');
        $totalTransactionsCount = (clone $query)->count();

        $channels = PaymentChannel::orderBy('sort_order')->get();

        return view('admin.reports.payments', compact(
            'payments',
            'filters',
            'totalPaid',
            'totalPending',
            'totalTransactionsCount',
            'channels'
        ));
    }
}
