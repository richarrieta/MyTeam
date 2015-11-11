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

    protected $table = "fichas";

    /**
     * Campos que se pueden llenar mediante el uso de mass-assignment
     * @link http://laravel.com/docs/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'representante_id',
        'jugador_id',
        'ind_hermano',
        'ind_pago_especial',
        'monto_mensual',
        'fecha_ingreso',
        'fecha_egreso',
        'numero',
        'posicion_id',
        'mejoras',
        'fortalezas',
        'observaciones',
        'goles',
        'altura',
        'peso',
        'talla_camisa',
        'talla_short'
    ];

    /**
     * Reglas que debe cumplir el objeto al momento de ejecutar el metodo save,
     * si el modelo no cumple con estas reglas el metodo save retornará false, y los cambios realizados no haran
     * persistencia.
     * @link http://laravel.com/docs/validation#available-validation-rules
     * @var array
     */
    protected $dates = ['fecha_ingreso', 'fecha_egreso', 'deleted_at'];
    protected $appends = ['monto_mensual'];

    protected function getRules() {
        return [
            'representante_id' => 'required|integer',
            'jugador_id' => 'required|integer',
            'club_id' => 'required|integer',
            'ind_hermano' => 'required',
            'ind_cuota' => 'required',
            'monto_mensual' => 'required',
            'fecha_ingreso' => 'required|date' . ($this->fecha_evento != null ? ('|before:' . $this->fecha_evento) : ''),
            'fecha_egreso' => 'date' . ($this->fecha_montaje != null ? ('|after:' . $this->fecha_montaje) : ''),
            'posicion_id' => 'required|integer',
        ];
    }

    protected function getPrettyFields() {
        return [
            'representante_id' => 'Representante',
            'jugador_id' => 'Jugador',
            'club_id' => 'Club',
            'ind_hermano' => 'Tiene Hermanos en el club',
            'ind_pago_especial' => 'Paga mensualidad especial',
            'monto_mensual' => 'Monto mensualidad',
            'fecha_ingreso' => 'Fecha de ingreso al club',
            'fecha_egreso' => 'Fecha de egreso del club',
            'numero' => 'Numero de ficha en camiseta',
            'posicion_id' => 'Posicion de juego predominante',
            'mejoras' => 'Puntos a mejorar',
            'fortalezas' => 'Fortalezas',
            'observaciones' => 'Observaciones',
            'goles' => 'Goles con el club',
            'altura' => 'Altura',
            'peso' => 'Peso',
            'talla_camisa' => 'Talla de camisa',
            'talla_short' => 'Talla de short'
        ];
    }

    public static $estatuses = [
        '1' => 'Activo',
        '2' => 'Lesionado',
        '3' => 'Amolestado',
        '4' => 'Suspendido',
        '5' => 'Bloqueado',
        '6' => 'Traspasado',
    ];

    public function getPrettyName() {
        return "fichas";
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
    
    public function getEstatusDisplayAttribute()
    {
        return static::$estatuses[$this->estatus];
    }

}
