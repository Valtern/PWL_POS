<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('m_kategori')->insert([
            ['kategori_id' => 1, 'kode' => 'KAT001', 'nama' => 'Elektronik'],
            ['kategori_id' => 2, 'kode' => 'KAT001', 'nama' => 'Pakaian'],
            ['kategori_id' => 3, 'kode' => 'KAT001', 'nama' => 'Makanan'],
            ['kategori_id' => 4, 'kode' => 'KAT001', 'nama' => 'Minuman'],
            ['kategori_id' => 5, 'kode' => 'KAT001', 'nama' =>'Aksesoris']
        ]);
    }
}
