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
}
