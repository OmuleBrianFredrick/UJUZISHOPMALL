<?php

namespace App\Http\Controllers;

use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCommerceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->is_admin ?? false, 403);

        $days = min(max((int) $request->integer('days', 30), 7), 365);
        $since = now()->subDays($days);

        $summary = [
            'sales' => (float) Order::where('payment_status', 'paid')->where('created_at', '>=', $since)->sum('total'),
            'orders' => Order::where('created_at', '>=', $since)->count(),
            'paid_orders' => Order::where('payment_status', 'paid')->where('created_at', '>=', $since)->count(),
            'customers' => User::where('created_at', '>=', $since)->count(),
            'products' => Product::count(),
            'low_stock' => Product::whereColumn('quantity', '<=', 'reorder_level')->count(),
            'sellers' => SellerProfile::count(),
            'pending_sellers' => SellerProfile::where('status', 'pending')->count(),
            'payments_pending' => Payment::whereIn('status', ['pending', 'processing'])->count(),
            'commission' => (float) FinancialLedger::where('type', 'commission')->where('direction', 'debit')->where('created_at', '>=', $since)->sum('amount'),
            'seller_credits' => (float) FinancialLedger::where('type', 'sale')->where('direction', 'credit')->where('created_at', '>=', $since)->sum('amount'),
        ];

        $recentOrders = Order::with('user')->latest()->limit(10)->get();
        $recentPayments = Payment::with('order')->latest()->limit(10)->get();
        $topProducts = Product::orderByDesc('quantity')->limit(8)->get();

        $salesByDay = Order::where('payment_status', 'paid')->where('created_at', '>=', $since)
            ->selectRaw("DATE(created_at) AS day, SUM(total) AS total, COUNT(*) AS orders")
            ->groupBy('day')->orderBy('day')->get();

        $paymentBreakdown = Payment::select('method', 'status', DB::raw('COUNT(*) AS total'))
            ->groupBy('method', 'status')->orderBy('method')->get();

        return view('admin.commerce.index', compact('summary', 'recentOrders', 'recentPayments', 'topProducts', 'salesByDay', 'paymentBreakdown', 'days'));
    }
}
