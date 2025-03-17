<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kategori_id' => 1,
                'kategori_kode' => 'HP',
                'kategori_nama' => 'HandPhone',
            ],
            [
                'kategori_id' => 2,
                'kategori_kode' => 'LP',
                'kategori_nama' => 'Laptop',
            ],
            [
                'kategori_id' => 3,
                'kategori_kode' => 'MS',
                'kategori_nama' => 'Mouse',
            ],
            [
                'kategori_id' => 4,
                'kategori_kode' => 'KB',
                'kategori_nama' => 'Keyboard',
            ],
            [
                'kategori_id' => 5,
                'kategori_kode' => 'PRT',
                'kategori_nama' => 'Printer',
            ],
        ];
        DB::table('m_kategori')->insert($data);
    }
}
