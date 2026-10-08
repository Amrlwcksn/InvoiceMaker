<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceMakerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_can_be_accessed(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Ringkasan');
    }

    public function test_customer_crud_operations(): void
    {
        // Create Customer
        $response = $this->post(route('customers.store'), [
            'name' => 'PT Test Indonesia',
            'email' => 'test@example.com',
            'phone' => '08123456789',
            'address' => 'Jakarta Selatan',
        ]);
        $response->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['name' => 'PT Test Indonesia']);

        $customer = Customer::where('name', 'PT Test Indonesia')->first();

        // Update Customer
        $updateResponse = $this->put(route('customers.update', $customer), [
            'name' => 'PT Test Indonesia Updated',
            'email' => 'updated@example.com',
            'phone' => '08123456789',
            'address' => 'Jakarta Selatan',
        ]);
        $updateResponse->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['name' => 'PT Test Indonesia Updated']);

        // Delete Customer
        $deleteResponse = $this->delete(route('customers.destroy', $customer));
        $deleteResponse->assertRedirect(route('customers.index'));
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    public function test_invoice_creation_recalculates_totals_on_backend(): void
    {
        $customer = Customer::create([
            'name' => 'John Client',
            'email' => 'client@test.com',
        ]);

        $postData = [
            'customer_id' => $customer->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'unpaid',
            'discount' => 100000,
            'tax' => 50000,
            'notes' => 'Test Notes',
            'items' => [
                [
                    'item_name' => 'Jasa Desain Website',
                    'description' => 'Desain landing page',
                    'quantity' => 2,
                    'unit_price' => 1000000, // Sub = 2M
                    'discount' => 200000,   // Item Total = 1.8M
                ]
            ]
        ];

        $response = $this->post(route('invoices.store'), $postData);
        
        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $response->assertRedirect(route('invoices.show', $invoice));

        // Subtotal = 1.8M
        // Total = 1.8M - 100k + 50k = 1,750,000
        $this->assertEquals(1800000, $invoice->subtotal);
        $this->assertEquals(1750000, $invoice->total);
        $this->assertEquals(1, $invoice->items->count());
        $this->assertEquals('INV-2026-001', $invoice->invoice_number);
    }

    public function test_pdf_download_generates_stream(): void
    {
        $customer = Customer::create(['name' => 'PDF Customer']);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-999',
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'subtotal' => 500000,
            'total' => 500000,
            'status' => 'paid',
        ]);
        $invoice->items()->create([
            'item_name' => 'Sample Item',
            'quantity' => 1,
            'unit_price' => 500000,
            'total' => 500000,
        ]);

        $response = $this->get(route('invoices.pdf', $invoice));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_settings_update(): void
    {
        $response = $this->post(route('settings.update'), [
            'business_name' => 'Antigravity Corporate',
            'email' => 'corp@antigravity.id',
            'phone' => '0811111111',
            'address' => 'Jakarta',
        ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertEquals('Antigravity Corporate', Setting::get('business_name'));
    }

    public function test_vendor_stamp_can_be_uploaded_and_displayed(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->image('vendor_stamp.png', 200, 200);

        $response = $this->post(route('settings.update'), [
            'business_name' => 'Antigravity Vendor',
            'stamp' => $file,
        ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertNotNull(Setting::get('stamp'));
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists(Setting::get('stamp'));

        $customer = Customer::create(['name' => 'Stamp Customer']);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-STAMP',
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'paid',
        ]);

        $showResponse = $this->get(route('invoices.show', $invoice));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Stempel Vendor');
    }

    public function test_invoice_displays_correct_status_watermark(): void
    {
        $customer = Customer::create(['name' => 'Watermark Customer']);

        $paidInvoice = Invoice::create([
            'invoice_number' => 'INV-2026-PAID',
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'paid',
        ]);

        $unpaidInvoice = Invoice::create([
            'invoice_number' => 'INV-2026-UNPAID',
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'unpaid',
        ]);

        $draftInvoice = Invoice::create([
            'invoice_number' => 'INV-2026-DRAFT',
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'draft',
        ]);

        $overdueInvoice = Invoice::create([
            'invoice_number' => 'INV-2026-OVERDUE',
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'due_date' => now()->subDays(2),
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'overdue',
        ]);

        // Paid -> Lunas.png
        $resPaid = $this->get(route('invoices.show', $paidInvoice));
        $resPaid->assertStatus(200);
        $resPaid->assertSee('Lunas.png');

        // Unpaid -> BelumLunas.png
        $resUnpaid = $this->get(route('invoices.show', $unpaidInvoice));
        $resUnpaid->assertStatus(200);
        $resUnpaid->assertSee('BelumLunas.png');

        // Overdue -> JatuhTempo.png
        $resOverdue = $this->get(route('invoices.show', $overdueInvoice));
        $resOverdue->assertStatus(200);
        $resOverdue->assertSee('JatuhTempo.png');
    }

    public function test_invoices_can_be_filtered_by_month_and_exported_to_csv(): void
    {
        $customer = Customer::create(['name' => 'Monthly Customer']);

        $octoberInvoice = Invoice::create([
            'invoice_number' => 'INV-2026-OCT',
            'customer_id' => $customer->id,
            'invoice_date' => '2026-10-05',
            'due_date' => '2026-10-12',
            'subtotal' => 300000,
            'total' => 300000,
            'status' => 'paid',
        ]);
        $octoberInvoice->items()->create([
            'item_name' => 'Sewa Kamera',
            'description' => 'Sewa Kamera Sony Alpha',
            'quantity' => 1,
            'unit_price' => 300000,
            'total' => 300000,
        ]);

        $septemberInvoice = Invoice::create([
            'invoice_number' => 'INV-2026-SEP',
            'customer_id' => $customer->id,
            'invoice_date' => '2026-09-15',
            'due_date' => '2026-09-22',
            'subtotal' => 150000,
            'total' => 150000,
            'status' => 'paid',
        ]);

        // Filter October
        $indexResponse = $this->get(route('invoices.index', ['month_year' => '2026-10']));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('INV-2026-OCT');
        $indexResponse->assertDontSee('INV-2026-SEP');

        // Export CSV
        $exportResponse = $this->get(route('invoices.export', ['month_year' => '2026-10']));
        $exportResponse->assertStatus(200);
        $exportResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_reset_invoices_requires_confirmation_text(): void
    {
        $customer = Customer::create(['name' => 'Reset Test Customer']);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-2026-PURGE',
            'customer_id' => $customer->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 100000,
            'total' => 100000,
            'status' => 'draft',
        ]);

        // Invalid confirmation
        $failResponse = $this->post(route('settings.reset-invoices'), [
            'scope' => 'all',
            'confirm_text' => 'WRONG',
        ]);
        $failResponse->assertSessionHasErrors(['confirm_text']);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);

        // Valid confirmation
        $successResponse = $this->post(route('settings.reset-invoices'), [
            'scope' => 'all',
            'confirm_text' => 'HAPUS DATA',
        ]);
        $successResponse->assertRedirect(route('settings.index'));
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }
}
