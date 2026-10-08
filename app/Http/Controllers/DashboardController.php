<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalInvoicesCount = Invoice::count();
        $unpaidInvoicesCount = Invoice::whereIn('status', ['unpaid', 'overdue'])->count();
        $paidInvoicesCount = Invoice::where('status', 'paid')->count();
        $totalInvoiceAmount = Invoice::sum('total');

        $recentInvoices = Invoice::with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalInvoicesCount',
            'unpaidInvoicesCount',
            'paidInvoicesCount',
            'totalInvoiceAmount',
            'recentInvoices'
        ));
    }
}
