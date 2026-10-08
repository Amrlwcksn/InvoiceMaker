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
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ], [
            'business_name.required' => 'Nama bisnis wajib diisi.',
            'logo.image' => 'Logo harus berupa berkas gambar.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
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

        return redirect()->route('settings.index')
            ->with('success', 'Informasi bisnis berhasil diperbarui.');
    }
}
