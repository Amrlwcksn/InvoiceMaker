<!-- @extends('layouts.app') -->

<!-- @section('title', 'Settings - Informasi Bisnis & Stempel')
@section('page_title', 'Settings') -->

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    <!-- Top Header Banner -->
    <div class="bg-gradient-to-r from-zinc-900 via-zinc-950 to-black rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 bg-zinc-800/80 border border-zinc-700/60 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Pengaturan Vendor & Branding</span>
                </div>
                <h2 class="text-xl md:text-2xl font-black tracking-tight text-white">Profil & Media Invoice</h2>
                <p class="text-zinc-400 text-xs mt-1 leading-relaxed max-w-xl">Atur identitas bisnis, logo, dan stempel toko Anda. Perubahan akan langsung diterapkan secara otomatis pada seluruh tampilan dan dokumen cetak/PDF invoice.</p>
            </div>
        </div>
    </div>

    <!-- System & Information Overview Card -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-2xl bg-zinc-950 text-white flex items-center justify-center font-black text-xs shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-zinc-950">Ringkasan Informasi Usaha Saat Ini</h3>
                    <p class="text-xs text-zinc-500">Status kelengkapan identitas usaha yang digunakan pada invoice.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-2">
            <!-- Business Name -->
            <div class="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 block">Nama Usaha</span>
                <span class="text-xs font-black text-zinc-950 truncate block mt-0.5">
                    {{ !empty($settings['business_name']) ? $settings['business_name'] : 'Belum diisi' }}
                </span>
            </div>

            <!-- Contact Email / Phone -->
            <div class="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/80">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 block">Kontak Utama</span>
                <span class="text-xs font-bold text-zinc-800 truncate block mt-0.5">
                    {{ !empty($settings['email']) ? $settings['email'] : (!empty($settings['phone']) ? $settings['phone'] : 'Belum diisi') }}
                </span>
            </div>

            <!-- Logo Status -->
            <div class="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/80 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 block">Logo Perusahaan</span>
                    <span class="text-xs font-bold text-zinc-900 mt-0.5 block">
                        {{ (!empty($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo'])) ? 'Terpasang' : 'Belum diunggah' }}
                    </span>
                </div>
                @if(!empty($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo']))
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0" title="Logo Aktif"></span>
                @else
                    <span class="w-3 h-3 rounded-full bg-zinc-300 shrink-0" title="Logo Belum Diunggah"></span>
                @endif
            </div>

            <!-- Stamp Status -->
            <div class="p-3.5 rounded-2xl bg-zinc-50 border border-zinc-200/80 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 block">Stempel Vendor</span>
                    <span class="text-xs font-bold text-zinc-900 mt-0.5 block">
                        {{ (!empty($settings['stamp']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['stamp'])) ? 'Terpasang' : 'Belum diunggah' }}
                    </span>
                </div>
                @if(!empty($settings['stamp']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['stamp']))
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0" title="Stempel Aktif"></span>
                @else
                    <span class="w-3 h-3 rounded-full bg-zinc-300 shrink-0" title="Stempel Belum Diunggah"></span>
                @endif
            </div>
        </div>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. Media & Branding Section -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-zinc-950">Aset Media & Identitas Visual</h3>
                        <p class="text-xs text-zinc-500">Unggah Logo Utama dan Stempel Resmi toko/vendor Anda.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Logo Upload Box -->
                <div class="p-5 rounded-2xl border-2 border-dashed border-zinc-200 hover:border-zinc-400 bg-zinc-50/70 transition-all space-y-4 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-800 truncate">1. Logo Bisnis</span>
                        @if(!empty($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo']))
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-200 text-zinc-600 shrink-0">Belum diunggah</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 my-1">
                        <div class="w-20 h-20 rounded-2xl bg-white border border-zinc-200 flex items-center justify-center overflow-hidden shadow-xs shrink-0 relative">
                            @if(!empty($settings['logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['logo']))
                                <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Logo Bisnis" class="w-full h-full object-contain p-2">
                            @else
                                <div class="text-center p-2 text-zinc-400">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-[8px] font-bold uppercase text-zinc-400">No Logo</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <p class="text-[11px] font-semibold text-zinc-700 leading-snug">Tampil pada header invoice.</p>
                            <p class="text-[10px] text-zinc-400">Maks 2MB.</p>
                        </div>
                    </div>

                    <div>
                        <input type="file" name="logo" accept="image/*" class="w-full text-xs text-zinc-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-zinc-950 file:text-white hover:file:bg-black cursor-pointer border border-zinc-200 rounded-xl bg-white p-1">
                    </div>
                </div>

                <!-- Vendor Stamp Upload Box -->
                <div class="p-5 rounded-2xl border-2 border-dashed border-zinc-200 hover:border-zinc-400 bg-zinc-50/70 transition-all space-y-4 flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-800 truncate">2. Stempel Vendor</span>
                        @if(!empty($settings['stamp']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['stamp']))
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shrink-0">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-zinc-200 text-zinc-600 shrink-0">Belum diunggah</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 my-1">
                        <div class="w-20 h-20 rounded-2xl bg-white border border-zinc-200 flex items-center justify-center overflow-hidden shadow-xs shrink-0 relative">
                            @if(!empty($settings['stamp']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['stamp']))
                                <img src="{{ asset('storage/' . $settings['stamp']) }}" alt="Stempel Vendor" class="w-full h-full object-contain p-2">
                            @else
                                <div class="text-center p-2 text-zinc-400">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    <span class="text-[8px] font-bold uppercase text-zinc-400">No Stamp</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <p class="text-[11px] font-semibold text-zinc-700 leading-snug">Tampil di kanan bawah invoice.</p>
                            <p class="text-[10px] text-zinc-400">Maks 2MB.</p>
                        </div>
                    </div>

                    <div>
                        <input type="file" name="stamp" accept="image/*" class="w-full text-xs text-zinc-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-zinc-950 file:text-white hover:file:bg-black cursor-pointer border border-zinc-200 rounded-xl bg-white p-1">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Business Information Section -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-sm space-y-6">
            <div class="flex items-center space-x-3 border-b border-zinc-100 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-zinc-950">Profil & Kontak Usaha</h3>
                    <p class="text-xs text-zinc-500">Informasi utama identitas penyedia jasa/penjual.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1.5">Nama Bisnis / Usaha <span class="text-rose-500">*</span></label>
                    <input type="text" name="business_name" value="{{ old('business_name', $settings['business_name'] ?? '') }}" required placeholder="Contoh: Studio Digital Creatives" class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl text-xs font-bold text-zinc-950 focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1.5">Email Resmi Bisnis</label>
                    <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}" placeholder="billing@bisnis.com" class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl text-xs font-medium text-zinc-950 focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1.5">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}" placeholder="+62 812 3456 7890" class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl text-xs font-medium text-zinc-950 focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1.5">Alamat Kantor / Studio</label>
                    <textarea name="address" rows="3" placeholder="Jl. Sudirman No. 45, Jakarta..." class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl text-xs font-medium text-zinc-950 focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 focus:outline-none transition-all">{{ old('address', $settings['address'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. Payment & Default Terms Section -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-zinc-200/90 shadow-sm space-y-6">
            <div class="flex items-center space-x-3 border-b border-zinc-100 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-zinc-100 border border-zinc-200 flex items-center justify-center text-zinc-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-zinc-950">Rekening Bank & Catatan Default</h3>
                    <p class="text-xs text-zinc-500">Ketentuan pembayaran dan catatan otomatis pada setiap invoice baru.</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1.5">Informasi Pembayaran / Rekening Bank</label>
                    <textarea name="bank_info" rows="3" placeholder="Contoh: Bank BCA: 8830123456 a.n. Nama Pemilik..." class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl text-xs font-medium text-zinc-950 focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 focus:outline-none transition-all">{{ old('bank_info', $settings['bank_info'] ?? '') }}</textarea>
                    <p class="text-[10px] text-zinc-400 mt-1">Muncul pada kotak pembayaran di bagian bawah invoice.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-zinc-700 mb-1.5">Default Catatan / Syarat & Ketentuan (Notes)</label>
                    <textarea name="default_notes" rows="3" placeholder="Pembayaran dilakukan maksimal 14 hari kerja..." class="w-full px-4 py-2.5 border border-zinc-300 rounded-xl text-xs font-medium text-zinc-950 focus:ring-2 focus:ring-zinc-950 focus:border-zinc-950 focus:outline-none transition-all">{{ old('default_notes', $settings['default_notes'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Sticky Submit Bar -->
        <div class="bg-white p-4 md:p-5 rounded-2xl border border-zinc-200/90 shadow-md flex items-center justify-between gap-4">
            <span class="text-xs text-zinc-500 font-medium hidden sm:inline">Pastikan data yang dimasukkan sudah benar sebelum menyimpan.</span>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-zinc-950 hover:bg-black text-white font-extrabold rounded-xl text-xs shadow-md border border-zinc-900 transition-all flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Simpan Perubahan Settings</span>
            </button>
        </div>
    </form>

    <!-- Danger Zone / Reset Data Section -->
    <div id="danger-zone" class="bg-rose-50 rounded-3xl p-6 md:p-8 border-2 border-rose-200 shadow-md space-y-6">
        <div class="flex items-center space-x-3 border-b border-rose-200 pb-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center font-black shrink-0 shadow-sm border border-rose-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <div class="inline-flex items-center space-x-1.5 bg-rose-200/80 text-rose-950 px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase tracking-widest mb-1 border border-rose-300">
                    <span>⚠️ Fitur Pembersihan Data</span>
                </div>
                <h3 class="text-base font-black text-rose-950">Danger Zone: Reset Data Transaksi</h3>
                <p class="text-xs text-rose-800 font-medium">Kosongkan data invoice bulanan jika Anda ingin menghapus data percobaan atau memperbarui sistem untuk bulan baru.</p>
            </div>
        </div>

        <form action="{{ route('settings.reset-invoices') }}" method="POST" onsubmit="return confirmReset(event, this);" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-rose-950 mb-1.5">Pilih Cakupan Reset Data</label>
                    <select name="scope" id="reset_scope" class="w-full px-4 py-3 border-2 border-rose-300 rounded-2xl text-xs font-bold text-zinc-950 bg-white focus:ring-2 focus:ring-rose-600 focus:outline-none shadow-xs">
                        <option value="all">Hapus Seluruh Data Invoice (Kosongkan Sistem)</option>
                        @php
                            $availableMonths = \App\Models\Invoice::select('invoice_date')
                                ->orderBy('invoice_date', 'desc')
                                ->get()
                                ->filter(fn ($inv) => !empty($inv->invoice_date))
                                ->map(fn ($inv) => [
                                    'key' => $inv->invoice_date->format('Y-m'),
                                    'label' => $inv->invoice_date->format('F Y'),
                                ])
                                ->unique('key')
                                ->values();
                        @endphp
                        @foreach($availableMonths as $m)
                            <option value="{{ $m['key'] }}">Hapus Data Periode {{ $m['label'] }} ({{ $m['key'] }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black uppercase tracking-wider text-rose-950 mb-1.5">Ketik "HAPUS DATA" Untuk Konfirmasi</label>
                    <input type="text" name="confirm_text" id="reset_confirm_text" placeholder="Ketik HAPUS DATA" required class="w-full px-4 py-3 border-2 border-rose-300 rounded-2xl text-xs font-black text-rose-950 bg-white placeholder-rose-300 focus:ring-2 focus:ring-rose-600 focus:outline-none shadow-xs">
                </div>
            </div>

            <div class="flex items-center justify-between gap-4 pt-2 border-t border-rose-200">
                <span class="text-[11px] font-semibold text-rose-700 hidden sm:inline">Perhatian: Tindakan ini permanen dan tidak dapat dibatalkan.</span>
                <button type="submit" 
                        style="background-color: #dc2626; color: #ffffff !important; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 28px; border-radius: 16px; font-size: 13px; font-weight: 900; border: 2px solid #b91c1c; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4); cursor: pointer;"
                        class="w-full sm:w-auto px-8 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-2xl text-xs shadow-md border-2 border-rose-500 transition-all flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4 text-white" style="width: 18px; height: 18px; stroke: #ffffff; stroke-width: 2.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span style="color: #ffffff !important; font-weight: 900; letter-spacing: 0.05em; text-transform: uppercase;">EKSEKUSI RESET DATA INVOICE</span>
                </button>
            </div>
        </form>
    </div>

    <script>
    function confirmReset(e, form) {
        var input = document.getElementById('reset_confirm_text').value;
        if (input !== 'HAPUS DATA') {
            e.preventDefault();
            alert('Konfirmasi tidak sesuai! Harap ketik "HAPUS DATA" untuk melanjutkan reset.');
            return false;
        }
        return confirm('Apakah Anda benar-benar yakin ingin menghapus data invoice ini? Tindakan ini tidak dapat dibatalkan.');
    }
    </script>

</div>
@endsection
