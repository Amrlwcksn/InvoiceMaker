@extends('layouts.app')

@section('title', 'Buat Invoice Baru')
@section('page_title', 'Buat Invoice Baru')

@section('content')
<div x-data="invoiceForm()" class="space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-zinc-600 hover:text-zinc-950 transition-colors">
            <span>← Kembali ke History Invoice</span>
        </a>
        <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Draft Invoice: <strong class="text-zinc-950 font-mono text-xs">{{ $nextInvoiceNumber }}</strong></span>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- LEFT COLUMN: FORM INPUT -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-xs space-y-6">
            <div class="border-b border-zinc-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-zinc-950">Form Invoice</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Isi detail transaksi. Preview disinkronkan secara langsung.</p>
                </div>
                <span class="px-2.5 py-1 bg-zinc-100 text-zinc-900 text-[10px] font-bold rounded-full border border-zinc-300">Live Preview Ready</span>
            </div>

            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Customer Selection -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700">Customer <span class="text-rose-500">*</span></label>
                        <button type="button" @click="showCustomerModal = true" class="text-xs font-bold text-zinc-950 hover:underline flex items-center space-x-1">
                            <span>+ Tambah Customer Baru</span>
                        </button>
                    </div>
                    <select name="customer_id" 
                            x-model="customerId" 
                            required 
                            class="w-full px-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 focus:bg-white focus:outline-none transition-all">
                        <option value="">-- Pilih Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
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
                                               placeholder="Nama Item / Produk..." 
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
                                           placeholder="Deskripsi singkat item (opsional)..." 
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
                        <input type="number" step="1" min="0" name="discount" x-model.number="invoiceDiscount" placeholder="0" class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Pajak / Tax (Rp)</label>
                        <input type="number" step="1" min="0" name="tax" x-model.number="tax" placeholder="0" class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                </div>

                <!-- Notes / Payment Info -->
                <div class="space-y-1">
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700">Catatan / Payment Notes</label>
                    <textarea name="notes" x-model="notes" rows="3" placeholder="Instruksi pembayaran, nomor rekening bank..." class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs focus:ring-2 focus:ring-zinc-950 focus:outline-none"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-zinc-100 flex items-center justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-zinc-950 hover:bg-black text-white font-bold rounded-2xl text-sm shadow-md transition-all hover:scale-105 border border-zinc-900">
                        Simpan Invoice
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
                
                <!-- Invoice Header (Larger Logo Display) -->
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
                        <p class="text-zinc-500 text-[11px] whitespace-pre-line mt-0.5 leading-relaxed">{{ $settings['address'] ?? 'Alamat Bisnis' }}</p>
                        <p class="text-zinc-500 text-[11px] mt-0.5">{{ $settings['phone'] ?? '' }} {{ !empty($settings['email']) ? '• ' . $settings['email'] : '' }}</p>
                    </div>

                    <div class="text-right">
                        <span class="text-xl font-black text-zinc-950 tracking-wider block">INVOICE</span>
                        <span class="font-mono font-bold text-zinc-950 block mt-1 text-xs">{{ $nextInvoiceNumber }}</span>
                        
                        <div class="mt-2 inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                             :class="{
                                'bg-zinc-100 text-zinc-600 border-zinc-200': status === 'draft',
                                'bg-amber-50 text-amber-900 border-amber-300': status === 'unpaid',
                                'bg-emerald-50 text-emerald-900 border-emerald-300': status === 'paid',
                                'bg-rose-50 text-rose-900 border-rose-300': status === 'overdue'
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

    <!-- Quick Modal Customer Inline -->
    <div x-show="showCustomerModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 p-4">
        <div @click.away="showCustomerModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                <h4 class="font-bold text-zinc-950 text-sm">Tambah Customer Baru</h4>
                <button type="button" @click="showCustomerModal = false" class="text-zinc-400 font-bold">✕</button>
            </div>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-[11px] font-bold text-zinc-700 mb-1">Nama Customer *</label>
                    <input type="text" x-model="newCustomer.name" placeholder="Nama..." class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-zinc-700 mb-1">Email</label>
                    <input type="email" x-model="newCustomer.email" placeholder="Email..." class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-zinc-700 mb-1">Telepon</label>
                    <input type="text" x-model="newCustomer.phone" placeholder="No HP/WA..." class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-zinc-700 mb-1">Alamat</label>
                    <textarea x-model="newCustomer.address" rows="2" placeholder="Alamat..." class="w-full px-3 py-2 border border-zinc-200 rounded-xl text-xs"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-zinc-100">
                <button type="button" @click="showCustomerModal = false" class="px-4 py-2 bg-zinc-100 rounded-xl text-xs font-bold">Batal</button>
                <button type="button" @click="saveQuickCustomer()" class="px-4 py-2 bg-zinc-950 text-white rounded-xl text-xs font-bold shadow-xs">Simpan Customer</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function invoiceForm() {
    @php
        $initialItems = old('items', [
            ['item_name' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'discount' => 0]
        ]);
    @endphp

    const rawItems = @json(array_values($initialItems));

    return {
        customerList: @json($customers),
        customerId: @json((string)old('customer_id', '')),
        invoiceDate: @json(old('invoice_date', date('Y-m-d'))),
        dueDate: @json(old('due_date', \Carbon\Carbon::now()->addDays(14)->format('Y-m-d'))),
        status: @json(old('status', 'unpaid')),
        items: Array.isArray(rawItems) ? rawItems.map(function(item) {
            return {
                item_name: item.item_name || '',
                description: item.description || '',
                quantity: parseFloat(item.quantity) || 1,
                unit_price: parseFloat(item.unit_price) || 0,
                discount: parseFloat(item.discount) || 0
            };
        }) : [{ item_name: '', description: '', quantity: 1, unit_price: 0, discount: 0 }],
        invoiceDiscount: parseFloat(@json(old('discount', 0))) || 0,
        tax: parseFloat(@json(old('tax', 0))) || 0,
        notes: @json(old('notes', $settings['default_notes'] ?? '')),

        showCustomerModal: false,
        newCustomer: { name: '', email: '', phone: '', address: '' },

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
        },

        async saveQuickCustomer() {
            if (!this.newCustomer.name) {
                alert('Nama customer wajib diisi.');
                return;
            }

            try {
                const response = await fetch('{{ route("customers.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.newCustomer)
                });

                const data = await response.json();
                if (data.success) {
                    this.customerList.push(data.customer);
                    this.customerId = String(data.customer.id);
                    this.newCustomer = { name: '', email: '', phone: '', address: '' };
                    this.showCustomerModal = false;
                } else {
                    alert(data.message || 'Gagal menyimpan customer');
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi.');
            }
        }
    };
}
</script>
@endpush
