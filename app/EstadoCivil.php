<?php

namespace App;

class EstadoCivil extends BaseModel {

    protected $table = "estados_civiles";

    /**
     * Campos que se pueden llenar mediante el uso de mass-assignment
     * @link http://laravel.com/docs/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * Reglas que debe cumplir el objeto al momento de ejecutar el metodo save, 
     * si el modelo no cumple con estas reglas el metodo save retornará false, 
     * y los cambios realizados no haran persistencia.
     * @link http://laravel.com/docs/validation#available-validation-rules
     */
    protected function getRules() {
        return [
            'nombre' => 'required',
        ];
    }

    protected function getPrettyFields() {
        return [
            'nombre' => 'Estado Civil',
        ];
    }

    public function getPrettyName() {
        return "Estado Civil";
    }

    public function personas() {
        return $this->hasMany('Persona');
    }

}
