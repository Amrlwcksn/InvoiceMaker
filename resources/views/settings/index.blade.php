@extends('layouts.app')

@section('title', 'Settings - Informasi Bisnis')
@section('page_title', 'Informasi Bisnis & Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-xs space-y-6">
        <div class="border-b border-zinc-100 pb-4">
            <h3 class="text-base font-extrabold text-zinc-950">Profil Bisnis & Konfigurasi Invoice</h3>
            <p class="text-xs text-zinc-500 mt-0.5">Informasi ini akan muncul secara otomatis pada header & footer seluruh invoice yang Anda buat.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Logo Section -->
            <div class="p-5 bg-zinc-50 rounded-2xl border border-zinc-200 flex flex-col sm:flex-row items-center gap-6">
                <div class="w-32 h-32 rounded-2xl bg-white border border-zinc-300 flex items-center justify-center overflow-hidden shadow-xs shrink-0 relative">
                    @if(!empty($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo']))
                        <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo Bisnis" class="w-full h-full object-contain p-2">
                    @else
                        <div class="text-center p-2 text-zinc-400">
                            <span class="block text-xl font-black text-zinc-950">INV</span>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-zinc-500">No Logo</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <label class="block text-xs font-bold text-zinc-950 mb-1">Logo Bisnis</label>
                    <p class="text-xs text-zinc-500 mb-3">Format gambar PNG, JPG, SVG. Maksimal ukuran 2MB.</p>
                    <input type="file" name="logo" accept="image/*" class="text-xs text-zinc-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-zinc-950 file:text-white hover:file:bg-black cursor-pointer">
                </div>
            </div>

            <!-- Business Contact Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Nama Bisnis / Usaha <span class="text-rose-500">*</span></label>
                    <input type="text" name="business_name" value="{{ old('business_name', $settings['business_name'] ?? '') }}" required placeholder="Contoh: Studio Digital" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-bold focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Email Resmi Bisnis</label>
                    <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" placeholder="billing@bisnis.com" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" placeholder="+62 812 3456 7890" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Alamat Kantor / Studio</label>
                    <textarea name="address" rows="3" placeholder="Jl. Sudirman No. 45, Jakarta..." class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">{{ old('address', $settings['address'] ?? '') }}</textarea>
                </div>
            </div>

            <!-- Additional Defaults -->
            <div class="space-y-5 pt-4 border-t border-zinc-100">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Informasi Pembayaran / Rekening Bank</label>
                    <textarea name="bank_info" rows="3" placeholder="Contoh: Bank BCA: 8830123456 a.n. Nama Pemilik..." class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">{{ old('bank_info', $settings['bank_info'] ?? '') }}</textarea>
                    <p class="text-[10px] text-zinc-400 mt-1">Informasi ini akan ditampilkan pada bagian bawah invoice untuk memudahkan pembayaran dari customer.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1">Default Catatan / Syarat & Ketentuan (Notes)</label>
                    <textarea name="default_notes" rows="3" placeholder="Pembayaran dilakukan maksimal 14 hari kerja..." class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-zinc-950 focus:outline-none">{{ old('default_notes', $settings['default_notes'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-zinc-100">
                <button type="submit" class="px-6 py-3 bg-zinc-950 hover:bg-black text-white font-bold rounded-xl text-xs shadow-xs border border-zinc-900 transition-all">
                    Simpan Perubahan Settings
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
