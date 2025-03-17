<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'penjualan_id' => 1,
                'user_id' => 1,
                'pembeli' => 'Ridho',
                'penjualan_kode' => 'code-01',
                'penjualan_tanggal' => '2022-09-09 14:30:00'
            ],
            [
                'penjualan_id' => 2,
                'user_id' => 2,
                'pembeli' => 'Budi',
                'penjualan_kode' => 'code-02',
                'penjualan_tanggal' => '2022-09-11 12:30:00'
            ],
            [
                'penjualan_id' => 3,
                'user_id' => 3,
                'pembeli' => 'Jono',
                'penjualan_kode' => 'code-03',
                'penjualan_tanggal' => '2022-10-09 11:30:00'
            ],
            [
                'penjualan_id' => 4,
                'user_id' => 1,
                'pembeli' => 'Jane',
                'penjualan_kode' => 'code-04',
                'penjualan_tanggal' => '2022-09-12 11:10:00'
            ],
            [
                'penjualan_id' => 5,
                'user_id' => 2,
                'pembeli' => 'John',
                'penjualan_kode' => 'code-05',
                'penjualan_tanggal' => '2022-03-12 15:10:00'
            ],
            [
                'penjualan_id' => 6,
                'user_id' => 3,
                'pembeli' => 'Denis',
                'penjualan_kode' => 'code-06',
                'penjualan_tanggal' => '2022-09-12 16:10:00'
            ],
            [
                'penjualan_id' => 7,
                'user_id' => 1,
                'pembeli' => 'Adit',
                'penjualan_kode' => 'code-07',
                'penjualan_tanggal' => '2022-09-12 19:01:00'
            ],
            [
                'penjualan_id' => 8,
                'user_id' => 2,
                'pembeli' => 'Jarwo',
                'penjualan_kode' => 'code-08',
                'penjualan_tanggal' => '2022-09-12 19:11:00'
            ],
            [
                'penjualan_id' => 9,
                'user_id' => 3,
                'pembeli' => 'Sopo',
                'penjualan_kode' => 'code-09',
                'penjualan_tanggal' => '2022-09-12 15:14:00'
            ],
            [
                'penjualan_id' => 10,
                'user_id' => 1,
                'pembeli' => 'Rain',
                'penjualan_kode' => 'code-10',
                'penjualan_tanggal' => '2022-09-12 20:10:00'
            ],
        ];
        DB::table('t_penjualan')->insert($data);
    }
}
