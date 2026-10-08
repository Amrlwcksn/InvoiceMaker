@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Manajemen Customer')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-zinc-200/90 shadow-xs">
        
        <!-- Search Input -->
        <form action="{{ route('customers.index') }}" method="GET" class="flex-1 max-w-md">
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama, email, atau telepon customer..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:bg-white transition-all">
                <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </form>

        <button @click="showModal = true" 
                class="inline-flex items-center justify-center space-x-2 bg-zinc-950 hover:bg-black text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-xs transition-all border border-zinc-900">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ Tambah Customer</span>
        </button>
    </div>

    <!-- Customer Table Section -->
    <div class="bg-white rounded-3xl border border-zinc-200/90 shadow-xs overflow-hidden">
        @if($customers->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 bg-zinc-100 text-zinc-700 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-zinc-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h4 class="text-base font-extrabold text-zinc-950">Belum ada customer</h4>
                <p class="text-xs text-zinc-500 mt-1 max-w-sm mx-auto">Tambahkan kontak customer Anda untuk mempermudah pembuatan invoice berikutnya.</p>
                <div class="mt-5">
                    <button @click="showModal = true" class="inline-flex items-center space-x-2 bg-zinc-950 hover:bg-black text-white font-bold px-5 py-2.5 rounded-xl text-xs shadow-xs transition-all">
                        <span>+ Tambah Customer</span>
                    </button>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50/80 border-b border-zinc-100 text-[10px] font-extrabold uppercase tracking-wider text-zinc-500">
                            <th class="py-3.5 px-6">Nama Customer</th>
                            <th class="py-3.5 px-6">Email</th>
                            <th class="py-3.5 px-6">Telepon</th>
                            <th class="py-3.5 px-6">Alamat</th>
                            <th class="py-3.5 px-6">Total Invoices</th>
                            <th class="py-3.5 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-zinc-50/60 transition-colors">
                                <td class="py-4 px-6 font-extrabold text-zinc-950">
                                    <a href="{{ route('customers.show', $customer) }}" class="hover:underline">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="py-4 px-6 text-zinc-600 font-medium">
                                    {{ $customer->email ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-zinc-600 font-mono text-xs">
                                    {{ $customer->phone ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-zinc-500 text-xs max-w-xs truncate">
                                    {{ $customer->address ?: '-' }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-800 border border-zinc-200">
                                        {{ $customer->invoices_count }} invoice
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-1.5">
                                    <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold rounded-lg text-[11px] transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-2.5 py-1.5 bg-zinc-900 hover:bg-black text-white font-bold rounded-lg text-[11px] transition-colors">
                                        Edit
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus customer {{ $customer->name }}?');">
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
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Customer -->
    <div x-show="showModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/70 p-4">
        
        <div @click.away="showModal = false" class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 transform transition-all">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                <h3 class="text-base font-extrabold text-zinc-950">Tambah Customer Baru</h3>
                <button @click="showModal = false" class="text-zinc-400 hover:text-zinc-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('customers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Nama Customer <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: PT Example Indonesia" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Email</label>
                        <input type="email" name="email" placeholder="email@domain.com" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" placeholder="+62 812 3456 7890" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3" placeholder="Alamat lengkap customer..." class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-zinc-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold rounded-xl text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-zinc-950 hover:bg-black text-white font-bold rounded-xl text-xs shadow-xs border border-zinc-900">Simpan Customer</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
