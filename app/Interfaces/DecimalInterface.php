<?php

namespace App\Interfaces;

interface DecimalInterface
{

    /**
     * Valores que tomara el objeto por defecto al momento de insertar..
     * @return array Default Values..
     */
    static function getDecimalFields();
}
