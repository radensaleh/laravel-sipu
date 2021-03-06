<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTbProdiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_prodi', function (Blueprint $table) {
          $table->string('id_prodi')->primary();
          $table->string('id_jurusan')->index();
          $table->foreign('id_jurusan')->references('id_jurusan')->on('tb_jurusan');
          $table->string('nama_prodi');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_prodi');
    }
}
