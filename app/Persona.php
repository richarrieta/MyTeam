<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of Ficha
 *
 * @author richarrieta
 */
class Ficha extends BaseModel {

    protected $table = "personas";

    /**
     * Campos que se pueden llenar mediante el uso de mass-assignment
     * @link http://laravel.com/docs/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'foto',
        'tipo_nacionalidad_id',
        'documento_identidad',
        'sexo',
        'estado_civil_id',
        'lugar_nacimiento',
        'fecha_nacimiento',
        'pais_id',
        'direccion',
        'telefono_fijo',
        'telefono_celular',
        'email',
        'facebook',
        'twitter',
        'observaciones'
    ];

    /**
     * Reglas que debe cumplir el objeto al momento de ejecutar el metodo save,
     * si el modelo no cumple con estas reglas el metodo save retornará false, y los cambios realizados no haran
     * persistencia.
     * @link http://laravel.com/docs/validation#available-validation-rules
     * @var array
     */
    protected $dates = ['fecha_nacimiento'];

    protected function getRules() {
        return [
            'nombre' => 'required',
            'apellido' => 'required',
            'foto' => '',
            'tipo_nacionalidad_id' => 'required|integer',
            'documento_identidad' => 'required',
            'sexo' => 'required',
            'estado_civil_id' => 'required',
            'lugar_nacimiento' => 'required',
            'fecha_nacimiento' => 'required|date' . ($this->fecha_evento != null ? ('|before:' . $this->fecha_evento) : ''),
            'pais_id' => 'required|integer',
            'direccion' => 'required',
            'telefono_fijo' => 'max:14|min:10|regex:/^[0-9.-]*$/',
            'telefono_celular' => 'max:14|min:10|regex:/^[0-9.-]*$/',
            'email' => 'email',
        ];
    }

    protected function getPrettyFields() {
        return [
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'foto' => 'Foto',
            'tipo_nacionalidad_id' => 'Nacionalidad',
            'documento_identidad' => 'Documento de identidad',
            'sexo' => 'Sexo',
            'estado_civil_id' => 'Estado Civil',
            'lugar_nacimiento' => 'Lugar de nacimiento',
            'fecha_nacimiento' => 'Fecha de nacimiento',
            'pais_id' => 'Pais',
            'direccion' => 'Direccion',
            'telefono_fijo' => 'Telefono Fijo',
            'telefono_celular' => 'Telefono Celular',
            'email' => 'Correo electronico',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'observaciones' => 'Observaciones'
        ];
    }

    public function getPrettyName() {
        return "personas";
    }

    /**
     * Define una relación pertenece al Jugador
     * @return Jugador
     */
    public function jugador() {
        return $this->belongsTo('Persona');
    }

    /**
     * Define una relación pertenece al Representante
     * @return Representante
     */
    public function representante() {
        return $this->belongsTo('Persona');
    }

    /**
     * Define una relación pertenece al Club
     * @return Representante
     */
    public function club() {
        return $this->belongsTo('Club');
    }

    /**
     * Define una relación pertenece al Posicion
     * @return Representante
     */
    public function posicion() {
        return $this->belongsTo('Posicion');
    }

    public function setFechaIngresoAttribute($param) {
        try {
            $this->attributes['fecha_ingreso'] = Carbon::createFromFormat('d/m/Y', $param);
        } catch (\InvalidArgumentException $e) {
            $this->attributes['fecha_ingreso'] = null;
        }
    }

    public function setFechaEgresoAttribute($param) {
        try {
            $this->attributes['fecha_egreso'] = Carbon::createFromFormat('d/m/Y', $param);
        } catch (\InvalidArgumentException $e) {
            $this->attributes['fecha_egreso'] = null;
        }
    }

    public function getEstatusDisplayAttribute() {
        return static::$estatuses[$this->estatus];
    }

}
