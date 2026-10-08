<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'name' => 'PT Example Indonesia',
            'email' => 'finance@example.co.id',
            'phone' => '+62 21 555 0192',
            'address' => 'Gedung Menara Mandiri 2, Jl. Jend. Gatot Subroto Kav. 54, Jakarta',
        ]);

        Customer::create([
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'phone' => '+62 819 8765 4321',
            'address' => 'Jl. Kebon Jeruk Indah No. 12, Jakarta Barat',
        ]);

        Customer::create([
            'name' => 'ABC Creative Studio',
            'email' => 'hello@abccreative.com',
            'phone' => '+62 22 420 1199',
            'address' => 'Jl. Riau No. 88, Bandung, Jawa Barat',
        ]);
    }
}
