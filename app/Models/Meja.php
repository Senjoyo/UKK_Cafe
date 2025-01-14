<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    use HasFactory;
    public $fillable = [
        'nomerMeja'
    ];
    protected $primaryKey = 'id';
    public $timestamps = false;


}
