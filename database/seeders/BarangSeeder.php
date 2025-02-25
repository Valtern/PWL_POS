<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'barang_id' => 1,
                'kategori_id' => 1,
                'barang_kode' => 'HP1',
                'barang_nama' => 'HP Samsung',
                'harga_beli' => 22000000,
                'harga_jual' => 24000000,
            ],
            [
                'barang_id' => 2,
                'kategori_id' => 2,
                'barang_kode' => 'LP1',
                'barang_nama' => 'Laptop MSI',
                'harga_beli' => 23000000,
                'harga_jual' => 25000000,
            ],
            [
                'barang_id' => 3,
                'kategori_id' => 3,
                'barang_kode' => 'MS1',
                'barang_nama' => 'Mouse Razer',
                'harga_beli' => 400000,
                'harga_jual' => 500000,
            ],
            [
                'barang_id' => 4,
                'kategori_id' => 4,
                'barang_kode' => 'KB1',
                'barang_nama' => 'Keyboard Vortex',
                'harga_beli' => 600000,
                'harga_jual' => 700000,
            ],
            [
                'barang_id' => 5,
                'kategori_id' => 5,
                'barang_kode' => 'PRT1',
                'barang_nama' => 'Printer Cannon',
                'harga_beli' => 1400000,
                'harga_jual' => 1500000,
            ],
            [
                'barang_id' => 6,
                'kategori_id' => 1,
                'barang_kode' => 'HP2',
                'barang_nama' => 'Iphone 16',
                'harga_beli' => 23000000,
                'harga_jual' => 25000000,
            ],
            [
                'barang_id' => 7,
                'kategori_id' => 2,
                'barang_kode' => 'LP2',
                'barang_nama' => 'Laptop Dell',
                'harga_beli' => 12000000,
                'harga_jual' => 14000000,
            ],
            [
                'barang_id' => 8,
                'kategori_id' => 3,
                'barang_kode' => 'MS2',
                'barang_nama' => 'Mouse Logitech',
                'harga_beli' => 300000,
                'harga_jual' => 400000,
            ],
            [
                'barang_id' => 9,
                'kategori_id' => 4,
                'barang_kode' => 'KB2',
                'barang_nama' => 'Keyboard Razer',
                'harga_beli' => 500000,
                'harga_jual' => 600000,
            ],
            [
                'barang_id' => 10,
                'kategori_id' => 5,
                'barang_kode' => 'PRT2',
                'barang_nama' => 'Printer HP',
                'harga_beli' => 2000000,
                'harga_jual' => 1200000,
            ],
        ];
        DB::table('m_barang')->insert($data);
    }
}
