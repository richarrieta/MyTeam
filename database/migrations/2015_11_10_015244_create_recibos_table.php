<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecibosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recibos', function (Blueprint $table) {
            $table->increments('id');  
            $table->string('num_control', 50)->nullable();
            $table->integer('persona_id', false, true)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->decimal('monto_pagado', 14, 2)->nullable();
            $table->integer('user_id', false, true)->nullable();
            $table->integer('version')->default(1);
            $table->boolean('ind_active')->default(1);
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
        Schema::drop('recibos');
    }
}
