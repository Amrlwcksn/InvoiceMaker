<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'status' => ['required', 'in:draft,unpaid,paid,overdue'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak terdaftar.',
            'invoice_date.required' => 'Tanggal invoice wajib diisi.',
            'due_date.required' => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal invoice.',
            'items.required' => 'Invoice harus memiliki minimal satu item.',
            'items.min' => 'Invoice harus memiliki minimal satu item.',
            'items.*.item_name.required' => 'Nama item wajib diisi.',
            'items.*.quantity.required' => 'Jumlah (Qty) item wajib diisi.',
            'items.*.quantity.min' => 'Jumlah (Qty) item minimal 0.01.',
            'items.*.unit_price.required' => 'Harga satuan item wajib diisi.',
            'items.*.unit_price.numeric' => 'Harga satuan harus berupa angka.',
        ];
    }
}
