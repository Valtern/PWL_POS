<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangModel extends Model
{
    use HasFactory;

    protected $table = 'm_barang'; // Explicitly define the correct table name
    protected $primaryKey = 'barang_id'; // Define the primary key

    public $timestamps = false; // Jika tidak ada timestamps (created_at, updated_at)

    protected $fillable = ['category_id','barang_kode','barang_nama','harga_beli','harga_jual']; // Pastikan kolom bisa diisi

    public function users(): HasMany
    {
        return $this->hasMany(UserModel::class, 'barang_id', 'barang_id');
    }

    public function kategori()
{
    return $this->belongsTo(KategoriModel::class, 'category_id', 'category_id');
}
}
