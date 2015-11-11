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
            $table->integer('representante_id', false, true);            
            $table->integer('jugador_id', false, true);
            $table->integer('club_id', false, true);
            $table->boolean('ind_hermano')->default(0);
            $table->boolean('ind_pago_especial')->default(0);
            $table->decimal('monto_mensual', 14, 2)->nullable();
            $table->date('fecha_ingreso');
            $table->date('fecha_egreso')->nullable();
            $table->integer('numero')->nullable();
            $table->string('posicion_id')->nullable();
            $table->string('mejoras', 1500)->nullable();
            $table->string('fortalezas', 1500)->nullable();
            $table->string('observaciones', 1500)->nullable();
            $table->integer('goles')->nullable();
            $table->string('altura')->nullable();
            $table->string('peso')->nullable();
            $table->string('talla_camisa')->nullable();
            $table->string('talla_short')->nullable();
            $table->integer('estatus', false, true)->default(1);
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
