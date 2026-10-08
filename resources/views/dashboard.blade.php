@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Ringkasan')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action Banner with Subtle Mesh Gradient Glow -->
    <div class="bg-gradient-to-r from-zinc-950 via-zinc-900 to-zinc-950 rounded-3xl p-6 md:p-8 text-white border border-zinc-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <!-- <div class="inline-flex items-center space-x-2 bg-zinc-800/90 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest text-zinc-300 border border-zinc-700/80 mb-3 shadow-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Invoice Management Core</span>
            </div> -->
            <h2 class="text-2xl md:text-3xl font-black tracking-tight text-white">Invoice Maker</h2>
            <p class="text-zinc-400 text-xs mt-1.5 max-w-xl leading-relaxed">Kelola pembuatan invoice, data customer, dan riwayat transaksi bisnis Anda dalam antarmuka yang bersih, cepat, dan presisi.</p>
        </div>
        <a href="{{ route('invoices.create') }}" 
           class="inline-flex items-center justify-center space-x-2 bg-white text-zinc-950 font-extrabold px-6 py-3.5 rounded-2xl shadow-sm hover:bg-zinc-100 transition-all hover:scale-[1.03] active:scale-[0.98] shrink-0 relative z-10">
            <svg class="w-4 h-4 text-zinc-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Buat Invoice Baru</span>
        </a>
    </div>

    <!-- Summary Metrics Cards with Professional Vector SVG Icons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Invoice Card -->
        <div class="bg-white rounded-3xl p-6 border border-zinc-200/90 shadow-xs hover:border-zinc-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Total Invoice</span>
                <div class="w-10 h-10 rounded-2xl bg-zinc-100 text-zinc-900 flex items-center justify-center border border-zinc-200 group-hover:bg-zinc-950 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black text-zinc-950 tracking-tight">{{ number_format($totalInvoicesCount) }}</span>
                <span class="text-xs text-zinc-500 font-semibold ml-1">berkas</span>
            </div>
        </div>

        <!-- Belum Dibayar Card -->
        <div class="bg-white rounded-3xl p-6 border border-zinc-200/90 shadow-xs hover:border-zinc-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600">Belum Dibayar</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-800 flex items-center justify-center border border-amber-200 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black text-zinc-950 tracking-tight">{{ number_format($unpaidInvoicesCount) }}</span>
                <span class="text-xs text-zinc-500 font-semibold ml-1">perlu ditagih</span>
            </div>
        </div>

        <!-- Invoice Lunas Card -->
        <div class="bg-white rounded-3xl p-6 border border-zinc-200/90 shadow-xs hover:border-zinc-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">Invoice Lunas</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-black text-zinc-950 tracking-tight">{{ number_format($paidInvoicesCount) }}</span>
                <span class="text-xs text-zinc-500 font-semibold ml-1">terbayar</span>
            </div>
        </div>

        <!-- Total Nilai Invoice Card -->
        <div class="bg-white rounded-3xl p-6 border border-zinc-200/90 shadow-xs hover:border-zinc-300 transition-all group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Total Nilai Invoice</span>
                <div class="w-10 h-10 rounded-2xl bg-zinc-950 text-white flex items-center justify-center border border-zinc-900 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xl font-black text-zinc-950 tracking-tight">Rp {{ number_format($totalInvoiceAmount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Invoices Table Section -->
    <div class="bg-white rounded-3xl border border-zinc-200/90 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-zinc-950">Invoice Terbaru</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Daftar transaksi invoice yang baru saja ditambahkan ke sistem.</p>
            </div>
            <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-zinc-950 hover:underline flex items-center space-x-1.5">
                <span>Lihat History Lengkap</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        @if($recentInvoices->isEmpty())
            <!-- Vector SVG Empty State -->
            <div class="p-12 text-center">
                <div class="w-12 h-12 bg-zinc-100 text-zinc-700 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-zinc-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4 class="text-base font-extrabold text-zinc-950">Belum ada invoice</h4>
                <p class="text-xs text-zinc-500 mt-1 max-w-sm mx-auto">Mulai buat invoice pertama Anda untuk mencatat transaksi penjualan bisnis secara profesional.</p>
                <div class="mt-5">
                    <a href="{{ route('invoices.create') }}" class="inline-flex items-center space-x-2 bg-zinc-950 hover:bg-black text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-xs transition-all">
                        <span>+ Buat Invoice</span>
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/80 border-b border-zinc-100 text-[10px] font-extrabold uppercase tracking-wider text-zinc-500">
                            <th class="py-3.5 px-6">Nomor Invoice</th>
                            <th class="py-3.5 px-6">Customer</th>
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">Total</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs">
                        @foreach($recentInvoices as $invoice)
                            <tr class="hover:bg-zinc-50/60 transition-colors">
                                <td class="py-4 px-6 font-bold font-mono text-zinc-950">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="py-4 px-6 font-semibold text-zinc-900">
                                    {{ $invoice->customer ? $invoice->customer->name : 'N/A' }}
                                </td>
                                <td class="py-4 px-6 text-zinc-500 font-medium">
                                    {{ $invoice->invoice_date->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6 font-extrabold text-zinc-950">
                                    {{ $invoice->formatted_total }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $invoice->status_badge_class }}">
                                        {{ $invoice->status_label }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-1.5">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold rounded-lg text-[11px] transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center px-2.5 py-1.5 bg-zinc-900 hover:bg-black text-white font-bold rounded-lg text-[11px] transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice {{ $invoice->invoice_number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg text-[11px] transition-colors border border-rose-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
