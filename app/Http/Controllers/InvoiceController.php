<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Setting;
use App\Services\InvoiceCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        if ($monthYear = $request->input('month_year')) {
            $parts = explode('-', $monthYear);
            if (count($parts) === 2) {
                $query->whereYear('invoice_date', $parts[0])
                      ->whereMonth('invoice_date', $parts[1]);
            }
        }

        $invoices = $query->latest('invoice_date')->latest('id')->paginate(10)->withQueryString();

        $statusCounts = [
            'all' => Invoice::count(),
            'draft' => Invoice::where('status', 'draft')->count(),
            'unpaid' => Invoice::where('status', 'unpaid')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
        ];

        $stats = [
            'total_invoices' => (clone $query)->count(),
            'total_revenue' => (clone $query)->where('status', 'paid')->sum('total'),
            'unpaid_revenue' => (clone $query)->whereIn('status', ['unpaid', 'overdue'])->sum('total'),
        ];

        $availableMonths = Invoice::select('invoice_date')
            ->orderBy('invoice_date', 'desc')
            ->get()
            ->filter(fn ($inv) => !empty($inv->invoice_date))
            ->map(function ($inv) {
                return [
                    'key' => $inv->invoice_date->format('Y-m'),
                    'label' => $inv->invoice_date->format('F Y'),
                ];
            })
            ->unique('key')
            ->values();

        return view('invoices.index', compact('invoices', 'statusCounts', 'stats', 'availableMonths'));
    }

    public function export(Request $request)
    {
        $query = Invoice::with(['customer', 'items']);

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

        if ($monthYear = $request->input('month_year')) {
            $parts = explode('-', $monthYear);
            if (count($parts) === 2) {
                $query->whereYear('invoice_date', $parts[0])
                      ->whereMonth('invoice_date', $parts[1]);
            }
        }

        $invoices = $query->orderBy('invoice_date', 'asc')->get();

        $filename = 'Laporan_Transaksi_' . ($request->input('month_year') ?: date('Y-m-d')) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No. Invoice',
                'Tanggal Invoice',
                'Jatuh Tempo',
                'Nama Customer',
                'Email Customer',
                'Telepon Customer',
                'Alamat Customer',
                'Rincian Item / Sewa',
                'Subtotal (Rp)',
                'Diskon (Rp)',
                'Pajak (Rp)',
                'Total (Rp)',
                'Status Pembayaran',
            ]);

            foreach ($invoices as $invoice) {
                $itemsText = $invoice->items->map(function ($item) {
                    $name = !empty($item->item_name) ? $item->item_name : (!empty($item->description) ? $item->description : 'Item Tanpa Nama');
                    $qty = (float) $item->quantity == (int) $item->quantity ? (int) $item->quantity : $item->quantity;
                    return $name . ' (' . $qty . 'x @ Rp ' . number_format($item->unit_price, 0, ',', '.') . ')';
                })->implode('; ');

                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '-',
                    $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-',
                    $invoice->customer ? $invoice->customer->name : '-',
                    $invoice->customer ? $invoice->customer->email : '-',
                    $invoice->customer ? $invoice->customer->phone : '-',
                    $invoice->customer ? $invoice->customer->address : '-',
                    $itemsText ?: '-',
                    number_format($invoice->subtotal, 0, ',', '.'),
                    number_format($invoice->discount, 0, ',', '.'),
                    number_format($invoice->tax, 0, ',', '.'),
                    number_format($invoice->total, 0, ',', '.'),
                    $invoice->status_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

    public function pdf(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'items']);
        $settings = Setting::getAll();

        $logoBase64 = null;
        if (!empty($settings['logo']) && Storage::disk('public')->exists($settings['logo'])) {
            $path = storage_path('app/public/' . $settings['logo']);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $stampBase64 = null;
        $stampPath = public_path('images/stamps/' . $invoice->stamp_image);
        if (file_exists($stampPath)) {
            $stampData = file_get_contents($stampPath);
            $stampBase64 = 'data:image/png;base64,' . base64_encode($stampData);
        }

        $vendorStampBase64 = null;
        if (!empty($settings['stamp']) && Storage::disk('public')->exists($settings['stamp'])) {
            $vStampPath = storage_path('app/public/' . $settings['stamp']);
            if (file_exists($vStampPath)) {
                $type = pathinfo($vStampPath, PATHINFO_EXTENSION);
                $data = file_get_contents($vStampPath);
                $vendorStampBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'settings', 'logoBase64', 'stampBase64', 'vendorStampBase64'));

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Invoice-' . $invoice->invoice_number . '.pdf"',
        ]);
    }
}
