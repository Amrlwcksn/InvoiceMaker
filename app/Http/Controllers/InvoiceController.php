<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Setting;
use App\Services\InvoiceCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with('customer');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            if (in_array($status, ['draft', 'unpaid', 'paid', 'overdue'])) {
                $query->where('status', $status);
            }
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        $statusCounts = [
            'all' => Invoice::count(),
            'draft' => Invoice::where('status', 'draft')->count(),
            'unpaid' => Invoice::where('status', 'unpaid')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
        ];

        return view('invoices.index', compact('invoices', 'statusCounts'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $nextInvoiceNumber = Invoice::generateInvoiceNumber();
        $settings = Setting::getAll();

        return view('invoices.create', compact('customers', 'nextInvoiceNumber', 'settings'));
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $calculated = InvoiceCalculator::calculate(
            $validated['items'],
            $validated['discount'] ?? 0,
            $validated['tax'] ?? 0
        );

        $invoice = DB::transaction(function () use ($validated, $calculated) {
            $invoiceNumber = Invoice::generateInvoiceNumber();

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $calculated['subtotal'],
                'discount' => $calculated['discount'],
                'tax' => $calculated['tax'],
                'total' => $calculated['total'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($calculated['items'] as $itemData) {
                $invoice->items()->create($itemData);
            }

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} berhasil dibuat.");
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);
        $settings = Setting::getAll();

        return view('invoices.show', compact('invoice', 'settings'));
    }

    public function edit(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);
        $customers = Customer::withTrashed()->orderBy('name')->get();
        $settings = Setting::getAll();

        return view('invoices.edit', compact('invoice', 'customers', 'settings'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validated();

        $calculated = InvoiceCalculator::calculate(
            $validated['items'],
            $validated['discount'] ?? 0,
            $validated['tax'] ?? 0
        );

        DB::transaction(function () use ($invoice, $validated, $calculated) {
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $calculated['subtotal'],
                'discount' => $calculated['discount'],
                'tax' => $calculated['tax'],
                'total' => $calculated['total'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete and re-create items cleanly
            $invoice->items()->delete();
            foreach ($calculated['items'] as $itemData) {
                $invoice->items()->create($itemData);
            }
        });

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} berhasil diperbarui.");
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoiceNumber = $invoice->invoice_number;
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', "Invoice {$invoiceNumber} berhasil dihapus.");
    }
}
