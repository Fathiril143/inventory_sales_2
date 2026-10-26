<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $totalProducts = Product::count();
        $totalTransactions = Purchase::count() + Sale::count();
        $salesToday = Sale::whereDate('sold_at', $today)->where('payment_status', 'paid')->sum('total');
        $salesThisMonth = Sale::whereBetween('sold_at', [$monthStart, now()])->where('payment_status', 'paid')->sum('total');
        
        // Total revenue paid & pending
        $totalRevenuePaid = Sale::where('payment_status', 'paid')->sum('total');
        $totalPendingAmount = Sale::whereIn('payment_status', ['pending', 'waiting_verification'])->sum('total');
        $waitingVerificationCount = Payment::where('status', 'waiting_verification')->count();

        // Breakdown per payment method
        $paymentMethodStats = Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total_amount'))
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->get();

        // Pending verifications list for quick action
        $pendingVerifications = Payment::with(['sale', 'channel'])
            ->where('status', 'waiting_verification')
            ->latest()
            ->limit(5)
            ->get();

        $lowStockProducts = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalTransactions',
            'salesToday',
            'salesThisMonth',
            'totalRevenuePaid',
            'totalPendingAmount',
            'waitingVerificationCount',
            'paymentMethodStats',
            'pendingVerifications',
            'lowStockProducts',
        ));
    }
}
