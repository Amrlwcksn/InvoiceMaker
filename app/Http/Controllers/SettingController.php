<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::getAll();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'bank_info' => ['nullable', 'string'],
            'default_notes' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            'stamp' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
        ], [
            'business_name.required' => 'Nama bisnis wajib diisi.',
            'logo.image' => 'Logo harus berupa berkas gambar.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
            'stamp.image' => 'Stempel harus berupa berkas gambar.',
            'stamp.max' => 'Ukuran stempel maksimal 2MB.',
        ]);

        $fields = ['business_name', 'email', 'phone', 'address', 'bank_info', 'default_notes'];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field));
        }

        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            Setting::set('logo', $path);
        }

        if ($request->hasFile('stamp')) {
            $oldStamp = Setting::get('stamp');
            if ($oldStamp && Storage::disk('public')->exists($oldStamp)) {
                Storage::disk('public')->delete($oldStamp);
            }

            $path = $request->file('stamp')->store('stamps', 'public');
            Setting::set('stamp', $path);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Informasi bisnis berhasil diperbarui.');
    }

    public function resetInvoices(Request $request): RedirectResponse
    {
        $request->validate([
            'confirm_text' => ['required', 'string', 'in:HAPUS DATA'],
            'scope' => ['required', 'string'],
        ], [
            'confirm_text.in' => 'Konfirmasi kata sandi harus sesuai (ketik "HAPUS DATA").',
            'confirm_text.required' => 'Ketik "HAPUS DATA" untuk mengonfirmasi.',
        ]);

        $scope = $request->input('scope');

        if ($scope === 'all') {
            \App\Models\InvoiceItem::query()->delete();
            \App\Models\Invoice::query()->delete();
            $message = 'Seluruh data transaksi invoice berhasil direset.';
        } else {
            $parts = explode('-', $scope);
            if (count($parts) === 2) {
                $invoiceIds = \App\Models\Invoice::whereYear('invoice_date', $parts[0])
                    ->whereMonth('invoice_date', $parts[1])
                    ->pluck('id');

                \App\Models\InvoiceItem::whereIn('invoice_id', $invoiceIds)->delete();
                \App\Models\Invoice::whereIn('id', $invoiceIds)->delete();

                $message = "Data transaksi periode {$scope} berhasil direset.";
            } else {
                return redirect()->route('settings.index')
                    ->with('error', 'Format periode tidak valid.');
            }
        }

        return redirect()->route('settings.index')
            ->with('success', $message);
    }
}
