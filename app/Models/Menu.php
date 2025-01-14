<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public $primaryKey = 'id';
    public $timestamps = false;
    public $fillable = [
        'namaMenu',
        'jenis',
        'deskripsi',
        'gambar',
        'harga'
    ];

        // Update relasi ke transaksi
        public function transactions()
        {
            return $this->belongsToMany(Transaksi::class,) // Ganti 'menu_transaction' dengan 'transactions'
                         ->withPivot('jumlah'); // Jika Anda menyimpan jumlah dalam tabel pivot
        }
}
