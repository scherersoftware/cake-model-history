<?php

namespace ModelHistory\Model\Transform;

use Cake\Utility\Inflector;
use InvalidArgumentException;

abstract class Transform
{

    /**
     * Amend the data before displaying.
     *
     * @param  string  $fieldname  Field name
     * @param  mixed   $value      Value to be amended
     * @param  mixed  $model      Optional model to be used
     * @return mixed
     */
    abstract public function display($fieldname, $value, $model = null);

    /**
     * Amend the data before saving.
     *
     * @param  mixed  $fieldname  Field name
     * @param  mixed  $value      Value to be amended
     * @param  mixed  $model      Optional model to be used
     * @return mixed
     */
    abstract public function save($fieldname, $value, $model = null);

    /**
     * Transform factory
     *
     * @param  string  $type  Transform type
     * @return Transform
     */
    public static function get($type)
    {
        $namespace = '\\ModelHistory\\Model\\Transform\\';
        $transformClass = $namespace . ucfirst(strtolower($type)) . 'Transform';
        if (class_exists($transformClass)) {
            return new $transformClass();
        }
        throw new InvalidArgumentException('Transform ' . $transformClass . ' not found!');
    }
}
