<?php

namespace App;

use App\Helpers\Helper;
use App\Interfaces\SelectInterface;
use App\Interfaces\SimpleTableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;

/**
 * Description of BaseModel
 * Modelo base que extiende a eloquent con todo lo necesario para validaciones.
 * y observadores
 *
 * Validaciones: Para poder usar la validacion se debe incluir el atributo $rules para el validator.
 * Si se quiere validación especial se debe sobreescribir el metodo Validate.
 * Por defecto el metodo validate es ejecutado con el evento save();
 *
 * @author Nadin Yamaui
 */
abstract class BaseModel extends Model implements SelectInterface, SimpleTableInterface
{

    /**
     * Reglas que debe cumplir el objeto al momento de ejecutar el metodo save,
     * si el modelo no cumple con estas reglas el metodo save retornará false, y los cambios realizados no haran
     * persistencia.
     * @link http://laravel.com/docs/validation#available-validation-rules
     * @var array
     */
    protected $appends = [];
    protected $dates = [];
    protected $displayTable = [];

    /**
     * Error message bag
     * @var Illuminate\Support\MessageBag
     */
    public $errors;

    /**
     * Validator instance
     * @var Illuminate\Validation\Validators
     */
    protected $validator;
    public static $cmbsexo = [
        ''  => 'Seleccione',
        'M' => 'Masculino',
        'F' => 'Femenino'
    ];
    protected static $cmbsino = [
        '0' => 'No',
        '1' => 'Si'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->errors = new MessageBag();
        $this->validator = \App::make('validator');
    }

    public static function create(array $attributes = [])
    {
        $model = new static();
        $model->fill($attributes);
        $model->save();

        return $model;
    }

    /**
     * Retrieve error message bag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set error message bag
     *
     * @var Illuminate\Support\MessageBag
     */
    protected function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public static function findOrNew($str, $columns = [])
    {
        if ($str == "") {
            return new static();
        } else {
            return static::findOrFail($str);
        }
    }

    /**
     * Validates current attributes against rules
     */
    public function validate()
    {
        $v = $this->validator->make($this->attributes, $this->getRules());
        $v->setAttributeNames($this->getPrettyFields());
        if ($v->passes()) {
            $this->afterValidate();

            return true;
        }
        $this->setErrors($v->messages());

        return false;
    }

    public static function getCampoCombo()
    {
        return "nombre";
    }

    public static function getCombo($campo = "Seleccione", array $condiciones = null)
    {
        $campoCombo = static::getCampoCombo();
        if (static::getCampoOrder() == "") {
            $campoOrder = $campoCombo;
        } else {
            $campoOrder = static::getCampoOrder();
        }
        if ($condiciones == null) {
            $registros = self::orderBy($campoOrder)->get();
        } else {
            foreach ($condiciones as $key => $condicion) {
                if ($key == 0) {
                    $registros = self::where($condicion['CAMPO'], '=', $condicion['VALOR']);
                } else {
                    $registros = $registros->where($condicion['CAMPO'], '=', $condicion['VALOR']);
                }
            }
            $registros = $registros->orderBy($campoOrder)->get();
        }
        $retorno = ['' => $campo];
        foreach ($registros as $registro) {
            $retorno[$registro->id] = $registro->{$campoCombo};
        }
        if ($campo == "" && count($retorno) > 1) {
            unset($retorno['']);
        }

        return $retorno;
    }

    public static function getCampoOrder()
    {
        return "";
    }

    public function hasErrors()
    {
        return $this->errors->count() > 0;
    }

    public function fill(array $atributos)
    {
        foreach ($atributos as $key => $atributo) {
            if ($atributo == "" && !$this->isBooleanField($key)) {
                $atributos[$key] = null;
            } else {
                if ($atributo == "" || is_null($atributo)) {
                    $atributos[$key] = false;
                } else {
                    if ($atributo != "" && $this->isDecimalField($key)) {
                        $atributos[$key] = Helper::tf($atributo);
                    }
                }
            }
        }

        return parent::fill($atributos);
    }

    public function getValueAt($key, $format = true)
    {
        $arr = explode('->', $key);
        switch (count($arr)) {
            case 3:
                if (isset($this->{$arr[0]}->{$arr[1]}->{$arr[2]})) {
                    return $this->{$arr[0]}->{$arr[1]}->{$arr[2]};
                }
                break;
            case 2:
                if (isset($this->{$arr[0]}->{$arr[1]})) {
                    return $this->{$arr[0]}->{$arr[1]};
                }
            case 1:
                if ($format && $this->isBooleanField($key) &&
                    isset(static::$cmbsino[$this->{$key}])
                ) {
                    return static::$cmbsino[$this->{$key}];
                }
                if ($format && $this->isDateField($key) && is_object($this->{$key})) {
                    return $this->{$key}->format('d/m/Y');
                }

                return $this->{$key};
        }

        return "";
    }

