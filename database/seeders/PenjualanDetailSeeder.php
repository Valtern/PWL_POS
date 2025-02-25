<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $detail_id = 1;

        for ($penjualan_id = 1; $penjualan_id <= 10; $penjualan_id++) {
            for ($i = 0; $i < 3; $i++) {
                $data[] = [
                    'detail_id' => $detail_id++,
                    'penjualan_id' => $penjualan_id,
                    'barang_id' => rand(1, 10), 
                    'harga' => rand(100000, 5000000), 
                    'jumlah' => rand(1, 5) 
                ];
            }
        }

        DB::table('t_penjualan_detail')->insert($data);
    }
}
