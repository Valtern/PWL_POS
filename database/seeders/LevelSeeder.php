<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['level_id' => 1, 'level_kode' => 'ADM', 'level_nama' => 'Administrator'],
            ['level_id' => 2, 'level_kode' => 'MNG', 'level_nama' => 'Manager'],
            ['level_id' => 3, 'level_kode' => 'STF', 'level_nama' => 'Staff/Kasir'],
            ['level_id' => 4, 'level_kode' => 'PLG', 'level_nama' => 'Pelanggan'],
        ];
    
        foreach ($data as $item) {
            DB::table('m_level')->updateOrInsert(
                ['level_kode' => $item['level_kode']], 
                [
                    'level_id' => $item['level_id'],
                    'level_nama' => $item['level_nama']
                ]
            );
        }
   }    
}