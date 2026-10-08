<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            'business_name' => 'Antigravity Digital Studio',
            'email' => 'billing@antigravity.id',
            'phone' => '+62 812 3456 7890',
            'address' => "Jl. Jendral Sudirman No. 45, Lantai 12\nJakarta Selatan, DKI Jakarta 12190",
            'bank_info' => "Bank BCA: 8830123456 a.n. Antigravity Digital Studio\nBank Mandiri: 1370009876543 a.n. Antigravity Digital Studio",
            'default_notes' => 'Pembayaran dilakukan maksimal 14 hari kerja setelah invoice diterima. Terima kasih atas kerja samanya!',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
