<?php

class TipoNacionalidad extends BaseModel {

    protected $table = "tipo_nacionalidades";

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
     * si el modelo no cumple con estas reglas el metodo save retornará false, y los cambios realizados no haran persistencia.
     * @link http://laravel.com/docs/validation#available-validation-rules
     * @var array
     */
    protected function getRules() {
        return [
            'nombre' => 'required',
        ];
    }

    protected function getPrettyFields() {
        return [
            'nombre' => 'Nacionalidad',
        ];
    }

    public function getPrettyName() {
        return "Nacionalidad";
    }

}
