<?php

namespace App\Interfaces;

interface SimpleTableInterface
{

    /**
     * Columnas a mostrar en la tabla
     * @return array Default Values..
     */
    function getTableFields();
}
