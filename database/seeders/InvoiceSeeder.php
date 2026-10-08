<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\InvoiceCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $ptExample = Customer::where('name', 'PT Example Indonesia')->first();
        $johnDoe = Customer::where('name', 'John Doe')->first();
        $abcCreative = Customer::where('name', 'ABC Creative Studio')->first();

        // Invoice 1: PT Example Indonesia (Paid)
        $items1 = [
            [
                'item_name' => 'Pengembangan Website Company Profile',
                'description' => 'Desain UI/UX modern, responsive Blade view, integrasi Laravel CMS',
                'quantity' => 1,
                'unit_price' => 7500000,
                'discount' => 500000,
            ],
            [
                'item_name' => 'Maintenance & Server Setup 1 Tahun',
                'description' => 'Setup VPS Ubuntu, SSL Certificate, Automated Daily Backup',
                'quantity' => 1,
                'unit_price' => 2000000,
                'discount' => 0,
            ],
        ];
        $calc1 = InvoiceCalculator::calculate($items1, 0, 900000); // tax 900k
        $inv1 = Invoice::create([
            'invoice_number' => 'INV-2026-001',
            'customer_id' => $ptExample->id,
            'invoice_date' => Carbon::now()->subDays(15),
            'due_date' => Carbon::now()->subDays(1),
            'subtotal' => $calc1['subtotal'],
            'discount' => $calc1['discount'],
            'tax' => $calc1['tax'],
            'total' => $calc1['total'],
            'status' => 'paid',
            'notes' => 'Pembayaran lunas via Transfer Bank BCA pada tanggal ' . Carbon::now()->subDays(2)->format('d/m/Y'),
        ]);
        foreach ($calc1['items'] as $item) {
            $inv1->items()->create($item);
        }

        // Invoice 2: John Doe (Unpaid)
        $items2 = [
            [
                'item_name' => 'Dokumentasi Event Video & Foto',
                'description' => 'Liputan full day event, 2 kameramen, lighting gear',
                'quantity' => 1,
                'unit_price' => 1500000,
                'discount' => 0,
            ],
            [
                'item_name' => 'Video Editing Teaser Instagram & Youtube',
                'description' => 'Durasi 60 detik + Full HD 1080p Export',
                'quantity' => 2,
                'unit_price' => 500000,
                'discount' => 100000,
            ],
        ];
        $calc2 = InvoiceCalculator::calculate($items2, 100000, 0); // invoice discount 100k
        $inv2 = Invoice::create([
            'invoice_number' => 'INV-2026-002',
            'customer_id' => $johnDoe->id,
            'invoice_date' => Carbon::now()->subDays(5),
            'due_date' => Carbon::now()->addDays(9),
            'subtotal' => $calc2['subtotal'],
            'discount' => $calc2['discount'],
            'tax' => $calc2['tax'],
            'total' => $calc2['total'],
            'status' => 'unpaid',
            'notes' => 'Harap sertakan nomor invoice pada berita transfer saat melakukan pembayaran.',
        ]);
        foreach ($calc2['items'] as $item) {
            $inv2->items()->create($item);
        }

        // Invoice 3: ABC Creative Studio (Draft)
        $items3 = [
            [
                'item_name' => 'Desain Branding & Guideline Logo',
                'description' => 'Logo vector, color palette, typography specification',
                'quantity' => 1,
                'unit_price' => 3500000,
                'discount' => 0,
            ],
        ];
        $calc3 = InvoiceCalculator::calculate($items3, 0, 385000);
        $inv3 = Invoice::create([
            'invoice_number' => 'INV-2026-003',
            'customer_id' => $abcCreative->id,
            'invoice_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(14),
            'subtotal' => $calc3['subtotal'],
            'discount' => $calc3['discount'],
            'tax' => $calc3['tax'],
            'total' => $calc3['total'],
            'status' => 'draft',
            'notes' => 'Draft invoice untuk peninjauan awal tim ABC Creative Studio.',
        ]);
        foreach ($calc3['items'] as $item) {
            $inv3->items()->create($item);
        }
    }
}
