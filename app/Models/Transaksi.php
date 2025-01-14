<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaction'; // Nama tabel di database

    protected $fillable = [
        'user_id',     // ID pengguna yang melakukan transaksi
        'meja_id',    // ID meja yang digunakan
        'total_harga', // Total harga dari transaksi
    ];
        // Definisikan relasi jika ada
        public function user()
        {
            return $this->belongsTo(User::class);
        }
    
        public function meja()
        {
            return $this->belongsTo(Meja::class);
        }
    
        public function menus()
        {
            return $this->belongsToMany(Menu::class)->withPivot('jumlah'); // Jika Anda menggunakan relasi many-to-many
        }
}