    public function getPublicFields()
    {
        $arrDisplay = $this->getTableFields();
        $arrReturn = [];
        foreach ($arrDisplay as $display) {
            $arrReturn[$display] = $this->getDescription($display);
        }

        return $arrReturn;
    }

    protected function addError($var, $description)
    {
        $this->errors->add($var, $description);
    }

    public function getDescription($attr)
    {
        $arr = explode('->', $attr);
        switch (count($arr)) {
            case 3:
                $rel2 = str_replace('_id', '', $arr[1]);
                $camelField = camel_case($rel2);
                $obj = $this->{$arr[0]}()->getRelated()->{$camelField}()->getRelated();

                return $obj->getPrettyFields()[$arr[2]];
            case 2:
                $obj = $this->{$arr[0]}()->getRelated();

                return $obj->getPrettyFields()[$arr[1]];
            case 1:
                return $this->getPrettyFields()[$arr[0]];
        }
    }

    public function isRelatedField($field)
    {
        $test = $this->getRelatedField($field);
        //Yes the field is a relationn
        if (class_basename($test) == "BelongsTo") {
            return true;
        } else {
            return false;
        }
    }

    public function getRelatedOptions($field)
    {
        $related = $this->getRelatedField($field, false)->getRelated();
        $className = get_class($related);
        if (method_exists($related, 'getParent')) {
            $relatedObj = $this->getRelatedField($field, true);
            if (is_object($relatedObj)) {
                return call_user_func([$className, 'getCombo'], $relatedObj->{$related->getParent()});
            } else {
                return call_user_func([$className, 'getCombo']);
            }
        } else {
            return call_user_func([$className, 'getCombo']);
        }
    }

    public function isDateField($field)
    {
        return in_array($field, $this->dates);
    }

    private function getRelatedField($field, $getInstance = false)
    {
        $arr = explode('->', $field);
        switch (count($arr)) {
            case 3:
                $field = str_replace('_id', '', $arr[2]);
                $camelField = camel_case($field);
                $parent = $this->{$arr[0]}()->getRelated()->{$arr[1]}()->getRelated();
                if (method_exists($parent, $camelField)) {
                    //Return..
                    if ($getInstance && isset($this->{$arr[0]}->{$arr[1]}->{$camelField})) {
                        return $this->{$arr[0]}->{$arr[1]}->{$camelField};
                    } else {
                        if (!$getInstance) {
                            return $parent->{$camelField}();
                        }
                    }
                }
            case 2:
                $field = str_replace('_id', '', $arr[1]);
                $camelField = camel_case($field);
                $parent = $this->{$arr[0]}()->getRelated();
                //Method Existss??
                if (method_exists($parent, $camelField)) {
                    //Return..
                    if ($getInstance && isset($this->{$arr[0]}->{$camelField})) {
                        return $this->{$arr[0]}->{$camelField};
                    } else {
                        if (!$getInstance) {
                            return $parent->{$camelField}();
                        }
                    }
                }
            case 1:
                $field = str_replace('_id', '', $field);
                $camelField = camel_case($field);
                //Method Existss??
                if (method_exists($this, $camelField)) {
                    //Return..
                    if ($getInstance && isset($this->{$camelField})) {
                        return $this->{$camelField};
                    } else {
                        if (!$getInstance) {
                            return $this->{$camelField}();
                        }
                    }
                }
        }

        return null;
    }

    public function isBooleanField($field)
    {
        return starts_with($field, 'ind_');
    }

    public function getTableFields()
    {
        return $this->fillable;
    }

    public function isRequired($field)
    {
        $rules = $this->rules;
        if (isset($rules[$field])) {
            return strpos($rules[$field], 'required') !== false && strpos($rules[$field], 'required_') === false;
        }

        return false;
    }

    public function isDecimalField($field)
    {
        if (method_exists($this, 'getDecimalFields')) {
            return in_array($field, static::getDecimalFields());
        }

        return false;
    }

    protected function afterValidate()
    {

    }

    public function getFillable()
    {
        return $this->fillable;
    }

    public function getEstatusDisplayAttribute()
    {
        return static::$estatusArray[$this->estatus];
    }

    public abstract function getPrettyName();

    protected abstract function getPrettyFields();

    protected abstract function getRules();
}
