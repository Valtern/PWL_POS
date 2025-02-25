<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class penjualanDetSeeder extends Seeder
{
    public function run(): void
    {
        $detail_id = 1;
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                DB::table('t_penjualan_detail')->insert([
                    'detail_id' => $detail_id++,
                    'penjualan_id' => $i,
                    'barang_id' => $j,
                    'harga' => 10000 * $j,
                    'jumlah' => 1,
                ]);
            }
        }
    }
}
