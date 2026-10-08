@extends('layouts.app')

@section('title', 'Edit Invoice - ' . $invoice->invoice_number)
@section('page_title', 'Edit Invoice ' . $invoice->invoice_number)

@section('content')
<div x-data="invoiceEditForm()" class="space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center space-x-2 text-xs font-bold text-zinc-600 hover:text-zinc-950 transition-colors">
            <span>← Kembali ke Detail Invoice</span>
        </a>
        <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Invoice Number: <strong class="text-zinc-950 font-mono text-xs">{{ $invoice->invoice_number }}</strong></span>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- LEFT COLUMN: FORM INPUT -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-xs space-y-6">
            <div class="border-b border-zinc-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-zinc-950">Edit Form Invoice</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Perbarui rincian invoice. Perubahan akan disinkronkan secara langsung.</p>
                </div>
                <span class="px-2.5 py-1 bg-zinc-100 text-zinc-900 text-[10px] font-bold rounded-full border border-zinc-300">Live Preview Synchronized</span>
            </div>

            <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Customer Selection -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700">Customer <span class="text-rose-500">*</span></label>
                    <select name="customer_id" 
                            x-model="customerId" 
                            required 
                            class="w-full px-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 focus:bg-white focus:outline-none transition-all">
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ (string)old('customer_id', $invoice->customer_id) === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }} {{ $c->trashed() ? ' (Non-Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dates & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Tanggal Invoice <span class="text-rose-500">*</span></label>
                        <input type="date" name="invoice_date" x-model="invoiceDate" required class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Jatuh Tempo <span class="text-rose-500">*</span></label>
                        <input type="date" name="due_date" x-model="dueDate" required class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Status Invoice <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="status" required class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                            <option value="draft">Draft</option>
                            <option value="unpaid">Unpaid / Belum Dibayar</option>
                            <option value="paid">Paid / Lunas</option>
                            <option value="overdue">Overdue / Jatuh Tempo</option>
                        </select>
                    </div>
                </div>

                <!-- Dynamic Items Table -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-2">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700">Item & Layanan <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-zinc-400 font-semibold" x-text="items.length + ' item'"></span>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 bg-zinc-50 border border-zinc-200/90 rounded-2xl space-y-3 relative">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <input type="text" 
                                               :name="'items['+index+'][item_name]'" 
                                               x-model="item.item_name" 
                                               placeholder="Nama Item..." 
                                               required 
                                               class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                                    </div>
                                    <button type="button" 
                                            @click="removeItem(index)" 
                                            x-show="items.length > 1" 
                                            class="p-2 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition-colors">
                                        ✕
                                    </button>
                                </div>

                                <div>
                                    <input type="text" 
                                           :name="'items['+index+'][description]'" 
                                           x-model="item.description" 
                                           placeholder="Deskripsi singkat item..." 
                                           class="w-full px-3 py-1.5 bg-white border border-zinc-200 rounded-xl text-xs focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                                </div>

                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-zinc-500 mb-1">Qty</label>
                                        <input type="number" 
                                               step="0.01" 
                                               min="0.01" 
                                               :name="'items['+index+'][quantity]'" 
                                               x-model.number="item.quantity" 
                                               required 
                                               class="w-full px-3 py-1.5 bg-white border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-zinc-500 mb-1">Harga Satuan (Rp)</label>
                                        <input type="number" 
                                               step="1" 
                                               min="0" 
                                               :name="'items['+index+'][unit_price]'" 
                                               x-model.number="item.unit_price" 
                                               required 
                                               class="w-full px-3 py-1.5 bg-white border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-zinc-500 mb-1">Diskon Item (Rp)</label>
                                        <input type="number" 
                                               step="1" 
                                               min="0" 
                                               :name="'items['+index+'][discount]'" 
                                               x-model.number="item.discount" 
                                               class="w-full px-3 py-1.5 bg-white border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                                    </div>
                                </div>

                                <div class="text-right pt-1 border-t border-zinc-200/80 text-xs font-semibold text-zinc-600">
                                    Subtotal Item: <strong class="text-zinc-950 font-bold" x-text="formatRupiah(getItemTotal(item))"></strong>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" 
                            @click="addItem()" 
                            class="w-full py-2.5 border border-dashed border-zinc-300 hover:border-zinc-950 text-zinc-900 font-bold rounded-2xl text-xs transition-colors flex items-center justify-center space-x-2 bg-white">
                        <span>+ Tambah Item</span>
                    </button>
                </div>

                <!-- Invoice Summary Adjustments (Discount & Tax) -->
                <div class="pt-4 border-t border-zinc-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Diskon Invoice (Rp)</label>
                        <input type="number" step="1" min="0" name="discount" x-model.number="invoiceDiscount" class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Pajak / Tax (Rp)</label>
                        <input type="number" step="1" min="0" name="tax" x-model.number="tax" class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                </div>

                <!-- Notes / Payment Info -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700">Catatan / Payment Notes</label>
                    <textarea name="notes" x-model="notes" rows="3" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs focus:ring-2 focus:ring-zinc-950 focus:outline-none"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-zinc-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('invoices.show', $invoice) }}" class="px-5 py-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold rounded-2xl text-xs">Batal</a>
                    <button type="submit" class="px-8 py-3 bg-zinc-950 hover:bg-black text-white font-bold rounded-2xl text-xs shadow-md transition-all hover:scale-105 border border-zinc-900">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- RIGHT COLUMN: LIVE INVOICE PREVIEW -->
        <div class="lg:col-span-5 sticky top-24 space-y-3">
            <div class="flex items-center justify-between px-2">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Live Preview A4</span>
                <span class="text-[10px] font-bold text-zinc-900 bg-zinc-100 px-2.5 py-0.5 rounded-full border border-zinc-300">Synchronized</span>
            </div>

            <!-- Preview Card -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200 shadow-xl space-y-6 text-zinc-900 text-xs">
                
                <!-- Invoice Header -->
                <div class="flex items-start justify-between border-b border-zinc-200 pb-6">
                    <div>
                        @if(!empty($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo']))
                            <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" class="h-16 md:h-20 max-h-24 w-auto object-contain mb-3">
                        @else
                            <div class="font-black text-xl text-zinc-950 tracking-tight mb-2">
                                {{ $settings['business_name'] ?? 'NAMA BISNIS' }}
                            </div>
                        @endif
                        <p class="font-extrabold text-zinc-950 text-sm">{{ $settings['business_name'] ?? 'Nama Bisnis Anda' }}</p>
                        <p class="text-zinc-500 text-[11px] whitespace-pre-line mt-0.5">{{ $settings['address'] ?? 'Alamat Bisnis' }}</p>
                        <p class="text-zinc-500 text-[11px] mt-0.5">{{ $settings['phone'] ?? '' }} {{ !empty($settings['email']) ? '• ' . $settings['email'] : '' }}</p>
                    </div>

                    <div class="text-right">
                        <span class="text-xl font-black text-zinc-950 tracking-wider block">INVOICE</span>
                        <span class="font-mono font-bold text-zinc-950 block mt-1 text-xs">{{ $invoice->invoice_number }}</span>
                        
                        <div class="mt-2 inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                             :class="{
                                'bg-zinc-100 text-zinc-700 border-zinc-300': status === 'draft',
                                'bg-zinc-100 text-zinc-950 border-zinc-950': status === 'unpaid',
                                'bg-zinc-950 text-white border-zinc-950': status === 'paid',
                                'bg-rose-50 text-rose-800 border-rose-300': status === 'overdue'
                             }">
                            <span x-text="status.toUpperCase()"></span>
                        </div>
                    </div>
                </div>

                <!-- Invoice Meta & Customer Bill To -->
                <div class="grid grid-cols-2 gap-4 pb-4 border-b border-zinc-200">
                    <div>
                        <span class="block text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Bill To:</span>
                        <p class="font-extrabold text-zinc-950 text-xs mt-0.5" x-text="selectedCustomer ? selectedCustomer.name : 'Pilih Customer...'"></p>
                        <p class="text-zinc-500 text-[11px]" x-text="selectedCustomer ? selectedCustomer.email : ''"></p>
                        <p class="text-zinc-500 text-[11px] font-mono" x-text="selectedCustomer ? selectedCustomer.phone : ''"></p>
                        <p class="text-zinc-500 text-[11px] whitespace-pre-line mt-0.5" x-text="selectedCustomer ? selectedCustomer.address : ''"></p>
                    </div>
                    <div class="text-right space-y-1">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase text-zinc-400 block">Tanggal Invoice:</span>
                            <span class="font-semibold text-zinc-900" x-text="formatDate(invoiceDate)"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase text-zinc-400 block">Jatuh Tempo:</span>
                            <span class="font-bold text-zinc-950" x-text="formatDate(dueDate)"></span>
                        </div>
                    </div>
                </div>

                <!-- Preview Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-zinc-950 text-[10px] font-extrabold uppercase tracking-wider text-zinc-950">
                                <th class="py-2">Item</th>
                                <th class="py-2 text-center">Qty</th>
                                <th class="py-2 text-right">Price</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            <template x-for="(item, i) in items" :key="i">
                                <tr>
                                    <td class="py-2.5 pr-2">
                                        <p class="font-bold text-zinc-950" x-text="item.item_name || 'Nama Item'"></p>
                                        <p class="text-[10px] text-zinc-500" x-text="item.description"></p>
                                    </td>
                                    <td class="py-2.5 text-center font-semibold" x-text="item.quantity || 1"></td>
                                    <td class="py-2.5 text-right font-semibold" x-text="formatRupiah(item.unit_price)"></td>
                                    <td class="py-2.5 text-right font-bold text-zinc-950" x-text="formatRupiah(getItemTotal(item))"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Summary Totals -->
                <div class="border-t border-zinc-950 pt-4 space-y-1 text-right">
                    <div class="flex justify-between text-zinc-600">
                        <span>Subtotal:</span>
                        <span class="font-semibold text-zinc-950" x-text="formatRupiah(calculateSubtotal())"></span>
                    </div>
                    <div x-show="invoiceDiscount > 0" class="flex justify-between text-zinc-600">
                        <span>Diskon:</span>
                        <span class="font-semibold text-rose-600" x-text="'- ' + formatRupiah(invoiceDiscount)"></span>
                    </div>
                    <div x-show="tax > 0" class="flex justify-between text-zinc-600">
                        <span>Pajak (Tax):</span>
                        <span class="font-semibold text-zinc-950" x-text="formatRupiah(tax)"></span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-zinc-950 pt-2 border-t border-zinc-300">
                        <span>GRAND TOTAL:</span>
                        <span class="text-zinc-950" x-text="formatRupiah(calculateGrandTotal())"></span>
                    </div>
                </div>

                <!-- Preview Notes & Bank Info -->
                <div class="border-t border-zinc-100 pt-4 space-y-2">
                    <div x-show="notes">
                        <span class="block text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 mb-0.5">Payment Notes:</span>
                        <p class="text-zinc-600 text-[11px] whitespace-pre-line" x-text="notes"></p>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function invoiceEditForm() {
    @php
        $initialItems = old('items', $invoice->items->map(function($i) {
            return [
                'item_name' => $i->item_name,
                'description' => $i->description,
                'quantity' => (float)$i->quantity,
                'unit_price' => (float)$i->unit_price,
                'discount' => (float)$i->discount,
            ];
        })->toArray());

        if (empty($initialItems)) {
            $initialItems = [['item_name' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'discount' => 0]];
        }

        $invDateStr = old('invoice_date', $invoice->invoice_date ? (is_string($invoice->invoice_date) ? substr($invoice->invoice_date, 0, 10) : $invoice->invoice_date->format('Y-m-d')) : date('Y-m-d'));
        $dueDateStr = old('due_date', $invoice->due_date ? (is_string($invoice->due_date) ? substr($invoice->due_date, 0, 10) : $invoice->due_date->format('Y-m-d')) : date('Y-m-d'));
    @endphp

    const rawItems = @json(array_values($initialItems));

    return {
        customerList: @json($customers),
        customerId: @json((string)old('customer_id', $invoice->customer_id)),
        invoiceDate: @json($invDateStr),
        dueDate: @json($dueDateStr),
        status: @json(old('status', $invoice->status)),
        items: Array.isArray(rawItems) ? rawItems.map(function(item) {
            return {
                item_name: item.item_name || '',
                description: item.description || '',
                quantity: parseFloat(item.quantity) || 1,
                unit_price: parseFloat(item.unit_price) || 0,
                discount: parseFloat(item.discount) || 0
            };
        }) : [{ item_name: '', description: '', quantity: 1, unit_price: 0, discount: 0 }],
        invoiceDiscount: parseFloat(@json(old('discount', $invoice->discount))) || 0,
        tax: parseFloat(@json(old('tax', $invoice->tax))) || 0,
        notes: @json(old('notes', $invoice->notes ?? '')),

        get selectedCustomer() {
            return this.customerList.find(c => String(c.id) === String(this.customerId)) || null;
        },

        addItem() {
            this.items.push({ item_name: '', description: '', quantity: 1, unit_price: 0, discount: 0 });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        getItemTotal(item) {
            const qty = parseFloat(item.quantity) || 0;
            const price = parseFloat(item.unit_price) || 0;
            const disc = parseFloat(item.discount) || 0;
            return Math.max(0, (qty * price) - disc);
        },

        calculateSubtotal() {
            return this.items.reduce((sum, item) => sum + this.getItemTotal(item), 0);
        },

        calculateGrandTotal() {
            const sub = this.calculateSubtotal();
            const disc = parseFloat(this.invoiceDiscount) || 0;
            const tx = parseFloat(this.tax) || 0;
            return Math.max(0, (sub - disc) + tx);
        },

        formatRupiah(number) {
            const val = parseFloat(number) || 0;
            return 'Rp ' + val.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return dateStr;
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    };
}
</script>
@endpush
