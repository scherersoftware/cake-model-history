<?php

namespace ModelHistory\Model\Filter;

use Cake\Utility\Inflector;
use InvalidArgumentException;

abstract class Filter
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
     * Filter factory
     *
     * @param  string  $type  Filter type
     * @return Filter
     */
    public static function get($type)
    {
        $namespace = '\\ModelHistory\\Model\\Filter\\';
        $filterClass = $namespace . ucfirst(strtolower($type)) . 'Filter';
        if (class_exists($filterClass)) {
            return new $filterClass();
        }
        throw new InvalidArgumentException('Filter ' . $filterClass . ' not found!');
    }
}
