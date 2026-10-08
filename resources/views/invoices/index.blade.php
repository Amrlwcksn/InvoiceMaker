@extends('layouts.app')

@section('title', 'History Invoice')
@section('page_title', 'History Invoice')

@section('content')
<div class="space-y-6">

    <!-- Rekap Omset & Laporan Invoices Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-3xl border border-zinc-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Total Transaksi</span>
                <p class="text-xl font-black text-zinc-950 mt-0.5">{{ number_format($stats['total_invoices']) }} Invoice</p>
            </div>
            <div class="w-9 h-9 rounded-2xl bg-zinc-100 text-zinc-800 flex items-center justify-center font-bold text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-3xl border border-zinc-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">Rekap Omset Lunas</span>
                <p class="text-lg font-black text-emerald-950 mt-0.5">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs border border-emerald-200">
                ✓
            </div>
        </div>

        <div class="bg-white p-4 rounded-3xl border border-zinc-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-rose-600">Belum Lunas</span>
                <p class="text-lg font-black text-rose-950 mt-0.5">Rp {{ number_format($stats['unpaid_revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="w-9 h-9 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs border border-rose-200">
                !
            </div>
        </div>
    </div>
    
    <!-- Status Filter Tabs & Create Action -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-zinc-200/90 shadow-xs">
        
        <!-- Status Tabs -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 lg:pb-0">
            @php
                $currentStatus = request('status', 'all');
            @endphp
            <a href="{{ route('invoices.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus === 'all' ? 'bg-zinc-950 text-white shadow-xs' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                Semua <span class="ml-1 opacity-70">({{ $statusCounts['all'] }})</span>
            </a>
            <a href="{{ route('invoices.index', array_merge(request()->except('status', 'page'), ['status' => 'draft'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus === 'draft' ? 'bg-zinc-950 text-white shadow-xs' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                Draft <span class="ml-1 opacity-70">({{ $statusCounts['draft'] }})</span>
            </a>
            <a href="{{ route('invoices.index', array_merge(request()->except('status', 'page'), ['status' => 'unpaid'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus === 'unpaid' ? 'bg-zinc-950 text-white shadow-xs' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                Belum Dibayar <span class="ml-1 opacity-70">({{ $statusCounts['unpaid'] }})</span>
            </a>
            <a href="{{ route('invoices.index', array_merge(request()->except('status', 'page'), ['status' => 'paid'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus === 'paid' ? 'bg-zinc-950 text-white shadow-xs' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                Lunas <span class="ml-1 opacity-70">({{ $statusCounts['paid'] }})</span>
            </a>
            <a href="{{ route('invoices.index', array_merge(request()->except('status', 'page'), ['status' => 'overdue'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus === 'overdue' ? 'bg-rose-950 text-white shadow-xs' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                Jatuh Tempo <span class="ml-1 opacity-70">({{ $statusCounts['overdue'] }})</span>
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Period Month Filter -->
            <form action="{{ route('invoices.index') }}" method="GET" class="flex items-center">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="month_year" onchange="this.form.submit()" class="bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2 text-xs font-semibold text-zinc-700 focus:outline-none focus:ring-2 focus:ring-zinc-950 cursor-pointer">
                    <option value="">Semua Periode</option>
                    @foreach($availableMonths as $m)
                        <option value="{{ $m['key'] }}" {{ request('month_year') === $m['key'] ? 'selected' : '' }}>
                            {{ $m['label'] }}
                        </option>
                    @endforeach
                </select>
            </form>

            <!-- Search Form -->
            <form action="{{ route('invoices.index') }}" method="GET" class="flex-1 min-w-[200px]">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('month_year'))
                    <input type="hidden" name="month_year" value="{{ request('month_year') }}">
                @endif
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nomor invoice / customer..." 
                           class="w-full pl-9 pr-4 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all">
                    <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>

            <!-- Export Sheets Button -->
            <a href="{{ route('invoices.export', request()->all()) }}" 
               title="Export data transaksi ke format CSV / Sheets"
               class="inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-emerald-600 via-emerald-700 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900 text-white font-black px-4 py-2 rounded-xl text-xs shadow-md border border-emerald-500 hover:scale-[1.02] transition-all shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Export Sheets</span>
            </a>

            <a href="{{ route('invoices.create') }}" 
               class="inline-flex items-center justify-center space-x-2 bg-zinc-950 hover:bg-black text-white font-bold px-4 py-2 rounded-xl text-xs shadow-xs transition-all shrink-0 border border-zinc-900">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Invoice</span>
            </a>
        </div>
    </div>

    <!-- History Invoices Table -->
    <div class="bg-white rounded-3xl border border-zinc-200/90 shadow-xs overflow-hidden">
        @if($invoices->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 bg-zinc-100 text-zinc-700 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-zinc-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4 class="text-base font-extrabold text-zinc-950">Belum ada invoice</h4>
                <p class="text-xs text-zinc-500 mt-1 max-w-sm mx-auto">Mulai buat invoice pertama Anda untuk menyimpan seluruh riwayat transaksi.</p>
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
                            <th class="py-3.5 px-6">Tanggal Invoice</th>
                            <th class="py-3.5 px-6">Jatuh Tempo</th>
                            <th class="py-3.5 px-6">Total</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs">
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-zinc-50/60 transition-colors">
                                <td class="py-4 px-6 font-bold font-mono text-zinc-950">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="py-4 px-6 font-semibold text-zinc-900">
                                    @if($invoice->customer)
                                        <a href="{{ route('customers.show', $invoice->customer) }}" class="hover:underline">
                                            {{ $invoice->customer->name }}
                                        </a>
                                    @else
                                        <span class="text-zinc-400 italic">Terhapus</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-zinc-500 font-medium">
                                    {{ $invoice->invoice_date->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6 text-zinc-500 font-medium">
                                    {{ $invoice->due_date->format('d M Y') }}
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

            <!-- Pagination -->
            <div class="p-4 border-t border-zinc-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
