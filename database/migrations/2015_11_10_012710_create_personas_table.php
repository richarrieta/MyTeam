<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('personas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 50);
            $table->string('apellido', 30);
            $table->string('foto', 50)->nullable();
            $table->integer('tipo_nacionalidad_id', false, true)->nullable();
            $table->bigInteger('documento_identidad')->nullable();
            $table->string('sexo', 1)->nullable();
            $table->integer('estado_civil_id', false, true)->nullable();
            $table->string('lugar_nacimiento', 500)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('pais_id', 15)->nullable();
            $table->integer('direccion', false, true)->nullable();
            $table->string('telefono_fijo', 20)->nullable();
            $table->string('telefono_celular', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->string('observaciones', 1500)->nullable();
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
    public function down() {
        Schema::drop('personas');
    }

}
