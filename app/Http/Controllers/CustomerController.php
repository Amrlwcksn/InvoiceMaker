<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Customer::withCount('invoices');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($customers);
        }

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse|JsonResponse
    {
        $customer = Customer::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil ditambahkan.',
                'customer' => $customer,
            ], 201);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        $customer->load(['invoices' => function ($q) {
            $q->latest();
        }]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse|JsonResponse
    {
        $customer->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil diperbarui.',
                'customer' => $customer,
            ]);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $invoicesCount = $customer->invoices()->count();

        $customer->delete();

        $message = 'Customer berhasil dihapus.';
        if ($invoicesCount > 0) {
            $message = "Customer berhasil diarsipkan (soft delete). {$invoicesCount} invoice terkait tetap tersimpan secara aman.";
        }

        return redirect()->route('customers.index')->with('success', $message);
    }
}
