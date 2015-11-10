<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('nombre', 50);
            $table->string('escudo', 50);            
            $table->string('pais_id', 15)->nullable();
            $table->integer('direccion_sede', false, true)->nullable();
            $table->string('telefono_fijo', 20)->nullable();
            $table->string('telefono_celular', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->decimal('cuota_mensual', 14, 2)->nullable();
            $table->string('observaciones', 1500)->nullable();
            $table->boolean('ind_verificado')->default(0);
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
        Schema::drop('clubs');
    }
}
