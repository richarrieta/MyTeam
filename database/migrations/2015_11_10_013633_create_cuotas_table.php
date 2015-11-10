<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ficha_id', false, true)->nullable(); 
            $table->integer('ano')->default(1);
            $table->integer('periodo')->default(1);            
            $table->boolean('ind_cuota_especial')->default(0);
            $table->decimal('monto_cuota', 14, 2)->nullable();            
            $table->decimal('monto_pagado', 14, 2)->nullable();
            $table->boolean('ind_pago_parcial')->default(1);
            $table->integer('recibo_id', false, true)->nullable();
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
        Schema::drop('cuotas');
    }
}
