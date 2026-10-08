@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page_title', 'Detail Invoice')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Action Bar (no-print) - Removed PDF Download as requested -->
    <div class="no-print bg-white p-4 md:p-5 rounded-3xl border border-zinc-200/90 shadow-xs flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-zinc-600 hover:text-zinc-950 transition-colors">
            <span>← Kembali ke History</span>
        </a>

        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Download PDF Button -->
            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" 
               class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-4 py-2.5 rounded-xl text-xs shadow-xs transition-all border border-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Download PDF</span>
            </a>

            <!-- Cetak / Print PDF Button (Full Complete Document) -->
            <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" 
               class="inline-flex items-center space-x-2 bg-zinc-950 hover:bg-black text-white font-extrabold px-4.5 py-2.5 rounded-xl text-xs shadow-xs transition-all border border-zinc-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak / Print PDF Invoice</span>
            </a>

            <!-- Edit -->
            <a href="{{ route('invoices.edit', $invoice) }}" 
               class="inline-flex items-center space-x-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold px-4 py-2.5 rounded-xl text-xs transition-colors border border-zinc-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit</span>
            </a>

            <!-- Delete -->
            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice {{ $invoice->invoice_number }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center space-x-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-4 py-2.5 rounded-xl text-xs transition-colors border border-rose-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Delete</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Printable Invoice Document Card -->
    <div class="printable-card relative overflow-hidden print:overflow-visible bg-white rounded-3xl p-8 md:p-12 border border-zinc-200/90 shadow-lg print-shadow-none print-p-0 space-y-8 text-zinc-900">
        
        <!-- Transparent PNG Watermark Stamp (Lunas.png / BelumLunas.png / JatuhTempo.png) -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none overflow-hidden z-10 p-2" aria-hidden="true">
            <img src="{{ asset('images/stamps/' . $invoice->stamp_image) }}" 
                 alt="Stempel {{ $invoice->status_label }}" 
                 class="w-[96%] sm:w-[92%] max-w-[780px] h-auto object-contain opacity-[0.45] print:opacity-[0.45] transition-all">
        </div>
        
        <!-- Header: Logo & Business Info + Invoice Title -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-b border-zinc-200 pb-8 gap-6">
            <div>
                @if(!empty($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo']))
                    <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo" class="h-16 md:h-20 max-h-24 w-auto object-contain mb-4">
                @endif
                <h2 class="text-xl font-extrabold text-zinc-950 tracking-tight">{{ $settings['business_name'] ?? 'Nama Bisnis Anda' }}</h2>
                <p class="text-zinc-500 text-xs whitespace-pre-line mt-1 leading-relaxed">{{ $settings['address'] ?? 'Alamat Bisnis' }}</p>
                <p class="text-zinc-500 text-xs mt-1">{{ $settings['phone'] ?? '' }} {{ !empty($settings['email']) ? '• ' . $settings['email'] : '' }}</p>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-3xl font-black text-zinc-950 tracking-wider block">INVOICE</span>
                <span class="font-mono text-base font-bold text-zinc-950 block mt-1">{{ $invoice->invoice_number }}</span>
                
                <div class="mt-3 inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $invoice->status_badge_class }}">
                    {{ strtoupper($invoice->status_label) }}
                </div>
            </div>
        </div>

        <!-- Bill To & Dates Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pb-6 border-b border-zinc-200">
            <div>
                <span class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-400 mb-1">Bill To (Ditujukan Kepada):</span>
                @if($invoice->customer)
                    <p class="font-extrabold text-zinc-950 text-base">{{ $invoice->customer->name }}</p>
                    <p class="text-zinc-600 text-xs mt-1">{{ $invoice->customer->email ?: '' }}</p>
                    <p class="text-zinc-600 text-xs font-mono mt-0.5">{{ $invoice->customer->phone ?: '' }}</p>
                    <p class="text-zinc-600 text-xs whitespace-pre-line mt-1">{{ $invoice->customer->address ?: '' }}</p>
                @else
                    <p class="text-zinc-400 italic text-sm">Data Customer Terhapus</p>
                @endif
            </div>

            <div class="sm:text-right space-y-2">
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-zinc-400 block">Tanggal Invoice:</span>
                    <span class="font-bold text-zinc-950 text-xs">{{ $invoice->invoice_date->format('d F Y') }}</span>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-zinc-400 block">Tanggal Jatuh Tempo:</span>
                    <span class="font-bold text-zinc-950 text-xs">{{ $invoice->due_date->format('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Invoice Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-zinc-950 text-xs font-black uppercase tracking-wider text-zinc-950">
                        <th class="py-3 pr-4">Description</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-right">Unit Price</th>
                        <th class="py-3 px-4 text-right">Discount</th>
                        <th class="py-3 pl-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 text-xs">
                    @foreach($invoice->items as $item)
                        <tr>
                            <td class="py-4 pr-4">
                                <p class="font-bold text-zinc-950 text-sm">{{ $item->item_name }}</p>
                                @if($item->description)
                                    <p class="text-xs text-zinc-500 mt-0.5 whitespace-pre-line">{{ $item->description }}</p>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center font-semibold text-zinc-800">
                                {{ (float)$item->quantity }}
                            </td>
                            <td class="py-4 px-4 text-right font-semibold text-zinc-800">
                                {{ $item->formatted_unit_price }}
                            </td>
                            <td class="py-4 px-4 text-right font-semibold text-zinc-500">
                                {{ $item->discount > 0 ? 'Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-4 pl-4 text-right font-extrabold text-zinc-950">
                                {{ $item->formatted_total }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Totals Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start border-t-2 border-zinc-950 pt-6 gap-6">
            <div class="max-w-xs space-y-2">
                @if(!empty($settings['bank_info']))
                    <div>
                        <span class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-400 mb-1">Informasi Pembayaran:</span>
                        <p class="text-xs font-medium text-zinc-700 whitespace-pre-line bg-zinc-50 p-3.5 rounded-2xl border border-zinc-200">{{ $settings['bank_info'] }}</p>
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-72 space-y-2 text-xs text-right">
                <div class="flex justify-between text-zinc-600">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-zinc-950">{{ $invoice->formatted_subtotal }}</span>
                </div>
                @if($invoice->discount > 0)
                    <div class="flex justify-between text-zinc-600">
                        <span>Diskon Invoice:</span>
                        <span class="font-semibold text-rose-600">- {{ $invoice->formatted_discount }}</span>
                    </div>
                @endif
                @if($invoice->tax > 0)
                    <div class="flex justify-between text-zinc-600">
                        <span>Pajak (Tax):</span>
                        <span class="font-semibold text-zinc-950">{{ $invoice->formatted_tax }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-zinc-950 pt-3 border-t border-zinc-300">
                    <span>TOTAL:</span>
                    <span class="text-zinc-950 text-lg">{{ $invoice->formatted_total }}</span>
                </div>
            </div>
        </div>

        <!-- Notes & Vendor Stamp Footer -->
        <div class="flex flex-col sm:flex-row justify-between items-end border-t border-zinc-200 pt-6 gap-6">
            <div class="flex-1">
                @if($invoice->notes)
                    <span class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-400 mb-1">Catatan / Terms & Conditions:</span>
                    <p class="text-xs text-zinc-600 whitespace-pre-line leading-relaxed">{{ $invoice->notes }}</p>
                @endif
            </div>

            @if(!empty($settings['stamp']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['stamp']))
                <div class="text-center sm:text-right shrink-0">
                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 mb-1">Hormat Kami,</span>
                    <div class="h-20 max-h-24 w-auto flex items-center justify-center sm:justify-end my-1">
                        <img src="{{ asset('storage/' . $settings['stamp']) }}" alt="Stempel Vendor" class="h-full max-h-24 w-auto object-contain">
                    </div>
                    <span class="block text-xs font-bold text-zinc-950 mt-1">{{ $settings['business_name'] ?? '' }}</span>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
