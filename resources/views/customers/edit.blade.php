@extends('layouts.app')

@section('title', 'Edit Customer - ' . $customer->name)
@section('page_title', 'Edit Customer')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <a href="{{ route('customers.index') }}" class="inline-flex items-center space-x-2 text-xs font-bold text-zinc-600 hover:text-zinc-950 transition-colors">
            <span>← Kembali ke Daftar Customer</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-xs space-y-6">
        <div class="border-b border-zinc-100 pb-4">
            <h3 class="text-base font-extrabold text-zinc-950">Edit Data Customer</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Perbarui informasi kontak dan alamat customer.</p>
        </div>

        <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Nama Customer <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="3" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-zinc-100">
                <a href="{{ route('customers.index') }}" class="px-5 py-2.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-900 font-bold rounded-xl text-xs">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-zinc-950 hover:bg-black text-white font-bold rounded-xl text-xs shadow-xs border border-zinc-900">Simpan Perubahan</button>
            </div>
        </form>
    </div>

</div>
@endsection
