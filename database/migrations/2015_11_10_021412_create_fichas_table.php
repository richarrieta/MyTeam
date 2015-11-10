<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFichasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fichas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('representante_id', false, true)->nullable();            
            $table->integer('jugador_id', false, true)->nullable();
            $table->boolean('ind_hermano')->default(0);
            $table->boolean('ind_cuota')->default(0);
            $table->decimal('cuota_mensual', 14, 2)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_egreso')->nullable();
            $table->integer('numero');
            $table->string('posicion')->nullable();
            $table->string('debilidades', 1500)->nullable();
            $table->string('fortalezas', 1500)->nullable();
            $table->string('observaciones', 1500)->nullable();
            $table->integer('goles')->nullable();
            $table->string('altura')->nullable();
            $table->string('peso')->nullable();
            $table->string('talla_camisa')->nullable();
            $table->string('talla_short')->nullable();
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
        Schema::drop('fichas');
    }
}
