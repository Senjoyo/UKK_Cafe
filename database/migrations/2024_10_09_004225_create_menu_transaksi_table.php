<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('menu_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaction')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->integer('jumlah'); // Jumlah menu yang dipesan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_transaksi');
    }
}

