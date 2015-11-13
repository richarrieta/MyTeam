<?php

namespace App;

class Club extends BaseModel
{

    protected $table = "clubs";

    /**
     * Campos que se pueden llenar mediante el uso de mass-assignment
     * @link http://laravel.com/docs/eloquent#mass-assignment
     * @var array
     */
    protected $fillable = [
        'nombre',
    ];

    protected function getPrettyFields()
    {
        return [
            'nombre' => 'Club',
        ];
    }

    public function getPrettyName()
    {
        return "Club";
    }

    protected function getRules()
    {
        return [
            'nombre' => 'required',
        ];
    }

}
