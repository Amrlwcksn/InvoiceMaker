@extends('layouts.app')

@section('title', 'Detail Customer - ' . $customer->name)
@section('page_title', 'Detail Customer')

@section('content')
<div class="space-y-6">
    
    <!-- Top Nav & Actions -->
    <div class="flex items-center justify-between">
        <a href="{{ route('customers.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-zinc-600 hover:text-zinc-950 transition-colors">
            <span>← Kembali ke Daftar Customer</span>
        </a>
        <div class="flex items-center space-x-3">
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center space-x-2 bg-zinc-950 hover:bg-black text-white font-bold px-4 py-2.5 rounded-xl text-xs shadow-xs transition-all">
                <span>Edit Customer</span>
            </a>
        </div>
    </div>

    <!-- Customer Details Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-xs space-y-6">
        <div class="flex items-start justify-between pb-6 border-b border-zinc-100">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-zinc-950 text-white flex items-center justify-center text-xl font-black shadow-sm">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-zinc-950">{{ $customer->name }}</h2>
                    <p class="text-xs text-zinc-400 mt-0.5">Terdaftar sejak {{ $customer->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-zinc-100 text-zinc-900 text-xs font-bold rounded-full border border-zinc-200">
                {{ $customer->invoices->count() }} Invoices
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Email</span>
                <p class="text-zinc-950 font-bold mt-1 text-xs">{{ $customer->email ?: 'Tidak ada' }}</p>
            </div>
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Telepon</span>
                <p class="text-zinc-950 font-bold font-mono mt-1 text-xs">{{ $customer->phone ?: 'Tidak ada' }}</p>
            </div>
            <div>
                <span class="block text-[10px] font-extrabold uppercase tracking-wider text-zinc-400">Alamat</span>
                <p class="text-zinc-950 font-semibold mt-1 text-xs whitespace-pre-line">{{ $customer->address ?: 'Tidak ada' }}</p>
            </div>
        </div>
    </div>

    <!-- History Invoices Customer -->
    <div class="bg-white rounded-3xl border border-zinc-200/90 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-zinc-950">History Invoice {{ $customer->name }}</h3>
            <a href="{{ route('invoices.create') }}" class="text-xs font-bold text-zinc-950 hover:underline">
                + Buat Invoice Baru
            </a>
        </div>

        @if($customer->invoices->isEmpty())
            <div class="p-8 text-center text-zinc-500 text-xs">
                Belum ada history invoice untuk customer ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/80 border-b border-zinc-100 text-[10px] font-extrabold uppercase tracking-wider text-zinc-500">
                            <th class="py-3.5 px-6">Nomor Invoice</th>
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">Jatuh Tempo</th>
                            <th class="py-3.5 px-6">Total</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs">
                        @foreach($customer->invoices as $invoice)
                            <tr class="hover:bg-zinc-50/60 transition-colors">
                                <td class="py-4 px-6 font-bold font-mono text-zinc-950">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
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
